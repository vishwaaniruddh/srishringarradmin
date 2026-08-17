<?php
require_once __DIR__ . '/../Core/Database.php';
$secrets = include(__DIR__ . '/../Config/secrets.php');
$apiKey = $secrets['GEMINI_API_KEY'] ?? '';

$con = \Core\Database::getConnection('con');
$res = mysqli_query($con, "SELECT pin.img_name, p.product_id, p.product_name, p.product_code 
                           FROM product_images_new pin 
                           JOIN product p ON pin.product_id = p.product_id 
                           WHERE pin.img_name != '' 
                           ORDER BY p.product_id DESC 
                           LIMIT 3");

while ($row = mysqli_fetch_assoc($res)) {
    echo "Product: {$row['product_id']} ({$row['product_code']}) - Image: {$row['img_name']}\n";
    $imgPath = $row['img_name'];
    $localPath = __DIR__ . '/../../yn/uploads' . $imgPath;
    $imgContent = null;
    $mimeType = 'image/jpeg';

    if (file_exists($localPath)) {
        $imgContent = file_get_contents($localPath);
        $mime = mime_content_type($localPath);
        if ($mime)
            $mimeType = $mime;
        echo "Found locally: $localPath\n";
    } else {
        $remoteUrl = 'https://srishringarr.com/yn/uploads' . $imgPath;
        $imgContent = @file_get_contents($remoteUrl);
        echo "Fetched remotely: $remoteUrl\n";
    }

    if ($imgContent) {
        $base64Image = base64_encode($imgContent);
        $prompt = "You are an expert Indian fashion & jewelry color analyst. Analyze the jewelry or apparel item in this image. " .
            "Identify 1 to 4 dominant and accent color names for this item from standard fashion/jewelry terminology " .
            "(such as Gold, Antique Gold, Rose Gold, Silver, Red, Maroon, Ruby, Green, Emerald Green, Mint Green, Pink, Baby Pink, Fuchsia Pink, White, Off White, Kundan, Yellow, Mustard, Blue, Navy Blue, Royal Blue, Turquoise, Peach, Black, Multicolor, etc.). " .
            "Return ONLY a raw JSON array of strings containing the color names (e.g. [\"Gold\", \"Emerald Green\"]). " .
            "Do NOT wrap in markdown formatting (no ```json or ```).";

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;
        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64Image
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
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        echo "HTTP Code: $httpCode\n";
        $dec = json_decode($response, true);
        $rawText = $dec['candidates'][0]['content']['parts'][0]['text'] ?? '';
        echo "Gemini Raw Output: $rawText\n";

        $clean = trim(preg_replace('/^```json|```$/i', '', trim($rawText)));
        $colors = json_decode($clean, true);
        echo "Parsed Colors: " . print_r($colors, true) . "\n------------------------\n";
    }
}
