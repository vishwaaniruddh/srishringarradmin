<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Database.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Model.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Models/ProductModel.php';

$model = new \Models\ProductModel();

$query = "(SELECT product_id as id, product_code as code, 'jewellery' as type FROM product)
          UNION ALL
          (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type FROM garment_product)
          ORDER BY id DESC LIMIT 5";

$result = $model->query($model->getDbConnection(), $query);

if (!$result) {
    die("Query failed: " . mysqli_error($model->getDbConnection()));
}

while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
    
    // Test getting details
    $full = $model->getProductById($row['id'], $row['type']);
    echo "Full product " . $row['id'] . " fetched: " . ($full ? "Yes" : "No") . "\n";
}

echo "Done\n";
