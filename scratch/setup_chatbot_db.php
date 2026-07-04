<?php
$con = mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
if (!$con) { die("Connection failed"); }

// Create table
$sql = "CREATE TABLE IF NOT EXISTS chatbot_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(50) DEFAULT 'gemini',
    groq_key VARCHAR(255) DEFAULT '',
    gemini_key VARCHAR(255) DEFAULT '',
    openrouter_key VARCHAR(255) DEFAULT '',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if (mysqli_query($con, $sql)) {
    echo "Table created successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}

// Migrate from secrets.php if it exists and table is empty
$secretsFile = __DIR__ . '/../Config/secrets.php';
if (file_exists($secretsFile)) {
    $secrets = include($secretsFile);
    
    $check = mysqli_query($con, "SELECT COUNT(*) as count FROM chatbot_settings");
    $row = mysqli_fetch_assoc($check);
    
    if ($row['count'] == 0) {
        $provider = $secrets['CHATBOT_PROVIDER'] ?? 'gemini';
        $groqKey = $secrets['GROQ_API_KEY'] ?? '';
        $geminiKey = $secrets['GEMINI_API_KEY'] ?? '';
        $openrouterKey = $secrets['OPENROUTER_API_KEY'] ?? '';
        
        $stmt = $con->prepare("INSERT INTO chatbot_settings (provider, groq_key, gemini_key, openrouter_key) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $provider, $groqKey, $geminiKey, $openrouterKey);
        if ($stmt->execute()) {
            echo "Migrated existing keys to database.\n";
        } else {
            echo "Failed to migrate keys: " . $stmt->error . "\n";
        }
    } else {
        echo "Table already seeded.\n";
    }
} else {
    echo "No secrets.php found to migrate.\n";
}
