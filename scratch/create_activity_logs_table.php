<?php
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/../Core/Database.php';

use Core\Database;

$con = Database::getConnection('con');
if (!$con) {
    echo "Connection to 'con' database failed.\n";
    exit(1);
}

$sql = "CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    admin_username VARCHAR(100) NULL,
    controller VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    request_method VARCHAR(10) NOT NULL,
    request_uri TEXT NOT NULL,
    get_params TEXT NULL,
    post_params TEXT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_admin_id (admin_id),
    KEY idx_controller_action (controller, action),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($con, $sql)) {
    echo "Table 'admin_activity_logs' created successfully (or already exists).\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}
