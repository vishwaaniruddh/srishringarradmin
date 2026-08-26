<?php
/**
 * Direct Server Archive Importer
 * Imports products from the server's "archive" folder (Spreadsheet + SKU image folders)
 * Can be run via Browser or CLI (php import_archive.php)
 */

@ini_set('max_execution_time', 0);
@ini_set('memory_limit', '2048M');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Autoload composer & project classes
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $paths = [
        __DIR__ . DIRECTORY_SEPARATOR . $class . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . 'new_admin' . DIRECTORY_SEPARATOR . $class . '.php',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'new_admin' . DIRECTORY_SEPARATOR . $class . '.php'
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Locate the "archive" directory
$possibleArchivePaths = [
    __DIR__ . '/archive',
    __DIR__ . '/../archive',
    __DIR__ . '/../../archive',
    dirname(__DIR__) . '/archive',
    '/home/u464193275/domains/srishringarr.com/public_html/archive'
];

$archiveDir = null;
foreach ($possibleArchivePaths as $p) {
    if (is_dir($p)) {
        $archiveDir = realpath($p);
        break;
    }
}

// Check custom path from request
if (!empty($_POST['custom_path']) || !empty($_GET['custom_path'])) {
    $cPath = trim($_POST['custom_path'] ?? $_GET['custom_path']);
    if (is_dir($cPath)) {
        $archiveDir = realpath($cPath);
    }
}

$isCli = (php_sapi_name() === 'cli');

// AJAX API Handler for Batch Processing
if (isset($_GET['action']) && $_GET['action'] === 'process_row') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $productModel = new \Models\ProductModel();
    $categoryModel = new \Models\CategoryModel();

    try {
        $type = strtolower(trim($input['type'] ?? ''));
        $code = trim($input['sku'] ?? $input['sku_code'] ?? $input['code'] ?? '');
        $skuFolder = trim($input['sku_folder_path'] ?? '');

        if (empty($code)) throw new \Exception("Missing SKU");

        if (empty($type)) {
            $type = (stripos($code, 'GM') === 0 || stripos($code, 'LM') === 0 || stripos($code, 'FM') === 0) ? 'garments' : 'jewellery';
        } else if ($type === 'garment' || $type === 'apparel') {
            $type = 'garments';
        } else if ($type === 'jewelry' || $type === 'jewel') {
            $type = 'jewellery';
        }

        // 1. Check if SKU exists in EITHER jewellery OR garments -> SKIP if exists
        $existsJewel = $productModel->checkProductExists($code, 'jewellery');
        $existsGarment = $productModel->checkProductExists($code, 'garments');
        if ($existsJewel || $existsGarment) {
            echo json_encode([
                'status' => 'skipped',
                'sku' => $code,
                'message' => "SKU $code already exists in database (" . ($existsJewel ? 'jewellery' : 'garments') . "). Skipped."
            ]);
            exit;
        }

        // 2. Process & Copy Images from SKU Folder
        $downloadedImages = [];
        $current_year = date('Y');
        $current_month = date('m');
        $upload_base = __DIR__ . "/../yn/uploads/";
        if (!file_exists($upload_base)) {
            $upload_base = __DIR__ . "/../../yn/uploads/";
        }
        if (!file_exists($upload_base)) {
            $upload_base = __DIR__ . "/yn/uploads/";
        }
        $upload_path = $current_year . '/' . $current_month . '/';
        $full_upload_path = $upload_base . $upload_path;

        if (!file_exists($full_upload_path)) {
            @mkdir($full_upload_path, 0777, true);
        }

        if (!empty($skuFolder) && is_dir($skuFolder)) {
            $files = scandir($skuFolder);
            $validExts = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                $filePath = $skuFolder . DIRECTORY_SEPARATOR . $f;
                if (is_file($filePath)) {
                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if (in_array($ext, $validExts)) {
                        $newFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . '_' . time() . '_' . uniqid() . '.' . $ext;
                        if (@copy($filePath, $full_upload_path . $newFilename)) {
                            $downloadedImages[] = $upload_path . $newFilename;
                        }
                    }
                }
            }
        }

        // 3. Category & Subcategory Resolution
        $rawMain = !empty($input['categories']) ? (is_array($input['categories']) ? $input['categories'] : explode(',', (string)$input['categories'])) : [];
        if (!empty($input['category_id'])) $rawMain = array_merge($rawMain, explode(',', (string)$input['category_id']));
        if (!empty($input['category'])) $rawMain = array_merge($rawMain, explode(',', (string)$input['category']));

        $rawSub = !empty($input['sub_categories']) ? (is_array($input['sub_categories']) ? $input['sub_categories'] : explode(',', (string)$input['sub_categories'])) : [];
        if (!empty($input['subcat_id'])) $rawSub = array_merge($rawSub, explode(',', (string)$input['subcat_id']));
        if (!empty($input['sub_category'])) $rawSub = array_merge($rawSub, explode(',', (string)$input['sub_category']));

        $mainCategoryIds = [];
        foreach ($rawMain as $item) {
            $item = trim((string)$item);
            if (empty($item)) continue;
            if (is_numeric($item) && (int)$item > 0) {
                $mainCategoryIds[] = (int)$item;
            } else {
                $foundId = $categoryModel->getCategoryIdByName($item, $type);
                if ($foundId > 0) $mainCategoryIds[] = $foundId;
            }
        }
        $mainCategoryIds = array_values(array_unique(array_filter($mainCategoryIds)));

        $subcategoryIds = [];
        foreach ($rawSub as $item) {
            $item = trim((string)$item);
            if (empty($item)) continue;
            if (is_numeric($item) && (int)$item > 0) {
                $subcategoryIds[] = (int)$item;
            } else {
                $foundId = $categoryModel->getCategoryIdByName($item, $type);
                if ($foundId > 0) $subcategoryIds[] = $foundId;
            }
        }
        $subcategoryIds = array_values(array_unique(array_filter($subcategoryIds)));

        $primaryCatId = !empty($mainCategoryIds) ? $mainCategoryIds[0] : 0;
        $primarySubId = !empty($subcategoryIds) ? $subcategoryIds[0] : 0;

        // 4. Colors
        $colorsInput = $input['colors'] ?? $input['brand_color'] ?? '';
        $colorsArray = [];
        if (!empty($colorsInput)) {
            if (is_array($colorsInput)) {
                $colorsArray = $colorsInput;
            } else if (is_string($colorsInput)) {
                $trimmedC = trim($colorsInput);
                $decoded = json_decode($trimmedC, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $colorsArray = $decoded;
                } else {
                    $colorsArray = array_filter(array_map('trim', explode(',', $trimmedC)));
                }
            }
        }

        $saveData = [
            'code' => $code,
            'name' => $input['name'] ?? 'Imported Product',
            'description' => $input['description'] ?? '',
            'category' => $primaryCatId,
            'sub_category' => $primarySubId,
            'categories' => $mainCategoryIds,
            'sub_categories' => $subcategoryIds,
            's_price' => (float)($input['s_price'] ?? $input['sales_price'] ?? $input['sale_price'] ?? 0),
            'rental_price' => (float)($input['rental_price'] ?? $input['rent_price'] ?? 0),
            'deposit' => (float)($input['deposit'] ?? 0),
            'size_avail' => $input['size_avail'] ?? $input['size'] ?? '',
            'brand_name' => $input['brand_name'] ?? $input['brand'] ?? '',
            'colors' => $colorsArray,
            'brand_color' => $colorsArray,
            'price_source' => (!empty($input['price_source']) && strtolower($input['price_source']) === 'manual') ? 'manual' : 'pos',
            'availability' => in_array(strtolower($input['availability'] ?? ''), ['rent', 'sell', 'both']) ? strtolower($input['availability']) : 'both'
        ];

        $productModel->saveProduct($type, $saveData, $downloadedImages);

        echo json_encode([
            'status' => 'success',
            'sku' => $code,
            'images_count' => count($downloadedImages),
            'message' => "Created product $code with " . count($downloadedImages) . " images."
        ]);
        exit;
    } catch (\Exception $e) {
        echo json_encode([
            'status' => 'error',
            'sku' => $code ?? 'UNKNOWN',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Function to scan archive folder for spreadsheet & SKU folders
function scanArchiveDirectory($archiveDir) {
    if (!$archiveDir || !is_dir($archiveDir)) {
        return ['error' => 'Archive directory not found.'];
    }

    $spreadsheetFile = null;
    $skuFolders = [];
    $allEntries = scandir($archiveDir);

    foreach ($allEntries as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        $fullPath = $archiveDir . DIRECTORY_SEPARATOR . $entry;

        if (is_file($fullPath)) {
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                $spreadsheetFile = $fullPath;
            }
        } elseif (is_dir($fullPath)) {
            $skuFolders[strtolower($entry)] = $fullPath;
        }
    }

    if (!$spreadsheetFile) {
        return ['error' => 'No .xlsx, .xls, or .csv spreadsheet found in ' . $archiveDir];
    }

    // Read spreadsheet rows
    $parsedProducts = [];
    $ext = strtolower(pathinfo($spreadsheetFile, PATHINFO_EXTENSION));

    if ($ext === 'csv') {
        $handle = fopen($spreadsheetFile, 'r');
        if ($handle) {
            $headers = [];
            $rowIdx = 0;
            while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $rowIdx++;
                if ($rowIdx === 1) {
                    $headers = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                    }, $row);
                    continue;
                }
                if (empty(array_filter($row))) continue;
                $item = [];
                foreach ($headers as $idx => $header) {
                    $item[$header] = isset($row[$idx]) ? trim((string)$row[$idx]) : '';
                }
                $parsedProducts[] = $item;
            }
            fclose($handle);
        }
    } else {
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadsheetFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (!empty($rows) && count($rows) > 1) {
                $rawHeaders = $rows[0];
                $headers = array_map(function($h) {
                    return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$h)));
                }, $rawHeaders);

                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (empty(array_filter($row, function($v) { return $v !== null && $v !== ''; }))) continue;
                    $item = [];
                    foreach ($headers as $idx => $header) {
                        if (!empty($header)) {
                            $val = $row[$idx] ?? '';
                            $item[$header] = is_string($val) ? trim($val) : (string)$val;
                        }
                    }
                    $parsedProducts[] = $item;
                }
            }
        } else {
            return ['error' => 'PhpSpreadsheet is not loaded. Please use a CSV file or install composer dependencies.'];
        }
    }

    // Match products with SKU folders
    $validProducts = [];
    foreach ($parsedProducts as $p) {
        $sku = trim($p['sku'] ?? $p['sku_code'] ?? $p['product_code'] ?? $p['code'] ?? '');
        $name = trim($p['name'] ?? $p['product_name'] ?? '');
        if (empty($sku) && empty($name)) continue;

        if (empty($p['sku'])) $p['sku'] = $sku;

        $skuKey = strtolower($sku);
        $matchedPath = $skuFolders[$skuKey] ?? null;

        $imgCount = 0;
        if ($matchedPath && is_dir($matchedPath)) {
            $imgs = scandir($matchedPath);
            foreach ($imgs as $im) {
                if ($im !== '.' && $im !== '..') $imgCount++;
            }
        }

        $p['sku_folder_path'] = $matchedPath ?: '';
        $p['images_count'] = $imgCount;
        $validProducts[] = $p;
    }

    return [
        'archive_dir' => $archiveDir,
        'spreadsheet_file' => basename($spreadsheetFile),
        'total_folders' => count($skuFolders),
        'products' => $validProducts
    ];
}

