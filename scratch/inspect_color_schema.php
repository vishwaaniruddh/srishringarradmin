<?php
require_once __DIR__ . '/../Core/Database.php';

$con = \Core\Database::getConnection('con');

echo "=== brand_color table schema & data ===\n";
$res = mysqli_query($con, "DESCRIBE brand_color");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
$res = mysqli_query($con, "SELECT * FROM brand_color LIMIT 30");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n=== product_color table schema & data ===\n";
$res = mysqli_query($con, "DESCRIBE product_color");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
$res = mysqli_query($con, "SELECT * FROM product_color LIMIT 20");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "\n=== Distinct non-empty brand_color in product ===\n";
$res = mysqli_query($con, "SELECT DISTINCT brand_color FROM product WHERE brand_color IS NOT NULL AND brand_color != '' LIMIT 20");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['brand_color'] . "\n";
}

echo "\n=== Distinct non-empty brand_color in garment_product ===\n";
$res = mysqli_query($con, "SELECT DISTINCT brand_color FROM garment_product WHERE brand_color IS NOT NULL AND brand_color != '' LIMIT 20");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['brand_color'] . "\n";
}

echo "\n=== Check product with product_id=11937 or check max product_id ===\n";
$res = mysqli_query($con, "SELECT product_id, product_code, product_name, brand_color, deposit FROM product ORDER BY product_id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
