<?php
$con = mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
if (!$con) { die("Connection failed: " . mysqli_connect_error()); }

$sql = "CREATE TABLE IF NOT EXISTS ai_playground_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    context_size VARCHAR(100) DEFAULT '',
    context_details TEXT,
    generated_data LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $sql)) {
    echo "Table ai_playground_history created successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}

mysqli_close($con);
