<?php
namespace Controllers\Products;

use Models\ProductModel;

trait ProductBulkTrait {
    public function bulkDelete() {
        $this->view('products/bulk_delete');
    }

    public function downloadDeleteTemplate() {
        while (ob_get_level()) ob_end_clean();
        
        $csv = "sku\n";
        $csv .= "SKU123\n";
        $csv .= "SKU456\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="bulk_delete_template.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
        exit;
    }

    public function processBulkDelete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'No file uploaded or upload error'], 400);
        }

        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip header
        $header = @fgetcsv($handle, 0, ',', '"', '\\');
        if (!$header) {
             $this->json(['error' => 'Empty file'], 400);
        }
        $skuIndex = array_search('sku', array_map('strtolower', $header));

        if ($skuIndex === false) {
            $this->json(['error' => 'Invalid file format. Must have a "sku" column.'], 400);
        }

        $productModel = new ProductModel();
        $deletedCount = 0;
        $failedCount = 0;
        $skus = [];

        while (($row = @fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $sku = trim($row[$skuIndex]);
            if (empty($sku)) continue;
            
            if ($productModel->deleteBySku($sku)) {
                $deletedCount++;
                $skus[] = $sku;
            } else {
                $failedCount++;
            }
        }
        fclose($handle);

        $this->json([
            'success' => true,
            'message' => "Successfully deleted $deletedCount products. $failedCount failed or not found.",
            'deletedCount' => $deletedCount,
            'failedCount' => $failedCount,
            'skus' => $skus
        ]);
    }

    public function bulkUpdate() {
        $productModel = new ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $this->view('products/bulk_update', [
            'jewelCategories' => $jewelCategories,
            'garments' => $garments
        ]);
    }

    public function processBulkUpdate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $rawSkus = $input['skus'] ?? '';
        $priceSource = $input['price_source'] ?? 'no_change';
        $availability = $input['availability'] ?? 'no_change';
        
        $updateBrand = isset($input['update_brand']) && $input['update_brand'] === true;
        $brandName = $input['brand_name'] ?? '';
        
        $updateCategory = isset($input['update_category']) && $input['update_category'] === true;
        $categoryType = $input['category_type'] ?? ''; // 'jewellery' or 'garments'
        $categoryId = (int)($input['category_id'] ?? 0);
        $subCategoryId = (int)($input['subcategory_id'] ?? 0);

        // Parse SKUs (split by newline, comma, space)
        $skusList = preg_split('/[\n,\s]+/', $rawSkus);
        $skus = array_filter(array_map('trim', $skusList));

        if (empty($skus)) {
            $this->json(['error' => 'Please enter at least one SKU.'], 400);
            return;
        }

        // Verify if at least one change is selected
        if ($priceSource === 'no_change' && $availability === 'no_change' && !$updateBrand && !$updateCategory) {
            $this->json(['error' => 'No update actions selected. Please choose at least one setting to update.'], 400);
            return;
        }

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        $updatedCount = 0;
        $notFoundSkus = [];
        $errors = [];

        foreach ($skus as $sku) {
            $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
            $existsGarment = $productModel->checkProductExists($sku, 'garments');

            if (!$existsJewel && !$existsGarment) {
                $notFoundSkus[] = $sku;
                continue;
            }

            $success = false;

            if ($existsJewel) {
                $sets = [];
                $params = [];
                $types = "";

                if ($priceSource !== 'no_change') {
                    $sets[] = "price_source = ?";
                    $params[] = $priceSource;
                    $types .= "s";
                }
                if ($availability !== 'no_change') {
                    $sets[] = "availability = ?";
                    $params[] = $availability;
                    $types .= "s";
                }
                if ($updateBrand) {
                    $sets[] = "brand_name = ?";
                    $params[] = $brandName;
                    $types .= "s";
                }
                if ($updateCategory && $categoryType === 'jewellery') {
                    $sets[] = "categories_id = ?";
                    $params[] = $categoryId;
                    $types .= "i";
                    
                    $sets[] = "subcat_id = ?";
                    $params[] = $subCategoryId;
                    $types .= "i";
                }

                if (!empty($sets)) {
                    $setString = implode(', ', $sets);
                    $sql = "UPDATE product SET $setString WHERE product_code = ?";
                    $stmt = mysqli_prepare($db, $sql);
                    
                    if ($stmt) {
                        $bindParams = [];
                        $bindTypes = $types . "s";
                        $bindParams[] = &$bindTypes;
                        for ($j = 0; $j < count($params); $j++) {
                            $bindParams[] = &$params[$j];
                        }
                        $bindParams[] = &$sku;
                        
                        call_user_func_array([$stmt, 'bind_param'], $bindParams);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $success = true;
                        } else {
                            $errors[] = "SKU $sku (Jewellery): " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $errors[] = "SKU $sku (Jewellery): Failed to prepare query: " . mysqli_error($db);
                    }
                } else {
                    $success = true; // Nothing to change for Jewellery
                }
            }

            if ($existsGarment) {
                $sets = [];
                $params = [];
                $types = "";

                if ($priceSource !== 'no_change') {
                    $sets[] = "price_source = ?";
                    $params[] = $priceSource;
                    $types .= "s";
                }
                if ($availability !== 'no_change') {
                    $sets[] = "availability = ?";
                    $params[] = $availability;
                    $types .= "s";
                }
                if ($updateBrand) {
                    $sets[] = "brand_name = ?";
                    $params[] = $brandName;
                    $types .= "s";
                }
                if ($updateCategory && $categoryType === 'garments') {
                    $sets[] = "garment_id = ?";
                    $params[] = $categoryId;
                    $types .= "i";
                    
                    $sets[] = "product_for = ?";
                    $params[] = $subCategoryId;
                    $types .= "i";
                }

                if (!empty($sets)) {
                    $setString = implode(', ', $sets);
                    $sql = "UPDATE garment_product SET $setString WHERE gproduct_code = ?";
                    $stmt = mysqli_prepare($db, $sql);
                    
                    if ($stmt) {
                        $bindParams = [];
                        $bindTypes = $types . "s";
                        $bindParams[] = &$bindTypes;
                        for ($j = 0; $j < count($params); $j++) {
                            $bindParams[] = &$params[$j];
                        }
                        $bindParams[] = &$sku;
                        
                        call_user_func_array([$stmt, 'bind_param'], $bindParams);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $success = true;
                        } else {
                            $errors[] = "SKU $sku (Garments): " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $errors[] = "SKU $sku (Garments): Failed to prepare query: " . mysqli_error($db);
                    }
                } else {
                    $success = true; // Nothing to change for Garments
                }
            }

            if ($success) {
                $updatedCount++;
            }
        }

        $this->json([
            'success' => true,
            'message' => "Bulk update completed.",
            'updatedCount' => $updatedCount,
            'notFoundCount' => count($notFoundSkus),
            'notFoundSkus' => $notFoundSkus,
            'errors' => $errors
        ]);
    }

    public function processBulkPriceUpdate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $productModel = new ProductModel();
        $updates = [];
        $errors = [];
        $notFoundSkus = [];
        $updatedCount = 0;

        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $inputMode = $input['price_input_mode'] ?? 'paste';
            $priceData = $input['price_data'] ?? '';
        } else {
            $inputMode = $_POST['price_input_mode'] ?? 'paste';
            $priceData = $_POST['price_data'] ?? '';
        }

        if ($inputMode === 'paste') {
            if (empty($priceData)) {
                $this->json(['error' => 'Please paste some pricing data.'], 400);
                return;
            }

            $rawRows = preg_split('/\r\n|\r|\n/', $priceData);
            foreach ($rawRows as $row) {
                $row = trim($row);
                if (empty($row)) continue;

                // Split by tab, comma, or multiple spaces
                $cols = preg_split('/\t|,| {2,}/', $row);
                $cols = array_filter(array_map('trim', $cols));

                if (count($cols) < 4) {
                    // Try split by single space if columns weren't separated by tabs or commas
                    $cols = preg_split('/\s+/', $row);
                    if (count($cols) < 4) {
                        $errors[] = "Row invalid (must have 4 columns: SKU, MRP, Rent, Deposit): \"$row\"";
                        continue;
                    }
                }

                $sku = $cols[0];
                // Skip header row if present
                if (strtolower($sku) === 'sku' || strtolower($sku) === 'wid' || strtolower($sku) === 'code') {
                    continue;
                }

                $mrp = floatval(preg_replace('/[^\d.]/', '', $cols[1]));
                $rent = floatval(preg_replace('/[^\d.]/', '', $cols[2]));
                $deposit = floatval(preg_replace('/[^\d.]/', '', $cols[3]));

                $updates[] = [
                    'sku' => $sku,
                    'mrp' => $mrp,
                    'rent' => $rent,
                    'deposit' => $deposit
                ];
            }
        } elseif ($inputMode === 'file' || isset($_FILES['price_file'])) {
            if (!isset($_FILES['price_file']) || $_FILES['price_file']['error'] !== UPLOAD_ERR_OK) {
                $this->json(['error' => 'No file uploaded or upload error.'], 400);
                return;
            }

            $file = $_FILES['price_file']['tmp_name'];
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Find column indices
                $headerRow = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE)[0];

                $skuIdx = -1;
                $mrpIdx = -1;
                $rentIdx = -1;
                $depositIdx = -1;

                foreach ($headerRow as $idx => $header) {
                    if ($header === null) continue;
                    $header = strtolower(trim($header));
                    if (in_array($header, ['sku', 'wid', 'code', 'product_code', 'gproduct_code'])) {
                        $skuIdx = $idx;
                    } elseif (in_array($header, ['mrp', 'price', 'sales_price', 's_price', 'selling'])) {
                        $mrpIdx = $idx;
                    } elseif (in_array($header, ['rent', 'rental', 'rental+gst', 'rent_price', 'rental_price'])) {
                        $rentIdx = $idx;
                    } elseif (in_array($header, ['deposit', 'sd', 'security', 'security_deposit', 'deposite'])) {
                        $depositIdx = $idx;
                    }
                }

                if ($skuIdx === -1 || $mrpIdx === -1 || $rentIdx === -1 || $depositIdx === -1) {
                    $this->json(['error' => 'Could not detect column headers. Make sure you have columns like SKU/wid, MRP, Rent/rental+gst, Deposit/sd.'], 400);
                    return;
                }

                for ($row = 2; $row <= $highestRow; $row++) {
                    $skuCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($skuIdx + 1);
                    $mrpCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mrpIdx + 1);
                    $rentCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($rentIdx + 1);
                    $depositCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($depositIdx + 1);

                    $sku = trim($sheet->getCell($skuCol . $row)->getValue() ?? '');
                    if (empty($sku)) continue;

                    $mrpVal = $sheet->getCell($mrpCol . $row)->getValue();
                    $rentVal = $sheet->getCell($rentCol . $row)->getValue();
                    $depositVal = $sheet->getCell($depositCol . $row)->getValue();

                    // Clean and extract numeric values, or fallback to null
                    $mrp = ($mrpVal !== null && $mrpVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $mrpVal)) : null;
                    $rent = ($rentVal !== null && $rentVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $rentVal)) : null;
                    $deposit = ($depositVal !== null && $depositVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $depositVal)) : null;

                    $updates[] = [
                        'sku' => $sku,
                        'mrp' => $mrp,
                        'rent' => $rent,
                        'deposit' => $deposit
                    ];
                }
            } catch (\Exception $e) {
                $this->json(['error' => 'Failed to parse Excel file: ' . $e->getMessage()], 500);
                return;
            }
        } else {
            $this->json(['error' => 'Invalid request mode.'], 400);
            return;
        }

        if (empty($updates)) {
            $this->json(['error' => 'No valid pricing records found to update.'], 400);
            return;
        }

        foreach ($updates as $update) {
            $sku = $update['sku'];
            $mrpVal = $update['mrp'];
            $rentVal = $update['rent'];
            $depositVal = $update['deposit'];

            $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
            $existsGarment = $productModel->checkProductExists($sku, 'garments');

            if (!$existsJewel && !$existsGarment) {
                $notFoundSkus[] = $sku;
                continue;
            }

            // Fetch current product to merge if values are missing (e.g. rent is null)
            $type = $existsJewel ? 'jewellery' : 'garments';
            $pid = $existsJewel ? $existsJewel['product_id'] : $existsGarment['gproduct_id'];
            $currentProduct = $productModel->getProductById($pid, $type);

            if (!$currentProduct) {
                $errors[] = "SKU $sku: Failed to retrieve current product info.";
                continue;
            }

            $finalMrp = ($mrpVal !== null) ? $mrpVal : (float)($currentProduct['s_price'] ?? 0);
            $finalRent = ($rentVal !== null) ? $rentVal : (float)($currentProduct['rental_price'] ?? 0);
            $finalDeposit = ($depositVal !== null) ? $depositVal : (float)($currentProduct['deposit'] ?? 0);

            if ($productModel->updateProductPrices($sku, $finalMrp, $finalRent, $finalDeposit)) {
                $updatedCount++;
            } else {
                $errors[] = "SKU $sku: Database update failed.";
            }
        }

        $this->json([
            'success' => true,
            'message' => "Bulk price update completed.",
            'updatedCount' => $updatedCount,
            'notFoundCount' => count($notFoundSkus),
            'notFoundSkus' => $notFoundSkus,
            'errors' => $errors
        ]);
    }

    public function uploadSkuImages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $sku = trim($_POST['sku'] ?? '');
        if (empty($sku)) {
            $this->json(['error' => 'SKU is required'], 400);
            return;
        }

        if (empty($_FILES['files']['name'])) {
            $this->json(['error' => 'No files uploaded'], 400);
            return;
        }

        $productModel = new ProductModel();
        $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
        $existsGarment = $productModel->checkProductExists($sku, 'garments');

        if (!$existsJewel && !$existsGarment) {
            $this->json(['error' => "SKU '$sku' not found in database"], 404);
            return;
        }

        $uploadedPaths = [];
        $files = $_FILES['files'];
        $current_year = date('Y');
        $current_month = date('m');
        $upload_base = __DIR__ . "/../../yn/uploads/";
        $upload_path = $current_year . '/' . $current_month . '/';
        $full_upload_path = $upload_base . $upload_path;

        if (!file_exists($full_upload_path)) {
            mkdir($full_upload_path, 0755, true);
        }

        // Loop files
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $sku . '_' . time() . '_' . uniqid() . '.' . $ext;

            if (move_uploaded_file($files['tmp_name'][$i], $full_upload_path . $filename)) {
                $uploadedPaths[] = $upload_path . $filename;
                // Auto-generate 3-4 responsive variants (Original, Large, Tabs, Mobile)
                try {
                    \Core\ImageOptimizerService::generateResponsiveVariants($full_upload_path . $filename);
                } catch (\Throwable $e) {
                    error_log("Failed to generate responsive variants for {$filename}: " . $e->getMessage());
                }
            }
        }

        if (empty($uploadedPaths)) {
            $this->json(['error' => 'Failed to save any uploaded images'], 500);
            return;
        }

        try {
            if ($productModel->addImagesToProduct($sku, $uploadedPaths)) {
                $this->json([
                    'success' => true,
                    'message' => "Successfully uploaded " . count($uploadedPaths) . " images for SKU $sku."
                ]);
            } else {
                $this->json(['error' => 'Failed to link images to product database'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }


}
