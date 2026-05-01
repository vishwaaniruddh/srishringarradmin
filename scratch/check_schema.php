<?php
$con = mysqli_connect("localhost", "reporting", "reporting", "u464193275_srishrinjewels");
if (!$con) die("Connection failed: " . mysqli_connect_error());

function describe($con, $table) {
    echo "\n--- $table ---\n";
    $res = mysqli_query($con, "DESCRIBE $table");
    if ($res) {
        while($row = mysqli_fetch_assoc($res)) {
            echo "{$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
}

describe($con, 'product');
describe($con, 'garment_product');
describe($con, 'product_images_new');
