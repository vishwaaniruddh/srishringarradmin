<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Models\ProductModel;

$con = Database::getConnection('con');
$model = new ProductModel();

echo "=== Testing Auto-Fix Logic for Unmapped Products in ss DB ===\n";

$resJ = mysqli_query($con, "SELECT p.product_id, p.product_code, p.product_name, p.categories_id, p.subcat_id 
                           FROM product p 
                           WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");

$fixedCount = 0;
while ($r = mysqli_fetch_assoc($resJ)) {
    $pid = (int)$r['product_id'];
    $sku = $r['product_code'];
    $cat = (int)$r['categories_id'];
    $sub = (int)$r['subcat_id'];

    if ($cat <= 0 && $sub <= 0) {
        $upperSku = strtoupper($sku);
        if (str_starts_with($upperSku, 'BR')) {
            $cat = 22; // BRACELET
        } elseif (str_starts_with($upperSku, 'JU')) {
            $cat = 15; // KAMAR PATTA
        } elseif (str_starts_with($upperSku, 'K')) {
            $cat = 1;  // Necklace Sets
            $sub = 3;  // Kundan
        } elseif (str_starts_with($upperSku, 'EAR')) {
            $cat = 17; // Earrings
        } else {
            $cat = 1;  // Default Necklace Sets
        }
    }

    echo "Fixing Jewellery #$pid [$sku]: Main Cat=$cat, Sub Cat=$sub\n";
    $model->saveProductCategories($pid, 'jewellery', $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
    $fixedCount++;
}

echo "\nFixed Jewellery Count: $fixedCount\n";

// Re-check unmapped count
$resCheck = mysqli_query($con, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
$rem = mysqli_fetch_assoc($resCheck)['cnt'];
echo "Remaining unmapped Jewellery: $rem\n";
