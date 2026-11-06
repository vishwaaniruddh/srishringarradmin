<?php
namespace Controllers\Products;

use Models\ProductModel;

trait ProductAiTrait {
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

        $provider = strtolower($_GET['ai_provider'] ?? 'gemini');
        $openAiKey = $secrets['OPENAI_API_KEY'] ?? '';
        $geminiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if ($provider === 'openai') {
            if (empty($openAiKey)) {
                $this->json(['error' => 'OpenAI API Key is not configured in secrets.php'], 400);
                return;
            }
            $openAiPayload = [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}",
                                    'detail' => 'high'
                                ]
                            ]
                        ]
                    ]
                ],
                'temperature' => 0.4
            ];
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $openAiKey
                ],
                CURLOPT_POSTFIELDS => json_encode($openAiPayload),
                CURLOPT_TIMEOUT => 35,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode !== 200) {
                $err = json_decode($response, true);
                $this->json(['error' => 'OpenAI API request failed: ' . ($err['error']['message'] ?? $response)], 500);
                return;
            }
            $decoded = json_decode($response, true);
            $text = $decoded['choices'][0]['message']['content'] ?? '';
            $promptTokens = (int)($decoded['usage']['prompt_tokens'] ?? 0);
            $candidateTokens = (int)($decoded['usage']['completion_tokens'] ?? 0);
            $totalTokens = (int)($decoded['usage']['total_tokens'] ?? 0);
        } else {
            if (empty($geminiKey)) {
                $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
                return;
            }
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $geminiKey;
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
        }
        
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

        $provider = strtolower($_GET['ai_provider'] ?? 'gemini');
        $openAiKey = $secrets['OPENAI_API_KEY'] ?? '';
        $geminiKey = $secrets['GEMINI_API_KEY'] ?? '';

        if ($provider === 'openai') {
            if (empty($openAiKey)) {
                $this->json(['error' => 'OpenAI API Key is not configured in secrets.php'], 400);
                return;
            }
            $openAiPayload = [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}",
                                    'detail' => 'high'
                                ]
                            ]
                        ]
                    ]
                ],
                'temperature' => 0.4
            ];
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $openAiKey
                ],
                CURLOPT_POSTFIELDS => json_encode($openAiPayload),
                CURLOPT_TIMEOUT => 35,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode !== 200) {
                $err = json_decode($response, true);
                $this->json(['error' => 'OpenAI API request failed: ' . ($err['error']['message'] ?? $response)], 500);
                return;
            }
            $decoded = json_decode($response, true);
            $description = trim($decoded['choices'][0]['message']['content'] ?? '');
            $promptTokens = (int)($decoded['usage']['prompt_tokens'] ?? 0);
            $candidateTokens = (int)($decoded['usage']['completion_tokens'] ?? 0);
            $totalTokens = (int)($decoded['usage']['total_tokens'] ?? 0);
        } else {
            if (empty($geminiKey)) {
                $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
                return;
            }
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $geminiKey;
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
        }

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

    public function aiAnalyzeUpload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $dataUri = $input['image'] ?? '';
        $target = $input['target'] ?? 'name'; // 'name' or 'desc'
        $provider = strtolower($input['provider'] ?? 'gemini');
        $type = $input['type'] ?? 'jewellery';

        if (empty($dataUri)) {
            $this->json(['error' => 'No image data provided'], 400);
            return;
        }

        // Parse base64 and mime
        $mimeType = 'image/jpeg';
        $base64Image = $dataUri;
        if (preg_match('/^data:(image\/[a-zA-Z0-9\+\-\.]+);base64,(.+)$/', $dataUri, $matches)) {
            $mimeType = $matches[1];
            $base64Image = $matches[2];
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');

        if ($target === 'name') {
            $prompt = "You are a professional fashion copywriter for Srishringarr. " .
                      "Analyze this $type item in the image. Suggest exactly 1 descriptive product title (at least 10 words long). " .
                      "Use simple, clear, and easy-to-understand English describing the item's colors, materials, design, and suitability for weddings or festive parties. " .
                      "Return ONLY the plain title text without quotes or markdown formatting.";
        } else {
            $prompt = "You are a luxury fashion brand copywriter for Srishringarr. " .
                      "Analyze this $type item in the image. Write a detailed, compelling product description (around 60 to 90 words). " .
                      "Include: 1) An introduction highlighting elegance and occasions, and 2) A section titled 'Key Features:' with bullet points starting with '• '. " .
                      "Return ONLY clean plain text without any markdown asterisks (no '**', no '#').";
        }

        if ($provider === 'openai') {
            $apiKey = $secrets['OPENAI_API_KEY'] ?? '';
            if (empty($apiKey)) {
                $this->json(['error' => 'OpenAI API Key is not configured in secrets.php'], 400);
                return;
            }

            $payload = [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}",
                                    'detail' => 'high'
                                ]
                            ]
                        ]
                    ]
                ],
                'temperature' => 0.4
            ];

            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 35,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode !== 200) {
                $err = json_decode($response, true);
                $this->json(['error' => 'OpenAI API failed: ' . ($err['error']['message'] ?? $response)], 500);
                return;
            }

            $decoded = json_decode($response, true);
            $result = trim($decoded['choices'][0]['message']['content'] ?? '');
            $this->json(['success' => true, 'result' => trim($result, '"\'')]);
        } else {
            $apiKey = $secrets['GEMINI_API_KEY'] ?? '';
            if (empty($apiKey)) {
                $this->json(['error' => 'Gemini API Key is not configured in secrets.php'], 400);
                return;
            }

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
                $this->json(['error' => 'Gemini API failed: ' . $response], 500);
                return;
            }

            $decoded = json_decode($response, true);
            $result = trim($decoded['candidates'][0]['content']['parts'][0]['text'] ?? '');
            $this->json(['success' => true, 'result' => trim($result, '"\'')]);
        }
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
            mkdir($uploadDir, 0755, true);
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
            mkdir($upload_path, 0755, true);
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
        $hasGemini = false;
        $hasOpenAi = false;
        if (file_exists($secretsFile)) {
            $sec = include($secretsFile);
            $hasGemini = !empty($sec['GEMINI_API_KEY']);
            $hasOpenAi = !empty($sec['OPENAI_API_KEY']);
            $hasApiKey = $hasGemini || $hasOpenAi;
        }

        $this->view('products/bulk_ai_writer', [
            'categories' => $categories,
            'hasApiKey' => $hasApiKey,
            'hasGemini' => $hasGemini,
            'hasOpenAi' => $hasOpenAi
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
        $provider = strtolower($_GET['ai_provider'] ?? '');
        $geminiApiKey = $secrets['GEMINI_API_KEY'] ?? '';
        $openAiApiKey = $secrets['OPENAI_API_KEY'] ?? '';

        if (empty($provider)) {
            $provider = !empty($geminiApiKey) ? 'gemini' : 'openai';
        }

        if ($provider === 'openai' && empty($openAiApiKey)) {
            if (!empty($geminiApiKey)) $provider = 'gemini';
            else {
                $this->json(['error' => 'OpenAI API Key is not configured in Config/secrets.php'], 400);
                return;
            }
        } elseif ($provider === 'gemini' && empty($geminiApiKey)) {
            if (!empty($openAiApiKey)) $provider = 'openai';
            else {
                $this->json(['error' => 'Gemini API Key is not configured in Config/secrets.php'], 400);
                return;
            }
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

        $resultJson = null;
        $promptTokens = 0;
        $candidateTokens = 0;
        $totalTokens = 0;

        if ($provider === 'openai') {
            $payload = json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert luxury Indian fashion and bridal jewellery copywriter for Srishringarr Fashion Studio, Mumbai. Always return a valid parseable JSON object with keys "name", "short_description", and "description".'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:$mimeType;base64,$base64Image",
                                    'detail' => 'low'
                                ]
                            ]
                        ]
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.4,
                'max_tokens' => 1500
            ]);

            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $openAiApiKey
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 40,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);

            if ($httpCode !== 200) {
                $this->json(['error' => 'OpenAI API request failed (HTTP ' . $httpCode . '): ' . $response], 500);
                return;
            }

            $decoded = json_decode($response, true);
            $rawText = $decoded['choices'][0]['message']['content'] ?? '';
            $resultJson = json_decode($rawText, true);

            $promptTokens = (int)($decoded['usage']['prompt_tokens'] ?? 0);
            $candidateTokens = (int)($decoded['usage']['completion_tokens'] ?? 0);
            $totalTokens = (int)($decoded['usage']['total_tokens'] ?? 0);

        } else {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $geminiApiKey;
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
                CURLOPT_TIMEOUT => 35,
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

            $promptTokens = (int)($decoded['usageMetadata']['promptTokenCount'] ?? 0);
            $candidateTokens = (int)($decoded['usageMetadata']['candidatesTokenCount'] ?? 0);
            $totalTokens = (int)($decoded['usageMetadata']['totalTokenCount'] ?? 0);
        }

        if (!is_array($resultJson) || empty($resultJson['name'])) {
            $this->json(['error' => 'Could not parse AI JSON response.'], 500);
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
