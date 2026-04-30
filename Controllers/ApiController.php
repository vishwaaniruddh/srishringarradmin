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
}
