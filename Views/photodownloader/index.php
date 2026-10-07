<!DOCTYPE html>
<html lang="en">
<head>
    <title>Photo Downloader &amp; Duplicate Photos - Srishringarr</title>
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

        /* Tab Navigation Bar */
        .tab-nav-btn {
            background: transparent;
            color: #71717a;
            border: 1px solid transparent;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }
        .tab-nav-btn:hover {
            color: #09090b;
            background: #f4f4f5;
        }
        .tab-nav-btn.active {
            background: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
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
            padding: 0 10px;
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

        .shadcn-btn-danger {
            background: #ffffff !important;
            border-color: #fca5a5 !important;
            color: #dc2626 !important;
        }
        .shadcn-btn-danger:hover {
            background: #fef2f2 !important;
            border-color: #f87171 !important;
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
        .status-dot-danger  { background-color: #ef4444; }

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
            height: 34px;
            padding: 0 10px 0 32px;
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

        /* Duplicate Photo Card */
        .duplicate-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 14px;
            transition: all 0.15s ease;
        }
        .duplicate-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .img-thumb-preview {
            width: 72px;
            height: 72px;
            border-radius: 6px;
            object-fit: cover;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .img-thumb-preview:hover {
            transform: scale(1.05);
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
            $pageTitle = 'Photo Management';
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

                    <!-- Tab Navigation Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="inline-flex p-1 bg-zinc-200/70 rounded-lg border border-zinc-200">
                                <button type="button" id="tabNavDownloader" onclick="switchMainTab('downloader')" class="tab-nav-btn <?php echo ($activeTab !== 'duplicates') ? 'active' : ''; ?>">
                                    <i class="fas fa-cloud-arrow-down"></i>
                                    <span>Photo Downloader</span>
                                </button>
                                <button type="button" id="tabNavDuplicates" onclick="switchMainTab('duplicates')" class="tab-nav-btn <?php echo ($activeTab === 'duplicates') ? 'active' : ''; ?>">
                                    <i class="fas fa-clone"></i>
                                    <span>Duplicate Photos</span>
                                    <span class="shadcn-badge font-mono text-[10px] ml-1 bg-white text-zinc-900">Scanner</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span id="savedTimestampBadge" class="text-xs text-zinc-500 bg-white border border-zinc-200 px-2.5 py-1.5 rounded-md">
                                <i class="far fa-clock text-zinc-400 mr-1"></i>
                                <span id="savedTimestampText">
                                    <?php echo !empty($settings['updated_at']) ? 'Config saved: ' . date('M j, Y g:i A', strtotime($settings['updated_at'])) : 'Photo Downloader Active'; ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <?php
                        $selectedCats = $settings['selected_categories'] ?? [];
                        $stockStatus = $settings['stock_status'] ?? 'all';
                        $imageScope = $settings['image_scope'] ?? 'all';
                        $limitProducts = $settings['limit_products'] ?? 'all';
                        $compressImages = $settings['compress_images'] ?? '1';

                        $totalApparelCats = count($categories['Apparel']['children'] ?? []);
                        $totalJewelCats = count($categories['Jewellery']['children'] ?? []);
                        $totalAllCats = $totalApparelCats + $totalJewelCats;
                    ?>

                    <!-- ========================================================================= -->
                    <!-- ======================= TAB 1: PHOTO DOWNLOADER ========================= -->
                    <!-- ========================================================================= -->
                    <div id="tabContentDownloader" style="<?php echo ($activeTab === 'duplicates') ? 'display: none;' : ''; ?>">

                        <!-- Header Banner -->
                        <div class="mb-5">
                            <div class="flex items-center gap-2">
                                <h1 class="text-lg font-semibold text-zinc-900 tracking-tight">Category-Wise Photo Archive</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-success mr-1"></span>
                                    High-Speed ZIP Packaging
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Download product images organized category-wise ({Department}/{Category}/{SKU}_00_main_image.jpg). Large 50-60MB raw photos are automatically compressed on server into crisp 2K resolution (~350KB) for instant downloads without 504 timeouts.</p>
                        </div>

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
                        <form id="downloaderForm" onsubmit="event.preventDefault(); startBatchDownload();">

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                                <!-- LEFT COLUMN: CATEGORY SELECTION -->
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
                                                                    <button type="button" onclick="downloadSingleCategory('<?php echo htmlspecialchars($catKey); ?>')" 
                                                                       title="Download only <?php echo htmlspecialchars($catData['name']); ?>" 
                                                                       class="text-zinc-400 hover:text-zinc-900 text-xs px-1.5 py-0.5 rounded hover:bg-zinc-200 transition-colors">
                                                                        <i class="fas fa-download"></i>
                                                                    </button>
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
                                                            <?php $isChecked = in_array($catKey, $selectedCats); ?>
                                                            <div class="cat-item-row flex items-center justify-between p-2 rounded hover:bg-zinc-50 transition-colors group" data-name="<?php echo strtolower(htmlspecialchars($catData['name'])); ?>">
                                                                <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0 select-none">
                                                                    <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox cat-jewel w-4 h-4 accent-zinc-900 rounded cursor-pointer" onchange="handleSelectionChange()">
                                                                    <span class="text-xs text-zinc-800 group-hover:text-zinc-950 font-medium truncate"><?php echo htmlspecialchars($catData['name']); ?></span>
                                                                </label>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="shadcn-badge font-mono text-[10px] text-zinc-500 bg-zinc-50"><?php echo (int)$catData['count']; ?> items</span>
                                                                    <button type="button" onclick="downloadSingleCategory('<?php echo htmlspecialchars($catKey); ?>')" 
                                                                       title="Download only <?php echo htmlspecialchars($catData['name']); ?>" 
                                                                       class="text-zinc-400 hover:text-zinc-900 text-xs px-1.5 py-0.5 rounded hover:bg-zinc-200 transition-colors">
                                                                        <i class="fas fa-download"></i>
                                                                    </button>
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

                                <!-- RIGHT COLUMN: OPTIONS & DOWNLOAD TRIGGER -->
                                <div class="lg:col-span-5 space-y-6">

                                    <div class="shadcn-card">
                                        <div class="shadcn-card-header">
                                            <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                                <i class="fas fa-sliders text-zinc-400 text-xs"></i>
                                                <span>2. Downloader Options</span>
                                            </h2>
                                            <span class="shadcn-badge text-[10px]">Server Packager</span>
                                        </div>

                                        <div class="p-5 space-y-5">

                                            <!-- Compression Option (CRITICAL FIX FOR 504 TIMEOUT) -->
                                            <div class="p-3.5 bg-emerald-50/60 border border-emerald-200/80 rounded-lg">
                                                <div class="flex items-start gap-2.5">
                                                    <input type="checkbox" id="chkCompressImages" name="compress_images" value="1" <?php echo $compressImages === '1' ? 'checked' : ''; ?> class="accent-emerald-700 w-4 h-4 mt-0.5 cursor-pointer" onchange="handleSelectionChange()">
                                                    <div>
                                                        <label for="chkCompressImages" class="text-xs font-semibold text-emerald-950 cursor-pointer flex items-center gap-1.5">
                                                            <span>Server Image Compression &amp; Speed Boost</span>
                                                            <span class="shadcn-badge text-[9px] bg-emerald-100 text-emerald-800 border-emerald-300">Recommended</span>
                                                        </label>
                                                        <p class="text-[11px] text-emerald-800/90 mt-1 leading-relaxed">
                                                            Compresses large 50-60MB camera photos to high-res 2K (~350KB) on the server before packing into ZIP. <strong>Fixes 504 Gateway Timeouts</strong> and reduces ZIP size by 99% for 100x faster downloads.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

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

                                            <!-- Download Limit Option -->
                                            <div class="pt-3 border-t border-zinc-100">
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="text-xs font-semibold text-zinc-900 block">
                                                        Download Limit / Sample Size:
                                                    </label>
                                                    <span class="shadcn-badge font-mono text-[10px]">Testing Option</span>
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="radio-option-card <?php echo $limitProducts === 'all' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'limit_products', 'all')">
                                                        <input type="radio" name="limit_products" value="all" <?php echo $limitProducts === 'all' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                        <div>
                                                            <div class="text-xs font-semibold text-zinc-900">All Products (Full Export)</div>
                                                            <div class="text-[11px] text-zinc-500">Download every product in each selected category.</div>
                                                        </div>
                                                    </label>

                                                    <label class="radio-option-card <?php echo $limitProducts === '10' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'limit_products', '10')">
                                                        <input type="radio" name="limit_products" value="10" <?php echo $limitProducts === '10' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                        <div>
                                                            <div class="text-xs font-semibold text-zinc-900 flex items-center gap-1.5">
                                                                <span>Max 10 Products (Quick Test)</span>
                                                                <span class="shadcn-badge text-[10px] bg-emerald-50 text-emerald-700 border-emerald-200">Fast Sample</span>
                                                            </div>
                                                            <div class="text-[11px] text-zinc-500">Downloads max 10 products per category for quick verification.</div>
                                                        </div>
                                                    </label>

                                                    <label class="radio-option-card <?php echo $limitProducts === '25' ? 'selected' : ''; ?>" onclick="selectRadio(this, 'limit_products', '25')">
                                                        <input type="radio" name="limit_products" value="25" <?php echo $limitProducts === '25' ? 'checked' : ''; ?> class="accent-zinc-900 mt-0.5" onchange="handleSelectionChange()">
                                                        <div>
                                                            <div class="text-xs font-semibold text-zinc-900">Max 25 Products per Category</div>
                                                            <div class="text-[11px] text-zinc-500">Moderate sample size for testing larger batch packages.</div>
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
📦 srishringarr_photos.zip
├── 📁 Apparel/
│   └── 📁 Evening Gowns/
│       ├── 🖼️ {sku}_00_main_image.jpg
│       └── 🖼️ {sku}_01_other_image.jpg
└── 📁 Jewellery/
    └── 📁 Earrings/
        └── 🖼️ {sku}_00_main_image.jpg
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="pt-4 border-t border-zinc-100 flex flex-col gap-2.5">
                                                <button type="button" id="btnDownloadZip" onclick="startBatchDownload()" class="shadcn-btn shadcn-btn-primary w-full py-2.5 h-10 text-sm font-semibold justify-center">
                                                    <i class="fas fa-cloud-arrow-down" id="downloadIcon"></i>
                                                    <span id="downloadBtnText">Create &amp; Download ZIP Archive</span>
                                                </button>

                                                <button type="button" id="btnSaveConfig" onclick="saveConfigurationAjax()" class="shadcn-btn w-full py-2 h-9 text-xs justify-center font-medium">
                                                    <i class="fas fa-floppy-disk text-zinc-400" id="saveIcon"></i>
                                                    <span>Save Configuration to Backend</span>
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </form>

                    </div>

                    <!-- ========================================================================= -->
                    <!-- ======================= TAB 2: DUPLICATE PHOTOS ========================= -->
                    <!-- ========================================================================= -->
                    <div id="tabContentDuplicates" style="<?php echo ($activeTab !== 'duplicates') ? 'display: none;' : ''; ?>">

                        <!-- Corrupted Text Records Alert Banner -->
                        <div id="corruptRecordsBanner" class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg flex items-center justify-between text-xs" style="display: none;">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-triangle-exclamation text-amber-600 text-base"></i>
                                <div>
                                    <div class="font-semibold text-amber-950">Corrupted Text Entries Detected in Database</div>
                                    <div class="text-[11px] text-amber-800 mt-0.5">Found <strong id="corruptCountText">0</strong> non-image text records (such as <code>/4.Featuring attractive designs</code>) in <code>product_images_new</code> table.</div>
                                </div>
                            </div>
                            <button type="button" id="btnPurgeCorrupt" onclick="purgeCorruptedRecords()" class="shadcn-btn shadcn-btn-sm text-xs bg-amber-600 hover:bg-amber-700 text-white font-medium flex-shrink-0">
                                <i class="fas fa-broom mr-1"></i> Purge From Database
                            </button>
                        </div>

                        <!-- Header Banner -->
                        <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-lg font-semibold text-zinc-900 tracking-tight">Duplicate &amp; Unreferenced Photo Manager</h1>
                                    <span class="shadcn-badge font-mono text-[11px]">
                                        <span class="status-dot status-dot-warning mr-1"></span>
                                        Catalog Deduplication
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-500 mt-1">Scan catalog photos category-wise to find duplicate image entries, or scan server disk for unreferenced photos whose reference is not found in <code>product_images_new</code> table.</p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <button type="button" onclick="exportDuplicatesCsv()" class="shadcn-btn shadcn-btn-sm text-xs">
                                    <i class="fas fa-file-csv text-zinc-500 mr-1"></i>
                                    <span>Export CSV Report</span>
                                </button>
                                <button type="button" onclick="confirmDeduplicateCategory()" id="btnDeduplicateCat" class="shadcn-btn shadcn-btn-sm shadcn-btn-danger text-xs">
                                    <i class="fas fa-trash-can mr-1"></i>
                                    <span>Deduplicate Current Category</span>
                                </button>
                            </div>
                        </div>

                        <!-- Sub-View Switcher -->
                        <div class="flex items-center gap-2 mb-4">
                            <button type="button" id="subTabDupes" onclick="switchDupeView('catalog')" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary text-xs">
                                <i class="fas fa-clone mr-1"></i> Catalog Duplicate Photos
                            </button>
                            <button type="button" id="subTabUnreferenced" onclick="switchDupeView('unreferenced')" class="shadcn-btn shadcn-btn-sm text-xs">
                                <i class="fas fa-file-circle-question mr-1"></i> Unreferenced Server Photos (No DB Reference)
                            </button>
                        </div>

                        <!-- ==================== SUB-VIEW 1: CATALOG DUPLICATE PHOTOS ==================== -->
                        <div id="subViewCatalogDupes">
                            <!-- Duplicate Filters Card -->
                            <div class="shadcn-card mb-6">
                                <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                    <!-- Category Selector -->
                                    <div class="md:col-span-5">
                                        <label class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Category Filter</label>
                                        <select id="dupeCategorySelect" onchange="fetchDuplicates(1)" class="w-full h-9 px-3 border border-zinc-200 rounded-md text-xs bg-white text-zinc-900 outline-none focus:border-zinc-900">
                                            <option value="all">-- All Categories (Full Storefront) --</option>
                                            <optgroup label="Apparel &amp; Garments">
                                                <?php foreach (($categories['Apparel']['children'] ?? []) as $cKey => $cItem): ?>
                                                    <option value="<?php echo htmlspecialchars($cKey); ?>"><?php echo htmlspecialchars($cItem['name']); ?> (<?php echo (int)$cItem['count']; ?> items)</option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Jewellery">
                                                <?php foreach (($categories['Jewellery']['children'] ?? []) as $cKey => $cItem): ?>
                                                    <option value="<?php echo htmlspecialchars($cKey); ?>"><?php echo htmlspecialchars($cItem['name']); ?> (<?php echo (int)$cItem['count']; ?> items)</option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <!-- Duplicate Type Filter -->
                                    <div class="md:col-span-4">
                                        <label class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Duplicate Type</label>
                                        <select id="dupeTypeSelect" onchange="fetchDuplicates(1)" class="w-full h-9 px-3 border border-zinc-200 rounded-md text-xs bg-white text-zinc-900 outline-none focus:border-zinc-900">
                                            <option value="all">All Real Duplicate Photos (Images Only)</option>
                                            <option value="multi_sku">Shared Across Multiple SKUs (Different Products)</option>
                                            <option value="single_sku_repeated">Repeated Duplicates on Same SKU (Uploaded Multiple Times)</option>
                                        </select>
                                    </div>

                                    <!-- Search Box -->
                                    <div class="md:col-span-3">
                                        <label class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Search SKU / Image</label>
                                        <div class="search-input-wrap">
                                            <i class="fas fa-search search-icon"></i>
                                            <input type="text" id="dupeSearchInput" placeholder="Filter by SKU or image..." oninput="debounceDupeSearch()">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Duplicate Summary KPIs -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                                <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Duplicate Image Groups</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="dupeKpiGroups" class="text-xl font-semibold text-zinc-900 font-mono">--</span>
                                        <span class="text-xs text-zinc-400">unique duplicate photos</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                    <i class="fas fa-clone"></i>
                                </div>
                            </div>

                            <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Redundant Extra Records</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="dupeKpiRedundant" class="text-xl font-semibold text-rose-600 font-mono">--</span>
                                        <span class="text-xs text-zinc-400">extra rows in DB</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 text-xs">
                                    <i class="fas fa-trash-can"></i>
                                </div>
                            </div>

                            <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Current Scope</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="dupeKpiScope" class="text-sm font-semibold text-zinc-900 truncate max-w-[200px]">All Categories</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                    <i class="fas fa-filter"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Duplicate Photos Listing Area -->
                        <div class="shadcn-card">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                        <i class="fas fa-images text-zinc-400 text-xs"></i>
                                        <span>Duplicate Photos Catalog</span>
                                    </h2>
                                    <span id="dupeResultsCountBadge" class="shadcn-badge font-mono text-[10px]">Loading...</span>
                                </div>

                                <div class="flex items-center gap-2" id="dupePaginationControls">
                                    <!-- Dynamic pagination populated by JS -->
                                </div>
                            </div>

                            <!-- List Container -->
                            <div id="dupeListContainer" class="p-5 space-y-4 min-h-[220px]">
                                <!-- Injected dynamically via fetchDuplicates() -->
                            </div>
                        </div>
                    </div>

                    <!-- ==================== SUB-VIEW 2: UNREFERENCED SERVER PHOTOS ==================== -->
                    <div id="subViewUnreferenced" style="display: none;">
                        <div class="shadcn-card mb-6">
                            <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <label class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Server Upload Folder</label>
                                        <input type="text" id="unreferencedFolderInput" value="2026/08" placeholder="e.g. 2026/08 or 2026/10" class="h-9 px-3 border border-zinc-200 rounded-md text-xs bg-white text-zinc-900 outline-none focus:border-zinc-900 w-44 font-mono">
                                    </div>
                                    <div class="pt-4">
                                        <button type="button" onclick="fetchUnreferencedPhotos()" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary text-xs">
                                            <i class="fas fa-magnifying-glass mr-1"></i> Scan Folder
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-zinc-500">
                                    Scans physical files on server disk and flags photos whose filename is <strong>NOT found in <code>product_images_new</code> table</strong>.
                                </div>
                            </div>
                        </div>

                        <!-- Unreferenced KPIs -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                            <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Files Scanned</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="unrefKpiScanned" class="text-xl font-semibold text-zinc-900 font-mono">--</span>
                                        <span class="text-xs text-zinc-400">files on disk</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                            </div>

                            <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Unreferenced Photos</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="unrefKpiCount" class="text-xl font-semibold text-rose-600 font-mono">--</span>
                                        <span class="text-xs text-zinc-400">no DB reference</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 text-xs">
                                    <i class="fas fa-file-circle-xmark"></i>
                                </div>
                            </div>

                            <div class="shadcn-stat-card">
                                <div>
                                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Wasted Storage</span>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span id="unrefKpiSize" class="text-xl font-semibold text-amber-600 font-mono">--</span>
                                        <span class="text-xs text-zinc-400">MB on server</span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-xs">
                                    <i class="fas fa-hard-drive"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Unreferenced Photos List -->
                        <div class="shadcn-card">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                        <i class="fas fa-file-circle-question text-zinc-400 text-xs"></i>
                                        <span>Orphan Server Photos (Reference Not Found in DB)</span>
                                    </h2>
                                    <span id="unrefResultsCountBadge" class="shadcn-badge font-mono text-[10px]">Ready to scan</span>
                                </div>
                            </div>

                            <div id="unrefListContainer" class="p-5 space-y-4 min-h-[220px]">
                                <div class="py-12 text-center text-zinc-400">
                                    <i class="fas fa-folder-magnifying-glass text-2xl text-zinc-300 mb-2"></i>
                                    <p class="text-xs">Click "Scan Folder" above to inspect server photos unreferenced in <code>product_images_new</code>.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                </div>
            </main>
        </div>
    </div>

    <!-- ==================== REAL-TIME DOWNLOAD PROGRESS MODAL ==================== -->
    <div id="downloadModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" style="display:none;">
        <div class="bg-white rounded-xl max-w-md w-full shadow-2xl border border-zinc-200 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="p-5 border-b border-zinc-100 flex items-center gap-3 bg-zinc-50/50">
                <div class="w-9 h-9 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-sm flex-shrink-0" id="modalHeaderIconWrap">
                    <i class="fas fa-box-archive" id="modalHeaderIcon"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-zinc-900 tracking-tight" id="modalTitle">Packaging Photo Archive</h3>
                    <p class="text-xs text-zinc-500 mt-0.5 truncate" id="modalSubtitle">Collecting images and building ZIP structure...</p>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4">
                <!-- Percentage & Stage label -->
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-zinc-800" id="modalStageText">Starting download session...</span>
                    <span class="shadcn-badge font-mono text-xs font-semibold" id="modalPercentBadge">0%</span>
                </div>

                <!-- Animated Progress Bar -->
                <div class="w-full bg-zinc-100 h-2.5 rounded-full overflow-hidden border border-zinc-200">
                    <div id="modalProgressBar" class="bg-zinc-900 h-full w-0 transition-all duration-200 rounded-full"></div>
                </div>

                <!-- Live Metrics Counters -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg text-center">
                        <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Products Processed</span>
                        <span id="modalProcessedCount" class="text-xs font-semibold text-zinc-900 font-mono mt-0.5 block">0 / 0</span>
                    </div>
                    <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg text-center">
                        <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Photos Packed</span>
                        <span id="modalPhotosPackedCount" class="text-xs font-semibold text-zinc-900 font-mono mt-0.5 block">0 photos</span>
                    </div>
                </div>

                <!-- Live ZIP File Size -->
                <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-medium flex items-center gap-1.5">
                        <i class="fas fa-file-zipper text-zinc-400"></i> Server ZIP Size:
                    </span>
                    <span id="modalZipSize" class="font-mono font-semibold text-zinc-900">0.0 MB</span>
                </div>

                <!-- Current Item Status -->
                <div class="p-3 bg-zinc-100/60 rounded-lg border border-zinc-200/60 text-xs">
                    <span class="text-zinc-400 block text-[10px] uppercase tracking-wider font-semibold">Current Batch</span>
                    <div id="modalCurrentItemLabel" class="text-zinc-700 font-medium truncate mt-0.5">Initializing packaging pipeline...</div>
                </div>

                <!-- Retry Notification banner (hidden by default) -->
                <div id="modalRetryNotice" class="hidden p-2.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-md text-xs flex items-center gap-2">
                    <i class="fas fa-arrows-rotate fa-spin text-amber-600"></i>
                    <span id="modalRetryText">Server busy, retrying batch...</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-zinc-100 bg-zinc-50 flex items-center justify-between gap-3">
                <button type="button" id="btnCancelDownload" onclick="cancelInteractiveDownload()" class="shadcn-btn shadcn-btn-sm text-xs text-zinc-600 hover:text-rose-600">
                    <i class="fas fa-xmark text-zinc-400 mr-1"></i> Cancel
                </button>

                <div class="flex items-center gap-2">
                    <a id="btnDirectDownloadLink" href="#" style="display:none;" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary text-xs">
                        <i class="fas fa-download mr-1"></i> Download ZIP
                    </a>
                    <button type="button" id="btnCloseModal" onclick="closeDownloadModal()" style="display:none;" class="shadcn-btn shadcn-btn-sm text-xs">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== IMAGE LIGHTBOX MODAL ==================== -->
    <div id="imageLightboxModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display:none;" onclick="closeImageLightbox()">
        <div class="relative max-w-3xl max-h-[90vh] bg-zinc-900 rounded-xl overflow-hidden shadow-2xl p-2 flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="w-full flex items-center justify-between p-2 text-white/80 border-b border-zinc-800 mb-2">
                <span id="lightboxTitle" class="text-xs font-mono truncate max-w-md text-zinc-300">Image Preview</span>
                <button type="button" onclick="closeImageLightbox()" class="text-zinc-400 hover:text-white text-lg p-1">&times;</button>
            </div>
            <img id="lightboxImg" src="" alt="Preview" class="max-h-[75vh] max-w-full object-contain rounded">
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-box"></div>

    <script>
        // Tab switching
        function switchMainTab(tabName) {
            const btnDownloader = document.getElementById('tabNavDownloader');
            const btnDuplicates = document.getElementById('tabNavDuplicates');
            const contentDownloader = document.getElementById('tabContentDownloader');
            const contentDuplicates = document.getElementById('tabContentDuplicates');

            if (tabName === 'duplicates') {
                btnDuplicates.classList.add('active');
                btnDownloader.classList.remove('active');
                contentDuplicates.style.display = 'block';
                contentDownloader.style.display = 'none';

                // Update URL query state without full reload
                const url = new URL(window.location);
                url.searchParams.set('tab', 'duplicates');
                window.history.replaceState({}, '', url);

                // Fetch duplicates if not loaded yet
                fetchDuplicates(currentDupePage);
            } else {
                btnDownloader.classList.add('active');
                btnDuplicates.classList.remove('active');
                contentDownloader.style.display = 'block';
                contentDuplicates.style.display = 'none';

                const url = new URL(window.location);
                url.searchParams.delete('tab');
                window.history.replaceState({}, '', url);
            }
        }

        // Lightbox
        function openImageLightbox(url, title) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxTitle').textContent = title || 'Image Preview';
            document.getElementById('imageLightboxModal').style.display = 'flex';
        }
        function closeImageLightbox() {
            document.getElementById('imageLightboxModal').style.display = 'none';
        }

        // Toast feedback
        function showToast(message, type = 'info') {
            const box = document.getElementById('toast-box');
            const toast = document.createElement('div');
            toast.className = 'toast-msg';

            let icon = 'fas fa-check-circle text-emerald-400';
            if (type === 'error') icon = 'fas fa-exclamation-circle text-rose-400';
            if (type === 'warning') icon = 'fas fa-triangle-exclamation text-amber-400';

            toast.innerHTML = `<i class="${icon}"></i> <span>${message}</span>`;
            box.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                toast.style.transition = 'all 0.2s ease';
                setTimeout(() => toast.remove(), 200);
            }, 3500);
        }

        // Copy text to clipboard
        function copyToClipboard(text, msg = 'Copied to clipboard!') {
            navigator.clipboard.writeText(text).then(() => {
                showToast(msg);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
                showToast(msg);
            });
        }

        // ==================== PHOTO DOWNLOADER LOGIC ====================
        let previewDebounceTimer = null;
        let activeJobId = null;
        let isDownloadCancelled = false;

        function filterCategoryList(query) {
            const cleanQ = query.trim().toLowerCase();
            const rows = document.querySelectorAll('.cat-item-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                row.style.display = (cleanQ === '' || name.includes(cleanQ)) ? 'flex' : 'none';
            });
        }

        function toggleDeptCheckboxes(dept, isChecked) {
            const cls = (dept === 'apparel') ? '.cat-apparel' : '.cat-jewel';
            const checkboxes = document.querySelectorAll(cls);
            checkboxes.forEach(cb => {
                if (cb.closest('.cat-item-row').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            handleSelectionChange();
        }

        function selectCategoriesFilter(scope) {
            const allCheckboxes = document.querySelectorAll('.cat-checkbox');
            const chkApparel = document.getElementById('chkDeptApparel');
            const chkJewel = document.getElementById('chkDeptJewel');

            if (scope === 'all') {
                allCheckboxes.forEach(cb => cb.checked = true);
                if (chkApparel) chkApparel.checked = true;
                if (chkJewel) chkJewel.checked = true;
            } else if (scope === 'none') {
                allCheckboxes.forEach(cb => cb.checked = false);
                if (chkApparel) chkApparel.checked = false;
                if (chkJewel) chkJewel.checked = false;
            } else if (scope === 'apparel') {
                document.querySelectorAll('.cat-apparel').forEach(cb => cb.checked = true);
                document.querySelectorAll('.cat-jewel').forEach(cb => cb.checked = false);
                if (chkApparel) chkApparel.checked = true;
                if (chkJewel) chkJewel.checked = false;
            } else if (scope === 'jewel') {
                document.querySelectorAll('.cat-apparel').forEach(cb => cb.checked = false);
                document.querySelectorAll('.cat-jewel').forEach(cb => cb.checked = true);
                if (chkApparel) chkApparel.checked = false;
                if (chkJewel) chkJewel.checked = true;
            }
            handleSelectionChange();
        }

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

        let previewAbortController = null;

        function handleSelectionChange() {
            const checkedBoxes = document.querySelectorAll('.cat-checkbox:checked');
            const count = checkedBoxes.length;
            document.getElementById('metricSelectedCount').textContent = count;

            clearTimeout(previewDebounceTimer);
            previewDebounceTimer = setTimeout(fetchPreviewMetrics, 450);
        }

        async function fetchPreviewMetrics() {
            if (previewAbortController) {
                try { previewAbortController.abort(); } catch(e) {}
            }
            previewAbortController = new AbortController();

            const form = document.getElementById('downloaderForm');
            const formData = new FormData(form);

            const estProdBadge = document.getElementById('metricEstProducts');
            const estImgBadge = document.getElementById('metricEstImages');

            estProdBadge.textContent = '...';
            estImgBadge.textContent = '...';

            try {
                const params = new URLSearchParams();
                for (const [key, val] of formData.entries()) {
                    if (key !== 'controller' && key !== 'action') {
                        params.append(key, val);
                    }
                }

                const response = await fetch('index.php?controller=photodownloader&action=preview&' + params.toString(), {
                    signal: previewAbortController.signal
                });
                const res = await response.json();

                if (res.success && res.data) {
                    estProdBadge.textContent = res.data.total_products.toLocaleString();
                    estImgBadge.textContent = res.data.total_images.toLocaleString();
                } else {
                    estProdBadge.textContent = '--';
                    estImgBadge.textContent = '--';
                }
            } catch (err) {
                if (err.name === 'AbortError') return;
                estProdBadge.textContent = '--';
                estImgBadge.textContent = '--';
            }
        }

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
                    showToast('Configuration saved successfully!');
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

        function openDownloadModal() {
            const modal = document.getElementById('downloadModal');
            modal.style.display = 'flex';
            document.getElementById('btnCancelDownload').style.display = 'inline-flex';
            document.getElementById('btnCloseModal').style.display = 'none';
            document.getElementById('btnDirectDownloadLink').style.display = 'none';
            document.getElementById('modalRetryNotice').classList.add('hidden');
            document.getElementById('modalTitle').textContent = 'Packaging Photo Archive';
            document.getElementById('modalSubtitle').textContent = 'Compressing and building ZIP structure on server...';
            document.getElementById('modalHeaderIconWrap').className = 'w-9 h-9 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-sm flex-shrink-0';
            document.getElementById('modalHeaderIcon').className = 'fas fa-box-archive fa-spin';
        }

        function closeDownloadModal() {
            const modal = document.getElementById('downloadModal');
            modal.style.display = 'none';
        }

        function updateModalState(percent, stage, processedText, packedCount, currentItem, zipSizeMb) {
            document.getElementById('modalProgressBar').style.width = percent + '%';
            document.getElementById('modalPercentBadge').textContent = percent + '%';
            if (stage) document.getElementById('modalStageText').textContent = stage;
            if (processedText) document.getElementById('modalProcessedCount').textContent = processedText;
            if (packedCount !== null && packedCount !== undefined) document.getElementById('modalPhotosPackedCount').textContent = packedCount.toLocaleString() + ' photos';
            if (currentItem) document.getElementById('modalCurrentItemLabel').textContent = currentItem;
            if (zipSizeMb !== undefined && zipSizeMb !== null) {
                document.getElementById('modalZipSize').textContent = zipSizeMb + ' MB';
            }
        }

        async function cancelInteractiveDownload() {
            isDownloadCancelled = true;
            document.getElementById('modalStageText').textContent = 'Cancelling download...';
            if (activeJobId) {
                const fd = new FormData();
                fd.append('job_id', activeJobId);
                await fetch('index.php?controller=photodownloader&action=cancelDownloadJob', { method: 'POST', body: fd }).catch(() => {});
            }
            closeDownloadModal();
            showToast('Download cancelled.', 'info');
        }

        function downloadSingleCategory(catKey) {
            const allCheckboxes = document.querySelectorAll('.cat-checkbox');
            allCheckboxes.forEach(cb => {
                cb.checked = (cb.value === catKey);
            });
            const chkApparel = document.getElementById('chkDeptApparel');
            const chkJewel = document.getElementById('chkDeptJewel');
            if (chkApparel) chkApparel.checked = false;
            if (chkJewel) chkJewel.checked = false;
            handleSelectionChange();
            startBatchDownload();
        }

        // High-Speed Chunked Batch Download Engine with Auto-Retry
        async function startBatchDownload() {
            const checkedBoxes = document.querySelectorAll('.cat-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showToast('Please select at least one category to download.', 'error');
                return;
            }

            openDownloadModal();
            isDownloadCancelled = false;
            activeJobId = null;

            updateModalState(2, 'Initializing packaging queue on server...', '0 / 0', 0, 'Scanning catalog database...', 0.0);

            const form = document.getElementById('downloaderForm');
            const formData = new FormData(form);

            try {
                // 1. Initialize job
                const startRes = await fetch('index.php?controller=photodownloader&action=startDownloadJob', {
                    method: 'POST',
                    body: formData
                });
                const startData = await startRes.json();

                if (!startData.success) {
                    closeDownloadModal();
                    showToast(startData.message || 'Failed to start download job.', 'error');
                    return;
                }

                activeJobId = startData.job_id;
                const totalChunks = startData.total_chunks;
                const totalProducts = startData.total_products;

                updateModalState(4, 'Compressing & packing photos into ZIP...', `0 / ${totalProducts}`, 0, 'Starting batch processing...', 0.0);

                // 2. Process chunks with resilience and auto-retry
                for (let i = 0; i < totalChunks; i++) {
                    if (isDownloadCancelled) break;

                    const chunkForm = new FormData();
                    chunkForm.append('job_id', activeJobId);
                    chunkForm.append('chunk_index', i);

                    let chunkData = null;
                    let retries = 0;
                    const maxRetries = 3;

                    while (retries < maxRetries && !isDownloadCancelled) {
                        try {
                            const chunkRes = await fetch('index.php?controller=photodownloader&action=processDownloadChunk', {
                                method: 'POST',
                                body: chunkForm
                            });

                            if (!chunkRes.ok) {
                                throw new Error(`Server returned HTTP ${chunkRes.status}`);
                            }

                            chunkData = await chunkRes.json();
                            if (!chunkData.success) {
                                throw new Error(chunkData.message || 'Chunk error');
                            }

                            // Success
                            document.getElementById('modalRetryNotice').classList.add('hidden');
                            break;
                        } catch (err) {
                            retries++;
                            if (retries >= maxRetries) {
                                throw new Error(`Batch ${i + 1}/${totalChunks} failed after 3 attempts: ${err.message}`);
                            }
                            // Show retry notice in modal
                            const retryNotice = document.getElementById('modalRetryNotice');
                            retryNotice.classList.remove('hidden');
                            document.getElementById('modalRetryText').textContent = `Retrying batch ${i + 1} of ${totalChunks} (attempt ${retries + 1}/${maxRetries})...`;
                            await new Promise(r => setTimeout(r, 2000));
                        }
                    }

                    if (!chunkData) break;

                    const pct = Math.max(5, chunkData.percent);
                    updateModalState(
                        pct,
                        `Processing: ${chunkData.processed_count} / ${chunkData.total_products} products`,
                        `${chunkData.processed_count} / ${chunkData.total_products}`,
                        chunkData.photos_packed,
                        `Packed: ${chunkData.current_label}`,
                        chunkData.zip_size_mb
                    );

                    if (chunkData.is_complete) break;
                }

                if (!isDownloadCancelled) {
                    // Complete state
                    updateModalState(100, 'ZIP Archive Complete! Download starting...', `${totalProducts} / ${totalProducts}`, null, 'Archive packaged successfully on server.');
                    
                    document.getElementById('modalTitle').textContent = 'ZIP Archive Ready!';
                    document.getElementById('modalSubtitle').textContent = 'Your photos are packaged into the ZIP archive.';
                    document.getElementById('modalHeaderIconWrap').className = 'w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm flex-shrink-0';
                    document.getElementById('modalHeaderIcon').className = 'fas fa-check';

                    const downloadUrl = `index.php?controller=photodownloader&action=serveJobZip&job_id=${activeJobId}`;
                    const directBtn = document.getElementById('btnDirectDownloadLink');
                    directBtn.href = downloadUrl;
                    directBtn.style.display = 'inline-flex';

                    document.getElementById('btnCancelDownload').style.display = 'none';
                    document.getElementById('btnCloseModal').style.display = 'inline-flex';

                    // Trigger direct browser download
                    window.location.href = downloadUrl;
                    showToast('ZIP archive created! Downloading now...', 'info');
                }

            } catch (err) {
                if (!isDownloadCancelled) {
                    closeDownloadModal();
                    showToast('Packaging error: ' + err.message, 'error');
                }
            }
        }

        // ==================== DUPLICATE PHOTOS LOGIC ====================
        let currentDupePage = 1;
        let dupeSearchTimer = null;

        function debounceDupeSearch() {
            clearTimeout(dupeSearchTimer);
            dupeSearchTimer = setTimeout(() => {
                fetchDuplicates(1);
            }, 350);
        }

        async function fetchDuplicates(page = 1) {
            currentDupePage = page;
            const container = document.getElementById('dupeListContainer');
            const category = document.getElementById('dupeCategorySelect').value;
            const dupeType = document.getElementById('dupeTypeSelect').value;
            const search = document.getElementById('dupeSearchInput').value.trim();

            container.innerHTML = `
                <div class="py-12 text-center text-zinc-400">
                    <i class="fas fa-spinner fa-spin text-2xl text-zinc-900 mb-2"></i>
                    <p class="text-xs">Scanning catalog for duplicate photos...</p>
                </div>
            `;

            try {
                const params = new URLSearchParams({
                    controller: 'photodownloader',
                    action: 'getDuplicates',
                    category: category,
                    duplicate_type: dupeType,
                    search: search,
                    page: page,
                    limit: 25
                });

                const res = await fetch('index.php?' + params.toString());
                const data = await res.json();

                if (!data.success) {
                    container.innerHTML = `<div class="p-4 text-center text-rose-600 text-xs">Error: ${data.message || 'Failed to scan duplicates.'}</div>`;
                    return;
                }

                // Check corrupted text records banner
                const corruptBanner = document.getElementById('corruptRecordsBanner');
                if (corruptBanner) {
                    if (data.corrupt_text_records_count > 0) {
                        corruptBanner.style.display = 'flex';
                        const countEl = document.getElementById('corruptCountText');
                        if (countEl) countEl.textContent = data.corrupt_text_records_count;
                    } else {
                        corruptBanner.style.display = 'none';
                    }
                }

                // Update KPIs
                document.getElementById('dupeKpiGroups').textContent = data.summary.total_duplicate_groups.toLocaleString();
                document.getElementById('dupeKpiRedundant').textContent = data.summary.total_redundant_photos.toLocaleString();
                document.getElementById('dupeKpiScope').textContent = data.category_label || 'All Categories';
                document.getElementById('dupeResultsCountBadge').textContent = `${data.summary.total_duplicate_groups} groups found`;

                // Update Pagination Controls
                renderDupePagination(data.summary);

                // Render groups
                if (data.groups.length === 0) {
                    container.innerHTML = `
                        <div class="py-12 text-center text-zinc-400">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-lg border border-emerald-200">
                                <i class="fas fa-check"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-zinc-900">No Duplicate Photos Found!</h3>
                            <p class="text-xs text-zinc-500 mt-1">Every photo in this category is unique and cleanly indexed.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.groups.forEach((g, idx) => {
                    const escImg = g.img_name.replace(/'/g, "\\'");
                    const skusHtml = g.skus_list.map(s => `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-zinc-100 text-zinc-800 border border-zinc-200">
                            ${s}
                        </span>
                    `).join(' ');

                    const isMultiSku = g.distinct_sku_count > 1;

                    html += `
                        <div class="duplicate-card flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between" id="dupeCard_${idx}">
                            <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                <img src="${g.clean_url}" alt="Photo" class="img-thumb-preview cursor-pointer" onerror="handleThumbError(this, '${g.clean_url}')" onclick="openImageLightbox('${g.clean_url}', '${g.file_name}')">
                                
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-semibold text-zinc-900 font-mono truncate max-w-sm">${g.file_name}</span>
                                        <button type="button" onclick="copyToClipboard('${g.clean_url}', 'Image URL copied!')" title="Copy URL" class="text-zinc-400 hover:text-zinc-800 text-[11px]">
                                            <i class="far fa-copy"></i>
                                        </button>
                                        <span class="shadcn-badge font-mono text-[10px] ${isMultiSku ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-zinc-100 text-zinc-800'}">
                                            <i class="fas fa-layer-group text-[9px] mr-1"></i> ${g.occurrence_count} copies
                                        </span>
                                        <span class="shadcn-badge font-mono text-[10px] ${isMultiSku ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-zinc-100 text-zinc-700'}">
                                            ${g.distinct_sku_count} ${g.distinct_sku_count === 1 ? 'SKU' : 'Different SKUs'}
                                        </span>
                                    </div>

                                    <div class="text-[11px] text-zinc-400 font-mono truncate mt-0.5">
                                        Path: ${g.img_name}
                                    </div>

                                    <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                        <span class="text-[11px] text-zinc-500 font-medium">SKUs:</span>
                                        ${skusHtml}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-center flex-wrap">
                                <button type="button" onclick="toggleDupeDetails(${idx})" class="shadcn-btn shadcn-btn-sm text-xs">
                                    <span>Details (${g.records.length})</span>
                                    <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                                </button>
                                <button type="button" onclick="deduplicateSingleGroup('${escImg}', ${g.keep_id}, ${idx})" class="shadcn-btn shadcn-btn-sm text-xs bg-slate-900 text-white hover:bg-slate-800" title="Keeps record #${g.keep_id} and removes duplicate copies from product_images_new">
                                    <i class="fas fa-check-double mr-1"></i> Deduplicate (Keep #1)
                                </button>
                                <button type="button" onclick="deleteAllReferencesForGroup('${escImg}', ${idx})" class="shadcn-btn shadcn-btn-sm shadcn-btn-danger text-xs" title="Removes ALL references of this photo from product_images_new table so its reference is not found in database">
                                    <i class="fas fa-trash-can mr-1"></i> Delete All References
                                </button>
                            </div>
                        </div>

                        <!-- Expandable Details Row -->
                        <div id="dupeDetails_${idx}" class="hidden p-3.5 bg-zinc-50 border border-t-0 border-zinc-200 rounded-b-lg -mt-3 text-xs space-y-2">
                            <div class="font-semibold text-zinc-700 text-[11px] uppercase tracking-wider mb-1">
                                Database Records in <code class="bg-zinc-200 px-1 py-0.5 rounded font-mono">product_images_new</code>:
                            </div>
                            <div class="divide-y divide-zinc-200">
                                ${g.records.map((r, rIdx) => `
                                    <div class="py-1.5 flex items-center justify-between text-zinc-600" id="recRow_${r.id}">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono text-zinc-900 font-medium">#${r.id}</span>
                                            <span class="shadcn-badge font-mono text-[10px]">SKU: ${r.pro_code}</span>
                                            <span class="text-zinc-400 text-[11px]">Rank: ${r.rank}</span>
                                            <span class="text-zinc-400 text-[11px]">Date: ${r.date_added}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            ${rIdx === 0 ? '<span class="text-emerald-700 font-semibold text-[11px] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Keep (Primary)</span>' : '<span class="text-rose-600 font-semibold text-[11px] bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Redundant Copy</span>'}
                                            <button type="button" onclick="deleteSpecificRecord(${r.id}, ${idx})" class="text-zinc-400 hover:text-rose-600 text-[11px] px-1.5 py-0.5 rounded border border-zinc-200 hover:border-rose-200 transition-colors" title="Delete record #${r.id} from product_images_new">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

            } catch (err) {
                container.innerHTML = `<div class="p-4 text-center text-rose-600 text-xs">Scan failed: ${err.message}</div>`;
            }
        }

        // Sub-View Switching between Catalog Duplicates and Unreferenced Server Photos
        function switchDupeView(viewName) {
            const btnCatalog = document.getElementById('subTabDupes');
            const btnUnref = document.getElementById('subTabUnreferenced');
            const viewCatalog = document.getElementById('subViewCatalogDupes');
            const viewUnref = document.getElementById('subViewUnreferenced');

            if (viewName === 'unreferenced') {
                if (btnUnref) btnUnref.className = 'shadcn-btn shadcn-btn-sm shadcn-btn-primary text-xs';
                if (btnCatalog) btnCatalog.className = 'shadcn-btn shadcn-btn-sm text-xs';
                if (viewUnref) viewUnref.style.display = 'block';
                if (viewCatalog) viewCatalog.style.display = 'none';
                fetchUnreferencedPhotos();
            } else {
                if (btnCatalog) btnCatalog.className = 'shadcn-btn shadcn-btn-sm shadcn-btn-primary text-xs';
                if (btnUnref) btnUnref.className = 'shadcn-btn shadcn-btn-sm text-xs';
                if (viewCatalog) viewCatalog.style.display = 'block';
                if (viewUnref) viewUnref.style.display = 'none';
                fetchDuplicates(currentDupePage);
            }
        }

        // Purge Corrupted Text Records
        async function purgeCorruptedRecords() {
            if (!confirm('Are you sure you want to purge all corrupted non-image text entries from product_images_new table?\n\nThis permanently removes descriptions (such as /4.Featuring attractive designs) and leaves only real photos in your database.')) {
                return;
            }

            const btn = document.getElementById('btnPurgeCorrupt');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Purging...';
            }

            try {
                const res = await fetch('index.php?controller=photodownloader&action=purgeCorruptedTextRecords', {
                    method: 'POST'
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'Corrupted records purged successfully!');
                    const banner = document.getElementById('corruptRecordsBanner');
                    if (banner) banner.style.display = 'none';
                    fetchDuplicates(1);
                } else {
                    showToast(data.message || 'Failed to purge records.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-broom mr-1"></i> Purge From Database';
                }
            }
        }

        // Thumbnail Error Fallback Handler
        function handleThumbError(imgEl, rawUrl) {
            const step = parseInt(imgEl.getAttribute('data-err-step') || '0', 10);
            if (step === 0 && rawUrl && rawUrl.includes('/yn/uploads/')) {
                imgEl.setAttribute('data-err-step', '1');
                imgEl.src = rawUrl.replace('/yn/uploads/', '/uploads/');
            } else if (step <= 1 && rawUrl && rawUrl.includes('/uploads/')) {
                imgEl.setAttribute('data-err-step', '2');
                imgEl.src = rawUrl.replace(/.*\/uploads\//, 'https://srishringarr.com/');
            } else {
                imgEl.onerror = null;
                imgEl.src = 'https://placehold.co/72x72/f1f5f9/64748b?text=No+Img';
            }
        }

        function toggleDupeDetails(idx) {
            const el = document.getElementById(`dupeDetails_${idx}`);
            if (el) {
                el.classList.toggle('hidden');
            }
        }

        function renderDupePagination(summary) {
            const wrap = document.getElementById('dupePaginationControls');
            if (summary.total_pages <= 1) {
                wrap.innerHTML = '';
                return;
            }

            wrap.innerHTML = `
                <button type="button" onclick="fetchDuplicates(${summary.current_page - 1})" ${summary.current_page <= 1 ? 'disabled' : ''} class="shadcn-btn shadcn-btn-sm text-xs ${summary.current_page <= 1 ? 'opacity-40 cursor-not-allowed' : ''}">
                    <i class="fas fa-chevron-left mr-1"></i> Prev
                </button>
                <span class="text-xs text-zinc-500 font-mono px-2">Page ${summary.current_page} of ${summary.total_pages}</span>
                <button type="button" onclick="fetchDuplicates(${summary.current_page + 1})" ${summary.current_page >= summary.total_pages ? 'disabled' : ''} class="shadcn-btn shadcn-btn-sm text-xs ${summary.current_page >= summary.total_pages ? 'opacity-40 cursor-not-allowed' : ''}">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </button>
            `;
        }

        async function deduplicateSingleGroup(imgName, keepId, cardIdx) {
            if (!confirm(`Are you sure you want to remove duplicate entries for this photo?\n\nThis will keep primary record #${keepId} and safely delete all redundant duplicate records from product_images_new table.`)) {
                return;
            }

            try {
                const fd = new FormData();
                fd.append('img_name', imgName);
                fd.append('keep_id', keepId);

                const res = await fetch('index.php?controller=photodownloader&action=deduplicateGroup', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'Duplicate photo records removed successfully!');
                    const card = document.getElementById(`dupeCard_${cardIdx}`);
                    const details = document.getElementById(`dupeDetails_${cardIdx}`);
                    if (card) card.remove();
                    if (details) details.remove();
                    fetchDuplicates(currentDupePage);
                } else {
                    showToast(data.message || 'Failed to remove duplicates.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }

        // Delete ALL references of an image from product_images_new table
        async function deleteAllReferencesForGroup(imgName, cardIdx) {
            if (!confirm(`⚠️ DELETE ALL REFERENCES\n\nAre you sure you want to delete ALL database references for this photo?\n\nImage: ${imgName}\n\nThis will ensure this photo's reference is completely NOT found in product_images_new table.`)) {
                return;
            }

            try {
                const fd = new FormData();
                fd.append('img_name', imgName);

                const res = await fetch('index.php?controller=photodownloader&action=deleteAllReferences', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'All references removed from product_images_new table!');
                    const card = document.getElementById(`dupeCard_${cardIdx}`);
                    const details = document.getElementById(`dupeDetails_${cardIdx}`);
                    if (card) card.remove();
                    if (details) details.remove();
                    fetchDuplicates(currentDupePage);
                } else {
                    showToast(data.message || 'Failed to delete references.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }

        // Delete a specific database row by ID from product_images_new
        async function deleteSpecificRecord(recordId, cardIdx) {
            if (!confirm(`Delete database record #${recordId} from product_images_new table?`)) {
                return;
            }

            try {
                const fd = new FormData();
                fd.append('id', recordId);

                const res = await fetch('index.php?controller=photodownloader&action=deleteRecordById', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    showToast(`Record #${recordId} removed from database.`);
                    const row = document.getElementById(`recRow_${recordId}`);
                    if (row) row.remove();
                } else {
                    showToast(data.message || 'Failed to delete record.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }

        // ==================== UNREFERENCED SERVER PHOTOS LOGIC ====================
        async function fetchUnreferencedPhotos() {
            const container = document.getElementById('unrefListContainer');
            const folderInput = document.getElementById('unreferencedFolderInput');
            const folder = folderInput ? folderInput.value.trim() : '2026/08';

            container.innerHTML = `
                <div class="py-12 text-center text-zinc-400">
                    <i class="fas fa-spinner fa-spin text-2xl text-zinc-900 mb-2"></i>
                    <p class="text-xs">Scanning server disk in folder <code>${folder}</code> for photos unreferenced in <code>product_images_new</code>...</p>
                </div>
            `;

            try {
                const res = await fetch(`index.php?controller=photodownloader&action=getUnreferencedPhotos&folder=${encodeURIComponent(folder)}`);
                const data = await res.json();

                if (!data.success) {
                    container.innerHTML = `<div class="p-4 text-center text-rose-600 text-xs">Error: ${data.message || 'Failed to scan server folder.'}</div>`;
                    return;
                }

                // Update KPIs
                const scannedEl = document.getElementById('unrefKpiScanned');
                const countEl = document.getElementById('unrefKpiCount');
                const sizeEl = document.getElementById('unrefKpiSize');
                const badgeEl = document.getElementById('unrefResultsCountBadge');

                if (scannedEl) scannedEl.textContent = (data.total_files_scanned || 0).toLocaleString();
                if (countEl) countEl.textContent = (data.unreferenced_count || 0).toLocaleString();
                if (sizeEl) sizeEl.textContent = (data.unreferenced_size_mb || 0) + ' MB';
                if (badgeEl) badgeEl.textContent = `${data.unreferenced_count || 0} unreferenced photos`;

                if (!data.items || data.items.length === 0) {
                    container.innerHTML = `
                        <div class="py-12 text-center text-zinc-400">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-lg border border-emerald-200">
                                <i class="fas fa-check"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-zinc-900">All Photos in This Folder Are Referenced!</h3>
                            <p class="text-xs text-zinc-500 mt-1">Every photo found in folder <code>${folder}</code> is actively referenced by products in <code>product_images_new</code>.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.items.forEach((item, idx) => {
                    const escFile = item.file_name.replace(/'/g, "\\'");
                    const escFolder = (item.folder || folder).replace(/'/g, "\\'");

                    html += `
                        <div class="duplicate-card flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between" id="unrefCard_${idx}">
                            <div class="flex items-center gap-3.5 min-w-0 flex-1">
                                <img src="${item.full_url}" alt="Unreferenced Photo" class="img-thumb-preview cursor-pointer" onerror="handleThumbError(this, '${item.full_url}')" onclick="openImageLightbox('${item.full_url}', '${escFile}')">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-semibold text-zinc-900 font-mono truncate max-w-sm">${item.file_name}</span>
                                        <button type="button" onclick="copyToClipboard('${item.full_url}', 'Image URL copied!')" title="Copy URL" class="text-zinc-400 hover:text-zinc-800 text-[11px]">
                                            <i class="far fa-copy"></i>
                                        </button>
                                        <span class="shadcn-badge font-mono text-[10px] bg-amber-50 text-amber-800 border-amber-200">
                                            <i class="fas fa-hard-drive mr-1"></i> ${item.file_size_mb} MB
                                        </span>
                                        <span class="shadcn-badge font-mono text-[10px] bg-rose-50 text-rose-700 border-rose-200">
                                            <i class="fas fa-ban mr-1"></i> Reference Not in DB
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-zinc-400 font-mono truncate mt-0.5">
                                        Location: /yn/uploads/${item.folder}/${item.file_name}
                                    </div>
                                    <div class="text-[11px] text-zinc-500 mt-1">
                                        Modified: ${item.modified_at} &bull; <span class="text-rose-600 font-medium">Reference NOT found in product_images_new table</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-center">
                                <button type="button" onclick="deleteUnreferencedFile('${escFile}', '${escFolder}', ${idx})" class="shadcn-btn shadcn-btn-sm shadcn-btn-danger text-xs">
                                    <i class="fas fa-trash-can mr-1"></i> Delete From Server Disk
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

            } catch (err) {
                container.innerHTML = `<div class="p-4 text-center text-rose-600 text-xs">Disk scan failed: ${err.message}</div>`;
            }
        }

        async function deleteUnreferencedFile(fileName, folder, cardIdx) {
            if (!confirm(`Are you sure you want to permanently delete this unreferenced photo from server disk?\n\nFile: ${fileName}\nFolder: ${folder}\n\nThis file is confirmed NOT referenced by any product in product_images_new table.`)) {
                return;
            }

            try {
                const fd = new FormData();
                fd.append('file_name', fileName);
                fd.append('folder', folder);

                const res = await fetch('index.php?controller=photodownloader&action=deleteUnreferencedFile', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'File removed from server disk!');
                    const card = document.getElementById(`unrefCard_${cardIdx}`);
                    if (card) card.remove();
                } else {
                    showToast(data.message || 'Failed to delete file.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }

        async function confirmDeduplicateCategory() {
            const catSelect = document.getElementById('dupeCategorySelect');
            const catName = catSelect.options[catSelect.selectedIndex].text;
            const catVal = catSelect.value;

            if (!confirm(`⚠️ DEDUPLICATION CONFIRMATION\n\nAre you sure you want to deduplicate ALL photos in:\n"${catName}"?\n\nFor every duplicate photo in this category, the primary copy will be preserved and all redundant duplicate records will be removed from product_images_new table.\n\nProceed?`)) {
                return;
            }

            const btn = document.getElementById('btnDeduplicateCat');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deduplicating...';

            try {
                const fd = new FormData();
                fd.append('category', catVal);

                const res = await fetch('index.php?controller=photodownloader&action=deduplicateCategory', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'Category deduplicated successfully!');
                    fetchDuplicates(1);
                } else {
                    showToast(data.message || 'Failed to deduplicate category.', 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-can mr-1"></i> Deduplicate Current Category';
            }
        }

        function exportDuplicatesCsv() {
            const category = document.getElementById('dupeCategorySelect').value;
            const dupeType = document.getElementById('dupeTypeSelect').value;
            const search = document.getElementById('dupeSearchInput').value.trim();

            const params = new URLSearchParams({
                controller: 'photodownloader',
                action: 'exportDuplicatesCsv',
                category: category,
                duplicate_type: dupeType,
                search: search
            });

            window.location.href = 'index.php?' + params.toString();
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            fetchPreviewMetrics();

            // Check URL for autostart
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autostart') === '1') {
                const cleanUrl = window.location.pathname + '?controller=photodownloader&action=index';
                window.history.replaceState({}, document.title, cleanUrl);
                setTimeout(() => {
                    startBatchDownload();
                }, 350);
            }

            // Check if duplicates tab requested
            if (urlParams.get('tab') === 'duplicates') {
                switchMainTab('duplicates');
            }
        });
    </script>
</body>
</html>
