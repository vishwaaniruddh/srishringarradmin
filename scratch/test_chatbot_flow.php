<?php
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Instantiate and test Chatbot Model
$chatbot = new \Models\ChatbotModel();
$userMessage = "hi";
$history = [];
$imageBase64 = null;

$response = $chatbot->processMessage($userMessage, $history, $imageBase64);
echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
