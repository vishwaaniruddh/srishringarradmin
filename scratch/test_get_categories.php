<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Database;

$con = Database::getConnection('con');

echo "=== Testing Improved getCategories() ===\n";

function getCategoriesImproved($con) {
    $categories = [];

    // 1. Apparel Categories
    $apparel_qry = "SELECT garment_id, name FROM garments WHERE Main_id=1 OR Main_id=3 ORDER BY name";
    $apparel_res = mysqli_query($con, $apparel_qry);
    $apparel_data = ['children' => [], 'count' => 0];

    while ($row = mysqli_fetch_assoc($apparel_res)) {
        $id = (int)$row['garment_id'];
        $name = ucwords(strtolower($row['name']));

        // Get subcategories in garment_subcat if any
        $subIds = [$id];
        $subQ = mysqli_query($con, "SELECT sub_id FROM garment_subcat WHERE gmain_id = $id");
        while ($subR = mysqli_fetch_assoc($subQ)) {
            $subIds[] = (int)$subR['sub_id'];
        }
        $subListStr = implode(',', array_unique($subIds));

        $count_qry = "SELECT COUNT(DISTINCT gp.gproduct_id) as cnt 
                      FROM garment_product gp
                      LEFT JOIN product_categories pc ON (gp.gproduct_id = pc.product_id AND pc.product_type = 'garments')
                      WHERE gp.garment_id IN ($subListStr) 
                         OR gp.product_for IN ($subListStr)
                         OR pc.legacy_category_id IN ($subListStr)
                         OR pc.legacy_subcategory_id IN ($subListStr)";
        $count_res = mysqli_query($con, $count_qry);
        $count_row = mysqli_fetch_assoc($count_res);
        $count = (int)($count_row['cnt'] ?? 0);

        if ($count > 0) {
            $apparel_data['children']["garment:$id"] = [
                'name' => $name,
                'count' => $count
            ];
            $apparel_data['count'] += $count;
        }
    }
    if ($apparel_data['count'] > 0) {
        $categories['Apparel'] = $apparel_data;
    }

    // 2. Jewellery Categories
    $jewel_qry = "SELECT subcat_id, categories_name FROM jewel_subcat WHERE mcat_id=1 OR mcat_id=3 ORDER BY categories_name";
    $jewel_res = mysqli_query($con, $jewel_qry);
    $jewel_data = ['children' => [], 'count' => 0];

    while ($row = mysqli_fetch_assoc($jewel_res)) {
        $parent_id = (int)$row['subcat_id'];
        $parent_name = ucwords(strtolower($row['categories_name']));

        // Get subcategory IDs in subcat1
        $subcat1Ids = [];
        $sub_qry = "SELECT subcat_id, name FROM subcat1 WHERE maincat_id = $parent_id AND status=1 ORDER BY name";
        $sub_res = mysqli_query($con, $sub_qry);
        $subRows = [];
        while ($sub_row = mysqli_fetch_assoc($sub_res)) {
            $subRows[] = $sub_row;
            $subcat1Ids[] = (int)$sub_row['subcat_id'];
        }

        $allCatIds = array_unique(array_merge([$parent_id], $subcat1Ids));
        $allIdsStr = implode(',', $allCatIds);

        // Count all products belonging to this parent category or any of its subcategories
        $parent_count_qry = "SELECT COUNT(DISTINCT p.product_id) as cnt 
                            FROM product p
                            LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                            WHERE p.categories_id IN ($allIdsStr) 
                               OR p.subcat_id IN ($allIdsStr)
                               OR pc.legacy_category_id IN ($allIdsStr)
                               OR pc.legacy_subcategory_id IN ($allIdsStr)";
        $parent_count_res = mysqli_query($con, $parent_count_qry);
        $parent_count_row = mysqli_fetch_assoc($parent_count_res);
        $parent_count = (int)($parent_count_row['cnt'] ?? 0);

        if ($parent_count > 0) {
            $jewel_data['children']["jewel_parent:$parent_id"] = [
                'name' => $parent_name,
                'count' => $parent_count
            ];

            // Now process subcategories
            foreach ($subRows as $sub_row) {
                $sub_id = (int)$sub_row['subcat_id'];
                $sub_name = ucwords(strtolower($sub_row['name']));

                $sub_count_qry = "SELECT COUNT(DISTINCT p.product_id) as cnt 
                                  FROM product p
                                  LEFT JOIN product_categories pc ON (p.product_id = pc.product_id AND pc.product_type = 'jewellery')
                                  WHERE p.subcat_id = $sub_id 
                                     OR p.categories_id = $sub_id 
                                     OR pc.legacy_subcategory_id = $sub_id";
                $sub_count_res = mysqli_query($con, $sub_count_qry);
                $sub_count_row = mysqli_fetch_assoc($sub_count_res);
                $sub_count = (int)($sub_count_row['cnt'] ?? 0);

                if ($sub_count > 0 && strtolower(trim($sub_name)) !== strtolower(trim($parent_name))) {
                    $jewel_data['children']["jewel_child:$sub_id"] = [
                        'name' => "— $sub_name",
                        'count' => $sub_count
                    ];
                }
            }
            $jewel_data['count'] += $parent_count;
        }
    }
    if ($jewel_data['count'] > 0) {
        $categories['Jewellery'] = $jewel_data;
    }

    return $categories;
}

$cats = getCategoriesImproved($con);
print_r($cats['Jewellery']['children']);
