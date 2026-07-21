<?php
namespace Controllers;

use Core\Controller;

class AiplaygroundController extends Controller {

    public function index() {
        $session_id = uniqid('sess_');
        $data = ['session_id' => $session_id];
        $this->view('ai_playground/index', $data);
    }

    private function getDbConnection() {
        $con = mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
        if (!$con) {
            return null;
        }
        return $con;
    }

    private function saveHistory($session_id, $type, $size, $info, $data) {
        if (empty($session_id)) return;
        $con = $this->getDbConnection();
        if (!$con) return;
        
        $sql = "INSERT INTO ai_playground_history (session_id, type, context_size, context_details, generated_data) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $session_id, $type, $size, $info, $data);
            $stmt->execute();
            $stmt->close();
        }
        mysqli_close($con);
    }

    private function getGeminiKey() {
        $secrets = include(__DIR__ . '/../Config/secrets.php');
        return $secrets['GEMINI_API_KEY'] ?? '';
    }

    public function generateNames() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $b64Images = $input['images'] ?? [];
        if (isset($input['image']) && !empty($input['image'])) {
            $b64Images[] = $input['image'];
        }
        $productInfo = $input['product_info'] ?? '';
        $size = $input['size'] ?? '';
        $session_id = $input['session_id'] ?? '';

        if (empty($b64Images)) {
            $this->json(['error' => 'At least one image is required'], 400);
            return;
        }

        $apiKey = $this->getGeminiKey();
        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured.'], 400);
            return;
        }

        $prompt = "You are a professional fashion copywriter for Srishringarr. " .
                  "Analyze the product in the image. " .
                  (!empty($productInfo) ? "Additional product details: $productInfo. " : "") .
                  (!empty($size) ? "Available size: $size. " : "") .
                  "Suggest exactly 5 descriptive product names (each name MUST be at least 10 words long). " .
                  "Use very simple, clear, and easy-to-understand English. Do NOT use complex, rare, fancy, flowery, or poetic words. " .
                  "Each name MUST have at least 10 words. " .
                  "Return ONLY a raw JSON array of strings containing the 5 suggested names. Do not include markdown code block formatting.";

        $response = $this->callGeminiText($apiKey, $prompt, $b64Images);
        if (isset($response['error'])) {
            $this->json(['error' => $response['error']], 500);
            return;
        }

        $text = $response['text'];
        $names = [];
        preg_match('/\[.*\]/s', $text, $matches);
        if (!empty($matches)) {
            $names = json_decode($matches[0], true);
        } else {
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = trim(preg_replace('/^[0-9\.\-\*]+/', '', $line));
                if (!empty($line)) {
                    $names[] = $line;
                }
            }
        }

        if (empty($names)) {
            $this->json(['error' => 'Failed to parse generated names.'], 500);
            return;
        }

        $names = array_slice($names, 0, 5);
        $this->saveHistory($session_id, 'names', $size, $productInfo, json_encode($names));
        $this->json(['success' => true, 'names' => $names]);
    }

    public function generateDescription() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $b64Images = $input['images'] ?? [];
        if (isset($input['image']) && !empty($input['image'])) {
            $b64Images[] = $input['image'];
        }
        $productInfo = $input['product_info'] ?? '';
        $size = $input['size'] ?? '';
        $maxWords = (int)($input['max_words'] ?? 100);
        $session_id = $input['session_id'] ?? '';

        if (empty($b64Images)) {
            $this->json(['error' => 'At least one image is required'], 400);
            return;
        }

        $apiKey = $this->getGeminiKey();
        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured.'], 400);
            return;
        }

        $prompt = "You are a professional luxury fashion brand copywriter for Srishringarr. " .
                  "Analyze the product in the image. Write a detailed, premium, and compelling product description. " .
                  (!empty($productInfo) ? "Context from user: $productInfo. " : "") .
                  (!empty($size) ? "Available size: $size. Include size information appropriately. " : "") .
                  "The total description MUST NOT exceed $maxWords words. " .
                  "Structure the response to have:\n" .
                  "1. A compelling description paragraph.\n" .
                  "2. A section titled 'Key Features:' followed by bullet points.\n" .
                  "CRITICAL FORMATTING RULES FOR PLAIN TEXT:\n" .
                  "- Do not use any markdown tags.\n" .
                  "- For bullet points, start each bullet item with a literal bullet character '•'.\n" .
                  "Return ONLY the clean plain text of description and key features.";

        $response = $this->callGeminiText($apiKey, $prompt, $b64Images);
        if (isset($response['error'])) {
            $this->json(['error' => $response['error']], 500);
            return;
        }

        $descText = trim($response['text']);
        $this->saveHistory($session_id, 'description', $size, $productInfo, $descText);
        $this->json(['success' => true, 'description' => $descText]);
    }

    public function generateImages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $b64Images = $input['images'] ?? [];
        if (isset($input['image']) && !empty($input['image'])) {
            $b64Images[] = $input['image'];
        }
        $productInfo = $input['product_info'] ?? '';
        $size = $input['size'] ?? '';
        $count = (int)($input['count'] ?? 1);
        $promptParam = $input['prompt'] ?? '';
        $session_id = $input['session_id'] ?? '';

        if (empty($b64Images)) {
            $this->json(['error' => 'At least one image is required'], 400);
            return;
        }
        
        if ($count < 1) $count = 1;
        if ($count > 4) $count = 4;

        $apiKey = $this->getGeminiKey();
        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured.'], 400);
            return;
        }

        $prompt = $promptParam ?: "A photorealistic beautiful Indian fashion model wearing this exact product. The model should have open flowing hair. The background should have elegant props like a palace or traditional setting that compliments the jewelry or garment perfectly. Do not change the product details. Show a close-up portrait shot focusing on the product." . (!empty($productInfo) ? " Note: " . $productInfo : "");

        $generatedImages = [];
        for ($i = 0; $i < $count; $i++) {
            $currentPrompt = $prompt . ($i > 0 ? " Make this variation " . ($i+1) . " slightly different in pose or lighting." : "");
            $res = $this->callGeminiImage($apiKey, $currentPrompt, $b64Images[0]);
            if (isset($res['error'])) {
                if ($i === 0) {
                    $this->json(['error' => $res['error']], 500);
                    return;
                }
                break;
            }
            
            $rawB64 = $res['b64'];
            $imgData = base64_decode($rawB64);
            
            $year = date('Y');
            $month = date('m');
            $uploadDir = __DIR__ . "/../../yn/uploads/ai_playground/{$year}/{$month}/{$session_id}";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid('img_') . '.jpg';
            $filePath = $uploadDir . '/' . $fileName;
            file_put_contents($filePath, $imgData);
            
            $relativePath = "uploads/ai_playground/{$year}/{$month}/{$session_id}/{$fileName}";
            $this->saveHistory($session_id, 'image', $size, $productInfo, $relativePath);
            
            $generatedImages[] = "http://localhost/ss/yn/" . $relativePath;
        }

        $this->json(['success' => true, 'images' => $generatedImages]);
    }

    public function generateVideoStart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $b64Images = $input['images'] ?? [];
        if (isset($input['image']) && !empty($input['image'])) {
            $b64Images[] = $input['image'];
        }
        $promptParam = $input['prompt'] ?? '';

        if (empty($b64Images)) {
            $this->json(['error' => 'At least one image is required'], 400);
            return;
        }

        $apiKey = $this->getGeminiKey();
        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured.'], 400);
            return;
        }

        // Prepare base64
        $base64Image = $b64Images[0];
        if (strpos($base64Image, ',') !== false) {
            $base64Image = explode(',', $base64Image)[1];
        }

        $prompt = $promptParam ?: "A beautiful, cinematic showcase of this fashion product. The product is the central focus.";

        $payload = [
            'instances' => [
                [
                    'prompt' => $prompt,
                    'image' => [
                        'bytesBase64Encoded' => $base64Image,
                        'mimeType' => 'image/jpeg'
                    ]
                ]
            ],
            'parameters' => [
                'aspectRatio' => '9:16'
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/veo-3.1-generate-preview:predictLongRunning";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200) {
            $this->json(['error' => 'API request failed: ' . $response], 500);
            return;
        }

        $decoded = json_decode($response, true);
        $operationName = $decoded['name'] ?? '';

        if (empty($operationName)) {
            $this->json(['error' => 'Failed to get operation name.'], 500);
            return;
        }

        $this->json(['success' => true, 'operation_name' => $operationName]);
    }

    public function generateVideoStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $operationName = $input['operation_name'] ?? '';
        $session_id = $input['session_id'] ?? '';
        $size = $input['size'] ?? '';
        $productInfo = $input['product_info'] ?? '';

        if (empty($operationName)) {
            $this->json(['error' => 'operation_name is required'], 400);
            return;
        }

        $apiKey = $this->getGeminiKey();
        $url = "https://generativelanguage.googleapis.com/v1beta/" . $operationName;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-goog-api-key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200) {
            $this->json(['error' => 'Status request failed: ' . $response], 500);
            return;
        }

        $decoded = json_decode($response, true);
        $isDone = $decoded['done'] ?? false;

        if (!$isDone) {
            $this->json(['success' => true, 'done' => false]);
            return;
        }

        if (isset($decoded['error'])) {
            $this->json(['error' => 'Video generation failed: ' . json_encode($decoded['error'])], 500);
            return;
        }

        $videoUri = $decoded['response']['generateVideoResponse']['generatedSamples'][0]['video']['uri'] ?? '';
        if (empty($videoUri)) {
            $this->json(['error' => 'No video URI found in response.'], 500);
            return;
        }

        // Download video
        $year = date('Y');
        $month = date('m');
        $uploadDir = __DIR__ . "/../../yn/uploads/ai_playground/{$year}/{$month}/{$session_id}";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid('vid_') . '.mp4';
        $filePath = $uploadDir . '/' . $fileName;

        $ch = curl_init($videoUri);
        $fp = fopen($filePath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-goog-api-key: ' . $apiKey
        ]);
        curl_exec($ch);
        fclose($fp);

        $relativePath = "uploads/ai_playground/{$year}/{$month}/{$session_id}/{$fileName}";
        $this->saveHistory($session_id, 'video', $size, $productInfo, $relativePath);
        
        $this->json(['success' => true, 'done' => true, 'video_url' => "http://localhost/ss/yn/" . $relativePath]);
    }

    private function callGeminiText($apiKey, $prompt, $base64Images) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey;
        
        $parts = [['text' => $prompt]];
        foreach ($base64Images as $img) {
            if (strpos($img, ',') !== false) {
                $img = explode(',', $img)[1];
            }
            $parts[] = [
                'inlineData' => [
                    'mimeType' => 'image/jpeg',
                    'data' => $img
                ]
            ];
        }

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => $parts
                ]
            ]
        ]);

        return $this->curlPost($url, $payload);
    }

    private function callGeminiImage($apiKey, $prompt, $base64Image) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-image:generateContent?key=' . $apiKey;
        if (strpos($base64Image, ',') !== false) {
            $base64Image = explode(',', $base64Image)[1];
        }
        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => 'image/jpeg',
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
            CURLOPT_TIMEOUT => 45,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($httpCode !== 200) {
            $errObj = json_decode($response, true);
            $errMsg = $errObj['error']['message'] ?? 'API request failed';
            return ['error' => 'Gemini API Error: ' . $errMsg];
        }

        $decoded = json_decode($response, true);
        $b64 = $decoded['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
        
        if ($b64) {
            return ['b64' => $b64];
        }
        return ['error' => 'No image data returned. Raw response: ' . $response];
    }

    private function curlPost($url, $payload) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Gemini API request failed: ' . $response];
        }

        $decoded = json_decode($response, true);
        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
        return ['text' => $text];
    }
}
