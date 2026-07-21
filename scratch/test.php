<?php
$secrets = include(__DIR__ . '/../Config/secrets.php');
$apiKey = $secrets['GEMINI_API_KEY'] ?? '';

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-image:generateContent?key=' . $apiKey;

$payload = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => 'A photo of a cute cat'],
                [
                    'inlineData' => [
                        'mimeType' => 'image/png',
                        'data' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
                    ]
                ]
            ]
        ]
    ]
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 45,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
@curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
