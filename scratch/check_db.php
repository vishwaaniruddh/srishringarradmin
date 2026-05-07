<?php
require_once 'Core/Database.php';
use Core\Database;

$con = Database::getConnection('con');
if (!$con) {
    echo "Failed to connect to database 'con'\n";
    exit;
}

$res = mysqli_query($con, "SHOW TABLES");
echo "Tables in 'con' database:\n";
while ($row = mysqli_fetch_array($res)) {
    echo $row[0] . "\n";
}
