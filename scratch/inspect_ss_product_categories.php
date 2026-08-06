<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

$con = Database::getConnection('con');

echo "=== product_categories table schema in ss DB ===\n";
$res = mysqli_query($con, "DESCRIBE product_categories");
while ($r = mysqli_fetch_assoc($res)) {
    echo "{$r['Field']} - {$r['Type']} (Null: {$r['Null']}, Key: {$r['Key']}, Default: {$r['Default']})\n";
}

echo "\n=== Sample records in product_categories ===\n";
$resSample = mysqli_query($con, "SELECT * FROM product_categories LIMIT 10");
while ($r = mysqli_fetch_assoc($resSample)) {
    print_r($r);
}

echo "\n=== Products in 'product' without product_categories ===\n";
$resUnmappedJ = mysqli_query($con, "SELECT p.product_id, p.product_code, p.product_name, p.categories_id, p.subcat_id 
                                    FROM product p 
                                    WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
echo "Count Jewellery unmapped: " . mysqli_num_rows($resUnmappedJ) . "\n";
while ($r = mysqli_fetch_assoc($resUnmappedJ)) {
    print_r($r);
}

echo "\n=== Products in 'garment_product' without product_categories ===\n";
$resUnmappedG = mysqli_query($con, "SELECT gp.gproduct_id, gp.gproduct_code, gp.gproduct_name, gp.garment_id, gp.product_for 
                                    FROM garment_product gp 
                                    WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
echo "Count Garments unmapped: " . mysqli_num_rows($resUnmappedG) . "\n";
while ($r = mysqli_fetch_assoc($resUnmappedG)) {
    print_r($r);
}
