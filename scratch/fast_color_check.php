<?php
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');

echo "=== Distinct brand_color in product table ===\n";
$res = mysqli_query($con, "SELECT DISTINCT brand_color, COUNT(*) as cnt FROM product GROUP BY brand_color");
while ($row = mysqli_fetch_assoc($res)) {
    echo "'" . $row['brand_color'] . "' => " . $row['cnt'] . "\n";
}

echo "\n=== Distinct brand_color in garment_product table ===\n";
$res2 = mysqli_query($con, "SELECT DISTINCT brand_color, COUNT(*) as cnt FROM garment_product GROUP BY brand_color");
while ($row = mysqli_fetch_assoc($res2)) {
    echo "'" . $row['brand_color'] . "' => " . $row['cnt'] . "\n";
}

echo "\n=== All rows from brand_color table ===\n";
$res3 = mysqli_query($con, "SELECT * FROM brand_color ORDER BY id");
$colors = [];
while ($row = mysqli_fetch_assoc($res3)) {
    $colors[] = $row;
}
echo json_encode($colors, JSON_PRETTY_PRINT);

echo "\n=== Total rows in product_color ===\n";
$res4 = mysqli_query($con, "SELECT count(*) FROM product_color");
$row4 = mysqli_fetch_row($res4);
echo "product_color count: " . $row4[0] . "\n";
