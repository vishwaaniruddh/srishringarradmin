<?php
$creds = [
    'host' => '193.203.184.203',
    'user' => 'u464193275_FCSOL',
    'pass' => 'caMrYFsAmF',
    'db'   => 'u464193275_ib3Xh'
];

$con = mysqli_connect($creds['host'], $creds['user'], $creds['pass'], $creds['db']);
if (!$con) die("Connection failed: " . mysqli_connect_error());

// Check post types and meta keys
echo "\n--- wpxyz_posts sample ---\n";
$res = mysqli_query($con, "SELECT ID, post_title, post_content FROM wpxyz_posts WHERE post_type = 'product' LIMIT 1");
if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}

echo "\n--- wpxyz_postmeta sample for a product with SKU ---\n";
$res = mysqli_query($con, "SELECT pm.post_id, pm.meta_key, pm.meta_value 
                           FROM wpxyz_postmeta pm 
                           JOIN wpxyz_postmeta pm2 ON pm.post_id = pm2.post_id 
                           WHERE pm2.meta_key = '_sku' AND pm2.meta_value != '' 
                           AND pm.meta_key IN ('_sku', '_product_image_gallery', '_thumbnail_id') 
                           LIMIT 10");
if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}
