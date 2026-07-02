<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');

$res = mysqli_query($con, "SELECT COUNT(*) FROM product_images_new WHERE gproduct_id > 0");
$row = mysqli_fetch_row($res);
echo "Rows with gproduct_id > 0: " . $row[0] . "\n";

$res = mysqli_query($con, "SELECT COUNT(*) FROM product_images_new WHERE product_id > 0 AND gproduct_id = 0");
$row = mysqli_fetch_row($res);
echo "Rows with product_id > 0 and gproduct_id = 0: " . $row[0] . "\n";
