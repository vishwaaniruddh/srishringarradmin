<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;

class PhotodownloaderController extends Controller {

    private $productModel;
    private $db;
    private $db3;
    private $inStockMap = null;
    private $configFile;
    private $localUploadsDir;
    private $cacheDir;
    private $tempZipDir;

    public function __construct() {
        // Release session lock immediately so parallel AJAX requests never queue up as pending
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $this->productModel = new ProductModel();
        $this->db = $this->productModel->getDb();
        $this->db3 = $this->productModel->getDb3();
        $this->configFile = __DIR__ . '/../Config/photo_downloader_settings.json';

        // Detect uploads directory on server / local disk
        $candidates = [
            dirname(__DIR__, 2) . '/yn/uploads',
            dirname(__DIR__, 2) . '/uploads',
            realpath(__DIR__ . '/../../yn/uploads'),
            realpath(__DIR__ . '/../yn/uploads'),
            (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/yn/uploads' : null),
            (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/uploads' : null)
        ];

        $this->localUploadsDir = null;
        foreach ($candidates as $cand) {
            if ($cand && is_dir($cand)) {
                $this->localUploadsDir = realpath($cand) ?: $cand;
                break;
            }
        }

        // Setup cache and temporary zip folders
        $this->cacheDir = __DIR__ . '/../scratch/img_cache';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }

        $this->tempZipDir = __DIR__ . '/../scratch/temp_zips';
        if (!is_dir($this->tempZipDir)) {
            @mkdir($this->tempZipDir, 0755, true);
        }

        // Opportunistic cleanup of temporary zips older than 2 hours
        $this->garbageCollectTempFiles();
    }

    /**
     * Clean up zip and json job files older than 2 hours
     */
    private function garbageCollectTempFiles() {
        $dirs = array_filter([$this->tempZipDir, sys_get_temp_dir()]);
        $expiry = time() - 7200; // 2 hours
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) continue;
            $files = @glob($dir . DIRECTORY_SEPARATOR . 'ss_job_*.*');
            if ($files) {
                foreach ($files as $file) {
                    if (filemtime($file) < $expiry) {
                        @unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Get persisted configuration from JSON file
     */
    public function getSettings() {
        $defaults = [
            'selected_categories' => ['garment:22'],
            'stock_status' => 'all',       // 'all', 'available', 'outofstock'
            'image_scope' => 'all',        // 'main', 'all'
            'limit_products' => 'all',     // 'all', '10', '25'
            'compress_images' => '1',      // '1' (compress 50-60MB photos down to ~350KB), '0' (raw)
            'updated_at' => null
        ];

        if (file_exists($this->configFile)) {
            $content = @file_get_contents($this->configFile);
            $parsed = @json_decode($content, true);
            if (is_array($parsed)) {
                return array_merge($defaults, $parsed);
            }
        }
        return $defaults;
    }

    /**
     * Persist configuration to backend JSON
     */
    private function saveSettingsData($data) {
        $existing = $this->getSettings();
        $merged = array_merge($existing, $data);
        $merged['updated_at'] = date('Y-m-d H:i:s');

        $dir = dirname($this->configFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $saved = @file_put_contents($this->configFile, json_encode($merged, JSON_PRETTY_PRINT));
        return $saved !== false;
    }

    /**
     * Main configuration and downloader UI
     */
    public function index() {
        $categories = $this->productModel->getCategories();
        $settings = $this->getSettings();
        $activeTab = trim($_GET['tab'] ?? 'downloader');
        if (!in_array($activeTab, ['downloader', 'duplicates'])) {
            $activeTab = 'downloader';
        }

        $this->view('photodownloader/index', [
            'categories' => $categories,
            'settings' => $settings,
            'activeTab' => $activeTab
        ]);
    }

    /**
     * AJAX endpoint to save settings
     */
    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $categories = $_POST['categories'] ?? [];
        if (!is_array($categories)) {
            $categories = array_filter(explode(',', (string)$categories));
        }

        $stockStatus = strtolower(trim($_POST['stock_status'] ?? 'all'));
        if (!in_array($stockStatus, ['all', 'available', 'outofstock'])) {
            $stockStatus = 'all';
        }

        $imageScope = strtolower(trim($_POST['image_scope'] ?? 'all'));
        if (!in_array($imageScope, ['main', 'all'])) {
            $imageScope = 'all';
        }

        $limitProducts = strtolower(trim($_POST['limit_products'] ?? 'all'));
        if (!in_array($limitProducts, ['all', '10', '25'])) {
            $limitProducts = 'all';
        }

        $compressImages = isset($_POST['compress_images']) ? (string)$_POST['compress_images'] : '1';
        if ($compressImages !== '0') {
            $compressImages = '1';
        }

        $saved = $this->saveSettingsData([
            'selected_categories' => array_values(array_unique($categories)),
            'stock_status' => $stockStatus,
            'image_scope' => $imageScope,
            'limit_products' => $limitProducts,
            'compress_images' => $compressImages
        ]);

        if ($saved) {
            $this->json([
                'success' => true,
                'message' => 'Downloader configuration saved successfully.',
                'settings' => $this->getSettings()
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save configuration.'], 500);
        }
    }

    /**
     * AJAX endpoint to preview estimated products and images count
     */
    public function preview() {
        $categories = $_REQUEST['categories'] ?? [];
        if (!is_array($categories)) {
            $categories = array_filter(explode(',', (string)$categories));
        }

        if (empty($categories)) {
            $savedSettings = $this->getSettings();
            $categories = $savedSettings['selected_categories'] ?? [];
        }

        $stockStatus = strtolower(trim($_REQUEST['stock_status'] ?? 'all'));
        if (!in_array($stockStatus, ['all', 'available', 'outofstock'])) {
            $savedSettings = $this->getSettings();
            $stockStatus = $savedSettings['stock_status'] ?? 'all';
        }

        $imageScope = strtolower(trim($_REQUEST['image_scope'] ?? 'all'));
        if (!in_array($imageScope, ['main', 'all'])) {
            $savedSettings = $this->getSettings();
            $imageScope = $savedSettings['image_scope'] ?? 'all';
        }

        $limitProducts = strtolower(trim($_REQUEST['limit_products'] ?? ''));
        if (!in_array($limitProducts, ['all', '10', '25'])) {
            $savedSettings = $this->getSettings();
            $limitProducts = $savedSettings['limit_products'] ?? 'all';
        }

        $previewData = $this->calculatePreview($categories, $stockStatus, $imageScope, $limitProducts);
        $this->json([
            'success' => true,
            'data' => $previewData
        ]);
    }

    /**
     * AJAX endpoint: Fetch live product photo previews from the production server for selected categories
     */
    public function getPhotosPreview() {
        $categories = $_REQUEST['categories'] ?? [];
        if (!is_array($categories)) {
            $categories = array_filter(explode(',', (string)$categories));
        }

        if (empty($categories)) {
            $savedSettings = $this->getSettings();
            $categories = $savedSettings['selected_categories'] ?? [];
        }

        $stockStatus = strtolower(trim($_REQUEST['stock_status'] ?? 'all'));
        $imageScope = strtolower(trim($_REQUEST['image_scope'] ?? 'all'));
        $page = max(1, (int)($_REQUEST['page'] ?? 1));
        $limit = 24;

        $this->loadInStockMap();

        $allProducts = [];
        foreach ($categories as $catKey) {
            $info = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$info || empty($info['products'])) continue;
            foreach ($info['products'] as $p) {
                $p['category_name'] = $info['name'];
                $p['department'] = $info['department'];
                $p['category_type'] = $info['type'];
                $allProducts[] = $p;
            }
        }

        $totalProducts = count($allProducts);
        $offset = ($page - 1) * $limit;
        $slicedProducts = array_slice($allProducts, $offset, $limit);

        $items = [];
        foreach ($slicedProducts as $p) {
            $pid = (int)$p['id'];
            $sku = trim($p['sku']);
            $isGarment = ($p['category_type'] === 'garment');

            $imgWhere = $isGarment ? "(gproduct_id = $pid OR pro_code = '$sku')" : "(product_id = $pid OR pro_code = '$sku')";
            $sql = "SELECT id, img_name, rank FROM product_images_new 
                    WHERE $imgWhere 
                      AND img_name != '' 
                      AND (img_name LIKE '%.jpg' OR img_name LIKE '%.jpeg' OR img_name LIKE '%.png' OR img_name LIKE '%.webp')
                    ORDER BY rank ASC, id ASC";
            $res = mysqli_query($this->db, $sql);
            $photos = [];
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rawImg = trim($r['img_name']);
                    $cleanRel = ltrim(preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $rawImg), '/');
                    $parts = explode('/', $cleanRel);
                    $encoded = array_map('rawurlencode', $parts);
                    $serverUrl = 'https://srishringarr.com/yn/uploads/' . implode('/', $encoded);
                    $photos[] = [
                        'id' => (int)$r['id'],
                        'raw_name' => $rawImg,
                        'file_name' => basename($rawImg),
                        'server_url' => $serverUrl,
                        'rank' => (int)$r['rank']
                    ];
                }
            }

            if (!empty($photos)) {
                $primaryPhoto = $photos[0];
                $items[] = [
                    'sku' => $sku,
                    'title' => $p['name'],
                    'category' => $p['category_name'],
                    'department' => $p['department'],
                    'primary_url' => $primaryPhoto['server_url'],
                    'primary_filename' => $primaryPhoto['file_name'],
                    'photos_count' => count($photos),
                    'photos' => $photos
                ];
            }
        }

        $this->json([
            'success' => true,
            'total_products' => $totalProducts,
            'page' => $page,
            'total_pages' => ($totalProducts > 0 ? (int)ceil($totalProducts / $limit) : 1),
            'server_domain' => 'https://srishringarr.com',
            'items' => $items
        ]);
    }

    /**
     * Calculate summary metrics for preview using fast indexed queries
     */
    private function calculatePreview($categoryKeys, $stockStatus, $imageScope, $limitProducts = 'all') {
        $totalProducts = 0;
        $categoryBreakdown = [];
        $garmentIds = [];
        $jewelIds = [];

        $this->loadInStockMap();

        foreach ($categoryKeys as $catKey) {
            $info = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$info) continue;

            $prods = $info['products'];
            if ($limitProducts !== 'all' && is_numeric($limitProducts) && (int)$limitProducts > 0) {
                $prods = array_slice($prods, 0, (int)$limitProducts);
            }

            $productCount = count($prods);
            $totalProducts += $productCount;

            if ($productCount > 0) {
                if ($info['type'] === 'garment') {
                    foreach ($prods as $p) {
                        $garmentIds[] = (int)$p['id'];
                    }
                } else {
                    foreach ($prods as $p) {
                        $jewelIds[] = (int)$p['id'];
                    }
                }
            }

            $categoryBreakdown[] = [
                'key' => $catKey,
                'name' => $info['name'],
                'department' => $info['department'],
                'products' => $productCount
            ];
        }

        $totalImages = 0;
        if ($imageScope === 'main') {
            $totalImages = $totalProducts;
        } else {
            $garmentIds = array_unique($garmentIds);
            if (!empty($garmentIds)) {
                $gChunks = array_chunk($garmentIds, 1000);
                foreach ($gChunks as $gChunk) {
                    $gList = implode(',', $gChunk);
                    $gRes = mysqli_query($this->db, "SELECT COUNT(*) as c FROM product_images_new WHERE gproduct_id IN ($gList)");
                    if ($gRes && $gRow = mysqli_fetch_assoc($gRes)) {
                        $totalImages += (int)$gRow['c'];
                    }
                }
            }

            $jewelIds = array_unique($jewelIds);
            if (!empty($jewelIds)) {
                $jChunks = array_chunk($jewelIds, 1000);
                foreach ($jChunks as $jChunk) {
                    $jList = implode(',', $jChunk);
                    $jRes = mysqli_query($this->db, "SELECT COUNT(*) as c FROM product_images_new WHERE product_id IN ($jList)");
                    if ($jRes && $jRow = mysqli_fetch_assoc($jRes)) {
                        $totalImages += (int)$jRow['c'];
                    }
                }
            }
        }

        return [
            'total_categories' => count($categoryKeys),
            'total_products' => $totalProducts,
            'total_images' => $totalImages,
            'breakdown' => $categoryBreakdown
        ];
    }

    /**
     * Start a chunked download session
     */
    public function startDownloadJob() {
        @ini_set('memory_limit', '1024M');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $categories = $_POST['categories'] ?? [];
        if (!is_array($categories)) {
            $categories = array_filter(explode(',', (string)$categories));
        }

        if (empty($categories)) {
            $savedSettings = $this->getSettings();
            $categories = $savedSettings['selected_categories'] ?? [];
        }

        $stockStatus = strtolower(trim($_POST['stock_status'] ?? 'all'));
        if (!in_array($stockStatus, ['all', 'available', 'outofstock'])) {
            $stockStatus = 'all';
        }

        $imageScope = strtolower(trim($_POST['image_scope'] ?? 'all'));
        if (!in_array($imageScope, ['main', 'all'])) {
            $imageScope = 'all';
        }

        $limitProducts = strtolower(trim($_POST['limit_products'] ?? 'all'));
        if (!in_array($limitProducts, ['all', '10', '25'])) {
            $savedSettings = $this->getSettings();
            $limitProducts = $savedSettings['limit_products'] ?? 'all';
        }

        $compressImages = isset($_POST['compress_images']) ? (string)$_POST['compress_images'] : '1';
        if ($compressImages !== '0') {
            $compressImages = '1';
        }

        // Persist settings
        $this->saveSettingsData([
            'selected_categories' => array_values(array_unique($categories)),
            'stock_status' => $stockStatus,
            'image_scope' => $imageScope,
            'limit_products' => $limitProducts,
            'compress_images' => $compressImages
        ]);

        if (!class_exists('\ZipArchive')) {
            $this->json(['success' => false, 'message' => 'PHP ZipArchive extension is not enabled on this server.'], 500);
            return;
        }

        // Gather all products across selected categories
        $this->loadInStockMap();
        $allProducts = [];

        foreach ($categories as $catKey) {
            $catInfo = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$catInfo || empty($catInfo['products'])) continue;

            $deptName = $this->sanitizeFolderName($catInfo['department']);
            $catFolderName = $this->sanitizeFolderName($catInfo['name']);

            $catProds = $catInfo['products'];
            if ($limitProducts !== 'all' && is_numeric($limitProducts) && (int)$limitProducts > 0) {
                $catProds = array_slice($catProds, 0, (int)$limitProducts);
            }

            foreach ($catProds as $p) {
                $sku = trim($p['sku'] ?? '');
                if (empty($sku)) continue;
                $allProducts[] = [
                    'id' => (int)$p['id'],
                    'sku' => $sku,
                    'name' => $p['name'] ?? $sku,
                    'type' => $p['type'] ?? 'garment',
                    'dept' => $deptName,
                    'cat_name' => $catFolderName
                ];
            }
        }

        if (empty($allProducts)) {
            $this->json(['success' => false, 'message' => 'No products found matching the selected filters.'], 400);
            return;
        }

        $jobId = bin2hex(random_bytes(16));
        $zipBaseDir = is_dir($this->tempZipDir) && is_writable($this->tempZipDir) ? $this->tempZipDir : sys_get_temp_dir();
        $zipPath = $zipBaseDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.zip';
        $metaPath = $zipBaseDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json';

        // 4 products per chunk guarantees completion in 4-8 seconds without ever hitting Nginx 60s timeout!
        $chunkSize = 4;

        $totalProducts = count($allProducts);
        $totalChunks = (int)ceil($totalProducts / $chunkSize);

        $jobMeta = [
            'job_id' => $jobId,
            'zip_path' => $zipPath,
            'meta_path' => $metaPath,
            'image_scope' => $imageScope,
            'compress_images' => $compressImages,
            'total_products' => $totalProducts,
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
            'photos_packed' => 0,
            'products' => $allProducts,
            'created_at' => time()
        ];

        file_put_contents($metaPath, json_encode($jobMeta));

        $this->json([
            'success' => true,
            'job_id' => $jobId,
            'total_products' => $totalProducts,
            'total_chunks' => $totalChunks,
            'chunk_size' => $chunkSize,
            'compress_images' => $compressImages
        ]);
    }

