<!DOCTYPE html>
<html lang="en">
<head>
    <title>Photo Downloader - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Slate / Zinc Theme) */
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
            background-color: #fafafa !important;
            color: #09090b !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Metric Cards */
        .shadcn-stat-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.15s ease;
        }
        .shadcn-stat-card:hover {
            border-color: #d4d4d8;
        }

        /* ShadCN Card Container */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
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

        /* ShadCN Buttons */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 14px;
            height: 34px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            cursor: pointer;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #09090b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            font-family: inherit;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        .shadcn-btn-sm {
            height: 28px;
            padding: 0 9px;
            font-size: 11.5px;
            border-radius: 5px;
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

        /* Neutral Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
            flex-shrink: 0;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-dot-success { background-color: #10b981; }
        .status-dot-primary { background-color: #09090b; }
        .status-dot-warning { background-color: #f59e0b; }

        /* Search input */
        .search-input-wrap {
            position: relative;
            width: 100%;
        }
        .search-input-wrap i.search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            font-size: 11.5px;
            pointer-events: none;
        }
        .search-input-wrap input {
            width: 100%;
            height: 32px;
            padding: 0 10px 0 30px;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: all 0.15s ease;
        }
        .search-input-wrap input:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }

        /* Radio Options Card */
        .radio-option-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.12s ease;
        }
        .radio-option-card:hover {
            border-color: #d4d4d8;
            background: #fafafa;
        }
        .radio-option-card.selected {
            border-color: #09090b;
            background: #fdfdfd;
            box-shadow: 0 0 0 1px #09090b;
        }

        /* Folder Tree Preview */
        .folder-tree {
            font-family: 'JetBrains Mono', 'Fira Code', Menlo, monospace;
            font-size: 11.5px;
            background: #09090b;
            color: #f4f4f5;
            border-radius: 6px;
            padding: 14px;
            line-height: 1.6;
            overflow-x: auto;
        }

        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }

        /* Floating Toast */
        #toast-box {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
        .toast-msg {
            background: #09090b;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 500;
            padding: 9px 15px;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: auto;
            animation: toastIn 0.15s ease forwards;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Photo Downloader';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Flash messages -->
                    <?php if (!empty($_GET['error'])): ?>
                        <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-center justify-between text-xs font-medium">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle text-rose-600"></i>
                                <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
                        </div>
                    <?php endif; ?>

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">Photo Downloader</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-success mr-1"></span>
                                    Category-Wise ZIP Export
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Download product images organized category-wise into a clean folder hierarchy ({Department}/{Category}/{SKU}/...).</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span id="savedTimestampBadge" class="text-xs text-zinc-500 bg-white border border-zinc-200 px-2.5 py-1.5 rounded-md">
                                <i class="far fa-clock text-zinc-400 mr-1"></i>
                                <span id="savedTimestampText">
                                    <?php echo !empty($settings['updated_at']) ? 'Config saved: ' . date('M j, Y g:i A', strtotime($settings['updated_at'])) : 'Default Configuration'; ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <?php
                        $selectedCats = $settings['selected_categories'] ?? [];
                        $stockStatus = $settings['stock_status'] ?? 'all';
                        $imageScope = $settings['image_scope'] ?? 'all';

                        $totalApparelCats = count($categories['Apparel']['children'] ?? []);
                        $totalJewelCats = count($categories['Jewellery']['children'] ?? []);
                        $totalAllCats = $totalApparelCats + $totalJewelCats;
                    ?>

                    <!-- Metrics / Summary Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Categories</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo number_format($totalAllCats); ?></span>
                                    <span class="text-xs text-zinc-400">catalog nodes</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Selected Categories</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span id="metricSelectedCount" class="text-xl font-semibold text-zinc-900"><?php echo count($selectedCats); ?></span>
                                    <span class="text-xs text-zinc-400">chosen for zip</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-check-square"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Estimated Products</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span id="metricEstProducts" class="text-xl font-semibold text-zinc-900 font-mono">--</span>
                                    <span class="text-xs text-zinc-400">items</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Estimated Photos</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span id="metricEstImages" class="text-xl font-semibold text-zinc-900 font-mono">--</span>
                                    <span class="text-xs text-zinc-400">in archive</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-images"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form / Layout Grid -->
                    <form id="downloaderForm" method="GET" action="index.php">
                        <input type="hidden" name="controller" value="photodownloader">
                        <input type="hidden" name="action" value="download">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                            <!-- ==================== LEFT COLUMN: CATEGORY SELECTION ==================== -->
                            <div class="lg:col-span-7 space-y-6">

                                <div class="shadcn-card">
                                    <div class="shadcn-card-header">
                                        <div>
                                            <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fas fa-folder-tree text-zinc-400 text-xs"></i>
                                                <span>1. Category Selection</span>
                                            </h2>
                                            <p class="text-xs text-zinc-500 mt-0.5">Select which product categories will be included in the photo download archive.</p>
                                        </div>

                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button type="button" onclick="selectCategoriesFilter('all')" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium px-2 py-1 rounded hover:bg-zinc-100 transition-colors">Select All</button>
                                            <span class="text-zinc-300">|</span>
                                            <button type="button" onclick="selectCategoriesFilter('apparel')" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium px-2 py-1 rounded hover:bg-zinc-100 transition-colors">All Apparel</button>
                                            <span class="text-zinc-300">|</span>
                                            <button type="button" onclick="selectCategoriesFilter('jewel')" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium px-2 py-1 rounded hover:bg-zinc-100 transition-colors">All Jewellery</button>
                                            <span class="text-zinc-300">|</span>
                                            <button type="button" onclick="selectCategoriesFilter('none')" class="text-xs text-rose-600 hover:text-rose-800 font-medium px-2 py-1 rounded hover:bg-rose-50 transition-colors">Clear All</button>
                                        </div>
                                    </div>

                                    <div class="p-4 border-b border-zinc-100 bg-zinc-50/50">
                                        <div class="search-input-wrap">
                                            <i class="fas fa-search search-icon"></i>
                                            <input type="text" id="catSearchInput" placeholder="Filter category by name (e.g. Evening Gowns, Earrings)..." autocomplete="off" onkeyup="filterCategoryList(this.value)">
                                        </div>
                                    </div>

                                    <div class="p-5 space-y-6">

                                        <!-- Department 1: Apparel -->
                                        <div class="dept-container" data-dept="apparel">
                                            <div class="flex items-center justify-between border-b border-zinc-200 pb-2.5 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="chkDeptApparel" class="w-4 h-4 accent-zinc-900 rounded cursor-pointer" onchange="toggleDeptCheckboxes('apparel', this.checked)">
                                                    <label for="chkDeptApparel" class="text-xs font-semibold text-zinc-900 cursor-pointer select-none flex items-center gap-1.5">
                                                        <i class="fas fa-tshirt text-zinc-500"></i>
                                                        <span>Apparel &amp; Garments</span>
                                                    </label>
                                                </div>
                                                <span class="shadcn-badge font-mono text-[10px]"><?php echo count($categories['Apparel']['children'] ?? []); ?> categories</span>
                                            </div>

                                            <div class="space-y-1 max-h-64 overflow-y-auto custom-scrollbar pr-1" id="apparelCatList">
                                                <?php if (!empty($categories['Apparel']['children'])): ?>
                                                    <?php foreach ($categories['Apparel']['children'] as $catKey => $catData): ?>
                                                        <?php $isChecked = in_array($catKey, $selectedCats); ?>
                                                        <div class="cat-item-row flex items-center justify-between p-2 rounded hover:bg-zinc-50 transition-colors group" data-name="<?php echo strtolower(htmlspecialchars($catData['name'])); ?>">
                                                            <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0 select-none">
                                                                <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox cat-apparel w-4 h-4 accent-zinc-900 rounded cursor-pointer" onchange="handleSelectionChange()">
                                                                <span class="text-xs text-zinc-800 group-hover:text-zinc-950 font-medium truncate"><?php echo htmlspecialchars($catData['name']); ?></span>
                                                            </label>
                                                            <div class="flex items-center gap-2">
                                                                <span class="shadcn-badge font-mono text-[10px] text-zinc-500 bg-zinc-50"><?php echo (int)$catData['count']; ?> items</span>
                                                                <a href="index.php?controller=photodownloader&action=download&categories[]=<?php echo urlencode($catKey); ?>&stock_status=<?php echo urlencode($stockStatus); ?>&image_scope=<?php echo urlencode($imageScope); ?>" 
                                                                   title="Download only <?php echo htmlspecialchars($catData['name']); ?>" 
                                                                   class="text-zinc-400 hover:text-zinc-900 text-xs px-1.5 py-0.5 rounded hover:bg-zinc-200 transition-colors">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-xs text-zinc-400 py-3 text-center">No apparel categories found.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Department 2: Jewellery -->
                                        <div class="dept-container" data-dept="jewel">
                                            <div class="flex items-center justify-between border-b border-zinc-200 pb-2.5 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" id="chkDeptJewel" class="w-4 h-4 accent-zinc-900 rounded cursor-pointer" onchange="toggleDeptCheckboxes('jewel', this.checked)">
                                                    <label for="chkDeptJewel" class="text-xs font-semibold text-zinc-900 cursor-pointer select-none flex items-center gap-1.5">
                                                        <i class="fas fa-gem text-zinc-500"></i>
                                                        <span>Jewellery Collection</span>
                                                    </label>
                                                </div>
                                                <span class="shadcn-badge font-mono text-[10px]"><?php echo count($categories['Jewellery']['children'] ?? []); ?> categories</span>
                                            </div>

                                            <div class="space-y-1 max-h-80 overflow-y-auto custom-scrollbar pr-1" id="jewelCatList">
                                                <?php if (!empty($categories['Jewellery']['children'])): ?>
                                                    <?php foreach ($categories['Jewellery']['children'] as $catKey => $catData): ?>
                                                        <?php 
                                                            $isChecked = in_array($catKey, $selectedCats);
                                                            $isSub = str_starts_with($catKey, 'jewel_child:');
                                                        ?>
                                                        <div class="cat-item-row flex items-center justify-between p-2 rounded hover:bg-zinc-50 transition-colors group <?php echo $isSub ? 'ml-4 bg-zinc-50/40' : ''; ?>" data-name="<?php echo strtolower(htmlspecialchars($catData['name'])); ?>">
                                                            <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0 select-none">
                                                                <?php if ($isSub): ?>
                                                                    <span class="text-zinc-300 font-mono text-xs select-none">└─</span>
                                                                <?php endif; ?>
                                                                <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox cat-jewel w-4 h-4 accent-zinc-900 rounded cursor-pointer" onchange="handleSelectionChange()">
                                                                <span class="text-xs <?php echo $isSub ? 'text-zinc-600 font-normal' : 'text-zinc-900 font-semibold'; ?> group-hover:text-zinc-950 truncate">
                                                                    <?php echo htmlspecialchars($catData['name']); ?>
                                                                </span>
                                                            </label>
                                                            <div class="flex items-center gap-2">
                                                                <span class="shadcn-badge font-mono text-[10px] text-zinc-500 bg-zinc-50"><?php echo (int)$catData['count']; ?> items</span>
                                                                <a href="index.php?controller=photodownloader&action=download&categories[]=<?php echo urlencode($catKey); ?>&stock_status=<?php echo urlencode($stockStatus); ?>&image_scope=<?php echo urlencode($imageScope); ?>" 
                                                                   title="Download only <?php echo htmlspecialchars($catData['name']); ?>" 
                                                                   class="text-zinc-400 hover:text-zinc-900 text-xs px-1.5 py-0.5 rounded hover:bg-zinc-200 transition-colors">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-xs text-zinc-400 py-3 text-center">No jewellery categories found.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <!-- ==================== RIGHT COLUMN: FILTERS & ACTIONS ==================== -->
                            <div class="lg:col-span-5 space-y-6">

                                <!-- Card 2: Stock & Image Filters -->
                                <div class="shadcn-card">
                                    <div class="shadcn-card-header">
                                        <div>
                                            <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fas fa-sliders text-zinc-400 text-xs"></i>
                                                <span>2. Stock &amp; Photo Options</span>
                                            </h2>
                                            <p class="text-xs text-zinc-500 mt-0.5">Filter products by stock status and decide whether to download main photo or all photos.</p>
                                        </div>
                                    </div>

                                    <div class="p-5 space-y-5">

                                        <!-- Stock Status Option -->
                                        <div>
                                            <label class="text-xs font-semibold text-zinc-900 block mb-2">
                                                Product Stock Availability:
                                            </label>
                                            <div class="space-y-2">
                                                <label class="radio-option-card <?php echo $stockStatus === 'all' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'stock_status', 'all')">
                                                    <input type="radio" name="stock_status" value="all" <?php echo $stockStatus === 'all' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                    <div>
                                                        <div class="text-xs font-semibold text-zinc-900">All Products</div>
                                                        <div class="text-[11px] text-zinc-500">Include every catalog product regardless of current POS stock level.</div>
                                                    </div>
                                                </label>

                                                <label class="radio-option-card <?php echo $stockStatus === 'available' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'stock_status', 'available')">
                                                    <input type="radio" name="stock_status" value="available" <?php echo $stockStatus === 'available' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                    <div>
                                                        <div class="text-xs font-semibold text-zinc-900 flex items-center gap-1.5">
                                                            <span>Available Products (In Stock)</span>
                                                            <span class="status-dot status-dot-success"></span>
                                                        </div>
                                                        <div class="text-[11px] text-zinc-500">Only download products that currently have POS inventory (quantity &gt; 0).</div>
                                                    </div>
                                                </label>

                                                <label class="radio-option-card <?php echo $stockStatus === 'outofstock' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'stock_status', 'outofstock')">
                                                    <input type="radio" name="stock_status" value="outofstock" <?php echo $stockStatus === 'outofstock' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                    <div>
                                                        <div class="text-xs font-semibold text-zinc-900 flex items-center gap-1.5">
                                                            <span>Out of Stock Products</span>
                                                            <span class="status-dot status-dot-warning"></span>
                                                        </div>
                                                        <div class="text-[11px] text-zinc-500">Only download products where POS stock is 0 or sold out.</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Image Scope Option -->
                                        <div class="pt-3 border-t border-zinc-100">
                                            <label class="text-xs font-semibold text-zinc-900 block mb-2">
                                                Image Download Scope:
                                            </label>
                                            <div class="space-y-2">
                                                <label class="radio-option-card <?php echo $imageScope === 'all' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'image_scope', 'all')">
                                                    <input type="radio" name="image_scope" value="all" <?php echo $imageScope === 'all' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                    <div>
                                                        <div class="text-xs font-semibold text-zinc-900 flex items-center gap-1.5">
                                                            <span>All Images (Recommended)</span>
                                                            <span class="shadcn-badge text-[10px]">Complete</span>
                                                        </div>
                                                        <div class="text-[11px] text-zinc-500">Downloads main hero image + all supplementary gallery angles for each product.</div>
                                                    </div>
                                                </label>

                                                <label class="radio-option-card <?php echo $imageScope === 'main' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'image_scope', 'main')">
                                                    <input type="radio" name="image_scope" value="main" <?php echo $imageScope === 'main' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                    <div>
                                                        <div class="text-xs font-semibold text-zinc-900">Main Image Only</div>
                                                        <div class="text-[11px] text-zinc-500">Downloads only the primary featured cover photo for each SKU (faster download).</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Folder Hierarchy Preview Box -->
                                        <div class="pt-3 border-t border-zinc-100">
                                            <span class="text-[11px] font-semibold text-zinc-600 block mb-1.5">
                                                <i class="fas fa-sitemap mr-1 text-zinc-400"></i> Resulting ZIP Directory Structure:
                                            </span>
                                            <div class="folder-tree">
📦 srishringarr_photos.zip<br>
├── 📁 Apparel/<br>
│   └── 📁 Evening Gowns/<br>
│       └── 📁 {sku}/<br>
│           ├── 🖼️ 00_main_image.png<br>
│           └── 🖼️ 01_other_image.png<br>
└── 📁 Jewellery/<br>
    └── 📁 Earrings/<br>
        └── 📁 {sku}/<br>
            └── 🖼️ 00_main_image.jpg
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="pt-4 border-t border-zinc-100 flex flex-col gap-2.5">
                                            <button type="submit" id="btnDownloadZip" class="shadcn-btn shadcn-btn-primary w-full py-2.5 h-10 text-sm font-semibold justify-center">
                                                <i class="fas fa-cloud-arrow-down" id="downloadIcon"></i>
                                                <span id="downloadBtnText">Download ZIP Archive</span>
                                            </button>

                                            <button type="button" id="btnSaveConfig" onclick="saveConfigurationAjax()" class="shadcn-btn w-full py-2 h-9 text-xs justify-center font-medium">
                                                <i class="fas fa-floppy-disk text-zinc-400" id="saveIcon"></i>
                                                <span>Save Configuration to Backend</span>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <!-- Tips / Notes Card -->
                                <div class="p-4 bg-zinc-100/60 border border-zinc-200/80 rounded-lg text-xs text-zinc-600 space-y-1.5">
                                    <div class="font-semibold text-zinc-800 flex items-center gap-1.5">
                                        <i class="fas fa-circle-info text-zinc-400"></i>
                                        <span>Pro Tips &amp; Persistence</span>
                                    </div>
                                    <p class="text-[11px] text-zinc-500 leading-relaxed">
                                        &bull; Selected categories and filter choices are saved in the backend and will be remembered the next time you visit.<br>
                                        &bull; Each product SKU folder keeps its images neatly numbered with the primary photo ranked first as <code class="bg-zinc-200/70 px-1 py-0.5 rounded text-zinc-800 font-mono text-[10px]">00_main_...</code>.
                                    </p>
                                </div>

                            </div>

                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    <!-- Floating Toast Container -->
    <div id="toast-box"></div>

    <script>
        let previewDebounceTimer = null;

        // Show Toast Notification
        function showToast(message, type = 'info') {
            const box = document.getElementById('toast-box');
            const toast = document.createElement('div');
            toast.className = 'toast-msg';
            
            let icon = '<i class="fas fa-check-circle text-emerald-400"></i>';
            if (type === 'error') icon = '<i class="fas fa-exclamation-triangle text-rose-400"></i>';
            if (type === 'info') icon = '<i class="fas fa-info-circle text-sky-400"></i>';

            toast.innerHTML = `${icon} <span>${message}</span>`;
            box.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                toast.style.transition = 'all 0.2s ease';
                setTimeout(() => toast.remove(), 200);
            }, 3000);
        }

        // Search category names in the list
        function filterCategoryList(term) {
            const query = term.toLowerCase().trim();
            const rows = document.querySelectorAll('.cat-item-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                if (!query || name.includes(query)) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Select All / Dept / None
        function selectCategoriesFilter(type) {
            const allCheckboxes = document.querySelectorAll('.cat-checkbox');

            if (type === 'all') {
                allCheckboxes.forEach(cb => cb.checked = true);
                document.getElementById('chkDeptApparel').checked = true;
                document.getElementById('chkDeptJewel').checked = true;
            } else if (type === 'none') {
                allCheckboxes.forEach(cb => cb.checked = false);
                document.getElementById('chkDeptApparel').checked = false;
                document.getElementById('chkDeptJewel').checked = false;
            } else if (type === 'apparel') {
                document.querySelectorAll('.cat-apparel').forEach(cb => cb.checked = true);
                document.getElementById('chkDeptApparel').checked = true;
            } else if (type === 'jewel') {
                document.querySelectorAll('.cat-jewel').forEach(cb => cb.checked = true);
                document.getElementById('chkDeptJewel').checked = true;
            }

            handleSelectionChange();
        }

        // Toggle Department checkboxes
        function toggleDeptCheckboxes(dept, isChecked) {
            const selector = dept === 'apparel' ? '.cat-apparel' : '.cat-jewel';
            document.querySelectorAll(selector).forEach(cb => cb.checked = isChecked);
            handleSelectionChange();
        }

        // Radio Option Card Click handler
        function selectRadio(cardElement, radioName, value) {
            const container = cardElement.closest('.space-y-2');
            container.querySelectorAll('.radio-option-card').forEach(c => c.classList.remove('selected'));
            cardElement.classList.add('selected');

            const radio = cardElement.querySelector(`input[name="${radioName}"]`);
            if (radio) {
                radio.checked = true;
                handleSelectionChange();
            }
        }

        // On selection change: update count badge and trigger debounced preview
        function handleSelectionChange() {
            const checkedBoxes = document.querySelectorAll('.cat-checkbox:checked');
            const count = checkedBoxes.length;
            document.getElementById('metricSelectedCount').textContent = count;

            // Debounced preview calculation
            clearTimeout(previewDebounceTimer);
            previewDebounceTimer = setTimeout(fetchPreviewMetrics, 400);
        }

        // Fetch estimated metrics from backend
        async function fetchPreviewMetrics() {
            const form = document.getElementById('downloaderForm');
            const formData = new FormData(form);

            const estProdBadge = document.getElementById('metricEstProducts');
            const estImgBadge = document.getElementById('metricEstImages');

            estProdBadge.textContent = '...';
            estImgBadge.textContent = '...';

            try {
                const params = new URLSearchParams();
                for (const [key, val] of formData.entries()) {
                    params.append(key, val);
                }

                const response = await fetch('index.php?controller=photodownloader&action=preview&' + params.toString());
                const res = await response.json();

                if (res.success && res.data) {
                    estProdBadge.textContent = res.data.total_products.toLocaleString();
                    estImgBadge.textContent = res.data.total_images.toLocaleString();
                } else {
                    estProdBadge.textContent = '--';
                    estImgBadge.textContent = '--';
                }
            } catch (err) {
                estProdBadge.textContent = '--';
                estImgBadge.textContent = '--';
            }
        }

        // Save Configuration to Backend via AJAX
        async function saveConfigurationAjax() {
            const btn = document.getElementById('btnSaveConfig');
            const icon = document.getElementById('saveIcon');
            const originalIconClass = icon.className;

            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin text-zinc-400';

            const form = document.getElementById('downloaderForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('index.php?controller=photodownloader&action=saveSettings', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.success) {
                    showToast('Configuration saved to backend successfully!');
                    if (res.settings && res.settings.updated_at) {
                        const dateObj = new Date(res.settings.updated_at.replace(/-/g, '/'));
                        document.getElementById('savedTimestampText').textContent = 'Config saved: ' + dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
                    }
                } else {
                    showToast(res.message || 'Failed to save configuration.', 'error');
                }
            } catch (err) {
                showToast('Network error while saving settings.', 'error');
            } finally {
                btn.disabled = false;
                icon.className = originalIconClass;
            }
        }

        // Handle Download Form Submit
        document.getElementById('downloaderForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.cat-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                showToast('Please select at least one category to download.', 'error');
                return;
            }

            const btn = document.getElementById('btnDownloadZip');
            const icon = document.getElementById('downloadIcon');
            const btnText = document.getElementById('downloadBtnText');

            icon.className = 'fas fa-spinner fa-spin';
            btnText.textContent = 'Generating ZIP Archive...';

            showToast('Preparing ZIP archive. The download will start momentarily...', 'info');

            // Reset button state after delay
            setTimeout(() => {
                icon.className = 'fas fa-cloud-arrow-down';
                btnText.textContent = 'Download ZIP Archive';
            }, 6000);
        });

        // Initialize preview on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetchPreviewMetrics();
        });
    </script>
</body>
</html>
