<?php
$key = 'AQ.Ab8RN6LbpLTcmpeyCAlDMe3qQvN77N1iCWNGWi9yCpzn066y9A';
$model = 'gemini-2.5-flash';
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
curl_close($ch);

echo "Standard Gemini API Test:\n";
echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Response: " . $response . "\n\n";
