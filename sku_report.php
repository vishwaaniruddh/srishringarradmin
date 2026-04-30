<?php
include('config.php');

// WordPress (Yosshitaneha) Database Credentials
$wp_host = 'localhost';
$wp_user = 'u464193275_FCSOL';
$wp_pass = 'caMrYFsAmF';
$wp_db = 'u464193275_ib3Xh';

// Establish WordPress Connection
$wp_con = mysqli_connect($wp_host, $wp_user, $wp_pass, $wp_db);

if (!$wp_con) {
    $wp_error = mysqli_connect_error();
}

// 1. Fetch SKUs from Srishringarr (Part A)
$skus_a = [];
$details_a = [];

// Jewelry - Filter out 'nath'
$res_j = mysqli_query($con, "SELECT product_code, product_name, ss_product_name FROM product 
                             WHERE product_code != '' 
                             AND product_name NOT LIKE '%nath%' 
                             AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
while ($row = mysqli_fetch_assoc($res_j)) {
    $sku = strtoupper(trim($row['product_code']));
    $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['product_name'];
    $skus_a[] = $sku;
    $details_a[$sku] = ['name' => $name, 'cat' => 'Jewelry'];
}

// Apparel - Filter out 'nath' (just in case)
$res_app = mysqli_query($con, "SELECT gproduct_code, gproduct_name, ss_product_name FROM garment_product 
                               WHERE gproduct_code != '' 
                               AND gproduct_name NOT LIKE '%nath%' 
                               AND (ss_product_name IS NULL OR ss_product_name NOT LIKE '%nath%')");
while ($row = mysqli_fetch_assoc($res_app)) {
    $sku = strtoupper(trim($row['gproduct_code']));
    $name = !empty($row['ss_product_name']) ? $row['ss_product_name'] : $row['gproduct_name'];
    $skus_a[] = $sku;
    $details_a[$sku] = ['name' => $name, 'cat' => 'Apparel'];
}

// Unique SKUs for A
$skus_a = array_unique($skus_a);

// 2. Fetch SKUs from Yosshitaneha (Part B - WordPress)
$skus_b = [];
$details_b = [];
if ($wp_con) {
    $query_wp = "SELECT pm.meta_value as sku, p.post_title as name 
                 FROM wpxyz_posts p 
                 JOIN wpxyz_postmeta pm ON p.ID = pm.post_id 
                 WHERE p.post_type IN ('product', 'product_variation') 
                 AND pm.meta_key = '_sku' 
                 AND pm.meta_value != ''";
    $res_wp = mysqli_query($wp_con, $query_wp);
    if ($res_wp) {
        while ($row = mysqli_fetch_assoc($res_wp)) {
            $sku = strtoupper(trim($row['sku']));
            $skus_b[] = $sku;
            $details_b[$sku] = ['name' => $row['name']];
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
    fputcsv($output, array('SKU', 'Product Name', 'Source/Status'));

    switch ($type) {
        case 'all_a':
            foreach ($skus_a as $sku) fputcsv($output, array($sku, $details_a[$sku]['name'], 'Srishringarr (A)'));
            break;
        case 'all_b':
            foreach ($skus_b as $sku) fputcsv($output, array($sku, $details_b[$sku]['name'], 'Yosshitaneha (B)'));
            break;
        case 'only_a':
            foreach ($only_in_a as $sku) fputcsv($output, array($sku, $details_a[$sku]['name'], 'Only in Srishringarr'));
            break;
        case 'only_b':
            foreach ($only_in_b as $sku) fputcsv($output, array($sku, $details_b[$sku]['name'], 'Only in Yosshitaneha'));
            break;
        case 'both':
            foreach ($both_ab as $sku) fputcsv($output, array($sku, $details_a[$sku]['name'], 'Matched in Both'));
            break;
    }
    fclose($output);
    exit;
}

include('header.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<style>
    .report-container { padding: 40px 0; background: #f4f7f6; min-height: 100vh; }
    .nav-tabs { border-bottom: 2px solid #f51167; }
    .nav-link { color: #555; font-weight: 600; border: none !important; padding: 12px 25px; }
    .nav-link.active { background-color: #f51167 !important; color: #fff !important; border-radius: 10px 10px 0 0; }
    .tab-content { background: #fff; padding: 30px; border-radius: 0 0 15px 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .sku-badge { font-family: monospace; background: #eee; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
    .export-btn { background: #f51167; color: white; border-radius: 50px; padding: 8px 20px; text-decoration: none; font-size: 14px; transition: 0.3s; }
    .export-btn:hover { background: #d40f5a; color: white; transform: translateY(-2px); }
    .summary-card { background: #fff; border-radius: 15px; padding: 20px; margin-bottom: 20px; border-left: 5px solid #f51167; }
</style>

<div class="report-container">
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fa-solid fa-code-compare me-2"></i> SKU Master Audit: Srishringarr vs Yosshitaneha</h2>
            <div class="text-end">
                <span class="badge bg-dark p-2">SS: <?php echo count($skus_a); ?> SKUs</span>
                <span class="badge bg-primary p-2">YN: <?php echo count($skus_b); ?> SKUs</span>
            </div>
        </div>

        <?php if (isset($wp_error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>Yosshitaneha (WordPress) Connection Failed:</strong> <?php echo $wp_error; ?>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-2">
                <div class="summary-card text-center">
                    <h6 class="text-muted">Srishringarr (A)</h6>
                    <h3><?php echo count($skus_a); ?></h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card text-center" style="border-left-color: #0d6efd;">
                    <h6 class="text-muted">Yosshitaneha (B)</h6>
                    <h3><?php echo count($skus_b); ?></h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card text-center" style="border-left-color: #dc3545;">
                    <h6 class="text-muted">Only in SS</h6>
                    <h3><?php echo count($only_in_a); ?></h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card text-center" style="border-left-color: #fd7e14;">
                    <h6 class="text-muted">Only in YN</h6>
                    <h3><?php echo count($only_in_b); ?></h3>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card text-center" style="border-left-color: #198754;">
                    <h6 class="text-muted">SS = YN (Match)</h6>
                    <h3><?php echo count($both_ab); ?></h3>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="skuTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="all-a-tab" data-bs-toggle="tab" data-bs-target="#all-a"
                    type="button">Srishringarr (A)</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="all-b-tab" data-bs-toggle="tab" data-bs-target="#all-b" type="button">Yosshitaneha (B)</button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-danger" id="only-a-tab" data-bs-toggle="tab" data-bs-target="#only-a"
                    type="button">Only in SS</button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-warning" id="only-b-tab" data-bs-toggle="tab" data-bs-target="#only-b"
                    type="button">Only in YN</button>
            </li>
            <li class="nav-item">
                <button class="nav-link text-success" id="both-tab" data-bs-toggle="tab" data-bs-target="#both"
                    type="button">Matched (A=B)</button>
            </li>
        </ul>

        <div class="tab-content" id="skuTabsContent">
            <!-- Part A -->
            <div class="tab-pane fade show active" id="all-a">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>SKUs in Srishringarr (Web) <small class='text-muted'>(Excluding 'Nath')</small></h5>
                    <a href="?export=all_a" class="export-btn"><i class="fa fa-download me-2"></i>Export SS</a>
                </div>
                <?php renderTable($skus_a, $details_a, 'Srishringarr'); ?>
            </div>

            <!-- Part B -->
            <div class="tab-pane fade" id="all-b">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>SKUs in Yosshitaneha (WordPress)</h5>
                    <a href="?export=all_b" class="export-btn"><i class="fa fa-download me-2"></i>Export YN</a>
                </div>
                <?php renderTable($skus_b, $details_b, 'Yosshitaneha'); ?>
            </div>

            <!-- Only in A -->
            <div class="tab-pane fade" id="only-a">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>SKUs in Srishringarr but MISSING in Yosshitaneha</h5>
                    <a href="?export=only_a" class="export-btn btn-danger"><i class="fa fa-download me-2"></i>Export SS Only</a>
                </div>
                <?php renderTable($only_in_a, $details_a, 'Srishringarr'); ?>
            </div>

            <!-- Only in B -->
            <div class="tab-pane fade" id="only-b">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>SKUs in Yosshitaneha but MISSING in Srishringarr</h5>
                    <a href="?export=only_b" class="export-btn btn-warning"><i class="fa fa-download me-2"></i>Export YN Only</a>
                </div>
                <?php renderTable($only_in_b, $details_b, 'Yosshitaneha'); ?>
            </div>

            <!-- Both -->
            <div class="tab-pane fade" id="both">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>SKUs matching in both Platforms</h5>
                    <a href="?export=both" class="export-btn btn-success"><i class="fa fa-download me-2"></i>Export Matches</a>
                </div>
                <?php renderTable($both_ab, $details_a, 'Both'); ?>
            </div>
        </div>
    </div>
</div>

<?php
function renderTable($skus, $details, $source)
{
    echo '<table class="table table-hover datatable w-100">';
    echo '<thead><tr><th>#</th><th>SKU</th><th>Product Name</th><th>Category/Source</th></tr></thead>';
    echo '<tbody>';
    $i = 1;
    foreach ($skus as $sku) {
        $name = isset($details[$sku]) ? htmlspecialchars($details[$sku]['name']) : 'N/A';
        $cat = isset($details[$sku]['cat']) ? $details[$sku]['cat'] : $source;
        echo "<tr>";
        echo "<td>$i</td>";
        echo "<td><span class='sku-badge'>$sku</span></td>";
        echo "<td>$name</td>";
        echo "<td>$cat</td>";
        echo "</tr>";
        $i++;
    }
    echo '</tbody></table>';
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('.datatable').DataTable({
            "pageLength": 25,
            "language": { "search": "Filter SKU/Name:" }
        });
    });
</script>

</body>
</html>