<?php
/**
 * Chatbot Configuration
 * 
 * Supported providers: 'groq', 'gemini', 'openrouter'
 * 
 * GROQ (RECOMMENDED - Free & Fast):
 *   Get your free API key at: https://console.groq.com/keys
 *   No credit card required. 30 requests/minute free.
 * 
 * GEMINI:
 *   Get your free API key at: https://aistudio.google.com/apikey
 * 
 * OPENROUTER (Many free models):
 *   Get your free API key at: https://openrouter.ai/keys
 */

require_once __DIR__ . '/env.php';

return [
    // ===== ACTIVE PROVIDER =====
    'provider' => 'groq',  // Options: 'groq', 'gemini', 'openrouter'

    // ===== GROQ (Recommended) =====
    'groq' => [
        'api_key' => get_config_secret('GROQ_API_KEY', ''),
        'model' => 'llama-3.3-70b-versatile',
        'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
    ],

    // ===== GEMINI =====
    'gemini' => [
        'api_key' => get_config_secret('GEMINI_API_KEY', ''),
        'model' => 'gemini-2.5-flash',
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    ],

    // ===== OPENROUTER (Alternative) =====
    'openrouter' => [
        'api_key' => get_config_secret('OPENROUTER_API_KEY', ''),
        'model' => 'deepseek/deepseek-chat-v3-0324:free',
        'endpoint' => 'https://openrouter.ai/api/v1/chat/completions',
    ],

    // ===== General Settings =====
    'max_history' => 20,
    'max_sql_rows' => 50,
    'temperature' => 0.3,
];
