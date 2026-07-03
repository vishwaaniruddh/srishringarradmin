<?php
namespace Core;

class Auth {
    /**
     * Start session if not already started.
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is logged in.
     */
    public static function isLoggedIn(): bool {
        self::startSession();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Get the logged-in admin user data.
     */
    public static function user(): ?array {
        self::startSession();
        if (!self::isLoggedIn()) return null;
        return [
            'id'       => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null,
            'email'    => $_SESSION['admin_email'] ?? null,
        ];
    }

    /**
     * Set session after successful login.
     */
    public static function login(array $user): void {
        self::startSession();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']        = $user['id'];
        $_SESSION['admin_username']  = $user['username'];
        $_SESSION['admin_email']     = $user['email'];
        $_SESSION['admin_login_at']  = time();

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
    }

    /**
     * Destroy session and log out.
     */
    public static function logout(): void {
        self::startSession();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Enforce authentication — redirect to login if not authenticated.
     * Call this at the top of any protected route.
     */
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
}
