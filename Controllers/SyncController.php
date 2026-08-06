<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Core\ProductSyncService;

class SyncController extends Controller {

    public function index() {
        $con = Database::getConnection('con');
        $logs = [];
        $stats = [
            'total_synced' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'last_sync' => 'Never'
        ];

        if ($con) {
            // Stats
            $res = mysqli_query($con, "SELECT 
                COUNT(*) as total, 
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_cnt,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_cnt,
                MAX(synced_at) as last_time
                FROM product_sync_logs");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $stats['total_synced'] = (int)($row['total'] ?? 0);
                $stats['success_count'] = (int)($row['success_cnt'] ?? 0);
                $stats['failed_count'] = (int)($row['failed_cnt'] ?? 0);
                $stats['last_sync'] = !empty($row['last_time']) ? date('M j, Y g:i A', strtotime($row['last_time'])) : 'Never';
            }

            // Recent Logs
            $logRes = mysqli_query($con, "SELECT * FROM product_sync_logs ORDER BY synced_at DESC LIMIT 100");
            if ($logRes) {
                while ($r = mysqli_fetch_assoc($logRes)) {
                    $logs[] = $r;
                }
            }
        }

        $productModel = new \Models\ProductModel();
        $categories = $productModel->getCategories();
        $syncSettings = ProductSyncService::getSyncSettings();

        $this->view('sync/index', [
            'logs' => $logs,
            'stats' => $stats,
            'categories' => $categories,
            'syncSettings' => $syncSettings
        ]);
    }

    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $syncAll = !empty($_POST['sync_all']);
        $enabledCategories = $_POST['categories'] ?? [];

        $saved = ProductSyncService::saveSyncSettings($enabledCategories, $syncAll);
        if ($saved) {
            $this->json(['success' => true, 'message' => 'Sync Configuration saved successfully!']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save configuration settings']);
        }
    }

