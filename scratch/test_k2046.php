<?php
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

echo "=== TESTING SYNC FOR PRODUCT 11433 (SKU k2046) ===\n";

$res = \Core\ProductSyncService::syncProduct(11433, 'jewellery', 'manual');
print_r($res);

$childPdo = \Core\ProductSyncService::getChildPdo();
$stmt = $childPdo->prepare("SELECT id, name, sku, price, main_image FROM products WHERE sku = 'k2046'");
$stmt->execute();
$prod = $stmt->fetch();

echo "\nChild Product Entry:\n";
print_r($prod);

$stmtImg = $childPdo->prepare("SELECT * FROM product_images WHERE product_id = " . ($prod['id'] ?? 0));
$stmtImg->execute();
$imgs = $stmtImg->fetchAll();
echo "\nChild Gallery Images:\n";
print_r($imgs);
