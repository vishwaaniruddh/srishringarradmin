<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Export &amp; Import History - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        :root {
            --wp-dark: #09090b;
            --wp-border: #e4e4e7;
            --wp-bg: #fafafa;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: var(--wp-bg) !important;
            color: var(--wp-text) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        .shadcn-card {
            background: #ffffff;
            border: 1px solid var(--wp-border);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .shadcn-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            flex-wrap: wrap;
            gap: 10px;
        }

        .shadcn-card-body {
            padding: 18px;
        }

        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 12px;
            height: 32px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1;
            border-radius: 6px;
            border: 1px solid var(--wp-border);
            background: #ffffff;
            color: #09090b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        .shadcn-btn-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .shadcn-btn-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        .shadcn-btn-sm {
            height: 26px;
            padding: 0 8px;
            font-size: 11.5px;
            border-radius: 4px;
        }

        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        .field-input, .field-select {
            height: 34px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
            font-family: inherit;
        }
        .field-input:focus, .field-select:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }

        .quick-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 500;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #3f3f46;
            cursor: pointer;
            transition: all 0.12s ease;
            user-select: none;
        }
        .quick-chip:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }
        .quick-chip.active {
            background: #09090b !important;
            color: #ffffff !important;
            border-color: #09090b !important;
            font-weight: 600;
        }

        .mode-tab {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.12s ease;
            border: 1px solid transparent;
            color: #71717a;
            background: transparent;
        }
        .mode-tab.active {
            background: #ffffff;
            color: #09090b;
            font-weight: 600;
            border-color: #e4e4e7;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .shadcn-table th {
            background: #fafafa;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
            border-bottom: 1px solid #e4e4e7;
            text-align: left;
        }
        .shadcn-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f4f4f5;
            vertical-align: middle;
        }
        .shadcn-table tr:hover td {
            background: #fafafa;
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Unified Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Product Export & Import History';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-gray-50/50">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Product Export &amp; Import History</h1>
                                <span class="shadcn-badge">
                                    <i class="fa-solid fa-file-export text-[10px]"></i> Reports Center
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">
                                Filter products by exact import date or date range and export comprehensive reports to CSV or Excel.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="index.php?controller=product&action=index" class="shadcn-btn">
                                <i class="fa-solid fa-boxes-stacked"></i> All Products
                            </a>
                            <a href="import_archive.php" class="shadcn-btn">
                                <i class="fa-solid fa-folder-tree"></i> Archive Importer
                            </a>
                        </div>
                    </div>

                    <!-- Metric Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <!-- Total Imported Products -->
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lifetime Imported Products</span>
                                <span class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    <i class="fa-solid fa-boxes-packing text-xs"></i>
                                </span>
                            </div>
                            <div class="text-2xl font-bold text-slate-900 tracking-tight">
                                <?php echo number_format($totalImportedLifetime); ?>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Recorded across all historical import batches</p>
                        </div>

                        <!-- Latest Import Batch -->
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Latest Import Batch</span>
                                <span class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                </span>
                            </div>
                            <?php if (!empty($importHistory)): ?>
                                <div class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                    <?php echo date('d M Y', strtotime($importHistory[0]['import_date'])); ?>
                                    <span class="text-xs font-normal text-slate-500">(<?php echo number_format($importHistory[0]['total_count']); ?> items)</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    <?php echo (int)$importHistory[0]['jewellery_count']; ?> Jewellery &bull; <?php echo (int)$importHistory[0]['garment_count']; ?> Garments
                                </p>
                            <?php else: ?>
                                <div class="text-lg font-bold text-slate-700">No Batches Yet</div>
                                <p class="text-[11px] text-slate-400 mt-1">—</p>
                            <?php endif; ?>
                        </div>

                        <!-- Total Historical Batches -->
                        <div class="shadcn-card p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Historical Import Dates</span>
                                <span class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    <i class="fa-solid fa-timeline text-xs"></i>
                                </span>
                            </div>
                            <div class="text-2xl font-bold text-slate-900 tracking-tight">
                                <?php echo count($importHistory); ?> Days
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Distinct upload dates logged in database</p>
                        </div>
                    </div>

                    <!-- ==================== EXPORT FILTER & DOWNLOAD TOOLBAR ==================== -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-filter"></i>
                                </span>
                                <span class="font-bold text-xs text-slate-900 uppercase tracking-wider">Export Filter &amp; Date Selection</span>
                            </div>

                            <!-- Mode Toggle -->
                            <div class="bg-slate-100 p-0.5 rounded-lg border border-slate-200 flex items-center gap-1">
                                <button type="button" onclick="setFilterMode('single')" id="mode_btn_single" class="mode-tab <?php echo $filterMode !== 'range' ? 'active' : ''; ?>">
                                    <i class="fa-regular fa-calendar"></i> Single Date
                                </button>
                                <button type="button" onclick="setFilterMode('range')" id="mode_btn_range" class="mode-tab <?php echo $filterMode === 'range' ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-calendar-week"></i> Date Range
                                </button>
                            </div>
                        </div>

                        <div class="shadcn-card-body">
                            <form id="export_filter_form" method="GET" action="index.php">
                                <input type="hidden" name="controller" value="product">
                                <input type="hidden" name="action" id="form_action_input" value="exportPage">
                                <input type="hidden" name="mode" id="filter_mode_input" value="<?php echo htmlspecialchars($filterMode); ?>">

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    
                                    <!-- Single Date Field -->
                                    <div id="single_date_col" class="<?php echo $filterMode === 'range' ? 'hidden' : 'md:col-span-4'; ?>">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                            Import Date <span class="text-slate-400 font-normal">(YYYY-MM-DD)</span>
                                        </label>
                                        <div class="relative">
                                            <input type="date" name="date" id="input_single_date" value="<?php echo htmlspecialchars($singleDate); ?>" class="field-input w-full">
                                        </div>
                                    </div>

                                    <!-- Date Range Fields -->
                                    <div id="range_start_col" class="<?php echo $filterMode === 'range' ? 'md:col-span-3' : 'hidden'; ?>">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">From Date</label>
                                        <input type="date" name="start_date" id="input_start_date" value="<?php echo htmlspecialchars($startDate); ?>" class="field-input w-full">
                                    </div>
                                    <div id="range_end_col" class="<?php echo $filterMode === 'range' ? 'md:col-span-3' : 'hidden'; ?>">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">To Date</label>
                                        <input type="date" name="end_date" id="input_end_date" value="<?php echo htmlspecialchars($endDate); ?>" class="field-input w-full">
                                    </div>

                                    <!-- Product Department / Type -->
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Product Type</label>
                                        <select name="type" class="field-select w-full">
                                            <option value="all" <?php echo $productType === 'all' ? 'selected' : ''; ?>>All Products (Jewellery &amp; Apparel)</option>
                                            <option value="jewellery" <?php echo $productType === 'jewellery' ? 'selected' : ''; ?>>Jewellery Only</option>
                                            <option value="garments" <?php echo $productType === 'garments' ? 'selected' : ''; ?>>Apparel / Garments Only</option>
                                        </select>
                                    </div>

                                    <!-- SKU or Name Filter -->
                                    <div class="<?php echo $filterMode === 'range' ? 'md:col-span-3' : 'md:col-span-3'; ?>">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">SKU or Keyword <span class="text-slate-400 font-normal">(Optional)</span></label>
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Filter by code or name..." class="field-input w-full">
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="<?php echo $filterMode === 'range' ? 'md:col-span-3' : 'md:col-span-2'; ?> flex items-center gap-1.5 flex-wrap">
                                        <button type="submit" onclick="document.getElementById('form_action_input').value='exportPage'" class="shadcn-btn w-full justify-center" title="Filter and view live preview below">
                                            <i class="fa-solid fa-magnifying-glass"></i> Filter &amp; Preview
                                        </button>
                                    </div>
                                </div>

                                <!-- Quick Preset Date Chips -->
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[11px] font-semibold text-slate-500 mr-1 flex items-center gap-1">
                                        <i class="fa-solid fa-bolt text-amber-500"></i> Quick Presets:
                                    </span>

                                    <?php if (!empty($importHistory)): ?>
                                        <?php 
                                        $topHistory = array_slice($importHistory, 0, 4);
                                        foreach ($topHistory as $bh): 
                                            $isChipActive = ($filterMode === 'single' && $singleDate === $bh['import_date']);
                                            $chipLabel = date('d M', strtotime($bh['import_date'])) . ' (' . number_format($bh['total_count']) . ')';
                                        ?>
                                            <button type="button" onclick="selectQuickDate('<?php echo $bh['import_date']; ?>')" class="quick-chip <?php echo $isChipActive ? 'active' : ''; ?>">
                                                <i class="fa-regular fa-calendar-check text-[10px]"></i> <?php echo $chipLabel; ?>
                                            </button>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <button type="button" onclick="selectQuickRange(7)" class="quick-chip">
                                        Last 7 Days
                                    </button>
                                    <button type="button" onclick="selectQuickRange(30)" class="quick-chip">
                                        Last 30 Days
                                    </button>
                                    <button type="button" onclick="selectAllHistory()" class="quick-chip">
                                        All Time
                                    </button>
                                </div>

                                <!-- Export Trigger Bar -->
                                <div class="mt-4 pt-3.5 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-3 bg-slate-50/70 -mx-4 -mb-4 p-4 rounded-b-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-700">
                                            Found <span class="font-bold text-slate-900 text-sm"><?php echo number_format($totalFilteredCount); ?></span> matching product(s)
                                        </span>
                                        <span class="shadcn-badge bg-white">
                                            <?php echo number_format($jewelleryFilteredCount); ?> Jewellery &bull; <?php echo number_format($garmentFilteredCount); ?> Garments
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Download CSV Button -->
                                        <button type="submit" formaction="index.php?controller=product&action=export&format=csv" class="shadcn-btn shadcn-btn-primary" title="Download matching products as CSV spreadsheet">
                                            <i class="fa-solid fa-file-csv"></i> Download CSV
                                        </button>

                                        <!-- Download Excel Button -->
                                        <button type="submit" formaction="index.php?controller=product&action=export&format=excel" class="shadcn-btn bg-white hover:bg-slate-50 text-slate-900 border-slate-300" title="Download matching products as formatted Microsoft Excel (.xlsx)">
                                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Download Excel (.xlsx)
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ==================== WHEN WE HAVE IMPORTED PRODUCTS (IMPORT HISTORY BREAKDOWN) ==================== -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 uppercase tracking-wider">When Products Were Imported (Import History &amp; Batches)</span>
                                    <p class="text-[11px] text-slate-400 font-normal">All recorded import dates with product breakdown and 1-click batch download</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">
                                <?php echo count($importHistory); ?> Batch Dates Logged
                            </span>
                        </div>

                        <div class="p-0 overflow-x-auto max-h-[360px] overflow-y-auto">
                            <?php if (!empty($importHistory)): ?>
                                <table class="shadcn-table">
                                    <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-2xs">
                                        <tr>
                                            <th class="bg-slate-50">Import Date &amp; Day</th>
                                            <th class="bg-slate-50 text-center">Total Imported</th>
                                            <th class="bg-slate-50">Department Breakdown</th>
                                            <th class="bg-slate-50">Quick Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($importHistory as $idx => $batch): 
                                            $dt = $batch['import_date'];
                                            $timestamp = strtotime($dt);
                                            $formattedDate = date('d M Y', $timestamp);
                                            $dayName = date('l', $timestamp);
                                            $isCurrentSelected = ($filterMode === 'single' && $singleDate === $dt);
                                        ?>
                                            <tr class="<?php echo $isCurrentSelected ? 'bg-slate-50/80 font-medium' : ''; ?>">
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-slate-900 text-xs font-mono">
                                                            <?php echo $formattedDate; ?>
                                                        </span>
                                                        <span class="text-[11px] text-slate-500">
                                                            (<?php echo $dayName; ?>)
                                                        </span>
                                                        <?php if ($idx === 0): ?>
                                                            <span class="shadcn-badge bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px]">
                                                                <i class="fa-solid fa-sparkles text-[9px]"></i> Latest
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($isCurrentSelected): ?>
                                                            <span class="shadcn-badge bg-slate-900 text-white border-slate-900 text-[10px]">
                                                                Selected
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="font-bold text-slate-900 text-sm">
                                                        <?php echo number_format($batch['total_count']); ?>
                                                    </span>
                                                    <span class="text-[10.5px] text-slate-400 block">items</span>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="shadcn-badge bg-slate-50 text-slate-700">
                                                            <i class="fa-solid fa-gem text-[10px] text-slate-400"></i>
                                                            <?php echo number_format($batch['jewellery_count']); ?> Jewellery
                                                        </span>
                                                        <span class="shadcn-badge bg-slate-50 text-slate-700">
                                                            <i class="fa-solid fa-vest text-[10px] text-slate-400"></i>
                                                            <?php echo number_format($batch['garment_count']); ?> Garments
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <!-- 1-Click CSV -->
                                                        <a href="index.php?controller=product&action=export&format=csv&mode=single&date=<?php echo urlencode($dt); ?>" class="shadcn-btn shadcn-btn-sm" title="Download CSV for this exact import date">
                                                            <i class="fa-solid fa-file-csv"></i> CSV
                                                        </a>
                                                        <!-- 1-Click Excel -->
                                                        <a href="index.php?controller=product&action=export&format=excel&mode=single&date=<?php echo urlencode($dt); ?>" class="shadcn-btn shadcn-btn-sm" title="Download Excel for this exact import date">
                                                            <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                                                        </a>
                                                        <!-- Filter & Preview -->
                                                        <a href="index.php?controller=product&action=exportPage&mode=single&date=<?php echo urlencode($dt); ?>" class="shadcn-btn shadcn-btn-sm text-slate-600 hover:text-slate-900" title="Load this batch in preview below">
                                                            <i class="fa-solid fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-8 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300"></i>
                                    <p>No product import dates found in database.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ==================== FILTERED PRODUCTS LIVE PREVIEW ==================== -->
                    <div class="shadcn-card">
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-table-list"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 uppercase tracking-wider">Filtered Products Live Preview</span>
                                    <p class="text-[11px] text-slate-400 font-normal">
                                        Showing <?php echo count($previewProducts); ?> of <?php echo number_format($totalFilteredCount); ?> total items for current selection
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500 font-medium">
                                    <?php echo $totalFilteredCount > 60 ? 'Preview limited to 60 items. Download exports all ' . number_format($totalFilteredCount) . ' items.' : 'All matching items displayed below'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-0 overflow-x-auto max-h-[460px] overflow-y-auto">
                            <?php if (!empty($previewProducts)): ?>
                                <table class="shadcn-table">
                                    <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-2xs">
                                        <tr>
                                            <th class="w-12 text-center bg-slate-50">Image</th>
                                            <th class="bg-slate-50">SKU Code</th>
                                            <th class="bg-slate-50">Product Name</th>
                                            <th class="bg-slate-50">Department</th>
                                            <th class="bg-slate-50">Category</th>
                                            <th class="text-right bg-slate-50">Rent Price</th>
                                            <th class="text-right bg-slate-50">Sales Price</th>
                                            <th>Date Added / Imported</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($previewProducts as $prod): 
                                            $img = $prod['main_image'] ?? '';
                                            if (!empty($img)) {
                                                $cleanImg = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads'], '', $img), '/');
                                                $imgUrl = "https://srishringarr.com/yn/uploads/" . $cleanImg;
                                            } else {
                                                $imgUrl = 'https://srishringarr.com/static/images/default.jpg';
                                            }
                                        ?>
                                            <tr>
                                                <td class="text-center">
                                                    <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($prod['code']); ?>" class="w-9 h-11 object-cover rounded border border-slate-200 bg-slate-100 mx-auto" onerror="this.src='https://placehold.co/40x50?text=No+Img'">
                                                </td>
                                                <td>
                                                    <span class="font-mono font-bold text-slate-900 text-xs">
                                                        <?php echo htmlspecialchars($prod['code']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="font-medium text-slate-800 line-clamp-1 max-w-xs" title="<?php echo htmlspecialchars($prod['name']); ?>">
                                                        <?php echo htmlspecialchars($prod['name']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="shadcn-badge uppercase text-[10px]">
                                                        <?php echo htmlspecialchars($prod['type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-slate-600 text-xs">
                                                        <?php echo htmlspecialchars($prod['category_name']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-right font-semibold text-slate-900">
                                                    ₹<?php echo number_format((float)($prod['rent_price'] ?? 0)); ?>
                                                </td>
                                                <td class="text-right text-slate-600">
                                                    ₹<?php echo number_format((float)($prod['sales_price'] ?? 0)); ?>
                                                </td>
                                                <td>
                                                    <span class="font-mono text-slate-500 text-[11px]">
                                                        <?php echo htmlspecialchars($prod['date_added']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-12 text-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-medium text-slate-600">No products match your current date or keyword filter.</p>
                                    <p class="text-slate-400 mt-1">Try selecting a different date from the Import History table above.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        function setFilterMode(mode) {
            document.getElementById('filter_mode_input').value = mode;

            const btnSingle = document.getElementById('mode_btn_single');
            const btnRange = document.getElementById('mode_btn_range');
            const singleCol = document.getElementById('single_date_col');
            const rangeStartCol = document.getElementById('range_start_col');
            const rangeEndCol = document.getElementById('range_end_col');

            if (mode === 'range') {
                btnSingle.classList.remove('active');
                btnRange.classList.add('active');

                singleCol.classList.add('hidden');
                rangeStartCol.classList.remove('hidden');
                rangeEndCol.classList.remove('hidden');
            } else {
                btnSingle.classList.add('active');
                btnRange.classList.remove('active');

                singleCol.classList.remove('hidden');
                rangeStartCol.classList.add('hidden');
                rangeEndCol.classList.add('hidden');
            }
        }

        function selectQuickDate(dateStr) {
            setFilterMode('single');
            document.getElementById('input_single_date').value = dateStr;
            document.getElementById('form_action_input').value = 'exportPage';
            document.getElementById('export_filter_form').submit();
        }

        function selectQuickRange(days) {
            setFilterMode('range');
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - days);

            const formatYMD = (d) => d.toISOString().split('T')[0];
            document.getElementById('input_start_date').value = formatYMD(start);
            document.getElementById('input_end_date').value = formatYMD(end);
            document.getElementById('form_action_input').value = 'exportPage';
            document.getElementById('export_filter_form').submit();
        }

        function selectAllHistory() {
            setFilterMode('single');
            document.getElementById('input_single_date').value = '';
            document.getElementById('input_start_date').value = '';
            document.getElementById('input_end_date').value = '';
            document.getElementById('form_action_input').value = 'exportPage';
            document.getElementById('export_filter_form').submit();
        }
    </script>
</body>
</html>
