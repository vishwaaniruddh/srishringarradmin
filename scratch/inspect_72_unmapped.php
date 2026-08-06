<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

$con = Database::getConnection('con');

echo "=== Inspecting Unmapped Parent Jewellery Products ===\n";
$resJ = mysqli_query($con, "SELECT p.product_id, p.product_code, p.product_name, p.categories_id, p.subcat_id 
                           FROM product p 
                           WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");

echo "Count unmapped Jewellery: " . mysqli_num_rows($resJ) . "\n";
while ($r = mysqli_fetch_assoc($resJ)) {
    echo "ID {$r['product_id']} | Code: '{$r['product_code']}' | Name: '{$r['product_name']}' | Cat: {$r['categories_id']} | Sub: {$r['subcat_id']}\n";
}

echo "\n=== Inspecting Unmapped Parent Garment Products ===\n";
$resG = mysqli_query($con, "SELECT gp.gproduct_id, gp.gproduct_code, gp.gproduct_name, gp.garment_id, gp.product_for 
                           FROM garment_product gp 
                           WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
echo "Count unmapped Garments: " . mysqli_num_rows($resG) . "\n";
while ($r = mysqli_fetch_assoc($resG)) {
    echo "ID {$r['gproduct_id']} | Code: '{$r['gproduct_code']}' | Name: '{$r['gproduct_name']}' | Cat: {$r['garment_id']} | Sub: {$r['product_for']}\n";
}
