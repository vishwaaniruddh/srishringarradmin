<?php
namespace Models;

use Core\Model;

class CouponModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->createTable();
    }

    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS coupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            discount_type ENUM('percent', 'fixed_cart', 'fixed_product') DEFAULT 'percent',
            coupon_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
            expiry_date DATE NULL,
            usage_count INT DEFAULT 0,
            usage_limit INT NULL,
            usage_limit_per_user INT NULL,
            limit_usage_to_x_items INT NULL,
            minimum_amount DECIMAL(10, 2) NULL,
            maximum_amount DECIMAL(10, 2) NULL,
            individual_use TINYINT(1) DEFAULT 0,
            exclude_sale_items TINYINT(1) DEFAULT 0,
            product_ids TEXT NULL,
            exclude_product_ids TEXT NULL,
            product_categories TEXT NULL,
            exclude_product_categories TEXT NULL,
            customer_email_white_list TEXT NULL,
            status ENUM('active', 'expired', 'disabled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        return $this->query($this->db, $sql);
    }

    public function getAll() {
        $sql = "SELECT * FROM coupons ORDER BY created_at DESC";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getById($id) {
        $safeId = (int)$id;
        $sql = "SELECT * FROM coupons WHERE id = $safeId";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result);
    }

    public function getByCode($code) {
        $safeCode = mysqli_real_escape_string($this->db, $code);
        $sql = "SELECT * FROM coupons WHERE code = '$safeCode'";
        $result = $this->query($this->db, $sql);
        return $this->fetchOne($result);
    }

    public function create($data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = $key;
            if ($value === null) {
                $values[] = "NULL";
            } else {
                $values[] = "'" . mysqli_real_escape_string($this->db, $value) . "'";
            }
        }
        $sql = "INSERT INTO coupons (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        return $this->query($this->db, $sql);
    }

    public function update($id, $data) {
        $safeId = (int)$id;
        $sets = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                $sets[] = "$key = NULL";
            } else {
                $sets[] = "$key = '" . mysqli_real_escape_string($this->db, $value) . "'";
            }
        }
        $sql = "UPDATE coupons SET " . implode(', ', $sets) . " WHERE id = $safeId";
        return $this->query($this->db, $sql);
    }

    public function delete($id) {
        $safeId = (int)$id;
        $sql = "DELETE FROM coupons WHERE id = $safeId";
        return $this->query($this->db, $sql);
    }
}
