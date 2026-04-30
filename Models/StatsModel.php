<?php
namespace Models;

use Core\Model;

class StatsModel extends Model {
    public function getTotalOrders() {
        $sql = "SELECT COUNT(*) as count FROM phppos_rent";
        $result = $this->query($this->db3, $sql);
        return $this->fetchOne($result)['count'];
    }

    public function getMonthlyRevenue() {
        $sql = "SELECT SUM(rent_amount) as total FROM phppos_rent 
                WHERE MONTH(bill_date) = MONTH(CURRENT_DATE) 
                AND YEAR(bill_date) = YEAR(CURRENT_DATE)";
        $result = $this->query($this->db3, $sql);
        return $this->fetchOne($result)['total'] ?? 0;
    }

    public function getActiveProducts() {
        $jewellery = $this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM product"))['count'];
        $garments = $this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM garment_product"))['count'];
        return $jewellery + $garments;
    }

    public function getActiveRentals() {
        $sql = "SELECT COUNT(*) as count FROM phppos_rent 
                WHERE pick_date <= CURRENT_DATE 
                AND delivery_date >= CURRENT_DATE";
        $result = $this->query($this->db3, $sql);
        return $this->fetchOne($result)['count'];
    }
}
