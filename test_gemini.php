<?php
/**
 * Quick test script for AI API connectivity.
 * Run via browser: http://localhost/sri/admin/new_admin/test_gemini.php
 */
header('Content-Type: text/plain');

echo "=== AI Chatbot API Connectivity Test ===\n\n";

// 1. Check cURL
echo "1. cURL extension: " . (function_exists('curl_init') ? 'YES ✓' : 'NO ✗') . "\n";

// 2. Load config
$config = include(__DIR__ . '/Config/chatbot_config.php');
$provider = $config['provider'] ?? 'groq';
$pc = $config[$provider] ?? [];

echo "2. Provider: " . strtoupper($provider) . "\n";
echo "3. Model: " . ($pc['model'] ?? 'unknown') . "\n";
echo "4. API Key: " . (strlen($pc['api_key'] ?? '') > 10 ? substr($pc['api_key'], 0, 10) . '...' : 'NOT SET') . "\n";
echo "5. Endpoint: " . ($pc['endpoint'] ?? 'unknown') . "\n\n";

// 3. Build test request based on provider
if ($provider === 'gemini') {
    $url = $pc['endpoint'] . $pc['model'] . ':generateContent?key=' . $pc['api_key'];
    $payload = json_encode([
        'contents' => [['role' => 'user', 'parts' => [['text' => 'Say hello in one sentence.']]]]
    ]);
    $headers = ['Content-Type: application/json'];
} else {
    // OpenAI-compatible (Groq, OpenRouter)
    $url = $pc['endpoint'];
    $payload = json_encode([
        'model' => $pc['model'],
        'messages' => [['role' => 'user', 'content' => 'Say hello in one sentence.']],
        'max_tokens' => 50
    ]);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $pc['api_key']
    ];
    if ($provider === 'openrouter') {
        $headers[] = 'HTTP-Referer: http://localhost';
    }
}

echo "6. Sending test request...\n";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "7. HTTP Code: " . $httpCode . "\n";

if ($curlError) {
    echo "8. cURL Error: " . $curlError . "\n";
} else {
    echo "8. cURL Error: None ✓\n";
    
    $decoded = json_decode($response, true);
    
    // Check Gemini response format
    if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
        echo "\n✅ SUCCESS! AI responded:\n";
        echo '   "' . $decoded['candidates'][0]['content']['parts'][0]['text'] . "\"\n";
        echo "\nYour chatbot is ready to go!\n";
    }
    // Check OpenAI-compatible response format
    elseif (isset($decoded['choices'][0]['message']['content'])) {
        echo "\n✅ SUCCESS! AI responded:\n";
        echo '   "' . $decoded['choices'][0]['message']['content'] . "\"\n";
        echo "\nYour chatbot is ready to go!\n";
    }
    // Error
    elseif (isset($decoded['error'])) {
        echo "\n❌ API Error:\n";
        echo "   Code: " . ($decoded['error']['code'] ?? $decoded['error']['type'] ?? 'unknown') . "\n";
        echo "   Message: " . ($decoded['error']['message'] ?? 'unknown') . "\n";
    }
    else {
        echo "\n⚠️ Unexpected response:\n";
        echo substr($response, 0, 500) . "\n";
    }
}
