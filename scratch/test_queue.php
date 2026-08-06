<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;
use Core\ProductSyncService;

$con = Database::getConnection('con');

$settings = ProductSyncService::getSyncSettings();

echo "=== Testing Filtered Sync Queue ===\n";
echo "sync_all: " . ($settings['sync_all'] ? 'true' : 'false') . "\n";
echo "Enabled count: " . count($settings['enabled_categories'] ?? []) . "\n";

function getFilteredSyncQueueItems($con, $settings) {
    $items = [];
    $syncAll = !empty($settings['sync_all']);
    $enabled = $settings['enabled_categories'] ?? [];

    if ($syncAll || empty($enabled)) {
        // Fetch ALL Jewellery
        $resJ = mysqli_query($con, "SELECT product_id as id, 'jewellery' as type, product_code as code, product_name as name FROM product ORDER BY product_id DESC");
        if ($resJ) {
            while ($r = mysqli_fetch_assoc($resJ)) {
                $r['id'] = (int)$r['id'];
                $r['code'] = trim($r['code'] ?? '');
                $r['name'] = trim($r['name'] ?? '');
                $items[] = $r;
            }
        }
        // Fetch ALL Garments
        $resG = mysqli_query($con, "SELECT gproduct_id as id, 'garments' as type, gproduct_code as code, gproduct_name as name FROM garment_product ORDER BY gproduct_id DESC");
        if ($resG) {
            while ($r = mysqli_fetch_assoc($resG)) {
                $r['id'] = (int)$r['id'];
                $r['code'] = trim($r['code'] ?? '');
                $r['name'] = trim($r['name'] ?? '');
                $items[] = $r;
            }
        }
        return $items;
    }

    // Extract Jewellery and Garment enabled IDs
    $jewelParentIds = [];
    $jewelChildIds = [];
    $garmentIds = [];

    foreach ($enabled as $catStr) {
        if (str_starts_with($catStr, 'jewel_parent:')) {
            $jewelParentIds[] = (int)str_replace('jewel_parent:', '', $catStr);
        } elseif (str_starts_with($catStr, 'jewel_child:')) {
            $jewelChildIds[] = (int)str_replace('jewel_child:', '', $catStr);
        } elseif (str_starts_with($catStr, 'garment:')) {
            $garmentIds[] = (int)str_replace('garment:', '', $catStr);
        }
    }

    // For any jewelParentIds, expand to their subcat1 IDs as well
    if (!empty($jewelParentIds)) {
        $parentStr = implode(',', $jewelParentIds);
        $subRes = mysqli_query($con, "SELECT subcat_id FROM subcat1 WHERE maincat_id IN ($parentStr) AND status = 1");
        if ($subRes) {
            while ($subRow = mysqli_fetch_assoc($subRes)) {
                $jewelChildIds[] = (int)$subRow['subcat_id'];
            }
        }
    }

    // For any garmentIds, expand to their sub_ids in garment_subcat
    if (!empty($garmentIds)) {
        $gMainStr = implode(',', $garmentIds);
        $gSubRes = mysqli_query($con, "SELECT sub_id FROM garment_subcat WHERE gmain_id IN ($gMainStr)");
        if ($gSubRes) {
            while ($gSubRow = mysqli_fetch_assoc($gSubRes)) {
                $garmentIds[] = (int)$gSubRow['sub_id'];
            }
        }
    }

    $allJewelIds = array_unique(array_merge($jewelParentIds, $jewelChildIds));
    $allGarmentIds = array_unique($garmentIds);

    // Fetch Jewellery
    if (!empty($allJewelIds)) {
        $jewelStr = implode(',', $allJewelIds);
        $sqlJ = "SELECT DISTINCT p.product_id as id, 'jewellery' as type, p.product_code as code, p.product_name as name 
                 FROM product p
                 LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                 WHERE p.categories_id IN ($jewelStr)
                    OR p.subcat_id IN ($jewelStr)
                    OR pc.legacy_category_id IN ($jewelStr)
                    OR pc.legacy_subcategory_id IN ($jewelStr)
                 ORDER BY p.product_id DESC";
        $resJ = mysqli_query($con, $sqlJ);
        if ($resJ) {
            while ($r = mysqli_fetch_assoc($resJ)) {
                $r['id'] = (int)$r['id'];
                $r['code'] = trim($r['code'] ?? '');
                $r['name'] = trim($r['name'] ?? '');
                $items[] = $r;
            }
        }
    }

    // Fetch Garments
    if (!empty($allGarmentIds)) {
        $gStr = implode(',', $allGarmentIds);
        $sqlG = "SELECT DISTINCT gp.gproduct_id as id, 'garments' as type, gp.gproduct_code as code, gp.gproduct_name as name 
                 FROM garment_product gp
                 LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                 WHERE gp.garment_id IN ($gStr)
                    OR gp.product_for IN ($gStr)
                    OR pc.legacy_category_id IN ($gStr)
                    OR pc.legacy_subcategory_id IN ($gStr)
                 ORDER BY gp.gproduct_id DESC";
        $resG = mysqli_query($con, $sqlG);
        if ($resG) {
            while ($r = mysqli_fetch_assoc($resG)) {
                $r['id'] = (int)$r['id'];
                $r['code'] = trim($r['code'] ?? '');
                $r['name'] = trim($r['name'] ?? '');
                $items[] = $r;
            }
        }
    }

    return $items;
}

$filteredItems = getFilteredSyncQueueItems($con, $settings);
echo "Filtered items in queue: " . count($filteredItems) . "\n";
if (!empty($filteredItems)) {
    echo "First 5 items:\n";
    print_r(array_slice($filteredItems, 0, 5));
}
