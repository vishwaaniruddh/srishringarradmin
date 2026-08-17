<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Core/ProductSyncService.php';
require_once __DIR__ . '/../Models/ProductModel.php';

$model = new \Models\ProductModel();
$con = \Core\Database::getConnection('con');

// Find a product with images
$res = mysqli_query($con, "SELECT pin.product_id, p.product_name, p.product_code 
                           FROM product_images_new pin 
                           JOIN product p ON pin.product_id = p.product_id 
                           WHERE pin.img_name != '' 
                           ORDER BY p.product_id DESC 
                           LIMIT 1");
$row = mysqli_fetch_assoc($res);
$targetId = (int)$row['product_id'];

echo "=== Testing on Product ID: $targetId ({$row['product_name']}) ===\n";

// Step 1: Explicitly clear brand_color in DB first
mysqli_query($con, "UPDATE product SET brand_color = '' WHERE product_id = $targetId");
$before = $model->getProductById($targetId, 'jewellery');
echo "brand_color before update: '" . ($before['brand_color'] ?? '') . "'\n";
echo "Parsed colors before update: " . json_encode($before['colors']) . "\n";

// Step 2: Call updateProduct with EMPTY colors array (simulate form submitted with no colors selected)
$updateData = [
    'name' => $before['name'],
    'description' => $before['description'] ?? '',
    'category' => $before['category'] ?? 1,
    'sub_category' => $before['sub_category'] ?? 1,
    's_price' => $before['s_price'] ?? 1000,
    'rental_price' => $before['rental_price'] ?? 500,
    'deposit' => $before['deposit'] ?? 300,
    'featured' => 0,
    'price_source' => 'manual',
    'availability' => 'both',
    'size_avail' => '',
    'brand_name' => '',
    'colors' => [], // <-- INTENTIONALLY EMPTY!
    'code' => $before['code']
];

echo "\nCalling updateProduct() with empty colors array...\n";
$model->updateProduct('jewellery', $targetId, $updateData, []);

// Step 3: Fetch updated product from DB
$after = $model->getProductById($targetId, 'jewellery');
echo "\n=== Verification After Auto-Detect Update ===\n";
echo "brand_color raw in DB: " . $after['brand_color'] . "\n";
echo "Parsed colors array: " . json_encode($after['colors']) . "\n";

if (!empty($after['colors']) && is_array($after['colors'])) {
    echo "\n>>> SUCCESS: Gemini image analysis automatically detected and saved colors: " . implode(', ', $after['colors']) . " <<<\n";
} else {
    echo "\n>>> FAILED: Colors were not automatically detected. <<<\n";
}
