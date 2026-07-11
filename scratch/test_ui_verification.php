<?php
// Set up CLI environment to mimic Web Server request
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/new_admin/index.php?controller=report&action=activityLogs';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI UI Verification Tool';

$_GET = [
    'controller' => 'report',
    'action' => 'activityLogs'
];

// Start session and mock login
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 999;
$_SESSION['admin_username'] = 'verifier_admin';
$_SESSION['admin_email'] = 'verifier@example.com';

echo "=== Starting UI Verification ===\n\n";

// Capture output of the index.php execution
ob_start();
include __DIR__ . '/../index.php';
$html = ob_get_clean();

// Check for key elements in the output HTML
$checks = [
    'Title Tag' => '<title>System Activity Logs - Srishringarr</title>',
    'Header Label' => 'System Activity Logs',
    'Logs Table' => '<table class="w-full text-left border-collapse">',
    'Search Form' => 'name="action" value="activityLogs"',
    'Inspector Modal' => 'id="inspectorModal"',
    'Inspection Action' => 'inspectPayload('
];

$passed = true;
foreach ($checks as $name => $snippet) {
    if (strpos($html, $snippet) !== false) {
        echo "   ✓ [PASS] Found $name: \"$snippet\"\n";
    } else {
        echo "   ✗ [FAIL] Missing $name: \"$snippet\"\n";
        $passed = false;
    }
}

if ($passed) {
    echo "\n✓ SUCCESS: UI renders activity logs page correctly!\n";
} else {
    echo "\n✗ FAILURE: Some UI elements are missing.\n";
    exit(1);
}
