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

        $response = $chatbot->processMessage($userMessage, $history);

        $this->json([
            'success' => true,
            'reply'   => $response['reply'],
            'type'    => $response['type'],
        ]);
    }

    /**
     * GET endpoint: Health check / config status.
     * URL: index.php?controller=chatbot&action=status
     */
    public function status() {
        $config = include(__DIR__ . '/../Config/chatbot_config.php');
        $provider = $config['provider'] ?? 'groq';
        $providerConfig = $config[$provider] ?? [];
        
        $chatbot = new ChatbotModel();
        $isConfigured = $chatbot->isConfigured();

        $this->json([
            'configured' => $isConfigured,
            'provider' => $provider,
            'model' => $providerConfig['model'] ?? 'unknown',
            'message' => $isConfigured 
                ? "Chatbot is ready! (Provider: {$provider})" 
                : "Please set your API key for '{$provider}' in Config/chatbot_config.php"
        ]);
    }
}
