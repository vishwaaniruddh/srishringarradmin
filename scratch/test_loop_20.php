<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;
use Core\Database;

$con = Database::getConnection('con');

echo "=== Testing Loop Sync of 20 Products ===\n";
$res = mysqli_query($con, "SELECT product_id, product_code FROM product ORDER BY product_id DESC LIMIT 20");
$successCount = 0;
$failCount = 0;

while ($r = mysqli_fetch_assoc($res)) {
    $pid = (int)$r['product_id'];
    $sku = $r['product_code'];
    $syncRes = ProductSyncService::syncProduct($pid, 'jewellery', 'manual');
    if ($syncRes['success']) {
        $successCount++;
        echo "✓ [$sku] Synced successfully\n";
    } else {
        $failCount++;
        echo "❌ [$sku] Failed: {$syncRes['message']}\n";
    }
}

echo "\nSummary: $successCount Success, $failCount Failed\n";
