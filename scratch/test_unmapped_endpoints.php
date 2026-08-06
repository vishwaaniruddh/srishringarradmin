<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Controllers\SyncController;
use Core\ProductSyncService;
use Core\Database;

echo "=== Testing Unmapped Products Query ===\n";

$pdoChild = ProductSyncService::getChildPdo();
if ($pdoChild) {
    $stmtCount = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE p.category_id > 0 AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = p.category_id)");
    echo "Child DB unmapped before fix: " . $stmtCount->fetch()['cnt'] . "\n";
}

$con = Database::getConnection('con');
if ($con) {
    $resJ = mysqli_query($con, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
    $cntJ = (int)(mysqli_fetch_assoc($resJ)['cnt'] ?? 0);
    echo "Parent DB unmapped jewellery before fix: " . $cntJ . "\n";
}
