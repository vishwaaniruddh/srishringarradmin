<?php
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');

$res = mysqli_query($con, "SELECT id, TRIM(color) as color_name, status FROM brand_color WHERE color != '' AND color NOT LIKE '%crawler%' AND color NOT LIKE '%74.125%' AND color NOT LIKE '%addmin%' ORDER BY TRIM(color) ASC");
$cleanColors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $c = trim($row['color_name']);
    if (!empty($c) && !in_array(strtolower($c), array_map('strtolower', $cleanColors))) {
        $cleanColors[] = ucwords(strtolower($c));
    }
}
sort($cleanColors);
echo "Cleaned predefined colors count: " . count($cleanColors) . "\n";
print_r($cleanColors);
