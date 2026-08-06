<?php
namespace Core;

use Models\ProductModel;
use PDO;
use PDOException;

class ProductSyncService {

    private static $childPdo = null;

    /**
     * Get PDO connection to Child Database (Yosshitaneha)
     */
    public static function getChildPdo() {
        if (self::$childPdo !== null) {
            try {
                self::$childPdo->query("SELECT 1");
                return self::$childPdo;
            } catch (\Throwable $t) {
                self::$childPdo = null;
            }
        }

        $httpHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

        $isProduction = (
            str_contains($httpHost, 'srishringarr.com') || 
            str_contains($httpHost, 'yosshitaneha.com') || 
            str_contains($docRoot, 'u464193275') ||
            (php_sapi_name() !== 'cli' && !str_contains($httpHost, 'localhost') && !str_contains($httpHost, '127.0.0.1') && !empty($httpHost))
        );

        if ($isProduction) {
            $host = 'localhost';
            $user = 'u464193275_yosshitanehafs';
            $pass = 'AVav@@2026';
            $dbname = 'u464193275_yosshitanehafs';
        } else {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $dbname = 'yosshitaneha_db';
        }

        try {
            self::$childPdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return self::$childPdo;
        } catch (PDOException $e) {
            error_log("ProductSyncService Child DB Connection Failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to create a URL-friendly slug
     */
    private static function createSlug($text, $sku = '') {
        $text = trim((string)$text);
        $sku = trim((string)$sku);

        if (empty($text) || $text === '1' || is_numeric($text) || mb_strlen($text) < 3) {
            $text = (!empty($sku) ? $sku : 'product-' . time());
        }

        $slug = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $slug = @iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = strtolower($slug);

        if (empty($slug) || $slug === '1') {
            $slug = 'product-' . strtolower(preg_replace('~[^\pL\d]+~u', '-', $sku));
        }

        if (is_numeric($slug) || strlen($slug) < 3) {
            if (!empty($sku) && strtolower($sku) !== $slug) {
                $slug .= '-' . strtolower(preg_replace('~[^\pL\d]+~u', '-', $sku));
            }
        }

        return $slug;
    }

    /**
     * Download or copy image content from local file or remote URL using cURL
     */
    private static function fetchImageContent($source) {
        if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
            if (!function_exists('curl_init')) {
                $ctx = stream_context_create(['http' => ['timeout' => 10]]);
                $c = @file_get_contents($source, false, $ctx);
                return ($c && strlen($c) > 100) ? $c : false;
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $source,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode === 200 && $content && strlen($content) > 100) {
                return $content;
            }
            return false;
        } else {
            if (file_exists($source)) {
                $content = @file_get_contents($source);
                if ($content && strlen($content) > 100) {
                    return $content;
                }
            }
            return false;
        }
    }

    /**
     * Get Sync Category Settings
     */
    public static function getSyncSettings() {
        $configFile = __DIR__ . '/../Config/sync_settings.json';
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);
            $data = json_decode($content, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [
            'sync_all' => true,
            'enabled_categories' => []
        ];
    }

    /**
     * Save Sync Category Settings
     */
    public static function saveSyncSettings($enabledCategories, $syncAll = false) {
        $configFile = __DIR__ . '/../Config/sync_settings.json';
        $data = [
            'sync_all' => (bool)$syncAll,
            'enabled_categories' => is_array($enabledCategories) ? array_values(array_unique($enabledCategories)) : [],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * Check if a product's category is enabled for sync
     */
    public static function isCategoryEnabled($productType, $parentProduct) {
        $settings = self::getSyncSettings();

        // If sync_all is enabled or no categories are explicitly saved, allow all
        if (!empty($settings['sync_all']) || empty($settings['enabled_categories'])) {
            return true;
        }

        $enabled = $settings['enabled_categories'];
        $con = Database::getConnection('con');

        if ($productType === 'garments' || $productType === 'garment') {
            $catIds = [];
            if (!empty($parentProduct['category'])) $catIds[] = (int)$parentProduct['category'];
            if (!empty($parentProduct['sub_category'])) $catIds[] = (int)$parentProduct['sub_category'];

            $pid = (int)($parentProduct['id'] ?? 0);
            if ($con && $pid > 0) {
                $pcRes = mysqli_query($con, "SELECT legacy_category_id, legacy_subcategory_id FROM product_categories WHERE product_id = $pid AND product_type = 'garments'");
                if ($pcRes) {
                    while ($pcRow = mysqli_fetch_assoc($pcRes)) {
                        if (!empty($pcRow['legacy_category_id'])) $catIds[] = (int)$pcRow['legacy_category_id'];
                        if (!empty($pcRow['legacy_subcategory_id'])) $catIds[] = (int)$pcRow['legacy_subcategory_id'];
                    }
                }
            }
            $catIds = array_unique(array_filter($catIds));

            foreach ($catIds as $cId) {
                if (in_array("garment:$cId", $enabled)) return true;
                if ($con) {
                    $gRes = mysqli_query($con, "SELECT gmain_id FROM garment_subcat WHERE sub_id = $cId LIMIT 1");
                    if ($gRes && $gRow = mysqli_fetch_assoc($gRes)) {
                        $gMain = (int)$gRow['gmain_id'];
                        if (in_array("garment:$gMain", $enabled)) return true;
                    }
                }
            }
            return false;
        } else {
            $parentCatIds = [];
            $subCatIds = [];

            $pCat = (int)($parentProduct['category'] ?? 0);
            $sCat = (int)($parentProduct['sub_category'] ?? 0);

            if ($pCat > 0) $parentCatIds[] = $pCat;
            if ($sCat > 0) $subCatIds[] = $sCat;

            $pid = (int)($parentProduct['id'] ?? 0);
            if ($con && $pid > 0) {
                $pcRes = mysqli_query($con, "SELECT legacy_category_id, legacy_subcategory_id FROM product_categories WHERE product_id = $pid AND product_type = 'jewellery'");
                if ($pcRes) {
                    while ($pcRow = mysqli_fetch_assoc($pcRes)) {
                        if (!empty($pcRow['legacy_category_id'])) $parentCatIds[] = (int)$pcRow['legacy_category_id'];
                        if (!empty($pcRow['legacy_subcategory_id'])) $subCatIds[] = (int)$pcRow['legacy_subcategory_id'];
                    }
                }
            }

            if ($con) {
                // If a parentCatId is actually in subcat1, move it to subCatIds and resolve its maincat_id
                foreach ($parentCatIds as $pId) {
                    $checkSub1 = mysqli_query($con, "SELECT subcat_id, maincat_id FROM subcat1 WHERE subcat_id = $pId LIMIT 1");
                    if ($checkSub1 && $subRow = mysqli_fetch_assoc($checkSub1)) {
                        $subCatIds[] = $pId;
                        if (!empty($subRow['maincat_id'])) {
                            $parentCatIds[] = (int)$subRow['maincat_id'];
                        }
                    }
                }

                // For any subCatId, find its maincat_id in subcat1
                foreach ($subCatIds as $sId) {
                    $checkSub1 = mysqli_query($con, "SELECT maincat_id FROM subcat1 WHERE subcat_id = $sId LIMIT 1");
                    if ($checkSub1 && $subRow = mysqli_fetch_assoc($checkSub1)) {
                        if (!empty($subRow['maincat_id'])) {
                            $parentCatIds[] = (int)$subRow['maincat_id'];
                        }
                    }
                }
            }

            $parentCatIds = array_unique(array_filter($parentCatIds));
            $subCatIds = array_unique(array_filter($subCatIds));

            foreach ($subCatIds as $sId) {
                if (in_array("jewel_child:$sId", $enabled)) return true;
            }
            foreach ($parentCatIds as $pId) {
                if (in_array("jewel_parent:$pId", $enabled)) return true;
            }
            return false;
        }
    }

    /**
     * Sync a single product from Parent to Child
     * 
     * @param int $productId
     * @param string $productType ('jewellery' or 'garments')
     * @param string $mode ('auto' or 'manual')
     * @return array ['success' => bool, 'message' => string, 'sku' => string]
     */
    public static function syncProduct($productId, $productType = 'jewellery', $mode = 'auto') {
        $productId = (int)$productId;
        $productType = strtolower($productType) === 'garments' ? 'garments' : 'jewellery';

        if (!$productId) {
            return ['success' => false, 'message' => 'Invalid Product ID', 'sku' => 'N/A'];
        }

        // Ensure parent DB connection is alive before creating ProductModel
        $conCheck = Database::getConnection('con');
        if (!$conCheck) {
            // Retry once after a brief pause (shared hosting connection pool exhaustion)
            usleep(200000); // 200ms
            $conCheck = Database::getConnection('con');
            if (!$conCheck) {
                return ['success' => false, 'message' => 'Parent DB connection unavailable', 'sku' => 'N/A'];
            }
        }

        $productModel = new ProductModel();
        $parentProduct = $productModel->getProductById($productId, $productType);

        // Retry once if parent product fetch failed (connection may have dropped mid-query)
        if (!$parentProduct) {
            usleep(100000); // 100ms
            $productModel = new ProductModel();
            $parentProduct = $productModel->getProductById($productId, $productType);
        }

        if (!$parentProduct) {
            self::logSync($productId, $productType, 'N/A', $mode, 'failed', 'Product not found in Parent DB');
            return ['success' => false, 'message' => 'Product not found in Parent DB', 'sku' => 'N/A'];
        }

        $sku = trim($parentProduct['code'] ?? '');
        if (empty($sku)) {
            self::logSync($productId, $productType, 'N/A', $mode, 'failed', 'Product SKU code is empty');
            return ['success' => false, 'message' => 'Product SKU code is empty', 'sku' => 'N/A'];
        }

        // Check if product category is enabled in Sync Configuration
        if (!self::isCategoryEnabled($productType, $parentProduct)) {
            $catName = $parentProduct['category_name'] ?? 'Category Restricted';
            $msg = "Skipped SKU $sku ($productType): Category '$catName' is disabled in Sync Configuration";
            self::logSync($productId, $productType, $sku, $mode, 'skipped', $msg);
            return ['success' => true, 'skipped' => true, 'message' => $msg, 'sku' => $sku];
        }

        $childPdo = self::getChildPdo();
        if (!$childPdo) {
            // Retry child DB connection once
            usleep(200000);
            self::$childPdo = null;
            $childPdo = self::getChildPdo();
            if (!$childPdo) {
                self::logSync($productId, $productType, $sku, $mode, 'failed', 'Could not connect to Child DB');
                return ['success' => false, 'message' => 'Could not connect to Child DB', 'sku' => $sku];
            }
        }

        try {
            $childPdo->beginTransaction();

            // 1. Process Category — enforce 2-level hierarchy: Jewellery/Outfit → Subcategory
            $categoryId = null;
            $inferred = self::inferCategoryFromSku($sku, $productType, (int)($parentProduct['category'] ?? 0), (int)($parentProduct['sub_category'] ?? 0));
            $mainCatName = ($inferred['main_category'] === 'outfit') ? 'Outfit' : 'Jewellery';
            $subCatName = $inferred['category_name'] ?? '';

            // If parent product has a real category name from DB, prefer it over the inferred one
            $parentCatName = trim($parentProduct['category_name'] ?? '');
            if (!empty($parentCatName) && $parentCatName !== 'N/A') {
                $subCatName = $parentCatName;
            }
            // Fallback if still empty
            if (empty($subCatName)) {
                $subCatName = $mainCatName;
            }

            // Step 1a: Find or create the main parent category (Jewellery / Outfit)
            $mainCatSlug = self::createSlug($mainCatName);
            $stmtMainCat = $childPdo->prepare("SELECT id FROM categories WHERE slug = :slug AND (parent_id IS NULL OR parent_id = 0) LIMIT 1");
            $stmtMainCat->execute([':slug' => $mainCatSlug]);
            $mainCatRow = $stmtMainCat->fetch();

            if ($mainCatRow) {
                $mainCatId = (int)$mainCatRow['id'];
            } else {
                $stmtInsMain = $childPdo->prepare("INSERT INTO categories (name, slug, parent_id, description) VALUES (:name, :slug, NULL, :desc)");
                $stmtInsMain->execute([
                    ':name' => $mainCatName,
                    ':slug' => $mainCatSlug,
                    ':desc' => $mainCatName . ' collection from Srishringarr'
                ]);
                $mainCatId = (int)$childPdo->lastInsertId();
            }

            // Step 1b: Find or create the subcategory as child of main category
            // If subcategory name is same as main category, just use the main category directly
            if (strtolower(trim($subCatName)) === strtolower(trim($mainCatName))) {
                $categoryId = $mainCatId;
            } else {
                // First try to find by name under this parent (name is more stable than slug)
                $stmtSubCat = $childPdo->prepare("SELECT id FROM categories WHERE name = :name AND parent_id = :pid LIMIT 1");
                $stmtSubCat->execute([':name' => $subCatName, ':pid' => $mainCatId]);
                $subCatRow = $stmtSubCat->fetch();

                if ($subCatRow) {
                    $categoryId = (int)$subCatRow['id'];
                } else {
                    // Generate a unique slug — auto-increment if collision exists
                    $baseSlug = self::createSlug($subCatName);
                    $slug = $baseSlug;
                    $counter = 1;
                    while (true) {
                        $stmtSlugCheck = $childPdo->prepare("SELECT id FROM categories WHERE slug = :slug LIMIT 1");
                        $stmtSlugCheck->execute([':slug' => $slug]);
                        if (!$stmtSlugCheck->fetch()) {
                            break; // slug is unique
                        }
                        $counter++;
                        $slug = $baseSlug . '-' . $counter;
                    }

                    $stmtInsSub = $childPdo->prepare("INSERT INTO categories (name, slug, parent_id, description) VALUES (:name, :slug, :pid, :desc)");
                    $stmtInsSub->execute([
                        ':name' => $subCatName,
                        ':slug' => $slug,
                        ':pid' => $mainCatId,
                        ':desc' => $subCatName . ' - ' . $mainCatName
                    ]);
                    $categoryId = (int)$childPdo->lastInsertId();
                }
            }

            // 2. Prepare Product Data
            $name = trim($parentProduct['name'] ?? 'Product ' . $sku);
            $slug = self::createSlug($name, $sku);
            $description = trim($parentProduct['description'] ?? '');
            $shortDescription = mb_substr(strip_tags($description), 0, 200);

            // Resolved Price Logic: If sales_price in product/garment_product is empty/0, fetch unit_price from phppos_items (DB3)
            $price = (float)($parentProduct['s_price'] ?? 0);
            if ($price <= 0) {
                try {
                    $db3 = Database::getConnection('con3');
                    if (!$db3) {
                        usleep(100000);
                        $db3 = Database::getConnection('con3');
                    }
                    if ($db3) {
                        $stmtPos = @mysqli_prepare($db3, "SELECT unit_price FROM phppos_items WHERE name = ? LIMIT 1");
                        if ($stmtPos) {
                            @mysqli_stmt_bind_param($stmtPos, "s", $sku);
                            @mysqli_stmt_execute($stmtPos);
                            $resPos = @mysqli_stmt_get_result($stmtPos);
                            if ($resPos && $rPos = @mysqli_fetch_assoc($resPos)) {
                                $price = (float)($rPos['unit_price'] ?? 0);
                            }
                            @mysqli_stmt_close($stmtPos);
                        }
                    }
                } catch (\Throwable $t) {
                    error_log("Price lookup exception for SKU $sku: " . $t->getMessage());
                }
            }

            $discountPercent = (float)($parentProduct['discount'] ?? 0);
            $salePrice = null;

            if ($discountPercent > 0 && $price > 0) {
                $salePrice = round($price - ($price * ($discountPercent / 100)), 2);
            }

            $stockQty = (int)($parentProduct['quantity'] ?? 10);
            $isFeatured = !empty($parentProduct['featured']) ? 1 : 0;
            $status = (isset($parentProduct['availability']) && $parentProduct['availability'] == 0) ? 'draft' : 'published';

            // Target Directories for Child Store (yn/admin/uploads/products/{sku}/)
            $ynAdminDir = null;
            $possiblePaths = [
                // Hostinger Production Server Paths
                '/home/u464193275/domains/yosshitaneha.com/public_html/admin/',
                dirname(__DIR__, 4) . '/yosshitaneha.com/public_html/admin/',
                dirname(__DIR__, 4) . '/yosshitaneha.com/admin/',
                dirname(__DIR__, 3) . '/yosshitaneha.com/public_html/admin/',
                ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/../yosshitaneha.com/public_html/admin/',
                ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/../domains/yosshitaneha.com/public_html/admin/',
                // Local XAMPP Paths
                dirname(__DIR__, 3) . '/yn/admin/',
                dirname(__DIR__, 2) . '/yn/admin/',
                ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/yn/admin/',
                'C:/xampp/htdocs/yn/admin/'
            ];
            foreach ($possiblePaths as $p) {
                if (is_dir($p)) {
                    $ynAdminDir = rtrim($p, '/') . '/';
                    break;
                }
            }
            if (!$ynAdminDir) {
                $ynAdminDir = 'C:/xampp/htdocs/yn/admin/';
            }

            $productUploadDir = $ynAdminDir . 'uploads/products/' . $sku . '/';
            $thumbUploadDir = $productUploadDir . 'thumbs/';

            if (!is_dir($productUploadDir)) {
                @mkdir($productUploadDir, 0755, true);
            }
            if (!is_dir($thumbUploadDir)) {
                @mkdir($thumbUploadDir, 0755, true);
            }

            // Fetch Parent Product Images
            $parentImages = $productModel->getProductImages($productId, $productType);
            $mainImage = null;
            $galleryImages = [];

            if (!empty($parentImages)) {
                foreach ($parentImages as $idx => $img) {
                    $rawPath = is_array($img) ? ($img['img_name'] ?? $img['img_path'] ?? $img['image_name'] ?? '') : (string)$img;
                    if (empty($rawPath)) continue;

                    $cleanName = ltrim($rawPath, '/');
                    $filename = basename($cleanName);

                    // Destination file paths inside yn/admin/uploads/products/{sku}/
                    $destFile = $productUploadDir . $filename;
                    $thumbFile = $thumbUploadDir . $filename;

                    // Relative DB paths for Child DB
                    $dbImgPath = 'uploads/products/' . $sku . '/' . $filename;
                    $dbThumbPath = 'uploads/products/' . $sku . '/thumbs/' . $filename;

                    // Copy / acquire image if missing or 0 bytes
                    if (!file_exists($destFile) || filesize($destFile) < 100) {
                        $sources = [
                            '/home/u464193275/domains/srishringarr.com/public_html/yn/uploads/' . ltrim(str_replace(['yn/uploads/', 'uploads/'], '', $cleanName), '/'),
                            '/home/u464193275/domains/srishringarr.com/public_html/' . $cleanName,
                            $ynAdminDir . '../uploads/' . ltrim(str_replace(['yn/uploads/', 'uploads/'], '', $cleanName), '/'),
                            $ynAdminDir . '../' . $cleanName,
                            dirname(__DIR__, 3) . '/ss/' . $cleanName,
                            dirname(__DIR__, 3) . '/yn/uploads/' . ltrim(str_replace(['yn/uploads/', 'uploads/'], '', $cleanName), '/'),
                            'https://srishringarr.com/yn/uploads/' . ltrim(str_replace(['yn/uploads/', 'uploads/'], '', $cleanName), '/'),
                            'https://srishringarr.com/uploads/' . ltrim(str_replace(['yn/uploads/', 'uploads/'], '', $cleanName), '/'),
                            'https://srishringarr.com/' . $cleanName,
                            'https://yosshitaneha.com/wp-content/uploads/' . $cleanName
                        ];

                        $content = null;
                        foreach ($sources as $src) {
                            $c = self::fetchImageContent($src);
                            if ($c && strlen($c) > 100) {
                                $content = $c;
                                break;
                            }
                        }

                        if ($content) {
                            @file_put_contents($destFile, $content);

                            // Generate thumbnail via GD if available
                            $savedThumb = false;
                            if (function_exists('imagecreatefromstring')) {
                                $gdImg = @imagecreatefromstring($content);
                                if ($gdImg) {
                                    $w = imagesx($gdImg);
                                    $h = imagesy($gdImg);
                                    $maxDim = 300;
                                    if ($w > $maxDim || $h > $maxDim) {
                                        $ratio = min($maxDim / $w, $maxDim / $h);
                                        $newW = (int)($w * $ratio);
                                        $newH = (int)($h * $ratio);
                                        $thumbGd = imagecreatetruecolor($newW, $newH);
                                        imagecopyresampled($thumbGd, $gdImg, 0, 0, 0, 0, $newW, $newH, $w, $h);
                                        @imagejpeg($thumbGd, $thumbFile, 85);
                                        $savedThumb = true;
                                    }
                                }
                            }
                            if (!$savedThumb) {
                                @file_put_contents($thumbFile, $content);
                            }
                        }
                    }

                    if (!$mainImage) {
                        $mainImage = $dbImgPath;
                    }
                    $galleryImages[] = [
                        'image_path' => $dbImgPath,
                        'thumb_path' => $dbThumbPath
                    ];
                }
            }

            // 3. UPSERT Product in Child DB
            $stmtCheck = $childPdo->prepare("SELECT id FROM products WHERE sku = :sku LIMIT 1");
            $stmtCheck->execute([':sku' => $sku]);
            $existingProduct = $stmtCheck->fetch();

            // Check for duplicate slug in Child DB and make unique if collision exists
            $stmtSlugCheck = $childPdo->prepare("SELECT id FROM products WHERE slug = :slug AND sku != :sku LIMIT 1");
            $stmtSlugCheck->execute([':slug' => $slug, ':sku' => $sku]);
            if ($stmtSlugCheck->fetch()) {
                $cleanSkuSlug = strtolower(preg_replace('~[^\pL\d]+~u', '-', $sku));
                if (!str_contains($slug, $cleanSkuSlug)) {
                    $slug .= '-' . $cleanSkuSlug;
                } else {
                    $slug .= '-' . $productId;
                }
            }

            if ($existingProduct) {
                $childProductId = $existingProduct['id'];
                $stmtUpdate = $childPdo->prepare("UPDATE products SET 
                    category_id = :category_id,
                    name = :name,
                    slug = :slug,
                    description = :description,
                    short_description = :short_description,
                    price = :price,
                    sale_price = :sale_price,
                    stock_qty = :stock_qty,
                    is_featured = :is_featured,
                    status = :status,
                    main_image = :main_image,
                    updated_at = NOW(),
                    deleted_at = NULL
                WHERE id = :id");

                $stmtUpdate->execute([
                    ':category_id' => $categoryId,
                    ':name' => $name,
                    ':slug' => $slug,
                    ':description' => $description,
                    ':short_description' => $shortDescription,
                    ':price' => $price,
                    ':sale_price' => $salePrice,
                    ':stock_qty' => $stockQty,
                    ':is_featured' => $isFeatured,
                    ':status' => $status,
                    ':main_image' => $mainImage,
                    ':id' => $childProductId
                ]);
                $actionText = 'Updated';
            } else {
                $stmtInsert = $childPdo->prepare("INSERT INTO products 
                    (category_id, name, slug, sku, description, short_description, price, sale_price, stock_qty, is_featured, status, main_image, created_at, updated_at)
                    VALUES 
                    (:category_id, :name, :slug, :sku, :description, :short_description, :price, :sale_price, :stock_qty, :is_featured, :status, :main_image, NOW(), NOW())");

                $stmtInsert->execute([
                    ':category_id' => $categoryId,
                    ':name' => $name,
                    ':slug' => $slug,
                    ':sku' => $sku,
                    ':description' => $description,
                    ':short_description' => $shortDescription,
                    ':price' => $price,
                    ':sale_price' => $salePrice,
                    ':stock_qty' => $stockQty,
                    ':is_featured' => $isFeatured,
                    ':status' => $status,
                    ':main_image' => $mainImage
                ]);
                $childProductId = $childPdo->lastInsertId();
                $actionText = 'Inserted';
            }

            // 4. Sync Gallery Images
            if ($childProductId) {
                $childPdo->prepare("DELETE FROM product_images WHERE product_id = :pid")->execute([':pid' => $childProductId]);
                if (!empty($galleryImages)) {
                    $stmtImgIns = $childPdo->prepare("INSERT INTO product_images (product_id, image_path, thumb_path, sort_order) VALUES (:pid, :img, :thumb, :sort)");
                    
                    $sort = 0;
                    foreach ($galleryImages as $gImg) {
                        $stmtImgIns->execute([
                            ':pid' => $childProductId,
                            ':img' => $gImg['image_path'],
                            ':thumb' => $gImg['thumb_path'],
                            ':sort' => $sort++
                        ]);
                    }
                }
            }

            // 5. Sync Category Relation in product_categories if categoryId exists
            if ($childProductId && $categoryId) {
                $stmtCatRel = $childPdo->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:pid, :cid)");
                $stmtCatRel->execute([':pid' => $childProductId, ':cid' => $categoryId]);
            }

            $childPdo->commit();

            // Clear Cache in Child
            self::clearChildCache();

            // Log Success
            $msg = "Successfully $actionText SKU $sku ($productType) to Yosshitaneha";
            self::logSync($productId, $productType, $sku, $mode, 'success', $msg);

            return ['success' => true, 'message' => $msg, 'sku' => $sku];

        } catch (\Exception $e) {
            if ($childPdo->inTransaction()) {
                $childPdo->rollBack();
            }
            $errMsg = "Sync failed for SKU $sku: " . $e->getMessage();
            self::logSync($productId, $productType, $sku, $mode, 'failed', $errMsg);
            return ['success' => false, 'message' => $errMsg, 'sku' => $sku];
        }
    }

    /**
     * Delete / Soft Delete product from Child DB when deleted in Parent
     */
    public static function deleteProductFromChild($sku) {
        $sku = trim($sku);
        if (empty($sku)) return false;

        $childPdo = self::getChildPdo();
        if (!$childPdo) return false;

        try {
            $stmt = $childPdo->prepare("UPDATE products SET status = 'draft', deleted_at = NOW() WHERE sku = :sku");
            $stmt->execute([':sku' => $sku]);
            self::clearChildCache();
            return true;
        } catch (\Exception $e) {
            error_log("Failed to soft-delete SKU $sku in Child DB: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk Sync All Products from Parent to Child DB
     */
    public static function syncAllProducts($mode = 'manual') {
        $con = \Core\Database::getConnection('con');
        if (!$con) {
            return ['success' => false, 'message' => 'Parent DB connection failed'];
        }

        $productsToSync = [];
        // Fetch Jewellery
        $resJ = mysqli_query($con, "SELECT product_id as id, 'jewellery' as type FROM product");
        if ($resJ) {
            while ($r = mysqli_fetch_assoc($resJ)) {
                $productsToSync[] = $r;
            }
        }

        // Fetch Garments
        $resG = mysqli_query($con, "SELECT gproduct_id as id, 'garments' as type FROM garment_product");
        if ($resG) {
            while ($r = mysqli_fetch_assoc($resG)) {
                $productsToSync[] = $r;
            }
        }

        $total = count($productsToSync);
        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($productsToSync as $item) {
            $res = self::syncProduct($item['id'], $item['type'], $mode);
            if ($res['success']) {
                $successCount++;
            } else {
                $failedCount++;
                $errors[] = "ID {$item['id']} ({$item['type']}): " . $res['message'];
            }
        }

        return [
            'success' => true,
            'total' => $total,
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'errors' => array_slice($errors, 0, 20)
        ];
    }

    /**
     * Clear Cache directory in Child project
     */
    private static function clearChildCache() {
        $cacheDirs = [
            __DIR__ . '/../../yn/admin/cache',
            __DIR__ . '/../../../yn/admin/cache',
            $_SERVER['DOCUMENT_ROOT'] . '/yn/admin/cache',
            $_SERVER['DOCUMENT_ROOT'] . '/admin/cache'
        ];

        foreach ($cacheDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*');
                if ($files) {
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Log sync activity to product_sync_logs table
     */
    public static function logSync($productId, $productType, $sku, $mode, $status, $message) {
        try {
            $con = \Core\Database::getConnection('con');
            if (!$con) {
                usleep(50000);
                $con = \Core\Database::getConnection('con');
            }
            if (!$con) return;

            $stmt = @mysqli_prepare($con, "INSERT INTO product_sync_logs (product_id, product_type, sku, sync_mode, status, message) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                @mysqli_stmt_bind_param($stmt, "isssss", $productId, $productType, $sku, $mode, $status, $message);
                @mysqli_stmt_execute($stmt);
                @mysqli_stmt_close($stmt);
            }
        } catch (\Throwable $t) {
            error_log("logSync error for SKU $sku: " . $t->getMessage());
        }
    }

    /**
     * Infer category and subcategory IDs based on SKU prefix rules
     */
    public static function inferCategoryFromSku($sku, $type = 'auto', $existingCat = 0, $existingSub = 0) {
        $cleanSku = strtoupper(trim((string)$sku));
        $cat = (int)$existingCat;
        $sub = (int)$existingSub;

        // 1. Main Category Determination (Outfit vs Jewellery)
        if (empty($type) || $type === 'auto') {
            if (preg_match('/^(LEH|GW|GOWN|INDO|ANAR|KURTI|SAREE|SAR|DRESS|SUIT|CHOLI)/i', $cleanSku)) {
                $type = 'outfit';
            } else {
                $type = 'jewellery';
            }
        }

        $mainCategory = ($type === 'garments' || $type === 'garment' || $type === 'outfit') ? 'outfit' : 'jewellery';

        // 2. Category and Subcategory Inference based on reference menu structure
        if ($mainCategory === 'jewellery') {
            if ($cat <= 0) {
                if (str_starts_with($cleanSku, 'SET')) {
                    $cat = 1; // Necklace Sets
                    $sub = 2; // American Diamond
                } elseif (str_starts_with($cleanSku, 'K')) {
                    $cat = 1; // Necklace Sets
                    $sub = 3; // Kundan
                } elseif (str_starts_with($cleanSku, 'EAR')) {
                    $cat = 17; // Earrings
                    $sub = 59; // EARRINGS
                } elseif (str_starts_with($cleanSku, 'BR')) {
                    $cat = 22; // Bracelet
                } elseif (str_starts_with($cleanSku, 'JU') || str_starts_with($cleanSku, 'KAMAR')) {
                    $cat = 15; // Kamar Patta / Baju Bandh
                } elseif (str_starts_with($cleanSku, 'BANG')) {
                    $cat = 18; // Bangles
                } elseif (str_starts_with($cleanSku, 'DAM') || str_starts_with($cleanSku, 'MATH')) {
                    $cat = 19; // Damini / Mathapatti
                } elseif (str_starts_with($cleanSku, 'TIK')) {
                    $cat = 20; // Tikka
                } elseif (str_starts_with($cleanSku, 'HATH')) {
                    $cat = 21; // Hath Phool
                } elseif (str_starts_with($cleanSku, 'PAY') || str_starts_with($cleanSku, 'PAG')) {
                    $cat = 23; // Payal / Pag Pan
                } elseif (str_starts_with($cleanSku, 'PEND')) {
                    $cat = 24; // Pendant Set
                } elseif (str_starts_with($cleanSku, 'MALA')) {
                    $cat = 25; // Mala
                } elseif (str_starts_with($cleanSku, 'BOR')) {
                    $cat = 26; // Borlas
                } else {
                    $cat = 1; // Default Necklace Sets
                    $sub = 3; // Kundan
                }
            } else {
                if ($sub <= 0 && $cat == 1) {
                    if (str_starts_with($cleanSku, 'SET')) {
                        $sub = 2; // American Diamond
                    } elseif (str_starts_with($cleanSku, 'K')) {
                        $sub = 3; // Kundan
                    }
                }
            }
        } else { // DESIGNER OUTFITS
            if ($cat <= 0) {
                if (str_starts_with($cleanSku, 'LEH')) {
                    $cat = 10; // Lehenga Choli
                } elseif (str_starts_with($cleanSku, 'GW') || str_starts_with($cleanSku, 'GOWN')) {
                    $cat = 22; // Evening Gowns
                } elseif (str_starts_with($cleanSku, 'INDO')) {
                    $cat = 28; // Indo Western Outfits
                } elseif (str_starts_with($cleanSku, 'ANAR') || str_starts_with($cleanSku, 'KURTI')) {
                    $cat = 29; // Anarkalis / Kurtis
                } elseif (str_starts_with($cleanSku, 'SAREE') || str_starts_with($cleanSku, 'SAR')) {
                    $cat = 30; // Sarees
                } else {
                    $cat = 10; // Default Lehenga Choli
                }
            }
        }

        // Map category_id to human-readable name for child DB
        $catNames = [
            // Jewellery
            1 => 'Necklace Sets', 17 => 'Earrings', 22 => 'Bracelet',
            15 => 'Kamar Patta', 18 => 'Bangles', 19 => 'Damini / Mathapatti',
            20 => 'Tikka', 21 => 'Hath Phool', 23 => 'Payal / Pag Pan',
            24 => 'Pendant Set', 25 => 'Mala', 26 => 'Borlas',
            // Outfit
            10 => 'Lehenga Choli', 28 => 'Indo Western Outfits',
            29 => 'Anarkalis / Kurtis', 30 => 'Sarees'
        ];
        // For outfit cat=22, use 'Evening Gowns' instead of the jewellery 'Bracelet'
        if ($mainCategory === 'outfit' && $cat === 22) {
            $categoryName = 'Evening Gowns';
        } else {
            $categoryName = $catNames[$cat] ?? ($mainCategory === 'outfit' ? 'Outfit' : 'Jewellery');
        }

        return [
            'main_category' => $mainCategory,
            'type'          => $mainCategory === 'outfit' ? 'garments' : 'jewellery',
            'category_id'   => $cat,
            'subcategory_id'=> $sub,
            'category_name' => $categoryName
        ];
    }
}
