<?php
namespace Models;

use Core\Model;

class StatsModel extends Model {
    public function getTotalOrders() {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $this->query($this->db, $sql);
        return (int)($this->fetchOne($result)['count'] ?? 0);
    }

    public function getMonthlyRevenue() {
        // First try current month rental revenue from phppos_rent
        $sql = "SELECT SUM(rent_amount) as total FROM phppos_rent 
                WHERE MONTH(bill_date) = MONTH(CURRENT_DATE) 
                AND YEAR(bill_date) = YEAR(CURRENT_DATE)";
        $result = $this->query($this->db3, $sql);
        $total = (float)($this->fetchOne($result)['total'] ?? 0);

        if ($total <= 0) {
            // Check online orders as fallback
            $sqlOnline = "SELECT SUM(total_amount) as total FROM orders 
                          WHERE MONTH(created_at) = MONTH(CURRENT_DATE) 
                          AND YEAR(created_at) = YEAR(CURRENT_DATE)
                          AND status = 'paid'";
            $resultOnline = $this->query($this->db, $sqlOnline);
            $total = (float)($this->fetchOne($resultOnline)['total'] ?? 0);
        }

        // If current month has zero, get the latest active month revenue
        if ($total <= 0) {
            $sqlLatest = "SELECT SUM(rent_amount) as total FROM phppos_rent 
                          WHERE bill_date IS NOT NULL AND bill_date != '0000-00-00'
                          GROUP BY DATE_FORMAT(bill_date, '%Y-%m')
                          ORDER BY bill_date DESC LIMIT 1";
            $resLatest = $this->query($this->db3, $sqlLatest);
            $total = (float)($this->fetchOne($resLatest)['total'] ?? 0);
        }

        return $total;
    }

    public function getTotalRentalRevenue() {
        $sql = "SELECT SUM(rent_amount) as total, COUNT(*) as count FROM phppos_rent";
        $result = $this->query($this->db3, $sql);
        $row = $this->fetchOne($result);
        return [
            'total' => (float)($row['total'] ?? 0),
            'count' => (int)($row['count'] ?? 0)
        ];
    }

    public function getActiveProducts() {
        $jewellery = (int)($this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM product"))['count'] ?? 0);
        $garments = (int)($this->fetchOne($this->query($this->db, "SELECT COUNT(*) as count FROM garment_product"))['count'] ?? 0);
        return $jewellery + $garments;
    }

    public function getActiveRentals() {
        $sql = "SELECT 
            COUNT(CASE WHEN booking_status IN ('Booked', 'Picked', 'Picked Up') THEN 1 END) as active_rentals,
            COUNT(CASE WHEN booking_status = 'Booked' THEN 1 END) as booked_count,
            COUNT(CASE WHEN booking_status IN ('Picked', 'Picked Up') THEN 1 END) as picked_count,
            COUNT(CASE WHEN delivery_date < CURRENT_DATE AND booking_status IN ('Picked', 'Picked Up', 'Booked') THEN 1 END) as pending_returns
        FROM phppos_rent";
        $result = $this->query($this->db3, $sql);
        $row = $this->fetchOne($result);
        return [
            'active_rentals' => (int)($row['active_rentals'] ?? 0),
            'booked' => (int)($row['booked_count'] ?? 0),
            'picked' => (int)($row['picked_count'] ?? 0),
            'pending_returns' => (int)($row['pending_returns'] ?? 0)
        ];
    }

    public function getStockSummary() {
        $sql = "SELECT COUNT(*) as total_items, SUM(quantity) as total_qty, SUM(unit_price * quantity) as retail_val 
                FROM phppos_items WHERE is_deleted = 0";
        $result = $this->query($this->db3, $sql);
        $row = $this->fetchOne($result);
        return [
            'total_items' => (int)($row['total_items'] ?? 0),
            'total_qty' => (float)($row['total_qty'] ?? 0),
            'retail_value' => (float)($row['retail_val'] ?? 0)
        ];
    }

    public function getCategoryDistribution($limit = 5) {
        $sql = "SELECT category, COUNT(*) as item_count, SUM(quantity) as total_qty, SUM(unit_price * quantity) as total_val 
                FROM phppos_items 
                WHERE category IS NOT NULL AND category != '' AND category != 'ONLINE' 
                GROUP BY category 
                ORDER BY item_count DESC 
                LIMIT $limit";
        $result = $this->query($this->db3, $sql);
        return $this->fetchAll($result);
    }

    public function getRevenueTrends($months = 6) {
        $sql = "SELECT DATE_FORMAT(bill_date, '%b \'%y') as m_label, DATE_FORMAT(bill_date, '%Y-%m') as ym, 
                       COUNT(*) as bookings_count, SUM(rent_amount) as rev 
                FROM phppos_rent 
                WHERE bill_date IS NOT NULL AND bill_date != '0000-00-00' 
                GROUP BY ym 
                ORDER BY ym DESC 
                LIMIT $months";
        $result = $this->query($this->db3, $sql);
        $rows = $this->fetchAll($result);
        return array_reverse($rows);
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

    public function getRecentBookings($limit = 8) {
        $sql = "SELECT r.bill_id, r.bill_date, r.pick_date, r.delivery_date, r.booking_status, r.rent_amount, r.amount,
                COALESCE(NULLIF(TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))), ''), r.cust_name, r.person_Name, 'Customer') as customer_name,
                p.phone_number as customer_phone,
                (SELECT GROUP_CONCAT(item_id SEPARATOR ', ') FROM order_detail WHERE bill_id = r.bill_id) as items
                FROM phppos_rent r
                LEFT JOIN phppos_people p ON r.cust_id = p.person_id
                ORDER BY r.bill_id DESC
                LIMIT $limit";
        $result = $this->query($this->db3, $sql);
        return $this->fetchAll($result);
    }
}
