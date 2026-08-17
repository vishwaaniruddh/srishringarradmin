<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Models/ProductModel.php';

$model = new \Models\ProductModel();
$con = \Core\Database::getConnection('con');

$res = mysqli_query($con, "SELECT gproduct_id, gproduct_name, gproduct_code FROM garment_product ORDER BY gproduct_id DESC LIMIT 1");
$row = mysqli_fetch_assoc($res);
if ($row) {
    $gid = (int)$row['gproduct_id'];
    echo "Testing on Garment ID: $gid ({$row['gproduct_name']})\n";
    $product = $model->getProductById($gid, 'garments');
    
    $testColors = ["Navy Blue", "Silver", "Peach"];
    $updateData = [
        'name' => $product['name'] ?? 'Test Garment',
        'description' => $product['description'] ?? 'Test Desc',
        'category' => $product['category'] ?? 1,
        'sub_category' => $product['sub_category'] ?? 1,
        's_price' => $product['s_price'] ?? 2000,
        'rental_price' => $product['rental_price'] ?? 800,
        'deposit' => $product['deposit'] ?? 500,
        'featured' => 0,
        'price_source' => 'manual',
        'availability' => 'both',
        'size_avail' => 'M, L, XL',
        'brand_name' => 'Designer Studio',
        'colors' => $testColors,
        'code' => $product['code'] ?? 'G1'
    ];
    $model->updateProduct('garments', $gid, $updateData, []);
    
    $refetched = $model->getProductById($gid, 'garments');
    echo "Raw brand_color: " . $refetched['brand_color'] . "\n";
    echo "Parsed colors: " . json_encode($refetched['colors']) . "\n";
    if ($refetched['colors'] === $testColors) {
        echo ">>> GARMENT SUCCESS <<<\n";
    } else {
        echo ">>> GARMENT MISMATCH <<<\n";
    }
}
