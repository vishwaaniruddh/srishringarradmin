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
            'active_rentals' => $statsModel->getActiveRentals()
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
        
        $params = [
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'category' => $category,
            'featured' => $featured
        ];
        
        $products = $productModel->getProducts($params);
        $totalRecords = $productModel->getTotalCount($params);
        $categories = $productModel->getCategories();
        
        $this->json([
            'products' => $products,
            'totalRecords' => (int)$totalRecords,
            'totalPages' => ceil($totalRecords / $limit),
            'currentPage' => $page,
            'categories' => $categories
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
}
