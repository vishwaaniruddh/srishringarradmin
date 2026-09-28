<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Models\ProductModel;
use Core\ProductSyncService;

$id = 12547;
$type = 'jewellery';

$productModel = new ProductModel();
$product = $productModel->getProductById($id, $type);
$images = $productModel->getProductImages($id, $type);
$jewelCategories = $productModel->getJewelCategories();
$garments = $productModel->getGarments();
$allCategoriesTree = $productModel->getAllCategoriesWithSubcategories($type);
$assignedCategories = $productModel->getProductAssignedCategories($id, $type);
$availableColors = $productModel->getAvailableColors();
$isSyncApplicable = ProductSyncService::isCategoryEnabled($type, $product);

echo "Product found: " . ($product ? $product['name'] : 'NO') . "\n";
echo "Images count: " . count($images) . "\n";
echo "Assigned categories: " . json_encode($assignedCategories) . "\n";

// Let's test output buffering render of edit.php
$_GET['id'] = $id;
$_GET['type'] = $type;
ob_start();
try {
    include __DIR__ . '/../Views/products/edit.php';
    $html = ob_get_clean();
    echo "Rendered HTML length: " . strlen($html) . " bytes\n";
    // Check if btnSubmitProduct exists in HTML
    if (strpos($html, 'btnSubmitProduct') !== false) {
        echo "SUCCESS: btnSubmitProduct is present in rendered HTML!\n";
    } else {
        echo "WARNING: btnSubmitProduct is NOT in rendered HTML!\n";
    }
} catch (\Throwable $e) {
    ob_end_clean();
    echo "RENDER ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
