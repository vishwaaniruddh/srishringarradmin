<?php
namespace Core;

class ActivityTracker {
    /**
     * Track and log admin panel activity.
     */
    public static function log() {
        try {
            // 1. Get request context
            $controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
            $action = isset($_GET['action']) ? $_GET['action'] : 'index';
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            // 2. Parse and sanitize POST/JSON payload parameters
            $postParams = $_POST;
            $rawInput = @file_get_contents('php://input');
            if (!empty($rawInput)) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $postParams = array_merge($postParams, $decoded);
                }
            }

            // Sanitize sensitive values (like passwords/tokens/keys)
            $sensitiveKeys = ['password', 'pwd', 'token', 'key', 'secret', 'gemini_api_key'];
            self::sanitizeParams($postParams, $sensitiveKeys);

            // 3. Get admin info
            $adminUser = Auth::user();
            $adminId = $adminUser['id'] ?? null;
            $adminUsername = $adminUser['username'] ?? null;

            // If not logged in but trying to log in, capture the attempted username
            if (!$adminUsername && strtolower($controller) === 'auth' && strtolower($action) === 'login' && $method === 'POST') {
                $adminUsername = $postParams['username'] ?? 'guest';
            }

            // 4. Log to Database
            $db = Database::getConnection('con');
            if ($db) {
                $stmt = $db->prepare("INSERT INTO admin_activity_logs (admin_id, admin_username, controller, action, request_method, request_uri, get_params, post_params, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $getStr = json_encode($_GET, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $postStr = json_encode($postParams, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $stmt->bind_param(
                        "isssssssss",
                        $adminId,
                        $adminUsername,
                        $controller,
                        $action,
                        $method,
                        $uri,
                        $getStr,
                        $postStr,
                        $ip,
                        $userAgent
                    );
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // 5. Log to File
            $logDir = __DIR__ . '/../Logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/activity.log';
            $timestamp = date('Y-m-d H:i:s');
            $adminLabel = $adminUsername ? "{$adminUsername}" . ($adminId ? " (ID: {$adminId})" : "") : "Guest";
            $getStrCompact = json_encode($_GET);
            $postStrCompact = json_encode($postParams);
            
            $logLine = "[{$timestamp}] [{$ip}] [{$adminLabel}] [{$method}] {$controller}/{$action} - URI: {$uri} - GET: {$getStrCompact} - POST: {$postStrCompact} - UA: {$userAgent}\n";
            @file_put_contents($logFile, $logLine, FILE_APPEND);

        } catch (\Throwable $e) {
            // Prevent log failures from crashing the admin panel
            $timestamp = date('Y-m-d H:i:s');
            $errorLogFile = __DIR__ . '/../Logs/activity_error.log';
            if (!is_dir(dirname($errorLogFile))) {
                @mkdir(dirname($errorLogFile), 0755, true);
            }
            @file_put_contents($errorLogFile, "[{$timestamp}] Logging failed: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
        }
    }

    /**
     * Recursively mask sensitive fields.
     */
    private static function sanitizeParams(&$params, $sensitiveKeys) {
        if (!is_array($params)) return;
        foreach ($params as $key => &$value) {
            if (is_array($value)) {
                self::sanitizeParams($value, $sensitiveKeys);
            } else {
                if (in_array(strtolower($key), $sensitiveKeys)) {
                    $value = '********';
                }
            }
        }
    }
}
