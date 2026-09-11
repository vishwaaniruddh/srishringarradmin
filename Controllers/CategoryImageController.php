<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;
use ZipArchive;

class CategoryImageController extends Controller
{
    private $db;
    private $storageDir;

    public function __construct()
    {
        $this->db = Database::getConnection('con');
        $this->storageDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'scratch' . DIRECTORY_SEPARATOR . 'zips';
        if (!is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0777, true);
        }
    }

    /**
     * Render the main category image download page
     */
    public function index()
    {
        $productModel = new ProductModel();
        $categoriesTree = $productModel->getCategories();

        // Also compile a flat sorted list for easy selection
        $flatCategories = [];

        // 1. Apparel
        $garmentRes = $this->db->query("SELECT garment_id as id, name FROM garments WHERE Main_id=1 OR Main_id=3 ORDER BY name ASC");
        $apparelList = [];
        if ($garmentRes) {
            while ($g = $garmentRes->fetch_assoc()) {
                $gid = (int)$g['id'];
                $countRes = $this->db->query("SELECT COUNT(DISTINCT gp.gproduct_id) as cnt 
                                              FROM garment_product gp
                                              LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                                              WHERE gp.garment_id = $gid 
                                                 OR gp.product_for = $gid 
                                                 OR pc.legacy_category_id = $gid 
                                                 OR pc.legacy_subcategory_id = $gid");
                $cnt = (int)($countRes ? $countRes->fetch_assoc()['cnt'] : 0);
                $apparelList[] = [
                    'key' => "garment:$gid",
                    'name' => ucwords(strtolower($g['name'])),
                    'raw_name' => $g['name'],
                    'slug' => $this->slugify($g['name']),
                    'count' => $cnt,
                    'type' => 'Apparel'
                ];
            }
        }

        // 2. Jewellery
        $jewelRes = $this->db->query("SELECT subcat_id as id, categories_name as name FROM jewel_subcat WHERE mcat_id=1 OR mcat_id=3 ORDER BY categories_name ASC");
        $jewelList = [];
        if ($jewelRes) {
            while ($j = $jewelRes->fetch_assoc()) {
                $jid = (int)$j['id'];
                // Count products in maincat or subcat
                $countRes = $this->db->query("SELECT COUNT(DISTINCT p.product_id) as cnt 
                                              FROM product p
                                              LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                                              WHERE p.categories_id = $jid 
                                                 OR p.subcat_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $jid)
                                                 OR pc.legacy_category_id = $jid 
                                                 OR pc.legacy_subcategory_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $jid)");
                $cnt = (int)($countRes ? $countRes->fetch_assoc()['cnt'] : 0);
                $jewelList[] = [
                    'key' => "jewel_parent:$jid",
                    'name' => ucwords(strtolower($j['name'])),
                    'raw_name' => $j['name'],
                    'slug' => $this->slugify($j['name']),
                    'count' => $cnt,
                    'type' => 'Jewellery'
                ];
            }
        }

        $this->view('category_images/index', [
            'apparelList' => $apparelList,
            'jewelList' => $jewelList,
            'categoriesTree' => $categoriesTree
        ]);
    }

    /**
     * AJAX endpoint to get category statistics & sample preview
     */
    public function info()
    {
        $categoryKey = $_GET['category'] ?? '';
        if (empty($categoryKey)) {
            $this->json(['success' => false, 'message' => 'No category specified.'], 400);
            return;
        }

        $catData = $this->resolveCategory($categoryKey);
        if (!$catData) {
            $this->json(['success' => false, 'message' => 'Category not found.'], 404);
            return;
        }

        $products = $this->getProductsForCategory($catData);
        $totalProducts = count($products);

        // Calculate total images and get sample previews
        $totalImages = 0;
        $previewProducts = [];

        foreach ($products as $idx => $p) {
            $imgs = $this->getProductImages($p['id'], $catData['is_garment'], $p['sku']);
            $imgCount = count($imgs);
            $totalImages += $imgCount;

            if ($idx < 8) {
                $sampleThumb = null;
                if ($imgCount > 0) {
                    $sampleThumb = $this->resolveSampleImageUrl($imgs[0]['img_name']);
                }
                $previewProducts[] = [
                    'id' => $p['id'],
                    'sku' => $p['sku'],
                    'name' => $p['name'],
                    'image_count' => $imgCount,
                    'thumbnail' => $sampleThumb
                ];
            }
        }

        $this->json([
            'success' => true,
            'category_name' => $catData['name'],
            'slug' => $catData['slug'],
            'total_products' => $totalProducts,
            'total_images' => $totalImages,
            'preview_products' => $previewProducts
        ]);
    }

    /**
     * Initialize download session and prepare list of image tasks
     */
    public function initDownload()
    {
        $categoryKey = $_POST['category'] ?? $_GET['category'] ?? '';
        $includeFolder = isset($_POST['include_folder']) ? (int)$_POST['include_folder'] : 1;

        if (empty($categoryKey)) {
            $this->json(['success' => false, 'message' => 'Category is required.'], 400);
            return;
        }

        $catData = $this->resolveCategory($categoryKey);
        if (!$catData) {
            $this->json(['success' => false, 'message' => 'Category not found.'], 404);
            return;
        }

        $products = $this->getProductsForCategory($catData);
        if (empty($products)) {
            $this->json(['success' => false, 'message' => 'No products found in this category.'], 400);
            return;
        }

        $slug = $catData['slug'];
        $tasks = [];
        $taskIndex = 0;

        foreach ($products as $p) {
            $sku = trim($p['sku']);
            if (empty($sku)) continue;
            $cleanSku = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sku);

            $imgs = $this->getProductImages($p['id'], $catData['is_garment'], $sku);
            $imgNum = 1;
            $seenImgs = [];

            foreach ($imgs as $img) {
                $rawName = trim($img['img_name']);
                if (empty($rawName) || isset($seenImgs[$rawName])) continue;
                $seenImgs[$rawName] = true;

                $ext = strtolower(pathinfo($rawName, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }

                $filename = "{$cleanSku}_{$imgNum}.{$ext}";
                $archivePath = $includeFolder ? "{$slug}/{$filename}" : $filename;

                $tasks[] = [
                    'index' => $taskIndex++,
                    'sku' => $sku,
                    'clean_sku' => $cleanSku,
                    'raw_path' => $rawName,
                    'filename' => $filename,
                    'archive_path' => $archivePath
                ];

                $imgNum++;
            }
        }

        if (empty($tasks)) {
            $this->json(['success' => false, 'message' => 'No product images found in this category to download.'], 400);
            return;
        }

        // Clean up old session files (> 1 hour old)
        $this->cleanupOldSessions();

        $sessionId = 'cimg_' . bin2hex(random_bytes(8));
        $zipFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.zip";
        $metaFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.json";

        // Create empty zip
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->json(['success' => false, 'message' => 'Failed to initialize ZIP archive on server.'], 500);
            return;
        }

        // If includeFolder is requested, add empty directory inside zip
        if ($includeFolder) {
            $zip->addEmptyDir($slug);
        }
        $zip->close();

        // Save session data
        $sessionData = [
            'session_id' => $sessionId,
            'category_name' => $catData['name'],
            'slug' => $slug,
            'include_folder' => $includeFolder,
            'created_at' => time(),
            'total_tasks' => count($tasks),
            'tasks' => $tasks
        ];
        file_put_contents($metaFilePath, json_encode($sessionData));

        $this->json([
            'success' => true,
            'session_id' => $sessionId,
            'category_name' => $catData['name'],
            'slug' => $slug,
            'total_images' => count($tasks),
            'total_products' => count($products)
        ]);
    }

    /**
     * Process a batch of images and add them to the ZipArchive
     */
    public function processBatch()
    {
        $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? '';
        $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : (int)($_GET['offset'] ?? 0);
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : (int)($_GET['limit'] ?? 25);

        if (empty($sessionId)) {
            $this->json(['success' => false, 'message' => 'Session ID required.'], 400);
            return;
        }

        $metaFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.json";
        $zipFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.zip";

        if (!file_exists($metaFilePath) || !file_exists($zipFilePath)) {
            $this->json(['success' => false, 'message' => 'Session expired or not found.'], 404);
            return;
        }

        $sessionData = json_decode(file_get_contents($metaFilePath), true);
        if (!$sessionData || !isset($sessionData['tasks'])) {
            $this->json(['success' => false, 'message' => 'Invalid session data.'], 500);
            return;
        }

        $allTasks = $sessionData['tasks'];
        $totalTasks = count($allTasks);
        $batchTasks = array_slice($allTasks, $offset, $limit);

        if (empty($batchTasks)) {
            $this->json([
                'success' => true,
                'completed' => true,
                'processed' => 0,
                'offset' => $offset,
                'total' => $totalTasks,
                'percent' => 100
            ]);
            return;
        }

        // Fetch image contents (local disk check + parallel curl_multi)
        $fetchedImages = $this->fetchBatchImages($batchTasks);

        // Open zip and write images
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === true) {
            foreach ($batchTasks as $idx => $task) {
                $archivePath = $task['archive_path'];
                $content = $fetchedImages[$idx] ?? null;

                if (!empty($content)) {
                    $zip->addFromString($archivePath, $content);
                }
            }
            $zip->close();
        } else {
            $this->json(['success' => false, 'message' => 'Unable to open ZIP file for batch writing.'], 500);
            return;
        }

        $newCompleted = min($offset + count($batchTasks), $totalTasks);
        $percent = round(($newCompleted / $totalTasks) * 100, 1);
        $lastSku = end($batchTasks)['sku'] ?? '';

        $this->json([
            'success' => true,
            'completed' => ($newCompleted >= $totalTasks),
            'processed' => count($batchTasks),
            'offset' => $newCompleted,
            'total' => $totalTasks,
            'percent' => $percent,
            'current_sku' => $lastSku
        ]);
    }

    /**
     * Download the finalized ZIP file
     */
    public function downloadZip()
    {
        $sessionId = $_GET['session_id'] ?? '';
        if (empty($sessionId) || !preg_match('/^[a-zA-Z0-9_]+$/', $sessionId)) {
            die('Invalid session ID.');
        }

        $metaFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.json";
        $zipFilePath = $this->storageDir . DIRECTORY_SEPARATOR . "{$sessionId}.zip";

        if (!file_exists($zipFilePath)) {
            die('ZIP file does not exist or has expired.');
        }

        $slug = 'product-images';
        if (file_exists($metaFilePath)) {
            $sessionData = json_decode(file_get_contents($metaFilePath), true);
            if (!empty($sessionData['slug'])) {
                $slug = $sessionData['slug'];
            }
        }

        $downloadFilename = "{$slug}.zip";
        $fileSize = filesize($zipFilePath);

        // Clear output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);

        readfile($zipFilePath);
        exit;
    }

    /**
     * Direct synchronous ZIP generator (fallback option)
     */
    public function directZip()
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $categoryKey = $_GET['category'] ?? '';
        $includeFolder = isset($_GET['include_folder']) ? (int)$_GET['include_folder'] : 1;

        if (empty($categoryKey)) {
            die('Category is required.');
        }

        $catData = $this->resolveCategory($categoryKey);
        if (!$catData) {
            die('Category not found.');
        }

        $products = $this->getProductsForCategory($catData);
        if (empty($products)) {
            die('No products found in this category.');
        }

        $slug = $catData['slug'];
        $tempZip = tempnam(sys_get_temp_dir(), 'zip_');
        $zip = new ZipArchive();
        if ($zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            die('Failed to create temporary ZIP.');
        }

        if ($includeFolder) {
            $zip->addEmptyDir($slug);
        }

        foreach ($products as $p) {
            $sku = trim($p['sku']);
            if (empty($sku)) continue;
            $cleanSku = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sku);

            $imgs = $this->getProductImages($p['id'], $catData['is_garment'], $sku);
            $imgNum = 1;
            $seenImgs = [];

            foreach ($imgs as $img) {
                $raw = trim($img['img_name']);
                if (empty($raw) || isset($seenImgs[$raw])) continue;
                $seenImgs[$raw] = true;

                $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }

                $filename = "{$cleanSku}_{$imgNum}.{$ext}";
                $archivePath = $includeFolder ? "{$slug}/{$filename}" : $filename;

                $content = $this->fetchSingleImage($raw);
                if (!empty($content)) {
                    $zip->addFromString($archivePath, $content);
                }

                $imgNum++;
            }
        }

        $zip->close();

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $slug . '.zip"');
        header('Content-Length: ' . filesize($tempZip));
        readfile($tempZip);
        @unlink($tempZip);
        exit;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function resolveCategory($key)
    {
        if (strpos($key, ':') !== false) {
            list($type, $id) = explode(':', $key, 2);
            $id = (int)$id;

            if ($type === 'garment') {
                $q = $this->db->query("SELECT name FROM garments WHERE garment_id = $id LIMIT 1");
                $row = $q ? $q->fetch_assoc() : null;
                if ($row) {
                    return [
                        'type' => 'garment',
                        'id' => $id,
                        'name' => $row['name'],
                        'slug' => $this->slugify($row['name']),
                        'is_garment' => true
                    ];
                }
            } elseif ($type === 'jewel_parent') {
                $q = $this->db->query("SELECT categories_name as name FROM jewel_subcat WHERE subcat_id = $id LIMIT 1");
                $row = $q ? $q->fetch_assoc() : null;
                if ($row) {
                    return [
                        'type' => 'jewel_parent',
                        'id' => $id,
                        'name' => $row['name'],
                        'slug' => $this->slugify($row['name']),
                        'is_garment' => false
                    ];
                }
            } elseif ($type === 'jewel_child') {
                $q = $this->db->query("SELECT name FROM subcat1 WHERE subcat_id = $id LIMIT 1");
                $row = $q ? $q->fetch_assoc() : null;
                if ($row) {
                    return [
                        'type' => 'jewel_child',
                        'id' => $id,
                        'name' => $row['name'],
                        'slug' => $this->slugify($row['name']),
                        'is_garment' => false
                    ];
                }
            }
        }

        return null;
    }

    private function getProductsForCategory($catData)
    {
        $id = (int)$catData['id'];
        $type = $catData['type'];
        $products = [];

        if ($type === 'garment') {
            $sql = "SELECT DISTINCT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name
                    FROM garment_product gp
                    LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                    WHERE gp.garment_id = $id 
                       OR gp.product_for = $id 
                       OR pc.legacy_category_id = $id 
                       OR pc.legacy_subcategory_id = $id
                    ORDER BY gp.gproduct_id ASC";
        } elseif ($type === 'jewel_parent') {
            $sql = "SELECT DISTINCT p.product_id as id, p.product_code as sku, p.product_name as name
                    FROM product p
                    LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                    WHERE p.categories_id = $id 
                       OR p.subcat_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id)
                       OR pc.legacy_category_id = $id 
                       OR pc.legacy_subcategory_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id)
                    ORDER BY p.product_id ASC";
        } elseif ($type === 'jewel_child') {
            $sql = "SELECT DISTINCT p.product_id as id, p.product_code as sku, p.product_name as name
                    FROM product p
                    LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                    WHERE p.subcat_id = $id 
                       OR pc.legacy_subcategory_id = $id
                    ORDER BY p.product_id ASC";
        } else {
            return [];
        }

        $res = $this->db->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $products[] = $row;
            }
        }

        return $products;
    }

    private function getProductImages($productId, $isGarment, $sku)
    {
        $pid = (int)$productId;
        $idField = $isGarment ? 'gproduct_id' : 'product_id';
        $escapedSku = $this->db->real_escape_string($sku);

        $sql = "SELECT id, img_name, rank 
                FROM product_images_new 
                WHERE $idField = $pid OR pro_code = '$escapedSku' 
                ORDER BY rank ASC, id ASC";

        $res = $this->db->query($sql);
        $images = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $images[] = $row;
            }
        }
        return $images;
    }

    /**
     * Parallel fetch images for a batch using multi-curl and local filesystem cache
     */
    private function fetchBatchImages($tasks)
    {
        $contents = [];
        $remoteTasks = [];

        // 1. Try local disk paths first
        foreach ($tasks as $i => $task) {
            $raw = $task['raw_path'];
            $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $raw), '/');

            $localPaths = [
                'C:/xampp/htdocs/ss/yn/uploads/' . $clean,
                'C:/xampp/htdocs/yn/uploads/' . $clean,
                'C:/xampp/htdocs/ss/' . $clean,
                'C:/xampp/htdocs/yn/admin/uploads/' . $clean
            ];

            $foundLocal = false;
            foreach ($localPaths as $lp) {
                if (file_exists($lp) && is_file($lp) && filesize($lp) > 0) {
                    $contents[$i] = file_get_contents($lp);
                    $foundLocal = true;
                    break;
                }
            }

            if (!$foundLocal) {
                $remoteTasks[$i] = $clean;
            }
        }

        // 2. Fetch remote via curl_multi for remaining
        if (!empty($remoteTasks)) {
            $mh = curl_multi_init();
            $handles = [];

            foreach ($remoteTasks as $i => $clean) {
                // Primary url
                $primaryUrl = "https://srishringarr.com/yn/uploads/{$clean}";
                $ch = curl_init($primaryUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_multi_add_handle($mh, $ch);
                $handles[$i] = [
                    'handle' => $ch,
                    'clean' => $clean,
                    'primary_url' => $primaryUrl
                ];
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh, 0.1);
            } while ($running > 0);

            $needsFallback = [];
            foreach ($handles as $i => $hInfo) {
                $ch = $hInfo['handle'];
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $data = curl_multi_getcontent($ch);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);

                if ($code === 200 && !empty($data)) {
                    $contents[$i] = $data;
                } else {
                    $needsFallback[$i] = $hInfo['clean'];
                }
            }
            curl_multi_close($mh);

            // 3. Fallback for 404s (e.g. yosshitaneha.com older archives)
            if (!empty($needsFallback)) {
                $mh2 = curl_multi_init();
                $handles2 = [];

                foreach ($needsFallback as $i => $clean) {
                    $fallbackUrl = "https://yosshitaneha.com/admin/uploads/{$clean}";
                    $ch2 = curl_init($fallbackUrl);
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch2, CURLOPT_TIMEOUT, 8);
                    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
                    curl_multi_add_handle($mh2, $ch2);
                    $handles2[$i] = $ch2;
                }

                $running2 = null;
                do {
                    curl_multi_exec($mh2, $running2);
                    curl_multi_select($mh2, 0.1);
                } while ($running2 > 0);

                foreach ($handles2 as $i => $ch2) {
                    $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                    $data2 = curl_multi_getcontent($ch2);
                    curl_multi_remove_handle($mh2, $ch2);
                    curl_close($ch2);

                    if ($code2 === 200 && !empty($data2)) {
                        $contents[$i] = $data2;
                    }
                }
                curl_multi_close($mh2);
            }
        }

        return $contents;
    }

    private function fetchSingleImage($rawPath)
    {
        $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $rawPath), '/');

        $localPaths = [
            'C:/xampp/htdocs/ss/yn/uploads/' . $clean,
            'C:/xampp/htdocs/yn/uploads/' . $clean,
            'C:/xampp/htdocs/ss/' . $clean,
            'C:/xampp/htdocs/yn/admin/uploads/' . $clean
        ];

        foreach ($localPaths as $lp) {
            if (file_exists($lp) && is_file($lp) && filesize($lp) > 0) {
                return file_get_contents($lp);
            }
        }

        $remoteUrls = [
            "https://srishringarr.com/yn/uploads/{$clean}",
            "https://yosshitaneha.com/admin/uploads/{$clean}",
            "https://srishringarr.com/uploads/{$clean}"
        ];

        foreach ($remoteUrls as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && !empty($data)) {
                return $data;
            }
        }

        return null;
    }

    private function resolveSampleImageUrl($rawPath)
    {
        $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $rawPath), '/');
        // Check if local file exists
        $local = 'C:/xampp/htdocs/ss/yn/uploads/' . $clean;
        if (file_exists($local)) {
            return '/ss/yn/uploads/' . $clean;
        }
        return "https://srishringarr.com/yn/uploads/{$clean}";
    }

    private function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = preg_replace('/[\s_-]+/', '-', $text);
        return trim($text, '-');
    }

    private function cleanupOldSessions()
    {
        $files = @glob($this->storageDir . DIRECTORY_SEPARATOR . 'cimg_*');
        if ($files) {
            $oneHourAgo = time() - 3600;
            foreach ($files as $file) {
                if (filemtime($file) < $oneHourAgo) {
                    @unlink($file);
                }
            }
        }
    }
}
