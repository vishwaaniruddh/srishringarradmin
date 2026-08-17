<?php
require_once __DIR__ . '/../../API/autoload.php';

$apiModel = new \API\Models\ProductModel();
$product = $apiModel->getProductById(11433, 'jewellery');

echo "=== API Product Detail Test ===\n";
echo "ID: " . $product['id'] . "\n";
echo "Name: " . $product['name'] . "\n";
echo "Raw brand_color: " . ($product['brand_color'] ?? '(none)') . "\n";
echo "Parsed colors: " . json_encode($product['colors'] ?? []) . "\n";

if (!empty($product['colors'])) {
    echo ">>> API COLOR TEST PASSED! <<<\n";
} else {
    echo ">>> API COLOR TEST FAILED <<<\n";
}
