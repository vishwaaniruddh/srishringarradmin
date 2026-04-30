<?php
require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Database.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Core/Model.php';
require 'c:/xampp/htdocs/sri/admin/new_admin/Models/ProductModel.php';

$model = new \Models\ProductModel();

$query = "(SELECT product_id as id, product_code as code, 'jewellery' as type FROM product)
          UNION ALL
          (SELECT gproduct_id as id, gproduct_code as code, 'garments' as type FROM garment_product)
          ORDER BY id DESC";

$result = $model->query($model->getDbConnection(), $query);

$output = fopen('test_export.csv', 'w');
fputcsv($output, ['sku', 'name', 'description', 'type', 'category_id', 'subcat_id', 's_price', 'rental_price', 'deposit', 'images']);

$i = 0;
while ($p = mysqli_fetch_assoc($result)) {
    $fullProduct = $model->getProductById($p['id'], $p['type']);
    if (!$fullProduct) continue;

    $images = $model->getProductImages($p['id'], $p['type']);
    $imageUrls = [];
    if ($images) {
        foreach ($images as $img) {
            $imageUrls[] = "https://srishringarr.com/yn/uploads" . $img['img_name'];
        }
    }

    fputcsv($output, [
        $fullProduct['code'] ?? $p['code'] ?? '',
        $fullProduct['name'] ?? '',
        $fullProduct['description'] ?? '',
        $p['type'] ?? '',
        $fullProduct['category'] ?? '',
        $fullProduct['sub_category'] ?? '',
        $fullProduct['s_price'] ?? 0,
        $fullProduct['rental_price'] ?? 0,
        $fullProduct['deposit'] ?? 0,
        implode(',', $imageUrls)
    ]);
    $i++;
    if ($i % 500 == 0) {
        echo "Exported $i...\n";
    }
}
fclose($output);
echo "Done! Total: $i\n";
