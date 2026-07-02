<?php
$config = include __DIR__ . '/../Config/chatbot_config.php';
$apiKey = $config['groq']['api_key'];
$model = $config['groq']['model'];
$endpoint = $config['groq']['endpoint'];

$payload = [
    'model' => $model,
    'messages' => [
        ['role' => 'user', 'content' => 'hello']
    ]
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Response: " . $response . "\n";
