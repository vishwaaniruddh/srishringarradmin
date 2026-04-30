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
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        
        $params = [
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'category' => $category
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
}
