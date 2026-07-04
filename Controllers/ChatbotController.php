<?php
namespace Controllers;

use Core\Controller;
use Models\ChatbotModel;

class ChatbotController extends Controller {
    /**
     * Show chatbot settings page.
     */
    public function settings() {
        $db = \Core\Database::getConnection('con');
        $result = $db->query("SELECT * FROM chatbot_settings LIMIT 1");
        $settings = $result ? $result->fetch_assoc() : null;
        
        $provider = $settings['provider'] ?? 'gemini';
        $groqKey = $settings['groq_key'] ?? '';
        $geminiKey = $settings['gemini_key'] ?? '';
        $openrouterKey = $settings['openrouter_key'] ?? '';
        
        $this->view('chatbot_settings', [
            'provider' => $provider,
            'groqKey' => $groqKey,
            'geminiKey' => $geminiKey,
            'openrouterKey' => $openrouterKey,
            'success' => $_GET['success'] ?? false
        ]);
    }

    /**
     * Save chatbot settings.
     */
    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?controller=chatbot&action=settings');
            return;
        }

        $db = \Core\Database::getConnection('con');
        
        $provider = $_POST['provider'] ?? 'gemini';
        $groqKey = $_POST['groq_key'] ?? '';
        $geminiKey = $_POST['gemini_key'] ?? '';
        $openrouterKey = $_POST['openrouter_key'] ?? '';

        $check = $db->query("SELECT id FROM chatbot_settings LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $row = $check->fetch_assoc();
            $stmt = $db->prepare("UPDATE chatbot_settings SET provider=?, groq_key=?, gemini_key=?, openrouter_key=? WHERE id=?");
            $stmt->bind_param("ssssi", $provider, $groqKey, $geminiKey, $openrouterKey, $row['id']);
            $stmt->execute();
        } else {
            $stmt = $db->prepare("INSERT INTO chatbot_settings (provider, groq_key, gemini_key, openrouter_key) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $provider, $groqKey, $geminiKey, $openrouterKey);
            $stmt->execute();
        }

        $this->redirect('index.php?controller=chatbot&action=settings&success=1');
    }

    /**
     * AJAX endpoint to quickly switch the active provider from the chat widget
     */
    public function updateProvider() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['provider'])) {
            $this->json(['success' => false, 'error' => 'Provider is required'], 400);
            return;
        }

        $provider = $input['provider'];
        $allowed = ['groq', 'gemini', 'openrouter'];
        if (!in_array($provider, $allowed)) {
            $this->json(['success' => false, 'error' => 'Invalid provider'], 400);
            return;
        }

        $db = \Core\Database::getConnection('con');
        $check = $db->query("SELECT id FROM chatbot_settings LIMIT 1");
        
        if ($check && $check->num_rows > 0) {
            $row = $check->fetch_assoc();
            $stmt = $db->prepare("UPDATE chatbot_settings SET provider=? WHERE id=?");
            $stmt->bind_param("si", $provider, $row['id']);
            $stmt->execute();
        } else {
            $stmt = $db->prepare("INSERT INTO chatbot_settings (provider) VALUES (?)");
            $stmt->bind_param("s", $provider);
            $stmt->execute();
        }

        $this->json(['success' => true, 'provider' => $provider]);
    }

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
        $chatbot = new ChatbotModel();
        $isConfigured = $chatbot->isConfigured();
        
        // Get the active dynamic configuration
        // Using Reflection or a public getter since config is private
        // Wait, I can just read it from DB here
        $db = \Core\Database::getConnection('con');
        $result = $db->query("SELECT provider FROM chatbot_settings LIMIT 1");
        $provider = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['provider'] : 'gemini';
        
        $baseConfig = include(__DIR__ . '/../Config/chatbot_config.php');
        $providerConfig = $chatbot->getProviderConfig(); // This has the DB API keys
        
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
