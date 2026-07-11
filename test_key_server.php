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

echo "PHP Version: " . PHP_VERSION . "\n";
echo "cURL Enabled: " . (function_exists('curl_init') ? 'yes' : 'no') . "\n";

// Get server's outgoing IP
$ip_ch = curl_init('https://api.ipify.org');
curl_setopt($ip_ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ip_ch, CURLOPT_SSL_VERIFYPEER, false);
$server_ip = trim(curl_exec($ip_ch));
curl_close($ip_ch);
echo "Server Outgoing IP: " . $server_ip . "\n\n";

// Get request headers sent to httpbin
echo "--- Outgoing Headers (httpbin) ---\n";
$hb_ch = curl_init('https://httpbin.org/headers');
curl_setopt($hb_ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($hb_ch, CURLOPT_SSL_VERIFYPEER, false);
$hb_res = curl_exec($hb_ch);
curl_close($hb_ch);
echo $hb_res . "\n\n";

$model = 'gemini-flash-latest';
$endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $key;

$payload = [
    'contents' => [
        ['parts' => [['text' => 'hello']]]
    ]
];

// Test 1: cURL request
echo "--- Test 1: cURL ---\n";
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$err = curl_error($ch);
@curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
echo "cURL Error: " . ($err ?: 'None') . "\n";
echo "Response: " . $response . "\n\n";

// Test 2: file_get_contents request
echo "--- Test 2: file_get_contents ---\n";
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($payload),
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);
$fgc_response = @file_get_contents($endpoint, false, $context);
echo "Response: " . $fgc_response . "\n";

