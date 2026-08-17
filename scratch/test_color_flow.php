<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Core/ProductSyncService.php';
require_once __DIR__ . '/../Models/ProductModel.php';

$model = new \Models\ProductModel();

$con = \Core\Database::getConnection('con');
$res = mysqli_query($con, "SELECT product_id FROM product ORDER BY product_id DESC LIMIT 1");
$row = mysqli_fetch_assoc($res);
$targetId = (int)$row['product_id'];

$product = $model->getProductById($targetId, 'jewellery');
echo "Testing Product ID: $targetId ({$product['name']})\n";

$testColors = ["Gold", "Emerald Green", "Ruby Kundan"];
$updateData = [
    'name' => $product['name'],
    'description' => $product['description'] ?? '',
    'category' => $product['category'] ?? 1,
    'sub_category' => $product['sub_category'] ?? 1,
    's_price' => $product['s_price'] ?? 1000,
    'rental_price' => $product['rental_price'] ?? 500,
    'deposit' => $product['deposit'] ?? 300,
    'featured' => $product['featured'] ?? 0,
    'price_source' => 'manual',
    'availability' => 'both',
    'size_avail' => $product['size_avail'] ?? '',
    'brand_name' => $product['brand_name'] ?? '',
    'colors' => $testColors,
    'code' => $product['code'] ?? 'TEST1'
];

$model->updateProduct('jewellery', $targetId, $updateData, []);
$refetched = $model->getProductById($targetId, 'jewellery');

echo "Saved brand_color raw: " . $refetched['brand_color'] . "\n";
echo "Saved colors parsed: " . json_encode($refetched['colors']) . "\n";

if ($refetched['colors'] === $testColors) {
    echo ">>> ALL TESTS PASSED SUCCESSFULLY! <<<\n";
} else {
    echo ">>> TEST FAILED <<<\n";
}
