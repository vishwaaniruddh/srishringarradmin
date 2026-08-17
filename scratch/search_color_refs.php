<?php
function searchInFiles($dir, $pattern) {
    $rdi = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $rii = new RecursiveIteratorIterator($rdi);
    $matches = [];
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $ext = pathinfo($file->getPathname(), PATHINFO_EXTENSION);
        if (!in_array($ext, ['php', 'js', 'jsx', 'ts', 'tsx', 'sql', 'html', 'json'])) continue;
        if (str_contains($file->getPathname(), 'vendor') || str_contains($file->getPathname(), 'node_modules')) continue;
        
        $content = @file_get_contents($file->getPathname());
        if ($content && stripos($content, $pattern) !== false) {
            $matches[] = $file->getPathname();
        }
    }
    return $matches;
}

echo "=== Searching for brand_color ===\n";
print_r(searchInFiles(__DIR__ . '/../../', 'brand_color'));

echo "=== Searching for product_color ===\n";
print_r(searchInFiles(__DIR__ . '/../../', 'product_color'));

echo "=== Searching for brand_color in DB records ===\n";
require_once __DIR__ . '/../Core/Database.php';
$con = \Core\Database::getConnection('con');
$res = mysqli_query($con, "SELECT product_id, product_code, brand_color FROM product WHERE brand_color IS NOT NULL AND brand_color != '' LIMIT 10");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
$res2 = mysqli_query($con, "SELECT gproduct_id, gproduct_code, brand_color FROM garment_product WHERE brand_color IS NOT NULL AND brand_color != '' LIMIT 10");
while ($row = mysqli_fetch_assoc($res2)) {
    print_r($row);
}
