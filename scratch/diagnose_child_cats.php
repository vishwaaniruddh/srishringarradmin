<?php
/**
 * Diagnostic: Inspect child DB category hierarchy to understand the sync problem.
 * Expected: Jewellery → Necklace Sets → American Diamond (3-level)
 * Current:  Jewellery → "Necklace Sets" (2-level, with parent category_name used as subcategory)
 */
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\ProductSyncService;
use Core\Database;

$pdoChild = ProductSyncService::getChildPdo();
$con = Database::getConnection('con');

echo "=== CHILD DB: Categories Table (full) ===\n";
if ($pdoChild) {
    $stmt = $pdoChild->query("SELECT id, name, slug, parent_id, description FROM categories ORDER BY parent_id, id");
    $categories = [];
    while ($row = $stmt->fetch()) {
        $categories[] = $row;
        $parentLabel = $row['parent_id'] ? "parent_id={$row['parent_id']}" : "TOP-LEVEL";
        echo "  ID {$row['id']}: {$row['name']} (slug={$row['slug']}) [$parentLabel]\n";
    }
    
    echo "\n=== CHILD DB: Category Tree Visualization ===\n";
    $tree = [];
    foreach ($categories as $cat) {
        $pid = (int)($cat['parent_id'] ?? 0);
        if ($pid === 0) {
            $tree[$cat['id']] = ['name' => $cat['name'], 'children' => []];
        }
    }
    foreach ($categories as $cat) {
        $pid = (int)($cat['parent_id'] ?? 0);
        if ($pid > 0 && isset($tree[$pid])) {
            $tree[$pid]['children'][$cat['id']] = ['name' => $cat['name'], 'children' => []];
        }
    }
    // 3rd level
    foreach ($categories as $cat) {
        $pid = (int)($cat['parent_id'] ?? 0);
        if ($pid > 0) {
            foreach ($tree as $topId => &$topCat) {
                if (isset($topCat['children'][$pid])) {
                    // Already added as 2nd level child
                } elseif (!isset($tree[$pid])) {
                    // This is a 3rd level category
                    foreach ($topCat['children'] as $childId => &$childCat) {
                        if ($childId === $pid) {
                            $childCat['children'][$cat['id']] = ['name' => $cat['name']];
                        }
                    }
                }
            }
        }
    }
    
    foreach ($tree as $topId => $topCat) {
        echo "  {$topCat['name']} (id=$topId)\n";
        foreach ($topCat['children'] as $childId => $childCat) {
            echo "    ├── {$childCat['name']} (id=$childId)\n";
            if (!empty($childCat['children'])) {
                foreach ($childCat['children'] as $gChildId => $gChildCat) {
                    echo "    │   └── {$gChildCat['name']} (id=$gChildId)\n";
                }
            }
        }
    }

    echo "\n=== CHILD DB: Product → Category Assignment Sample (10 products) ===\n";
    $stmt2 = $pdoChild->query("SELECT p.id, p.sku, p.name, p.category_id, c.name as cat_name, c.parent_id, cp.name as parent_cat_name 
                                FROM products p 
                                LEFT JOIN categories c ON p.category_id = c.id 
                                LEFT JOIN categories cp ON c.parent_id = cp.id 
                                ORDER BY p.id DESC LIMIT 10");
    while ($row = $stmt2->fetch()) {
        $hierarchy = ($row['parent_cat_name'] ? $row['parent_cat_name'] . ' → ' : '') . ($row['cat_name'] ?? 'NULL');
        echo "  Product #{$row['id']} SKU={$row['sku']}: category_id={$row['category_id']} → {$hierarchy}\n";
    }
}

echo "\n=== PARENT DB: Category Hierarchy (jewel_subcat → subcat1) ===\n";
if ($con) {
    $res = mysqli_query($con, "SELECT js.subcat_id, js.categories_name FROM jewel_subcat js WHERE js.mcat_id=1 OR js.mcat_id=3 ORDER BY js.categories_name");
    while ($row = mysqli_fetch_assoc($res)) {
        $parentId = $row['subcat_id'];
        $parentName = $row['categories_name'];
        echo "  {$parentName} (jewel_subcat.subcat_id={$parentId})\n";
        
        $subRes = mysqli_query($con, "SELECT subcat_id, name FROM subcat1 WHERE maincat_id = $parentId AND status = 1 ORDER BY name");
        while ($subRow = mysqli_fetch_assoc($subRes)) {
            echo "    ├── {$subRow['name']} (subcat1.subcat_id={$subRow['subcat_id']})\n";
        }
    }
    
    echo "\n=== PARENT DB: Sample Products with subcategory_name ===\n";
    $res2 = mysqli_query($con, "SELECT p.product_id, p.product_code, p.product_name, p.categories_id, p.subcat_id, 
                                       js.categories_name as main_cat_name, s.name as sub_cat_name
                                FROM product p 
                                LEFT JOIN jewel_subcat js ON p.categories_id = js.subcat_id
                                LEFT JOIN subcat1 s ON p.subcat_id = s.subcat_id 
                                WHERE p.categories_id > 0
                                ORDER BY p.product_id DESC LIMIT 15");
    while ($row = mysqli_fetch_assoc($res2)) {
        echo "  ID={$row['product_id']} SKU={$row['product_code']}: categories_id={$row['categories_id']}({$row['main_cat_name']}) → subcat_id={$row['subcat_id']}({$row['sub_cat_name']})\n";
    }
}

echo "\nDone.\n";
