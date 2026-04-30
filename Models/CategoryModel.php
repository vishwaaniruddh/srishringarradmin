<?php
namespace Models;

use Core\Model;

class CategoryModel extends Model {
    
    // --- JEWELLERY ---
    
    public function getJewelCategories() {
        $sql = "SELECT subcat_id as id, categories_name as name, `desc` FROM jewel_subcat WHERE mcat_id=1 OR mcat_id=3 ORDER BY categories_name ASC";
        $categories = $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
        foreach ($categories as &$cat) {
            $id = (int)$cat['id'];
            $count_sql = "SELECT COUNT(*) as cnt FROM product WHERE categories_id = $id";
            $cat['product_count'] = $this->db->query($count_sql)->fetch_assoc()['cnt'];
        }
        return $categories;
    }
    
    public function getJewelSubcategories($categoryId = null) {
        $where = "WHERE status = 1";
        if ($categoryId) $where .= " AND maincat_id = $categoryId";
        $sql = "SELECT subcat_id as id, maincat_id, name, `desc` FROM subcat1 $where ORDER BY name ASC";
        $subcategories = $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
        foreach ($subcategories as &$sub) {
            $id = (int)$sub['id'];
            $count_sql = "SELECT COUNT(*) as cnt FROM product WHERE subcat_id = $id";
            $sub['product_count'] = $this->db->query($count_sql)->fetch_assoc()['cnt'];
        }
        return $subcategories;
    }
    
    public function saveJewelCategory($data) {
        $name = $this->db->real_escape_string($data['name']);
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        $date = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO jewel_subcat (categories_name, `desc`, date_added, mcat_id) VALUES ('$name', '$desc', '$date', 1)";
        return $this->db->query($sql);
    }
    
    public function saveJewelSub($data) {
        $name = $this->db->real_escape_string($data['name']);
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        $mainId = (int)$data['parent_id'];
        
        $sql = "INSERT INTO subcat1 (maincat_id, name, `desc`, status) VALUES ($mainId, '$name', '$desc', 1)";
        return $this->db->query($sql);
    }
    
    // --- GARMENTS / APPAREL ---
    
    public function getGarmentCategories() {
        $sql = "SELECT garment_id as id, name, description FROM garments WHERE Main_id=1 OR Main_id=3 ORDER BY name ASC";
        $categories = $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
        foreach ($categories as &$cat) {
            $id = (int)$cat['id'];
            $count_sql = "SELECT COUNT(*) as cnt FROM garment_product WHERE garment_id = $id OR product_for = $id";
            $cat['product_count'] = $this->db->query($count_sql)->fetch_assoc()['cnt'];
        }
        return $categories;
    }

    public function getGarmentSubcategories($garmentId = null) {
        $where = $garmentId ? "WHERE gmain_id = $garmentId" : "";
        $sql = "SELECT sub_id as id, gmain_id, sub_name as name, description as `desc` FROM garment_subcat $where ORDER BY sub_name ASC";
        $subcategories = $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
        foreach ($subcategories as &$sub) {
            $id = (int)$sub['id'];
            $count_sql = "SELECT COUNT(*) as cnt FROM garment_product WHERE garment_id = $id"; // Garment subcats are rare but supported
            $sub['product_count'] = $this->db->query($count_sql)->fetch_assoc()['cnt'];
        }
        return $subcategories;
    }
    
    public function saveGarmentCategory($data) {
        $name = $this->db->real_escape_string($data['name']);
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        
        $sql = "INSERT INTO garments (name, description, Main_id) VALUES ('$name', '$desc', 1)";
        return $this->db->query($sql);
    }

    public function saveGarmentSub($data) {
        $name = $this->db->real_escape_string($data['name']);
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        $mainId = (int)$data['parent_id'];
        
        $sql = "INSERT INTO garment_subcat (gmain_id, sub_name, description) VALUES ($mainId, '$name', '$desc')";
        return $this->db->query($sql);
    }
    
    // --- GENERIC UPDATE ---
    
    public function updateCategory($type, $id, $data) {
        $id = (int)$id;
        $name = $this->db->real_escape_string($data['name']);
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        
        if ($type === 'jewel_cat') {
            $sql = "UPDATE jewel_subcat SET categories_name = '$name', `desc` = '$desc' WHERE subcat_id = $id";
        } elseif ($type === 'jewel_sub') {
            $mainId = (int)$data['parent_id'];
            $sql = "UPDATE subcat1 SET name = '$name', `desc` = '$desc', maincat_id = $mainId WHERE subcat_id = $id";
        } elseif ($type === 'garment_cat') {
            $sql = "UPDATE garments SET name = '$name', description = '$desc' WHERE garment_id = $id";
        } elseif ($type === 'garment_sub') {
            $mainId = (int)$data['parent_id'];
            $sql = "UPDATE garment_subcat SET sub_name = '$name', description = '$desc', gmain_id = $mainId WHERE sub_id = $id";
        } else {
            return false;
        }
        
        return $this->db->query($sql);
    }
    
    public function getCategory($type, $id) {
        $id = (int)$id;
        if ($type === 'jewel_cat') {
            $sql = "SELECT subcat_id as id, categories_name as name, `desc` FROM jewel_subcat WHERE subcat_id = $id";
        } elseif ($type === 'jewel_sub') {
            $sql = "SELECT subcat_id as id, maincat_id as parent_id, name, `desc` FROM subcat1 WHERE subcat_id = $id";
        } elseif ($type === 'garment_cat') {
            $sql = "SELECT garment_id as id, name, description as `desc` FROM garments WHERE garment_id = $id";
        } elseif ($type === 'garment_sub') {
            $sql = "SELECT sub_id as id, gmain_id as parent_id, sub_name as name, description as `desc` FROM garment_subcat WHERE sub_id = $id";
        } else {
            return null;
        }
        
        return $this->db->query($sql)->fetch_assoc();
    }

    public function getCategoryIdByName($name, $type = 'jewellery') {
        $name = mysqli_real_escape_string($this->db, trim($name));
        if (empty($name)) return 0;

        if ($type === 'jewellery') {
            // Check Main Categories
            $sql = "SELECT subcat_id as id FROM jewel_subcat WHERE categories_name = '$name' LIMIT 1";
            $res = mysqli_query($this->db, $sql);
            if ($row = mysqli_fetch_assoc($res)) return (int)$row['id'];

            // Check Subcategories
            $sql = "SELECT subcat_id as id FROM subcat1 WHERE name = '$name' LIMIT 1";
            $res = mysqli_query($this->db, $sql);
            if ($row = mysqli_fetch_assoc($res)) return (int)$row['id'];
        } else {
            // Apparel
            $sql = "SELECT garment_id as id FROM garment_category WHERE name = '$name' LIMIT 1";
            $res = mysqli_query($this->db, $sql);
            if ($row = mysqli_fetch_assoc($res)) return (int)$row['id'];
        }

        return 0;
    }
}
