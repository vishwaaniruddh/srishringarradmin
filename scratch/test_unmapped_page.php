<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Controllers\CategoryController;
use Core\Database;

$con = Database::getConnection('con');

echo "=== Unmapped Products Check ===\n";
if ($con) {
    $resJ = mysqli_query($con, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
    $cntJ = (int)(mysqli_fetch_assoc($resJ)['cnt'] ?? 0);

    $resG = mysqli_query($con, "SELECT COUNT(*) as cnt FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
    $cntG = (int)(mysqli_fetch_assoc($resG)['cnt'] ?? 0);

    echo "Unmapped Jewellery count: $cntJ\n";
    echo "Unmapped Garments count: $cntG\n";
    echo "Total Unmapped count: " . ($cntJ + $cntG) . "\n";
}
