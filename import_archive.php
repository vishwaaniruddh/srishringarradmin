<?php
/**
 * Direct Server Folder & Archive Importer (Enhanced Pre-Audit, POS Live Pricing & Reliable Batching)
 * Imports products from the server's folders directly (with or without Excel spreadsheet)
 * Integrates directly with new_admin sidebar layout and fetches real POS prices.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

@ini_set('max_execution_time', 0);
@ini_set('memory_limit', '2048M');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Autoload composer & project classes
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $paths = [
        __DIR__ . DIRECTORY_SEPARATOR . $class . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . 'new_admin' . DIRECTORY_SEPARATOR . $class . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'new_admin' . DIRECTORY_SEPARATOR . $class . '.php'
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Database connections
$dbPos = \Core\Database::getConnection('con3');
$dbWeb = \Core\Database::getConnection('con');

$productModel = new \Models\ProductModel();
$categoryModel = new \Models\CategoryModel();

// Helper: Calculate POS Pricing for SKUs using Sri Shringarr POS formula
function calculatePosPriceDetails($sku, $dbPos, $productType = 'jewellery')
{
    if (!$dbPos || empty($sku)) {
        return [
            'found' => false,
            'sku' => $sku,
            'mrp' => 0,
            'cost_price' => 0,
            'quantity' => 0,
            'pos_category' => '',
            'selling_price' => 0,
            'rental_price' => 0,
            'deposit' => 0
        ];
    }

    $escSku = mysqli_real_escape_string($dbPos, trim($sku));
    $sql = "SELECT name, category, category_type, unit_price, cost_price, quantity 
            FROM phppos_items 
            WHERE name = '$escSku' 
            LIMIT 1";
    $q = @mysqli_query($dbPos, $sql);
    $item = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_assoc($q) : null;

    if (!$item) {
        return [
            'found' => false,
            'sku' => $sku,
            'mrp' => 0,
            'cost_price' => 0,
            'quantity' => 0,
            'pos_category' => '',
            'selling_price' => 0,
            'rental_price' => 0,
            'deposit' => 0
        ];
    }

    $mrp = (float) ($item['unit_price'] ?? 0);
    $cost = (float) ($item['cost_price'] ?? 0);
    $qty = (float) ($item['quantity'] ?? 0);
    $posCat = $item['category'] ?? '';

    // Commission from order_detail and phppos_rent
    $commSql = "SELECT SUM(CAST(REPLACE(commission_amt, ',', '') AS DECIMAL(10,2))) 
                FROM order_detail 
                WHERE item_id = '$escSku' 
                AND bill_id IN (SELECT bill_id FROM phppos_rent WHERE booking_status != 'Booked')";
    $commRes = @mysqli_query($dbPos, $commSql);
    $commRow = $commRes ? mysqli_fetch_row($commRes) : null;
    $commissionAmount = (float) ($commRow[0] ?? 0);

    $currentsp = $mrp - $commissionAmount;
    $sellingCalc = $mrp - $commissionAmount;
    $sellingCalc = $sellingCalc - ($sellingCalc * 0.4);

    $lastSellingPrice = 0;
    $addedRentPrice = 0;
    $deposit = 0;

    if ($productType === 'jewellery') {
        $courier = ($mrp <= 2000) ? 100 : (($mrp <= 5000) ? 250 : (($mrp <= 10000) ? 500 : 1000));
        if ($mrp >= 10000) {
            $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
        } else {
            $lastSellingPrice = $mrp - ($mrp * 0.5);
        }

        if ($currentsp > 0) {
            if ($mrp <= 10000) {
                $rentprice = $mrp * 0.20;
                $addedRentPrice = $courier + $rentprice;
                $deposit = $mrp * 0.35;
            } else {
                $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                $addedRentPrice = max(3000, $courier + $rentprice);
                $deposit = max(3000, $currentsp * 0.35);
            }
        } else {
            if ($mrp <= 10000) {
                $addedRentPrice = $courier + ($mrp * 0.20);
                $deposit = $mrp * 0.35;
            } else {
                $deposit = 3000;
                $addedRentPrice = 3000;
            }
        }
    } else {
        if ($mrp >= 10000) {
            $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
        } else {
            $lastSellingPrice = $mrp - ($mrp * 0.5);
        }

        if ($currentsp > 0) {
            if ($mrp <= 10000) {
                $rentprice = $mrp * 0.20;
                $addedRentPrice = $rentprice;
                $deposit = $mrp * 0.35;
            } else {
                $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                $addedRentPrice = max(3000, $rentprice);
                $deposit = max(3000, $currentsp * 0.35);
            }
        } else {
            if ($mrp <= 10000) {
                $addedRentPrice = ($mrp * 0.20);
                $deposit = $mrp * 0.35;
            } else {
                $deposit = 3000;
                $addedRentPrice = 3000;
            }
        }
    }

    return [
        'found' => true,
        'sku' => $sku,
        'mrp' => round($mrp, 2),
        'cost_price' => round($cost, 2),
        'quantity' => $qty,
        'pos_category' => $posCat,
        'selling_price' => round($lastSellingPrice, 2),
        'rental_price' => round($addedRentPrice, 2),
        'deposit' => round($deposit, 2)
    ];
}

// Bulk fetch POS Prices for a list of SKUs
function fetchBulkPosPrices($skuList, $dbPos, $productType = 'jewellery')
{
    if (!$dbPos || empty($skuList))
        return [];
    $results = [];

    foreach (array_chunk($skuList, 250) as $chunk) {
        $escaped = array_map(function ($s) use ($dbPos) {
            return "'" . mysqli_real_escape_string($dbPos, trim($s)) . "'";
        }, $chunk);
        $inList = implode(',', $escaped);

        $sql = "SELECT name, category, category_type, unit_price, cost_price, quantity 
                FROM phppos_items 
                WHERE name IN ($inList)";
        $q = @mysqli_query($dbPos, $sql);
        if ($q) {
            while ($item = mysqli_fetch_assoc($q)) {
                $sku = $item['name'];
                $mrp = (float) ($item['unit_price'] ?? 0);
                $cost = (float) ($item['cost_price'] ?? 0);
                $qty = (float) ($item['quantity'] ?? 0);
                $posCat = $item['category'] ?? '';

                $escSku = mysqli_real_escape_string($dbPos, $sku);
                $commSql = "SELECT SUM(CAST(REPLACE(commission_amt, ',', '') AS DECIMAL(10,2))) 
                            FROM order_detail 
                            WHERE item_id = '$escSku' 
                            AND bill_id IN (SELECT bill_id FROM phppos_rent WHERE booking_status != 'Booked')";
                $commRes = @mysqli_query($dbPos, $commSql);
                $commRow = $commRes ? mysqli_fetch_row($commRes) : null;
                $commissionAmount = (float) ($commRow[0] ?? 0);

                $currentsp = $mrp - $commissionAmount;
                $sellingCalc = $mrp - $commissionAmount;
                $sellingCalc = $sellingCalc - ($sellingCalc * 0.4);

                $lastSellingPrice = 0;
                $addedRentPrice = 0;
                $deposit = 0;

                if ($productType === 'jewellery') {
                    $courier = ($mrp <= 2000) ? 100 : (($mrp <= 5000) ? 250 : (($mrp <= 10000) ? 500 : 1000));
                    if ($mrp >= 10000) {
                        $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
                    } else {
                        $lastSellingPrice = $mrp - ($mrp * 0.5);
                    }

                    if ($currentsp > 0) {
                        if ($mrp <= 10000) {
                            $rentprice = $mrp * 0.20;
                            $addedRentPrice = $courier + $rentprice;
                            $deposit = $mrp * 0.35;
                        } else {
                            $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                            $addedRentPrice = max(3000, $courier + $rentprice);
                            $deposit = max(3000, $currentsp * 0.35);
                        }
                    } else {
                        if ($mrp <= 10000) {
                            $addedRentPrice = $courier + ($mrp * 0.20);
                            $deposit = $mrp * 0.35;
                        } else {
                            $deposit = 3000;
                            $addedRentPrice = 3000;
                        }
                    }
                } else {
                    if ($mrp >= 10000) {
                        $lastSellingPrice = ($sellingCalc < 5000) ? 5000 : $sellingCalc;
                    } else {
                        $lastSellingPrice = $mrp - ($mrp * 0.5);
                    }

                    if ($currentsp > 0) {
                        if ($mrp <= 10000) {
                            $rentprice = $mrp * 0.20;
                            $addedRentPrice = $rentprice;
                            $deposit = $mrp * 0.35;
                        } else {
                            $rentprice = ($currentsp <= 40000) ? ($currentsp * 0.20) : (($currentsp <= 60000) ? ($currentsp * 0.17) : ($currentsp * 0.15));
                            $addedRentPrice = max(3000, $rentprice);
                            $deposit = max(3000, $currentsp * 0.35);
                        }
                    } else {
                        if ($mrp <= 10000) {
                            $addedRentPrice = ($mrp * 0.20);
                            $deposit = $mrp * 0.35;
                        } else {
                            $deposit = 3000;
                            $addedRentPrice = 3000;
                        }
                    }
                }

                $results[strtolower($sku)] = [
                    'found' => true,
                    'sku' => $sku,
                    'mrp' => round($mrp, 2),
                    'cost_price' => round($cost, 2),
                    'quantity' => $qty,
                    'pos_category' => $posCat,
                    'selling_price' => round($lastSellingPrice, 2),
                    'rental_price' => round($addedRentPrice, 2),
                    'deposit' => round($deposit, 2)
                ];
            }
        }
    }

    return $results;
}

// Auto-discover candidate folders on server
function getCandidateServerFolders()
{
    $candidates = [];
    $rootsToCheck = [
        dirname(__DIR__), // public_html or c:/xampp/htdocs/ss
        __DIR__,          // new_admin
    ];
    $ignoredNames = [
        '.',
        '..',
        '.git',
        '.vscode',
        '.agents',
        'vendor',
        'node_modules',
        'Config',
        'Controllers',
        'Models',
        'Views',
        'assets',
        'scratch',
        'Logs',
        'API',
        'pos',
        'yn',
        'client',
        'folder_copier',
        'ss_dashboard'
    ];

    foreach ($rootsToCheck as $root) {
        if (!is_dir($root))
            continue;
        $items = @scandir($root);
        if (!$items)
            continue;
        foreach ($items as $item) {
            if (in_array($item, $ignoredNames))
                continue;
            $full = $root . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                $subItems = @scandir($full);
                $hasSubfolders = false;
                $subCount = 0;
                if ($subItems) {
                    foreach ($subItems as $sub) {
                        if ($sub === '.' || $sub === '..')
                            continue;
                        if (is_dir($full . DIRECTORY_SEPARATOR . $sub)) {
                            $hasSubfolders = true;
                            $subCount++;
                        }
                    }
                }
                $candidates[$full] = [
                    'name' => $item,
                    'path' => $full,
                    'sub_folder_count' => $subCount,
                    'has_sku_folders' => $hasSubfolders
                ];
            }
        }
    }
    return $candidates;
}

// -------------------------------------------------------------
// AJAX API Handler for Batch Processing: process_row
// -------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'process_row') {
    header('Content-Type: application/json');
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            echo json_encode([
                'status' => 'error',
                'sku' => 'UNKNOWN',
                'message' => 'PHP Fatal Error: ' . $error['message'] . ' in ' . basename($error['file']) . ' line ' . $error['line']
            ]);
        }
    });

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input)
        $input = $_POST;

    try {
        $type = strtolower(trim($input['type'] ?? 'jewellery'));
        $code = trim($input['sku'] ?? $input['sku_code'] ?? $input['code'] ?? '');
        $skuFolder = trim($input['sku_folder_path'] ?? '');

        if (empty($code))
            throw new \Exception("Missing SKU");

        if (empty($type)) {
            $type = (stripos($code, 'GM') === 0 || stripos($code, 'LM') === 0 || stripos($code, 'FM') === 0) ? 'garments' : 'jewellery';
        } else if ($type === 'garment' || $type === 'apparel') {
            $type = 'garments';
        } else if ($type === 'jewelry' || $type === 'jewel') {
            $type = 'jewellery';
        }

        // 1. Check if SKU exists in EITHER jewellery OR garments -> SKIP if exists
        $existsJewel = $productModel->checkProductExists($code, 'jewellery');
        $existsGarment = $productModel->checkProductExists($code, 'garments');
        if ($existsJewel || $existsGarment) {
            echo json_encode([
                'status' => 'skipped',
                'sku' => $code,
                'message' => "SKU $code already exists in database (" . ($existsJewel ? 'jewellery' : 'garments') . "). Skipped."
            ]);
            exit;
        }

        // 2. Process & Copy Images from SKU Folder
        $downloadedImages = [];
        $current_year = date('Y');
        $current_month = date('m');
        $upload_base = __DIR__ . "/../yn/uploads/";
        if (!file_exists($upload_base)) {
            $upload_base = __DIR__ . "/../../yn/uploads/";
        }
        if (!file_exists($upload_base)) {
            $upload_base = __DIR__ . "/yn/uploads/";
        }
        $upload_path = $current_year . '/' . $current_month . '/';
        $full_upload_path = $upload_base . $upload_path;

        if (!file_exists($full_upload_path)) {
            @mkdir($full_upload_path, 0777, true);
        }

        if (!empty($skuFolder) && is_dir($skuFolder)) {
            $files = @scandir($skuFolder);
            $validExts = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];
            if ($files) {
                foreach ($files as $f) {
                    if ($f === '.' || $f === '..')
                        continue;
                    $filePath = $skuFolder . DIRECTORY_SEPARATOR . $f;
                    if (is_file($filePath)) {
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        if (in_array($ext, $validExts)) {
                            $newFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                            if (@copy($filePath, $full_upload_path . $newFilename)) {
                                $downloadedImages[] = $upload_path . $newFilename;
                            }
                        }
                    }
                }
            }
        }
        // 3. Category Resolution
        $catId = (int) ($input['category_id'] ?? $input['category'] ?? 0);
        $subcatId = (int) ($input['subcat_id'] ?? $input['sub_category'] ?? 0);

        // 4. Fetch / Apply Real POS Pricing
        $posPricing = calculatePosPriceDetails($code, $dbPos, $type);

        $sellingPrice = (float) ($input['s_price'] ?? $input['sales_price'] ?? 0);
        if ($sellingPrice <= 0 && $posPricing['found']) {
            $sellingPrice = $posPricing['selling_price'];
        }

        $rentalPrice = (float) ($input['rental_price'] ?? $input['rent_price'] ?? 0);
        if ($rentalPrice <= 0 && $posPricing['found']) {
            $rentalPrice = $posPricing['rental_price'];
        }

        $deposit = (float) ($input['deposit'] ?? 0);
        if ($deposit <= 0 && $posPricing['found']) {
            $deposit = $posPricing['deposit'];
        }

        $productName = trim($input['name'] ?? '');
        if (empty($productName) || $productName === 'Imported Product') {
            if (!empty($input['category_name'])) {
                $productName = trim($input['category_name']) . ' ' . $code;
            } elseif (!empty($posPricing['pos_category'])) {
                $productName = $posPricing['pos_category'] . ' ' . $code;
            } else {
                $productName = 'Product ' . $code;
            }
        }

        $saveData = [
            'code' => $code,
            'name' => $productName,
            'description' => $input['description'] ?? '',
            'category' => $catId,
            'sub_category' => $subcatId,
            'categories' => $catId > 0 ? [$catId] : [],
            'sub_categories' => $subcatId > 0 ? [$subcatId] : [],
            's_price' => $sellingPrice,
            'rental_price' => $rentalPrice,
            'deposit' => $deposit,
            'size_avail' => $input['size_avail'] ?? $input['size'] ?? '',
            'brand_name' => $input['brand_name'] ?? $input['brand'] ?? 'Sri Shringarr',
            'colors' => [],
            'brand_color' => [],
            'price_source' => 'pos',
            'availability' => 'both'
        ];

        // Save with autoDetectColors = false for fast batch execution
        $productModel->saveProduct($type, $saveData, $downloadedImages, false);

        echo json_encode([
            'status' => 'success',
            'sku' => $code,
            'images_count' => count($downloadedImages),
            'mrp' => $posPricing['mrp'],
            'rent_price' => $rentalPrice,
            'deposit' => $deposit,
            'message' => "Created product $code with " . count($downloadedImages) . " images and POS pricing."
        ]);
        exit;
    } catch (\Exception $e) {
        echo json_encode([
            'status' => 'error',
            'sku' => $code ?? 'UNKNOWN',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// -------------------------------------------------------------
// Scan Directory Function (Supports Excel OR SKU Folders Directly)
// -------------------------------------------------------------
function scanArchiveDirectory($archiveDir, $productType = 'jewellery', $selectedCatId = 0, $selectedCatName = '', $dbPos = null, $dbWeb = null)
{
    if (!$archiveDir || !is_dir($archiveDir)) {
        return ['error' => 'Archive directory not found or not specified.'];
    }

    $spreadsheetFile = null;
    $skuFolders = [];
    $allEntries = @scandir($archiveDir);
    if (!$allEntries) {
        return ['error' => 'Cannot read folder contents. Please verify directory permissions: ' . htmlspecialchars($archiveDir)];
    }

    $validExts = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

    foreach ($allEntries as $entry) {
        if ($entry === '.' || $entry === '..')
            continue;
        $fullPath = $archiveDir . DIRECTORY_SEPARATOR . $entry;

        if (is_file($fullPath)) {
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                $spreadsheetFile = $fullPath;
            }
        } elseif (is_dir($fullPath)) {
            $skuFolders[strtolower(trim($entry))] = [
                'sku' => trim($entry),
                'path' => $fullPath
            ];
        }
    }

    $parsedProducts = [];

    // Mode A: Parse Spreadsheet if present
    if ($spreadsheetFile) {
        $ext = strtolower(pathinfo($spreadsheetFile, PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            $handle = fopen($spreadsheetFile, 'r');
            if ($handle) {
                $headers = [];
                $rowIdx = 0;
                while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    $rowIdx++;
                    if ($rowIdx === 1) {
                        $headers = array_map(function ($h) {
                            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $h)));
                        }, $row);
                        continue;
                    }
                    if (empty(array_filter($row)))
                        continue;
                    $item = [];
                    foreach ($headers as $idx => $header) {
                        $item[$header] = isset($row[$idx]) ? trim((string) $row[$idx]) : '';
                    }
                    $parsedProducts[] = $item;
                }
                fclose($handle);
            }
        } else {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheetFile);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);

                if (!empty($rows) && count($rows) > 1) {
                    $rawHeaders = $rows[0];
                    $headers = array_map(function ($h) {
                        return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $h)));
                    }, $rawHeaders);

                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        if (empty(array_filter($row, function ($v) {
                            return $v !== null && $v !== ''; })))
                            continue;
                        $item = [];
                        foreach ($headers as $idx => $header) {
                            if (!empty($header)) {
                                $val = $row[$idx] ?? '';
                                $item[$header] = is_string($val) ? trim($val) : (string) $val;
                            }
                        }
                        $parsedProducts[] = $item;
                    }
                }
            }
        }
    }

    // Mode B: If NO spreadsheet found, automatically generate product list directly from SKU folders!
    if (empty($parsedProducts) && !empty($skuFolders)) {
        foreach ($skuFolders as $entryKey => $folderInfo) {
            $sku = $folderInfo['sku'];
            $parsedProducts[] = [
                'sku' => $sku,
                'name' => (!empty($selectedCatName) ? $selectedCatName . ' ' : '') . $sku,
                'category_id' => $selectedCatId,
                'type' => $productType
            ];
        }
    }

    if (empty($parsedProducts) && empty($skuFolders)) {
        return ['error' => 'No SKU subfolders or product spreadsheets found in ' . htmlspecialchars($archiveDir)];
    }

    // Match products with SKU folders & count images
    $validProducts = [];
    $skuList = [];
    foreach ($parsedProducts as $p) {
        $sku = trim($p['sku'] ?? $p['sku_code'] ?? $p['product_code'] ?? $p['code'] ?? '');
        if (empty($sku))
            continue;

        if (empty($p['sku']))
            $p['sku'] = $sku;

        $skuKey = strtolower($sku);
        $folderInfo = $skuFolders[$skuKey] ?? null;
        $matchedPath = $folderInfo ? $folderInfo['path'] : null;

        $imgCount = 0;
        if ($matchedPath && is_dir($matchedPath)) {
            $imgs = @scandir($matchedPath);
            if ($imgs) {
                foreach ($imgs as $im) {
                    if ($im === '.' || $im === '..')
                        continue;
                    $ext = strtolower(pathinfo($im, PATHINFO_EXTENSION));
                    if (in_array($ext, $validExts))
                        $imgCount++;
                }
            }
        }

        $p['sku_folder_path'] = $matchedPath ?: '';
        $p['images_count'] = $imgCount;
        $validProducts[] = $p;
        $skuList[] = $sku;
    }

    // Single Fast Bulk Query to Pre-Check Existing SKUs in Database
    $existingSkus = [];
    if ($dbWeb && !empty($skuList)) {
        foreach (array_chunk($skuList, 400) as $chunk) {
            $escaped = array_map(function ($s) use ($dbWeb) {
                return "'" . mysqli_real_escape_string($dbWeb, $s) . "'";
            }, $chunk);
            $inList = implode(',', $escaped);

            $q1 = @mysqli_query($dbWeb, "SELECT product_code FROM product WHERE product_code IN ($inList)");
            if ($q1) {
                while ($r = mysqli_fetch_assoc($q1)) {
                    $existingSkus[strtolower($r['product_code'])] = 'jewellery';
                }
            }

            $q2 = @mysqli_query($dbWeb, "SELECT gproduct_code FROM garment_product WHERE gproduct_code IN ($inList)");
            if ($q2) {
                while ($r = mysqli_fetch_assoc($q2)) {
                    $existingSkus[strtolower($r['gproduct_code'])] = 'garments';
                }
            }
        }
    }

    // Bulk Fetch Live POS Prices for all SKUs
    $posPrices = fetchBulkPosPrices($skuList, $dbPos, $productType);

    $readyCount = 0;
    $existCount = 0;
    $posFoundCount = 0;

    foreach ($validProducts as &$p) {
        $key = strtolower($p['sku']);
        $p['pos'] = $posPrices[$key] ?? [
            'found' => false,
            'mrp' => 0,
            'selling_price' => 0,
            'rental_price' => 0,
            'deposit' => 0,
            'quantity' => 0,
            'pos_category' => ''
        ];

        if ($p['pos']['found']) {
            $posFoundCount++;
            if (empty($p['name']) || $p['name'] === 'Imported Product') {
                if (!empty($selectedCatName)) {
                    $p['name'] = $selectedCatName . ' ' . $p['sku'];
                } elseif (!empty($p['pos']['pos_category'])) {
                    $p['name'] = $p['pos']['pos_category'] . ' ' . $p['sku'];
                }
            }
        }

        if (isset($existingSkus[$key])) {
            $p['pre_status'] = 'exists';
            $p['pre_message'] = 'Already in DB (' . $existingSkus[$key] . ')';
            $existCount++;
        } else {
            $p['pre_status'] = 'ready';
            $p['pre_message'] = 'Ready to Create (' . $p['images_count'] . ' photos)';
            $readyCount++;
        }
    }

    return [
        'archive_dir' => $archiveDir,
        'spreadsheet_file' => $spreadsheetFile ? basename($spreadsheetFile) : null,
        'has_spreadsheet' => !empty($spreadsheetFile),
        'total_folders' => count($skuFolders),
        'total_products' => count($validProducts),
        'ready_count' => $readyCount,
        'exist_count' => $existCount,
        'pos_found_count' => $posFoundCount,
        'products' => $validProducts
    ];
}

// -------------------------------------------------------------
// Initial Setup & Directory Resolution
// -------------------------------------------------------------
$detectedFolders = getCandidateServerFolders();

// Resolve Active Directory
$selectedCustomPath = trim($_POST['custom_path'] ?? $_GET['custom_path'] ?? '');
if (!empty($_POST['custom_path_manual']))
    $selectedCustomPath = trim($_POST['custom_path_manual']);
if (!empty($_GET['custom_path_manual']))
    $selectedCustomPath = trim($_GET['custom_path_manual']);

$archiveDir = null;

if (!empty($selectedCustomPath)) {
    if (is_dir($selectedCustomPath)) {
        $archiveDir = realpath($selectedCustomPath);
    } elseif (is_dir(dirname(__DIR__) . '/' . $selectedCustomPath)) {
        $archiveDir = realpath(dirname(__DIR__) . '/' . $selectedCustomPath);
    } elseif (is_dir(__DIR__ . '/' . $selectedCustomPath)) {
        $archiveDir = realpath(__DIR__ . '/' . $selectedCustomPath);
    }
}

// Default Fallbacks
if (!$archiveDir) {
    $fallbackPaths = [
        dirname(__DIR__) . '/hath_phool',
        dirname(__DIR__) . '/hathphool',
        dirname(__DIR__) . '/Hath_Phool',
        __DIR__ . '/hath_phool',
        dirname(__DIR__) . '/new_earrng',
        __DIR__ . '/new_earrng',
        dirname(__DIR__) . '/archive',
        __DIR__ . '/archive'
    ];
    foreach ($fallbackPaths as $fp) {
        if (is_dir($fp)) {
            $archiveDir = realpath($fp);
            break;
        }
    }
    // If still null, pick the first candidate folder if available
    if (!$archiveDir && !empty($detectedFolders)) {
        $first = reset($detectedFolders);
        $archiveDir = $first['path'];
    }
}

// Category lists
$jewelCategories = $categoryModel->getJewelCategories();
$jewelSubcategories = $categoryModel->getJewelSubcategories();
$garmentCategories = $categoryModel->getGarmentCategories();
$garmentSubcategories = $categoryModel->getGarmentSubcategories();

// Selected Category from Request
$selectedType = strtolower(trim($_POST['product_type'] ?? $_GET['product_type'] ?? 'jewellery'));
if (!in_array($selectedType, ['jewellery', 'garments']))
    $selectedType = 'jewellery';

$selectedCatId = (int) ($_POST['category_id'] ?? $_GET['category_id'] ?? 0);
// Auto-select "HATH PHOOL" (ID 23) if no category is picked yet and jewellery is active
if ($selectedCatId === 0 && $selectedType === 'jewellery') {
    foreach ($jewelCategories as $jc) {
        if (stripos($jc['name'], 'hath') !== false) {
            $selectedCatId = (int) $jc['id'];
            break;
        }
    }
    if ($selectedCatId === 0 && !empty($jewelCategories)) {
        $selectedCatId = (int) $jewelCategories[0]['id'];
    }
}

$selectedSubcatId = (int) ($_POST['subcat_id'] ?? $_GET['subcat_id'] ?? 0);

// Find Selected Category Name
$selectedCatName = '';
if ($selectedType === 'jewellery') {
    foreach ($jewelCategories as $jc) {
        if ((int) $jc['id'] === $selectedCatId) {
            $selectedCatName = $jc['name'];
            break;
        }
    }
} else {
    foreach ($garmentCategories as $gc) {
        if ((int) $gc['id'] === $selectedCatId) {
            $selectedCatName = $gc['name'];
            break;
        }
    }
}

// Handle optional direct Excel / CSV file upload into active directory
$uploadMessage = null;
$uploadError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    if (!$archiveDir || !is_dir($archiveDir)) {
        $uploadError = "Target folder does not exist or is not specified.";
    } else {
        $uploaded = $_FILES['excel_file'];
        if ($uploaded['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                $targetFile = $archiveDir . DIRECTORY_SEPARATOR . $uploaded['name'];
                if (move_uploaded_file($uploaded['tmp_name'], $targetFile)) {
                    $uploadMessage = "Successfully uploaded " . htmlspecialchars($uploaded['name']) . " to " . htmlspecialchars(basename($archiveDir)) . "/";
                } else {
                    $uploadError = "Failed to move uploaded file. Check folder write permissions on: " . htmlspecialchars($archiveDir);
                }
            } else {
                $uploadError = "Invalid file type (." . htmlspecialchars($ext) . "). Please upload a .xlsx, .xls, or .csv file.";
            }
        } else {
            $uploadError = "File upload failed with error code: " . $uploaded['error'];
        }
    }
}

// Perform Pre-Scan Audit
$scanResult = scanArchiveDirectory($archiveDir, $selectedType, $selectedCatId, $selectedCatName, $dbPos, $dbWeb);
$pageTitle = 'Server Folder Product Importer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Server Folder Importer - Srishringarr</title>
    <?php include __DIR__ . '/Views/partials/head.php'; ?>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .badge-pos {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-no-pos {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <?php include __DIR__ . '/Views/partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar Header -->
            <?php include __DIR__ . '/Views/partials/topbar.php'; ?>

            <!-- Main Content Panel -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-7xl mx-auto space-y-6">

                    <!-- Header Banner Card -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xl shadow-sm">
                                <i class="fas fa-server"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-slate-900">Server Folder Product Importer</h1>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-md border border-slate-200 uppercase tracking-wider">POS Live Sync</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Import products directly from server image folders, auto-match SKUs, and fetch real-time prices from the Sri Shringarr POS database.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <a href="index.php?controller=product&action=index" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold border border-slate-200 transition-all flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-arrow-left text-slate-400"></i> All Products
                            </a>
                            <a href="index.php?controller=product&action=import" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold border border-slate-200 transition-all flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-file-excel text-emerald-600"></i> ZIP / Excel Uploader
                            </a>
                        </div>
                    </div>

                    <?php if ($uploadMessage): ?>
                            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-xs text-emerald-800 flex items-center gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                                <span><?php echo $uploadMessage; ?></span>
                            </div>
                    <?php endif; ?>

                    <?php if ($uploadError): ?>
                            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl text-xs text-rose-800 flex items-center gap-2.5">
                                <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                                <span><?php echo $uploadError; ?></span>
                            </div>
                    <?php endif; ?>

                    <!-- STEP 1: CONFIGURATION BAR (Choose Folder & Choose Category) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs">1</span>
                                Choose Server Folder & Product Category
                            </h2>
                            <span class="text-xs text-slate-500">Live prices are fetched from real POS (<code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">phppos_items</code>)</span>
                        </div>

                        <form method="GET" action="import_archive.php" id="folderForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <!-- Folder Selection -->
                                <div class="md:col-span-5 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        <i class="fas fa-folder-open text-slate-500 mr-1"></i> Choose Server Folder
                                    </label>
                                    <div class="flex gap-2">
                                        <select name="custom_path" id="folderSelect" class="flex-1 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono text-slate-800 focus:outline-none focus:border-slate-900 shadow-sm" onchange="if(this.value !== 'custom') document.getElementById('folderForm').submit(); else document.getElementById('customInputWrapper').classList.remove('hidden');">
                                            <?php if (empty($detectedFolders)): ?>
                                                    <option value="<?php echo htmlspecialchars($archiveDir ?? ''); ?>"><?php echo htmlspecialchars($archiveDir ?? 'No folders auto-detected'); ?></option>
                                            <?php else: ?>
                                                    <?php foreach ($detectedFolders as $cPath => $c): ?>
                                                            <option value="<?php echo htmlspecialchars($cPath); ?>" <?php echo ($archiveDir === $cPath) ? 'selected' : ''; ?>>
                                                                📂 <?php echo htmlspecialchars($c['name']); ?> (<?php echo $c['sub_folder_count']; ?> subfolders)
                                                            </option>
                                                    <?php endforeach; ?>
                                                    <option value="custom" <?php echo (!empty($selectedCustomPath) && !isset($detectedFolders[$archiveDir])) ? 'selected' : ''; ?>>✏️ Enter Custom Path / Folder Name...</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div id="customInputWrapper" class="<?php echo (!empty($selectedCustomPath) && !isset($detectedFolders[$archiveDir])) ? '' : 'hidden'; ?> pt-2">
                                        <input type="text" name="custom_path_manual" id="customPathManual" value="<?php echo htmlspecialchars($archiveDir ?? ''); ?>" placeholder="e.g. hath_phool or /path/to/folder" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono text-slate-800 focus:outline-none focus:border-slate-900">
                                    </div>
                                </div>

                                <!-- Product Type -->
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        <i class="fas fa-layer-group text-slate-500 mr-1"></i> Type
                                    </label>
                                    <select name="product_type" id="productTypeSelect" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-900 shadow-sm" onchange="document.getElementById('folderForm').submit();">
                                        <option value="jewellery" <?php echo ($selectedType === 'jewellery') ? 'selected' : ''; ?>>Jewellery</option>
                                        <option value="garments" <?php echo ($selectedType === 'garments') ? 'selected' : ''; ?>>Garments</option>
                                    </select>
                                </div>

                                <!-- Main Category Selection -->
                                <div class="md:col-span-3 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        <i class="fas fa-tags text-slate-500 mr-1"></i> Main Category
                                    </label>
                                    <select name="category_id" id="categorySelect" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-900 shadow-sm" onchange="document.getElementById('folderForm').submit();">
                                        <?php if ($selectedType === 'jewellery'): ?>
                                                <?php foreach ($jewelCategories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo ((int) $cat['id'] === $selectedCatId) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?> (ID: <?php echo $cat['id']; ?>)
                                                        </option>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <?php foreach ($garmentCategories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo ((int) $cat['id'] === $selectedCatId) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?> (ID: <?php echo $cat['id']; ?>)
                                                        </option>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Subcategory Selection -->
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        <i class="fas fa-sitemap text-slate-500 mr-1"></i> Subcategory
                                    </label>
                                    <select name="subcat_id" id="subcatSelect" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-900 shadow-sm" onchange="document.getElementById('folderForm').submit();">
                                        <option value="0">None / General</option>
                                        <?php if ($selectedType === 'jewellery'): ?>
                                                <?php foreach ($jewelSubcategories as $sub): ?>
                                                        <?php if (empty($sub['maincat_id']) || (int) $sub['maincat_id'] === $selectedCatId): ?>
                                                                <option value="<?php echo $sub['id']; ?>" <?php echo ((int) $sub['id'] === $selectedSubcatId) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($sub['name']); ?> (ID: <?php echo $sub['id']; ?>)
                                                                </option>
                                                        <?php endif; ?>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <?php foreach ($garmentSubcategories as $sub): ?>
                                                        <option value="<?php echo $sub['id']; ?>" <?php echo ((int) $sub['id'] === $selectedSubcatId) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($sub['name']); ?>
                                                        </option>
                                                <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 flex-wrap gap-2 text-xs">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <span>Active Folder:</span>
                                    <code class="bg-slate-100 px-2 py-1 rounded text-slate-800 font-mono text-[11px] border border-slate-200"><?php echo htmlspecialchars($archiveDir ?? 'None selected'); ?></code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="document.getElementById('excel_upload_drawer').classList.toggle('hidden')" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg text-xs transition-all flex items-center gap-1.5 shadow-sm">
                                        <i class="fas fa-file-excel text-emerald-600"></i> Optional: Upload Excel (.xlsx)
                                    </button>
                                    <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg text-xs transition-all flex items-center gap-1.5 shadow-sm">
                                        <i class="fas fa-sync-alt"></i> Re-Scan Folder
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Optional Excel Upload Drawer -->
                    <div id="excel_upload_drawer" class="hidden bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-file-excel text-emerald-600"></i> Upload / Replace Spreadsheet in: <code class="bg-white px-2 py-0.5 rounded border border-slate-200 font-mono"><?php echo htmlspecialchars(basename($archiveDir ?? '')); ?>/</code>
                            </h3>
                            <button type="button" onclick="document.getElementById('excel_upload_drawer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500">Note: An Excel file is optional! If no spreadsheet is provided, the importer directly scans every SKU folder and fetches all details from POS.</p>
                        <form method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="flex items-center gap-3 flex-wrap">
                            <input type="hidden" name="custom_path" value="<?php echo htmlspecialchars($archiveDir ?? ''); ?>">
                            <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="flex-1 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 focus:outline-none shadow-sm">
                            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition-all flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-upload"></i> Upload & Re-Scan
                            </button>
                        </form>
                    </div>

                    <?php if (!empty($scanResult['error'])): ?>
                            <!-- Error Card -->
                            <div class="bg-rose-50 border border-rose-200 p-6 rounded-2xl text-xs text-rose-800 space-y-3">
                                <div class="flex items-center gap-2 text-sm font-bold text-rose-700">
                                    <i class="fas fa-exclamation-triangle"></i> Folder Scan Issue
                                </div>
                                <p><?php echo htmlspecialchars($scanResult['error']); ?></p>
                                <p class="text-slate-600">Please choose or enter a valid folder path on the server containing your SKU image folders.</p>
                            </div>
                    <?php else: ?>

                            <!-- STEP 2: PRE-SCAN AUDIT METRICS -->
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center justify-between">
                                        <span>Total Detected SKUs</span>
                                        <i class="fas fa-box-open text-slate-400"></i>
                                    </div>
                                    <div class="text-2xl font-black text-slate-900"><?php echo $scanResult['total_products']; ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1 truncate">
                                        <?php if ($scanResult['has_spreadsheet']): ?>
                                                <span class="text-emerald-600 font-bold"><i class="fas fa-file-excel mr-1"></i><?php echo htmlspecialchars($scanResult['spreadsheet_file']); ?></span>
                                        <?php else: ?>
                                                <span class="text-indigo-600 font-medium"><i class="fas fa-folder mr-1"></i>From SKU Folders Direct</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm cursor-pointer hover:border-slate-400 transition-all" onclick="filterTable('ready')">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center justify-between">
                                        <span>Ready to Create (New)</span>
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    </div>
                                    <div class="text-2xl font-black text-emerald-600"><?php echo $scanResult['ready_count']; ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1">Not in web database yet</div>
                                </div>

                                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm cursor-pointer hover:border-slate-400 transition-all" onclick="filterTable('exists')">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center justify-between">
                                        <span>Already in DB (Skipped)</span>
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    </div>
                                    <div class="text-2xl font-black text-amber-600"><?php echo $scanResult['exist_count']; ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1">Will not be overwritten</div>
                                </div>

                                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center justify-between">
                                        <span>Matched in POS</span>
                                        <i class="fas fa-cash-register text-slate-400"></i>
                                    </div>
                                    <div class="text-2xl font-black text-slate-900"><?php echo $scanResult['pos_found_count']; ?> <span class="text-xs font-normal text-slate-400">/ <?php echo $scanResult['total_products']; ?></span></div>
                                    <div class="text-[10px] text-slate-500 mt-1">Live price & rent ready</div>
                                </div>
                            </div>

                            <!-- STEP 3: EXECUTION CONTROLS & PROGRESS -->
                            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
                                <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-slate-100">
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                            <i class="fas fa-play text-slate-700"></i> Import Execution Controls
                                        </h2>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Importing into: <b class="text-slate-800"><?php echo htmlspecialchars($selectedCatName ?: 'Category ' . $selectedCatId); ?></b> 
                                            (Subcat: <?php echo $selectedSubcatId > 0 ? 'ID ' . $selectedSubcatId : 'General'; ?>)
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <button id="download_report_btn" class="hidden px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 transition-all flex items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-download text-emerald-600"></i> Download CSV Report
                                        </button>
                                        <button id="start_new_only_btn" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                                            <i class="fas fa-rocket"></i> Import ONLY New (<?php echo $scanResult['ready_count']; ?> Items)
                                        </button>
                                        <button id="start_all_btn" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 transition-all flex items-center gap-2 shadow-sm">
                                            <i class="fas fa-play"></i> Process All (<?php echo $scanResult['total_products']; ?> Items)
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Tabs & Stats -->
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs">
                                        <button type="button" onclick="filterTable('all')" id="tab_all" class="px-3 py-1.5 rounded-lg font-bold bg-white text-slate-900 shadow-sm transition-all">All (<?php echo $scanResult['total_products']; ?>)</button>
                                        <button type="button" onclick="filterTable('ready')" id="tab_ready" class="px-3 py-1.5 rounded-lg font-bold text-slate-600 hover:text-slate-900 transition-all">Ready / New (<?php echo $scanResult['ready_count']; ?>)</button>
                                        <button type="button" onclick="filterTable('exists')" id="tab_exists" class="px-3 py-1.5 rounded-lg font-bold text-slate-600 hover:text-slate-900 transition-all">Already in DB (<?php echo $scanResult['exist_count']; ?>)</button>
                                    </div>

                                    <div class="flex items-center gap-4 text-xs font-mono">
                                        <div>Created: <b id="cnt_created" class="text-emerald-600">0</b></div>
                                        <div>Skipped: <b id="cnt_skipped" class="text-amber-600">0</b></div>
                                        <div>Errors: <b id="cnt_error" class="text-rose-600">0</b></div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs">
                                        <span id="progress_status" class="text-slate-500 font-mono">Pre-scan audit ready. Click "Import ONLY New" to begin.</span>
                                        <span id="progress_pct" class="font-bold text-slate-900">0%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div id="progress_bar" class="bg-slate-900 h-full w-0 transition-all duration-150"></div>
                                    </div>
                                </div>

                                <!-- Pre-Scan Table with Live POS Pricing -->
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                                    <div class="max-h-[550px] overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead class="bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-wider sticky top-0 border-b border-slate-200 z-10">
                                                <tr>
                                                    <th class="px-4 py-3">#</th>
                                                    <th class="px-4 py-3">SKU</th>
                                                    <th class="px-4 py-3">Product Name</th>
                                                    <th class="px-4 py-3">Photos</th>
                                                    <th class="px-4 py-3">Real Server POS Pricing</th>
                                                    <th class="px-4 py-3 text-right">Status / Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="log_tbody" class="divide-y divide-slate-100 font-mono text-[11px]">
                                                <?php foreach ($scanResult['products'] as $idx => $p): ?>
                                                        <tr id="row-<?php echo $idx; ?>" class="product-row hover:bg-slate-50/70 transition-colors" data-prestatus="<?php echo $p['pre_status']; ?>" data-sku="<?php echo htmlspecialchars($p['sku']); ?>">
                                                            <td class="px-4 py-2.5 text-slate-400"><?php echo $idx + 1; ?></td>
                                                            <td class="px-4 py-2.5 font-bold text-slate-900"><?php echo htmlspecialchars($p['sku']); ?></td>
                                                            <td class="px-4 py-2.5 text-slate-700 truncate max-w-[200px]" title="<?php echo htmlspecialchars($p['name'] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($p['name'] ?? ''); ?>
                                                            </td>
                                                            <td class="px-4 py-2.5">
                                                                <?php if ($p['images_count'] > 0): ?>
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                                                            <i class="fas fa-images text-indigo-500"></i> <?php echo $p['images_count']; ?>
                                                                        </span>
                                                                <?php else: ?>
                                                                        <span class="text-slate-400">0</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="px-4 py-2.5">
                                                                <?php if (!empty($p['pos']['found'])): ?>
                                                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg badge-pos text-[10px] font-semibold">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            <span>MRP: ₹<?php echo number_format($p['pos']['mrp']); ?></span>
                                                                            <span class="text-emerald-300">•</span>
                                                                            <span>Rent: ₹<?php echo number_format($p['pos']['rental_price']); ?></span>
                                                                            <span class="text-emerald-300">•</span>
                                                                            <span>Dep: ₹<?php echo number_format($p['pos']['deposit']); ?></span>
                                                                            <?php if (!empty($p['pos']['pos_category'])): ?>
                                                                                    <span class="text-emerald-700/70 font-sans">(<?php echo htmlspecialchars($p['pos']['pos_category']); ?>)</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                <?php else: ?>
                                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg badge-no-pos text-[10px] font-medium">
                                                                            <i class="fas fa-exclamation-circle text-amber-500"></i> Not in POS
                                                                        </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-right status-col">
                                                                <?php if ($p['pre_status'] === 'exists'): ?>
                                                                        <span class="inline-flex items-center gap-1 text-amber-600 font-bold">
                                                                            <i class="fas fa-forward"></i> <?php echo $p['pre_message']; ?>
                                                                        </span>
                                                                <?php else: ?>
                                                                        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                                                                            <i class="fas fa-check"></i> <?php echo $p['pre_message']; ?>
                                                                        </span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <script>
                                const allProductsData = <?php echo json_encode($scanResult['products']); ?>;
                                const activeCategoryId = <?php echo (int) $selectedCatId; ?>;
                                const activeSubcatId = <?php echo (int) $selectedSubcatId; ?>;
                                const activeCategoryName = <?php echo json_encode($selectedCatName); ?>;
                                const activeProductType = <?php echo json_encode($selectedType); ?>;

                                const startNewOnlyBtn = document.getElementById('start_new_only_btn');
                                const startAllBtn = document.getElementById('start_all_btn');
                                const downloadReportBtn = document.getElementById('download_report_btn');
                                const progressBar = document.getElementById('progress_bar');
                                const progressPct = document.getElementById('progress_pct');
                                const progressStatus = document.getElementById('progress_status');
                            
                                const cntCreated = document.getElementById('cnt_created');
                                const cntSkipped = document.getElementById('cnt_skipped');
                                const cntError = document.getElementById('cnt_error');

                                let fullResults = [['#', 'SKU', 'Name', 'Category', 'Status', 'MRP', 'Rent', 'Deposit', 'Images', 'Message']];

                                function filterTable(type) {
                                    ['all', 'ready', 'exists'].forEach(t => {
                                        const tab = document.getElementById('tab_' + t);
                                        if (tab) {
                                            if (t === type) {
                                                tab.className = "px-3 py-1.5 rounded-lg font-bold bg-white text-slate-900 shadow-sm transition-all";
                                            } else {
                                                tab.className = "px-3 py-1.5 rounded-lg font-bold text-slate-600 hover:text-slate-900 transition-all";
                                            }
                                        }
                                    });

                                    const rows = document.querySelectorAll('.product-row');
                                    rows.forEach(r => {
                                        if (type === 'all' || r.getAttribute('data-prestatus') === type) {
                                            r.classList.remove('hidden');
                                        } else {
                                            r.classList.add('hidden');
                                        }
                                    });
                                }

                                async function runImport(itemsToProcess) {
                                    if (!confirm(`Are you sure you want to proceed with importing ${itemsToProcess.length} items into "${activeCategoryName || 'Selected Category'}"?`)) {
                                        return;
                                    }

                                    startNewOnlyBtn.disabled = true;
                                    startAllBtn.disabled = true;
                                    startNewOnlyBtn.classList.add('opacity-50', 'cursor-not-allowed');
                                    startAllBtn.classList.add('opacity-50', 'cursor-not-allowed');

                                    let created = 0, skipped = 0, errors = 0;

                                    for (let i = 0; i < itemsToProcess.length; i++) {
                                        const item = itemsToProcess[i];
                                        const rowEl = document.querySelector(`.product-row[data-sku="${item.sku}"]`);
                                        const statusCol = rowEl ? rowEl.querySelector('.status-col') : null;

                                        if (statusCol) {
                                            statusCol.innerHTML = `<span class="text-slate-900 animate-pulse font-bold"><i class="fas fa-spinner fa-spin mr-1"></i>Saving</span>`;
                                        }
                                        if (rowEl) rowEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                                        // Build payload with selected Category & POS prices
                                        const payload = {
                                            sku: item.sku,
                                            sku_folder_path: item.sku_folder_path,
                                            name: item.name || ((activeCategoryName ? activeCategoryName + ' ' : '') + item.sku),
                                            type: activeProductType,
                                            category_id: activeCategoryId,
                                            subcat_id: activeSubcatId,
                                            category_name: activeCategoryName,
                                            s_price: (item.pos && item.pos.selling_price) ? item.pos.selling_price : (item.s_price || 0),
                                            rental_price: (item.pos && item.pos.rental_price) ? item.pos.rental_price : (item.rental_price || 0),
                                            deposit: (item.pos && item.pos.deposit) ? item.pos.deposit : (item.deposit || 0),
                                            description: item.description || ''
                                        };

                                        let success = false;
                                        let retries = 0;

                                        while (!success && retries < 2) {
                                            try {
                                                const res = await fetch('import_archive.php?action=process_row', {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json' },
                                                    body: JSON.stringify(payload)
                                                });
                                                const data = await res.json();
                                                success = true;

                                                if (data.status === 'success') {
                                                    created++;
                                                    cntCreated.textContent = created;
                                                    if (statusCol) {
                                                        statusCol.innerHTML = `<span class="text-emerald-600 font-bold"><i class="fas fa-check-circle mr-1"></i>Created (${data.images_count || 0} imgs)</span>`;
                                                    }
                                                    fullResults.push([i+1, item.sku, payload.name, activeCategoryName, 'Created', data.mrp || 0, data.rent_price || 0, data.deposit || 0, data.images_count || 0, data.message || '']);
                                                } else if (data.status === 'skipped') {
                                                    skipped++;
                                                    cntSkipped.textContent = skipped;
                                                    if (statusCol) {
                                                        statusCol.innerHTML = `<div><span class="text-amber-600 font-bold"><i class="fas fa-forward mr-1"></i>Skipped</span><div class="text-[9px] text-amber-500 mt-0.5">Already in DB</div></div>`;
                                                    }
                                                    fullResults.push([i+1, item.sku, payload.name, activeCategoryName, 'Skipped', 0, 0, 0, 0, data.message || 'Already exists']);
                                                } else {
                                                    errors++;
                                                    cntError.textContent = errors;
                                                    if (statusCol) {
                                                        statusCol.innerHTML = `<div><span class="text-rose-600 font-bold"><i class="fas fa-times-circle mr-1"></i>Failed</span><div class="text-[9px] text-rose-500 mt-0.5 max-w-[280px] truncate" title="${data.message}">${data.message}</div></div>`;
                                                    }
                                                    fullResults.push([i+1, item.sku, payload.name, activeCategoryName, 'Error', 0, 0, 0, 0, data.message || 'Unknown error']);
                                                }
                                            } catch (err) {
                                                retries++;
                                                if (retries >= 2) {
                                                    errors++;
                                                    cntError.textContent = errors;
                                                    if (statusCol) {
                                                        statusCol.innerHTML = `<div><span class="text-rose-600 font-bold"><i class="fas fa-times-circle mr-1"></i>Error</span><div class="text-[9px] text-rose-500 mt-0.5">${err.message}</div></div>`;
                                                    }
                                                    fullResults.push([i+1, item.sku, payload.name, activeCategoryName, 'Error', 0, 0, 0, 0, err.message]);
                                                } else {
                                                    await new Promise(r => setTimeout(r, 500));
                                                }
                                            }
                                        }

                                        // Throttling to prevent connection spikes
                                        await new Promise(r => setTimeout(r, 60));

                                        const pct = Math.round(((i + 1) / itemsToProcess.length) * 100);
                                        progressBar.style.width = pct + '%';
                                        progressPct.textContent = pct + '%';
                                        progressStatus.textContent = `Processing ${i + 1} of ${itemsToProcess.length} (${item.sku})...`;
                                    }

                                    progressStatus.textContent = `Completed! Created: ${created}, Skipped: ${skipped}, Errors: ${errors}`;
                                    startNewOnlyBtn.textContent = 'Completed';
                                    startNewOnlyBtn.className = 'px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold cursor-default shadow-sm';
                                    downloadReportBtn.classList.remove('hidden');
                                }

                                startNewOnlyBtn.addEventListener('click', function() {
                                    const newItems = allProductsData.filter(p => p.pre_status === 'ready');
                                    if (newItems.length === 0) {
                                        alert('All products in this folder already exist in the database!');
                                        return;
                                    }
                                    runImport(newItems);
                                });

                                startAllBtn.addEventListener('click', function() {
                                    runImport(allProductsData);
                                });

                                downloadReportBtn.addEventListener('click', function() {
                                    const csvContent = fullResults.map(r => r.map(c => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
                                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                                const link = document.createElement('a');
                                link.href = URL.createObjectURL(blob);
                                link.download = `Server_Import_Report_${new Date().toISOString().slice(0,10)}.csv`;
                                link.click();
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts partial -->
    <?php include __DIR__ . '/Views/partials/scripts.php'; ?>

    <script>
        // Custom folder input sync
        const folderSelect = document.getElementById('folderSelect');
        const customInputWrapper = document.getElementById('customInputWrapper');
        const customPathManual = document.getElementById('customPathManual');

        if (folderSelect && customInputWrapper) {
            folderSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customInputWrapper.classList.remove('hidden');
                    if (customPathManual) customPathManual.focus();
                } else {
                    customInputWrapper.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
