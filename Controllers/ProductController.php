<?php
namespace Controllers;

use Core\Controller;
use Models\ProductModel;

class ProductController extends Controller {
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
    public function add() {
        $productModel = new ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        
        $this->view('products/add', [
            'jewelCategories' => $jewelCategories,
            'garments' => $garments
        ]);
    }

    public function checkSku() {
        $sku = $_GET['sku'] ?? '';
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

        // 2. Check if exists in POS
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

            $productModel->saveProduct($type, $_POST, $uploadedImages);
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
        
        $this->view('products/edit', [
            'product' => $product,
            'images' => $images,
            'type' => $type,
            'jewelCategories' => $jewelCategories,
            'garments' => $garments
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
            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect("index.php?controller=product&action=edit&id=$id&type=$type&error=" . urlencode($e->getMessage()));
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        try {
            $productModel->deleteProduct($id, $type);
            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect('index.php?controller=product&action=index&error=' . urlencode($e->getMessage()));
        }
    }

    private function handleImageUploads($code) {
        $uploadedImages = [];
        if (isset($_FILES['images'])) {
            $current_year = date('Y');
            $current_month = date('m');
            $upload_base = __DIR__ . "/../../../yn/uploads/";
            $upload_path = $upload_base . $current_year . '/' . $current_month . '/';

            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $filename = $_FILES['images']['name'][$key];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = $code . '_' . time() . '_' . ($key + 1) . '.' . $ext;
                    
                    if (move_uploaded_file($tmp_name, $upload_path . $new_filename)) {
                        $uploadedImages[] = $current_year . '/' . $current_month . '/' . $new_filename;
                    }
                }
            }
        }
        return $uploadedImages;
    }

    public function downloadTemplate() {
        if (ob_get_level()) ob_end_clean();
        
        $csv = "sku,name,description,type,category_id,subcat_id,s_price,rental_price,deposit,images\n";
        $csv .= '"JW101","Bridal Necklace Set","Beautiful antique set","jewellery","1","1","5000","1500","2000","https://example.com/img1.jpg,https://example.com/img2.jpg"' . "\n";
        $csv .= '"GM202","Red Lehenga Choli","Designer lehenga","garments","10","","12000","3500","5000","https://example.com/img3.jpg"' . "\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="product_import_template.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
        exit;
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

        $jewellery_search = '';
        $garments_search = '';
        
        if (!empty($search)) {
            $search_safe = mysqli_real_escape_string($db, $search);
            $jewellery_search = " AND (product_name LIKE '%$search_safe%' OR product_code LIKE '%$search_safe%')";
            $garments_search = " AND (gproduct_name LIKE '%$search_safe%' OR gproduct_code LIKE '%$search_safe%')";
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

        $query = "(SELECT product_id as id, product_code as code, 'jewellery' as type FROM product WHERE 1=1 $jewellery_search)
                  UNION ALL
                  (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type FROM garment_product WHERE 1=1 $garments_search)
                  ORDER BY id DESC";
                  
        $result = $productModel->query($db, $query);
        
        if (!$result) {
            die("SQL Error: " . mysqli_error($db));
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="products_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // Suppress deprecation warnings with @ for PHP 8.4 compatibility
        @fputcsv($output, ['sku', 'name', 'description', 'type', 'category_id', 'subcat_id', 's_price', 'rental_price', 'deposit', 'images'], ',', '"', '\\');

        $i = 0;
        while ($p = mysqli_fetch_assoc($result)) {
            $fullProduct = $productModel->getProductById($p['id'], $p['type']);
            if (!$fullProduct) continue;

            $images = $productModel->getProductImages($p['id'], $p['type']);
            $imageUrls = [];
            if ($images) {
                foreach ($images as $img) {
                    $imageUrls[] = "https://srishringarr.com/yn/uploads" . $img['img_name'];
                }
            }

            @fputcsv($output, [
                $fullProduct['code'] ?? $p['code'] ?? '',
                $fullProduct['name'] ?? $p['name'] ?? '',
                $fullProduct['description'] ?? '',
                $p['type'] ?? '',
                $fullProduct['category'] ?? '',
                $fullProduct['sub_category'] ?? '',
                $fullProduct['s_price'] ?? 0,
                $fullProduct['rental_price'] ?? 0,
                $fullProduct['deposit'] ?? 0,
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
        $this->view('products/import', [
            'categories' => $categories
        ]);
    }

    public function processImportRow() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        // Support both JSON (from modern dashboard) and Form Data (legacy)
        $input = json_decode(file_get_contents('php://input'), true);
        $data = !empty($input) ? $input : $_POST;
        
        $productModel = new ProductModel();

        try {
            $type = $data['type'] ?? 'jewellery';
            $code = $data['sku'] ?? '';

            if (empty($code)) throw new \Exception("Missing SKU");

            // Check if exists
            $isUpdate = $productModel->checkProductExists($code, $type);
            
            // Process Images from URLs
            $imageUrls = !empty($data['images']) ? explode(',', $data['images']) : [];
            $downloadedImages = [];
            
            foreach ($imageUrls as $url) {
                $url = trim($url);
                if (empty($url)) continue;

                // For updates, we might want to check if image already exists to avoid duplicates
                // But for now, we'll just download and add
                
                $current_year = date('Y');
                $current_month = date('m');
                $upload_base = __DIR__ . "/../../../yn/uploads/";
                $upload_path = $current_year . '/' . $current_month . '/';
                $full_upload_path = $upload_base . $upload_path;

                if (!file_exists($full_upload_path)) {
                    mkdir($full_upload_path, 0777, true);
                }

                $ext = pathinfo($url, PATHINFO_EXTENSION) ?: 'jpg';
                $filename = $code . '_' . time() . '_' . uniqid() . '.' . $ext;
                
                $imgData = @file_get_contents($url);
                if ($imgData) {
                    if (file_put_contents($full_upload_path . $filename, $imgData)) {
                        $downloadedImages[] = $upload_path . $filename;
                    }
                }
            }

            $catId = $data['category_id'] ?? 0;
            $subId = $data['subcat_id'] ?? 0;
            
            // Smart Match for Category Names if IDs are not numeric
            $categoryModel = new \Models\CategoryModel();
            if (!empty($catId) && !is_numeric($catId)) {
                $foundId = $categoryModel->getCategoryIdByName($catId, $type);
                if ($foundId) $catId = $foundId;
                else $catId = 0; // Fallback to 0 if not found
            }
            if (!empty($subId) && !is_numeric($subId)) {
                $foundId = $categoryModel->getCategoryIdByName($subId, $type);
                if ($foundId) $subId = $foundId;
                else $subId = 0;
            }

            $saveData = [
                'code' => $code,
                'name' => $data['name'] ?? 'Imported Product',
                'description' => $data['description'] ?? '',
                'category' => $catId,
                'sub_category' => $subId,
                's_price' => $data['s_price'] ?? 0,
                'rental_price' => $data['rental_price'] ?? 0,
                'deposit' => $data['deposit'] ?? 0
            ];

            if ($isUpdate) {
                $productModel->syncProductBySku($type, $saveData, $downloadedImages);
                return $this->json(['status' => 'updated', 'message' => "Product $code updated successfully"]);
            } else {
                $productModel->saveProduct($type, $saveData, $downloadedImages);
                return $this->json(['status' => 'success', 'message' => "Product $code imported successfully"]);
            }
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

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
}
