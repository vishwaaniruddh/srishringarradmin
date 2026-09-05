<?php
namespace Controllers;

use Core\Controller;
use Models\StatsModel;

class ApiController extends Controller {
    public function stats() {
        $statsModel = new \Models\StatsModel();
        
        $data = [
            'total_orders' => $statsModel->getTotalOrders(),
            'monthly_revenue' => $statsModel->getMonthlyRevenue(),
            'active_products' => $statsModel->getActiveProducts(),
            'active_rentals' => $statsModel->getActiveRentals(),
            'jewellery_count' => $statsModel->getJewelleryCount(),
            'garments_count' => $statsModel->getGarmentsCount(),
            'out_of_stock' => $statsModel->getOutOfStockCount(),
            'low_stock' => $statsModel->getLowStockCount(),
            'recent_bookings' => $statsModel->getRecentBookings(5)
        ];
        $this->json($data);
    }
    
    public function products() {
        $productModel = new \Models\ProductModel();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $featured = isset($_GET['featured']) ? $_GET['featured'] : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';
        $availableOnly = isset($_GET['available_only']) && ($_GET['available_only'] == 1 || $_GET['available_only'] == 'true');
        
        $params = [
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'category' => $category,
            'featured' => $featured,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'available_only' => $availableOnly
        ];
        
        $products = $productModel->getProducts($params);
        $totalRecords = $productModel->getTotalCount($params);
        $categories = $productModel->getCategories();
        $stats = $productModel->getProductStats();
        
        $this->json([
            'products' => $products,
            'totalRecords' => (int)$totalRecords,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page,
            'categories' => $categories,
            'stats' => $stats
        ]);
    }

    public function toggleFeatured() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? '';
        $status = (int)($input['status'] ?? 0);

        if (!$id || !$type) {
            $this->json(['error' => 'Missing parameters'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        if ($productModel->toggleFeatured($id, $type, $status)) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update featured status'], 500);
        }
    }

    public function togglePriceSource() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? '';
        $priceSource = $input['price_source'] ?? 'pos';

        if (!$id || !$type) {
            $this->json(['error' => 'Missing parameters'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        if ($productModel->togglePriceSource($id, $type, $priceSource)) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update price source'], 500);
        }
    }

    public function toggleAvailability() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? '';
        $availability = $input['availability'] ?? 'both';

        if (!$id || !$type) {
            $this->json(['error' => 'Missing parameters'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        if ($productModel->toggleAvailability($id, $type, $availability)) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update availability status'], 500);
        }
    }

    public function getProduct() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }

        $images = $productModel->getProductImages($id, $type);
        $assignedCategories = $productModel->getProductAssignedCategories($id, $type);
        $categoriesTree = $productModel->getAllCategoriesWithSubcategories($type);
        $availableColors = $productModel->getAvailableColors();

        $this->json([
            'success' => true,
            'product' => $product,
            'images' => $images,
            'assignedCategories' => $assignedCategories,
            'categoriesTree' => $categoriesTree,
            'availableColors' => $availableColors
        ]);
    }

    public function updateProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        try {
            $productModel->updateProduct($type, $id, $input);
            if (isset($input['categories']) || isset($input['sub_categories'])) {
                $mainCategories = $input['categories'] ?? [];
                $subcategories = $input['sub_categories'] ?? [];
                $productModel->saveProductCategories($id, $type, $mainCategories, $subcategories);
            }
            $this->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
