<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;

$pdoChild = ProductSyncService::getChildPdo();

if ($pdoChild) {
    echo "=== Child DB Category Mapping Analysis ===\n";

    // Unmapped products with valid category_id > 0
    $stmt1 = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE p.category_id > 0 AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id)");
    $row1 = $stmt1->fetch();
    echo "Unmapped products in Child DB with category_id > 0: {$row1['cnt']}\n";

    // Unmapped products with category_id IS NULL or 0
    $stmt2 = $pdoChild->query("SELECT COUNT(*) as cnt FROM products p WHERE (p.category_id IS NULL OR p.category_id = 0) AND NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id)");
    $row2 = $stmt2->fetch();
    echo "Unmapped products in Child DB with category_id = 0 or NULL: {$row2['cnt']}\n";

    // Total products in products table
    $stmtTotal = $pdoChild->query("SELECT COUNT(*) as cnt FROM products");
    echo "Total products in Child DB: {$stmtTotal->fetch()['cnt']}\n";

    // Total records in product_categories
    $stmtCatRel = $pdoChild->query("SELECT COUNT(*) as cnt FROM product_categories");
    echo "Total records in Child product_categories: {$stmtCatRel->fetch()['cnt']}\n";

    // Sample categories in Child DB
    echo "\nChild categories table (sample 10):\n";
    $stmtCats = $pdoChild->query("SELECT id, name, slug FROM categories LIMIT 10");
    while ($c = $stmtCats->fetch()) {
        echo "ID {$c['id']}: {$c['name']} ({$c['slug']})\n";
    }
}
