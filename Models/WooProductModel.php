<?php
namespace Models;

use Core\Model;
use Core\ProductSyncService;
use PDO;

class WooProductModel extends Model {

    private $pdo;

    public function __construct() {
        parent::__construct();
        $this->pdo = ProductSyncService::getChildPdo();
    }

    public function isConnected() {
        if ($this->pdo) {
            try {
                $this->pdo->query("SELECT 1");
                return true;
            } catch (\Throwable $t) {
                $this->pdo = null;
            }
        }
        $this->pdo = ProductSyncService::getChildPdo();
        return $this->pdo !== null && $this->pdo !== false;
    }

    /**
     * Resolve public image URL for child store
     */
    public static function formatImageUrl($imagePath) {
        $imagePath = trim((string)$imagePath);
        if (empty($imagePath)) {
            return '';
        }
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }
        return 'https://yosshitaneha.com/admin/' . ltrim($imagePath, '/');
    }

    /**
     * Get Products from Core PHP Child Store (products table)
     */
    public function getProducts($params = []) {
        if (!$this->isConnected()) return [];

        $limit = max(1, (int)($params['limit'] ?? 20));
        $page = max(1, (int)($params['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        $search = trim($params['search'] ?? '');
        $status = trim($params['status'] ?? '');
        $categoryId = (int)($params['category_id'] ?? 0);

        $conditions = ["p.deleted_at IS NULL"];
        $bindings = [];

        if (!empty($search)) {
            $conditions[] = "(p.name LIKE :search OR p.sku LIKE :search)";
            $bindings[':search'] = "%$search%";
        }

        if (!empty($status) && in_array($status, ['published', 'draft'])) {
            $conditions[] = "p.status = :status";
            $bindings[':status'] = $status;
        }

        if ($categoryId > 0) {
            $conditions[] = "EXISTS (SELECT 1 FROM product_categories pc_filter WHERE pc_filter.product_id = p.id AND pc_filter.category_id = :catId)";
            $bindings[':catId'] = $categoryId;
        }

        if (!empty($params['skus']) && is_array($params['skus'])) {
            $placeholders = [];
            foreach (array_values($params['skus']) as $idx => $skuVal) {
                $paramName = ":sku_$idx";
                $placeholders[] = $paramName;
                $bindings[$paramName] = trim($skuVal);
            }
            if (!empty($placeholders)) {
                $conditions[] = "p.sku IN (" . implode(',', $placeholders) . ")";
            }
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "SELECT 
                    p.id,
                    p.id as ID,
                    p.name,
                    p.slug,
                    p.sku,
                    p.description,
                    p.short_description,
                    p.price,
                    p.sale_price,
                    p.stock_qty as stock,
                    p.status,
                    p.is_featured,
                    p.main_image,
                    p.created_at,
                    p.updated_at,
                    (SELECT GROUP_CONCAT(c.name SEPARATOR ', ')
                     FROM product_categories pc
                     JOIN categories c ON pc.category_id = c.id
                     WHERE pc.product_id = p.id AND c.deleted_at IS NULL) as categories
                FROM products p
                WHERE $whereSql
                ORDER BY p.id DESC
                LIMIT $offset, $limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['image_url'] = self::formatImageUrl($r['main_image'] ?? '');
            if (empty($r['categories'])) {
                $r['categories'] = 'Uncategorized';
            }
        }
        unset($r);

        return $rows;
    }

    /**
     * Get total count of matching products
     */
    public function getTotalCount($search = '', $status = '', $categoryId = 0) {
        if (!$this->isConnected()) return 0;

        $conditions = ["p.deleted_at IS NULL"];
        $bindings = [];

        $search = trim($search);
        if (!empty($search)) {
            $conditions[] = "(p.name LIKE :search OR p.sku LIKE :search)";
            $bindings[':search'] = "%$search%";
        }

        if (!empty($status) && in_array($status, ['published', 'draft'])) {
            $conditions[] = "p.status = :status";
            $bindings[':status'] = $status;
        }

        if ($categoryId > 0) {
            $conditions[] = "EXISTS (SELECT 1 FROM product_categories pc_filter WHERE pc_filter.product_id = p.id AND pc_filter.category_id = :catId)";
            $bindings[':catId'] = $categoryId;
        }

        $whereSql = implode(' AND ', $conditions);
        $sql = "SELECT COUNT(*) as count FROM products p WHERE $whereSql";

        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['count'] ?? 0);
    }

    /**
     * Summary statistics for dashboard cards
     */
    public function getStats() {
        if (!$this->isConnected()) {
            return ['total' => 0, 'published' => 0, 'draft' => 0, 'out_of_stock' => 0];
        }

        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                        SUM(CASE WHEN stock_qty <= 0 THEN 1 ELSE 0 END) as out_of_stock
                    FROM products 
                    WHERE deleted_at IS NULL";
            $stmt = $this->pdo->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'total' => (int)($row['total'] ?? 0),
                'published' => (int)($row['published'] ?? 0),
                'draft' => (int)($row['draft'] ?? 0),
                'out_of_stock' => (int)($row['out_of_stock'] ?? 0)
            ];
        } catch (\Throwable $t) {
            return ['total' => 0, 'published' => 0, 'draft' => 0, 'out_of_stock' => 0];
        }
    }

    /**
     * Get list of active categories in Child DB
     */
    public function getCategories() {
        if (!$this->isConnected()) return [];

        try {
            $stmt = $this->pdo->query("SELECT id, name FROM categories WHERE deleted_at IS NULL ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $t) {
            return [];
        }
    }
}
