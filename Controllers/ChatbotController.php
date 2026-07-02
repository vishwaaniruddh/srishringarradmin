<?php
namespace Controllers;

use Core\Controller;
use Models\ChatbotModel;

class ChatbotController extends Controller {
    
    /**
     * POST endpoint: Process a chat message.
     * URL: index.php?controller=chatbot&action=chat
     */
    public function chat() {
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || empty($input['message'])) {
            $this->json(['error' => 'Message is required'], 400);
            return;
        }

        $userMessage = trim($input['message']);
        $history = $input['history'] ?? [];
        $imageBase64 = $input['image'] ?? null;

        // Limit history to prevent token overflow
        $maxHistory = 20;
        if (count($history) > $maxHistory) {
            $history = array_slice($history, -$maxHistory);
        }

        // Process with the chatbot model
        $chatbot = new ChatbotModel();
        
        if (!$chatbot->isConfigured()) {
            $config = include(__DIR__ . '/../Config/chatbot_config.php');
            $provider = $config['provider'] ?? 'groq';
            $this->json([
                'success' => false,
                'error' => "API key not configured for '{$provider}'. Please set it in Config/chatbot_config.php"
            ]);
            return;
        }

        $response = $chatbot->processMessage($userMessage, $history, $imageBase64);

        $this->json([
            'success' => true,
            'reply'   => $response['reply'],
            'type'    => $response['type'],
        ]);
    }

    public function status() {
        $config = include(__DIR__ . '/../Config/chatbot_config.php');
        $provider = $config['provider'] ?? 'groq';
        $providerConfig = $config[$provider] ?? [];
        
        $chatbot = new ChatbotModel();
        $isConfigured = $chatbot->isConfigured();
        
        $pingSuccess = false;
        $pingError = '';
        
        if ($isConfigured) {
            // Perform a live diagnostic ping to the AI provider
            if ($provider === 'gemini') {
                $endpoint = $providerConfig['endpoint'] . $providerConfig['model'] . ':generateContent';
                $payload = [
                    'contents' => [['parts' => [['text' => 'ping']]]],
                    'generationConfig' => ['maxOutputTokens' => 5]
                ];
                $headers = [
                    'Content-Type: application/json',
                    'x-goog-api-key: ' . $providerConfig['api_key']
                ];
            } else {
                $endpoint = $providerConfig['endpoint'];
                $payload = [
                    'model' => $providerConfig['model'],
                    'messages' => [['role' => 'user', 'content' => 'ping']],
                    'max_tokens' => 5
                ];
                $headers = [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $providerConfig['api_key']
                ];
            }
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            
            if ($curlError) {
                $pingError = 'cURL Error: ' . $curlError;
            } elseif ($httpCode !== 200) {
                $decoded = json_decode($response, true);
                $errMsg = $decoded['error']['message'] ?? $response;
                $pingError = "API Error (HTTP {$httpCode}): " . $errMsg;
            } else {
                $pingSuccess = true;
            }
        }

        $this->json([
            'configured' => $isConfigured,
            'provider' => $provider,
            'model' => $providerConfig['model'] ?? 'unknown',
            'ping_success' => $pingSuccess,
            'ping_error' => $pingError,
            'message' => $isConfigured 
                ? ($pingSuccess ? "Chatbot is ready and connected! (Provider: {$provider})" : "Chatbot API configured, but connection failed: {$pingError}")
                : "Please set your API key for '{$provider}' in Config/chatbot_config.php"
        ]);
    }
}
