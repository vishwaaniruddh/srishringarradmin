<?php
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Model.php';
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/ProductSyncService.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Controllers/ProductController.php';

$_GET['id'] = 11433;
$_GET['type'] = 'jewellery';

$controller = new \Controllers\ProductController();
ob_start();
$controller->aiSuggestColors();
$output = ob_get_clean();

echo "Controller Response: $output\n";
$decoded = json_decode($output, true);
if (isset($decoded['success']) && $decoded['success'] === true && !empty($decoded['colors'])) {
    echo ">>> CONTROLLER ACTION SUCCESS! <<<\n";
} else {
    echo ">>> CONTROLLER ACTION FAILED <<<\n";
}
