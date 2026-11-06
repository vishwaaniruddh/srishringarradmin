<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

// Autoloader
require __DIR__ . '/vendor/autoload.php';
spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $classPath . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    // Fallback for Linux case sensitivity (e.g., controllers/ vs Controllers/, models/ vs Models/)
    $parts = explode(DIRECTORY_SEPARATOR, $classPath);
    if (count($parts) >= 2) {
        $parts[0] = strtolower($parts[0]);
        $fallback = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
        if (file_exists($fallback)) {
            require_once $fallback;
            return;
        }
    }
});

// Set Error and Exception Handlers
set_exception_handler(['Core\ErrorHandler', 'handleException']);
set_error_handler(['Core\ErrorHandler', 'handleError']);

// Start session for auth
\Core\Auth::startSession();

// Front Controller
$controllerMap = [
    'photodownloader' => 'Photodownloader',
    'photodownload' => 'Photodownloader',
    'wooproduct' => 'Wooproduct',
    'aianalytics' => 'Aianalytics',
    'aimodels' => 'Aimodels',
    'aiplayground' => 'AiPlayground',
    'categoryimage' => 'CategoryImage',
    'allinonewriter' => 'AllInOneWriter',
    'video_generator' => 'VideoGenerator',
    'videogenerator' => 'VideoGenerator',
    'setting' => 'Setting',
    'settings' => 'Setting',
    'imageoptimizer' => 'ImageOptimizer',
    'optimizer' => 'ImageOptimizer'
];
$rawController = strtolower(trim($_GET['controller'] ?? 'dashboard'));
$controllerName = $controllerMap[$rawController] ?? (isset($_GET['controller']) ? ucfirst($_GET['controller']) : 'Dashboard');
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';


// Controllers that DON'T require authentication
$publicControllers = ['Auth'];

// Enforce authentication for all non-public controllers
if (!in_array($controllerName, $publicControllers)) {
    if (!\Core\Auth::isLoggedIn()) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}

// Track administrative activity
\Core\ActivityTracker::log();

$controllerClass = "Controllers\\" . $controllerName . "Controller";

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        die("Action $actionName not found in $controllerClass");
    }
} else {
    // Default to Dashboard if not found, or show error
    if ($controllerName === 'Dashboard') {
        require_once 'Controllers/DashboardController.php';
        $controller = new \Controllers\DashboardController();
        $controller->index();
    } else {
        die("Controller $controllerClass not found");
    }
}
