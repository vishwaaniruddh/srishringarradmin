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

echo "=== subcat1 record for ID 63 ===\n";
$res1 = mysqli_query($con, "SELECT * FROM subcat1 WHERE subcat_id = 63");
print_r(mysqli_fetch_assoc($res1));

echo "\n=== jewel_subcat record for ID 63 ===\n";
$res2 = mysqli_query($con, "SELECT * FROM jewel_subcat WHERE subcat_id = 63");
print_r(mysqli_fetch_assoc($res2));

echo "\n=== product record categories_id / subcat_id for T1087 ===\n";
$res3 = mysqli_query($con, "SELECT product_id, product_code, categories_id, subcat_id FROM product WHERE product_code = 'T1087'");
print_r(mysqli_fetch_assoc($res3));

echo "\n=== product_categories assigned for T1087 ===\n";
$res4 = mysqli_query($con, "SELECT * FROM product_categories WHERE product_code = 'T1087'");
while ($r = mysqli_fetch_assoc($res4)) {
    print_r($r);
}

echo "\n=== ALL Categories returned by ProductModel::getCategories() ===\n";
$model = new ProductModel();
$cats = $model->getCategories();
print_r($cats);
