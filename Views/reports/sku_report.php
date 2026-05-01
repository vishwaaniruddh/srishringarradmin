<!DOCTYPE html>
<html lang="en">
<head>
    <title>SKU Master Audit - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .nav-tabs { border-bottom: 2px solid #f51167; }
        .nav-link { color: #555; font-weight: 600; border: none !important; padding: 12px 25px; }
        .nav-link.active { background-color: #f51167 !important; color: #fff !important; border-radius: 10px 10px 0 0; }
        .tab-content { background: #fff; padding: 30px; border-radius: 0 0 15px 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
        .sku-badge { font-family: monospace; background: #eee; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .export-btn { background: #f51167; color: white; border-radius: 50px; padding: 8px 20px; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .export-btn:hover { background: #d40f5a; color: white; transform: translateY(-2px); }
        .summary-card { background: #fff; border-radius: 15px; padding: 20px; margin-bottom: 20px; border-left: 5px solid #f51167; }
        .search-input { width: 100%; padding: 10px 15px; border: 1px solid #eee; border-radius: 10px; margin-bottom: 15px; font-size: 14px; }
        .search-input:focus { outline: none; border-color: #f51167; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'SKU Master Audit';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Report Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="container-fluid">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-code-compare mr-2 text-primary"></i> SKU Master Audit</h2>
                            <p class="text-gray-500">Srishringarr (Local) vs Yosshitaneha (WordPress)</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 bg-gray-800 text-white text-xs font-bold rounded-full">SS: <?= count($skus_a) ?> SKUs</span>
                            <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">YN: <?= count($skus_b) ?> SKUs</span>
                        </div>
                    </div>

                    <?php if (isset($wp_error)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                            <p class="font-bold">Connection Warning</p>
                            <p><?= $wp_error ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                        <div class="summary-card">
                            <h6 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Srishringarr (A)</h6>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= count($skus_a) ?></h3>
                        </div>
                        <div class="summary-card" style="border-left-color: #0d6efd;">
                            <h6 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Yosshitaneha (B)</h6>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= count($skus_b) ?></h3>
                        </div>
                        <div class="summary-card" style="border-left-color: #dc3545;">
                            <h6 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Only in SS</h6>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= count($only_in_a) ?></h3>
                        </div>
                        <div class="summary-card" style="border-left-color: #fd7e14;">
                            <h6 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Only in YN</h6>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= count($only_in_b) ?></h3>
                        </div>
                        <div class="summary-card" style="border-left-color: #198754;">
                            <h6 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Matched (SS=YN)</h6>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= count($both_ab) ?></h3>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <ul class="flex border-b border-gray-200" id="skuTabs" role="tablist">
                            <li class="mr-1">
                                <button class="px-6 py-4 font-bold text-sm border-b-2 border-primary text-primary transition-all active-tab-btn" data-target="all-a">Srishringarr (A)</button>
                            </li>
                            <li class="mr-1">
                                <button class="px-6 py-4 font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-primary transition-all tab-btn" data-target="all-b">Yosshitaneha (B)</button>
                            </li>
                            <li class="mr-1">
                                <button class="px-6 py-4 font-bold text-sm border-b-2 border-transparent text-red-500 hover:text-red-700 transition-all tab-btn" data-target="only-a">Only in SS</button>
                            </li>
                            <li class="mr-1">
                                <button class="px-6 py-4 font-bold text-sm border-b-2 border-transparent text-orange-500 hover:text-orange-700 transition-all tab-btn" data-target="only-b">Only in YN</button>
                            </li>
                            <li class="mr-1">
                                <button class="px-6 py-4 font-bold text-sm border-b-2 border-transparent text-green-500 hover:text-green-700 transition-all tab-btn" data-target="both">Matched (SS=YN)</button>
                            </li>
                        </ul>

                        <div class="p-6">
                            <!-- Part A -->
                            <div class="tab-pane active" id="all-a">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-bold text-gray-700">SKUs in Srishringarr <small class="text-gray-400 font-normal">(Excluding 'Nath')</small></h5>
                                    <div class="flex gap-2">
                                        <a href="index.php?controller=report&action=sku&export=all_a" class="export-btn"><i class="fa fa-download mr-2"></i>Export SS</a>
                                    </div>
                                </div>
                                <?php renderTable($skus_a, $details_a, 'Srishringarr', 'all-a'); ?>
                            </div>

                            <!-- Part B -->
                            <div class="tab-pane hidden" id="all-b">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-bold text-gray-700">SKUs in Yosshitaneha (WordPress)</h5>
                                    <a href="index.php?controller=report&action=sku&export=all_b" class="export-btn"><i class="fa fa-download mr-2"></i>Export YN</a>
                                </div>
                                <?php renderTable($skus_b, $details_b, 'Yosshitaneha', 'all-b'); ?>
                            </div>

                            <!-- Only in A -->
                            <div class="tab-pane hidden" id="only-a">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-bold text-red-600">SKUs in SS but MISSING in YN</h5>
                                    <a href="index.php?controller=report&action=sku&export=only_a" class="export-btn bg-red-600 hover:bg-red-700"><i class="fa fa-download mr-2"></i>Export SS Only</a>
                                </div>
                                <?php renderTable($only_in_a, $details_a, 'Srishringarr', 'only-a'); ?>
                            </div>

                            <!-- Only in B -->
                            <div class="tab-pane hidden" id="only-b">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-bold text-orange-600">SKUs in YN but MISSING in SS</h5>
                                    <a href="index.php?controller=report&action=sku&export=only_b" class="export-btn bg-orange-600 hover:bg-orange-700"><i class="fa fa-download mr-2"></i>Export YN Only</a>
                                </div>
                                <?php renderTable($only_in_b, $details_b, 'Yosshitaneha', 'only-b'); ?>
                            </div>

                            <!-- Both -->
                            <div class="tab-pane hidden" id="both">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="font-bold text-green-600">Matched SKUs: Deep Content Comparison</h5>
                                    <div class="flex gap-4 items-center">
                                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                            <input type="checkbox" id="show-mismatches-only" class="rounded text-green-600">
                                            Show Mismatches Only
                                        </label>
                                        <a href="index.php?controller=report&action=sku&export=both" class="export-btn bg-green-600 hover:bg-green-700"><i class="fa fa-download mr-2"></i>Export Matches</a>
                                    </div>
                                </div>
                                <?php renderComparisonTable($both_ab, $details_a, $details_b, 'both'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switcher
            const tabBtns = document.querySelectorAll('.tab-btn, .active-tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');

                    // Update buttons
                    tabBtns.forEach(b => {
                        b.classList.remove('border-primary', 'text-primary', 'active-tab-btn');
                        b.classList.add('border-transparent', 'text-gray-500', 'tab-btn');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500', 'tab-btn');
                    this.classList.add('border-primary', 'text-primary', 'active-tab-btn');

                    // Update panes
                    tabPanes.forEach(p => p.classList.add('hidden'));
                    document.getElementById(target).classList.remove('hidden');
                });
            });

            // Table Search Filter
            window.filterTable = function(inputId, tableId) {
                const input = document.getElementById(inputId);
                const filter = input.value.toUpperCase();
                const table = document.getElementById(tableId);
                const tr = table.getElementsByTagName("tr");

                for (let i = 1; i < tr.length; i++) {
                    let found = false;
                    const tds = tr[i].getElementsByTagName("td");
                    for (let j = 0; j < tds.length; j++) {
                        if (tds[j]) {
                            const textValue = tds[j].textContent || tds[j].innerText;
                            if (textValue.toUpperCase().indexOf(filter) > -1) {
                                found = true;
                                break;
                            }
                        }
                    }
                    tr[i].style.display = found ? "" : "none";
                }
            };

            // Mismatch Filter
            const mismatchCheckbox = document.getElementById('show-mismatches-only');
            if (mismatchCheckbox) {
                mismatchCheckbox.addEventListener('change', function() {
                    const table = document.getElementById('table-both');
                    const tr = table.getElementsByTagName("tr");
                    for (let i = 1; i < tr.length; i++) {
                        if (this.checked) {
                            tr[i].style.display = tr[i].classList.contains('has-mismatch') ? "" : "none";
                        } else {
                            tr[i].style.display = "";
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
function renderTable($skus, $details, $source, $id)
{
    echo "<input type='text' id='search-$id' onkeyup='filterTable(\"search-$id\", \"table-$id\")' placeholder='Filter by SKU or Name...' class='search-input'>";
    echo "<div class='overflow-x-auto'><table id='table-$id' class='w-full text-left border-collapse text-sm'>";
    echo '<thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3">SKU</th><th class="px-4 py-3">Product Name</th><th class="px-4 py-3">Category/Source</th></tr></thead>';
    echo '<tbody class="divide-y divide-gray-100">';
    $i = 1;
    foreach ($skus as $sku) {
        $name = isset($details[$sku]) ? htmlspecialchars($details[$sku]['name']) : 'N/A';
        $cat = isset($details[$sku]['cat']) ? $details[$sku]['cat'] : $source;
        echo "<tr class='hover:bg-gray-50 transition-colors'>";
        echo "<td class='px-4 py-3 text-gray-400'>$i</td>";
        echo "<td class='px-4 py-3'><span class='sku-badge text-xs font-mono bg-gray-100 px-2 py-1 rounded'>$sku</span></td>";
        echo "<td class='px-4 py-3 font-medium'>$name</td>";
        echo "<td class='px-4 py-3 text-gray-500'>$cat</td>";
        echo "</tr>";
        $i++;
    }
    echo '</tbody></table></div>';
}

function renderComparisonTable($skus, $details_a, $details_b, $id)
{
    echo "<input type='text' id='search-$id' onkeyup='filterTable(\"search-$id\", \"table-$id\")' placeholder='Filter by SKU or Name...' class='search-input'>";
    echo "<div class='overflow-x-auto'><table id='table-$id' class='w-full text-left border-collapse text-xs'>";
    echo '<thead class="bg-gray-800 text-white border-b">
            <tr>
                <th class="px-3 py-3 sticky left-0 bg-gray-800">SKU</th>
                <th class="px-3 py-3 border-l border-gray-700">Property</th>
                <th class="px-3 py-3 border-l border-gray-700 bg-blue-900/50">YN (WordPress)</th>
                <th class="px-3 py-3 border-l border-gray-700 bg-pink-900/50">SS (Local)</th>
                <th class="px-3 py-3 border-l border-gray-700 text-center">Status</th>
            </tr>
          </thead>';
    echo '<tbody class="divide-y divide-gray-200">';
    
    foreach ($skus as $sku) {
        $a = $details_a[$sku];
        $b = $details_b[$sku];
        
        $title_match = (trim($a['name']) == trim($b['name']));
        $desc_match = (trim(strip_tags($a['desc'] ?? '')) == trim(strip_tags($b['desc'] ?? '')));
        $img_match = ($a['img_count'] == $b['img_count']);
        
        $has_mismatch = (!$title_match || !$desc_match || !$img_match);
        $mismatch_class = $has_mismatch ? 'has-mismatch bg-yellow-50/50' : 'bg-white';

        // Title Row
        echo "<tr class='$mismatch_class border-t-2 border-gray-100'>";
        echo "<td rowspan='3' class='px-3 py-4 font-bold bg-gray-50 sticky left-0 shadow-sm'><span class='sku-badge'>$sku</span></td>";
        echo "<td class='px-3 py-2 font-semibold text-gray-500'>Title</td>";
        echo "<td class='px-3 py-2 " . ($title_match ? '' : 'text-red-600 bg-red-50') . "'>" . htmlspecialchars($b['name']) . "</td>";
        echo "<td class='px-3 py-2 " . ($title_match ? '' : 'text-red-600 bg-red-50') . "'>" . htmlspecialchars($a['name']) . "</td>";
        echo "<td class='px-3 py-2 text-center'>" . ($title_match ? '✅' : '❌') . "</td>";
        echo "</tr>";

        // Description Row
        echo "<tr class='$mismatch_class'>";
        echo "<td class='px-3 py-2 font-semibold text-gray-500'>Description</td>";
        echo "<td class='px-3 py-2 " . ($desc_match ? '' : 'text-red-600 bg-red-50') . "'><div class='max-h-24 overflow-y-auto w-64'>" . (empty($b['desc']) ? '<i>Empty</i>' : htmlspecialchars(substr(strip_tags($b['desc']), 0, 300)) . '...') . "</div></td>";
        echo "<td class='px-3 py-2 " . ($desc_match ? '' : 'text-red-600 bg-red-50') . "'><div class='max-h-24 overflow-y-auto w-64'>" . (empty($a['desc']) ? '<i>Empty</i>' : htmlspecialchars(substr(strip_tags($a['desc']), 0, 300)) . '...') . "</div></td>";
        echo "<td class='px-3 py-2 text-center'>" . ($desc_match ? '✅' : '❌') . "</td>";
        echo "</tr>";

        // Image Count Row
        echo "<tr class='$mismatch_class'>";
        echo "<td class='px-3 py-2 font-semibold text-gray-500'>Images</td>";
        echo "<td class='px-3 py-2 " . ($img_match ? '' : 'font-bold text-red-600 bg-red-50') . "'>{$b['img_count']}</td>";
        echo "<td class='px-3 py-2 " . ($img_match ? '' : 'font-bold text-red-600 bg-red-50') . "'>{$a['img_count']}</td>";
        echo "<td class='px-3 py-2 text-center'>" . ($img_match ? '✅' : '❌') . "</td>";
        echo "</tr>";
    }
    echo '</tbody></table></div>';
}
?>
