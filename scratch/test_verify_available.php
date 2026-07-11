<?php
// Set up CLI environment to mimic Web Server request
$_SERVER['HTTP_HOST'] = 'localhost';

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Models\ProductModel;

echo "=== Verifying Available-Only Toggle Backend Filter ===\n\n";

$productModel = new ProductModel();

echo "1. Fetching first 10 products without filter:\n";
$products = $productModel->getProducts([
    'limit' => 10,
    'page' => 1,
    'available_only' => false
]);

$all_ok = true;
$out_of_stock_count = 0;
foreach ($products as $p) {
    $qty = $p['details']['quantity'] ?? 0;
    if ($qty <= 0) {
        $out_of_stock_count++;
    }
    echo "   - Code: {$p['code']} | Qty: {$qty}\n";
}
echo "   Found $out_of_stock_count out-of-stock products.\n\n";

echo "2. Fetching first 10 products WITH available_only = true filter:\n";
$availableProducts = $productModel->getProducts([
    'limit' => 10,
    'page' => 1,
    'available_only' => true
]);

$failures = 0;
foreach ($availableProducts as $p) {
    $qty = $p['details']['quantity'] ?? 0;
    $status = "[PASS]";
    if ($qty <= 0) {
        $status = "[FAIL]";
        $failures++;
    }
    echo "   - $status Code: {$p['code']} | Qty: {$qty}\n";
}

if ($failures === 0 && count($availableProducts) > 0) {
    echo "\n✓ SUCCESS: Available-only filter works perfectly! All returned products are in-stock.\n";
} elseif (count($availableProducts) === 0) {
    echo "\n⚠ Notice: No products returned (either no products in stock or empty database).\n";
} else {
    echo "\n✗ FAILURE: Some returned products are out-of-stock!\n";
    exit(1);
}
