<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

$con = Database::getConnection('con');

echo "=== jewel_subcat ===\n";
$res = mysqli_query($con, "SELECT subcat_id, categories_name FROM jewel_subcat");
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID {$r['subcat_id']}: {$r['categories_name']}\n";
}

echo "\n=== subcat1 (sample 15) ===\n";
$res = mysqli_query($con, "SELECT subcat_id, maincat_id, name FROM subcat1 LIMIT 15");
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID {$r['subcat_id']} (maincat_id {$r['maincat_id']}): {$r['name']}\n";
}

echo "\n=== garments ===\n";
$res = mysqli_query($con, "SELECT garment_id, name, Main_id FROM garments");
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID {$r['garment_id']} (Main_id {$r['Main_id']}): {$r['name']}\n";
}

echo "\n=== garment_subcat (sample 15) ===\n";
$res = mysqli_query($con, "SELECT sub_id, gmain_id, sub_name FROM garment_subcat LIMIT 15");
while ($r = mysqli_fetch_assoc($res)) {
    echo "ID {$r['sub_id']} (gmain_id {$r['gmain_id']}): {$r['sub_name']}\n";
}
