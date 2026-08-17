<?php
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');

echo "=== Sample from product_color ===\n";
$res = mysqli_query($con, "SELECT * FROM product_color LIMIT 20");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "=== Distinct colors in product_color ===\n";
$res2 = mysqli_query($con, "SELECT DISTINCT color, count(*) as c FROM product_color GROUP BY color ORDER BY c DESC");
while ($row = mysqli_fetch_assoc($res2)) {
    echo $row['color'] . " : " . $row['c'] . "\n";
}
