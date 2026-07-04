<?php
namespace Models;

use Core\Model;

class ChatbotModel extends Model {
    
    private $config;
    private $knowledgeBase;
    private $provider;
    
    public function __construct() {
        parent::__construct();
        $this->config = include(__DIR__ . '/../Config/chatbot_config.php');
        
        // Fetch dynamic settings from database
        $db = \Core\Database::getConnection('con');
        $result = $db->query("SELECT * FROM chatbot_settings LIMIT 1");
        if ($result && $result->num_rows > 0) {
            $settings = $result->fetch_assoc();
            $this->config['provider'] = $settings['provider'];
            $this->config['groq']['api_key'] = $settings['groq_key'];
            $this->config['gemini']['api_key'] = $settings['gemini_key'];
            $this->config['openrouter']['api_key'] = $settings['openrouter_key'];
        }

        $this->provider = $this->config['provider'] ?? 'gemini';
        $this->knowledgeBase = json_decode(
            file_get_contents(__DIR__ . '/../Config/chatbot_knowledge.json'), 
            true
        );
    }

    /**
     * Main entry: process a user message and return the AI response.
     */
    public function processMessage($userMessage, $conversationHistory = [], $imageBase64 = null) {
        // Step 1: Build the system prompt with knowledge base
        $systemPrompt = $this->buildSystemPrompt();
        
        // Step 2: Send to AI to classify intent and possibly get SQL
        $aiResponse = $this->callAI($systemPrompt, $userMessage, $conversationHistory, $imageBase64);
        
        if (!$aiResponse) {
            return [
                'reply' => "I'm sorry, I couldn't process your request right now. Please try again.",
                'type'  => 'error'
            ];
        }

        // Step 3: Check if AI wants to execute SQL
        $sqlMatch = [];
        if (preg_match('/\\\\?\[SQL_QUERY\\\\?\](.*?)\\\\?\[\\\\?\/SQL_QUERY\\\\?\]/s', $aiResponse, $sqlMatch)) {
            $sqlQuery = trim($sqlMatch[1]);
            $dbType = 'con'; // default
            
            // Detect which database to use
            $dbMatch = [];
            if (preg_match('/\\\\?\[DB\\\\?\](.*?)\\\\?\[\\\\?\/DB\\\\?\]/s', $aiResponse, $dbMatch)) {
                $dbType = trim($dbMatch[1]);
            }
            
            // Validate & execute SQL
            $sqlResult = $this->executeSafeQuery($sqlQuery, $dbType);
            
            if ($sqlResult['success']) {
                // Step 4: Send results back to AI for a human-friendly response
                $followUp = $this->callAIWithResults(
                    $systemPrompt, 
                    $userMessage, 
                    $sqlQuery, 
                    $sqlResult['data'],
                    $conversationHistory
                );
                
                if (!$followUp) {
                    $totalCount = count($sqlResult['data']);
                    $followUp = "I retrieved **{$totalCount}** record(s) from the database but ran into an issue formatting the response using the AI model (possibly due to token size or rate limits). Here is the raw data:\n\n";
                    if ($totalCount > 0) {
                        $followUp .= "```json\n" . json_encode($sqlResult['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . "\n```";
                    } else {
                        $followUp .= "*No matching records found.*";
                    }
                }
                
                return [
                    'reply' => $followUp,
                    'type'  => 'data',
                    'query' => $sqlQuery,
                    'raw_data' => $sqlResult['data']
                ];
            } else {
                return [
                    'reply' => "I tried to look that up, but encountered an issue: " . $sqlResult['error'],
                    'type'  => 'error'
                ];
            }
        }
        
        // Clean up any remaining tags from the response
        $cleanResponse = preg_replace('/\\\\?\[(?:SQL_QUERY|\/SQL_QUERY|DB|\/DB)\\\\?\].*?\\\\?\[\\\\?\/(?:SQL_QUERY|DB)\\\\?\]/s', '', $aiResponse);
        $cleanResponse = preg_replace('/\\\\?\[(?:SQL_QUERY|\/SQL_QUERY|DB|\/DB)\\\\?\]/s', '', $cleanResponse);
        
        return [
            'reply' => trim($cleanResponse),
            'type'  => 'text'
        ];
    }

    /**
     * Get the active provider config.
     */
    public function getProviderConfig() {
        return $this->config[$this->provider] ?? [];
    }

    /**
     * Check if the active provider is configured.
     */
    public function isConfigured() {
        $pc = $this->getProviderConfig();
        $key = $pc['api_key'] ?? '';
        return !empty($key) 
            && $key !== 'YOUR_GROQ_API_KEY_HERE' 
            && $key !== 'YOUR_GEMINI_API_KEY_HERE'
            && $key !== 'YOUR_OPENROUTER_API_KEY_HERE';
    }

    // ============================================================
    //  SYSTEM PROMPT (shared across all providers)
    // ============================================================

    private function buildSystemPrompt() {
        $kb = json_encode($this->knowledgeBase, JSON_PRETTY_PRINT);
        
        return <<<PROMPT
You are "Shri", the intelligent AI assistant for the Srishringarr admin panel. You help the admin/client understand their e-commerce platform, find real-time data, and navigate the admin panel.

## YOUR KNOWLEDGE BASE:
{$kb}

## YOUR CAPABILITIES:
1. **Answer questions** about the platform, its features, and how to use the admin panel.
2. **Query live data** from the databases when the user asks about real-time numbers, products, orders, etc.
3. **Explain business logic** like pricing formulas, rental calculations, commission, etc.
4. **Guide navigation** - tell users which page to go to and how to use features.
5. **Suggest product names and descriptions** when an image is provided. Keep recommendations premium and SEO-friendly.

## WHEN YOU NEED LIVE DATA:
If the user asks for real-time data (counts, lists, specific products, orders, revenue, etc.), you MUST generate a SQL query.

Format your SQL query EXACTLY like this:
[DB]con[/DB]
[SQL_QUERY]SELECT COUNT(*) as total FROM product[/SQL_QUERY]

Database options:
- `con` = Main product database (product, garment_product, categories, images)
- `con3` = POS database (phppos_items, phppos_rent, order_detail) 
- `woo` = WooCommerce remote database (wpxyz_posts, wpxyz_postmeta)

## SQL RULES (CRITICAL):
- ONLY generate SELECT queries. NEVER generate INSERT, UPDATE, DELETE, DROP, ALTER, or TRUNCATE.
- Always add LIMIT clause (max 50 rows for lists).
- Use proper table and column names from the knowledge base.
- For product counts, remember there are TWO tables: `product` (jewellery) and `garment_product` (garments).
- For specific product lookups by code/SKU, use `LIKE '%[code]%'` instead of exact `=` to handle potential hidden whitespace or trailing spaces.
- For POS data, the SKU is stored in the `name` column of `phppos_items`.
- Commission amounts in order_detail may have commas - use REPLACE(commission_amt, ',', '') for calculations.

## RESPONSE STYLE:
- Be friendly, professional, and concise.
- Use emojis sparingly for a modern feel.
- Format numbers nicely (e.g., ₹1,25,000 instead of 125000).
- When listing items, use clean formatting.
- When guiding users, provide the exact URL path.
- If you're unsure, say so rather than making things up.
- Answer in the same language the user asks in (Hindi/English).

## IMPORTANT:
- You represent the Srishringarr brand. Be helpful and knowledgeable.
- If someone asks about changing data, explain the process but NEVER generate write queries.
- For write operations, guide them to the correct admin panel page instead.
PROMPT;
    }

    // ============================================================
    //  AI CALL ROUTER (routes to correct provider)
    // ============================================================

    private function callAI($systemPrompt, $userMessage, $history = [], $imageBase64 = null) {
        if ($this->provider === 'gemini') {
            return $this->callGemini($systemPrompt, $userMessage, $history, $imageBase64);
        }
        // Groq and OpenRouter both use OpenAI-compatible format
        return $this->callOpenAICompatible($systemPrompt, $userMessage, $history, $imageBase64);
    }

    private function callAIWithResults($systemPrompt, $userMessage, $sqlQuery, $data, $history = []) {
        // Limit the size of data sent to the AI to prevent exceeding Groq TPM / context limits
        $maxRows = 10;
        $limitedData = $data;
        $hasMore = false;
        if (is_array($data) && count($data) > $maxRows) {
            $limitedData = array_slice($data, 0, $maxRows);
            $hasMore = true;
        }

        $dataJson = json_encode($limitedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        
        // Build an augmented history that includes the SQL results
        $augmentedHistory = $history;
        $augmentedHistory[] = ['role' => 'user', 'text' => $userMessage];
        $augmentedHistory[] = ['role' => 'model', 'text' => "I executed this query: `{$sqlQuery}` and got these results:"];
        
        $followUpMessage = "Here are the database results (showing " . ($hasMore ? "first {$maxRows}" : "all " . count($data)) . " records):\n\n```json\n{$dataJson}\n```\n\n";
        if ($hasMore) {
            $followUpMessage .= "Note: There are more records in the database, but please only summarize the first {$maxRows} and mention that additional records exist.\n\n";
        }

        // Automatically scan the results for any local product image paths
        $imageBase64 = null;
        $imagePath = $this->findImagePathInResults($data);
        if ($imagePath) {
            $imageBase64 = $this->getBase64ImageFromFilePath($imagePath);
        }

        if ($imageBase64) {
            $followUpMessage .= "I have also attached the matching product image file from the server (`{$imagePath}`). Please analyze both the visual attributes of the product image (style, design, colors) and the database records to provide an accurate response (e.g. suggesting titles/descriptions or answering questions about it). Format the output beautifully.";
        } else {
            $followUpMessage .= "Remember: Format the response nicely for the user. Do NOT include any SQL_QUERY tags. Just give a direct, helpful answer based on this data.";
        }

        if ($this->provider === 'gemini') {
            return $this->callGemini($systemPrompt, $followUpMessage, $augmentedHistory, $imageBase64);
        }
        return $this->callOpenAICompatible($systemPrompt, $followUpMessage, $augmentedHistory, $imageBase64);
    }

    // ============================================================
    //  OPENAI-COMPATIBLE PROVIDER (Groq, OpenRouter, etc.)
    // ============================================================

    private function callOpenAICompatible($systemPrompt, $userMessage, $history = [], $imageBase64 = null) {
        $pc = $this->getProviderConfig();
        $apiKey = $pc['api_key'];
        $model = $pc['model'];
        $endpoint = $pc['endpoint'];

        // Build messages array
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        foreach ($history as $turn) {
            $role = ($turn['role'] === 'model') ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $turn['text']];
        }

        if ($imageBase64) {
            // Override model to Groq's vision model if using Groq and the model isn't already a vision model
            if ($this->provider === 'groq' && strpos($model, 'vision') === false) {
                $model = 'llama-3.2-11b-vision-preview';
            }
            
            $content = [
                [
                    'type' => 'text',
                    'text' => $userMessage
                ],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => strpos($imageBase64, 'data:') === 0 ? $imageBase64 : "data:image/jpeg;base64," . $imageBase64
                    ]
                ]
            ];
            $messages[] = ['role' => 'user', 'content' => $content];
        } else {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $this->config['temperature'],
            'max_tokens' => 2048,
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ];

        // OpenRouter requires extra headers
        if ($this->provider === 'openrouter') {
            $headers[] = 'HTTP-Referer: http://localhost';
            $headers[] = 'X-Title: Srishringarr Admin Chatbot';
        }

        $response = $this->makeHttpRequest($endpoint, $payload, $headers);

        if (!$response) return null;

        $decoded = json_decode($response, true);
        
        if (isset($decoded['choices'][0]['message']['content'])) {
            return $decoded['choices'][0]['message']['content'];
        }

        if (isset($decoded['error'])) {
            error_log("Chatbot API error: " . json_encode($decoded['error']));
        }

        return null;
    }