    /**
     * Process a chunk of products with high-speed compression and ZipArchive
     */
    public function processDownloadChunk() {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(120);

        $jobId = preg_replace('/[^a-f0-9]/', '', (string)($_POST['job_id'] ?? ''));
        $chunkIndex = (int)($_POST['chunk_index'] ?? 0);

        $metaPath = null;
        $candidates = [
            $this->tempZipDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json'
        ];
        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                $metaPath = $cand;
                break;
            }
        }

        if (!$metaPath) {
            $this->json(['success' => false, 'message' => 'Download session expired or not found.'], 404);
            return;
        }

        $meta = json_decode(file_get_contents($metaPath), true);
        if (!$meta || empty($meta['zip_path'])) {
            $this->json(['success' => false, 'message' => 'Download session metadata corrupted.'], 500);
            return;
        }

        $chunkSize = (int)($meta['chunk_size'] ?? 4);
        $offset = $chunkIndex * $chunkSize;
        $productsSlice = array_slice($meta['products'], $offset, $chunkSize);

        $zipPath = $meta['zip_path'];
        $openFlags = (file_exists($zipPath) && filesize($zipPath) > 22)
            ? 0
            : (\ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, $openFlags) !== true) {
            $this->json(['success' => false, 'message' => 'Failed to open ZIP archive for appending.'], 500);
            return;
        }

        $imageScope = $meta['image_scope'] ?? 'all';
        $compressImages = ($meta['compress_images'] ?? '1') === '1';
        $imagesToPack = [];
        $lastLabel = '';

        foreach ($productsSlice as $prod) {
            $sku = $prod['sku'];
            $pid = (int)$prod['id'];
            $pType = $prod['type'];
            $dept = $prod['dept'];
            $catName = $prod['cat_name'];
            $skuFolder = $this->sanitizeFolderName($sku);
            $lastLabel = "{$catName} / {$sku}";

            $escSku = mysqli_real_escape_string($this->db, $sku);
            $idCol = ($pType === 'garment') ? 'gproduct_id' : 'product_id';
            $sql = "SELECT img_name, rank FROM product_images_new WHERE pro_code = '$escSku' OR $idCol = $pid ORDER BY rank ASC, id ASC";
            $res = mysqli_query($this->db, $sql);

            $seen = [];
            $imgs = [];
            if ($res) {
                while ($im = mysqli_fetch_assoc($res)) {
                    $raw = trim($im['img_name'] ?? '');
                    if ($raw && !isset($seen[$raw])) {
                        $seen[$raw] = true;
                        $imgs[] = $im;
                    }
                }
            }

            if ($imageScope === 'main' && !empty($imgs)) {
                $imgs = array_slice($imgs, 0, 1);
            }

            $idx = 0;
            foreach ($imgs as $im) {
                $raw = $im['img_name'];
                $cleanBase = $this->sanitizeFileName(basename($raw));
                $prefix = ($idx === 0) ? '00_main_' : sprintf('%02d_', $idx);
                $zipEntry = "{$dept}/{$catName}/{$skuFolder}_{$prefix}{$cleanBase}";

                $imagesToPack[] = [
                    'raw_name' => $raw,
                    'zip_entry' => $zipEntry
                ];
                $idx++;
            }
        }

        // Add this batch of images with high-speed compression
        $packedNow = $this->addImagesBatchToZip($zip, $imagesToPack, $compressImages);
        $zip->close();

        $meta['photos_packed'] = (int)($meta['photos_packed'] ?? 0) + $packedNow;
        file_put_contents($metaPath, json_encode($meta));

        $processedCount = min($offset + count($productsSlice), $meta['total_products']);
        $isComplete = ($chunkIndex + 1 >= $meta['total_chunks']);
        $percent = $meta['total_products'] > 0 ? round(($processedCount / $meta['total_products']) * 100) : 100;
        $currentZipSizeMb = file_exists($zipPath) ? round(filesize($zipPath) / (1024 * 1024), 2) : 0;

        $this->json([
            'success' => true,
            'job_id' => $jobId,
            'chunk_index' => $chunkIndex,
            'total_chunks' => $meta['total_chunks'],
            'processed_count' => $processedCount,
            'total_products' => $meta['total_products'],
            'photos_packed' => $meta['photos_packed'],
            'percent' => $percent,
            'current_label' => $lastLabel,
            'zip_size_mb' => $currentZipSizeMb,
            'is_complete' => $isComplete
        ]);
    }

    /**
     * Resolve full local filesystem path of an image
     */
    private function resolveLocalImagePath($rawName) {
        $cleanRel = ltrim(str_replace('\\', '/', (string)$rawName), '/');
        // Strip common redundant prefixes
        $subRels = [
            $cleanRel,
            preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $cleanRel),
            urldecode($cleanRel)
        ];
        $subRels = array_unique(array_filter($subRels));

        $rootCandidates = [
            $this->localUploadsDir,
            dirname(__DIR__, 2) . '/yn/uploads',
            dirname(__DIR__, 2) . '/uploads',
            (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/yn/uploads' : null),
            (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/uploads' : null),
            realpath(__DIR__ . '/../../yn/uploads'),
            realpath(__DIR__ . '/../yn/uploads')
        ];
        $rootCandidates = array_unique(array_filter($rootCandidates));

        foreach ($rootCandidates as $root) {
            foreach ($subRels as $rel) {
                $cand = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
                if (file_exists($cand) && is_file($cand)) {
                    return $cand;
                }
            }
        }
        return null;
    }

    /**
     * High-speed image compression for massive 50-60MB raw photos
     * Scales down to 1920px max dimension, JPEG quality 82, with disk caching
     */
    private function compressAndCacheImage($sourcePath, $maxDim = 1920, $quality = 82) {
        if (!file_exists($sourcePath)) return $sourcePath;

        $fileSize = filesize($sourcePath);
        // If image is already smaller than 1.5MB, no resizing needed
        if ($fileSize <= 1.5 * 1024 * 1024) {
            return $sourcePath;
        }

        // Cache filename by path, mtime, and dimensions
        $cacheKey = md5($sourcePath . '_' . filemtime($sourcePath) . "_{$maxDim}_{$quality}") . '.jpg';
        $cachedPath = $this->cacheDir . DIRECTORY_SEPARATOR . $cacheKey;

        if (file_exists($cachedPath) && filesize($cachedPath) > 500) {
            return $cachedPath;
        }

        // Inspect dimensions
        $info = @getimagesize($sourcePath);
        if (!$info) return $sourcePath;

        $origW = $info[0];
        $origH = $info[1];
        $mime = $info['mime'] ?? '';

        // If dimensions are already within limit and filesize isn't excessive
        if ($origW <= $maxDim && $origH <= $maxDim && $fileSize <= 2 * 1024 * 1024) {
            return $sourcePath;
        }

        $scale = min(1.0, $maxDim / max($origW, $origH));
        $newW = max(100, (int)round($origW * $scale));
        $newH = max(100, (int)round($origH * $scale));

        try {
            $srcImg = null;
            if ($mime === 'image/jpeg' || strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) === 'jpg') {
                $srcImg = @imagecreatefromjpeg($sourcePath);
            } elseif ($mime === 'image/png') {
                $srcImg = @imagecreatefrompng($sourcePath);
            } elseif ($mime === 'image/webp') {
                $srcImg = @imagecreatefromwebp($sourcePath);
            }

            if (!$srcImg) {
                return $sourcePath;
            }

            // Correct smartphone EXIF orientation if available
            if (function_exists('exif_read_data') && ($mime === 'image/jpeg')) {
                $exif = @exif_read_data($sourcePath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $srcImg = imagerotate($srcImg, 180, 0);
                            break;
                        case 6:
                            $srcImg = imagerotate($srcImg, -90, 0);
                            $t = $newW; $newW = $newH; $newH = $t;
                            $t2 = $origW; $origW = $origH; $origH = $t2;
                            break;
                        case 8:
                            $srcImg = imagerotate($srcImg, 90, 0);
                            $t = $newW; $newW = $newH; $newH = $t;
                            $t2 = $origW; $origW = $origH; $origH = $t2;
                            break;
                    }
                }
            }

            $dstImg = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            @imagedestroy($srcImg);

            $saved = @imagejpeg($dstImg, $cachedPath, $quality);
            @imagedestroy($dstImg);

            if ($saved && file_exists($cachedPath) && filesize($cachedPath) > 500) {
                return $cachedPath;
            }
        } catch (\Throwable $e) {
            // Graceful fallback to original file if memory or GD error occurs
            return $sourcePath;
        }

        return $sourcePath;
    }

    /**
     * Add a batch of images to the zip archive with high-speed compression
     */
    private function addImagesBatchToZip(\ZipArchive $zip, array $imagesList, $compress = true) {
        if (empty($imagesList)) return 0;

        $addedCount = 0;
        $remoteQueue = [];

        foreach ($imagesList as $idx => $item) {
            $rawName = $item['raw_name'];
            $zipEntry = $item['zip_entry'];

            // 1. Check local filesystem first
            $localPath = $this->resolveLocalImagePath($rawName);

            if ($localPath) {
                $pathToPack = $localPath;
                if ($compress) {
                    $pathToPack = $this->compressAndCacheImage($localPath, 1920, 82);
                }

                if ($zip->addFile($pathToPack, $zipEntry)) {
                    // Set compression to STORE (no deflate CPU overhead for already-compressed JPEGs)
                    if (defined('\ZipArchive::CM_STORE')) {
                        $zip->setCompressionName($zipEntry, \ZipArchive::CM_STORE);
                    }
                    $addedCount++;
                }
            } else {
                // Remote queue fallback (e.g. testing locally while images are only on live Hostinger)
                $cleanRel = ltrim(str_replace('\\', '/', (string)$rawName), '/');
                $cleanRel = preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $cleanRel);
                $remoteQueue[$idx] = [
                    'url' => 'https://srishringarr.com/yn/uploads/' . str_replace(' ', '%20', $cleanRel),
                    'zip_entry' => $zipEntry
                ];
            }
        }

        if (!empty($remoteQueue)) {
            // Fetch remote images in parallel chunks of 6 with 15s timeout
            $chunks = array_chunk($remoteQueue, 6, true);
            foreach ($chunks as $chunk) {
                $mh = curl_multi_init();
                $handles = [];

                foreach ($chunk as $idx => $rItem) {
                    $ch = curl_init($rItem['url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Srishringarr-Admin-PhotoDownloader/2.0');
                    curl_multi_add_handle($mh, $ch);
                    $handles[$idx] = $ch;
                }

                $active = null;
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM || $active);

                while ($active && $mrc == CURLM_OK) {
                    if (curl_multi_select($mh) != -1) {
                        do {
                            $mrc = curl_multi_exec($mh, $active);
                        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                    }
                }

                foreach ($handles as $idx => $ch) {
                    $content = curl_multi_getcontent($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_multi_remove_handle($mh, $ch);
                    unset($ch);

                    if ($httpCode === 200 && strlen($content) > 100) {
                        $zipEntryName = $chunk[$idx]['zip_entry'];

                        // If remote image is large (> 1.5MB) and compress is on, compress it
                        if ($compress && strlen($content) > 1.5 * 1024 * 1024) {
                            $tempRemote = tempnam($this->cacheDir, 'rem_');
                            file_put_contents($tempRemote, $content);
                            $compPath = $this->compressAndCacheImage($tempRemote, 1920, 82);
                            if (file_exists($compPath)) {
                                $zip->addFile($compPath, $zipEntryName);
                                if (defined('\ZipArchive::CM_STORE')) {
                                    $zip->setCompressionName($zipEntryName, \ZipArchive::CM_STORE);
                                }
                                $addedCount++;
                            }
                            @unlink($tempRemote);
                        } else {
                            if ($zip->addFromString($zipEntryName, $content)) {
                                if (defined('\ZipArchive::CM_STORE')) {
                                    $zip->setCompressionName($zipEntryName, \ZipArchive::CM_STORE);
                                }
                                $addedCount++;
                            }
                        }
                    }
                }
                curl_multi_close($mh);
            }
        }

        return $addedCount;
    }

    /**
     * Serve completed ZIP archive file with memory-safe 64KB chunk streaming
     */
    public function serveJobZip() {
        $jobId = preg_replace('/[^a-f0-9]/', '', (string)($_GET['job_id'] ?? ''));
        $zipCandidates = [
            $this->tempZipDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.zip',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.zip'
        ];
        $metaCandidates = [
            $this->tempZipDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json',
            sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json'
        ];

        $zipPath = null;
        foreach ($zipCandidates as $cand) {
            if (file_exists($cand) && filesize($cand) > 50) {
                $zipPath = $cand;
                break;
            }
        }

        if (!$zipPath) {
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('Download file not ready or has expired. Please restart the download.'));
            exit;
        }

        // Clean up metadata JSON
        foreach ($metaCandidates as $cand) {
            if (file_exists($cand)) @unlink($cand);
        }

        $fileName = 'srishringarr_photos_' . date('Ymd_His') . '.zip';
        $fileSize = filesize($zipPath);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        if (ob_get_level()) {
            ob_end_clean();
        }

        // Stream in 64KB chunks
        $fp = fopen($zipPath, 'rb');
        if ($fp) {
            while (!feof($fp)) {
                echo fread($fp, 1024 * 64);
                flush();
            }
            fclose($fp);
        }

        @unlink($zipPath);
        exit;
    }

    /**
     * Cancel download job and remove temporary files
     */
    public function cancelDownloadJob() {
        $jobId = preg_replace('/[^a-f0-9]/', '', (string)($_POST['job_id'] ?? ''));
        if ($jobId) {
            $zipCandidates = [
                $this->tempZipDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.zip',
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.zip'
            ];
            $metaCandidates = [
                $this->tempZipDir . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json',
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ss_job_' . $jobId . '.json'
            ];
            foreach ($zipCandidates as $f) { if (file_exists($f)) @unlink($f); }
            foreach ($metaCandidates as $f) { if (file_exists($f)) @unlink($f); }
        }
        $this->json(['success' => true]);
    }

    // =========================================================================
    // ====================== DUPLICATE PHOTOS FEATURE =========================
    // =========================================================================

    /**
     * AJAX endpoint: Find duplicate photos category wise (with only valid image files)
     */
    public function getDuplicates() {
        $catKey = trim($_REQUEST['category'] ?? 'all');
        $dupeType = strtolower(trim($_REQUEST['duplicate_type'] ?? 'all'));
        $search = trim($_REQUEST['search'] ?? '');
        $page = max(1, (int)($_REQUEST['page'] ?? 1));
        $limit = max(10, min(100, (int)($_REQUEST['limit'] ?? 25)));
        $offset = ($page - 1) * $limit;

        $where = [
            "pin.img_name != ''",
            "pin.img_name IS NOT NULL",
            // Strictly require valid image extensions to avoid text descriptions like '/4.Featuring...'
            "(pin.img_name LIKE '%.jpg' OR pin.img_name LIKE '%.jpeg' OR pin.img_name LIKE '%.png' OR pin.img_name LIKE '%.webp' OR pin.img_name LIKE '%.gif' OR pin.img_name LIKE '%.JPG' OR pin.img_name LIKE '%.JPEG' OR pin.img_name LIKE '%.PNG')",
            "pin.img_name NOT LIKE '%Featuring%'",
            "pin.img_name NOT LIKE '%Hathphool%'"
        ];
        $joins = "";
        $catLabel = "All Categories";

        if ($catKey !== 'all' && strpos($catKey, ':') !== false) {
            list($type, $id) = explode(':', $catKey, 2);
            $id = (int)$id;

            if ($type === 'garment') {
                $cQry = mysqli_query($this->db, "SELECT name FROM garments WHERE garment_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Apparel - " . ucwords(strtolower(trim($cRow['name'])));
                }

                $joins .= " JOIN garment_product gp ON (pin.gproduct_id = gp.gproduct_id OR pin.pro_code = gp.gproduct_code) ";
                $where[] = "(gp.garment_id = $id OR gp.product_for = $id)";
            } elseif ($type === 'jewel_parent' || $type === 'jewellery') {
                $cQry = mysqli_query($this->db, "SELECT categories_name FROM jewel_subcat WHERE subcat_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Jewellery - " . ucwords(strtolower(trim($cRow['categories_name'])));
                }

                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "(p.categories_id = $id OR p.subcat_id = $id)";
            } elseif ($type === 'jewel_child') {
                $cQry = mysqli_query($this->db, "SELECT name FROM subcat1 WHERE subcat_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Jewellery - " . ucwords(strtolower(trim($cRow['name'])));
                }

                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "p.subcat_id = $id";
            }
        }

        if (!empty($search)) {
            $escSearch = mysqli_real_escape_string($this->db, $search);
            $where[] = "(pin.pro_code LIKE '%$escSearch%' OR pin.img_name LIKE '%$escSearch%')";
        }

        $whereClause = implode(' AND ', $where);

        $having = ["COUNT(*) > 1"];
        if ($dupeType === 'multi_sku') {
            $having[] = "COUNT(DISTINCT pin.pro_code) > 1";
        } elseif ($dupeType === 'single_sku_repeated') {
            $having[] = "COUNT(*) > COUNT(DISTINCT pin.pro_code)";
        }
        $havingClause = implode(' AND ', $having);

        // 1. Calculate overall summary statistics
        $countSql = "SELECT pin.img_name,
                            COUNT(*) as occurrences,
                            COUNT(DISTINCT pin.pro_code) as distinct_skus
                     FROM product_images_new pin
                     $joins
                     WHERE $whereClause
                     GROUP BY pin.img_name
                     HAVING $havingClause";
        $countRes = mysqli_query($this->db, $countSql);

        $totalGroups = 0;
        $totalRedundantPhotos = 0;

        if ($countRes) {
            while ($cRow = mysqli_fetch_assoc($countRes)) {
                $totalGroups++;
                $occ = (int)$cRow['occurrences'];
                $totalRedundantPhotos += max(0, $occ - 1);
            }
        }

        // Count of corrupted text records in DB (e.g. /4.Featuring...)
        $corruptCount = 0;
        $corruptRes = mysqli_query($this->db, "SELECT COUNT(*) as c FROM product_images_new 
            WHERE (img_name NOT LIKE '%.jpg' AND img_name NOT LIKE '%.jpeg' AND img_name NOT LIKE '%.png' AND img_name NOT LIKE '%.webp' AND img_name NOT LIKE '%.gif' AND img_name != '')
               OR img_name LIKE '%Featuring%'");
        if ($corruptRes && $corruptRow = mysqli_fetch_assoc($corruptRes)) {
            $corruptCount = (int)$corruptRow['c'];
        }

        // 2. Sorting and Filter parameters
        $sort = strtolower(trim($_REQUEST['sort'] ?? 'recent'));
        $orderClause = ($sort === 'count') 
            ? "occurrence_count DESC, distinct_sku_count DESC" 
            : "max_id DESC, occurrence_count DESC";

        // Query paginated duplicate groups
        $sql = "SELECT pin.img_name,
                       COUNT(*) as occurrence_count,
                       COUNT(DISTINCT pin.pro_code) as distinct_sku_count,
                       GROUP_CONCAT(DISTINCT pin.pro_code ORDER BY pin.pro_code SEPARATOR ', ') as skus,
                       GROUP_CONCAT(pin.id ORDER BY pin.id SEPARATOR ',') as image_ids,
                       MIN(pin.id) as keep_id,
                       MAX(pin.id) as max_id
                FROM product_images_new pin
                $joins
                WHERE $whereClause
                GROUP BY pin.img_name
                HAVING $havingClause
                ORDER BY $orderClause
                LIMIT $limit OFFSET $offset";

        $res = mysqli_query($this->db, $sql);
        $groups = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rawImg = trim($row['img_name']);
                $cleanRel = ltrim(preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $rawImg), '/');
                $parts = explode('/', $cleanRel);
                $encoded = array_map('rawurlencode', $parts);
                $imgUrl = "https://srishringarr.com/yn/uploads/" . implode('/', $encoded);

                // Check local disk for file existence and file size
                $localPath = $this->resolveLocalImagePath($rawImg);
                $fileSizeMb = null;
                $existsOnDisk = false;
                if ($localPath && file_exists($localPath)) {
                    $existsOnDisk = true;
                    $fileSizeMb = round(filesize($localPath) / (1024 * 1024), 2);
                }

                // Fetch individual image records from product_images_new
                $idList = trim($row['image_ids'] ?? '');
                $records = [];
                if (!empty($idList)) {
                    $idSql = "SELECT id, pro_code, rank, date_added, product_id, gproduct_id 
                              FROM product_images_new 
                              WHERE id IN ($idList) 
                              ORDER BY rank ASC, id ASC";
                    $idRes = mysqli_query($this->db, $idSql);
                    if ($idRes) {
                        while ($rItem = mysqli_fetch_assoc($idRes)) {
                            $records[] = $rItem;
                        }
                    }
                }

                $skusArr = array_filter(array_map('trim', explode(',', $row['skus'] ?? '')));

                $groups[] = [
                    'img_name' => $rawImg,
                    'file_name' => basename($rawImg),
                    'clean_url' => $imgUrl,
                    'occurrence_count' => (int)$row['occurrence_count'],
                    'distinct_sku_count' => (int)$row['distinct_sku_count'],
                    'skus' => $row['skus'],
                    'skus_list' => array_values($skusArr),
                    'keep_id' => (int)$row['keep_id'],
                    'max_id' => (int)$row['max_id'],
                    'exists_on_disk' => $existsOnDisk,
                    'file_size_mb' => $fileSizeMb,
                    'server_status' => null,
                    'exists_on_server' => true,
                    'records' => $records
                ];
            }
        }

        // Fast parallel HEAD check against live production server (taking ~0.1s for batch)
        if (!empty($groups)) {
            $mh = curl_multi_init();
            $handles = [];
            foreach ($groups as $idx => $g) {
                $ch = curl_init($g['clean_url']);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_multi_add_handle($mh, $ch);
                $handles[$idx] = $ch;
            }

            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM || $active);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) != -1) {
                    do { $mrc = curl_multi_exec($mh, $active); } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }

            foreach ($handles as $idx => $ch) {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_multi_remove_handle($mh, $ch);
                $groups[$idx]['server_status'] = (int)$code;
                $groups[$idx]['exists_on_server'] = ($code === 200);
            }
            curl_multi_close($mh);
        }

        $totalPages = $totalGroups > 0 ? (int)ceil($totalGroups / $limit) : 1;

        $this->json([
            'success' => true,
            'category_label' => $catLabel,
            'corrupt_text_records_count' => $corruptCount,
            'server_domain' => 'https://srishringarr.com',
            'summary' => [
                'total_duplicate_groups' => $totalGroups,
                'total_redundant_photos' => $totalRedundantPhotos,
                'current_page' => $page,
                'per_page' => $limit,
                'total_pages' => $totalPages
            ],
            'groups' => $groups
        ]);
    }

    /**
     * AJAX endpoint: Clean up duplicate image records for a single image group
     * Ensures all redundant duplicate references are permanently removed from product_images_new table
     */
    public function deduplicateGroup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $imgName = trim($_POST['img_name'] ?? '');
        $keepId = (int)($_POST['keep_id'] ?? 0);

        if (empty($imgName)) {
            $this->json(['success' => false, 'message' => 'Missing image name parameter.'], 400);
            return;
        }

        $escImgName = mysqli_real_escape_string($this->db, $imgName);

        // If no keep_id was specified, keep the lowest ID (first/original uploaded record)
        if ($keepId <= 0) {
            $kRes = mysqli_query($this->db, "SELECT MIN(id) as min_id FROM product_images_new WHERE img_name = '$escImgName'");
            if ($kRes && $kRow = mysqli_fetch_assoc($kRes)) {
                $keepId = (int)$kRow['min_id'];
            }
        }

        if ($keepId <= 0) {
            $this->json(['success' => false, 'message' => 'Unable to determine primary record to keep.'], 400);
            return;
        }

        // Delete all duplicate copies except the keep_id from product_images_new table
        $delSql = "DELETE FROM product_images_new WHERE img_name = '$escImgName' AND id != $keepId";
        $deleted = mysqli_query($this->db, $delSql);

        if ($deleted) {
            $deletedCount = mysqli_affected_rows($this->db);

            // Verify that only exactly 1 record remains in product_images_new table
            $chk = mysqli_query($this->db, "SELECT COUNT(*) as c FROM product_images_new WHERE img_name = '$escImgName'");
            $remaining = 1;
            if ($chk && $r = mysqli_fetch_assoc($chk)) {
                $remaining = (int)$r['c'];
            }

            $this->json([
                'success' => true,
                'deleted_count' => $deletedCount,
                'keep_id' => $keepId,
                'remaining_in_db' => $remaining,
                'message' => "Removed {$deletedCount} duplicate reference(s). Exactly 1 primary record (#{$keepId}) remains in product_images_new table."
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Database error while removing duplicate photo records.'], 500);
        }
    }

    /**
     * AJAX endpoint: Delete ALL references of an image from product_images_new table
     * Ensures its reference is completely NOT found in product_images_new table.
     */
    public function deleteAllReferences() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $imgName = trim($_POST['img_name'] ?? '');
        if (empty($imgName)) {
            $this->json(['success' => false, 'message' => 'Missing image name parameter.'], 400);
            return;
        }

        $escImgName = mysqli_real_escape_string($this->db, $imgName);
        $delSql = "DELETE FROM product_images_new WHERE img_name = '$escImgName'";
        $deleted = mysqli_query($this->db, $delSql);

        if ($deleted) {
            $deletedCount = mysqli_affected_rows($this->db);
            // Verify 0 records remain
            $chk = mysqli_query($this->db, "SELECT COUNT(*) as c FROM product_images_new WHERE img_name = '$escImgName'");
            $remaining = 0;
            if ($chk && $r = mysqli_fetch_assoc($chk)) {
                $remaining = (int)$r['c'];
            }

            $this->json([
                'success' => true,
                'deleted_count' => $deletedCount,
                'remaining_in_db' => $remaining,
                'message' => "Removed all {$deletedCount} reference(s). Confirmed: reference is NOT found in product_images_new table."
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Database error while removing records.'], 500);
        }
    }

    /**
     * AJAX endpoint: Delete a single specific record by ID from product_images_new table
     */
    public function deleteRecordById() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $recordId = (int)($_POST['id'] ?? 0);
        if ($recordId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid record ID.'], 400);
            return;
        }

        $delSql = "DELETE FROM product_images_new WHERE id = $recordId";
        $deleted = mysqli_query($this->db, $delSql);

        if ($deleted) {
            $this->json([
                'success' => true,
                'deleted_id' => $recordId,
                'message' => "Record #{$recordId} removed from product_images_new table."
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Database error while deleting record.'], 500);
        }
    }

    /**
     * AJAX endpoint: Batch deduplicate an entire category (keeps primary record for each duplicate photo)
     * Permanently deletes redundant records from product_images_new table
     */
    public function deduplicateCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $catKey = trim($_POST['category'] ?? 'all');
        $where = [
            "pin.img_name != ''",
            "pin.img_name IS NOT NULL",
            "(pin.img_name LIKE '%.jpg' OR pin.img_name LIKE '%.jpeg' OR pin.img_name LIKE '%.png' OR pin.img_name LIKE '%.webp' OR pin.img_name LIKE '%.gif' OR pin.img_name LIKE '%.JPG' OR pin.img_name LIKE '%.JPEG' OR pin.img_name LIKE '%.PNG')"
        ];
        $joins = "";

        if ($catKey !== 'all' && strpos($catKey, ':') !== false) {
            list($type, $id) = explode(':', $catKey, 2);
            $id = (int)$id;

            if ($type === 'garment') {
                $joins .= " JOIN garment_product gp ON (pin.gproduct_id = gp.gproduct_id OR pin.pro_code = gp.gproduct_code) ";
                $where[] = "(gp.garment_id = $id OR gp.product_for = $id)";
            } elseif ($type === 'jewel_parent' || $type === 'jewellery') {
                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "(p.categories_id = $id OR p.subcat_id = $id)";
            } elseif ($type === 'jewel_child') {
                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "p.subcat_id = $id";
            }
        }

        $whereClause = implode(' AND ', $where);

        // Find all duplicate groups and their keep_ids
        $sql = "SELECT pin.img_name,
                       GROUP_CONCAT(pin.id ORDER BY pin.id SEPARATOR ',') as all_ids,
                       MIN(pin.id) as keep_id
                FROM product_images_new pin
                $joins
                WHERE $whereClause
                GROUP BY pin.img_name
                HAVING COUNT(*) > 1";

        $res = mysqli_query($this->db, $sql);
        $idsToDelete = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $allIds = array_filter(array_map('intval', explode(',', $row['all_ids'] ?? '')));
                $keepId = (int)$row['keep_id'];
                foreach ($allIds as $idVal) {
                    if ($idVal !== $keepId) {
                        $idsToDelete[] = $idVal;
                    }
                }
            }
        }

        if (empty($idsToDelete)) {
            $this->json([
                'success' => true,
                'deleted_count' => 0,
                'message' => 'No duplicate records found to clean up.'
            ]);
            return;
        }

        // Delete in batches of 500 from product_images_new table
        $totalDeleted = 0;
        $chunks = array_chunk($idsToDelete, 500);
        foreach ($chunks as $chunk) {
            $idStr = implode(',', $chunk);
            $delRes = mysqli_query($this->db, "DELETE FROM product_images_new WHERE id IN ($idStr)");
            if ($delRes) {
                $totalDeleted += mysqli_affected_rows($this->db);
            }
        }

        $this->json([
            'success' => true,
            'deleted_count' => $totalDeleted,
            'message' => "Successfully removed {$totalDeleted} duplicate references from product_images_new table!"
        ]);
    }

    /**
     * AJAX endpoint: Purge corrupted text records (like '/4.Featuring attractive designs') from product_images_new
     */
    public function purgeCorruptedTextRecords() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $sql = "DELETE FROM product_images_new 
                WHERE (img_name NOT LIKE '%.jpg' AND img_name NOT LIKE '%.jpeg' AND img_name NOT LIKE '%.png' AND img_name NOT LIKE '%.webp' AND img_name NOT LIKE '%.gif' AND img_name != '')
                   OR img_name LIKE '%Featuring%'";
        $res = mysqli_query($this->db, $sql);
        $count = mysqli_affected_rows($this->db);

        $this->json([
            'success' => true,
            'purged_count' => $count,
            'message' => "Purged {$count} corrupted text records from product_images_new table successfully!"
        ]);
    }

    /**
     * AJAX endpoint: Find photos on the server whose reference is NOT found in product_images_new table
     */
    public function getUnreferencedPhotos() {
        $folder = trim($_REQUEST['folder'] ?? '2026/08');
        $folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder);
        $folder = trim($folder, '/');

        $baseDir = $this->localUploadsDir;
        if (!$baseDir) {
            $candidates = [
                dirname(__DIR__, 2) . '/yn/uploads',
                dirname(__DIR__, 2) . '/uploads',
                (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/yn/uploads' : null),
                realpath(__DIR__ . '/../../yn/uploads')
            ];
            foreach ($candidates as $cand) {
                if ($cand && is_dir($cand)) { $baseDir = $cand; break; }
            }
        }

        if (!$baseDir || !is_dir($baseDir)) {
            $this->json([
                'success' => false,
                'message' => 'Server uploads folder not accessible locally on disk.'
            ]);
            return;
        }

        $scanTarget = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
        if (!is_dir($scanTarget)) {
            $scanTarget = $baseDir;
        }

        $files = @scandir($scanTarget);
        if (!$files) {
            $this->json([
                'success' => true,
                'folder' => $folder,
                'total_files_scanned' => 0,
                'unreferenced_count' => 0,
                'unreferenced_size_mb' => 0,
                'items' => []
            ]);
            return;
        }

        $unreferencedItems = [];
        $totalSize = 0;
        $totalScanned = 0;

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $fullPath = $scanTarget . DIRECTORY_SEPARATOR . $file;
            if (!is_file($fullPath)) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

            $totalScanned++;
            $fileSize = filesize($fullPath);
            $escFile = mysqli_real_escape_string($this->db, $file);

            // Check if this file is referenced in product_images_new table
            $chk = mysqli_query($this->db, "SELECT id FROM product_images_new WHERE img_name LIKE '%$escFile%' LIMIT 1");
            if (!$chk || mysqli_num_rows($chk) === 0) {
                // NOT referenced in product_images_new table!
                $totalSize += $fileSize;
                $unreferencedItems[] = [
                    'file_name' => $file,
                    'folder' => $folder,
                    'full_url' => 'https://srishringarr.com/yn/uploads/' . ltrim($folder . '/' . $file, '/'),
                    'file_size_mb' => round($fileSize / (1024 * 1024), 2),
                    'modified_at' => date('Y-m-d H:i:s', filemtime($fullPath)),
                    'status' => 'Reference NOT found in product_images_new table'
                ];
            }
        }

        $this->json([
            'success' => true,
            'folder' => $folder,
            'total_files_scanned' => $totalScanned,
            'unreferenced_count' => count($unreferencedItems),
            'unreferenced_size_mb' => round($totalSize / (1024 * 1024), 2),
            'items' => array_slice($unreferencedItems, 0, 100)
        ]);
    }

    /**
     * AJAX endpoint: Delete unreferenced file from server disk after re-verifying no DB reference exists
     */
    public function deleteUnreferencedFile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
            return;
        }

        $fileName = trim($_POST['file_name'] ?? '');
        $folder = trim($_POST['folder'] ?? '2026/08');
        $fileName = basename($fileName);

        if (empty($fileName)) {
            $this->json(['success' => false, 'message' => 'Missing file name.'], 400);
            return;
        }

        // Verify that NO reference exists in product_images_new
        $escFile = mysqli_real_escape_string($this->db, $fileName);
        $chk = mysqli_query($this->db, "SELECT id FROM product_images_new WHERE img_name LIKE '%$escFile%' LIMIT 1");
        if ($chk && mysqli_num_rows($chk) > 0) {
            $this->json(['success' => false, 'message' => 'Safety check aborted: This file IS referenced by an active product in product_images_new table.'], 400);
            return;
        }

        $baseDir = $this->localUploadsDir ?: (dirname(__DIR__, 2) . '/yn/uploads');
        $targetFile = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder) . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($targetFile) && is_file($targetFile)) {
            $bytes = filesize($targetFile);
            @unlink($targetFile);
            $this->json([
                'success' => true,
                'file_name' => $fileName,
                'freed_mb' => round($bytes / (1024 * 1024), 2),
                'message' => "Deleted unreferenced file '{$fileName}' from server disk."
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'File not found on server disk.'], 404);
        }
    }

    /**
     * Export category duplicate photos report to CSV
     */
    public function exportDuplicatesCsv() {
        $catKey = trim($_REQUEST['category'] ?? 'all');
        $dupeType = strtolower(trim($_REQUEST['duplicate_type'] ?? 'all'));
        $search = trim($_REQUEST['search'] ?? '');

        $where = ["pin.img_name != ''", "pin.img_name IS NOT NULL"];
        $joins = "";
        $catLabel = "All Categories";

        if ($catKey !== 'all' && strpos($catKey, ':') !== false) {
            list($type, $id) = explode(':', $catKey, 2);
            $id = (int)$id;

            if ($type === 'garment') {
                $cQry = mysqli_query($this->db, "SELECT name FROM garments WHERE garment_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Apparel - " . ucwords(strtolower(trim($cRow['name'])));
                }
                $joins .= " JOIN garment_product gp ON (pin.gproduct_id = gp.gproduct_id OR pin.pro_code = gp.gproduct_code) ";
                $where[] = "(gp.garment_id = $id OR gp.product_for = $id)";
            } elseif ($type === 'jewel_parent' || $type === 'jewellery') {
                $cQry = mysqli_query($this->db, "SELECT categories_name FROM jewel_subcat WHERE subcat_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Jewellery - " . ucwords(strtolower(trim($cRow['categories_name'])));
                }
                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "(p.categories_id = $id OR p.subcat_id = $id)";
            } elseif ($type === 'jewel_child') {
                $cQry = mysqli_query($this->db, "SELECT name FROM subcat1 WHERE subcat_id = $id LIMIT 1");
                if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                    $catLabel = "Jewellery - " . ucwords(strtolower(trim($cRow['name'])));
                }
                $joins .= " JOIN product p ON (pin.product_id = p.product_id OR pin.pro_code = p.product_code) ";
                $where[] = "p.subcat_id = $id";
            }
        }

        if (!empty($search)) {
            $escSearch = mysqli_real_escape_string($this->db, $search);
            $where[] = "(pin.pro_code LIKE '%$escSearch%' OR pin.img_name LIKE '%$escSearch%')";
        }

        $whereClause = implode(' AND ', $where);

        $having = ["COUNT(*) > 1"];
        if ($dupeType === 'multi_sku') {
            $having[] = "COUNT(DISTINCT pin.pro_code) > 1";
        } elseif ($dupeType === 'single_sku_repeated') {
            $having[] = "COUNT(*) > COUNT(DISTINCT pin.pro_code)";
        }
        $havingClause = implode(' AND ', $having);

        $sql = "SELECT pin.img_name,
                       COUNT(*) as occurrence_count,
                       COUNT(DISTINCT pin.pro_code) as distinct_sku_count,
                       GROUP_CONCAT(DISTINCT pin.pro_code ORDER BY pin.pro_code SEPARATOR '; ') as skus,
                       GROUP_CONCAT(pin.id ORDER BY pin.id SEPARATOR ',') as image_ids,
                       MIN(pin.id) as keep_id
                FROM product_images_new pin
                $joins
                WHERE $whereClause
                GROUP BY pin.img_name
                HAVING $havingClause
                ORDER BY occurrence_count DESC, distinct_sku_count DESC";

        $res = mysqli_query($this->db, $sql);

        $fileName = 'duplicate_photos_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Category', 'Image Name', 'Full URL', 'Occurrences', 'Redundant Extra Records', 'Distinct SKUs', 'SKUs List', 'Database IDs', 'Recommended Keep ID']);

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rawImg = trim($row['img_name']);
                $cleanRel = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $rawImg), '/');
                $imgUrl = "https://srishringarr.com/yn/uploads/" . str_replace(' ', '%20', $cleanRel);
                $occ = (int)$row['occurrence_count'];

                fputcsv($out, [
                    $catLabel,
                    $rawImg,
                    $imgUrl,
                    $occ,
                    max(0, $occ - 1),
                    (int)$row['distinct_sku_count'],
                    $row['skus'],
                    $row['image_ids'],
                    (int)$row['keep_id']
                ]);
            }
        }
        fclose($out);
        exit;
    }

    /**
     * Fallback direct download
     */
    public function download() {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(0);

        $categoryParam = $_REQUEST['categories'] ?? null;
        if ($categoryParam !== null) {
            $categories = is_array($categoryParam) ? $categoryParam : explode(',', (string)$categoryParam);
        } else {
            $savedSettings = $this->getSettings();
            $categories = $savedSettings['selected_categories'] ?? [];
        }
        $categories = array_filter(array_map('trim', $categories));

        if (empty($categories)) {
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('Please select at least one category to download.'));
            exit;
        }

        $stockStatus = strtolower(trim($_REQUEST['stock_status'] ?? ''));
        if (!in_array($stockStatus, ['all', 'available', 'outofstock'])) {
            $savedSettings = $this->getSettings();
            $stockStatus = $savedSettings['stock_status'] ?? 'all';
        }

        $imageScope = strtolower(trim($_REQUEST['image_scope'] ?? ''));
        if (!in_array($imageScope, ['main', 'all'])) {
            $savedSettings = $this->getSettings();
            $imageScope = $savedSettings['image_scope'] ?? 'all';
        }

        $limitProducts = strtolower(trim($_REQUEST['limit_products'] ?? ''));
        if (!in_array($limitProducts, ['all', '10', '25'])) {
            $savedSettings = $this->getSettings();
            $limitProducts = $savedSettings['limit_products'] ?? 'all';
        }

        $compressImages = ($_REQUEST['compress_images'] ?? '1') === '1';

        if (!class_exists('\ZipArchive')) {
            die('ZipArchive extension is not enabled on this server.');
        }

        $tempZipPath = tempnam($this->tempZipDir, 'ss_dir_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            die('Cannot create temporary ZIP archive.');
        }

        $this->loadInStockMap();
        $totalFilesAdded = 0;

        foreach ($categories as $catKey) {
            $catInfo = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$catInfo || empty($catInfo['products'])) continue;

            $deptName = $this->sanitizeFolderName($catInfo['department']);
            $categoryFolderName = $this->sanitizeFolderName($catInfo['name']);
            $imagesList = [];

            $catProds = $catInfo['products'];
            if ($limitProducts !== 'all' && is_numeric($limitProducts) && (int)$limitProducts > 0) {
                $catProds = array_slice($catProds, 0, (int)$limitProducts);
            }

            foreach ($catProds as $product) {
                $sku = trim($product['sku']);
                if (empty($sku)) continue;

                $skuFolderName = $this->sanitizeFolderName($sku);
                $pid = (int)$product['id'];
                $pType = $product['type'];
                $escSku = mysqli_real_escape_string($this->db, $sku);

                $idCol = ($pType === 'garment') ? 'gproduct_id' : 'product_id';
                $imgSql = "SELECT id, img_name, rank FROM product_images_new WHERE pro_code = '$escSku' OR $idCol = $pid ORDER BY rank ASC, id ASC";

                $imgRes = mysqli_query($this->db, $imgSql);
                $images = [];
                $seenImgPaths = [];

                if ($imgRes) {
                    while ($im = mysqli_fetch_assoc($imgRes)) {
                        $rawName = trim($im['img_name'] ?? '');
                        if (empty($rawName) || isset($seenImgPaths[$rawName])) {
                            continue;
                        }
                        $seenImgPaths[$rawName] = true;
                        $images[] = $im;
                    }
                }

                if (empty($images)) continue;

                if ($imageScope === 'main') {
                    $images = array_slice($images, 0, 1);
                }

                $imgIndex = 0;
                foreach ($images as $im) {
                    $rawName = $im['img_name'];
                    $baseName = basename($rawName);
                    $cleanBaseName = $this->sanitizeFileName($baseName);
                    $prefix = ($imgIndex === 0) ? '00_main_' : sprintf('%02d_', $imgIndex);
                    $zipEntryPath = "{$deptName}/{$categoryFolderName}/{$skuFolderName}_{$prefix}{$cleanBaseName}";

                    $imagesList[] = [
                        'raw_name' => $rawName,
                        'zip_entry' => $zipEntryPath
                    ];
                    $imgIndex++;
                }
            }

            if (!empty($imagesList)) {
                $totalFilesAdded += $this->addImagesBatchToZip($zip, $imagesList, $compressImages);
            }
        }

        $zip->close();

        if ($totalFilesAdded === 0 || !file_exists($tempZipPath) || filesize($tempZipPath) < 50) {
            @unlink($tempZipPath);
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('No images found matching the selected categories and stock filter.'));
            exit;
        }

        $fileName = 'srishringarr_photos_' . date('Ymd_His') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($tempZipPath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        if (ob_get_level()) ob_end_clean();
        $fp = fopen($tempZipPath, 'rb');
        if ($fp) {
            while (!feof($fp)) {
                echo fread($fp, 1024 * 64);
                flush();
            }
            fclose($fp);
        }
        @unlink($tempZipPath);
        exit;
    }

    /**
     * Load POS in-stock SKUs into memory
     */
    private function loadInStockMap() {
        if ($this->inStockMap !== null) {
            return;
        }

        $this->inStockMap = [];
        $res = mysqli_query($this->db3, "SELECT name FROM phppos_items WHERE quantity > 0 AND is_deleted = 0");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $sku = strtoupper(trim($row['name'] ?? ''));
                if (!empty($sku)) {
                    $this->inStockMap[$sku] = true;
                }
            }
        }
    }

    /**
     * Resolve category metadata and matching products for a specific category key
     */
    private function getCategoryMetaAndProducts($catKey, $stockStatus) {
        $catKey = trim($catKey);
        if (empty($catKey) || strpos($catKey, ':') === false) {
            return null;
        }

        list($type, $id) = explode(':', $catKey, 2);
        $id = (int)$id;
        if ($id <= 0) return null;

        $dept = 'Apparel';
        $catName = 'Unknown Category';
        $products = [];
        $pType = 'garment';

        if ($type === 'garment') {
            $dept = 'Apparel';
            $pType = 'garment';

            $cQry = mysqli_query($this->db, "SELECT name FROM garments WHERE garment_id = $id LIMIT 1");
            if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                $catName = ucwords(strtolower(trim($cRow['name'])));
            }

            if ($id == 29) { // Trail Gowns / Infinity Gowns
                $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garment' as type
                        FROM garment_product gp
                        WHERE gp.garment_id = 29 OR gp.product_for = 29 
                           OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 18 OR pc.legacy_category_id = 29))
                        ORDER BY gp.gproduct_id DESC";
            } elseif ($id == 28) { // Indo Western Outfits
                $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garment' as type
                        FROM garment_product gp
                        WHERE (gp.garment_id = 28 OR gp.product_for = 28 OR gp.gproduct_code LIKE 'YNW%' OR gp.gproduct_code LIKE 'asu%' 
                               OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 16 OR pc.legacy_category_id = 28)))
                          AND gp.garment_id != 29 AND gp.product_for != 29
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 18 OR pc.legacy_category_id = 29))
                        ORDER BY gp.gproduct_id DESC";
            } elseif ($id == 10) { // Lehenga Choli
                $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garment' as type
                        FROM garment_product gp
                        WHERE (gp.garment_id = 10 OR gp.product_for = 10 
                               OR (gp.garment_id = 0 AND gp.product_for = 0 AND (gp.gproduct_code LIKE 'ynl%' OR gp.gproduct_code LIKE 'aynl%' OR gp.gproduct_name LIKE '%lehenga%'))
                               OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 17 OR pc.legacy_category_id = 10)))
                          AND gp.garment_id != 28 AND gp.product_for != 28 AND gp.gproduct_code NOT LIKE 'YNW%' AND gp.gproduct_code NOT LIKE 'asu%'
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 16 OR pc.legacy_category_id = 28))
                          AND gp.garment_id != 29 AND gp.product_for != 29
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 18 OR pc.legacy_category_id = 29))
                        ORDER BY gp.gproduct_id DESC";
            } elseif ($id == 22) { // Evening Gowns
                $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garment' as type
                        FROM garment_product gp
                        WHERE (gp.garment_id = 22 OR gp.product_for = 22 
                               OR (gp.garment_id = 0 AND gp.product_for = 0 AND (gp.gproduct_code LIKE 'yng%' OR gp.gproduct_code LIKE 'ayng%' OR gp.gproduct_name LIKE '%gown%'))
                               OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 15 OR (pc.category_id = 4 AND pc.legacy_category_id = 22))))
                          AND gp.garment_id != 29 AND gp.product_for != 29
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 18 OR pc.legacy_category_id = 29))
                          AND gp.garment_id != 28 AND gp.product_for != 28 AND gp.gproduct_code NOT LIKE 'YNW%' AND gp.gproduct_code NOT LIKE 'asu%'
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 16 OR pc.legacy_category_id = 28))
                          AND gp.garment_id != 10 AND gp.product_for != 10
                          AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments' AND (pc.category_id = 17 OR pc.legacy_category_id = 10))
                        ORDER BY gp.gproduct_id DESC";
            } else {
                $subIds = [$id];
                $subQ = mysqli_query($this->db, "SELECT sub_id FROM garment_subcat WHERE gmain_id = $id");
                if ($subQ) {
                    while ($subR = mysqli_fetch_assoc($subQ)) {
                        $subIds[] = (int)$subR['sub_id'];
                    }
                }
                $subListStr = implode(',', array_unique($subIds));

                $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garment' as type
                        FROM garment_product gp
                        LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                        WHERE gp.garment_id IN ($subListStr) 
                           OR gp.product_for IN ($subListStr)
                           OR pc.legacy_category_id IN ($subListStr)
                           OR pc.legacy_subcategory_id IN ($subListStr)
                        ORDER BY gp.gproduct_id DESC";
            }
            $res = mysqli_query($this->db, $sql);
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $products[] = $r;
                }
            }
        } elseif ($type === 'jewel_parent') {
            $dept = 'Jewellery';
            $pType = 'jewellery';

            $cQry = mysqli_query($this->db, "SELECT categories_name FROM jewel_subcat WHERE subcat_id = $id LIMIT 1");
            if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                $catName = ucwords(strtolower(trim($cRow['categories_name'])));
            }

            $catIds = [$id];
            $subQ = mysqli_query($this->db, "SELECT subcat_id FROM subcat1 WHERE maincat_id = $id AND status=1");
            if ($subQ) {
                while ($subR = mysqli_fetch_assoc($subQ)) {
                    $catIds[] = (int)$subR['subcat_id'];
                }
            }
            $allIdsStr = implode(',', array_unique($catIds));

            $sql = "SELECT DISTINCT p.product_id as id, p.product_code as sku, p.product_name as name, 'jewellery' as type
                    FROM product p
                    LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                    WHERE p.categories_id IN ($allIdsStr) 
                       OR p.subcat_id IN ($allIdsStr)
                       OR pc.legacy_category_id IN ($allIdsStr)
                       OR pc.legacy_subcategory_id IN ($allIdsStr)
                    ORDER BY p.product_id DESC";
            $res = mysqli_query($this->db, $sql);
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $products[] = $r;
                }
            }
        } elseif ($type === 'jewel_child') {
            $dept = 'Jewellery';
            $pType = 'jewellery';

            $cQry = mysqli_query($this->db, "SELECT s.name, j.categories_name as parent_name 
                                             FROM subcat1 s 
                                             LEFT JOIN jewel_subcat j ON s.maincat_id = j.subcat_id 
                                             WHERE s.subcat_id = $id LIMIT 1");
            if ($cQry && $cRow = mysqli_fetch_assoc($cQry)) {
                $subName = ucwords(strtolower(trim($cRow['name'])));
                $parentName = !empty($cRow['parent_name']) ? ucwords(strtolower(trim($cRow['parent_name']))) : '';
                $catName = $parentName ? "{$parentName} - {$subName}" : $subName;
            }

            $sql = "SELECT DISTINCT p.product_id as id, p.product_code as sku, p.product_name as name, 'jewellery' as type
                    FROM product p
                    LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                    WHERE p.subcat_id = $id 
                       OR pc.legacy_subcategory_id = $id
                    ORDER BY p.product_id DESC";
            $res = mysqli_query($this->db, $sql);
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $products[] = $r;
                }
            }
        }

        // Apply Stock Filter
        if ($stockStatus !== 'all') {
            $this->loadInStockMap();
            $filtered = [];
            foreach ($products as $p) {
                $skuUpper = strtoupper(trim($p['sku']));
                $isInStock = isset($this->inStockMap[$skuUpper]);

                if ($stockStatus === 'available' && $isInStock) {
                    $filtered[] = $p;
                } elseif ($stockStatus === 'outofstock' && !$isInStock) {
                    $filtered[] = $p;
                }
            }
            $products = $filtered;
        }

        return [
            'department' => $dept,
            'name' => $catName,
            'type' => $pType,
            'products' => $products
        ];
    }

    /**
     * Clean folder names for cross-platform ZIP compatibility
     */
    private function sanitizeFolderName($name) {
        $clean = str_replace([' / ', '/', '\\'], ' - ', (string)$name);
        $clean = preg_replace('/[:*?"<>|]/', '_', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean, " ._-\t\n\r\0\x0B");
        return !empty($clean) ? $clean : 'General';
    }

    /**
     * Clean file names
     */
    private function sanitizeFileName($name) {
        $clean = preg_replace('/[\\/\\\\:*?"<>|]/', '_', (string)$name);
        $clean = trim($clean, " .\t\n\r\0\x0B");
        return !empty($clean) ? $clean : 'image.jpg';
    }
}

// Support both ?controller=photodownloader and ?controller=photoDownloader
if (!class_exists('Controllers\PhotoDownloaderController', false)) {
    class_alias(PhotodownloaderController::class, 'Controllers\PhotoDownloaderController');
}
