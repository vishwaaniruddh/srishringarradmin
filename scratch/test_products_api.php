<?php
$_GET = [
    'category' => 'garment:10',
    'type' => 'garments',
    'page' => '1',
    'min_price' => '0',
    'max_price' => '500000',
    'sort' => 'sku_desc'
];

require __DIR__ . '/../../API/v1/products.php';
