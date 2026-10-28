<?php
namespace Controllers;

use Core\Controller;
use Core\ImageOptimizerService;

class ImageOptimizerController extends Controller
{
    /**
     * Resolve and validate safe full path inside registered roots
     */
    private function resolveSafePath($rootKey, $relPath)
    {
        $roots = ImageOptimizerService::getAvailableRoots();
        if (!isset($roots[$rootKey])) {
            return false;
        }

        $baseDir = realpath($roots[$rootKey]['path']);
        if (!$baseDir || !is_dir($baseDir)) {
            return false;
        }

        // Clean relative path
        $relPath = str_replace('\\', '/', trim($relPath));
        $relPath = ltrim($relPath, '/');

        // Check if path is already absolute and starts with baseDir
        $candidate = realpath($relPath);
        if ($candidate && str_starts_with(str_replace('\\', '/', $candidate), str_replace('\\', '/', $baseDir))) {
            return $candidate;
        }

        $candidate = realpath($baseDir . '/' . $relPath);
        if ($candidate && str_starts_with(str_replace('\\', '/', $candidate), str_replace('\\', '/', $baseDir))) {
            return $candidate;
        }

        return false;
    }

    /**
     * Dashboard view
     */
    public function index()
    {
        $roots = ImageOptimizerService::getAvailableRoots();
        $defaultRoot = array_key_first($roots) ?: 'ss_uploads';

        $this->view('image_optimizer/index', [
            'roots' => $roots,
            'defaultRoot' => $defaultRoot,
            'variantsConfig' => ImageOptimizerService::VARIANTS
        ]);
    }

    /**
     * AJAX: Scan target directory
     */
    public function scan()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $rootKey = $_GET['root'] ?? 'ss_uploads';
        $minSizeKb = (int)($_GET['min_size_kb'] ?? 0);
        $filterType = $_GET['filter'] ?? 'all'; // 'all', 'heavy', 'missing_variants', 'has_variants'

        $roots = ImageOptimizerService::getAvailableRoots();
        if (!isset($roots[$rootKey])) {
            $this->json(['success' => false, 'error' => "Unknown root directory: $rootKey"], 400);
            return;
        }

        $dirPath = $roots[$rootKey]['path'];
        $scanResult = ImageOptimizerService::scanDirectory($dirPath, $minSizeKb);

        if (!$scanResult['success']) {
            $this->json($scanResult, 500);
            return;
        }

        // Apply secondary filtering if requested
        $files = $scanResult['files'];
        if ($filterType === 'missing_variants') {
            $files = array_values(array_filter($files, fn($f) => empty($f['variants_status']['has_all'])));
        } elseif ($filterType === 'has_variants') {
            $files = array_values(array_filter($files, fn($f) => !empty($f['variants_status']['has_all'])));
        } elseif ($filterType === 'heavy') {
            $files = array_values(array_filter($files, fn($f) => $f['size'] >= 1048576));
        }

        $scanResult['files_count'] = count($files);
        $scanResult['files'] = $files;
        $scanResult['root_info'] = $roots[$rootKey];

