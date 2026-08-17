<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Core/ProductSyncService.php';
require_once __DIR__ . '/../Models/ProductModel.php';

$model = new \Models\ProductModel();
$con = \Core\Database::getConnection('con');

$gid = 2642;
$sampleColors = ["Maroon", "Gold", "Rose Gold"];
$jsonColors = json_encode($sampleColors);

mysqli_query($con, "UPDATE garment_product SET brand_color = '" . mysqli_real_escape_string($con, $jsonColors) . "' WHERE gproduct_id = $gid");

echo "Updated garment_product ID 2642 with colors: $jsonColors\n";
