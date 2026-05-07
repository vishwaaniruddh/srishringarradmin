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
}
