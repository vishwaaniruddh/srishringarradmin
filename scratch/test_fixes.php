<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Models\ProductModel;
use Core\ProductSyncService;

$con = Database::getConnection('con');

echo "=== Testing Improved isCategoryEnabled for T1087 ===\n";

$model = new ProductModel();
$res = mysqli_query($con, "SELECT product_id FROM product WHERE product_code = 'T1087'");
$row = mysqli_fetch_assoc($res);
$fullProd = $model->getProductById($row['product_id'], 'jewellery');

echo "Product Details:\n";
print_r($fullProd);

$settings = ProductSyncService::getSyncSettings();
echo "\nEnabled categories in settings:\n";
print_r($settings['enabled_categories']);

function testIsCategoryEnabled($productType, $parentProduct, $enabledCategories, $con) {
    if (empty($enabledCategories)) return false;

    if ($productType === 'garments' || $productType === 'garment') {
        $catIds = [];
        if (!empty($parentProduct['category'])) $catIds[] = (int)$parentProduct['category'];
        if (!empty($parentProduct['sub_category'])) $catIds[] = (int)$parentProduct['sub_category'];

        // Also check product_categories
        $pid = (int)($parentProduct['id'] ?? 0);
        if ($pid > 0) {
            $pcRes = mysqli_query($con, "SELECT legacy_category_id, legacy_subcategory_id FROM product_categories WHERE product_id = $pid AND product_type = 'garments'");
            while ($pcRow = mysqli_fetch_assoc($pcRes)) {
                if (!empty($pcRow['legacy_category_id'])) $catIds[] = (int)$pcRow['legacy_category_id'];
                if (!empty($pcRow['legacy_subcategory_id'])) $catIds[] = (int)$pcRow['legacy_subcategory_id'];
            }
        }
        $catIds = array_unique(array_filter($catIds));

        foreach ($catIds as $cId) {
            if (in_array("garment:$cId", $enabledCategories)) return true;
            // Check if $cId is a subcategory in garment_subcat
            $gRes = mysqli_query($con, "SELECT gmain_id FROM garment_subcat WHERE sub_id = $cId LIMIT 1");
            if ($gRes && $gRow = mysqli_fetch_assoc($gRes)) {
                $gMain = (int)$gRow['gmain_id'];
                if (in_array("garment:$gMain", $enabledCategories)) return true;
            }
        }
        return false;
    } else { // Jewellery
        $parentCatIds = [];
        $subCatIds = [];

        $pCat = (int)($parentProduct['category'] ?? 0);
        $sCat = (int)($parentProduct['sub_category'] ?? 0);

        if ($pCat > 0) $parentCatIds[] = $pCat;
        if ($sCat > 0) $subCatIds[] = $sCat;

        // Check product_categories table
        $pid = (int)($parentProduct['id'] ?? 0);
        if ($pid > 0) {
            $pcRes = mysqli_query($con, "SELECT legacy_category_id, legacy_subcategory_id FROM product_categories WHERE product_id = $pid AND product_type = 'jewellery'");
            while ($pcRow = mysqli_fetch_assoc($pcRes)) {
                if (!empty($pcRow['legacy_category_id'])) $parentCatIds[] = (int)$pcRow['legacy_category_id'];
                if (!empty($pcRow['legacy_subcategory_id'])) $subCatIds[] = (int)$pcRow['legacy_subcategory_id'];
            }
        }

        // Normalize: if a parentCatId is actually in subcat1, move it to subCatIds
        foreach ($parentCatIds as $idx => $pId) {
            $checkSub1 = mysqli_query($con, "SELECT subcat_id, maincat_id FROM subcat1 WHERE subcat_id = $pId LIMIT 1");
            if ($checkSub1 && $subRow = mysqli_fetch_assoc($checkSub1)) {
                $subCatIds[] = $pId;
                if (!empty($subRow['maincat_id'])) {
                    $parentCatIds[] = (int)$subRow['maincat_id'];
                }
            }
        }

        // For any subCatId, find its maincat_id in subcat1
        foreach ($subCatIds as $sId) {
            $checkSub1 = mysqli_query($con, "SELECT maincat_id FROM subcat1 WHERE subcat_id = $sId LIMIT 1");
            if ($checkSub1 && $subRow = mysqli_fetch_assoc($checkSub1)) {
                if (!empty($subRow['maincat_id'])) {
                    $parentCatIds[] = (int)$subRow['maincat_id'];
                }
            }
        }

        $parentCatIds = array_unique(array_filter($parentCatIds));
        $subCatIds = array_unique(array_filter($subCatIds));

        // Check if explicit child is enabled
        foreach ($subCatIds as $sId) {
            if (in_array("jewel_child:$sId", $enabledCategories)) return true;
        }

        // Check if parent is enabled
        foreach ($parentCatIds as $pId) {
            if (in_array("jewel_parent:$pId", $enabledCategories)) return true;
        }

        return false;
    }
}

$testRes = testIsCategoryEnabled('jewellery', $fullProd, $settings['enabled_categories'], $con);
echo "\nTest Result for T1087: ";
var_dump($testRes);
