<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

$con = Database::getConnection('con');

if ($con) {
    $res = mysqli_query($con, "DESCRIBE ai_analytics");
    echo "=== ai_analytics table columns ===\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
    }
}
