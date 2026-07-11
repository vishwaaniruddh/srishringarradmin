<?php
// Set up CLI environment to mimic Web Server request
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/new_admin/index.php?controller=product&action=bulkUpdate';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI Verification Tool';

$_GET = [
    'controller' => 'product',
    'action' => 'bulkUpdate'
];

$_POST = [
    'skus' => 'TESTSKU1, TESTSKU2',
    'password' => 'secret_admin_pass_123',
    'availability' => 'both'
];

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/../' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Core\ActivityTracker;
use Models\ProductModel;

echo "=== Starting Automated Verification ===\n\n";

// Ensure session starts
\Core\Auth::startSession();
// Mock login session
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 999;
$_SESSION['admin_username'] = 'verifier_admin';
$_SESSION['admin_email'] = 'verifier@example.com';

echo "1. Testing ActivityTracker::log()...\n";
ActivityTracker::log();
echo "   ✓ ActivityTracker::log() finished execution.\n\n";

echo "2. Verifying database entry in admin_activity_logs...\n";
$db = Database::getConnection('con');
$res = mysqli_query($db, "SELECT * FROM admin_activity_logs ORDER BY id DESC LIMIT 1");
if ($res && $row = mysqli_fetch_assoc($res)) {
    echo "   Found log entry ID: " . $row['id'] . "\n";
    echo "   Admin Username: " . $row['admin_username'] . " (Expected: verifier_admin)\n";
    echo "   Controller: " . $row['controller'] . " (Expected: product)\n";
    echo "   Action: " . $row['action'] . " (Expected: bulkUpdate)\n";
    echo "   Request Method: " . $row['request_method'] . " (Expected: POST)\n";
    
    // Check password masking
    $postParams = json_decode($row['post_params'], true);
    if (isset($postParams['password'])) {
        if ($postParams['password'] === '********') {
            echo "   ✓ Success: Password field is masked!\n";
        } else {
            echo "   ✗ Failure: Password field is NOT masked: " . $postParams['password'] . "\n";
        }
    } else {
        echo "   ✗ Failure: Password field not found in logged post params.\n";
    }
} else {
    echo "   ✗ Failure: No log entry found in database table.\n";
}
echo "\n";

echo "3. Verifying log file updates...\n";
$logFile = __DIR__ . '/../Logs/activity.log';
if (file_exists($logFile)) {
    echo "   ✓ Log file exists.\n";
    $lines = file($logFile);
    $lastLine = end($lines);
    echo "   Last log line: " . trim($lastLine) . "\n";
    if (strpos($lastLine, 'secret_admin_pass_123') === false && strpos($lastLine, '********') !== false) {
        echo "   ✓ Success: Password is also masked in the log file!\n";
    } else {
        echo "   ✗ Failure: Password leaked or not found in log file.\n";
    }
} else {
    echo "   ✗ Failure: Log file does not exist.\n";
}
echo "\n";

echo "4. Testing ProductModel::getProducts Sorting...\n";
$productModel = new ProductModel();

echo "   a. Fetching name ASC...\n";
$productsAsc = $productModel->getProducts([
    'limit' => 3,
    'page' => 1,
    'sort_by' => 'name',
    'sort_order' => 'asc',
    'skip_details' => true
]);

foreach ($productsAsc as $p) {
    echo "      - Code: " . $p['code'] . " | Name: " . $p['name'] . "\n";
}

echo "   b. Fetching name DESC...\n";
$productsDesc = $productModel->getProducts([
    'limit' => 3,
    'page' => 1,
    'sort_by' => 'name',
    'sort_order' => 'desc',
    'skip_details' => true
]);

foreach ($productsDesc as $p) {
    echo "      - Code: " . $p['code'] . " | Name: " . $p['name'] . "\n";
}

if (!empty($productsAsc) && !empty($productsDesc)) {
    if ($productsAsc[0]['name'] !== $productsDesc[0]['name']) {
        echo "   ✓ Success: Product ordering changes correctly based on sort settings!\n";
    } else {
        echo "   ⚠ Warning: First product is identical in both lists. This might happen if you have very few products or they have identical names.\n";
    }
} else {
    echo "   ✗ Failure: Failed to retrieve products from model.\n";
}

echo "\n=== Verification Complete ===\n";