        $this->json($scanResult);
    }

    /**
     * AJAX: Compress single image (Lossy or Lossless)
     */
    public function compressSingle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true) ?: $_POST;

        $rootKey = $data['root'] ?? 'ss_uploads';
        $file = $data['file'] ?? '';
        $mode = ($data['mode'] ?? 'lossy') === 'lossless' ? 'lossless' : 'lossy';
        $maxDim = (int)($data['max_dimension'] ?? 1800);
        $quality = isset($data['quality']) ? (int)$data['quality'] : null;

        $fullPath = $this->resolveSafePath($rootKey, $file);
        if (!$fullPath) {
            $this->json(['success' => false, 'error' => 'Invalid file path or access denied'], 403);
            return;
        }

        $res = ImageOptimizerService::compressImage($fullPath, $mode, $maxDim, $quality);
        $vStatus = ImageOptimizerService::checkVariantStatus($fullPath);
        $res['variants_status'] = $vStatus;

        $this->json($res);
    }

    /**
     * AJAX: Generate responsive variants for a single image
     */
    public function generateVariantsSingle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true) ?: $_POST;

        $rootKey = $data['root'] ?? 'ss_uploads';
        $file = $data['file'] ?? '';
        $force = !empty($data['force']);
        $compressOriginal = !empty($data['compress_original']);

        $fullPath = $this->resolveSafePath($rootKey, $file);
        if (!$fullPath) {
            $this->json(['success' => false, 'error' => 'Invalid file path or access denied'], 403);
            return;
        }

        $res = ImageOptimizerService::generateResponsiveVariants($fullPath, $force, $compressOriginal);
        $vStatus = ImageOptimizerService::checkVariantStatus($fullPath);
        $res['variants_status'] = $vStatus;

        $this->json($res);
    }

    /**
     * AJAX: Batch processor (processes 5-10 images per batch request)
     */
    public function batchAction()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(180);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true) ?: $_POST;

        $rootKey = $data['root'] ?? 'ss_uploads';
        $action = $data['action'] ?? 'generate_variants'; // 'generate_variants', 'compress_lossy', 'compress_lossless'
        $files = $data['files'] ?? [];
        $force = !empty($data['force']);
        $compressOriginal = !empty($data['compress_original']);
        $maxDim = (int)($data['max_dimension'] ?? 1800);
        $quality = isset($data['quality']) ? (int)$data['quality'] : null;

        if (empty($files) || !is_array($files)) {
            $this->json(['success' => false, 'error' => 'No files provided for batch processing'], 400);
            return;
        }

        $results = [];
        $totalBytesSaved = 0;
        $totalVariantsCreated = 0;

        foreach ($files as $filePath) {
            $fullPath = $this->resolveSafePath($rootKey, $filePath);
            if (!$fullPath) {
                $results[] = [
                    'file' => $filePath,
                    'success' => false,
                    'error' => 'File path not accessible'
                ];
                continue;
            }

            if ($action === 'generate_variants') {
                $itemRes = ImageOptimizerService::generateResponsiveVariants($fullPath, $force, $compressOriginal);
                if ($itemRes['success']) {
                    foreach ($itemRes['variants'] as $v) {
                        if (($v['status'] ?? '') === 'created') {
                            $totalVariantsCreated++;
                        }
                    }
                }
                $vStatus = ImageOptimizerService::checkVariantStatus($fullPath);
                $itemRes['variants_status'] = $vStatus;
                $itemRes['file'] = $filePath;
                $results[] = $itemRes;

            } elseif ($action === 'compress_lossy' || $action === 'compress_lossless') {
                $mode = ($action === 'compress_lossless') ? 'lossless' : 'lossy';
                $itemRes = ImageOptimizerService::compressImage($fullPath, $mode, $maxDim, $quality);
                if (!empty($itemRes['saved_bytes'])) {
                    $totalBytesSaved += $itemRes['saved_bytes'];
                }
                $vStatus = ImageOptimizerService::checkVariantStatus($fullPath);
                $itemRes['variants_status'] = $vStatus;
                $itemRes['file'] = $filePath;
                $results[] = $itemRes;
            }
        }

        $this->json([
            'success' => true,
            'processed_count' => count($results),
            'variants_created_count' => $totalVariantsCreated,
            'bytes_saved' => $totalBytesSaved,
            'bytes_saved_fmt' => ImageOptimizerService::formatBytes($totalBytesSaved),
            'items' => $results
        ]);
    }

    /**
     * Image preview streaming handler
     */
    public function preview()
    {
        $rootKey = $_GET['root'] ?? 'ss_uploads';
        $file = $_GET['file'] ?? '';

        $fullPath = $this->resolveSafePath($rootKey, $file);
        if (!$fullPath || !file_exists($fullPath)) {
            header('Content-Type: image/svg+xml');
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f1f5f9"/><text x="50" y="55" font-family="sans-serif" font-size="10" fill="#94a3b8" text-anchor="middle">No Preview</text></svg>';
            exit;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = 'image/jpeg';
        if ($ext === 'png') $mime = 'image/png';
        elseif ($ext === 'webp') $mime = 'image/webp';
        elseif ($ext === 'gif') $mime = 'image/gif';

        header("Content-Type: $mime");
        header('Cache-Control: public, max-age=86400');
        readfile($fullPath);
        exit;
    }
}
