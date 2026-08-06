<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;

echo "=== Testing Complete SKU Inference Rules ===\n";

$testSkus = [
    'set824' => 'Necklace Sets -> American Diamond (Cat 1, Sub 2)',
    'set500on' => 'Necklace Sets -> American Diamond (Cat 1, Sub 2)',
    'set449' => 'Necklace Sets -> American Diamond (Cat 1, Sub 2)',
    'k196' => 'Necklace Sets -> Kundan (Cat 1, Sub 3)',
    'K2049' => 'Necklace Sets -> Kundan (Cat 1, Sub 3)',
    'BR41CS' => 'Bracelet (Cat 22)',
    'JU29' => 'Kamar Patta (Cat 15)',
    'EAR940C' => 'Earrings (Cat 17, Sub 59)',
    'LEH101' => 'Lehenga Choli (Cat 10)'
];

foreach ($testSkus as $sku => $expected) {
    $res = ProductSyncService::inferCategoryFromSku($sku, str_starts_with($sku, 'LEH') ? 'garments' : 'jewellery');
    echo "SKU: '$sku' => Cat: {$res['category_id']}, Sub: {$res['subcategory_id']} (Expected: $expected)\n";
}
