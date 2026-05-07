<?php
namespace Models;

use Core\Model;

class OrderModel extends Model {
    public function getOrders($limit = 50, $offset = 0) {
        // Fetching from web orders table (u464193275_srishrinjewels)
        $sql = "SELECT *, 
                CONCAT(first_name, ' ', last_name) as cust_name,
                (SELECT COUNT(*) FROM order_items WHERE order_id = orders.id) as item_count
                FROM orders 
                ORDER BY id DESC 
                LIMIT $limit OFFSET $offset";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getOrderDetails($orderId) {
        $safeId = (int)$orderId;
        $sql = "SELECT oi.*, 
                (SELECT img_name FROM product_images_new 
                 WHERE (oi.product_type = 'jewellery' AND product_id = oi.product_id)
                 OR (oi.product_type = 'garments' AND gproduct_id = oi.product_id)
                 ORDER BY rank LIMIT 1) as img_name
                FROM order_items oi 
                WHERE oi.order_id = $safeId";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getOrderSummary($orderId) {
        $safeId = (int)$orderId;
        $sql = "SELECT *, CONCAT(first_name, ' ', last_name) as cust_name FROM orders WHERE id = $safeId";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result);
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result)['count'];
    }
}
