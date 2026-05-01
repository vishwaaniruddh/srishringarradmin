<?php
header('Content-Type: text/plain');
echo "=== Listing Available Gemini Models ===\n\n";

$config = include(__DIR__ . '/Config/chatbot_config.php');
$key = $config['gemini_api_key'];
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $key;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['models'])) {
        echo "Available models that support generateContent:\n";
        echo str_repeat("-", 60) . "\n";
        foreach ($data['models'] as $model) {
            $methods = $model['supportedGenerationMethods'] ?? [];
            if (in_array('generateContent', $methods)) {
                $name = str_replace('models/', '', $model['name']);
                echo "  ✓ " . $name . "\n";
            }
        }
    }
} else {
    echo "Error: " . $response . "\n";
}
