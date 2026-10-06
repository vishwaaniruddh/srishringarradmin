<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;

class PhotoDownloaderController extends Controller {

    private $productModel;
    private $db;
    private $db3;
    private $inStockMap = null;
    private $configFile;
    private $localUploadsDir;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->db = $this->productModel->getDb();
        $this->db3 = $this->productModel->getDb3();
        $this->configFile = __DIR__ . '/../Config/photo_downloader_settings.json';

        // Detect uploads directory locally
        $localDir = realpath(__DIR__ . '/../../yn/uploads');
        if (!$localDir || !is_dir($localDir)) {
            $localDir = realpath(__DIR__ . '/../yn/uploads');
        }
        $this->localUploadsDir = $localDir ?: null;
    }

    /**
     * Get persisted configuration from JSON file
     */
    public function getSettings() {
        $defaults = [
            'selected_categories' => ['garment:22'],
            'stock_status' => 'all',   // 'all', 'available', 'outofstock'
            'image_scope' => 'all',    // 'main', 'all'
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

        $this->view('photodownloader/index', [
            'categories' => $categories,
            'settings' => $settings
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

        $saved = $this->saveSettingsData([
            'selected_categories' => array_values(array_unique($categories)),
            'stock_status' => $stockStatus,
            'image_scope' => $imageScope
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

        $previewData = $this->calculatePreview($categories, $stockStatus, $imageScope);
        $this->json([
            'success' => true,
            'data' => $previewData
        ]);
    }

    /**
     * Calculate summary metrics for preview
     */
    private function calculatePreview($categoryKeys, $stockStatus, $imageScope) {
        $totalProducts = 0;
        $totalImages = 0;
        $categoryBreakdown = [];

        $this->loadInStockMap();

        foreach ($categoryKeys as $catKey) {
            $info = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$info) continue;

            $productCount = count($info['products']);
            $imgCount = 0;

            if ($productCount > 0) {
                if ($imageScope === 'main') {
                    // For main image, exactly 1 image per product that has an image
                    $productIds = [];
                    $skus = [];
                    foreach ($info['products'] as $p) {
                        $productIds[] = (int)$p['id'];
                        $skus[] = "'" . mysqli_real_escape_string($this->db, $p['sku']) . "'";
                    }
                    $type = $info['type'];
                    $idCol = ($type === 'garment') ? 'gproduct_id' : 'product_id';
                    $idList = implode(',', $productIds);
                    $skuList = implode(',', $skus);

                    $sql = "SELECT COUNT(DISTINCT pro_code) as c 
                            FROM product_images_new 
                            WHERE pro_code IN ($skuList) OR $idCol IN ($idList)";
                    $res = mysqli_query($this->db, $sql);
                    if ($res && $row = mysqli_fetch_assoc($res)) {
                        $imgCount = (int)$row['c'];
                    } else {
                        $imgCount = $productCount;
                    }
                } else {
                    // All images: count all images for these products
                    $productIds = [];
                    $skus = [];
                    foreach ($info['products'] as $p) {
                        $productIds[] = (int)$p['id'];
                        $skus[] = "'" . mysqli_real_escape_string($this->db, $p['sku']) . "'";
                    }
                    $type = $info['type'];
                    $idCol = ($type === 'garment') ? 'gproduct_id' : 'product_id';
                    $idList = implode(',', $productIds);
                    $skuList = implode(',', $skus);

                    $sql = "SELECT COUNT(*) as c 
                            FROM product_images_new 
                            WHERE pro_code IN ($skuList) OR $idCol IN ($idList)";
                    $res = mysqli_query($this->db, $sql);
                    if ($res && $row = mysqli_fetch_assoc($res)) {
                        $imgCount = (int)$row['c'];
                    }
                }
            }

            $totalProducts += $productCount;
            $totalImages += $imgCount;

            $categoryBreakdown[] = [
                'key' => $catKey,
                'name' => $info['name'],
                'department' => $info['department'],
                'products' => $productCount,
                'images' => $imgCount
            ];
        }

        return [
            'total_categories' => count($categoryKeys),
            'total_products' => $totalProducts,
            'total_images' => $totalImages,
            'breakdown' => $categoryBreakdown
        ];
    }

    /**
     * Download the photos ZIP archive
     */
    public function download() {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(0);

        // Read requested options or fall back to saved settings
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

        // Persist current download choices
        $this->saveSettingsData([
            'selected_categories' => array_values(array_unique($categories)),
            'stock_status' => $stockStatus,
            'image_scope' => $imageScope
        ]);

        if (!class_exists('\ZipArchive')) {
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('PHP ZipArchive extension is not enabled on this server.'));
            exit;
        }

        $tempZipPath = tempnam(sys_get_temp_dir(), 'ss_photos_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('Failed to create temporary ZIP archive.'));
            exit;
        }

        $this->loadInStockMap();
        $totalFilesAdded = 0;

        foreach ($categories as $catKey) {
            $catInfo = $this->getCategoryMetaAndProducts($catKey, $stockStatus);
            if (!$catInfo || empty($catInfo['products'])) {
                continue;
            }

            $deptName = $this->sanitizeFolderName($catInfo['department']);
            $categoryFolderName = $this->sanitizeFolderName($catInfo['name']);

            foreach ($catInfo['products'] as $product) {
                $sku = trim($product['sku']);
                if (empty($sku)) continue;

                $skuFolderName = $this->sanitizeFolderName($sku);
                $pid = (int)$product['id'];
                $pType = $product['type'];
                $escSku = mysqli_real_escape_string($this->db, $sku);

                // Fetch images for this product
                if ($pType === 'garment') {
                    $imgSql = "SELECT id, img_name, rank 
                               FROM product_images_new 
                               WHERE pro_code = '$escSku' OR gproduct_id = $pid 
                               ORDER BY rank ASC, id ASC";
                } else {
                    $imgSql = "SELECT id, img_name, rank 
                               FROM product_images_new 
                               WHERE pro_code = '$escSku' OR product_id = $pid 
                               ORDER BY rank ASC, id ASC";
                }

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

                if (empty($images)) {
                    continue;
                }

                // If Main Image only, take first image
                if ($imageScope === 'main') {
                    $images = array_slice($images, 0, 1);
                }

                $imgIndex = 0;
                foreach ($images as $im) {
                    $rawName = $im['img_name'];
                    $baseName = basename($rawName);
                    $cleanBaseName = $this->sanitizeFileName($baseName);

                    // Form readable file names:
                    // Main image: 00_main_{name}
                    // Other images: 01_{name}, 02_{name}...
                    if ($imgIndex === 0) {
                        $zipFileName = '00_main_' . $cleanBaseName;
                    } else {
                        $zipFileName = sprintf('%02d', $imgIndex) . '_' . $cleanBaseName;
                    }

                    // ZIP directory structure:
                    // {Department}/{Category}/{SKU}/{filename}
                    // e.g.: Apparel/Evening Gowns/fm5264-1/00_main_image.jpg
                    $zipEntryPath = "{$deptName}/{$categoryFolderName}/{$skuFolderName}/{$zipFileName}";

                    $added = $this->addImageToZip($zip, $rawName, $zipEntryPath);
                    if ($added) {
                        $totalFilesAdded++;
                    }

                    $imgIndex++;
                }
            }
        }

        $zip->close();

        if ($totalFilesAdded === 0 || !file_exists($tempZipPath) || filesize($tempZipPath) < 50) {
            @unlink($tempZipPath);
            header('Location: index.php?controller=photodownloader&action=index&error=' . urlencode('No images found matching the selected categories and stock filter.'));
            exit;
        }

        // Stream ZIP file
        $fileName = 'srishringarr_photos_' . date('Ymd_His') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($tempZipPath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        ob_clean();
        flush();
        readfile($tempZipPath);
        @unlink($tempZipPath);
        exit;
    }

    /**
     * Add image to zip with local disk check and remote fallback
     */
    private function addImageToZip(\ZipArchive $zip, $imgName, $zipEntryPath) {
        $cleanRelPath = ltrim(str_replace('\\', '/', $imgName), '/');

        // 1. Try local uploads directory
        if ($this->localUploadsDir) {
            $localCandidate = $this->localUploadsDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanRelPath);
            if (file_exists($localCandidate) && is_file($localCandidate)) {
                return $zip->addFile($localCandidate, $zipEntryPath);
            }
        }

        // 2. Secondary local candidate
        $cand2 = realpath(__DIR__ . '/../../yn/uploads/' . $cleanRelPath);
        if ($cand2 && file_exists($cand2) && is_file($cand2)) {
            return $zip->addFile($cand2, $zipEntryPath);
        }

        // 3. Fallback: Fetch remotely from production CDN / server
        $remoteUrl = 'https://srishringarr.com/yn/uploads/' . str_replace(' ', '%20', $cleanRelPath);
        $content = $this->fetchRemoteFile($remoteUrl);
        if ($content !== false && strlen($content) > 100) {
            return $zip->addFromString($zipEntryPath, $content);
        }

        return false;
    }

    /**
     * Fetch remote file via cURL or file_get_contents
     */
    private function fetchRemoteFile($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Srishringarr-Admin-PhotoDownloader/1.0');
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            unset($ch);
            if ($httpCode === 200 && $data !== false) {
                return $data;
            }
        } else {
            $ctx = stream_context_create([
                'http' => ['timeout' => 6, 'user_agent' => 'Srishringarr-Admin-PhotoDownloader/1.0'],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
            ]);
            $data = @file_get_contents($url, false, $ctx);
            if ($data !== false) {
                return $data;
            }
        }
        return false;
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

            // Gather garment subcategories if any
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
        $clean = preg_replace('/[\\/\\\\:*?"<>|]/', '_', (string)$name);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = trim($clean, " .\t\n\r\0\x0B");
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
if (!class_exists('Controllers\PhotodownloaderController', false)) {
    class_alias(PhotoDownloaderController::class, 'Controllers\PhotodownloaderController');
}
