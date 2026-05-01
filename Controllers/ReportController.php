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
        $res_j = mysqli_query($con, "SELECT product_id, product_code, product_name, ss_product_name, product_desc FROM product 
                                     WHERE product_code != '' 
                                     AND product_name NOT LIKE '%nath%' 
                                     AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
        while ($row = mysqli_fetch_assoc($res_j)) {
            $sku = strtoupper(trim($row['product_code'] ?? ''));
            $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['product_name'];
            $skus_a[] = $sku;
            $details_a[$sku] = [
                'id' => $row['product_id'],
                'name' => $name, 
                'cat' => 'Jewelry',
                'desc' => $row['product_desc'],
                'img_count' => $img_counts_a[$sku] ?? 0
            ];
        }

        // Apparel
        $res_app = mysqli_query($con, "SELECT gproduct_id, gproduct_code, gproduct_name, ss_product_name, gproduct_desc FROM garment_product 
                                       WHERE gproduct_code != '' 
                                       AND gproduct_name NOT LIKE '%nath%' 
                                       AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
        while ($row = mysqli_fetch_assoc($res_app)) {
            $sku = strtoupper(trim($row['gproduct_code'] ?? ''));
            $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['gproduct_name'];
            $skus_a[] = $sku;
            $details_a[$sku] = [
                'id' => $row['gproduct_id'],
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
            $query_wp = "SELECT p.ID as post_id, pm.meta_value as sku, p.post_title as name, p.post_content as descr,
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
                        'post_id' => $row['post_id'],
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

    public function sync() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $sku = $_POST['sku'] ?? '';
        $type = $_POST['type'] ?? ''; // title, desc, images
        $cat = $_POST['cat'] ?? ''; // Jewelry, Apparel
        $id = $_POST['id'] ?? ''; // Local ID
        $post_id = $_POST['post_id'] ?? ''; // WordPress Post ID

        if (!$sku || !$type || !$cat || !$id || !$post_id) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        $con = Database::getConnection('con');
        $wp_con = Database::getConnection('woo');

        if ($type === 'title' || $type === 'desc') {
            // Fetch remote value
            $field = ($type === 'title') ? 'post_title' : 'post_content';
            $query = "SELECT $field FROM wpxyz_posts WHERE ID = $post_id";
            $res = mysqli_query($wp_con, $query);
            if (!$res) {
                echo json_encode(['success' => false, 'message' => 'Failed to fetch from WordPress']);
                exit;
            }
            $row = mysqli_fetch_assoc($res);
            $value = $row[$field];

            // Update local
            if ($cat === 'Jewelry') {
                $local_field = ($type === 'title') ? 'ss_product_name' : 'product_desc';
                $update = "UPDATE product SET $local_field = '" . mysqli_real_escape_string($con, $value) . "' WHERE product_id = $id";
            } else {
                $local_field = ($type === 'title') ? 'ss_product_name' : 'gproduct_desc';
                $update = "UPDATE garment_product SET $local_field = '" . mysqli_real_escape_string($con, $value) . "' WHERE gproduct_id = $id";
            }

            if (mysqli_query($con, $update)) {
                echo json_encode(['success' => true, 'message' => ucfirst($type) . ' synced successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed: ' . mysqli_error($con)]);
            }
        } elseif ($type === 'images') {
            // 1. Delete existing local images for this SKU
            mysqli_query($con, "DELETE FROM product_images_new WHERE pro_code = '" . mysqli_real_escape_string($con, $sku) . "'");

            // 2. Fetch remote image IDs from WordPress Meta
            $query_meta = "SELECT meta_key, meta_value FROM wpxyz_postmeta WHERE post_id = $post_id AND meta_key IN ('_product_image_gallery', '_thumbnail_id')";
            $res_meta = mysqli_query($wp_con, $query_meta);
            $gallery_ids = [];
            $thumb_id = '';
            while ($row = mysqli_fetch_assoc($res_meta)) {
                if ($row['meta_key'] === '_product_image_gallery') $gallery_ids = explode(',', $row['meta_value']);
                if ($row['meta_key'] === '_thumbnail_id') $thumb_id = $row['meta_value'];
            }

            $all_ids = array_filter(array_unique(array_merge([$thumb_id], $gallery_ids)));
            if (empty($all_ids)) {
                echo json_encode(['success' => true, 'message' => 'No images found on WordPress to sync']);
                exit;
            }

            // 3. Get URLs from WordPress and Download
            $id_list = implode(',', array_map('intval', $all_ids));
            $query_img = "SELECT ID, guid FROM wpxyz_posts WHERE ID IN ($id_list)";
            $res_img = mysqli_query($wp_con, $query_img);
            
            $synced_count = 0;
            $date_path = date('Y') . "/" . date('m') . "/";
            $upload_dir_rel = "yn/uploads/" . $date_path;
            $abs_upload_dir = __DIR__ . "/../../../" . $upload_dir_rel;
            
            if (!file_exists($abs_upload_dir)) mkdir($abs_upload_dir, 0777, true);

            while ($row = mysqli_fetch_assoc($res_img)) {
                $url = $row['guid'];
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (empty($ext)) $ext = 'jpg';
                
                $new_filename = $sku . "_" . $row['ID'] . "." . $ext;
                $db_path = "/" . $date_path . $new_filename;
                $save_path = $abs_upload_dir . $new_filename;

                // Download image content
                $img_data = @file_get_contents($url);
                if ($img_data) {
                    if (file_put_contents($save_path, $img_data)) {
                        // 4. Insert into product_images_new
                        $insert = "INSERT INTO product_images_new (pro_code, prod_image, img_name, product_id, gproduct_id, date_added) 
                                   VALUES (
                                    '" . mysqli_real_escape_string($con, $sku) . "', 
                                    '" . mysqli_real_escape_string($con, $db_path) . "', 
                                    '" . mysqli_real_escape_string($con, $db_path) . "', 
                                    " . ($cat === 'Jewelry' ? intval($id) : 0) . ", 
                                    " . ($cat === 'Apparel' ? intval($id) : 0) . ",
                                    NOW()
                                   )";
                        mysqli_query($con, $insert);
                        $synced_count++;
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => "$synced_count images downloaded and synced successfully"]);
        }
        exit;
    }
}
