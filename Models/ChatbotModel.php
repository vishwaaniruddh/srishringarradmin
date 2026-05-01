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
        $this->provider = $this->config['provider'] ?? 'groq';
        $this->knowledgeBase = json_decode(
            file_get_contents(__DIR__ . '/../Config/chatbot_knowledge.json'), 
            true
        );
    }

    /**
     * Main entry: process a user message and return the AI response.
     */
    public function processMessage($userMessage, $conversationHistory = []) {
        // Step 1: Build the system prompt with knowledge base
        $systemPrompt = $this->buildSystemPrompt();
        
        // Step 2: Send to AI to classify intent and possibly get SQL
        $aiResponse = $this->callAI($systemPrompt, $userMessage, $conversationHistory);
        
        if (!$aiResponse) {
            return [
                'reply' => "I'm sorry, I couldn't process your request right now. Please try again.",
                'type'  => 'error'
            ];
        }

        // Step 3: Check if AI wants to execute SQL
        $sqlMatch = [];
        if (preg_match('/\[SQL_QUERY\](.*?)\[\/SQL_QUERY\]/s', $aiResponse, $sqlMatch)) {
            $sqlQuery = trim($sqlMatch[1]);
            $dbType = 'con'; // default
            
            // Detect which database to use
            $dbMatch = [];
            if (preg_match('/\[DB\](.*?)\[\/DB\]/s', $aiResponse, $dbMatch)) {
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
                
                return [
                    'reply' => $followUp ?: "Here are the results, but I couldn't format them nicely.",
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
        $cleanResponse = preg_replace('/\[(?:SQL_QUERY|\/SQL_QUERY|DB|\/DB)\].*?\[\/(?:SQL_QUERY|DB)\]/s', '', $aiResponse);
        $cleanResponse = preg_replace('/\[(SQL_QUERY|\/SQL_QUERY|DB|\/DB)\]/s', '', $cleanResponse);
        
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

    private function callAI($systemPrompt, $userMessage, $history = []) {
        if ($this->provider === 'gemini') {
            return $this->callGemini($systemPrompt, $userMessage, $history);
        }
        // Groq and OpenRouter both use OpenAI-compatible format
        return $this->callOpenAICompatible($systemPrompt, $userMessage, $history);
    }

    private function callAIWithResults($systemPrompt, $userMessage, $sqlQuery, $data, $history = []) {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT);
        
        // Build an augmented history that includes the SQL results
        $augmentedHistory = $history;
        $augmentedHistory[] = ['role' => 'user', 'text' => $userMessage];
        $augmentedHistory[] = ['role' => 'model', 'text' => "I executed this query: `{$sqlQuery}` and got these results:"];
        
        $followUpMessage = "Here are the database results. Please provide a clear, formatted answer to my original question based on this data:\n\n```json\n{$dataJson}\n```\n\nRemember: Format the response nicely for the user. Do NOT include any SQL_QUERY tags. Just give a direct, helpful answer.";

        if ($this->provider === 'gemini') {
            return $this->callGemini($systemPrompt, $followUpMessage, $augmentedHistory);
        }
        return $this->callOpenAICompatible($systemPrompt, $followUpMessage, $augmentedHistory);
    }

    // ============================================================
    //  OPENAI-COMPATIBLE PROVIDER (Groq, OpenRouter, etc.)
    // ============================================================

    private function callOpenAICompatible($systemPrompt, $userMessage, $history = []) {
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

        $messages[] = ['role' => 'user', 'content' => $userMessage];

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

    private function callGemini($systemPrompt, $userMessage, $history = []) {
        $pc = $this->getProviderConfig();
        $apiKey = $pc['api_key'];
        $model = $pc['model'];
        $endpoint = $pc['endpoint'] . $model . ':generateContent?key=' . $apiKey;

        $contents = [];
        
        foreach ($history as $turn) {
            $role = ($turn['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $turn['text']]]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
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

        $response = $this->makeHttpRequest($endpoint, $payload, ['Content-Type: application/json']);

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
            curl_close($ch);

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
}
