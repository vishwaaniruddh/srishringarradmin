<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Core\ProductSyncService;

$conParent = Database::getConnection('con');
$pdoChild = ProductSyncService::getChildPdo();

echo "=== 1. PARENT DB (ss - DB1) ===\n";
if ($conParent) {
    // Check product without product_categories
    $resJ = mysqli_query($conParent, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
    $rowJ = mysqli_fetch_assoc($resJ);
    echo "Jewellery in 'product' table NOT in 'product_categories': {$rowJ['cnt']}\n";

    $resG = mysqli_query($conParent, "SELECT COUNT(*) as cnt FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
    $rowG = mysqli_fetch_assoc($resG);
    echo "Garments in 'garment_product' table NOT in 'product_categories': {$rowG['cnt']}\n";

    // Sample unmapped product from parent
    $resSampleJ = mysqli_query($conParent, "SELECT product_id, product_code, product_name, categories_id, subcat_id FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery') LIMIT 5");
    echo "Sample unmapped Jewellery in Parent:\n";
    while ($r = mysqli_fetch_assoc($resSampleJ)) {
        print_r($r);
    }
} else {
    echo "Parent DB connection failed.\n";
}

echo "\n=== 2. CHILD DB (yn / yosshitaneha_db) ===\n";
if ($pdoChild) {
    $stmt1 = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id)");
    $row1 = $stmt1->fetch();
    echo "Products in Child 'products' table NOT in 'product_categories': {$row1['cnt']}\n";

    $stmt2 = $pdoChild->query("SELECT p.id, p.sku, p.name, p.category_id FROM products p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id) LIMIT 5");
    echo "Sample unmapped Products in Child DB:\n";
    while ($r = $stmt2->fetch()) {
        print_r($r);
    }

    echo "\nChild product_categories schema:\n";
    $stmtCols = $pdoChild->query("DESCRIBE product_categories");
    while ($c = $stmtCols->fetch()) {
        echo "{$c['Field']} - {$c['Type']}\n";
    }
} else {
    echo "Child DB connection failed.\n";
}
