<?php
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/../Core/Database.php';

use Core\Database;

$con = Database::getConnection('con');
if (!$con) {
    echo "Connection failed.\n";
    exit(1);
}

// Add operation_type and generated_output to ai_analytics table if not present
$check1 = mysqli_query($con, "SHOW COLUMNS FROM ai_analytics LIKE 'operation_type'");
if (mysqli_num_rows($check1) == 0) {
    mysqli_query($con, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image' AFTER product_type");
    echo "Added operation_type column.\n";
} else {
    echo "operation_type column already exists.\n";
}

$check2 = mysqli_query($con, "SHOW COLUMNS FROM ai_analytics LIKE 'generated_output'");
if (mysqli_num_rows($check2) == 0) {
    mysqli_query($con, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL AFTER prompt_text");
    echo "Added generated_output column.\n";
} else {
    echo "generated_output column already exists.\n";
}

echo "Database Migration Complete.\n";
