<?php
$key = 'AQ.Ab8RN6L_7vSm0b3_a_MXpnKyOf_Fb2JTaKf3iYooybbZDEVBXg';
$model = 'gemini-2.5-flash';
$payload = [
    'contents' => [
        ['parts' => [['text' => 'hello']]]
    ]
];

// Test 3: Query Parameter ?key=
$endpoint3 = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $key;
$ch3 = curl_init($endpoint3);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_POST, true);
curl_setopt($ch3, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
$response3 = curl_exec($ch3);
$info3 = curl_getinfo($ch3);
curl_close($ch3);

echo "Test 3 (Query string ?key=):\n";
echo "HTTP Code: " . $info3['http_code'] . "\n";
echo "Response: " . $response3 . "\n\n";
