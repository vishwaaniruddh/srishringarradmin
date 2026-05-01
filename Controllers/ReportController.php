<?php
namespace Controllers;

use Core\Controller;
use Core\Database;

class ReportController extends Controller {
    public function sku() {
        $con = Database::getConnection('con');
        $wp_con = Database::getConnection('woo');

        if (!$wp_con) {
            $wp_error = "WordPress Connection Failed. Please check remote database credentials.";
        }

        // 1. Fetch SKUs and Details from Srishringarr (Part A - SS)
        $skus_a = [];
        $details_a = [];
        
        // Fetch Image Counts for SS
        $img_counts_a = [];
        $res_img = mysqli_query($con, "SELECT pro_code, COUNT(*) as total FROM product_images_new GROUP BY pro_code");
        while ($row = mysqli_fetch_assoc($res_img)) {
            $img_counts_a[strtoupper(trim($row['pro_code'] ?? ''))] = $row['total'];
        }

        // Jewelry
        $res_j = mysqli_query($con, "SELECT product_code, product_name, ss_product_name, product_desc FROM product 
                                     WHERE product_code != '' 
                                     AND product_name NOT LIKE '%nath%' 
                                     AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
        while ($row = mysqli_fetch_assoc($res_j)) {
            $sku = strtoupper(trim($row['product_code'] ?? ''));
            $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['product_name'];
            $skus_a[] = $sku;
            $details_a[$sku] = [
                'name' => $name, 
                'cat' => 'Jewelry',
                'desc' => $row['product_desc'],
                'img_count' => $img_counts_a[$sku] ?? 0
            ];
        }

        // Apparel
        $res_app = mysqli_query($con, "SELECT gproduct_code, gproduct_name, ss_product_name, gproduct_desc FROM garment_product 
                                       WHERE gproduct_code != '' 
                                       AND gproduct_name NOT LIKE '%nath%' 
                                       AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
        while ($row = mysqli_fetch_assoc($res_app)) {
            $sku = strtoupper(trim($row['gproduct_code'] ?? ''));
            $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['gproduct_name'];
            $skus_a[] = $sku;
            $details_a[$sku] = [
                'name' => $name, 
                'cat' => 'Apparel',
                'desc' => $row['gproduct_desc'],
                'img_count' => $img_counts_a[$sku] ?? 0
            ];
        }

        $skus_a = array_unique($skus_a);

        // 2. Fetch SKUs and Details from Yosshitaneha (Part B - YN)
        $skus_b = [];
        $details_b = [];
        if ($wp_con) {
            $query_wp = "SELECT pm.meta_value as sku, p.post_title as name, p.post_content as descr,
                         (SELECT meta_value FROM wpxyz_postmeta WHERE post_id = p.ID AND meta_key = '_product_image_gallery' LIMIT 1) as gallery,
                         (SELECT meta_value FROM wpxyz_postmeta WHERE post_id = p.ID AND meta_key = '_thumbnail_id' LIMIT 1) as thumb
                         FROM wpxyz_posts p 
                         JOIN wpxyz_postmeta pm ON p.ID = pm.post_id 
                         WHERE p.post_type IN ('product', 'product_variation') 
                         AND pm.meta_key = '_sku' 
                         AND pm.meta_value != ''";
            $res_wp = mysqli_query($wp_con, $query_wp);
            if ($res_wp) {
                while ($row = mysqli_fetch_assoc($res_wp)) {
                    $sku = strtoupper(trim($row['sku'] ?? ''));
                    $skus_b[] = $sku;
                    
                    // Image count calculation for YN
                    $gallery = trim($row['gallery'] ?? '');
                    $img_count = 0;
                    if (!empty($gallery)) {
                        $img_count = count(explode(',', $gallery));
                    }
                    if (!empty($row['thumb'])) {
                        $img_count += 1;
                    }

                    $details_b[$sku] = [
                        'name' => $row['name'],
                        'desc' => $row['descr'],
                        'img_count' => $img_count
                    ];
                }
            }
            $skus_b = array_unique($skus_b);
        }

        // 3. Comparison Logic
        $only_in_a = array_diff($skus_a, $skus_b);
        $only_in_b = array_diff($skus_b, $skus_a);
        $both_ab = array_intersect($skus_a, $skus_b);

        // Handle CSV Export
        if (isset($_GET['export'])) {
            $type = $_GET['export'];
            $filename = "sku_report_" . $type . "_" . date('Ymd_His') . ".csv";
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            
            $output = fopen('php://output', 'w');
            @fputcsv($output, array('SKU', 'Product Name', 'Source/Status'), ',', '"', '\\');
            
            switch ($type) {
                case 'all_a':
                    foreach ($skus_a as $sku) @fputcsv($output, array($sku, $details_a[$sku]['name'], 'Srishringarr (A)'), ',', '"', '\\');
                    break;
                case 'all_b':
                    foreach ($skus_b as $sku) @fputcsv($output, array($sku, $details_b[$sku]['name'], 'Yosshitaneha (B)'), ',', '"', '\\');
                    break;
                case 'only_a':
                    foreach ($only_in_a as $sku) @fputcsv($output, array($sku, $details_a[$sku]['name'], 'Only in Srishringarr'), ',', '"', '\\');
                    break;
                case 'only_b':
                    foreach ($only_in_b as $sku) @fputcsv($output, array($sku, $details_b[$sku]['name'], 'Only in Yosshitaneha'), ',', '"', '\\');
                    break;
                case 'both':
                    foreach ($both_ab as $sku) @fputcsv($output, array($sku, $details_a[$sku]['name'], 'Matched in Both'), ',', '"', '\\');
                    break;
            }
            fclose($output);
            exit;
        }

        $this->view('reports/sku_report', [
            'skus_a' => $skus_a,
            'skus_b' => $skus_b,
            'details_a' => $details_a,
            'details_b' => $details_b,
            'only_in_a' => $only_in_a,
            'only_in_b' => $only_in_b,
            'both_ab' => $both_ab,
            'wp_error' => $wp_error ?? null
        ]);
    }
}
