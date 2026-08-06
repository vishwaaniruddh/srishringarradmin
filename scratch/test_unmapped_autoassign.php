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

echo "=== Analyzing 47 Unmapped Jewellery Products ===\n";

$res = mysqli_query($con, "SELECT product_id, product_code, product_name, categories_id, subcat_id 
                           FROM product p 
                           WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");

$model = new ProductModel();
$countWithCat = 0;
$countZeroCat = 0;

while ($r = mysqli_fetch_assoc($res)) {
    $pid = (int)$r['product_id'];
    $sku = $r['product_code'];
    $cat = (int)$r['categories_id'];
    $sub = (int)$r['subcat_id'];

    if ($cat > 0 || $sub > 0) {
        $countWithCat++;
        echo "Product #$pid [$sku]: Has cat=$cat, sub=$sub\n";
    } else {
        $countZeroCat++;
        // Infer from SKU prefix
        $inferredCat = 0;
        $inferredSub = 0;
        $upperSku = strtoupper($sku);

        if (str_starts_with($upperSku, 'BR')) {
            $inferredCat = 22; // BRACELET
        } elseif (str_starts_with($upperSku, 'JU')) {
            $inferredCat = 15; // KAMAR PATTA
        } elseif (str_starts_with($upperSku, 'K')) {
            $inferredCat = 1;  // Necklace Sets
            $inferredSub = 3;  // Kundan
        } elseif (str_starts_with($upperSku, 'EAR')) {
            $inferredCat = 17; // Earrings
        }

        echo "Product #$pid [$sku] '{$r['product_name']}': Zero cat/sub -> Inferred Cat=$inferredCat, Sub=$inferredSub\n";
    }
}

echo "\nSummary:\nWith existing cat/sub: $countWithCat\nWith zero cat/sub: $countZeroCat\n";
