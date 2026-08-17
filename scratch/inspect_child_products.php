<?php
require_once __DIR__ . '/../Core/ProductSyncService.php';

try {
    $pdoChild = \Core\ProductSyncService::getChildPdo();
    if ($pdoChild) {
        echo "=== Columns in Child DB 'products' table ===\n";
        $stmt = $pdoChild->query("DESCRIBE products");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Child DB connection null\n";
    }
} catch (\Exception $e) {
    echo "Child DB Error: " . $e->getMessage() . "\n";
}
