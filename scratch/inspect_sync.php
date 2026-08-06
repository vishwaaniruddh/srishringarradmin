<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Models\ProductModel;
use Core\ProductSyncService;

$con = Database::getConnection('con');

echo "=== Sync Settings ===\n";
$settings = ProductSyncService::getSyncSettings();
print_r($settings);

echo "\n=== Searching SKU T1087 ===\n";
$res = mysqli_query($con, "SELECT * FROM product WHERE product_code = 'T1087'");
if ($res && $row = mysqli_fetch_assoc($res)) {
    print_r($row);
    
    $model = new ProductModel();
    $fullProd = $model->getProductById($row['product_id'], 'jewellery');
    echo "\n=== Full Product from ProductModel ===\n";
    print_r($fullProd);

    $assigned = $model->getProductAssignedCategories($row['product_id'], 'jewellery');
    echo "\n=== Assigned Categories ===\n";
    print_r($assigned);

    $isEnabled = ProductSyncService::isCategoryEnabled('jewellery', $fullProd);
    echo "\n=== Is Category Enabled? ===\n";
    var_dump($isEnabled);
} else {
    echo "SKU T1087 not found in product table. Searching garment_product...\n";
    $resG = mysqli_query($con, "SELECT * FROM garment_product WHERE gproduct_code = 'T1087'");
    if ($resG && $rowG = mysqli_fetch_assoc($resG)) {
        print_r($rowG);
        $model = new ProductModel();
        $fullProd = $model->getProductById($rowG['gproduct_id'], 'garments');
        print_r($fullProd);
        $isEnabled = ProductSyncService::isCategoryEnabled('garments', $fullProd);
        var_dump($isEnabled);
    } else {
        echo "SKU T1087 not found in garment_product either.\n";
    }
}
