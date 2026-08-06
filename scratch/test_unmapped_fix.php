<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;
use Core\Database;

$pdoChild = ProductSyncService::getChildPdo();
$conParent = Database::getConnection('con');

echo "=== Testing SQL Batch Insert for Child DB product_categories ===\n";

if ($pdoChild) {
    // Check how many rows would be affected
    $stmtBefore = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE p.category_id > 0 AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = p.category_id)");
    $cntBefore = $stmtBefore->fetch()['cnt'];
    echo "Unmapped records before: $cntBefore\n";
}

echo "\n=== Testing SQL Batch Insert for Parent DB product_categories ===\n";

if ($conParent) {
    // Unmapped Jewellery in Parent DB
    $sqlJ = "SELECT p.product_id, p.product_code, p.categories_id, p.subcat_id 
             FROM product p 
             WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')";
    $resJ = mysqli_query($conParent, $sqlJ);
    $unmappedJ = mysqli_num_rows($resJ);
    echo "Unmapped Parent Jewellery: $unmappedJ\n";
}
