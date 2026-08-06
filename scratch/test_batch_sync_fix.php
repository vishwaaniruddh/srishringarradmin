<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;
use Core\Database;

echo "=== Testing Batch Sync Fix for SKUs with name='1' ===\n";

$con = Database::getConnection('con');
if (!$con) {
    die("Parent DB connection failed\n");
}

$problemSkus = ['set500on', 'set449', 'set423a', 'set310', 'set140', 'k2056', 'k925'];

foreach ($problemSkus as $sku) {
    $res = mysqli_query($con, "SELECT product_id FROM product WHERE product_code = '$sku' LIMIT 1");
    if ($r = mysqli_fetch_assoc($res)) {
        $pid = (int)$r['product_id'];
        echo "Syncing SKU '$sku' (ID #$pid)... ";
        $syncRes = ProductSyncService::syncProduct($pid, 'jewellery', 'manual');
        if ($syncRes['success']) {
            echo "SUCCESS: {$syncRes['message']}\n";
        } else {
            echo "FAILED: {$syncRes['message']}\n";
        }
    } else {
        echo "SKU '$sku' not found in parent DB\n";
    }
}