    public function syncSingle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['success' => false, 'message' => 'Product ID is required'], 400);
            return;
        }

        $result = ProductSyncService::syncProduct($id, $type, 'manual');
        $this->json($result);
    }

    public function syncBulk() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        // Increase execution time limit for bulk operations
        set_time_limit(300);

        $result = ProductSyncService::syncAllProducts('manual');
        $this->json($result);
    }

    public function getSyncQueue() {
        $con = Database::getConnection('con');
        if (!$con) {
            $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
            return;
        }

        $settings = ProductSyncService::getSyncSettings();
        $syncAll = !empty($settings['sync_all']);
        $enabled = $settings['enabled_categories'] ?? [];

        $items = [];

        if ($syncAll || empty($enabled)) {
            // Fetch All Jewellery
            $resJ = mysqli_query($con, "SELECT product_id as id, 'jewellery' as type, product_code as code, product_name as name FROM product ORDER BY product_id DESC");
            if ($resJ) {
                while ($r = mysqli_fetch_assoc($resJ)) {
                    $r['id'] = (int)$r['id'];
                    $r['code'] = trim($r['code'] ?? '');
                    $r['name'] = trim($r['name'] ?? '');
                    $items[] = $r;
                }
            }

            // Fetch All Garments
            $resG = mysqli_query($con, "SELECT gproduct_id as id, 'garments' as type, gproduct_code as code, gproduct_name as name FROM garment_product ORDER BY gproduct_id DESC");
            if ($resG) {
                while ($r = mysqli_fetch_assoc($resG)) {
                    $r['id'] = (int)$r['id'];
                    $r['code'] = trim($r['code'] ?? '');
                    $r['name'] = trim($r['name'] ?? '');
                    $items[] = $r;
                }
            }
        } else {
            // Pre-filter by enabled categories
            $jewelParentIds = [];
            $jewelChildIds = [];
            $garmentIds = [];

            foreach ($enabled as $catStr) {
                if (str_starts_with($catStr, 'jewel_parent:')) {
                    $jewelParentIds[] = (int)str_replace('jewel_parent:', '', $catStr);
                } elseif (str_starts_with($catStr, 'jewel_child:')) {
                    $jewelChildIds[] = (int)str_replace('jewel_child:', '', $catStr);
                } elseif (str_starts_with($catStr, 'garment:')) {
                    $garmentIds[] = (int)str_replace('garment:', '', $catStr);
                }
            }

            if (!empty($jewelParentIds)) {
                $parentStr = implode(',', $jewelParentIds);
                $subRes = mysqli_query($con, "SELECT subcat_id FROM subcat1 WHERE maincat_id IN ($parentStr) AND status = 1");
                if ($subRes) {
                    while ($subRow = mysqli_fetch_assoc($subRes)) {
                        $jewelChildIds[] = (int)$subRow['subcat_id'];
                    }
                }
            }

            if (!empty($garmentIds)) {
                $gMainStr = implode(',', $garmentIds);
                $gSubRes = mysqli_query($con, "SELECT sub_id FROM garment_subcat WHERE gmain_id IN ($gMainStr)");
                if ($gSubRes) {
                    while ($gSubRow = mysqli_fetch_assoc($gSubRes)) {
                        $garmentIds[] = (int)$gSubRow['sub_id'];
                    }
                }
            }

            $allJewelIds = array_unique(array_merge($jewelParentIds, $jewelChildIds));
            $allGarmentIds = array_unique($garmentIds);

            if (!empty($allJewelIds)) {
                $jewelStr = implode(',', $allJewelIds);
                $sqlJ = "SELECT DISTINCT p.product_id as id, 'jewellery' as type, p.product_code as code, p.product_name as name 
                         FROM product p
                         LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                         WHERE p.categories_id IN ($jewelStr)
                            OR p.subcat_id IN ($jewelStr)
                            OR pc.legacy_category_id IN ($jewelStr)
                            OR pc.legacy_subcategory_id IN ($jewelStr)
                         ORDER BY p.product_id DESC";
                $resJ = mysqli_query($con, $sqlJ);
                if ($resJ) {
                    while ($r = mysqli_fetch_assoc($resJ)) {
                        $r['id'] = (int)$r['id'];
                        $r['code'] = trim($r['code'] ?? '');
                        $r['name'] = trim($r['name'] ?? '');
                        $items[] = $r;
                    }
                }
            }

            if (!empty($allGarmentIds)) {
                $gStr = implode(',', $allGarmentIds);
                $sqlG = "SELECT DISTINCT gp.gproduct_id as id, 'garments' as type, gp.gproduct_code as code, gp.gproduct_name as name 
                         FROM garment_product gp
                         LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                         WHERE gp.garment_id IN ($gStr)
                            OR gp.product_for IN ($gStr)
                            OR pc.legacy_category_id IN ($gStr)
                            OR pc.legacy_subcategory_id IN ($gStr)
                         ORDER BY gp.gproduct_id DESC";
                $resG = mysqli_query($con, $sqlG);
                if ($resG) {
                    while ($r = mysqli_fetch_assoc($resG)) {
                        $r['id'] = (int)$r['id'];
                        $r['code'] = trim($r['code'] ?? '');
                        $r['name'] = trim($r['name'] ?? '');
                        $items[] = $r;
                    }
                }
            }
        }

        $this->json([
            'success' => true,
            'total' => count($items),
            'items' => $items
        ]);
    }

    public function unmappedCategories() {
        $con = Database::getConnection('con');
        $pdoChild = ProductSyncService::getChildPdo();

        $childUnmappedCount = 0;
        $parentUnmappedCount = 0;

        if ($pdoChild) {
            $stmt1 = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE p.category_id > 0 AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = p.category_id)");
            $childUnmappedCount = (int)($stmt1->fetch()['cnt'] ?? 0);
        }

        if ($con) {
            $resJ = mysqli_query($con, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
            $cntJ = (int)(mysqli_fetch_assoc($resJ)['cnt'] ?? 0);

            $resG = mysqli_query($con, "SELECT COUNT(*) as cnt FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
            $cntG = (int)(mysqli_fetch_assoc($resG)['cnt'] ?? 0);

            $parentUnmappedCount = $cntJ + $cntG;
        }

        $this->view('sync/unmapped_categories', [
            'childUnmappedCount' => $childUnmappedCount,
            'parentUnmappedCount' => $parentUnmappedCount
        ]);
    }

    public function getUnmappedProducts() {
        $target = $_GET['target'] ?? 'child'; // 'child' or 'parent'
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $items = [];
        $total = 0;

        if ($target === 'child') {
            $pdoChild = ProductSyncService::getChildPdo();
            if (!$pdoChild) {
                $this->json(['success' => false, 'message' => 'Child DB connection failed'], 500);
                return;
            }

            $whereClause = "WHERE p.category_id > 0 AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = p.category_id)";
            if (!empty($search)) {
                $sEsc = "%$search%";
                $whereClause .= " AND (p.name LIKE " . $pdoChild->quote($sEsc) . " OR p.sku LIKE " . $pdoChild->quote($sEsc) . ")";
            }

            $stmtCount = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p $whereClause");
            $total = (int)($stmtCount->fetch()['cnt'] ?? 0);

            $sql = "SELECT p.id, p.sku, p.name, p.category_id, c.name as category_name 
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    $whereClause 
                    ORDER BY p.id DESC 
                    LIMIT $offset, $limit";
            $stmt = $pdoChild->query($sql);
            while ($r = $stmt->fetch()) {
                $items[] = [
                    'id' => (int)$r['id'],
                    'sku' => $r['sku'] ?? 'N/A',
                    'name' => $r['name'] ?? 'Product',
                    'category_id' => (int)$r['category_id'],
                    'category_name' => $r['category_name'] ?? 'Category #' . $r['category_id'],
                    'target' => 'child'
                ];
            }
        } else {
            $con = Database::getConnection('con');
            if (!$con) {
                $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
                return;
            }

            $searchEsc = mysqli_real_escape_string($con, $search);
            $searchWhereJ = !empty($search) ? " AND (p.product_name LIKE '%$searchEsc%' OR p.product_code LIKE '%$searchEsc%')" : "";
            $searchWhereG = !empty($search) ? " AND (gp.gproduct_name LIKE '%$searchEsc%' OR gp.gproduct_code LIKE '%$searchEsc%')" : "";

            $sqlCount = "SELECT SUM(cnt) as total FROM (
                SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery') $searchWhereJ
                UNION ALL
                SELECT COUNT(*) as cnt FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments') $searchWhereG
            ) as t";
            $resCount = mysqli_query($con, $sqlCount);
            $total = (int)(mysqli_fetch_assoc($resCount)['total'] ?? 0);

            $sqlCombined = "
                (SELECT p.product_id as id, p.product_code as sku, p.product_name as name, 'jewellery' as type, p.categories_id as category_id, c.categories_name as category_name
                 FROM product p
                 LEFT JOIN jewel_subcat c ON p.categories_id = c.subcat_id
                 WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery') $searchWhereJ)
                UNION ALL
                (SELECT gp.gproduct_id as id, gp.gproduct_code as sku, gp.gproduct_name as name, 'garments' as type, gp.garment_id as category_id, c.name as category_name
                 FROM garment_product gp
                 LEFT JOIN garments c ON gp.garment_id = c.garment_id
                 WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments') $searchWhereG)
                ORDER BY id DESC LIMIT $offset, $limit";
            $resComb = mysqli_query($con, $sqlCombined);
            if ($resComb) {
                while ($r = mysqli_fetch_assoc($resComb)) {
                    $items[] = [
                        'id' => (int)$r['id'],
                        'sku' => $r['sku'] ?? 'N/A',
                        'name' => $r['name'] ?? 'Product',
                        'type' => $r['type'],
                        'category_id' => (int)$r['category_id'],
                        'category_name' => $r['category_name'] ?? 'Category #' . $r['category_id'],
                        'target' => 'parent'
                    ];
                }
            }
        }

        $this->json([
            'success' => true,
            'target' => $target,
            'total' => $total,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'items' => $items
        ]);
    }

    public function fixUnmappedProducts() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $target = $_POST['target'] ?? 'child'; // 'child' or 'parent'
        $singleId = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? 'jewellery';

        if ($target === 'child') {
            $pdoChild = ProductSyncService::getChildPdo();
            if (!$pdoChild) {
                $this->json(['success' => false, 'message' => 'Child DB connection failed'], 500);
                return;
            }

            try {
                if ($singleId > 0) {
                    $stmt = $pdoChild->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) SELECT id, category_id FROM products WHERE id = :id AND category_id > 0");
                    $stmt->execute([':id' => $singleId]);
                    $inserted = $stmt->rowCount();
                    $msg = $inserted > 0 ? "Successfully inserted category record for Child product ID $singleId" : "No missing record found or already mapped for ID $singleId";
                } else {
                    $stmt = $pdoChild->query("INSERT IGNORE INTO product_categories (product_id, category_id) 
                        SELECT p.id, p.category_id 
                        FROM products p 
                        WHERE p.category_id > 0 
                        AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = p.category_id)");
                    $inserted = $stmt->rowCount();
                    $msg = "Successfully bulk-inserted $inserted category mapping records in Child DB!";
                }

                $this->json(['success' => true, 'message' => $msg, 'inserted_count' => $inserted ?? 0]);
            } catch (\Exception $e) {
                $this->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
            }
        } else {
            // Parent DB mapping fix
            $model = new \Models\ProductModel();
            $con = Database::getConnection('con');

            if (!$con) {
                $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
                return;
            }

            try {
                $inserted = 0;
                if ($singleId > 0) {
                    $prod = $model->getProductById($singleId, $type);
                    if ($prod) {
                        $cat = (int)($prod['category'] ?? 0);
                        $sub = (int)($prod['sub_category'] ?? 0);
                        $model->saveProductCategories($singleId, $type, $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
                        $inserted = 1;
                    }
                    $msg = "Successfully processed category mapping for Parent product ID $singleId";
                } else {
                    // Bulk fix parent products
                    $resJ = mysqli_query($con, "SELECT product_id, categories_id, subcat_id FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
                    if ($resJ) {
                        while ($r = mysqli_fetch_assoc($resJ)) {
                            $pid = (int)$r['product_id'];
                            $cat = (int)$r['categories_id'];
                            $sub = (int)$r['subcat_id'];
                            $model->saveProductCategories($pid, 'jewellery', $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
                            $inserted++;
                        }
                    }
                    $resG = mysqli_query($con, "SELECT gproduct_id, garment_id, product_for FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
                    if ($resG) {
                        while ($r = mysqli_fetch_assoc($resG)) {
                            $gid = (int)$r['gproduct_id'];
                            $cat = (int)$r['garment_id'];
                            $sub = (int)$r['product_for'];
                            $model->saveProductCategories($gid, 'garments', $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
                            $inserted++;
                        }
                    }
                    $msg = "Successfully bulk-processed $inserted category mapping records in Parent DB!";
                }

                $this->json(['success' => true, 'message' => $msg, 'inserted_count' => $inserted]);
            } catch (\Exception $e) {
                $this->json(['success' => false, 'message' => 'Parent DB error: ' . $e->getMessage()], 500);
            }
        }
    }
}
