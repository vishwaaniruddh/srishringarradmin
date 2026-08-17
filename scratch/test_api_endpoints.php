<?php
echo "======================================================\n";
echo "1. Testing API/v1/products.php Listing Endpoint\n";
echo "======================================================\n";

$_GET = [
    'category' => 'garment:10',
    'type' => 'garments',
    'page' => '1',
    'min_price' => '0',
    'max_price' => '500000',
    'sort' => 'sku_desc'
];

ob_start();
require __DIR__ . '/../../API/v1/products.php';
$productsOutput = ob_get_clean();

$productsJson = json_decode($productsOutput, true);
echo "Status: " . ($productsJson['status'] ?? 'unknown') . "\n";
echo "Total Products returned: " . count($productsJson['data'] ?? []) . "\n";

if (!empty($productsJson['data'])) {
    $first = $productsJson['data'][0];
    echo "\nSample Product in Listing:\n";
    echo "  - ID: {$first['id']}\n";
    echo "  - Name: {$first['name']}\n";
    echo "  - Code: {$first['code']}\n";
    echo "  - Type: {$first['type']}\n";
    echo "  - Raw brand_color: " . ($first['brand_color'] ?? '(none)') . "\n";
    echo "  - Parsed colors array: " . json_encode($first['colors'] ?? []) . "\n";
}

echo "\n======================================================\n";
echo "2. Testing API/v1/product-detail.php Detail Endpoint\n";
echo "======================================================\n";

$_GET = [
    'id' => '2642',
    'type' => 'garments'
];

ob_start();
require __DIR__ . '/../../API/v1/product-detail.php';
$detailOutput = ob_get_clean();

$detailJson = json_decode($detailOutput, true);
echo "Status: " . ($detailJson['status'] ?? 'unknown') . "\n";

if (!empty($detailJson['data'])) {
    $detail = $detailJson['data'];
    echo "\nProduct Detail Data for ID 2642:\n";
    echo "  - ID: {$detail['id']}\n";
    echo "  - Name: {$detail['name']}\n";
    echo "  - Code: {$detail['code']}\n";
    echo "  - Type: {$detail['type']}\n";
    echo "  - Raw brand_color: " . ($detail['brand_color'] ?? '(none)') . "\n";
    echo "  - Parsed colors array: " . json_encode($detail['colors'] ?? []) . "\n";
} else {
    echo "Detail error/message: " . ($detailJson['message'] ?? 'none') . "\n";
}
