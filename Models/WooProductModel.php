<?php
namespace Models;

use Core\Model;

class WooProductModel extends Model {
    
    private $prefix = "wpxyz_";

    public function __construct() {
        parent::__construct();
        $this->db = \Core\Database::getConnection('woo');
    }

    public function isConnected() {
        return $this->db !== null && $this->db !== false;
    }

    public function getProducts($params = []) {
        if (!$this->isConnected()) return [];

        $limit = $params['limit'] ?? 20;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;
        $search = $params['search'] ?? '';

        $searchQuery = "";
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $searchQuery = " AND (p.post_title LIKE '%$search%' OR pm_sku.meta_value LIKE '%$search%')";
        }

        if (!empty($params['skus']) && is_array($params['skus'])) {
            $skuList = array_map(function($s) { return "'" . mysqli_real_escape_string($this->db, trim($s)) . "'"; }, $params['skus']);
            $searchQuery .= " AND pm_sku.meta_value IN (" . implode(',', $skuList) . ")";
        }

        $sql = "SELECT 
                    p.ID,
                    p.post_title as name,
                    p.post_name as slug,
                    p.post_content as description,
                    pm_sku.meta_value as sku,
                    pm_price.meta_value as price,
                    pm_stock.meta_value as stock,
                    p_thumb.guid as image_url,
                    (SELECT GROUP_CONCAT(t.name) 
                     FROM {$this->prefix}terms t 
                     JOIN {$this->prefix}term_taxonomy tt ON t.term_id = tt.term_id 
                     JOIN {$this->prefix}term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id 
                     WHERE tr.object_id = p.ID AND tt.taxonomy = 'product_cat') as categories
                FROM {$this->prefix}posts p
                LEFT JOIN {$this->prefix}postmeta pm_sku ON p.ID = pm_sku.post_id AND pm_sku.meta_key = '_sku'
                LEFT JOIN {$this->prefix}postmeta pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = '_price'
                LEFT JOIN {$this->prefix}postmeta pm_stock ON p.ID = pm_stock.post_id AND pm_stock.meta_key = '_stock'
                LEFT JOIN {$this->prefix}postmeta pm_thumb ON p.ID = pm_thumb.post_id AND pm_thumb.meta_key = '_thumbnail_id'
                LEFT JOIN {$this->prefix}posts p_thumb ON pm_thumb.meta_value = p_thumb.ID
                WHERE p.post_type = 'product' 
                AND p.post_status = 'publish'
                $searchQuery
                ORDER BY p.post_date DESC
                LIMIT $offset, $limit";

        $result = mysqli_query($this->db, $sql);
        $products = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        }
        return $products;
    }

    public function getTotalCount($search = '') {
        if (!$this->isConnected()) return 0;

        $searchQuery = "";
        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->db, $search);
            $searchQuery = " AND (p.post_title LIKE '%$search%' OR pm_sku.meta_value LIKE '%$search%')";
        }

        $sql = "SELECT COUNT(*) as count 
                FROM {$this->prefix}posts p
                LEFT JOIN {$this->prefix}postmeta pm_sku ON p.ID = pm_sku.post_id AND pm_sku.meta_key = '_sku'
                WHERE p.post_type = 'product' 
                AND p.post_status = 'publish'
                $searchQuery";

        $result = mysqli_query($this->db, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return (int)$row['count'];
        }
        return 0;
    }
}
