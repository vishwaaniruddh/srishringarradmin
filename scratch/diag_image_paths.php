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

$con = \Core\Database::getConnection('con');
$model = new \Models\ProductModel();

$productId = 3501; // SKU KP87ON
$productType = 'jewellery';

$parentImages = $model->getProductImages($productId, $productType);
echo "=== PARENT IMAGES FOR PRODUCT 3501 (KP87ON) ===\n";
print_r($parentImages);

foreach ($parentImages as $img) {
    $rawPath = is_array($img) ? ($img['img_name'] ?? '') : (string)$img;
    echo "\nRaw Path: $rawPath\n";

    // Test file locations on disk
    $testPaths = [
        'C:/xampp/htdocs/yn/uploads/' . ltrim($rawPath, '/'),
        'C:/xampp/htdocs/yn/' . ltrim($rawPath, '/'),
        'C:/xampp/htdocs/ss/' . ltrim($rawPath, '/'),
        'C:/xampp/htdocs/ss/uploads/' . ltrim($rawPath, '/'),
        'https://srishringarr.com/yn/uploads/' . ltrim($rawPath, '/'),
        'https://srishringarr.com/' . ltrim($rawPath, '/'),
    ];

    foreach ($testPaths as $tp) {
        if (str_starts_with($tp, 'http')) {
            echo "   [URL] $tp\n";
        } else {
            echo "   [FILE " . (file_exists($tp) ? "EXISTS" : "NOT FOUND") . "] $tp\n";
        }
    }
}