$scanResult = scanArchiveDirectory($archiveDir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Archive Importer - Srishringarr</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6 md:p-12">
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Top Bar -->
        <div class="bg-slate-900/90 border border-slate-800 p-6 rounded-2xl shadow-xl flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-xl">
                    <i class="fas fa-server"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        Direct Server Archive Importer
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-500/30 uppercase">Bypasses Upload Limits</span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Processes SKU folders and spreadsheet directly from your server's root <code>archive/</code> directory.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php?controller=product&action=index" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold border border-slate-700 transition-all flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Admin Panel
                </a>
            </div>
        </div>

        <?php if (!empty($scanResult['error'])): ?>
            <!-- Error Card -->
            <div class="bg-rose-950/40 border border-rose-500/40 p-6 rounded-2xl text-xs text-rose-300 space-y-3">
                <div class="flex items-center gap-2 text-sm font-bold text-rose-400">
                    <i class="fas fa-exclamation-triangle"></i> Archive Directory Issue
                </div>
                <p><?php echo htmlspecialchars($scanResult['error']); ?></p>
                <form method="GET" class="flex gap-2 pt-2">
                    <input type="text" name="custom_path" placeholder="/path/to/your/archive/folder" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all">Scan Path</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Archive Detection Info -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Archive Path</div>
                    <div class="text-xs font-mono text-indigo-300 truncate" title="<?php echo htmlspecialchars($scanResult['archive_dir']); ?>">
                        <?php echo htmlspecialchars($scanResult['archive_dir']); ?>
                    </div>
                </div>
                <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Spreadsheet File</div>
                    <div class="text-sm font-bold text-emerald-400 flex items-center gap-1.5">
                        <i class="fas fa-file-excel"></i> <?php echo htmlspecialchars($scanResult['spreadsheet_file']); ?>
                    </div>
                </div>
                <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">SKU Image Folders</div>
                    <div class="text-2xl font-extrabold text-white"><?php echo $scanResult['total_folders']; ?></div>
                </div>
                <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Products in Sheet</div>
                    <div class="text-2xl font-extrabold text-indigo-400"><?php echo count($scanResult['products']); ?></div>
                </div>
            </div>

            <!-- Import Execution Section -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-bolt text-yellow-400"></i> Start Import Process
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Products that already exist will be safely <b>SKIPPED</b> without modifying data.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="download_report_btn" class="hidden px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5">
                            <i class="fas fa-download text-emerald-400"></i> Download Full Report (CSV)
                        </button>
                        <button id="start_btn" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                            <i class="fas fa-play"></i> Run Archive Import Now
                        </button>
                    </div>
                </div>

                <!-- Stats Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="bg-slate-950 p-3 rounded-xl border border-slate-800">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Total</div>
                        <div id="cnt_total" class="text-xl font-bold text-white"><?php echo count($scanResult['products']); ?></div>
                    </div>
                    <div class="bg-emerald-950/30 p-3 rounded-xl border border-emerald-500/20">
                        <div class="text-[10px] text-emerald-400 font-bold uppercase">Created</div>
                        <div id="cnt_created" class="text-xl font-bold text-emerald-400">0</div>
                    </div>
                    <div class="bg-amber-950/30 p-3 rounded-xl border border-amber-500/20">
                        <div class="text-[10px] text-amber-400 font-bold uppercase">Skipped (Exists)</div>
                        <div id="cnt_skipped" class="text-xl font-bold text-amber-400">0</div>
                    </div>
                    <div class="bg-rose-950/30 p-3 rounded-xl border border-rose-500/20">
                        <div class="text-[10px] text-rose-400 font-bold uppercase">Errors</div>
                        <div id="cnt_error" class="text-xl font-bold text-rose-400">0</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span id="progress_status" class="text-slate-400 font-mono">Ready to import...</span>
                        <span id="progress_pct" class="font-bold text-indigo-400">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-700">
                        <div id="progress_bar" class="bg-indigo-600 h-full w-0 transition-all duration-150"></div>
                    </div>
                </div>

                <!-- Live Log Table -->
                <div class="border border-slate-800 rounded-xl overflow-hidden bg-slate-950">
                    <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-900/90 text-slate-400 text-[10px] font-bold uppercase tracking-wider sticky top-0 border-b border-slate-800 z-10">
                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Product Name</th>
                                    <th class="px-4 py-3">Photos</th>
                                    <th class="px-4 py-3 text-right">Status & Notes</th>
                                </tr>
                            </thead>
                            <tbody id="log_tbody" class="divide-y divide-slate-800/60 font-mono text-[11px]">
                                <?php foreach ($scanResult['products'] as $idx => $p): ?>
                                    <tr id="row-<?php echo $idx; ?>" class="hover:bg-slate-900/40 transition-colors">
                                        <td class="px-4 py-2.5 text-slate-500"><?php echo $idx + 1; ?></td>
                                        <td class="px-4 py-2.5 font-bold text-indigo-300"><?php echo htmlspecialchars($p['sku']); ?></td>
                                        <td class="px-4 py-2.5 text-slate-300 truncate max-w-[200px]" title="<?php echo htmlspecialchars($p['name'] ?? ''); ?>"><?php echo htmlspecialchars($p['name'] ?? ''); ?></td>
                                        <td class="px-4 py-2.5 text-slate-400">
                                            <?php if ($p['images_count'] > 0): ?>
                                                <span class="text-emerald-400 font-bold"><i class="fas fa-images mr-1"></i><?php echo $p['images_count']; ?></span>
                                            <?php else: ?>
                                                <span class="text-slate-600">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-right status-col text-slate-500">Pending</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                const productsData = <?php echo json_encode($scanResult['products']); ?>;
                const startBtn = document.getElementById('start_btn');
                const downloadReportBtn = document.getElementById('download_report_btn');
                const progressBar = document.getElementById('progress_bar');
                const progressPct = document.getElementById('progress_pct');
                const progressStatus = document.getElementById('progress_status');
                
                const cntCreated = document.getElementById('cnt_created');
                const cntSkipped = document.getElementById('cnt_skipped');
                const cntError = document.getElementById('cnt_error');

                let fullResults = [['#', 'SKU', 'Name', 'Status', 'Images', 'Reason/Message']];

                startBtn.addEventListener('click', async function() {
                    startBtn.disabled = true;
                    startBtn.classList.add('opacity-50', 'cursor-not-allowed');

                    let created = 0, skipped = 0, errors = 0;

                    for (let i = 0; i < productsData.length; i++) {
                        const item = productsData[i];
                        const rowEl = document.getElementById(`row-${i}`);
                        const statusCol = rowEl ? rowEl.querySelector('.status-col') : null;

                        if (statusCol) {
                            statusCol.innerHTML = `<span class="text-indigo-400 animate-pulse"><i class="fas fa-spinner fa-spin mr-1"></i>Processing</span>`;
                        }
                        if (rowEl) rowEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                        try {
                            const res = await fetch('import_archive.php?action=process_row', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(item)
                            });
                            const data = await res.json();

                            if (data.status === 'success') {
                                created++;
                                cntCreated.textContent = created;
                                if (statusCol) {
                                    statusCol.innerHTML = `<span class="text-emerald-400 font-bold"><i class="fas fa-check-circle mr-1"></i>Created (${data.images_count || 0} imgs)</span>`;
                                }
                                fullResults.push([i+1, item.sku, item.name || '', 'Created', data.images_count || 0, data.message || '']);
                            } else if (data.status === 'skipped') {
                                skipped++;
                                cntSkipped.textContent = skipped;
                                if (statusCol) {
                                    statusCol.innerHTML = `<div><span class="text-amber-400 font-bold"><i class="fas fa-forward mr-1"></i>Skipped</span><div class="text-[9px] text-amber-500/80 mt-0.5">Already in DB</div></div>`;
                                }
                                fullResults.push([i+1, item.sku, item.name || '', 'Skipped', 0, data.message || 'Already exists']);
                            } else {
                                errors++;
                                cntError.textContent = errors;
                                if (statusCol) {
                                    statusCol.innerHTML = `<div><span class="text-rose-400 font-bold"><i class="fas fa-times-circle mr-1"></i>Failed</span><div class="text-[9px] text-rose-400 mt-0.5 max-w-[280px] truncate" title="${data.message}">${data.message}</div></div>`;
                                }
                                fullResults.push([i+1, item.sku, item.name || '', 'Error', 0, data.message || 'Unknown error']);
                            }
                        } catch (err) {
                            errors++;
                            cntError.textContent = errors;
                            if (statusCol) {
                                statusCol.innerHTML = `<div><span class="text-rose-400 font-bold"><i class="fas fa-times-circle mr-1"></i>HTTP Error</span><div class="text-[9px] text-rose-400 mt-0.5">${err.message}</div></div>`;
                            }
                            fullResults.push([i+1, item.sku, item.name || '', 'Error', 0, err.message]);
                        }

                        const pct = Math.round(((i + 1) / productsData.length) * 100);
                        progressBar.style.width = pct + '%';
                        progressPct.textContent = pct + '%';
                        progressStatus.textContent = `Processing item ${i + 1} of ${productsData.length} (${item.sku})...`;
                    }

                    progressStatus.textContent = `Import completed! Created: ${created}, Skipped: ${skipped}, Errors: ${errors}`;
                    startBtn.textContent = 'Import Completed';
                    startBtn.className = 'px-8 py-3 bg-emerald-600 text-white rounded-xl text-xs font-bold cursor-default';
                    downloadReportBtn.classList.remove('hidden');
                });

                downloadReportBtn.addEventListener('click', function() {
                    const csvContent = fullResults.map(r => r.map(c => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `Archive_Import_Report_${new Date().toISOString().slice(0,10)}.csv`;
                    link.click();
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
