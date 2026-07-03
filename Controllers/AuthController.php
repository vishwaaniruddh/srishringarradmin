<?php
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class AuthController extends Controller {

    /**
     * Show the login page.
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (Auth::isLoggedIn()) {
            $this->redirect('index.php');
            return;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } else {
                $db = Database::getConnection('con');
                if (!$db) {
                    $error = 'Database connection failed. Please try again.';
                } else {
                    $stmt = $db->prepare("SELECT id, username, password, email, status FROM admin_users WHERE username = ? LIMIT 1");
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();

                    if (!$user) {
                        $error = 'Invalid username or password.';
                    } elseif ($user['status'] !== 'active') {
                        $error = 'Your account has been deactivated. Contact support.';
                    } elseif (!password_verify($password, $user['password'])) {
                        $error = 'Invalid username or password.';
                    } else {
                        // Success — update last_login timestamp
                        $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                        $updateStmt->bind_param('i', $user['id']);
                        $updateStmt->execute();
                        $updateStmt->close();

                        // Set session
                        Auth::login($user);

                        // Redirect to dashboard
                        $this->redirect('index.php');
                        return;
                    }
                }
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    /**
     * Log the user out.
     */
    public function logout() {
        Auth::logout();
        $this->redirect('index.php?controller=auth&action=login');
    }
}
