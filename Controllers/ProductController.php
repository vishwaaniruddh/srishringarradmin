<?php
namespace Controllers;

use Core\Controller;
use Models\ProductModel;

class ProductController extends Controller {
    public function index() {
        $productModel = new ProductModel();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        
        $params = [
            'page' => $page,
            'limit' => 20,
            'search' => $search,
            'category' => $category
        ];
        
        $products = $productModel->getProducts($params);
        $totalRecords = $productModel->getTotalCount($params);
        $categories = $productModel->getCategories();
        
        $this->view('products/index', [
            'products' => $products,
            'totalRecords' => $totalRecords,
            'totalPages' => ceil($totalRecords / 20),
            'currentPage' => $page,
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

    public function export() {
        $productModel = new ProductModel();
        $params = ['limit' => 10000]; // Get all products
        $products = $productModel->getProducts($params);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['sku', 'name', 'description', 'type', 'category_id', 'subcat_id', 's_price', 'rental_price', 'deposit', 'images']);

        foreach ($products as $p) {
            // Get detailed info for export
            $fullProduct = $productModel->getProductById($p['id'], $p['type']);
            $images = $productModel->getProductImages($p['id'], $p['type']);
            $imageUrls = array_map(function($img) {
                return "https://srishringarr.com/yn/uploads" . $img['img_name'];
            }, $images);

            fputcsv($output, [
                $fullProduct['code'] ?? '',
                $fullProduct['name'] ?? '',
                $fullProduct['description'] ?? '',
                $p['type'] ?? '',
                $fullProduct['category'] ?? '',
                $fullProduct['sub_category'] ?? '',
                $fullProduct['s_price'] ?? 0,
                $fullProduct['rental_price'] ?? 0,
                $fullProduct['deposit'] ?? 0,
                implode(',', $imageUrls)
            ]);
        }
        fclose($output);
        exit;
    }

    public function import() {
        $this->view('products/import');
    }

    public function processImportRow() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $data = $_POST;
        $productModel = new ProductModel();

        try {
            $type = $data['type'] ?? 'jewellery';
            $code = $data['sku'] ?? '';

            if (empty($code)) throw new \Exception("Missing SKU");

            // Check if exists
            if ($productModel->checkProductExists($code, $type)) {
                return $this->json(['status' => 'skipped', 'message' => "Product $code already exists"]);
            }

            // Process Images from URLs
            $imageUrls = !empty($data['images']) ? explode(',', $data['images']) : [];
            $downloadedImages = [];
            
            foreach ($imageUrls as $url) {
                $url = trim($url);
                if (empty($url)) continue;

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

            $saveData = [
                'code' => $code,
                'name' => $data['name'] ?? 'Imported Product',
                'description' => $data['description'] ?? '',
                'category' => $data['category_id'] ?? 0,
                'sub_category' => $data['subcat_id'] ?? 0,
                's_price' => $data['s_price'] ?? 0,
                'rental_price' => $data['rental_price'] ?? 0,
                'deposit' => $data['deposit'] ?? 0
            ];

            $productModel->saveProduct($type, $saveData, $downloadedImages);

            return $this->json(['status' => 'success', 'message' => "Product $code imported successfully"]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
