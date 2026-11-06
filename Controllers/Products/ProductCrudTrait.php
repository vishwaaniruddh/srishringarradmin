<?php
namespace Controllers\Products;

use Models\ProductModel;
use Controllers\CategoryController;
use Core\ProductSyncService;

trait ProductCrudTrait {
    public function index() {
        $productModel = new ProductModel();
        
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $categories = $productModel->getCategories();
        
        $this->view('products/index', [
            'search' => $search,
            'category' => $category,
            'categories' => $categories
        ]);
    }

    public function unmapped() {
        $categoryController = new CategoryController();
        $categoryController->unmapped();
    }

    public function view_details() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        if (!$product) {
            $this->redirect('index.php?controller=product&action=index&error=Product+not+found');
        }

        $this->view('products/view', [
            'product' => $product,
            'images' => $images,
            'type' => $type
        ]);
    }


    public function saveProductField() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? 'jewellery';
        $field = $input['field'] ?? '';
        $value = trim($input['value'] ?? '');

        if (!$id || !in_array($type, ['jewellery', 'garments']) || !in_array($field, ['name', 'description']) || empty($value)) {
            $this->json(['error' => 'Invalid parameters'], 400);
            return;
        }

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        if ($type === 'jewellery') {
            $column = ($field === 'name') ? 'product_name' : 'product_desc';
            $sql = "UPDATE product SET $column = ? WHERE product_id = ?";
        } else {
            $column = ($field === 'name') ? 'gproduct_name' : 'gproduct_desc';
            $sql = "UPDATE garment_product SET $column = ? WHERE gproduct_id = ?";
        }

        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) {
            $this->json(['error' => mysqli_error($db)], 500);
            return;
        }

        mysqli_stmt_bind_param($stmt, "si", $value, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            try {
                \Core\ProductSyncService::syncProduct($id, $type, 'auto');
            } catch (\Throwable $th) {
                error_log("Auto-sync error on saveProductField: " . $th->getMessage());
            }
            $this->json(['success' => true, 'message' => ucfirst($field) . ' updated successfully']);
        } else {
            $err = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            $this->json(['error' => $err], 500);
        }
    }

    public function add() {
        $productModel = new ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $allJewelCategoriesTree = $productModel->getAllCategoriesWithSubcategories('jewellery');
        $allGarmentCategoriesTree = $productModel->getAllCategoriesWithSubcategories('garments');
        $availableColors = $productModel->getAvailableColors();
        
        $this->view('products/add', [
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'allJewelCategoriesTree' => $allJewelCategoriesTree,
            'allGarmentCategoriesTree' => $allGarmentCategoriesTree,
            'availableColors' => $availableColors
        ]);
    }

    public function checkSku() {
        $sku = $_GET['sku'] ?? '';
        $priceSource = $_GET['price_source'] ?? 'pos';
        if (!$sku) $this->json(['error' => 'Missing SKU'], 400);

        $productModel = new ProductModel();
        
        // 1. Check if already exists in local database
        $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
        $existsGarment = $productModel->checkProductExists($sku, 'garments');
        
        if ($existsJewel || $existsGarment) {
            $this->json([
                'allowed' => false,
                'message' => "Product with code '$sku' already exists in the local database."
            ]);
            return;
        }

        // 2. Manual price source: no POS validation needed
        if ($priceSource === 'manual') {
            $this->json([
                'allowed' => true,
                'message' => 'Manual pricing mode — POS validation skipped.'
            ]);
            return;
        }

        // 3. Check if exists in POS
        $posItem = $productModel->validateSkuInPos($sku);
        
        if ($posItem) {
            $this->json([
                'allowed' => true,
                'message' => 'Valid POS product found: ' . $posItem['name']
            ]);
        } else {
            $this->json([
                'allowed' => false,
                'message' => 'Product not found in POS system. Please add it to POS first.'
            ]);
        }
    }

    public function getSubcategories() {
        $type = $_GET['type'] ?? 'jewellery';
        $parentId = (int)$_GET['parent_id'];
        $productModel = new ProductModel();
        
        if ($type === 'jewellery') {
            $subs = $productModel->getJewelSubcategories($parentId);
        } else {
            $subs = $productModel->getGarmentSubcategories($parentId);
            // Map keys for consistent JSON output
            $subs = array_map(function($s) {
                return ['subcat_id' => $s['sub_id'], 'name' => $s['sub_name']];
            }, $subs);
        }
        
        echo json_encode($subs);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Check for duplicates
            if ($productModel->checkProductExists($code, $type)) {
                throw new \Exception("Product with code '$code' already exists in the system.");
            }

            // Process Images
            $uploadedImages = $this->handleImageUploads($code);

            $productId = $productModel->saveProduct($type, $_POST, $uploadedImages);

            // Process Multi-Category Selection
            $mainCategories = $_POST['categories'] ?? [];
            $subcategories = $_POST['sub_categories'] ?? [];
            if ($productId) {
                $productModel->saveProductCategories($productId, $type, $mainCategories, $subcategories);
            }

            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect('index.php?controller=product&action=add&error=' . urlencode($e->getMessage()));
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        
        $allCategoriesTree = $productModel->getAllCategoriesWithSubcategories($type);
        $assignedCategories = $productModel->getProductAssignedCategories($id, $type);
        $availableColors = $productModel->getAvailableColors();
        
        $isSyncApplicable = ProductSyncService::isCategoryEnabled($type, $product);

        $this->view('products/edit', [
            'product' => $product,
            'images' => $images,
            'type' => $type,
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'allCategoriesTree' => $allCategoriesTree,
            'assignedCategories' => $assignedCategories,
            'availableColors' => $availableColors,
            'isSyncApplicable' => $isSyncApplicable
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $id = (int)$_POST['id'];
        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Process New Images
            $uploadedImages = $this->handleImageUploads($code);

            $productModel->updateProduct($type, $id, $_POST, $uploadedImages);

            // Process Multi-Category Selection
            $mainCategories = $_POST['categories'] ?? [];
            $subcategories = $_POST['sub_categories'] ?? [];
            $productModel->saveProductCategories($id, $type, $mainCategories, $subcategories);

            // Check if product is applicable for sync to child store
            $syncParams = '';
            $updatedProduct = $productModel->getProductById($id, $type);
            $isSyncApplicable = ProductSyncService::isCategoryEnabled($type, $updatedProduct);
            if ($isSyncApplicable) {
                try {
                    $syncResult = ProductSyncService::syncProduct($id, $type, 'manual');
                    if (!empty($syncResult['success']) && empty($syncResult['skipped'])) {
                        $syncParams = '&sync_status=synced';
                    } elseif (!empty($syncResult['skipped'])) {
                        $syncParams = '&sync_status=skipped&sync_msg=' . urlencode($syncResult['message'] ?? 'Category skipped');
                    } else {
                        $syncParams = '&sync_status=error&sync_msg=' . urlencode($syncResult['message'] ?? 'Sync failed');
                    }
                } catch (\Throwable $st) {
                    $syncParams = '&sync_status=error&sync_msg=' . urlencode($st->getMessage());
                }
            } else {
                $syncParams = '&sync_status=not_applicable';
            }

            $this->redirect("index.php?controller=product&action=edit&id=$id&type=$type&success=1" . $syncParams);
        } catch (\Exception $e) {
            $this->redirect("index.php?controller=product&action=edit&id=$id&type=$type&error=" . urlencode($e->getMessage()));
        }
    }

    public function edit2() {
        $this->edit();
    }

    public function update2() {
        $this->update();
    }

    public function edit3() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $availableColors = $productModel->getAvailableColors();
        $isSyncApplicable = ProductSyncService::isCategoryEnabled($type, $product);
        
        $this->view('products/edit3', [
            'product' => $product,
            'images' => $images,
            'type' => $type,
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'availableColors' => $availableColors,
            'isSyncApplicable' => $isSyncApplicable
        ]);
    }

    public function update3() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $id = (int)$_POST['id'];
        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Process New Images
            $uploadedImages = $this->handleImageUploads($code);

            $productModel->updateProduct($type, $id, $_POST, $uploadedImages);

            // Check if product is applicable for sync to child store
            $syncParams = '';
            $updatedProduct = $productModel->getProductById($id, $type);
            $isSyncApplicable = ProductSyncService::isCategoryEnabled($type, $updatedProduct);
            if ($isSyncApplicable) {
                try {
                    $syncResult = ProductSyncService::syncProduct($id, $type, 'manual');
                    if (!empty($syncResult['success']) && empty($syncResult['skipped'])) {
                        $syncParams = '&sync_status=synced';
                    } elseif (!empty($syncResult['skipped'])) {
                        $syncParams = '&sync_status=skipped&sync_msg=' . urlencode($syncResult['message'] ?? 'Category skipped');
                    } else {
                        $syncParams = '&sync_status=error&sync_msg=' . urlencode($syncResult['message'] ?? 'Sync failed');
                    }
                } catch (\Throwable $st) {
                    $syncParams = '&sync_status=error&sync_msg=' . urlencode($st->getMessage());
                }
            } else {
                $syncParams = '&sync_status=not_applicable';
            }

            $this->redirect("index.php?controller=product&action=edit3&id=$id&type=$type&success=1" . $syncParams);
        } catch (\Exception $e) {
            $this->redirect("index.php?controller=product&action=edit3&id=$id&type=$type&error=" . urlencode($e->getMessage()));
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        try {
            $product = $productModel->getProductById($id, $type);
            $productModel->deleteProduct($id, $type);

            if ($product && !empty($product['code'])) {
                try {
                    \Core\ProductSyncService::deleteProductFromChild($product['code']);
                } catch (\Throwable $th) {
                    error_log("Auto-sync delete error: " . $th->getMessage());
                }
            }
            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect('index.php?controller=product&action=index&error=' . urlencode($e->getMessage()));
        }
    }

    public function deleteImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['ids'] ?? null;
        $id = (int)($input['id'] ?? 0);

        $productModel = new ProductModel();
        try {
            if (is_array($ids) && count($ids) > 0) {
                $successCount = 0;
                foreach ($ids as $imgId) {
                    if ($productModel->deleteImage((int)$imgId)) {
                        $successCount++;
                    }
                }
                $this->json(['success' => true, 'deleted_count' => $successCount]);
                return;
            }

            if (!$id) {
                $this->json(['error' => 'Missing image ID'], 400);
                return;
            }

            if ($productModel->deleteImage($id)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Failed to delete image from database'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function handleImageUploads($code) {
        $uploadedImages = [];
        if (isset($_FILES['images'])) {
            $current_year = date('Y');
            $current_month = date('m');
            $upload_base = __DIR__ . "/../../yn/uploads/";
            $upload_path = $upload_base . $current_year . '/' . $current_month . '/';

            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $filename = $_FILES['images']['name'][$key];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = $code . '_' . time() . '_' . ($key + 1) . '.' . $ext;
                    
                    if (move_uploaded_file($tmp_name, $upload_path . $new_filename)) {
                        $uploadedImages[] = $current_year . '/' . $current_month . '/' . $new_filename;
                        // Auto-generate 3-4 responsive variants (Original, Large, Tabs, Mobile)
                        try {
                            \Core\ImageOptimizerService::generateResponsiveVariants($upload_path . $new_filename);
                        } catch (\Throwable $e) {
                            error_log("Failed to generate responsive variants for {$new_filename}: " . $e->getMessage());
                        }
                    }
                }
            }
        }
        return $uploadedImages;
    }


    public function setMainImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $imageId = (int)($input['image_id'] ?? 0);
        $productId = (int)($input['product_id'] ?? 0);
        $type = $input['type'] ?? 'jewellery';

        if (!$imageId || !$productId || !in_array($type, ['jewellery', 'garments'])) {
            $this->json(['error' => 'Invalid parameters'], 400);
            return;
        }

        $productModel = new ProductModel();
        try {
            if ($productModel->setMainProductImage($imageId, $productId, $type)) {
                $this->json(['success' => true, 'message' => 'Main product image updated successfully']);
            } else {
                $this->json(['error' => 'Failed to update main image'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateImageWeight() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $imageId = (int)($input['image_id'] ?? 0);
        $weight = (int)($input['weight'] ?? 0);

        if (!$imageId) {
            $this->json(['error' => 'Image ID is required'], 400);
            return;
        }

        $productModel = new ProductModel();
        try {
            if ($productModel->updateImageWeight($imageId, $weight)) {
                $this->json(['success' => true, 'message' => 'Image weight updated successfully']);
            } else {
                $this->json(['error' => 'Failed to update image weight'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }


}
