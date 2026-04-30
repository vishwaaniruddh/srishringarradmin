<?php
require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Database.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Model.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Models/ProductModel.php';

$model = new \Models\ProductModel();

$query = "(SELECT product_id as id, product_code as code, 'jewellery' as type FROM product)
          UNION ALL
          (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type FROM garment_product)";
$result = $model->query($model->getDbConnection(), $query);
echo "Total products: " . mysqli_num_rows($result) . "\n";
