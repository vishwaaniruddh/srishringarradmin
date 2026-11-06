<?php
namespace Controllers\Products;

use Models\ProductModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait ProductImportExportTrait {
    public function downloadTemplate() {
        if (ob_get_level()) ob_end_clean();
        
        $csv = "sku,name,description,type,categories,sub_categories,rental_price,s_price,deposit,colors,size_avail,brand_name\n";
        $csv .= '"JW101","Bridal Kundan Necklace Set","Exquisite bridal set with matching earrings","jewellery","1, 2","1, 14","2500","8000","3000","Gold, Maroon","Free Size","Sri Shringaar"' . "\n";
        $csv .= '"GM202","Royal Red Velvet Bridal Lehenga","Intricate zardozi embroidery designer lehenga","garments","10, 22","","4500","18000","5000","Red, Golden","M, L, XL","Sri Shringaar"' . "\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="product_import_template.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
        exit;
    }

    public function downloadSampleZip() {
        if (ob_get_level()) ob_end_clean();

        $zipFile = tempnam(sys_get_temp_dir(), 'sample_import_');
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            die("Cannot create zip archive");
        }

        // 1. Create Sample Excel spreadsheet with PhpSpreadsheet if available
        $excelSaved = false;
        $excelTemp = tempnam(sys_get_temp_dir(), 'excel_');

        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            try {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Products');

                $headers = ['sku', 'name', 'description', 'type', 'categories', 'sub_categories', 'rental_price', 's_price', 'deposit', 'colors', 'size_avail', 'brand_name'];
                $sheet->fromArray([$headers], NULL, 'A1');

                $sampleRows = [
                    ['JW101', 'Bridal Kundan Necklace Set', 'Exquisite bridal set with matching earrings and maang tikka', 'jewellery', '1, 2', '1, 14', 2500, 8000, 3000, 'Gold, Maroon', 'Free Size', 'Sri Shringaar'],
                    ['JW102', 'Antique Gold Plated Choker', 'Traditional temple choker necklace with pearls', 'jewellery', '1', '1', 1800, 6500, 2000, 'Gold', 'Free Size', 'Sri Shringaar'],
                    ['GM202', 'Royal Red Velvet Bridal Lehenga', 'Intricate zardozi embroidery designer bridal lehenga with dupatta', 'garments', '10, 22', '', 4500, 18000, 5000, 'Red, Golden', 'M, L, XL', 'Sri Shringaar']
                ];
                $sheet->fromArray($sampleRows, NULL, 'A2');

                // Style Header
                $headerRange = 'A1:L1';
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($excelTemp);
                $zip->addFile($excelTemp, 'products_template.xlsx');
                $excelSaved = true;
            } catch (\Throwable $t) {
                error_log("Spreadsheet generation failed: " . $t->getMessage());
            }
        }

        if (!$excelSaved) {
            $csv = "sku,name,description,type,categories,sub_categories,rental_price,s_price,deposit,colors,size_avail,brand_name\n";
            $csv .= '"JW101","Bridal Kundan Necklace Set","Exquisite bridal set with matching earrings","jewellery","1, 2","1, 14","2500","8000","3000","Gold, Maroon","Free Size","Sri Shringaar"' . "\n";
            $csv .= '"GM202","Royal Red Velvet Bridal Lehenga","Intricate zardozi embroidery designer lehenga","garments","10, 22","","4500","18000","5000","Red, Golden","M, L, XL","Sri Shringaar"' . "\n";
            $zip->addFromString('products_template.csv', $csv);
        }

        // 2. Add sample SKU folders with sample images (1x1 PNG bytes as lightweight placeholder)
        $sampleImgBytes = '';
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(1, 1);
            if ($im) {
                ob_start();
                imagepng($im);
                $sampleImgBytes = ob_get_clean();
                imagedestroy($im);
            }
        }
        $zip->addEmptyDir('JW101');
        $zip->addFromString('JW101/front_view.jpg', $sampleImgBytes);
        $zip->addFromString('JW101/detail_view.jpg', $sampleImgBytes);

        $zip->addEmptyDir('JW102');
        $zip->addFromString('JW102/main_choker.jpg', $sampleImgBytes);

        $zip->addEmptyDir('GM202');
        $zip->addFromString('GM202/full_lehenga.jpg', $sampleImgBytes);
        $zip->addFromString('GM202/embroidery_zoom.jpg', $sampleImgBytes);

        // 3. Add Instructions Readme
        $readme = "=======================================================\r\n";
        $readme .= " SRI SHRINGAAR - BULK PRODUCT UPLOAD INSTRUCTIONS\r\n";
        $readme .= "=======================================================\r\n\r\n";
        $readme .= "1. SPREADSHEET (products_template.xlsx or .csv):\r\n";
        $readme .= "   - Fill in your product details in the Excel file.\r\n";
        $readme .= "   - 'sku' and 'name' are required columns.\r\n";
        $readme .= "   - You do NOT need any image URL column in the spreadsheet.\r\n\r\n";
        $readme .= "2. PRODUCT IMAGES:\r\n";
        $readme .= "   - Create a folder named exactly after each product SKU (e.g. 'JW101', 'GM202').\r\n";
        $readme .= "   - Put that product's photo(s) inside its folder (.jpg, .png, .webp supported).\r\n";
        $readme .= "   - You can put 1 or multiple images in each SKU folder.\r\n\r\n";
        $readme .= "3. ZIP AND UPLOAD:\r\n";
        $readme .= "   - Select the Excel file and your SKU folders, then compress them into a .zip file.\r\n";
        $readme .= "   - Upload the .zip archive on the admin Import page.\r\n";
        $readme .= "=======================================================\r\n";
        $zip->addFromString('README_INSTRUCTIONS.txt', $readme);

        $zip->close();
        if (file_exists($excelTemp)) @unlink($excelTemp);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="sample_product_import_package.zip"');
        header('Content-Length: ' . filesize($zipFile));
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($zipFile);
        @unlink($zipFile);
        exit;
    }

    /**
     * Dedicated Product Export & Import Batches History Page
     */
    public function exportPage() {
        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        // 1. Fetch historical import dates with breakdown
        $historySql = "
            SELECT 
                DATE(date_added) as import_date,
                COUNT(CASE WHEN type = 'jewellery' THEN 1 END) as jewellery_count,
                COUNT(CASE WHEN type = 'garment' THEN 1 END) as garment_count,
                COUNT(*) as total_count
            FROM (
                SELECT product_id as id, 'jewellery' as type, date_added FROM product WHERE date_added IS NOT NULL AND date_added != '0000-00-00 00:00:00'
                UNION ALL
                SELECT gproduct_id as id, 'garment' as type, date_added FROM garment_product WHERE date_added IS NOT NULL AND date_added != '0000-00-00 00:00:00'
            ) as combined
            GROUP BY DATE(date_added)
            ORDER BY import_date DESC
        ";
        $historyRes = mysqli_query($db, $historySql);
        $importHistory = [];
        $totalImportedLifetime = 0;
        if ($historyRes) {
            while ($row = mysqli_fetch_assoc($historyRes)) {
                $importHistory[] = $row;
                $totalImportedLifetime += (int)$row['total_count'];
            }
        }

        // 2. Active filters from GET
        $filterMode = trim($_GET['mode'] ?? 'single'); // 'single', 'range', 'all'
        $singleDate = trim($_GET['date'] ?? ($_GET['upload_date'] ?? ''));
        $startDate = trim($_GET['start_date'] ?? ($_GET['date_from'] ?? ''));
        $endDate = trim($_GET['end_date'] ?? ($_GET['date_to'] ?? ''));
        $productType = trim($_GET['type'] ?? 'all'); // 'all', 'jewellery', 'garments'
        $search = trim($_GET['search'] ?? '');

        // If 'saturday' keyword was passed
        if ($singleDate === 'saturday') {
            $singleDate = '2026-10-10';
        }

        // Default singleDate to most recent batch if no filters provided
        if (empty($singleDate) && empty($startDate) && empty($endDate) && $filterMode === 'single' && !empty($importHistory)) {
            $singleDate = $importHistory[0]['import_date'];
        }

        // Build SQL conditions
        $jewelleryConditions = [];
        $garmentConditions = [];

        if (!empty($search)) {
            $searchEsc = mysqli_real_escape_string($db, $search);
            $jewelleryConditions[] = "(p.product_code LIKE '%$searchEsc%' OR p.product_name LIKE '%$searchEsc%')";
            $garmentConditions[] = "(g.gproduct_code LIKE '%$searchEsc%' OR g.gproduct_name LIKE '%$searchEsc%')";
        }

        if ($filterMode === 'single' && !empty($singleDate)) {
            $dateEsc = mysqli_real_escape_string($db, $singleDate);
            $jewelleryConditions[] = "DATE(p.date_added) = '$dateEsc'";
            $garmentConditions[] = "DATE(g.date_added) = '$dateEsc'";
        } elseif ($filterMode === 'range') {
            if (!empty($startDate) && !empty($endDate)) {
                $startEsc = mysqli_real_escape_string($db, $startDate);
                $endEsc = mysqli_real_escape_string($db, $endDate);
                $jewelleryConditions[] = "DATE(p.date_added) BETWEEN '$startEsc' AND '$endEsc'";
                $garmentConditions[] = "DATE(g.date_added) BETWEEN '$startEsc' AND '$endEsc'";
            } elseif (!empty($startDate)) {
                $startEsc = mysqli_real_escape_string($db, $startDate);
                $jewelleryConditions[] = "DATE(p.date_added) >= '$startEsc'";
                $garmentConditions[] = "DATE(g.date_added) >= '$startEsc'";
            } elseif (!empty($endDate)) {
                $endEsc = mysqli_real_escape_string($db, $endDate);
                $jewelleryConditions[] = "DATE(p.date_added) <= '$endEsc'";
                $garmentConditions[] = "DATE(g.date_added) <= '$endEsc'";
            }
        }

        $jWhere = !empty($jewelleryConditions) ? " WHERE " . implode(" AND ", $jewelleryConditions) : "";
        $gWhere = !empty($garmentConditions) ? " WHERE " . implode(" AND ", $garmentConditions) : "";

        // Query preview & total counts
        $previewProducts = [];
        $totalFilteredCount = 0;
        $jewelleryFilteredCount = 0;
        $garmentFilteredCount = 0;

        $unionQueries = [];
        if ($productType === 'all' || $productType === 'jewellery') {
            $unionQueries[] = "
                SELECT 
                    p.product_id as id,
                    p.product_code as code,
                    'jewellery' as type,
                    p.product_name as name,
                    p.rent_price,
                    p.sales_price,
                    p.deposit,
                    p.date_added,
                    p.product_image as main_image,
                    COALESCE(s.name, j.categories_name, 'Jewellery') as category_name
                FROM product p
                LEFT JOIN subcat1 s ON p.subcat_id = s.subcat_id
                LEFT JOIN jewel_subcat j ON p.categories_id = j.subcat_id
                $jWhere
            ";
        }

        if ($productType === 'all' || $productType === 'garments') {
            $unionQueries[] = "
                SELECT 
                    g.gproduct_id as id,
                    g.gproduct_code as code,
                    'garments' as type,
                    g.gproduct_name as name,
                    g.rent_price,
                    g.sales_price,
                    g.deposit,
                    g.date_added,
                    g.gproduct_image as main_image,
                    COALESCE(gm.name, 'Apparel') as category_name
                FROM garment_product g
                LEFT JOIN garments gm ON g.garment_id = gm.garment_id
                $gWhere
            ";
        }

        if (!empty($unionQueries)) {
            $combinedQuery = implode(" UNION ALL ", $unionQueries);
            
            // Total count query
            $countQuery = "SELECT COUNT(*) as total, 
                                  COUNT(CASE WHEN type = 'jewellery' THEN 1 END) as j_count,
                                  COUNT(CASE WHEN type = 'garments' THEN 1 END) as g_count
                           FROM ($combinedQuery) as t";
            $countRes = mysqli_query($db, $countQuery);
            if ($countRes && $cRow = mysqli_fetch_assoc($countRes)) {
                $totalFilteredCount = (int)$cRow['total'];
                $jewelleryFilteredCount = (int)$cRow['j_count'];
                $garmentFilteredCount = (int)$cRow['g_count'];
            }

            // Preview items limit 60
            $previewQuery = "$combinedQuery ORDER BY date_added DESC, id DESC LIMIT 60";
            $pRes = mysqli_query($db, $previewQuery);
            if ($pRes) {
                while ($pRow = mysqli_fetch_assoc($pRes)) {
                    $previewProducts[] = $pRow;
                }
            }
        }

        $this->view('products/export', [
            'importHistory' => $importHistory,
            'totalImportedLifetime' => $totalImportedLifetime,
            'filterMode' => $filterMode,
            'singleDate' => $singleDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productType' => $productType,
            'search' => $search,
            'previewProducts' => $previewProducts,
            'totalFilteredCount' => $totalFilteredCount,
            'jewelleryFilteredCount' => $jewelleryFilteredCount,
            'garmentFilteredCount' => $garmentFilteredCount
        ]);
    }

    public function export() {
        while (ob_get_level()) ob_end_clean(); // Clear any buffers
        
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        // Get filters from URL
        $search = $_GET['search'] ?? '';
        $category_param = $_GET['category'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'id';
        $sortOrder = strtoupper($_GET['sort_order'] ?? 'DESC');
        $availableOnly = isset($_GET['available_only']) && ($_GET['available_only'] == 1 || $_GET['available_only'] == 'true');
        $productType = strtolower(trim($_GET['type'] ?? 'all'));

        $allowed_sort_by = [
            'id' => 'id',
            'name' => 'name',
            'code' => 'code',
            'rent_price' => 'db_rent_price',
            'sales_price' => 'original_sales_price',
            'featured' => 'featured',
            'date_added' => 'date_added'
        ];
        
        $order_clause = "id DESC";
        if (array_key_exists($sortBy, $allowed_sort_by)) {
            $column = $allowed_sort_by[$sortBy];
            if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
                $sortOrder = 'DESC';
            }
            $order_clause = "$column $sortOrder";
        }

        $jewellery_search = '';
        $garments_search = '';
        
        if (!empty($search)) {
            $search_safe = mysqli_real_escape_string($db, $search);
            $jewellery_search .= " AND (product_name LIKE '%$search_safe%' OR product_code LIKE '%$search_safe%')";
            $garments_search .= " AND (gproduct_name LIKE '%$search_safe%' OR gproduct_code LIKE '%$search_safe%')";
        }

        // Product type filtering
        if ($productType === 'jewellery') {
            $garments_search .= " AND 1=0";
        } elseif ($productType === 'garments' || $productType === 'garment') {
            $jewellery_search .= " AND 1=0";
        }

        if (!empty($category_param)) {
            if (strpos($category_param, ':') !== false) {
                list($type, $id) = explode(':', $category_param);
                $id = (int)$id;
                
                if ($type === 'garment') {
                    $garments_search .= " AND (garment_id = $id OR product_for = $id)";
                    $jewellery_search .= " AND 1=0";
                } elseif ($type === 'jewel_parent') {
                    $jewellery_search .= " AND categories_id = $id";
                    $garments_search .= " AND 1=0";
                } elseif ($type === 'jewel_child') {
                    $jewellery_search .= " AND subcat_id = $id";
                    $garments_search .= " AND 1=0";
                }
            }
        }
        if ($availableOnly) {
            $available_skus = [];
            $db3 = \Core\Database::getConnection('con3');
            if ($db3) {
                $res = mysqli_query($db3, "SELECT name FROM phppos_items WHERE quantity > 0");
                while ($row = mysqli_fetch_assoc($res)) {
                    if (!empty($row['name'])) {
                        $available_skus[] = mysqli_real_escape_string($db, $row['name']);
                    }
                }
            }
            if (!empty($available_skus)) {
                $sku_list = "'" . implode("','", $available_skus) . "'";
                $jewellery_search .= " AND product_code IN ($sku_list)";
                $garments_search .= " AND gproduct_code IN ($sku_list)";
            } else {
                $jewellery_search .= " AND 1=0";
                $garments_search .= " AND 1=0";
            }
        }

        $storePresence = $_GET['store_presence'] ?? '';
        if ($storePresence === 'in_child' || $storePresence === 'both') {
            $childSkus = \Core\ProductSyncService::getChildSkus();
            if (!empty($childSkus)) {
                $escaped = array_map(function($s) use ($db) { return "'" . mysqli_real_escape_string($db, $s) . "'"; }, $childSkus);
                $sku_list = implode(',', $escaped);
                $jewellery_search .= " AND product_code IN ($sku_list)";
                $garments_search .= " AND gproduct_code IN ($sku_list)";
            } else {
                $jewellery_search .= " AND 1=0";
                $garments_search .= " AND 1=0";
            }
        } elseif ($storePresence === 'parent_only' || $storePresence === 'not_in_child') {
            $childSkus = \Core\ProductSyncService::getChildSkus();
            if (!empty($childSkus)) {
                $escaped = array_map(function($s) use ($db) { return "'" . mysqli_real_escape_string($db, $s) . "'"; }, $childSkus);
                $sku_list = implode(',', $escaped);
                $jewellery_search .= " AND product_code NOT IN ($sku_list)";
                $garments_search .= " AND gproduct_code NOT IN ($sku_list)";
            }
        }

        // Apply Upload Date / Date Range filter
        $upload_date = $_GET['upload_date'] ?? ($_GET['filter'] ?? ($_GET['date'] ?? ''));
        $start_date = trim($_GET['start_date'] ?? ($_GET['date_from'] ?? ''));
        $end_date = trim($_GET['end_date'] ?? ($_GET['date_to'] ?? ''));
        $mode = trim($_GET['mode'] ?? 'single');

        if ($upload_date === 'saturday') {
            $upload_date = '2026-10-10';
        }

        if ($mode === 'range' || (!empty($start_date) && !empty($end_date))) {
            if (!empty($start_date) && !empty($end_date)) {
                $escStart = mysqli_real_escape_string($db, $start_date);
                $escEnd = mysqli_real_escape_string($db, $end_date);
                $jewellery_search .= " AND DATE(date_added) BETWEEN '$escStart' AND '$escEnd'";
                $garments_search .= " AND DATE(date_added) BETWEEN '$escStart' AND '$escEnd'";
            } elseif (!empty($start_date)) {
                $escStart = mysqli_real_escape_string($db, $start_date);
                $jewellery_search .= " AND DATE(date_added) >= '$escStart'";
                $garments_search .= " AND DATE(date_added) >= '$escStart'";
            } elseif (!empty($end_date)) {
                $escEnd = mysqli_real_escape_string($db, $end_date);
                $jewellery_search .= " AND DATE(date_added) <= '$escEnd'";
                $garments_search .= " AND DATE(date_added) <= '$escEnd'";
            }
        } elseif (!empty($upload_date)) {
            $escDate = mysqli_real_escape_string($db, $upload_date);
            $jewellery_search .= " AND DATE(date_added) = '$escDate'";
            $garments_search .= " AND DATE(date_added) = '$escDate'";
        }

        // Apply Featured filter
        $featured = $_GET['featured'] ?? '';
        if ($featured !== '') {
            $featured = (int)$featured;
            $jewellery_search .= " AND featured = $featured";
            $garments_search .= " AND featured = $featured";
        }

        $query = "(SELECT product_id as id, product_code as code, 'jewellery' as type, product_name as name, rent_price as db_rent_price, sales_price as original_sales_price, date_added, featured FROM product WHERE 1=1 $jewellery_search)
                  UNION ALL
                  (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type, gproduct_name as name, rent_price as db_rent_price, sales_price as original_sales_price, date_added, featured FROM garment_product WHERE 1=1 $garments_search)
                  ORDER BY $order_clause";
                  
        $result = $productModel->query($db, $query);
        
        if (!$result) {
            die("SQL Error: " . mysqli_error($db));
        }

        $format = strtolower($_GET['format'] ?? 'csv');
        $dateSuffix = '';
        if (!empty($start_date) && !empty($end_date)) {
            $dateSuffix = $start_date . '_to_' . $end_date . '_';
        } elseif (!empty($upload_date)) {
            $dateSuffix = $upload_date . '_';
        }
        $filename = 'imported_products_export_' . $dateSuffix . date('Ymd_His');
        $childSkusLookup = array_flip(array_map('strtoupper', \Core\ProductSyncService::getChildSkus()));

        if ($format === 'excel' || $format === 'xlsx') {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Products');

            $headers = ['SKU', 'Name', 'Description', 'Type', 'Category', 'Sub Category', 'Sales Price', 'Rental Price', 'Deposit', 'Quantity', 'Store Presence', 'Import / Added Date', 'Images'];
            foreach ($headers as $colIdx => $header) {
                $sheet->setCellValue([$colIdx + 1, 1], $header);
            }
            $sheet->getStyle('A1:M1')->getFont()->setBold(true);

            $rowNum = 2;
            while ($p = mysqli_fetch_assoc($result)) {
                $fullProduct = $productModel->getProductById($p['id'], $p['type']);
                if (!$fullProduct) continue;

                $sku = $fullProduct['code'] ?? $p['code'] ?? '';
                $qty = $productModel->getPosQuantity($sku);
                $presence = isset($childSkusLookup[strtoupper(trim($sku))]) ? 'Both Stores' : 'Parent Only';

                $images = $productModel->getProductImages($p['id'], $p['type']);
                $imageUrls = [];
                if ($images) {
                    foreach ($images as $img) {
                        $imageUrls[] = "https://srishringarr.com/yn/uploads" . $img['img_name'];
                    }
                }

                $sheet->setCellValue([1, $rowNum], $sku);
                $sheet->setCellValue([2, $rowNum], $fullProduct['name'] ?? $p['name'] ?? '');
                $sheet->setCellValue([3, $rowNum], strip_tags($fullProduct['description'] ?? ''));
                $sheet->setCellValue([4, $rowNum], ucfirst($p['type'] ?? ''));
                $sheet->setCellValue([5, $rowNum], $fullProduct['category'] ?? '');
                $sheet->setCellValue([6, $rowNum], $fullProduct['sub_category'] ?? '');
                $sheet->setCellValue([7, $rowNum], (float)($fullProduct['s_price'] ?? 0));
                $sheet->setCellValue([8, $rowNum], (float)($fullProduct['rental_price'] ?? 0));
                $sheet->setCellValue([9, $rowNum], (float)($fullProduct['deposit'] ?? 0));
                $sheet->setCellValue([10, $rowNum], (int)$qty);
                $sheet->setCellValue([11, $rowNum], $presence);
                $sheet->setCellValue([12, $rowNum], $p['date_added'] ?? '');
                $sheet->setCellValue([13, $rowNum], implode(', ', $imageUrls));
                $rowNum++;
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }

        // CSV Export
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM so Excel opens it with proper encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        @fputcsv($output, ['sku', 'name', 'description', 'type', 'category', 'sub_category', 's_price', 'rental_price', 'deposit', 'qty', 'store_presence', 'import_date', 'images'], ',', '"', '\\');

        $i = 0;
        while ($p = mysqli_fetch_assoc($result)) {
            $fullProduct = $productModel->getProductById($p['id'], $p['type']);
            if (!$fullProduct) continue;

            $sku = $fullProduct['code'] ?? $p['code'] ?? '';
            $qty = $productModel->getPosQuantity($sku);
            $presence = isset($childSkusLookup[strtoupper(trim($sku))]) ? 'Both Stores' : 'Parent Only';

            $images = $productModel->getProductImages($p['id'], $p['type']);
            $imageUrls = [];
            if ($images) {
                foreach ($images as $img) {
                    $imageUrls[] = "https://srishringarr.com/yn/uploads" . $img['img_name'];
                }
            }

            @fputcsv($output, [
                $sku,
                $fullProduct['name'] ?? $p['name'] ?? '',
                $fullProduct['description'] ?? '',
                $p['type'] ?? '',
                $fullProduct['category'] ?? '',
                $fullProduct['sub_category'] ?? '',
                $fullProduct['s_price'] ?? 0,
                $fullProduct['rental_price'] ?? 0,
                $fullProduct['deposit'] ?? 0,
                $qty,
                $presence,
                $p['date_added'] ?? '',
                implode(',', $imageUrls)
            ], ',', '"', '\\');
            
            $i++;
            if ($i % 50 == 0) {
                fflush($output);
                flush();
            }
        }
        fclose($output);
        exit;
    }

    public function import() {
        $productModel = new ProductModel();
        $categories = $productModel->getCategories();
        $jewelCategoriesTree = $productModel->getAllCategoriesWithSubcategories('jewellery');
        $garmentCategoriesTree = $productModel->getAllCategoriesWithSubcategories('garments');
        
        $this->view('products/import', [
            'categories' => $categories,
            'jewelCategoriesTree' => $jewelCategoriesTree,
            'garmentCategoriesTree' => $garmentCategoriesTree
        ]);
    }

    public function uploadImportPackage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        if (empty($_FILES['file']) && empty($_FILES['zip_file']) && empty($_FILES['package_file'])) {
            $this->json(['error' => 'No file uploaded. Please select a .zip or .xlsx/.csv file.'], 400);
            return;
        }

        $uploaded = $_FILES['file'] ?? $_FILES['zip_file'] ?? $_FILES['package_file'];
        if ($uploaded['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Upload error code: ' . $uploaded['error']], 400);
            return;
        }

        $originalName = $uploaded['name'];
        $tempFilePath = $uploaded['tmp_name'];
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/';
        if (!file_exists($tempImportsBase)) {
            mkdir($tempImportsBase, 0755, true);
        }

        $importToken = 'imp_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6));
        $extractDir = $tempImportsBase . $importToken . '/';
        if (!mkdir($extractDir, 0755, true)) {
            $this->json(['error' => 'Failed to create temporary extraction directory.'], 500);
            return;
        }

        $spreadsheetFile = null;

        if ($fileExt === 'zip') {
            if (!class_exists('\ZipArchive')) {
                $this->json(['error' => 'ZipArchive extension is not enabled in PHP.'], 500);
                return;
            }

            $zip = new \ZipArchive();
            if ($zip->open($tempFilePath) === true) {
                $zip->extractTo($extractDir);
                $zip->close();
            } else {
                $this->json(['error' => 'Failed to open and extract ZIP archive.'], 400);
                return;
            }
        } elseif (in_array($fileExt, ['xlsx', 'xls', 'csv'])) {
            $targetSpreadsheet = $extractDir . 'spreadsheet.' . $fileExt;
            if (!move_uploaded_file($tempFilePath, $targetSpreadsheet)) {
                $this->json(['error' => 'Failed to save uploaded spreadsheet.'], 500);
                return;
            }
            $spreadsheetFile = $targetSpreadsheet;
        } else {
            $this->json(['error' => 'Unsupported file type. Please upload a .zip, .xlsx, .xls, or .csv file.'], 400);
            return;
        }

        // Recursively index all files in $extractDir
        $skuFolderMap = []; // lowercase sku => array of relative file paths
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'bmp'];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $subPath = $iterator->getSubPathName();
            $subPath = str_replace('\\', '/', $subPath);
            
            // Ignore Mac OS metadata or temp lock files
            if (str_contains($subPath, '__MACOSX') || str_contains($subPath, '.~lock') || str_starts_with(basename($subPath), '~$')) {
                continue;
            }

            if ($item->isFile()) {
                $ext = strtolower(pathinfo($subPath, PATHINFO_EXTENSION));
                
                // If spreadsheet not yet identified, check if this is an Excel/CSV file
                if (!$spreadsheetFile && in_array($ext, ['xlsx', 'xls', 'csv'])) {
                    $spreadsheetFile = $item->getPathname();
                }

                // If image file, categorize under its parent folder (which represents SKU)
                if (in_array($ext, $imageExtensions)) {
                    $folderName = basename(dirname($subPath));
                    if ($folderName !== '.' && $folderName !== '' && $folderName !== basename($extractDir)) {
                        $skuKey = strtolower(trim($folderName));
                        if (!isset($skuFolderMap[$skuKey])) {
                            $skuFolderMap[$skuKey] = [];
                        }
                        $skuFolderMap[$skuKey][] = $subPath;
                    }
                }
            }
        }

        if (!$spreadsheetFile || !file_exists($spreadsheetFile)) {
            $this->json(['error' => 'No valid Excel (.xlsx / .xls) or CSV file found inside the uploaded package.'], 400);
            return;
        }

        // Parse spreadsheet rows
        $parsedProducts = [];
        $spreadsheetExt = strtolower(pathinfo($spreadsheetFile, PATHINFO_EXTENSION));

        if ($spreadsheetExt === 'csv') {
            $handle = fopen($spreadsheetFile, 'r');
            if ($handle) {
                $headers = [];
                $rowIdx = 0;
                while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    $rowIdx++;
                    if ($rowIdx === 1) {
                        $headers = array_map(function($h) {
                            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                        }, $row);
                        continue;
                    }
                    if (empty(array_filter($row))) continue;
                    $item = [];
                    foreach ($headers as $idx => $header) {
                        $item[$header] = isset($row[$idx]) ? trim((string)$row[$idx]) : '';
                    }
                    $parsedProducts[] = $item;
                }
                fclose($handle);
            }
        } else {
            // Read via PhpSpreadsheet
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheetFile);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);

                if (!empty($rows) && count($rows) > 1) {
                    $rawHeaders = $rows[0];
                    $headers = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                    }, $rawHeaders);

                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        if (empty(array_filter($row, function($v) { return $v !== null && $v !== ''; }))) continue;
                        $item = [];
                        foreach ($headers as $idx => $header) {
                            if (!empty($header)) {
                                $val = $row[$idx] ?? '';
                                $item[$header] = is_string($val) ? trim($val) : (string)$val;
                            }
                        }
                        $parsedProducts[] = $item;
                    }
                }
            } catch (\Throwable $e) {
                $this->json(['error' => 'Error reading spreadsheet: ' . $e->getMessage()], 400);
                return;
            }
        }

        // Match each product row with its SKU image folder
        $totalMatchedImages = 0;
        $matchedFolders = [];
        $validProducts = [];

        foreach ($parsedProducts as $prod) {
            $sku = trim($prod['sku'] ?? $prod['sku_code'] ?? $prod['product_code'] ?? $prod['code'] ?? '');
            $name = trim($prod['name'] ?? $prod['product_name'] ?? '');

            if (empty($sku) && empty($name)) continue;

            if (!empty($sku) && empty($prod['sku'])) {
                $prod['sku'] = $sku;
            }

            $skuKey = strtolower($sku);
            $matchedImages = [];

            if (!empty($skuKey) && isset($skuFolderMap[$skuKey])) {
                $matchedImages = $skuFolderMap[$skuKey];
                $matchedFolders[$skuKey] = true;
                $totalMatchedImages += count($matchedImages);
            }

            $prod['temp_images'] = $matchedImages;
            $prod['images_count'] = count($matchedImages);
            $validProducts[] = $prod;
        }

        $this->json([
            'status' => 'success',
            'import_token' => $importToken,
            'spreadsheet_name' => basename($spreadsheetFile),
            'total_products' => count($validProducts),
            'total_images' => $totalMatchedImages,
            'total_folders' => count($matchedFolders),
            'products' => $validProducts
        ]);
    }

    public function cleanupImportTemp() {
        $token = $_POST['import_token'] ?? $_GET['import_token'] ?? '';
        $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/';

        if (!empty($token)) {
            $safeToken = preg_replace('/[^a-zA-Z0-9_-]/', '', $token);
            $targetDir = $tempImportsBase . $safeToken;
            if (is_dir($targetDir)) {
                $this->rrmdir($targetDir);
            }
        }

        // Auto sweep folders older than 2 hours
        if (is_dir($tempImportsBase)) {
            $dirs = scandir($tempImportsBase);
            $now = time();
            foreach ($dirs as $d) {
                if ($d === '.' || $d === '..') continue;
                $path = $tempImportsBase . $d;
                if (is_dir($path) && ($now - filemtime($path) > 7200)) {
                    $this->rrmdir($path);
                }
            }
        }

        $this->json(['status' => 'success']);
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    } else {
                        @unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            @rmdir($dir);
        }
    }

    public function processImportRow() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        // Support both JSON (from modern dashboard) and Form Data (legacy)
        $input = json_decode(file_get_contents('php://input'), true);
        $data = !empty($input) ? $input : $_POST;
        
        $productModel = new ProductModel();
        $categoryModel = new \Models\CategoryModel();

        try {
            $type = strtolower(trim($data['type'] ?? ''));
            $code = trim($data['sku'] ?? $data['sku_code'] ?? $data['code'] ?? '');

            if (empty($code)) throw new \Exception("Missing SKU");

            // Auto-detect type if empty
            if (empty($type)) {
                $type = (stripos($code, 'GM') === 0 || stripos($code, 'LM') === 0 || stripos($code, 'FM') === 0) ? 'garments' : 'jewellery';
            } else if ($type === 'garment' || $type === 'apparel') {
                $type = 'garments';
            } else if ($type === 'jewelry' || $type === 'jewel') {
                $type = 'jewellery';
            }

            // Check if exists - Skip if already exists (do not update)
            $existingProduct = $productModel->checkProductExists($code, $type);
            if ($existingProduct) {
                return $this->json([
                    'status' => 'skipped',
                    'message' => "SKU $code already exists. Skipped (not modified)."
                ]);
            }
            
            // Process Images from extracted ZIP folder or from URLs / local paths
            $downloadedImages = [];
            $current_year = date('Y');
            $current_month = date('m');
            $upload_base = __DIR__ . "/../../yn/uploads/";
            $upload_path = $current_year . '/' . $current_month . '/';
            $full_upload_path = $upload_base . $upload_path;

            if (!file_exists($full_upload_path)) {
                mkdir($full_upload_path, 0755, true);
            }

            // 1. Check if temp_images array from ZIP import is present
            $tempImages = $data['temp_images'] ?? [];
            $importToken = !empty($data['import_token']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$data['import_token']) : '';

            if (!empty($importToken)) {
                $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/' . $importToken . '/';
                
                // If temp_images array was passed
                if (!empty($tempImages) && is_array($tempImages)) {
                    foreach ($tempImages as $relPath) {
                        $sourcePath = $tempImportsBase . ltrim((string)$relPath, '/\\');
                        if (file_exists($sourcePath) && is_file($sourcePath)) {
                            $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) ?: 'jpg';
                            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                            if (copy($sourcePath, $full_upload_path . $filename)) {
                                $downloadedImages[] = $upload_path . $filename;
                            }
                        }
                    }
                } else {
                    // Fallback: search folder named after SKU in temp directory
                    $skuDirs = [$tempImportsBase . $code, $tempImportsBase . strtolower($code), $tempImportsBase . strtoupper($code)];
                    foreach ($skuDirs as $sDir) {
                        if (file_exists($sDir) && is_dir($sDir)) {
                            $files = glob($sDir . '/*.{jpg,jpeg,png,webp,avif,gif,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
                            if (!empty($files)) {
                                foreach ($files as $f) {
                                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION)) ?: 'jpg';
                                    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                                    if (copy($f, $full_upload_path . $filename)) {
                                        $downloadedImages[] = $upload_path . $filename;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }

            // 2. Also support comma-separated images if supplied
            $rawImages = $data['images'] ?? $data['image'] ?? '';
            $imageUrls = !empty($rawImages) ? explode(',', (string)$rawImages) : [];
            
            foreach ($imageUrls as $url) {
                $url = trim($url);
                if (empty($url)) continue;

                // If already a local relative path, preserve it
                if (str_starts_with($url, '/') || str_starts_with($url, '202') || str_starts_with($url, 'uploads/')) {
                    $downloadedImages[] = ltrim(str_replace(['/yn/uploads/', 'yn/uploads/', '/uploads/'], '', $url), '/');
                    continue;
                }

                $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                
                $imgData = @file_get_contents($url);
                if ($imgData) {
                    if (file_put_contents($full_upload_path . $filename, $imgData)) {
                        $downloadedImages[] = $upload_path . $filename;
                    }
                }
            }

            // --- Multi-Category and Subcategory Parsing ---
            $rawMain = [];
            if (!empty($data['categories'])) {
                $rawMain = is_array($data['categories']) ? $data['categories'] : explode(',', (string)$data['categories']);
            }
            if (!empty($data['category_id'])) {
                $rawMain = array_merge($rawMain, is_array($data['category_id']) ? $data['category_id'] : explode(',', (string)$data['category_id']));
            }
            if (!empty($data['category'])) {
                $rawMain = array_merge($rawMain, is_array($data['category']) ? $data['category'] : explode(',', (string)$data['category']));
            }

            $rawSub = [];
            if (!empty($data['sub_categories'])) {
                $rawSub = is_array($data['sub_categories']) ? $data['sub_categories'] : explode(',', (string)$data['sub_categories']);
            }
            if (!empty($data['subcat_id'])) {
                $rawSub = array_merge($rawSub, is_array($data['subcat_id']) ? $data['subcat_id'] : explode(',', (string)$data['subcat_id']));
            }
            if (!empty($data['sub_category'])) {
                $rawSub = array_merge($rawSub, is_array($data['sub_category']) ? $data['sub_category'] : explode(',', (string)$data['sub_category']));
            }

            // Resolve Main Category IDs
            $mainCategoryIds = [];
            foreach ($rawMain as $item) {
                $item = trim((string)$item);
                if (empty($item)) continue;
                if (is_numeric($item) && (int)$item > 0) {
                    $mainCategoryIds[] = (int)$item;
                } else {
                    $foundId = $categoryModel->getCategoryIdByName($item, $type);
                    if ($foundId > 0) $mainCategoryIds[] = $foundId;
                }
            }
            $mainCategoryIds = array_values(array_unique(array_filter($mainCategoryIds)));

            // Resolve Subcategory IDs
            $subcategoryIds = [];
            foreach ($rawSub as $item) {
                $item = trim((string)$item);
                if (empty($item)) continue;
                if (is_numeric($item) && (int)$item > 0) {
                    $subcategoryIds[] = (int)$item;
                } else {
                    $foundId = $categoryModel->getCategoryIdByName($item, $type);
                    if ($foundId > 0) $subcategoryIds[] = $foundId;
                }
            }
            $subcategoryIds = array_values(array_unique(array_filter($subcategoryIds)));

            // Primary category & subcategory fallback
            $primaryCatId = !empty($mainCategoryIds) ? $mainCategoryIds[0] : 0;
            $primarySubId = !empty($subcategoryIds) ? $subcategoryIds[0] : 0;

            // Colors parsing
            $colorsInput = $data['colors'] ?? $data['brand_color'] ?? '';
            $colorsArray = [];
            if (!empty($colorsInput)) {
                if (is_array($colorsInput)) {
                    $colorsArray = $colorsInput;
                } else if (is_string($colorsInput)) {
                    $trimmedC = trim($colorsInput);
                    $decoded = json_decode($trimmedC, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $colorsArray = $decoded;
                    } else {
                        $colorsArray = array_filter(array_map('trim', explode(',', $trimmedC)));
                    }
                }
            }

            $saveData = [
                'code' => $code,
                'name' => $data['name'] ?? 'Imported Product',
                'description' => $data['description'] ?? '',
                'category' => $primaryCatId,
                'sub_category' => $primarySubId,
                'categories' => $mainCategoryIds,
                'sub_categories' => $subcategoryIds,
                's_price' => (float)($data['s_price'] ?? $data['sales_price'] ?? $data['sale_price'] ?? 0),
                'rental_price' => (float)($data['rental_price'] ?? $data['rent_price'] ?? 0),
                'deposit' => (float)($data['deposit'] ?? 0),
                'size_avail' => $data['size_avail'] ?? $data['size'] ?? '',
                'brand_name' => $data['brand_name'] ?? $data['brand'] ?? '',
                'colors' => $colorsArray,
                'brand_color' => $colorsArray,
                'price_source' => (!empty($data['price_source']) && strtolower($data['price_source']) === 'manual') ? 'manual' : 'pos',
                'availability' => in_array(strtolower($data['availability'] ?? ''), ['rent', 'sell', 'both']) ? strtolower($data['availability']) : 'both'
            ];

            $productModel->saveProduct($type, $saveData, $downloadedImages);
            return $this->json(['status' => 'success', 'message' => "Product $code imported successfully with categories and " . count($downloadedImages) . " images"]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


}
