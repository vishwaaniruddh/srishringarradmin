<?php
namespace Models;

use Core\Model;

class StatsModel extends Model {
    public function getTotalOrders() {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result)['count'];
    }

    public function getMonthlyRevenue() {
        $sql = "SELECT SUM(total_amount) as total FROM orders 
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE)
                AND status = 'paid'";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result)['total'] ?? 0;
    }

    public function getActiveProducts() {
        $jewellery = $this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM product"))['count'];
        $garments = $this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM garment_product"))['count'];
        return $jewellery + $garments;
    }

    public function getActiveRentals() {
        $sql = "SELECT COUNT(*) as count FROM order_items 
                WHERE booking_type = 'rent' 
                AND start_date <= CURRENT_DATE 
                AND end_date >= CURRENT_DATE";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result)['count'];
    }

    public function getOutOfStockCount() {
        $sql = "SELECT COUNT(*) as count FROM phppos_items WHERE quantity <= 0";
        $result = $this->query($this->db3, $sql);
        return (int)($this->fetchOne($result)['count'] ?? 0);
    }

    public function getLowStockCount() {
        $sql = "SELECT COUNT(*) as count FROM phppos_items WHERE quantity > 0 AND quantity <= 2";
        $result = $this->query($this->db3, $sql);
        return (int)($this->fetchOne($result)['count'] ?? 0);
    }

    public function getJewelleryCount() {
        $sql = "SELECT COUNT(*) as count FROM product";
        $result = $this->query($this->db, $sql);
        return (int)($this->fetchOne($result)['count'] ?? 0);
    }

    public function getGarmentsCount() {
        $sql = "SELECT COUNT(*) as count FROM garment_product";
        $result = $this->query($this->db, $sql);
        return (int)($this->fetchOne($result)['count'] ?? 0);
    }

    public function getRecentBookings($limit = 5) {
        $sql = "SELECT r.bill_id, r.bill_date, r.pick_date, r.delivery_date, r.booking_status, r.rent_amount, r.deposit_amount,
                (SELECT GROUP_CONCAT(item_id SEPARATOR ', ') FROM order_detail WHERE bill_id = r.bill_id) as items
                FROM phppos_rent r
                ORDER BY r.bill_id DESC
                LIMIT $limit";
        $result = $this->query($this->db3, $sql);
        return $this->fetchAll($result);
    }
}
