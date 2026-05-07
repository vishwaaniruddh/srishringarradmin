<?php
namespace Controllers;

use Models\OrderModel;

class OrdersController {
    public function index() {
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrders(100);
        
        $pageTitle = 'Orders Management';
        require_once 'Views/orders/index.php';
    }

    public function view() {
        $billId = $_GET['id'] ?? null;
        if (!$billId) {
            header('Location: index.php?controller=orders');
            exit;
        }

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderSummary($billId);
        $details = $orderModel->getOrderDetails($billId);

        $pageTitle = "Order #$billId Details";
        require_once 'Views/orders/view.php';
    }
}
