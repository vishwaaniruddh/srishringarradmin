<?php
require_once __DIR__ . '/../pos/db_connection.php';
require_once __DIR__ . '/Core/Controller.php';
require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/Models/ProductModel.php';
require_once __DIR__ . '/Controllers/AllInOneWriterController.php';

$c = new Controllers\AllInOneWriterController();

$testCases = [
    ['code' => 'B465ON', 'name' => 'Gold finish Borla with Chain', 'category_id' => 11],
    ['code' => 'BO115AON', 'name' => 'Kundan Borla with Chain Damini', 'category_id' => 11],
    ['code' => 'BO58ON', 'name' => 'Beautifull Simple Elegant Borla Maang Tikka', 'category_id' => 11],
    ['code' => 'BO74ON', 'name' => 'Vilandi Kundan, Pearl And Gold Finish Borla', 'category_id' => 11],
    ['code' => 'BO12', 'name' => 'Bridal Borla with Sari', 'category_id' => 11],
];

foreach ($testCases as $tc) {
    $detected = $c->detectCategoryKey($tc);
    echo "SKU: {$tc['code']} | Name: {$tc['name']} => Detected: {$detected}\n";
}
unlink(__FILE__);
