<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../Core/Database.php';

$con = \Core\Database::getConnection('con');

$ids = [2634, 2639, 2642, 2646, 2636, 2638, 2644, 2622, 2623, 2626, 2627, 2624, 2628, 2640, 2635, 2609, 2615, 2611, 2613, 2619];
$ids_str = implode(',', $ids);

$query = "SELECT gproduct_id, gproduct_name, gproduct_code FROM garment_product WHERE gproduct_id IN ($ids_str)";
$res = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($res)) {
    echo "Garment ID: " . $row['gproduct_id'] . " | Code: " . $row['gproduct_code'] . "\n";
    $img_query = "SELECT * FROM product_images_new WHERE gproduct_id = " . $row['gproduct_id'] . " OR product_id = " . $row['gproduct_id'] . " OR pro_code = '" . $row['gproduct_code'] . "'";
    $img_res = mysqli_query($con, $img_query);
    $found = false;
    while ($img_row = mysqli_fetch_assoc($img_res)) {
        $found = true;
        echo "   -> Image ID: " . $img_row['id'] . " | gproduct_id: " . $img_row['gproduct_id'] . " | product_id: " . $img_row['product_id'] . " | pro_code: " . $img_row['pro_code'] . " | img_name: " . $img_row['img_name'] . "\n";
    }
    if (!$found) {
        echo "   -> NO IMAGES FOUND AT ALL!\n";
    }
}
