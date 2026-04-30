<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Set Error and Exception Handlers
set_exception_handler(['Core\ErrorHandler', 'handleException']);
set_error_handler(['Core\ErrorHandler', 'handleError']);

// Front Controller
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) : 'Dashboard';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

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
        // We'll create this next
        require_once 'Controllers/DashboardController.php';
        $controller = new \Controllers\DashboardController();
        $controller->index();
    } else {
        die("Controller $controllerClass not found");
    }
}
