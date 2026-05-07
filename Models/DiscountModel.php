<?php
namespace Models;

use Core\Model;

class DiscountModel extends Model {
    
    public function getRules() {
        $sql = "SELECT * FROM discount_rules ORDER BY weight DESC, id DESC";
        $result = $this->query($this->db, $sql);
        $rules = $this->fetchAll($result);
        
        foreach ($rules as &$rule) {
            $rule['target_display'] = $this->resolveTargetNames($rule['target']);
            $rule['target_objects'] = $this->resolveTargetObjects($rule['target']);
        }
        
        return $rules;
    }

    public function addRule($data) {
        $scope = mysqli_real_escape_string($this->db, $data['scope']);
        $target = mysqli_real_escape_string($this->db, $data['target']);
        $type = mysqli_real_escape_string($this->db, $data['type']);
        $value = (float)$data['value'];
        $weight = (int)$data['weight'];
        $threshold = isset($data['threshold']) ? (float)$data['threshold'] : null;
        $threshold_max = isset($data['threshold_max']) ? (float)$data['threshold_max'] : null;

        $sql = "INSERT INTO discount_rules (scope, target, type, value, weight, threshold, threshold_max) 
                VALUES ('$scope', '$target', '$type', '$value', '$weight', " . 
                ($threshold !== null ? "'$threshold'" : "NULL") . ", " . 
                ($threshold_max !== null ? "'$threshold_max'" : "NULL") . ")";
        
        return $this->query($this->db, $sql);
    }

    public function updateRule($data) {
        $id = (int)$data['id'];
        $scope = mysqli_real_escape_string($this->db, $data['scope']);
        $target = mysqli_real_escape_string($this->db, $data['target']);
        $type = mysqli_real_escape_string($this->db, $data['type']);
        $value = (float)$data['value'];
        $weight = (int)$data['weight'];
        $threshold = isset($data['threshold']) && $data['threshold'] !== '' ? (float)$data['threshold'] : null;
        $threshold_max = isset($data['threshold_max']) && $data['threshold_max'] !== '' ? (float)$data['threshold_max'] : null;

        $sql = "UPDATE discount_rules SET 
                scope = '$scope', 
                target = '$target', 
                type = '$type', 
                value = '$value', 
                weight = '$weight', 
                threshold = " . ($threshold !== null ? "'$threshold'" : "NULL") . ", 
                threshold_max = " . ($threshold_max !== null ? "'$threshold_max'" : "NULL") . " 
                WHERE id = $id";
        
        return $this->query($this->db, $sql);
    }

    public function deleteRule($id) {
        $id = (int)$id;
        $sql = "DELETE FROM discount_rules WHERE id = $id";
        return $this->query($this->db, $sql);
    }

    public function getSettings() {
        $sql = "SELECT * FROM discount_settings";
        $result = $this->query($this->db, $sql);
        $settings = [];
        while ($row = $this->fetchOne($result)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSetting($key, $value) {
        $key = mysqli_real_escape_string($this->db, $key);
        $value = mysqli_real_escape_string($this->db, $value);
        $sql = "UPDATE discount_settings SET setting_value = '$value' WHERE setting_key = '$key'";
        return $this->query($this->db, $sql);
    }

    public function searchTargets($type, $term) {
        $term = mysqli_real_escape_string($this->db, $term);
        $results = [];

        if ($type === 'product') {
            $sql = "(SELECT product_id as id, product_name as name, product_code as code, 'jewellery' as type 
                     FROM product 
                     WHERE product_name LIKE '%$term%' OR product_code LIKE '%$term%' LIMIT 10)
                    UNION
                    (SELECT gproduct_id as id, gproduct_name as name, gproduct_code as code, 'garments' as type 
                     FROM garment_product 
                     WHERE gproduct_name LIKE '%$term%' OR gproduct_code LIKE '%$term%' LIMIT 10)";
            $res = $this->query($this->db, $sql);
            while ($row = $this->fetchOne($res)) {
                $results[] = [
                    'id' => $row['id'] . ':' . $row['type'],
                    'text' => $row['name'] . ' (' . $row['code'] . ')'
                ];
            }
        } elseif ($type === 'category') {
            // Apparel Categories
            $sql1 = "SELECT garment_id as id, name FROM garments WHERE name LIKE '%$term%' LIMIT 10";
            $res1 = $this->query($this->db, $sql1);
            while ($row = $this->fetchOne($res1)) {
                $results[] = [
                    'id' => $row['id'] . ':garment',
                    'text' => $row['name'] . ' (Garment)'
                ];
            }
            // Jewellery Categories
            $sql2 = "SELECT subcat_id as id, categories_name as name FROM jewel_subcat WHERE categories_name LIKE '%$term%' LIMIT 10";
            $res2 = $this->query($this->db, $sql2);
            while ($row = $this->fetchOne($res2)) {
                $results[] = [
                    'id' => $row['id'] . ':jewel_parent',
                    'text' => $row['name'] . ' (Jewel Category)'
                ];
            }
            // Jewellery Subcategories
            $sql3 = "SELECT subcat_id as id, name FROM subcat1 WHERE name LIKE '%$term%' LIMIT 10";
            $res3 = $this->query($this->db, $sql3);
            while ($row = $this->fetchOne($res3)) {
                $results[] = [
                    'id' => $row['id'] . ':jewel_child',
                    'text' => $row['name'] . ' (Jewel Subcategory)'
                ];
            }
        }

        return $results;
    }

    public function resolveTargetNames($targetStr) {
        if (empty($targetStr) || $targetStr === 'all') return 'All';
        
        $targets = explode(',', $targetStr);
        $names = [];
        
        foreach ($targets as $t) {
            if (strpos($t, ':') === false) {
                $names[] = $t;
                continue;
            }
            
            $parts = explode(':', $t);
            $id = (int)$parts[0];
            $type = $parts[1] ?? '';
            
            if ($type === 'jewellery') {
                $q = "SELECT product_name, product_code FROM product WHERE product_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $names[] = $row['product_name'] . ' (' . $row['product_code'] . ')';
            } elseif ($type === 'garments') {
                $q = "SELECT gproduct_name, gproduct_code FROM garment_product WHERE gproduct_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $names[] = $row['gproduct_name'] . ' (' . $row['gproduct_code'] . ')';
            } elseif ($type === 'garment') {
                $q = "SELECT name FROM garments WHERE garment_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $names[] = $row['name'] . ' (Cat)';
            } elseif ($type === 'jewel_parent') {
                $q = "SELECT categories_name FROM jewel_subcat WHERE subcat_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $names[] = $row['categories_name'] . ' (Cat)';
            } elseif ($type === 'jewel_child') {
                $q = "SELECT name FROM subcat1 WHERE subcat_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $names[] = $row['name'] . ' (Sub)';
            } else {
                $names[] = $t;
            }
        }
        
        return implode(', ', $names);
    }

    public function resolveTargetObjects($targetStr) {
        if (empty($targetStr) || $targetStr === 'all') return [];
        
        $targets = explode(',', $targetStr);
        $objects = [];
        
        foreach ($targets as $t) {
            if (strpos($t, ':') === false) continue;
            
            $parts = explode(':', $t);
            $id = (int)$parts[0];
            $type = $parts[1] ?? '';
            
            $name = '';
            if ($type === 'jewellery') {
                $q = "SELECT product_name, product_code FROM product WHERE product_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $name = $row['product_name'] . ' (' . $row['product_code'] . ')';
            } elseif ($type === 'garments') {
                $q = "SELECT gproduct_name, gproduct_code FROM garment_product WHERE gproduct_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $name = $row['gproduct_name'] . ' (' . $row['gproduct_code'] . ')';
            } elseif ($type === 'garment') {
                $q = "SELECT name FROM garments WHERE garment_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $name = $row['name'] . ' (Cat)';
            } elseif ($type === 'jewel_parent') {
                $q = "SELECT categories_name FROM jewel_subcat WHERE subcat_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $name = $row['categories_name'] . ' (Cat)';
            } elseif ($type === 'jewel_child') {
                $q = "SELECT name FROM subcat1 WHERE subcat_id = $id";
                $res = $this->query($this->db, $q);
                if ($row = $this->fetchOne($res)) $name = $row['name'] . ' (Sub)';
            }
            
            if ($name) {
                $objects[] = ['id' => $t, 'text' => $name];
            }
        }
        
        return $objects;
    }
}