    // ============================================================
    //  GEMINI PROVIDER
    // ============================================================

    private function callGemini($systemPrompt, $userMessage, $history = [], $imageBase64 = null) {
        $pc = $this->getProviderConfig();
        $apiKey = $pc['api_key'];
        $model = $pc['model'];
        $endpoint = $pc['endpoint'] . $model . ':generateContent';

        $contents = [];
        
        foreach ($history as $turn) {
            $role = ($turn['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $turn['text']]]
            ];
        }

        $parts = [];
        if ($imageBase64) {
            $mimeType = 'image/jpeg';
            $data = $imageBase64;
            // Parse data:image/png;base64,blah
            if (preg_match('/^data:(image\/[a-zA-Z+.-]+);base64,(.*)$/', $imageBase64, $matches)) {
                $mimeType = $matches[1];
                $data = $matches[2];
            } else {
                // If it's a raw base64 string without data: prefix
                $data = preg_replace('/^data:image\/[a-z]+;base64,/', '', $imageBase64);
            }
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $mimeType,
                    'data' => $data
                ]
            ];
        }
        $parts[] = ['text' => $userMessage];

        $contents[] = [
            'role' => 'user',
            'parts' => $parts
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->config['temperature'],
                'maxOutputTokens' => 2048,
            ]
        ];

        $headers = [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ];

        $response = $this->makeHttpRequest($endpoint, $payload, $headers);

        if (!$response) return null;

        $decoded = json_decode($response, true);
        
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return $decoded['candidates'][0]['content']['parts'][0]['text'];
        }

        return null;
    }

    // ============================================================
    //  HTTP CLIENT (shared, with retry for rate limits)
    // ============================================================

    private function makeHttpRequest($endpoint, $payload, $headers = []) {
        $maxRetries = 2;
        $retryDelay = 3;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            if ($curlError) {
                error_log("Chatbot cURL error: " . $curlError);
                return null;
            }

            // Retry on rate limit (429)
            if ($httpCode === 429 && $attempt < $maxRetries) {
                error_log("Chatbot: Rate limited (429), retrying in {$retryDelay}s");
                sleep($retryDelay);
                $retryDelay *= 2;
                continue;
            }

            if ($httpCode !== 200) {
                error_log("Chatbot HTTP {$httpCode}: " . $response);
                return null;
            }

            return $response;
        }

        return null;
    }

    // ============================================================
    //  SAFE SQL EXECUTOR
    // ============================================================

    private function executeSafeQuery($sql, $dbType = 'con') {
        // ========== SAFETY LAYER 1: Whitelist only SELECT ==========
        $trimmedSql = trim($sql);
        if (stripos($trimmedSql, 'SELECT') !== 0) {
            return ['success' => false, 'error' => 'Only SELECT queries are allowed.', 'data' => null];
        }

        // ========== SAFETY LAYER 2: Blacklist dangerous keywords ==========
        $dangerous = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'CREATE', 'REPLACE INTO', 'GRANT', 'REVOKE', 'EXEC', 'EXECUTE', 'CALL', 'LOAD', 'INTO OUTFILE', 'INTO DUMPFILE'];
        foreach ($dangerous as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $trimmedSql)) {
                return ['success' => false, 'error' => "Forbidden keyword detected: {$keyword}", 'data' => null];
            }
        }

        // ========== SAFETY LAYER 3: Enforce LIMIT ==========
        $maxRows = $this->config['max_sql_rows'];
        if (stripos($trimmedSql, 'LIMIT') === false) {
            $trimmedSql = rtrim($trimmedSql, ';') . " LIMIT {$maxRows}";
        }

        // ========== Get database connection ==========
        $connection = null;
        switch ($dbType) {
            case 'con':
                $connection = $this->db;
                break;
            case 'con3':
                $connection = $this->db3;
                break;
            case 'woo':
                $connection = \Core\Database::getConnection('woo');
                break;
            default:
                $connection = $this->db;
        }

        if (!$connection) {
            return ['success' => false, 'error' => "Database connection '{$dbType}' is not available.", 'data' => null];
        }

        // ========== Execute ==========
        $result = mysqli_query($connection, $trimmedSql);
        
        if (!$result) {
            $error = mysqli_error($connection);
            error_log("Chatbot SQL error: {$error} | Query: {$trimmedSql}");
            return ['success' => false, 'error' => "Query failed: {$error}", 'data' => null];
        }

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);

        return ['success' => true, 'error' => null, 'data' => $rows];
    }

    /**
     * Recursively find an image file path inside the database response arrays.
     */
    private function findImagePathInResults($data) {
        if (!is_array($data)) return null;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $res = $this->findImagePathInResults($value);
                if ($res) return $res;
            } elseif (is_string($value)) {
                $cleanVal = trim($value);
                if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $cleanVal)) {
                    if (stripos($cleanVal, 'http') === false) {
                        return $cleanVal;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Locate an image file on the server disk and convert it to a Base64 data URL.
     */
    private function getBase64ImageFromFilePath($imagePath) {
        $cleanPath = ltrim($imagePath, '/');
        
        $basePaths = [
            __DIR__ . '/../../yn/uploads/',
            __DIR__ . '/../../uploads/',
            __DIR__ . '/../yn/uploads/',
            __DIR__ . '/../uploads/'
        ];
        
        foreach ($basePaths as $base) {
            $fullPath = $base . $cleanPath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                $content = @file_get_contents($fullPath);
                if ($content !== false) {
                    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $mime = 'image/jpeg';
                    if (in_array(strtolower($ext), ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
                        $mime = 'image/' . (strtolower($ext) === 'jpg' ? 'jpeg' : strtolower($ext));
                    }
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
            
            $strippedPath = preg_replace('/^(yn\/uploads\/|uploads\/)/i', '', $cleanPath);
            $fullPath = $base . $strippedPath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                $content = @file_get_contents($fullPath);
                if ($content !== false) {
                    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $mime = 'image/jpeg';
                    if (in_array(strtolower($ext), ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
                        $mime = 'image/' . (strtolower($ext) === 'jpg' ? 'jpeg' : strtolower($ext));
                    }
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }
        return null;
    }
}
