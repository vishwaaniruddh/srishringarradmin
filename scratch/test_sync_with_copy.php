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

echo "==================================================\n";
echo " TESTING FULL PRODUCT SYNC WITH FILE COPYING      \n";
echo "==================================================\n\n";

$res = \Core\ProductSyncService::syncProduct(3501, 'jewellery', 'manual');
print_r($res);

$childPdo = \Core\ProductSyncService::getChildPdo();
$stmt = $childPdo->prepare("SELECT id, name, sku, price, sale_price, main_image FROM products WHERE sku = 'KP87ON'");
$stmt->execute();
$prod = $stmt->fetch();

echo "\nChild Product (ID {$prod['id']}):\n";
print_r($prod);

$stmtImg = $childPdo->prepare("SELECT * FROM product_images WHERE product_id = " . $prod['id']);
$stmtImg->execute();
$images = $stmtImg->fetchAll();
echo "\nChild Gallery Images (" . count($images) . " rows):\n";
print_r($images);
