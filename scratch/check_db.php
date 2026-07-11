<?php
$_SERVER['HTTP_HOST'] = 'localhost';
require_once __DIR__ . '/../Core/Database.php';
use Core\Database;

$con = Database::getConnection('con');
if (!$con) {
    echo "Failed to connect to database 'con'\n";
    exit;
}

function describe($con, $table) {
    echo "\n--- DESCRIBE $table ---\n";
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
