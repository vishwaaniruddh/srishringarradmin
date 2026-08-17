<?php
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');

$q1 = mysqli_query($con, "ALTER TABLE product MODIFY brand_color VARCHAR(500)");
echo "Alter product: " . ($q1 ? "SUCCESS" : mysqli_error($con)) . "\n";

$q2 = mysqli_query($con, "ALTER TABLE garment_product MODIFY brand_color VARCHAR(500)");
echo "Alter garment_product: " . ($q2 ? "SUCCESS" : mysqli_error($con)) . "\n";
