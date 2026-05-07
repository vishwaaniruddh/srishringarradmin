<?php
require_once 'Core/Database.php';
use Core\Database;

$con = Database::getConnection('con');
if (!$con) {
    echo "Failed to connect to database 'con'\n";
    exit;
}

$queries = [
    "CREATE TABLE IF NOT EXISTS discount_rules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        scope VARCHAR(50) NOT NULL,
        target TEXT,
        type ENUM('percentage', 'flat') NOT NULL,
        value DECIMAL(10,2) NOT NULL,
        weight INT DEFAULT 0,
        threshold DECIMAL(10,2) NULL,
        threshold_max DECIMAL(10,2) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS discount_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )"
];

foreach ($queries as $sql) {
    if (mysqli_query($con, $sql)) {
        echo "Successfully executed: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "Error executing query: " . mysqli_error($con) . "\n";
    }
}

// Initial settings
$initial_settings = [
    'da_badge_position' => 'top_left',
    'da_show_on_shop' => '1',
    'da_show_on_archive' => '1',
    'da_show_on_single' => '1',
    'da_show_on_related' => '1',
    'da_show_on_search' => '1',
    'da_show_on_cart' => '1'
];

foreach ($initial_settings as $key => $val) {
    $key = mysqli_real_escape_string($con, $key);
    $val = mysqli_real_escape_string($con, $val);
    mysqli_query($con, "INSERT IGNORE INTO discount_settings (setting_key, setting_value) VALUES ('$key', '$val')");
}
