<?php
namespace Controllers;

use Core\Controller;
use Models\ProductModel;

class ProductController extends Controller {
    public function index() {
        $productModel = new ProductModel();
        
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $categories = $productModel->getCategories();
        
        $this->view('products/index', [
            'search' => $search,
            'category' => $category,
            'categories' => $categories
        ]);
    }

    public function unmapped() {
        $categoryController = new CategoryController();
        $categoryController->unmapped();
    }

    public function view_details() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        if (!$product) {
            $this->redirect('index.php?controller=product&action=index&error=Product+not+found');
        }

        $this->view('products/view', [
            'product' => $product,
            'images' => $images,
            'type' => $type
        ]);
    }

    public function aiSuggestNames() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
            return;
        }

        $productModel = new ProductModel();
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'];
        $localPath = __DIR__ . '/../../yn/uploads' . $imgRelativePath;
        $imgContent = null;
        $mimeType = 'image/jpeg';

        if (file_exists($localPath)) {
            $imgContent = file_get_contents($localPath);
            $mime = mime_content_type($localPath);
            if ($mime) $mimeType = $mime;
        } else {
            $remoteUrl = 'https://srishringarr.com/yn/uploads' . $imgRelativePath;
            $imgContent = @file_get_contents($remoteUrl);
            $ext = strtolower(pathinfo($imgRelativePath, PATHINFO_EXTENSION));
            if ($ext === 'png') $mimeType = 'image/png';
            elseif ($ext === 'webp') $mimeType = 'image/webp';
        }

        if (empty($imgContent)) {
            $this->json(['error' => 'Failed to load product image for analysis.'], 400);
            return;
        }

        $base64Image = base64_encode($imgContent);
        $prompt = "You are a professional fashion copywriter for Srishringarr. " .
                  "Analyze the product in the image. Suggest exactly 5 descriptive product names (each name MUST be at least 10 words long) suitable for a $type item. " .
                  "Use very simple, clear, and easy-to-understand English. Do NOT use complex, rare, fancy, flowery, or poetic words (such as 'ethereal', 'wisteria', 'intricately', 'enchanted', 'resplendent', 'mystique', 'regal', etc.). " .
                  "Instead, use common, everyday words to describe the product's colors, materials, design, embroidery, and style. " .
                  "Each name MUST have at least 10 words. " .
                  "Example of expected output format and style: " .
                  "\"Beautiful red lehenga choli for wedding functions with heavy gold embroidery and a matching net dupatta\" or " .
                  "\"Traditional gold plated necklace set with green beads and matching earrings for party wear\". " .
                  "Return ONLY a raw JSON array of strings containing the 5 suggested names. Do not include markdown code block formatting (no ```json, no ```).";

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
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($httpCode !== 200) {
            $this->json(['error' => 'Gemini API request failed: ' . $response], 500);
            return;
        }

        $decoded = json_decode($response, true);
        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $promptTokens = (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
        $candidateTokens = (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
        $totalTokens = (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);
        
        $text = trim(preg_replace('/^```json|```$/', '', trim($text)));
        $names = json_decode($text, true);

        if (!is_array($names)) {
            preg_match_all('/"(.*?)"/', $text, $matches);
            $names = !empty($matches[1]) ? array_slice($matches[1], 0, 5) : [];
        }

        // Log Title Generation to ai_analytics DB
        $db = \Core\Database::getConnection('con');
        if ($db) {
            $costEstimate = max(0.01, (($promptTokens * 0.000000075) + ($candidateTokens * 0.0000003)) * 86);
            $genOutput = json_encode($names);
            $opType = 'title';
            $numImg = 0;

            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image'");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");

            $website = 'srishringarr';
            $stmt = $db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssiiiids", $id, $type, $opType, $prompt, $genOutput, $numImg, $promptTokens, $candidateTokens, $totalTokens, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        $this->json(['success' => true, 'names' => $names]);
    }

    public function aiSuggestColors() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $productModel = new ProductModel();
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'] ?? '';
        $colors = $productModel->detectColorsFromImage($imgRelativePath, $type);

        if (empty($colors)) {
            $this->json(['error' => 'Could not detect colors from image. Please select manually.'], 500);
            return;
        }

        $this->json(['success' => true, 'colors' => $colors]);
    }

    public function aiSuggestDescription() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        $maxWords = (int)($_GET['max_words'] ?? 100);
        
        if ($maxWords < 10) $maxWords = 10;
        if ($maxWords > 1000) $maxWords = 1000;

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
            return;
        }

        $productModel = new ProductModel();
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'];
        $localPath = __DIR__ . '/../../yn/uploads' . $imgRelativePath;
        $imgContent = null;
        $mimeType = 'image/jpeg';

        if (file_exists($localPath)) {
            $imgContent = file_get_contents($localPath);
            $mime = mime_content_type($localPath);
            if ($mime) $mimeType = $mime;
        } else {
            $remoteUrl = 'https://srishringarr.com/yn/uploads' . $imgRelativePath;
            $imgContent = @file_get_contents($remoteUrl);
            $ext = strtolower(pathinfo($imgRelativePath, PATHINFO_EXTENSION));
            if ($ext === 'png') $mimeType = 'image/png';
            elseif ($ext === 'webp') $mimeType = 'image/webp';
        }

        if (empty($imgContent)) {
            $this->json(['error' => 'Failed to load product image for analysis.'], 400);
            return;
        }

        $base64Image = base64_encode($imgContent);
        $prompt = "You are a professional luxury fashion brand copywriter for Srishringarr. " .
                  "Analyze the product in the image. Write a detailed, premium, and compelling product description for this $type item. " .
                  "The total description MUST NOT exceed $maxWords words. Be extremely concise if the word count limit is small. " .
                  "Structure the response to have:\n" .
                  "1. A compelling description paragraph introducing the item, emphasizing its visual elegance, style, and suitability for weddings, receptions, sangeets, or special occasions.\n" .
                  "2. A section titled 'Key Features:' followed by bullet points detailing specific design details, craftsmanship, embroidery/sequins/beading, fabric/metal materials, and accessories as visible or appropriate for this item.\n" .
                  "CRITICAL FORMATTING RULES FOR PLAIN TEXT:\n" .
                  "- Do not use any markdown tags (no '**', no '*', no '__', no '#').\n" .
                  "- For bullet points, start each bullet item with a literal bullet character '•' followed by a space (e.g., '• Feature Name: Feature description.').\n" .
                  "- Simply write headings as plain text (e.g., 'Key Features:').\n" .
                  "Do not include any placeholders, conversational text, or greetings. Return ONLY the clean plain text of description and key features.";

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
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);

        if ($httpCode !== 200) {
            $this->json(['error' => 'Gemini API request failed: ' . $response], 500);
            return;
        }

        $decoded = json_decode($response, true);
        $description = trim($decoded['candidates'][0]['content']['parts'][0]['text'] ?? '');
        $promptTokens = (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
        $candidateTokens = (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
        $totalTokens = (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);

        // Log Description Generation to ai_analytics DB
        $db = \Core\Database::getConnection('con');
        if ($db) {
            $costEstimate = max(0.01, (($promptTokens * 0.000000075) + ($candidateTokens * 0.0000003)) * 86);
            $opType = 'description';
            $numImg = 0;

            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image'");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");

            $website = 'srishringarr';
            $stmt = $db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssiiiids", $id, $type, $opType, $prompt, $description, $numImg, $promptTokens, $candidateTokens, $totalTokens, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        $this->json(['success' => true, 'description' => $description]);
    }

    public function aiGenerateModelImage() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        $input = json_decode(file_get_contents('php://input'), true);
        $basePrompt = $input['prompt'] ?? 'A photorealistic beautiful Indian fashion model wearing this exact product. The model should have open flowing hair. The background should have elegant props like a palace or traditional setting that compliments the jewelry perfectly. Do not change the product details. Show a close-up portrait shot focusing on the product.';
        $faceReference = $input['face_reference'] ?? '';
        $numImages = (int)($input['num_images'] ?? 1);
        if ($numImages < 1) $numImages = 1;
        if ($numImages > 4) $numImages = 4;

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'];
        $localPath = __DIR__ . '/../../yn/uploads' . $imgRelativePath;
        $imgContent = null;
        $mimeType = 'image/jpeg';

        if (file_exists($localPath)) {
            $imgContent = file_get_contents($localPath);
            $mime = mime_content_type($localPath);
            if ($mime) $mimeType = $mime;
        } else {
            $remoteUrl = 'https://srishringarr.com/yn/uploads' . $imgRelativePath;
            $imgContent = @file_get_contents($remoteUrl);
            $ext = strtolower(pathinfo($imgRelativePath, PATHINFO_EXTENSION));
            if ($ext === 'png') $mimeType = 'image/png';
            elseif ($ext === 'webp') $mimeType = 'image/webp';
        }

        if (empty($imgContent)) {
            $this->json(['error' => 'Failed to load product image for generation.'], 400);
            return;
        }

        $imgData = base64_encode($imgContent);
        
        $mediaParts = [
            [
                'inlineData' => [
                    'mimeType' => $mimeType,
                    'data' => $imgData
                ]
            ]
        ];

        // Add face reference if selected
        if (!empty($faceReference)) {
            $facePath = __DIR__ . '/../assets/models/' . basename($faceReference);
            if (file_exists($facePath)) {
                $faceContent = file_get_contents($facePath);
                $faceMime = mime_content_type($facePath) ?: 'image/png';
                $mediaParts[] = [
                    'inlineData' => [
                        'mimeType' => $faceMime,
                        'data' => base64_encode($faceContent)
                    ]
                ];
            }
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-image:generateContent?key=' . $apiKey;
        $generatedImages = [];
        $errors = [];
        
        $totalPromptTokens = 0;
        $totalCandidateTokens = 0;
        $totalTokensSum = 0;

        $variations = [
            '', // Standard as prompted
            ' Also, ensure a slight side-profile angle.',
            ' Also, ensure a dynamic and confident fashion pose.',
            ' Also, ensure a different elegant camera angle.'
        ];

        for ($i = 0; $i < $numImages; $i++) {
            $currentPrompt = $basePrompt . ($variations[$i] ?? '');
            $parts = array_merge([['text' => $currentPrompt]], $mediaParts);
            
            $payload = json_encode([
                'contents' => [
                    [
                        'parts' => $parts
                    ]
                ],
                'generationConfig' => [
                    'imageConfig' => [
                        'aspectRatio' => '2:3'
                    ]
                ]
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 45, // Wait longer for multiple sequential requests
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode === 200) {
                $decoded = json_decode($response, true);
                
                // Track Token Usage
                if (isset($decoded['usageMetadata'])) {
                    $totalPromptTokens += (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
                    $totalCandidateTokens += (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
                    $totalTokensSum += (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);
                }

                $b64 = $decoded['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
                if ($b64) {
                    $generatedImages[] = $b64;
                } else {
                    $errors[] = 'No image data returned in iteration ' . ($i+1);
                }
            } else {
                $errObj = json_decode($response, true);
                $errMsg = $errObj['error']['message'] ?? 'API request failed';
                $errors[] = 'API Error on iteration ' . ($i+1) . ': ' . $errMsg;
            }
            
            // Add a slight delay to avoid rate limits on multiple rapid requests
            if ($i < $numImages - 1) {
                sleep(2);
            }
        }

        // Log Analytics to DB (Imagen 3 rate: $0.03 per image = ~₹2.58 per image at 86 INR/USD)
        $actualGeneratedCount = count($generatedImages);
        $costEstimate = $actualGeneratedCount * 0.03 * 86;
        $opType = 'image';
        $genOutput = $actualGeneratedCount . " image(s) generated";
        
        $db = \Core\Database::getConnection('con');
        if ($db) {
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image'");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");

            $website = 'srishringarr';
            $stmt = $db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssiiiids", $id, $type, $opType, $basePrompt, $genOutput, $numImages, $totalPromptTokens, $totalCandidateTokens, $totalTokensSum, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        if (count($generatedImages) > 0) {
            // Even if some failed, return the successful ones
            $this->json(['success' => true, 'images_base64' => $generatedImages, 'partial_errors' => $errors]);
        } else {
            $this->json(['error' => implode(' | ', $errors)], 500);
        }
    }

    public function aiGenerateVideoStart() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        $input = json_decode(file_get_contents('php://input'), true);
        $prompt = $input['prompt'] ?? 'A beautiful, cinematic showcase of this fashion product. The product is the central focus.';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'];
        $localPath = __DIR__ . '/../../yn/uploads' . $imgRelativePath;
        $imgContent = null;
        $mimeType = 'image/jpeg';

        if (file_exists($localPath)) {
            $imgContent = file_get_contents($localPath);
            $mime = mime_content_type($localPath);
            if ($mime) $mimeType = $mime;
        } else {
            $remoteUrl = 'https://srishringarr.com/yn/uploads' . $imgRelativePath;
            $imgContent = @file_get_contents($remoteUrl);
            $ext = strtolower(pathinfo($imgRelativePath, PATHINFO_EXTENSION));
            if ($ext === 'png') $mimeType = 'image/png';
            elseif ($ext === 'webp') $mimeType = 'image/webp';
        }

        if (empty($imgContent)) {
            $this->json(['error' => 'Failed to load product image for video generation.'], 400);
            return;
        }

        $imgData = base64_encode($imgContent);
        
        $payload = [
            'instances' => [
                [
                    'prompt' => $prompt,
                    'image' => [
                        'bytesBase64Encoded' => $imgData,
                        'mimeType' => $mimeType
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

    public function aiGenerateVideoStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $operationName = $input['operation_name'] ?? '';
        $productId = (int)($input['product_id'] ?? 0);

        if (empty($operationName)) {
            $this->json(['error' => 'operation_name is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

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
        $uploadDir = __DIR__ . "/../../yn/uploads/product_videos";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid("prod_{$productId}_vid_") . '.mp4';
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

        $relativePath = "uploads/product_videos/{$fileName}";
        
        $this->json(['success' => true, 'done' => true, 'video_url' => "http://localhost/ss/yn/" . $relativePath]);
    }

    public function saveAiImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        $input = json_decode(file_get_contents('php://input'), true);
        $b64 = $input['image_base64'] ?? '';

        if (!$id || empty($b64)) {
            $this->json(['error' => 'Product ID and image data are required'], 400);
            return;
        }

        $productModel = new \Models\ProductModel();
        $product = $productModel->getProductById($id, $type);
        if (!$product) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }

        $code = $product['code'];
        $name = $product['name'] ?? '';
        $sub = $product['sub_category'] ?? 0;
        
        $imgData = base64_decode($b64);
        if (!$imgData) {
            $this->json(['error' => 'Invalid image data'], 400);
            return;
        }

        $current_year = date('Y');
        $current_month = date('m');
        $upload_base = __DIR__ . "/../../yn/uploads/";
        $upload_path = $upload_base . $current_year . '/' . $current_month . '/';

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $new_filename = $code . '_ai_' . time() . '.jpg';
        $full_local_path = $upload_path . $new_filename;
        
        if (file_put_contents($full_local_path, $imgData) === false) {
            $this->json(['error' => 'Failed to save image file'], 500);
            return;
        }

        $relative_path = '/' . $current_year . '/' . $current_month . '/' . $new_filename;
        
        $db = $productModel->getDbConnection();
        $date_added = date('Y-m-d H:i:s');
        
        $img_field = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
        $subcat_val = ($type === 'jewellery') ? $sub : 0;
        
        $img_sql = "INSERT INTO product_images_new (
            prod_name, prod_image, pro_code, img_name, 
            subcat_id, $img_field, date_added
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($db, $img_sql);
        mysqli_stmt_bind_param($stmt, "ssssiis", $name, $relative_path, $code, $relative_path, $subcat_val, $id, $date_added);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            $insert_id = mysqli_insert_id($db);
            $this->json(['success' => true, 'path' => $relative_path, 'id' => $insert_id]);
        } else {
            $this->json(['error' => 'Failed to insert image record in database'], 500);
        }
    }

    public function saveProductField() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $type = $input['type'] ?? 'jewellery';
        $field = $input['field'] ?? '';
        $value = trim($input['value'] ?? '');

        if (!$id || !in_array($type, ['jewellery', 'garments']) || !in_array($field, ['name', 'description']) || empty($value)) {
            $this->json(['error' => 'Invalid parameters'], 400);
            return;
        }

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        if ($type === 'jewellery') {
            $column = ($field === 'name') ? 'product_name' : 'product_desc';
            $sql = "UPDATE product SET $column = ? WHERE product_id = ?";
        } else {
            $column = ($field === 'name') ? 'gproduct_name' : 'gproduct_desc';
            $sql = "UPDATE garment_product SET $column = ? WHERE gproduct_id = ?";
        }

        $stmt = mysqli_prepare($db, $sql);
        if (!$stmt) {
            $this->json(['error' => mysqli_error($db)], 500);
            return;
        }

        mysqli_stmt_bind_param($stmt, "si", $value, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            try {
                \Core\ProductSyncService::syncProduct($id, $type, 'auto');
            } catch (\Throwable $th) {
                error_log("Auto-sync error on saveProductField: " . $th->getMessage());
            }
            $this->json(['success' => true, 'message' => ucfirst($field) . ' updated successfully']);
        } else {
            $err = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            $this->json(['error' => $err], 500);
        }
    }

    public function add() {
        $productModel = new ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $allJewelCategoriesTree = $productModel->getAllCategoriesWithSubcategories('jewellery');
        $allGarmentCategoriesTree = $productModel->getAllCategoriesWithSubcategories('garments');
        $availableColors = $productModel->getAvailableColors();
        
        $this->view('products/add', [
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'allJewelCategoriesTree' => $allJewelCategoriesTree,
            'allGarmentCategoriesTree' => $allGarmentCategoriesTree,
            'availableColors' => $availableColors
        ]);
    }

    public function checkSku() {
        $sku = $_GET['sku'] ?? '';
        $priceSource = $_GET['price_source'] ?? 'pos';
        if (!$sku) $this->json(['error' => 'Missing SKU'], 400);

        $productModel = new ProductModel();
        
        // 1. Check if already exists in local database
        $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
        $existsGarment = $productModel->checkProductExists($sku, 'garments');
        
        if ($existsJewel || $existsGarment) {
            $this->json([
                'allowed' => false,
                'message' => "Product with code '$sku' already exists in the local database."
            ]);
            return;
        }

        // 2. Manual price source: no POS validation needed
        if ($priceSource === 'manual') {
            $this->json([
                'allowed' => true,
                'message' => 'Manual pricing mode — POS validation skipped.'
            ]);
            return;
        }

        // 3. Check if exists in POS
        $posItem = $productModel->validateSkuInPos($sku);
        
        if ($posItem) {
            $this->json([
                'allowed' => true,
                'message' => 'Valid POS product found: ' . $posItem['name']
            ]);
        } else {
            $this->json([
                'allowed' => false,
                'message' => 'Product not found in POS system. Please add it to POS first.'
            ]);
        }
    }

    public function getSubcategories() {
        $type = $_GET['type'] ?? 'jewellery';
        $parentId = (int)$_GET['parent_id'];
        $productModel = new ProductModel();
        
        if ($type === 'jewellery') {
            $subs = $productModel->getJewelSubcategories($parentId);
        } else {
            $subs = $productModel->getGarmentSubcategories($parentId);
            // Map keys for consistent JSON output
            $subs = array_map(function($s) {
                return ['subcat_id' => $s['sub_id'], 'name' => $s['sub_name']];
            }, $subs);
        }
        
        echo json_encode($subs);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Check for duplicates
            if ($productModel->checkProductExists($code, $type)) {
                throw new \Exception("Product with code '$code' already exists in the system.");
            }

            // Process Images
            $uploadedImages = $this->handleImageUploads($code);

            $productId = $productModel->saveProduct($type, $_POST, $uploadedImages);

            // Process Multi-Category Selection
            $mainCategories = $_POST['categories'] ?? [];
            $subcategories = $_POST['sub_categories'] ?? [];
            if ($productId) {
                $productModel->saveProductCategories($productId, $type, $mainCategories, $subcategories);
            }

            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect('index.php?controller=product&action=add&error=' . urlencode($e->getMessage()));
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        
        $allCategoriesTree = $productModel->getAllCategoriesWithSubcategories($type);
        $assignedCategories = $productModel->getProductAssignedCategories($id, $type);
        $availableColors = $productModel->getAvailableColors();
        
        $this->view('products/edit', [
            'product' => $product,
            'images' => $images,
            'type' => $type,
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'allCategoriesTree' => $allCategoriesTree,
            'assignedCategories' => $assignedCategories,
            'availableColors' => $availableColors
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $id = (int)$_POST['id'];
        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Process New Images
            $uploadedImages = $this->handleImageUploads($code);

            $productModel->updateProduct($type, $id, $_POST, $uploadedImages);

            // Process Multi-Category Selection
            $mainCategories = $_POST['categories'] ?? [];
            $subcategories = $_POST['sub_categories'] ?? [];
            $productModel->saveProductCategories($id, $type, $mainCategories, $subcategories);

            $this->redirect("index.php?controller=product&action=edit&id=$id&type=$type&success=1");
        } catch (\Exception $e) {
            $this->redirect("index.php?controller=product&action=edit&id=$id&type=$type&error=" . urlencode($e->getMessage()));
        }
    }

    public function edit2() {
        $this->edit();
    }

    public function update2() {
        $this->update();
    }

    public function edit3() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);
        
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $availableColors = $productModel->getAvailableColors();
        
        $this->view('products/edit3', [
            'product' => $product,
            'images' => $images,
            'type' => $type,
            'jewelCategories' => $jewelCategories,
            'garments' => $garments,
            'availableColors' => $availableColors
        ]);
    }

    public function update3() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('index.php?controller=product&action=index');

        $id = (int)$_POST['id'];
        $type = $_POST['type'] ?? 'jewellery';
        $code = $_POST['code'] ?? '';
        $productModel = new ProductModel();
        
        try {
            // Process New Images
            $uploadedImages = $this->handleImageUploads($code);

            $productModel->updateProduct($type, $id, $_POST, $uploadedImages);
            $this->redirect("index.php?controller=product&action=edit3&id=$id&type=$type&success=1");
        } catch (\Exception $e) {
            $this->redirect("index.php?controller=product&action=edit3&id=$id&type=$type&error=" . urlencode($e->getMessage()));
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';
        
        if (!$id) $this->redirect('index.php?controller=product&action=index');

        $productModel = new ProductModel();
        try {
            $product = $productModel->getProductById($id, $type);
            $productModel->deleteProduct($id, $type);

            if ($product && !empty($product['code'])) {
                try {
                    \Core\ProductSyncService::deleteProductFromChild($product['code']);
                } catch (\Throwable $th) {
                    error_log("Auto-sync delete error: " . $th->getMessage());
                }
            }
            $this->redirect('index.php?controller=product&action=index&success=1');
        } catch (\Exception $e) {
            $this->redirect('index.php?controller=product&action=index&error=' . urlencode($e->getMessage()));
        }
    }

    public function deleteImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['ids'] ?? null;
        $id = (int)($input['id'] ?? 0);

        $productModel = new ProductModel();
        try {
            if (is_array($ids) && count($ids) > 0) {
                $successCount = 0;
                foreach ($ids as $imgId) {
                    if ($productModel->deleteImage((int)$imgId)) {
                        $successCount++;
                    }
                }
                $this->json(['success' => true, 'deleted_count' => $successCount]);
                return;
            }

            if (!$id) {
                $this->json(['error' => 'Missing image ID'], 400);
                return;
            }

            if ($productModel->deleteImage($id)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Failed to delete image from database'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function handleImageUploads($code) {
        $uploadedImages = [];
        if (isset($_FILES['images'])) {
            $current_year = date('Y');
            $current_month = date('m');
            $upload_base = __DIR__ . "/../../yn/uploads/";
            $upload_path = $upload_base . $current_year . '/' . $current_month . '/';

            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $filename = $_FILES['images']['name'][$key];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = $code . '_' . time() . '_' . ($key + 1) . '.' . $ext;
                    
                    if (move_uploaded_file($tmp_name, $upload_path . $new_filename)) {
                        $uploadedImages[] = $current_year . '/' . $current_month . '/' . $new_filename;
                    }
                }
            }
        }
        return $uploadedImages;
    }

    public function downloadTemplate() {
        if (ob_get_level()) ob_end_clean();
        
        $csv = "sku,name,description,type,categories,sub_categories,rental_price,s_price,deposit,colors,size_avail,brand_name\n";
        $csv .= '"JW101","Bridal Kundan Necklace Set","Exquisite bridal set with matching earrings","jewellery","1, 2","1, 14","2500","8000","3000","Gold, Maroon","Free Size","Sri Shringaar"' . "\n";
        $csv .= '"GM202","Royal Red Velvet Bridal Lehenga","Intricate zardozi embroidery designer lehenga","garments","10, 22","","4500","18000","5000","Red, Golden","M, L, XL","Sri Shringaar"' . "\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="product_import_template.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
        exit;
    }

    public function downloadSampleZip() {
        if (ob_get_level()) ob_end_clean();

        $zipFile = tempnam(sys_get_temp_dir(), 'sample_import_');
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            die("Cannot create zip archive");
        }

        // 1. Create Sample Excel spreadsheet with PhpSpreadsheet if available
        $excelSaved = false;
        $excelTemp = tempnam(sys_get_temp_dir(), 'excel_');

        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            try {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Products');

                $headers = ['sku', 'name', 'description', 'type', 'categories', 'sub_categories', 'rental_price', 's_price', 'deposit', 'colors', 'size_avail', 'brand_name'];
                $sheet->fromArray([$headers], NULL, 'A1');

                $sampleRows = [
                    ['JW101', 'Bridal Kundan Necklace Set', 'Exquisite bridal set with matching earrings and maang tikka', 'jewellery', '1, 2', '1, 14', 2500, 8000, 3000, 'Gold, Maroon', 'Free Size', 'Sri Shringaar'],
                    ['JW102', 'Antique Gold Plated Choker', 'Traditional temple choker necklace with pearls', 'jewellery', '1', '1', 1800, 6500, 2000, 'Gold', 'Free Size', 'Sri Shringaar'],
                    ['GM202', 'Royal Red Velvet Bridal Lehenga', 'Intricate zardozi embroidery designer bridal lehenga with dupatta', 'garments', '10, 22', '', 4500, 18000, 5000, 'Red, Golden', 'M, L, XL', 'Sri Shringaar']
                ];
                $sheet->fromArray($sampleRows, NULL, 'A2');

                // Style Header
                $headerRange = 'A1:L1';
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($excelTemp);
                $zip->addFile($excelTemp, 'products_template.xlsx');
                $excelSaved = true;
            } catch (\Throwable $t) {
                error_log("Spreadsheet generation failed: " . $t->getMessage());
            }
        }

        if (!$excelSaved) {
            $csv = "sku,name,description,type,categories,sub_categories,rental_price,s_price,deposit,colors,size_avail,brand_name\n";
            $csv .= '"JW101","Bridal Kundan Necklace Set","Exquisite bridal set with matching earrings","jewellery","1, 2","1, 14","2500","8000","3000","Gold, Maroon","Free Size","Sri Shringaar"' . "\n";
            $csv .= '"GM202","Royal Red Velvet Bridal Lehenga","Intricate zardozi embroidery designer lehenga","garments","10, 22","","4500","18000","5000","Red, Golden","M, L, XL","Sri Shringaar"' . "\n";
            $zip->addFromString('products_template.csv', $csv);
        }

        // 2. Add sample SKU folders with sample images (1x1 PNG bytes as lightweight placeholder)
        $sampleImgBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $zip->addEmptyDir('JW101');
        $zip->addFromString('JW101/front_view.jpg', $sampleImgBytes);
        $zip->addFromString('JW101/detail_view.jpg', $sampleImgBytes);

        $zip->addEmptyDir('JW102');
        $zip->addFromString('JW102/main_choker.jpg', $sampleImgBytes);

        $zip->addEmptyDir('GM202');
        $zip->addFromString('GM202/full_lehenga.jpg', $sampleImgBytes);
        $zip->addFromString('GM202/embroidery_zoom.jpg', $sampleImgBytes);

        // 3. Add Instructions Readme
        $readme = "=======================================================\r\n";
        $readme .= " SRI SHRINGAAR - BULK PRODUCT UPLOAD INSTRUCTIONS\r\n";
        $readme .= "=======================================================\r\n\r\n";
        $readme .= "1. SPREADSHEET (products_template.xlsx or .csv):\r\n";
        $readme .= "   - Fill in your product details in the Excel file.\r\n";
        $readme .= "   - 'sku' and 'name' are required columns.\r\n";
        $readme .= "   - You do NOT need any image URL column in the spreadsheet.\r\n\r\n";
        $readme .= "2. PRODUCT IMAGES:\r\n";
        $readme .= "   - Create a folder named exactly after each product SKU (e.g. 'JW101', 'GM202').\r\n";
        $readme .= "   - Put that product's photo(s) inside its folder (.jpg, .png, .webp supported).\r\n";
        $readme .= "   - You can put 1 or multiple images in each SKU folder.\r\n\r\n";
        $readme .= "3. ZIP AND UPLOAD:\r\n";
        $readme .= "   - Select the Excel file and your SKU folders, then compress them into a .zip file.\r\n";
        $readme .= "   - Upload the .zip archive on the admin Import page.\r\n";
        $readme .= "=======================================================\r\n";
        $zip->addFromString('README_INSTRUCTIONS.txt', $readme);

        $zip->close();
        if (file_exists($excelTemp)) @unlink($excelTemp);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="sample_product_import_package.zip"');
        header('Content-Length: ' . filesize($zipFile));
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($zipFile);
        @unlink($zipFile);
        exit;
    }

    public function export() {
        while (ob_get_level()) ob_end_clean(); // Clear any buffers
        
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        // Get filters from URL
        $search = $_GET['search'] ?? '';
        $category_param = $_GET['category'] ?? '';
        $sortBy = $_GET['sort_by'] ?? 'id';
        $sortOrder = strtoupper($_GET['sort_order'] ?? 'DESC');
        $availableOnly = isset($_GET['available_only']) && ($_GET['available_only'] == 1 || $_GET['available_only'] == 'true');

        $allowed_sort_by = [
            'id' => 'id',
            'name' => 'name',
            'code' => 'code',
            'rent_price' => 'db_rent_price',
            'sales_price' => 'original_sales_price',
            'featured' => 'featured'
        ];
        
        $order_clause = "id DESC";
        if (array_key_exists($sortBy, $allowed_sort_by)) {
            $column = $allowed_sort_by[$sortBy];
            if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
                $sortOrder = 'DESC';
            }
            $order_clause = "$column $sortOrder";
        }

        $jewellery_search = '';
        $garments_search = '';
        
        if (!empty($search)) {
            $search_safe = mysqli_real_escape_string($db, $search);
            $jewellery_search = " AND (product_name LIKE '%$search_safe%' OR product_code LIKE '%$search_safe%')";
            $garments_search = " AND (gproduct_name LIKE '%$search_safe%' OR gproduct_code LIKE '%$search_safe%')";
        }

        if (!empty($category_param)) {
            if (strpos($category_param, ':') !== false) {
                list($type, $id) = explode(':', $category_param);
                $id = (int)$id;
                
                if ($type === 'garment') {
                    $garments_search .= " AND (garment_id = $id OR product_for = $id)";
                    $jewellery_search .= " AND 1=0";
                } elseif ($type === 'jewel_parent') {
                    $jewellery_search .= " AND categories_id = $id";
                    $garments_search .= " AND 1=0";
                } elseif ($type === 'jewel_child') {
                    $jewellery_search .= " AND subcat_id = $id";
                    $garments_search .= " AND 1=0";
                }
            }
        }
        if ($availableOnly) {
            $available_skus = [];
            $db3 = \Core\Database::getConnection('con3');
            if ($db3) {
                $res = mysqli_query($db3, "SELECT name FROM phppos_items WHERE quantity > 0");
                while ($row = mysqli_fetch_assoc($res)) {
                    if (!empty($row['name'])) {
                        $available_skus[] = mysqli_real_escape_string($db, $row['name']);
                    }
                }
            }
            if (!empty($available_skus)) {
                $sku_list = "'" . implode("','", $available_skus) . "'";
                $jewellery_search .= " AND product_code IN ($sku_list)";
                $garments_search .= " AND gproduct_code IN ($sku_list)";
            } else {
                $jewellery_search .= " AND 1=0";
                $garments_search .= " AND 1=0";
            }
        }
        $query = "(SELECT product_id as id, product_code as code, 'jewellery' as type, product_name as name, rent_price as db_rent_price, sales_price as original_sales_price, featured FROM product WHERE 1=1 $jewellery_search)
                  UNION ALL
                  (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type, gproduct_name as name, rent_price as db_rent_price, sales_price as original_sales_price, featured FROM garment_product WHERE 1=1 $garments_search)
                  ORDER BY $order_clause";
                  
        $result = $productModel->query($db, $query);
        
        if (!$result) {
            die("SQL Error: " . mysqli_error($db));
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="products_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        // Suppress deprecation warnings with @ for PHP 8.4 compatibility
        @fputcsv($output, ['sku', 'name', 'description', 'type', 'category_id', 'subcat_id', 's_price', 'rental_price', 'deposit', 'qty', 'images'], ',', '"', '\\');

        $i = 0;
        while ($p = mysqli_fetch_assoc($result)) {
            $fullProduct = $productModel->getProductById($p['id'], $p['type']);
            if (!$fullProduct) continue;

            $sku = $fullProduct['code'] ?? $p['code'] ?? '';
            $qty = $productModel->getPosQuantity($sku);

            $images = $productModel->getProductImages($p['id'], $p['type']);
            $imageUrls = [];
            if ($images) {
                foreach ($images as $img) {
                    $imageUrls[] = "https://srishringarr.com/yn/uploads" . $img['img_name'];
                }
            }

            @fputcsv($output, [
                $sku,
                $fullProduct['name'] ?? $p['name'] ?? '',
                $fullProduct['description'] ?? '',
                $p['type'] ?? '',
                $fullProduct['category'] ?? '',
                $fullProduct['sub_category'] ?? '',
                $fullProduct['s_price'] ?? 0,
                $fullProduct['rental_price'] ?? 0,
                $fullProduct['deposit'] ?? 0,
                $qty,
                implode(',', $imageUrls)
            ], ',', '"', '\\');
            
            $i++;
            if ($i % 50 == 0) {
                fflush($output);
                flush();
            }
        }
        fclose($output);
        exit;
    }

    public function import() {
        $productModel = new ProductModel();
        $categories = $productModel->getCategories();
        $jewelCategoriesTree = $productModel->getAllCategoriesWithSubcategories('jewellery');
        $garmentCategoriesTree = $productModel->getAllCategoriesWithSubcategories('garments');
        
        $this->view('products/import', [
            'categories' => $categories,
            'jewelCategoriesTree' => $jewelCategoriesTree,
            'garmentCategoriesTree' => $garmentCategoriesTree
        ]);
    }

    public function uploadImportPackage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        if (empty($_FILES['file']) && empty($_FILES['zip_file']) && empty($_FILES['package_file'])) {
            $this->json(['error' => 'No file uploaded. Please select a .zip or .xlsx/.csv file.'], 400);
            return;
        }

        $uploaded = $_FILES['file'] ?? $_FILES['zip_file'] ?? $_FILES['package_file'];
        if ($uploaded['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Upload error code: ' . $uploaded['error']], 400);
            return;
        }

        $originalName = $uploaded['name'];
        $tempFilePath = $uploaded['tmp_name'];
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/';
        if (!file_exists($tempImportsBase)) {
            mkdir($tempImportsBase, 0777, true);
        }

        $importToken = 'imp_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6));
        $extractDir = $tempImportsBase . $importToken . '/';
        if (!mkdir($extractDir, 0777, true)) {
            $this->json(['error' => 'Failed to create temporary extraction directory.'], 500);
            return;
        }

        $spreadsheetFile = null;

        if ($fileExt === 'zip') {
            if (!class_exists('\ZipArchive')) {
                $this->json(['error' => 'ZipArchive extension is not enabled in PHP.'], 500);
                return;
            }

            $zip = new \ZipArchive();
            if ($zip->open($tempFilePath) === true) {
                $zip->extractTo($extractDir);
                $zip->close();
            } else {
                $this->json(['error' => 'Failed to open and extract ZIP archive.'], 400);
                return;
            }
        } elseif (in_array($fileExt, ['xlsx', 'xls', 'csv'])) {
            $targetSpreadsheet = $extractDir . 'spreadsheet.' . $fileExt;
            if (!move_uploaded_file($tempFilePath, $targetSpreadsheet)) {
                $this->json(['error' => 'Failed to save uploaded spreadsheet.'], 500);
                return;
            }
            $spreadsheetFile = $targetSpreadsheet;
        } else {
            $this->json(['error' => 'Unsupported file type. Please upload a .zip, .xlsx, .xls, or .csv file.'], 400);
            return;
        }

        // Recursively index all files in $extractDir
        $skuFolderMap = []; // lowercase sku => array of relative file paths
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'bmp'];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $subPath = $iterator->getSubPathName();
            $subPath = str_replace('\\', '/', $subPath);
            
            // Ignore Mac OS metadata or temp lock files
            if (str_contains($subPath, '__MACOSX') || str_contains($subPath, '.~lock') || str_starts_with(basename($subPath), '~$')) {
                continue;
            }

            if ($item->isFile()) {
                $ext = strtolower(pathinfo($subPath, PATHINFO_EXTENSION));
                
                // If spreadsheet not yet identified, check if this is an Excel/CSV file
                if (!$spreadsheetFile && in_array($ext, ['xlsx', 'xls', 'csv'])) {
                    $spreadsheetFile = $item->getPathname();
                }

                // If image file, categorize under its parent folder (which represents SKU)
                if (in_array($ext, $imageExtensions)) {
                    $folderName = basename(dirname($subPath));
                    if ($folderName !== '.' && $folderName !== '' && $folderName !== basename($extractDir)) {
                        $skuKey = strtolower(trim($folderName));
                        if (!isset($skuFolderMap[$skuKey])) {
                            $skuFolderMap[$skuKey] = [];
                        }
                        $skuFolderMap[$skuKey][] = $subPath;
                    }
                }
            }
        }

        if (!$spreadsheetFile || !file_exists($spreadsheetFile)) {
            $this->json(['error' => 'No valid Excel (.xlsx / .xls) or CSV file found inside the uploaded package.'], 400);
            return;
        }

        // Parse spreadsheet rows
        $parsedProducts = [];
        $spreadsheetExt = strtolower(pathinfo($spreadsheetFile, PATHINFO_EXTENSION));

        if ($spreadsheetExt === 'csv') {
            $handle = fopen($spreadsheetFile, 'r');
            if ($handle) {
                $headers = [];
                $rowIdx = 0;
                while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    $rowIdx++;
                    if ($rowIdx === 1) {
                        $headers = array_map(function($h) {
                            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                        }, $row);
                        continue;
                    }
                    if (empty(array_filter($row))) continue;
                    $item = [];
                    foreach ($headers as $idx => $header) {
                        $item[$header] = isset($row[$idx]) ? trim((string)$row[$idx]) : '';
                    }
                    $parsedProducts[] = $item;
                }
                fclose($handle);
            }
        } else {
            // Read via PhpSpreadsheet
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheetFile);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);

                if (!empty($rows) && count($rows) > 1) {
                    $rawHeaders = $rows[0];
                    $headers = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                    }, $rawHeaders);

                    for ($i = 1; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        if (empty(array_filter($row, function($v) { return $v !== null && $v !== ''; }))) continue;
                        $item = [];
                        foreach ($headers as $idx => $header) {
                            if (!empty($header)) {
                                $val = $row[$idx] ?? '';
                                $item[$header] = is_string($val) ? trim($val) : (string)$val;
                            }
                        }
                        $parsedProducts[] = $item;
                    }
                }
            } catch (\Throwable $e) {
                $this->json(['error' => 'Error reading spreadsheet: ' . $e->getMessage()], 400);
                return;
            }
        }

        // Match each product row with its SKU image folder
        $totalMatchedImages = 0;
        $matchedFolders = [];
        $validProducts = [];

        foreach ($parsedProducts as $prod) {
            $sku = trim($prod['sku'] ?? $prod['sku_code'] ?? $prod['product_code'] ?? $prod['code'] ?? '');
            $name = trim($prod['name'] ?? $prod['product_name'] ?? '');

            if (empty($sku) && empty($name)) continue;

            if (!empty($sku) && empty($prod['sku'])) {
                $prod['sku'] = $sku;
            }

            $skuKey = strtolower($sku);
            $matchedImages = [];

            if (!empty($skuKey) && isset($skuFolderMap[$skuKey])) {
                $matchedImages = $skuFolderMap[$skuKey];
                $matchedFolders[$skuKey] = true;
                $totalMatchedImages += count($matchedImages);
            }

            $prod['temp_images'] = $matchedImages;
            $prod['images_count'] = count($matchedImages);
            $validProducts[] = $prod;
        }

        $this->json([
            'status' => 'success',
            'import_token' => $importToken,
            'spreadsheet_name' => basename($spreadsheetFile),
            'total_products' => count($validProducts),
            'total_images' => $totalMatchedImages,
            'total_folders' => count($matchedFolders),
            'products' => $validProducts
        ]);
    }

    public function cleanupImportTemp() {
        $token = $_POST['import_token'] ?? $_GET['import_token'] ?? '';
        $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/';

        if (!empty($token)) {
            $safeToken = preg_replace('/[^a-zA-Z0-9_-]/', '', $token);
            $targetDir = $tempImportsBase . $safeToken;
            if (is_dir($targetDir)) {
                $this->rrmdir($targetDir);
            }
        }

        // Auto sweep folders older than 2 hours
        if (is_dir($tempImportsBase)) {
            $dirs = scandir($tempImportsBase);
            $now = time();
            foreach ($dirs as $d) {
                if ($d === '.' || $d === '..') continue;
                $path = $tempImportsBase . $d;
                if (is_dir($path) && ($now - filemtime($path) > 7200)) {
                    $this->rrmdir($path);
                }
            }
        }

        $this->json(['status' => 'success']);
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    } else {
                        @unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            @rmdir($dir);
        }
    }

    public function processImportRow() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        // Support both JSON (from modern dashboard) and Form Data (legacy)
        $input = json_decode(file_get_contents('php://input'), true);
        $data = !empty($input) ? $input : $_POST;
        
        $productModel = new ProductModel();
        $categoryModel = new \Models\CategoryModel();

        try {
            $type = strtolower(trim($data['type'] ?? ''));
            $code = trim($data['sku'] ?? $data['sku_code'] ?? $data['code'] ?? '');

            if (empty($code)) throw new \Exception("Missing SKU");

            // Auto-detect type if empty
            if (empty($type)) {
                $type = (stripos($code, 'GM') === 0 || stripos($code, 'LM') === 0 || stripos($code, 'FM') === 0) ? 'garments' : 'jewellery';
            } else if ($type === 'garment' || $type === 'apparel') {
                $type = 'garments';
            } else if ($type === 'jewelry' || $type === 'jewel') {
                $type = 'jewellery';
            }

            // Check if exists - Skip if already exists (do not update)
            $existingProduct = $productModel->checkProductExists($code, $type);
            if ($existingProduct) {
                return $this->json([
                    'status' => 'skipped',
                    'message' => "SKU $code already exists. Skipped (not modified)."
                ]);
            }
            
            // Process Images from extracted ZIP folder or from URLs / local paths
            $downloadedImages = [];
            $current_year = date('Y');
            $current_month = date('m');
            $upload_base = __DIR__ . "/../../yn/uploads/";
            $upload_path = $current_year . '/' . $current_month . '/';
            $full_upload_path = $upload_base . $upload_path;

            if (!file_exists($full_upload_path)) {
                mkdir($full_upload_path, 0777, true);
            }

            // 1. Check if temp_images array from ZIP import is present
            $tempImages = $data['temp_images'] ?? [];
            $importToken = !empty($data['import_token']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$data['import_token']) : '';

            if (!empty($importToken)) {
                $tempImportsBase = __DIR__ . '/../../yn/uploads/temp_imports/' . $importToken . '/';
                
                // If temp_images array was passed
                if (!empty($tempImages) && is_array($tempImages)) {
                    foreach ($tempImages as $relPath) {
                        $sourcePath = $tempImportsBase . ltrim((string)$relPath, '/\\');
                        if (file_exists($sourcePath) && is_file($sourcePath)) {
                            $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) ?: 'jpg';
                            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                            if (copy($sourcePath, $full_upload_path . $filename)) {
                                $downloadedImages[] = $upload_path . $filename;
                            }
                        }
                    }
                } else {
                    // Fallback: search folder named after SKU in temp directory
                    $skuDirs = [$tempImportsBase . $code, $tempImportsBase . strtolower($code), $tempImportsBase . strtoupper($code)];
                    foreach ($skuDirs as $sDir) {
                        if (file_exists($sDir) && is_dir($sDir)) {
                            $files = glob($sDir . '/*.{jpg,jpeg,png,webp,avif,gif,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
                            if (!empty($files)) {
                                foreach ($files as $f) {
                                    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION)) ?: 'jpg';
                                    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                                    if (copy($f, $full_upload_path . $filename)) {
                                        $downloadedImages[] = $upload_path . $filename;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }

            // 2. Also support comma-separated images if supplied
            $rawImages = $data['images'] ?? $data['image'] ?? '';
            $imageUrls = !empty($rawImages) ? explode(',', (string)$rawImages) : [];
            
            foreach ($imageUrls as $url) {
                $url = trim($url);
                if (empty($url)) continue;

                // If already a local relative path, preserve it
                if (str_starts_with($url, '/') || str_starts_with($url, '202') || str_starts_with($url, 'uploads/')) {
                    $downloadedImages[] = ltrim(str_replace(['/yn/uploads/', 'yn/uploads/', '/uploads/'], '', $url), '/');
                    continue;
                }

                $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                
                $imgData = @file_get_contents($url);
                if ($imgData) {
                    if (file_put_contents($full_upload_path . $filename, $imgData)) {
                        $downloadedImages[] = $upload_path . $filename;
                    }
                }
            }

            // --- Multi-Category and Subcategory Parsing ---
            $rawMain = [];
            if (!empty($data['categories'])) {
                $rawMain = is_array($data['categories']) ? $data['categories'] : explode(',', (string)$data['categories']);
            }
            if (!empty($data['category_id'])) {
                $rawMain = array_merge($rawMain, is_array($data['category_id']) ? $data['category_id'] : explode(',', (string)$data['category_id']));
            }
            if (!empty($data['category'])) {
                $rawMain = array_merge($rawMain, is_array($data['category']) ? $data['category'] : explode(',', (string)$data['category']));
            }

            $rawSub = [];
            if (!empty($data['sub_categories'])) {
                $rawSub = is_array($data['sub_categories']) ? $data['sub_categories'] : explode(',', (string)$data['sub_categories']);
            }
            if (!empty($data['subcat_id'])) {
                $rawSub = array_merge($rawSub, is_array($data['subcat_id']) ? $data['subcat_id'] : explode(',', (string)$data['subcat_id']));
            }
            if (!empty($data['sub_category'])) {
                $rawSub = array_merge($rawSub, is_array($data['sub_category']) ? $data['sub_category'] : explode(',', (string)$data['sub_category']));
            }

            // Resolve Main Category IDs
            $mainCategoryIds = [];
            foreach ($rawMain as $item) {
                $item = trim((string)$item);
                if (empty($item)) continue;
                if (is_numeric($item) && (int)$item > 0) {
                    $mainCategoryIds[] = (int)$item;
                } else {
                    $foundId = $categoryModel->getCategoryIdByName($item, $type);
                    if ($foundId > 0) $mainCategoryIds[] = $foundId;
                }
            }
            $mainCategoryIds = array_values(array_unique(array_filter($mainCategoryIds)));

            // Resolve Subcategory IDs
            $subcategoryIds = [];
            foreach ($rawSub as $item) {
                $item = trim((string)$item);
                if (empty($item)) continue;
                if (is_numeric($item) && (int)$item > 0) {
                    $subcategoryIds[] = (int)$item;
                } else {
                    $foundId = $categoryModel->getCategoryIdByName($item, $type);
                    if ($foundId > 0) $subcategoryIds[] = $foundId;
                }
            }
            $subcategoryIds = array_values(array_unique(array_filter($subcategoryIds)));

            // Primary category & subcategory fallback
            $primaryCatId = !empty($mainCategoryIds) ? $mainCategoryIds[0] : 0;
            $primarySubId = !empty($subcategoryIds) ? $subcategoryIds[0] : 0;

            // Colors parsing
            $colorsInput = $data['colors'] ?? $data['brand_color'] ?? '';
            $colorsArray = [];
            if (!empty($colorsInput)) {
                if (is_array($colorsInput)) {
                    $colorsArray = $colorsInput;
                } else if (is_string($colorsInput)) {
                    $trimmedC = trim($colorsInput);
                    $decoded = json_decode($trimmedC, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $colorsArray = $decoded;
                    } else {
                        $colorsArray = array_filter(array_map('trim', explode(',', $trimmedC)));
                    }
                }
            }

            $saveData = [
                'code' => $code,
                'name' => $data['name'] ?? 'Imported Product',
                'description' => $data['description'] ?? '',
                'category' => $primaryCatId,
                'sub_category' => $primarySubId,
                'categories' => $mainCategoryIds,
                'sub_categories' => $subcategoryIds,
                's_price' => (float)($data['s_price'] ?? $data['sales_price'] ?? $data['sale_price'] ?? 0),
                'rental_price' => (float)($data['rental_price'] ?? $data['rent_price'] ?? 0),
                'deposit' => (float)($data['deposit'] ?? 0),
                'size_avail' => $data['size_avail'] ?? $data['size'] ?? '',
                'brand_name' => $data['brand_name'] ?? $data['brand'] ?? '',
                'colors' => $colorsArray,
                'brand_color' => $colorsArray,
                'price_source' => (!empty($data['price_source']) && strtolower($data['price_source']) === 'manual') ? 'manual' : 'pos',
                'availability' => in_array(strtolower($data['availability'] ?? ''), ['rent', 'sell', 'both']) ? strtolower($data['availability']) : 'both'
            ];

            $productModel->saveProduct($type, $saveData, $downloadedImages);
            return $this->json(['status' => 'success', 'message' => "Product $code imported successfully with categories and " . count($downloadedImages) . " images"]);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function bulkDelete() {
        $this->view('products/bulk_delete');
    }

    public function downloadDeleteTemplate() {
        while (ob_get_level()) ob_end_clean();
        
        $csv = "sku\n";
        $csv .= "SKU123\n";
        $csv .= "SKU456\n";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="bulk_delete_template.csv"');
        header('Content-Length: ' . strlen($csv));
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $csv;
        exit;
    }

    public function processBulkDelete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'No file uploaded or upload error'], 400);
        }

        $file = $_FILES['file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip header
        $header = @fgetcsv($handle, 0, ',', '"', '\\');
        if (!$header) {
             $this->json(['error' => 'Empty file'], 400);
        }
        $skuIndex = array_search('sku', array_map('strtolower', $header));

        if ($skuIndex === false) {
            $this->json(['error' => 'Invalid file format. Must have a "sku" column.'], 400);
        }

        $productModel = new ProductModel();
        $deletedCount = 0;
        $failedCount = 0;
        $skus = [];

        while (($row = @fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $sku = trim($row[$skuIndex]);
            if (empty($sku)) continue;
            
            if ($productModel->deleteBySku($sku)) {
                $deletedCount++;
                $skus[] = $sku;
            } else {
                $failedCount++;
            }
        }
        fclose($handle);

        $this->json([
            'success' => true,
            'message' => "Successfully deleted $deletedCount products. $failedCount failed or not found.",
            'deletedCount' => $deletedCount,
            'failedCount' => $failedCount,
            'skus' => $skus
        ]);
    }

    public function bulkUpdate() {
        $productModel = new ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garments = $productModel->getGarments();
        $this->view('products/bulk_update', [
            'jewelCategories' => $jewelCategories,
            'garments' => $garments
        ]);
    }

    public function processBulkUpdate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $rawSkus = $input['skus'] ?? '';
        $priceSource = $input['price_source'] ?? 'no_change';
        $availability = $input['availability'] ?? 'no_change';
        
        $updateBrand = isset($input['update_brand']) && $input['update_brand'] === true;
        $brandName = $input['brand_name'] ?? '';
        
        $updateCategory = isset($input['update_category']) && $input['update_category'] === true;
        $categoryType = $input['category_type'] ?? ''; // 'jewellery' or 'garments'
        $categoryId = (int)($input['category_id'] ?? 0);
        $subCategoryId = (int)($input['subcategory_id'] ?? 0);

        // Parse SKUs (split by newline, comma, space)
        $skusList = preg_split('/[\n,\s]+/', $rawSkus);
        $skus = array_filter(array_map('trim', $skusList));

        if (empty($skus)) {
            $this->json(['error' => 'Please enter at least one SKU.'], 400);
            return;
        }

        // Verify if at least one change is selected
        if ($priceSource === 'no_change' && $availability === 'no_change' && !$updateBrand && !$updateCategory) {
            $this->json(['error' => 'No update actions selected. Please choose at least one setting to update.'], 400);
            return;
        }

        $productModel = new ProductModel();
        $db = $productModel->getDbConnection();

        $updatedCount = 0;
        $notFoundSkus = [];
        $errors = [];

        foreach ($skus as $sku) {
            $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
            $existsGarment = $productModel->checkProductExists($sku, 'garments');

            if (!$existsJewel && !$existsGarment) {
                $notFoundSkus[] = $sku;
                continue;
            }

            $success = false;

            if ($existsJewel) {
                $sets = [];
                $params = [];
                $types = "";

                if ($priceSource !== 'no_change') {
                    $sets[] = "price_source = ?";
                    $params[] = $priceSource;
                    $types .= "s";
                }
                if ($availability !== 'no_change') {
                    $sets[] = "availability = ?";
                    $params[] = $availability;
                    $types .= "s";
                }
                if ($updateBrand) {
                    $sets[] = "brand_name = ?";
                    $params[] = $brandName;
                    $types .= "s";
                }
                if ($updateCategory && $categoryType === 'jewellery') {
                    $sets[] = "categories_id = ?";
                    $params[] = $categoryId;
                    $types .= "i";
                    
                    $sets[] = "subcat_id = ?";
                    $params[] = $subCategoryId;
                    $types .= "i";
                }

                if (!empty($sets)) {
                    $setString = implode(', ', $sets);
                    $sql = "UPDATE product SET $setString WHERE product_code = ?";
                    $stmt = mysqli_prepare($db, $sql);
                    
                    if ($stmt) {
                        $bindParams = [];
                        $bindTypes = $types . "s";
                        $bindParams[] = &$bindTypes;
                        for ($j = 0; $j < count($params); $j++) {
                            $bindParams[] = &$params[$j];
                        }
                        $bindParams[] = &$sku;
                        
                        call_user_func_array([$stmt, 'bind_param'], $bindParams);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $success = true;
                        } else {
                            $errors[] = "SKU $sku (Jewellery): " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $errors[] = "SKU $sku (Jewellery): Failed to prepare query: " . mysqli_error($db);
                    }
                } else {
                    $success = true; // Nothing to change for Jewellery
                }
            }

            if ($existsGarment) {
                $sets = [];
                $params = [];
                $types = "";

                if ($priceSource !== 'no_change') {
                    $sets[] = "price_source = ?";
                    $params[] = $priceSource;
                    $types .= "s";
                }
                if ($availability !== 'no_change') {
                    $sets[] = "availability = ?";
                    $params[] = $availability;
                    $types .= "s";
                }
                if ($updateBrand) {
                    $sets[] = "brand_name = ?";
                    $params[] = $brandName;
                    $types .= "s";
                }
                if ($updateCategory && $categoryType === 'garments') {
                    $sets[] = "garment_id = ?";
                    $params[] = $categoryId;
                    $types .= "i";
                    
                    $sets[] = "product_for = ?";
                    $params[] = $subCategoryId;
                    $types .= "i";
                }

                if (!empty($sets)) {
                    $setString = implode(', ', $sets);
                    $sql = "UPDATE garment_product SET $setString WHERE gproduct_code = ?";
                    $stmt = mysqli_prepare($db, $sql);
                    
                    if ($stmt) {
                        $bindParams = [];
                        $bindTypes = $types . "s";
                        $bindParams[] = &$bindTypes;
                        for ($j = 0; $j < count($params); $j++) {
                            $bindParams[] = &$params[$j];
                        }
                        $bindParams[] = &$sku;
                        
                        call_user_func_array([$stmt, 'bind_param'], $bindParams);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $success = true;
                        } else {
                            $errors[] = "SKU $sku (Garments): " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $errors[] = "SKU $sku (Garments): Failed to prepare query: " . mysqli_error($db);
                    }
                } else {
                    $success = true; // Nothing to change for Garments
                }
            }

            if ($success) {
                $updatedCount++;
            }
        }

        $this->json([
            'success' => true,
            'message' => "Bulk update completed.",
            'updatedCount' => $updatedCount,
            'notFoundCount' => count($notFoundSkus),
            'notFoundSkus' => $notFoundSkus,
            'errors' => $errors
        ]);
    }

    public function processBulkPriceUpdate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $productModel = new ProductModel();
        $updates = [];
        $errors = [];
        $notFoundSkus = [];
        $updatedCount = 0;

        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $inputMode = $input['price_input_mode'] ?? 'paste';
            $priceData = $input['price_data'] ?? '';
        } else {
            $inputMode = $_POST['price_input_mode'] ?? 'paste';
            $priceData = $_POST['price_data'] ?? '';
        }

        if ($inputMode === 'paste') {
            if (empty($priceData)) {
                $this->json(['error' => 'Please paste some pricing data.'], 400);
                return;
            }

            $rawRows = preg_split('/\r\n|\r|\n/', $priceData);
            foreach ($rawRows as $row) {
                $row = trim($row);
                if (empty($row)) continue;

                // Split by tab, comma, or multiple spaces
                $cols = preg_split('/\t|,| {2,}/', $row);
                $cols = array_filter(array_map('trim', $cols));

                if (count($cols) < 4) {
                    // Try split by single space if columns weren't separated by tabs or commas
                    $cols = preg_split('/\s+/', $row);
                    if (count($cols) < 4) {
                        $errors[] = "Row invalid (must have 4 columns: SKU, MRP, Rent, Deposit): \"$row\"";
                        continue;
                    }
                }

                $sku = $cols[0];
                // Skip header row if present
                if (strtolower($sku) === 'sku' || strtolower($sku) === 'wid' || strtolower($sku) === 'code') {
                    continue;
                }

                $mrp = floatval(preg_replace('/[^\d.]/', '', $cols[1]));
                $rent = floatval(preg_replace('/[^\d.]/', '', $cols[2]));
                $deposit = floatval(preg_replace('/[^\d.]/', '', $cols[3]));

                $updates[] = [
                    'sku' => $sku,
                    'mrp' => $mrp,
                    'rent' => $rent,
                    'deposit' => $deposit
                ];
            }
        } elseif ($inputMode === 'file' || isset($_FILES['price_file'])) {
            if (!isset($_FILES['price_file']) || $_FILES['price_file']['error'] !== UPLOAD_ERR_OK) {
                $this->json(['error' => 'No file uploaded or upload error.'], 400);
                return;
            }

            $file = $_FILES['price_file']['tmp_name'];
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Find column indices
                $headerRow = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE)[0];

                $skuIdx = -1;
                $mrpIdx = -1;
                $rentIdx = -1;
                $depositIdx = -1;

                foreach ($headerRow as $idx => $header) {
                    if ($header === null) continue;
                    $header = strtolower(trim($header));
                    if (in_array($header, ['sku', 'wid', 'code', 'product_code', 'gproduct_code'])) {
                        $skuIdx = $idx;
                    } elseif (in_array($header, ['mrp', 'price', 'sales_price', 's_price', 'selling'])) {
                        $mrpIdx = $idx;
                    } elseif (in_array($header, ['rent', 'rental', 'rental+gst', 'rent_price', 'rental_price'])) {
                        $rentIdx = $idx;
                    } elseif (in_array($header, ['deposit', 'sd', 'security', 'security_deposit', 'deposite'])) {
                        $depositIdx = $idx;
                    }
                }

                if ($skuIdx === -1 || $mrpIdx === -1 || $rentIdx === -1 || $depositIdx === -1) {
                    $this->json(['error' => 'Could not detect column headers. Make sure you have columns like SKU/wid, MRP, Rent/rental+gst, Deposit/sd.'], 400);
                    return;
                }

                for ($row = 2; $row <= $highestRow; $row++) {
                    $skuCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($skuIdx + 1);
                    $mrpCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mrpIdx + 1);
                    $rentCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($rentIdx + 1);
                    $depositCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($depositIdx + 1);

                    $sku = trim($sheet->getCell($skuCol . $row)->getValue() ?? '');
                    if (empty($sku)) continue;

                    $mrpVal = $sheet->getCell($mrpCol . $row)->getValue();
                    $rentVal = $sheet->getCell($rentCol . $row)->getValue();
                    $depositVal = $sheet->getCell($depositCol . $row)->getValue();

                    // Clean and extract numeric values, or fallback to null
                    $mrp = ($mrpVal !== null && $mrpVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $mrpVal)) : null;
                    $rent = ($rentVal !== null && $rentVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $rentVal)) : null;
                    $deposit = ($depositVal !== null && $depositVal !== '') ? floatval(preg_replace('/[^\d.]/', '', $depositVal)) : null;

                    $updates[] = [
                        'sku' => $sku,
                        'mrp' => $mrp,
                        'rent' => $rent,
                        'deposit' => $deposit
                    ];
                }
            } catch (\Exception $e) {
                $this->json(['error' => 'Failed to parse Excel file: ' . $e->getMessage()], 500);
                return;
            }
        } else {
            $this->json(['error' => 'Invalid request mode.'], 400);
            return;
        }

        if (empty($updates)) {
            $this->json(['error' => 'No valid pricing records found to update.'], 400);
            return;
        }

        foreach ($updates as $update) {
            $sku = $update['sku'];
            $mrpVal = $update['mrp'];
            $rentVal = $update['rent'];
            $depositVal = $update['deposit'];

            $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
            $existsGarment = $productModel->checkProductExists($sku, 'garments');

            if (!$existsJewel && !$existsGarment) {
                $notFoundSkus[] = $sku;
                continue;
            }

            // Fetch current product to merge if values are missing (e.g. rent is null)
            $type = $existsJewel ? 'jewellery' : 'garments';
            $pid = $existsJewel ? $existsJewel['product_id'] : $existsGarment['gproduct_id'];
            $currentProduct = $productModel->getProductById($pid, $type);

            if (!$currentProduct) {
                $errors[] = "SKU $sku: Failed to retrieve current product info.";
                continue;
            }

            $finalMrp = ($mrpVal !== null) ? $mrpVal : (float)($currentProduct['s_price'] ?? 0);
            $finalRent = ($rentVal !== null) ? $rentVal : (float)($currentProduct['rental_price'] ?? 0);
            $finalDeposit = ($depositVal !== null) ? $depositVal : (float)($currentProduct['deposit'] ?? 0);

            if ($productModel->updateProductPrices($sku, $finalMrp, $finalRent, $finalDeposit)) {
                $updatedCount++;
            } else {
                $errors[] = "SKU $sku: Database update failed.";
            }
        }

        $this->json([
            'success' => true,
            'message' => "Bulk price update completed.",
            'updatedCount' => $updatedCount,
            'notFoundCount' => count($notFoundSkus),
            'notFoundSkus' => $notFoundSkus,
            'errors' => $errors
        ]);
    }

    public function uploadSkuImages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $sku = trim($_POST['sku'] ?? '');
        if (empty($sku)) {
            $this->json(['error' => 'SKU is required'], 400);
            return;
        }

        if (empty($_FILES['files']['name'])) {
            $this->json(['error' => 'No files uploaded'], 400);
            return;
        }

        $productModel = new ProductModel();
        $existsJewel = $productModel->checkProductExists($sku, 'jewellery');
        $existsGarment = $productModel->checkProductExists($sku, 'garments');

        if (!$existsJewel && !$existsGarment) {
            $this->json(['error' => "SKU '$sku' not found in database"], 404);
            return;
        }

        $uploadedPaths = [];
        $files = $_FILES['files'];
        $current_year = date('Y');
        $current_month = date('m');
        $upload_base = __DIR__ . "/../../yn/uploads/";
        $upload_path = $current_year . '/' . $current_month . '/';
        $full_upload_path = $upload_base . $upload_path;

        if (!file_exists($full_upload_path)) {
            mkdir($full_upload_path, 0777, true);
        }

        // Loop files
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $sku . '_' . time() . '_' . uniqid() . '.' . $ext;

            if (move_uploaded_file($files['tmp_name'][$i], $full_upload_path . $filename)) {
                $uploadedPaths[] = $upload_path . $filename;
            }
        }

        if (empty($uploadedPaths)) {
            $this->json(['error' => 'Failed to save any uploaded images'], 500);
            return;
        }

        try {
            if ($productModel->addImagesToProduct($sku, $uploadedPaths)) {
                $this->json([
                    'success' => true,
                    'message' => "Successfully uploaded " . count($uploadedPaths) . " images for SKU $sku."
                ]);
            } else {
                $this->json(['error' => 'Failed to link images to product database'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function setMainImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $imageId = (int)($input['image_id'] ?? 0);
        $productId = (int)($input['product_id'] ?? 0);
        $type = $input['type'] ?? 'jewellery';

        if (!$imageId || !$productId || !in_array($type, ['jewellery', 'garments'])) {
            $this->json(['error' => 'Invalid parameters'], 400);
            return;
        }

        $productModel = new ProductModel();
        try {
            if ($productModel->setMainProductImage($imageId, $productId, $type)) {
                $this->json(['success' => true, 'message' => 'Main product image updated successfully']);
            } else {
                $this->json(['error' => 'Failed to update main image'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateImageWeight() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $imageId = (int)($input['image_id'] ?? 0);
        $weight = (int)($input['weight'] ?? 0);

        if (!$imageId) {
            $this->json(['error' => 'Image ID is required'], 400);
            return;
        }

        $productModel = new ProductModel();
        try {
            if ($productModel->updateImageWeight($imageId, $weight)) {
                $this->json(['success' => true, 'message' => 'Image weight updated successfully']);
            } else {
                $this->json(['error' => 'Failed to update image weight'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public function descriptionCorrector() {
        $productModel = new ProductModel();
        $products = $productModel->getPoorlyFormattedProducts();
        
        $cleanedProducts = [];
        foreach ($products as $p) {
            $desc = $p['description'] ?? '';
            // Cleaning algorithm
            $cleaned = preg_replace('/^\s*•\s*/u', '', $desc);
            $cleaned = trim($cleaned);
            if (str_starts_with($cleaned, '"') && str_ends_with($cleaned, '"')) {
                $cleaned = substr($cleaned, 1, -1);
            }
            $cleaned = trim($cleaned);
            
            if (str_contains($cleaned, '??')) {
                $parts = explode('??', $cleaned);
                $new_text = trim($parts[0]);
                for ($i = 1; $i < count($parts); $i++) {
                    $new_text .= "\n" . $i . ") " . trim($parts[$i]);
                }
                $cleaned = $new_text;
            }
            
            $p['corrected_description'] = $cleaned;
            $cleanedProducts[] = $p;
        }
        
        $this->view('products/desc_corrector', [
            'products' => $cleanedProducts
        ]);
    }
    public function bulkAiWriter() {
        $productModel = new ProductModel();
        $categories = $productModel->getCategories();
        
        $secretsFile = __DIR__ . '/../Config/secrets.php';
        $hasApiKey = false;
        if (file_exists($secretsFile)) {
            $sec = include($secretsFile);
            $hasApiKey = !empty($sec['GEMINI_API_KEY']);
        }

        $this->view('products/bulk_ai_writer', [
            'categories' => $categories,
            'hasApiKey' => $hasApiKey
        ]);
    }

        public function bulkAiLoadProducts() {
        $category = $_GET['category'] ?? '';
        $filterType = $_GET['filter_type'] ?? 'needs_content';
        $search = trim($_GET['search'] ?? '');
        $nameFilter = trim($_GET['name_filter'] ?? '');
        $descFilter = trim($_GET['desc_filter'] ?? '');
        $skuFilter = trim($_GET['sku_filter'] ?? '');
        $limit = isset($_GET['limit']) ? min(200, max(10, (int)$_GET['limit'])) : 50;

        $db = \Core\Database::getConnection('con');
        if (!$db) {
            $this->json(['success' => false, 'error' => 'Database connection failed'], 500);
            return;
        }

        $jewelWhere = ["1=1"];
        $garmentWhere = ["1=1"];

        // Parse SKUs (split by comma, space, newline, or tab)
        $skus = [];
        if ($skuFilter !== '') {
            $rawSkus = preg_split('/[\r\n,\s]+/', $skuFilter);
            $skus = array_values(array_unique(array_filter(array_map('trim', $rawSkus))));
        }

        // Expand limit if user supplied a list of SKUs larger than current limit
        if (!empty($skus) && count($skus) > 1) {
            $limit = max($limit, min(500, count($skus)));
        }

        // Dedicated Name Filter
        if ($nameFilter !== '') {
            $escName = mysqli_real_escape_string($db, $nameFilter);
            if ($nameFilter === '1' || $nameFilter === '0') {
                $jewelWhere[] = "(TRIM(product_name) = '$escName' OR product_name = '$escName')";
                $garmentWhere[] = "(TRIM(gproduct_name) = '$escName' OR gproduct_name = '$escName')";
            } else {
                $jewelWhere[] = "product_name LIKE '%$escName%'";
                $garmentWhere[] = "gproduct_name LIKE '%$escName%'";
            }
        }

        // Dedicated Description Filter
        if ($descFilter !== '') {
            $escDesc = mysqli_real_escape_string($db, $descFilter);
            if ($descFilter === '1' || $descFilter === '0') {
                $jewelWhere[] = "(TRIM(product_desc) = '$escDesc' OR product_desc = '$escDesc' OR TRIM(short_desc) = '$escDesc')";
                $garmentWhere[] = "(TRIM(gproduct_desc) = '$escDesc' OR gproduct_desc = '$escDesc' OR TRIM(short_desc) = '$escDesc')";
            } else {
                $jewelWhere[] = "(product_desc LIKE '%$escDesc%' OR short_desc LIKE '%$escDesc%')";
                $garmentWhere[] = "(gproduct_desc LIKE '%$escDesc%' OR short_desc LIKE '%$escDesc%')";
            }
        }

        // Dedicated SKU Filter (Single SKU or Comma/Space-separated multiple SKUs)
        if (!empty($skus)) {
            if (count($skus) === 1) {
                $escSku = mysqli_real_escape_string($db, $skus[0]);
                $jewelWhere[] = "product_code LIKE '%$escSku%'";
                $garmentWhere[] = "gproduct_code LIKE '%$escSku%'";
            } else {
                $escapedSkus = array_map(function($s) use ($db) {
                    return "'" . mysqli_real_escape_string($db, $s) . "'";
                }, $skus);
                $inList = implode(',', $escapedSkus);

                $jewelOrs = ["product_code IN ($inList)", "TRIM(product_code) IN ($inList)"];
                $garmentOrs = ["gproduct_code IN ($inList)", "TRIM(gproduct_code) IN ($inList)"];

                foreach ($skus as $s) {
                    $esc = mysqli_real_escape_string($db, $s);
                    if (strlen($s) >= 3) {
                        $jewelOrs[] = "product_code LIKE '%$esc%'";
                        $garmentOrs[] = "gproduct_code LIKE '%$esc%'";
                    }
                }

                $jewelWhere[] = "(" . implode(" OR ", array_unique($jewelOrs)) . ")";
                $garmentWhere[] = "(" . implode(" OR ", array_unique($garmentOrs)) . ")";
            }
        }

        // General Search (searches everything)
        if (!empty($search)) {
            $escSearch = mysqli_real_escape_string($db, $search);
            $jewelWhere[] = "(product_name LIKE '%$escSearch%' OR product_code LIKE '%$escSearch%' OR product_desc LIKE '%$escSearch%' OR short_desc LIKE '%$escSearch%')";
            $garmentWhere[] = "(gproduct_name LIKE '%$escSearch%' OR gproduct_code LIKE '%$escSearch%' OR gproduct_desc LIKE '%$escSearch%' OR short_desc LIKE '%$escSearch%')";
        }

        // Category Filter
        if (!empty($category) && strpos($category, ':') !== false) {
            list($type, $id) = explode(':', $category);
            $id = (int)$id;

            if ($type === 'garment') {
                $garmentWhere[] = "(garment_id = $id OR product_for = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = garment_product.gproduct_id AND pc.product_type = 'garments' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id = $id)))";
                $jewelWhere[] = "1=0";
            } elseif ($type === 'jewel_parent' || $type === 'jewel_main') {
                $jewelWhere[] = "(categories_id = $id OR subcat_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id) OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = product.product_id AND pc.product_type = 'jewellery' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id))))";
                $garmentWhere[] = "1=0";
            } elseif ($type === 'jewel_child' || $type === 'jewel_sub') {
                $jewelWhere[] = "(subcat_id = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = product.product_id AND pc.product_type = 'jewellery' AND pc.legacy_subcategory_id = $id))";
                $garmentWhere[] = "1=0";
            }
        }

        // Quality Presets
        if ($filterType === 'name_is_1') {
            $jewelWhere[] = "(TRIM(product_name) = '1' OR product_name = '1')";
            $garmentWhere[] = "(TRIM(gproduct_name) = '1' OR gproduct_name = '1')";
        } elseif ($filterType === 'desc_is_1') {
            $jewelWhere[] = "(TRIM(product_desc) = '1' OR product_desc = '1' OR TRIM(short_desc) = '1')";
            $garmentWhere[] = "(TRIM(gproduct_desc) = '1' OR gproduct_desc = '1' OR TRIM(short_desc) = '1')";
        } elseif ($filterType === 'name_or_desc_is_1') {
            $jewelWhere[] = "(TRIM(product_name) = '1' OR product_name = '1' OR TRIM(product_desc) = '1' OR product_desc = '1')";
            $garmentWhere[] = "(TRIM(gproduct_name) = '1' OR gproduct_name = '1' OR TRIM(gproduct_desc) = '1' OR gproduct_desc = '1')";
        } elseif ($filterType === 'needs_content') {
            if (empty($skus)) {
                $jewelWhere[] = "(TRIM(product_name) = '1' OR product_name = '1' OR product_name = product_code OR product_name LIKE 'YN%' OR short_desc IS NULL OR short_desc = '' OR TRIM(short_desc) = '1' OR product_desc IS NULL OR product_desc = '' OR TRIM(product_desc) = '1' OR product_desc LIKE '%Premium Quality%' OR product_desc LIKE '%Collection%')";
                $garmentWhere[] = "(TRIM(gproduct_name) = '1' OR gproduct_name = '1' OR gproduct_name = gproduct_code OR gproduct_name LIKE 'YN%' OR short_desc IS NULL OR short_desc = '' OR TRIM(short_desc) = '1' OR gproduct_desc IS NULL OR gproduct_desc = '' OR TRIM(gproduct_desc) = '1' OR gproduct_desc LIKE '%Premium Quality%' OR gproduct_desc LIKE '%Collection%')";
            }
        } elseif ($filterType === 'missing_desc') {
            $jewelWhere[] = "(product_desc IS NULL OR product_desc = '' OR TRIM(product_desc) = '1' OR product_desc LIKE '%Premium Quality%')";
            $garmentWhere[] = "(gproduct_desc IS NULL OR gproduct_desc = '' OR TRIM(gproduct_desc) = '1' OR gproduct_desc LIKE '%Premium Quality%')";
        } elseif ($filterType === 'missing_short_desc') {
            $jewelWhere[] = "(short_desc IS NULL OR short_desc = '' OR TRIM(short_desc) = '1' OR short_desc = product_name)";
            $garmentWhere[] = "(short_desc IS NULL OR short_desc = '' OR TRIM(short_desc) = '1' OR short_desc = gproduct_name)";
        }

        $jewelWhereSql = implode(" AND ", $jewelWhere);
        $garmentWhereSql = implode(" AND ", $garmentWhere);

        $unionQuery = "
            (SELECT 
                product_id as id,
                product_name as name,
                product_code as code,
                'jewellery' as type,
                short_desc,
                product_desc as description,
                categories_id as category_id,
                subcat_id as subcategory_id
            FROM product 
            WHERE $jewelWhereSql)
            UNION ALL
            (SELECT 
                gproduct_id as id,
                gproduct_name as name,
                gproduct_code as code,
                'garment' as type,
                short_desc,
                gproduct_desc as description,
                garment_id as category_id,
                product_for as subcategory_id
            FROM garment_product 
            WHERE $garmentWhereSql)
            ORDER BY id DESC
            LIMIT $limit
        ";

        $res = mysqli_query($db, $unionQuery);
        $products = [];
        $productModel = new ProductModel();

        while ($row = mysqli_fetch_assoc($res)) {
            $images = $productModel->getProductImages($row['id'], $row['type']);
            $imgUrl = '';
            if (!empty($images[0]['img_name'])) {
                $raw = $images[0]['img_name'];
                if (str_starts_with($raw, 'http')) {
                    $imgUrl = $raw;
                } else {
                    $clean = ltrim($raw, '/');
                    $imgUrl = 'https://srishringarr.com/yn/uploads/' . $clean;
                }
            }

            // Resolve category name
            $catName = 'Catalog';
            if ($row['type'] === 'jewellery') {
                if (!empty($row['subcategory_id'])) {
                    $subQ = mysqli_query($db, "SELECT name FROM subcat1 WHERE subcat_id = " . (int)$row['subcategory_id']);
                    if ($subR = mysqli_fetch_assoc($subQ)) $catName = 'Jewellery > ' . ucwords(strtolower($subR['name']));
                } elseif (!empty($row['category_id'])) {
                    $mQ = mysqli_query($db, "SELECT categories_name FROM jewel_subcat WHERE subcat_id = " . (int)$row['category_id']);
                    if ($mR = mysqli_fetch_assoc($mQ)) $catName = 'Jewellery > ' . ucwords(strtolower($mR['categories_name']));
                }
            } else {
                if (!empty($row['category_id'])) {
                    $gQ = mysqli_query($db, "SELECT name FROM garments WHERE garment_id = " . (int)$row['category_id']);
                    if ($gR = mysqli_fetch_assoc($gQ)) $catName = 'Apparel > ' . ucwords(strtolower($gR['name']));
                }
            }

            $products[] = [
                'id' => (int)$row['id'],
                'type' => $row['type'],
                'code' => $row['code'],
                'name' => $row['name'],
                'short_desc' => $row['short_desc'] ?? '',
                'description' => $row['description'] ?? '',
                'image_url' => $imgUrl,
                'category_name' => $catName
            ];
        }

        $countQuery = "
            SELECT 
            (SELECT COUNT(*) FROM product WHERE $jewelWhereSql) + 
            (SELECT COUNT(*) FROM garment_product WHERE $garmentWhereSql) as total
        ";
        $countRes = mysqli_query($db, $countQuery);
        $totalCount = (int)(mysqli_fetch_assoc($countRes)['total'] ?? count($products));

        $this->json([
            'success' => true,
            'total_count' => $totalCount,
            'products' => $products
        ]);
    }

    public function aiGenerateBulkContent() {
        $id = (int)($_GET['id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['error' => 'Product ID is required'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if (empty($apiKey)) {
            $this->json(['error' => 'Gemini API Key is not configured in Config/secrets.php'], 400);
            return;
        }

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id, $type);
        $images = $productModel->getProductImages($id, $type);

        if (empty($images)) {
            $this->json(['error' => 'Product has no images to analyze.'], 400);
            return;
        }

        $imgRelativePath = $images[0]['img_name'];
        $cleanPath = ltrim($imgRelativePath, '/');
        
        $localPaths = [
            __DIR__ . '/../../yn/uploads/' . $cleanPath,
            __DIR__ . '/../../uploads/' . $cleanPath,
            __DIR__ . '/../uploads/' . $cleanPath,
            'C:/xampp/htdocs/ss/yn/uploads/' . $cleanPath,
            'C:/xampp/htdocs/yn/admin/uploads/' . $cleanPath
        ];

        $imgContent = null;
        $mimeType = 'image/jpeg';

        foreach ($localPaths as $lp) {
            if (file_exists($lp)) {
                $imgContent = file_get_contents($lp);
                $mime = @mime_content_type($lp);
                if ($mime) $mimeType = $mime;
                break;
            }
        }

        if (empty($imgContent)) {
            $remoteUrls = [
                'https://srishringarr.com/yn/uploads/' . $cleanPath,
                'https://srishringarr.com/uploads/' . $cleanPath,
                'https://yosshitaneha.com/admin/uploads/' . $cleanPath
            ];
            foreach ($remoteUrls as $url) {
                $imgContent = @file_get_contents($url);
                if (!empty($imgContent)) {
                    $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
                    if ($ext === 'png') $mimeType = 'image/png';
                    elseif ($ext === 'webp') $mimeType = 'image/webp';
                    break;
                }
            }
        }

        if (empty($imgContent)) {
            $this->json(['error' => 'Failed to load product image for AI analysis.'], 400);
            return;
        }

        $base64Image = base64_encode($imgContent);
        $categoryContext = $type === 'jewellery' ? 'Indian Fine & Bridal Jewellery' : 'Indian Luxury Designer Outfits & Apparel';
        $currentName = $product['name'] ?? ($product['product_name'] ?? ($product['gproduct_name'] ?? ''));
        $sku = $product['code'] ?? ($product['product_code'] ?? ($product['gproduct_code'] ?? ''));

        $prompt = "You are an expert luxury Indian fashion and bridal jewellery copywriter for Srishringarr Fashion Studio, Mumbai.\n" .
                  "Carefully examine the attached product photograph and context.\n\n" .
                  "Product Type: " . $categoryContext . "\n" .
                  "SKU Code: " . $sku . "\n" .
                  "Current Working Title: " . $currentName . "\n\n" .
                  "Based on visual analysis of the product's colors, fabrics, stones, metal plating, embroidery, pattern, and design silhouette, generate complete e-commerce product copy in JSON format with exactly three fields:\n" .
                  "1. \"name\": A clear, descriptive, and elegant product title (10 to 14 words long). Use simple, natural English. Include specific color, fabric/material (e.g. Pure Silk, Velvet, Brass/Copper Gold Plated, Kundan, Vilandi, Kalamkari), key design motifs, and style type. Do NOT use overly complex, archaic, or poetic words like 'resplendent', 'ethereal', 'wisteria', 'intricately'.\n" .
                  "2. \"short_description\": A compelling 1 to 2 sentence highlight summary (20 to 35 words) perfect for search previews and quick overview.\n" .
                  "3. \"description\": A rich, detailed product description (75 to 120 words). Start with an engaging paragraph describing its artisanal craftsmanship, aesthetic appeal, and suitability for weddings, festive occasions, sangeet, or receptions. Follow with 'Key Features:' and 3 to 5 concise bullet points starting with the bullet character '• ' (e.g., '• Fabric/Material: ...', '• Work/Embroidery: ...', '• Color Palette: ...', '• Occasion: ...'). Do NOT use markdown asterisks (no '**').\n\n" .
                  "Return ONLY a valid, parseable JSON object with keys \"name\", \"short_description\", and \"description\".";

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
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.4,
                'maxOutputTokens' => 2048,
            ]
        ]);

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
            $this->json(['error' => 'Gemini API request failed (HTTP ' . $httpCode . '): ' . $response], 500);
            return;
        }

        $decoded = json_decode($response, true);
        $rawText = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $cleanText = trim(preg_replace('/^```json|```$/', '', trim($rawText)));
        
        $resultJson = json_decode($cleanText, true);
        if (!is_array($resultJson) || empty($resultJson['name'])) {
            if (preg_match('/\{[\s\S]*\}/', $cleanText, $matches)) {
                $resultJson = json_decode($matches[0], true);
            }
        }

        if (!is_array($resultJson) || empty($resultJson['name'])) {
            $this->json(['error' => 'Could not parse Gemini JSON response: ' . $cleanText], 500);
            return;
        }

        // Log AI Generation to ai_analytics
        $db = \Core\Database::getConnection('con');
        if ($db) {
            $promptTokens = (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
            $candidateTokens = (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
            $totalTokens = (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);
            $costEstimate = max(0.01, (($promptTokens * 0.000000075) + ($candidateTokens * 0.0000003)) * 86);
            $genOutput = json_encode($resultJson);
            $opType = 'bulk_content';
            $numImg = 1;
            $website = 'srishringarr';

            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN operation_type VARCHAR(50) DEFAULT 'image'");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN generated_output TEXT NULL");
            @mysqli_query($db, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");

            $stmt = $db->prepare("INSERT INTO ai_analytics (product_id, product_type, operation_type, prompt_text, generated_output, num_images, prompt_tokens, candidate_tokens, total_tokens, cost_estimate, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issssiiiids", $id, $type, $opType, $prompt, $genOutput, $numImg, $promptTokens, $candidateTokens, $totalTokens, $costEstimate, $website);
                $stmt->execute();
                $stmt->close();
            }
        }

        $this->json([
            'success' => true,
            'id' => $id,
            'type' => $type,
            'name' => trim($resultJson['name'] ?? ''),
            'short_description' => trim($resultJson['short_description'] ?? ''),
            'description' => trim($resultJson['description'] ?? '')
        ]);
    }

    public function saveBulkAiContent() {
        $rawInput = json_decode(file_get_contents('php://input'), true);
        $id = (int)($rawInput['id'] ?? $_POST['id'] ?? 0);
        $type = ($rawInput['type'] ?? $_POST['type'] ?? 'jewellery') === 'garment' ? 'garment' : 'jewellery';
        $name = trim($rawInput['name'] ?? $_POST['name'] ?? '');
        $shortDesc = trim($rawInput['short_description'] ?? $_POST['short_description'] ?? '');
        $desc = trim($rawInput['description'] ?? $_POST['description'] ?? '');

        if (!$id || empty($name)) {
            $this->json(['error' => 'Product ID and Title cannot be empty.'], 400);
            return;
        }

        $db = \Core\Database::getConnection('con');
        if (!$db) {
            $this->json(['error' => 'Database connection failed'], 500);
            return;
        }

        $escName = mysqli_real_escape_string($db, $name);
        $escShort = mysqli_real_escape_string($db, $shortDesc);
        $escDesc = mysqli_real_escape_string($db, $desc);

        if ($type === 'jewellery') {
            $sql = "UPDATE product SET product_name = '$escName', short_desc = '$escShort', product_desc = '$escDesc' WHERE product_id = $id";
        } else {
            $sql = "UPDATE garment_product SET gproduct_name = '$escName', short_desc = '$escShort', gproduct_desc = '$escDesc' WHERE gproduct_id = $id";
        }

        if (mysqli_query($db, $sql)) {
            $this->json(['success' => true, 'id' => $id, 'type' => $type, 'message' => 'Product content updated successfully.']);
        } else {
            $this->json(['error' => 'Database update error: ' . mysqli_error($db)], 500);
        }
    }
}
