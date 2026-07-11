<?php
header('Content-Type: text/plain');

$secrets = @include __DIR__ . '/Config/secrets.php';
if (!$secrets) {
    echo "Failed to load secrets.php\n";
    exit;
}

$key = $secrets['GEMINI_API_KEY'] ?? '';
echo "Key Length: " . strlen($key) . "\n";
echo "Key Masked: " . substr($key, 0, 10) . "..." . substr($key, -5) . "\n";

$model = 'gemini-flash-latest';
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $key;

$payload = [
    'contents' => [
        ['parts' => [['text' => 'hello']]]
    ]
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
@curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Response: " . $response . "\n";
