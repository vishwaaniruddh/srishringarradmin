<?php
include __DIR__ . '/../../config.php';
global $con;

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$tables = ['product', 'garment_product'];
foreach ($tables as $table) {
    $check = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE 'featured'");
    if (!$check) {
        echo "Error checking $table: " . mysqli_error($con) . "\n";
        continue;
    }
    if (mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE `$table` ADD `featured` TINYINT(1) DEFAULT 0";
        if (mysqli_query($con, $sql)) {
            echo "Added 'featured' column to $table\n";
        } else {
            echo "Error adding column to $table: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "'featured' column already exists in $table\n";
    }
}

mysqli_close($con);
?>
