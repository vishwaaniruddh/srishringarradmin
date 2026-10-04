<!DOCTYPE html>
<html lang="en">
<head>
    <title>Category Hierarchy - Srishringarr</title>
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
            padding: 12px 18px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
        }

        /* ShadCN Buttons */
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

        .shadcn-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 5px;
            color: #71717a;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            transition: all 0.12s ease;
            text-decoration: none;
        }
        .shadcn-icon-btn:hover {
            background: #f4f4f5;
            border-color: #e4e4e7;
            color: #09090b;
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

        .shadcn-badge-sub {
            background: #fafafa;
            color: #71717a;
            border: 1px solid #e4e4e7;
            font-size: 10.5px;
        }

        /* Segmented Tabs */
        .segmented-tabs-container {
            display: inline-flex;
            align-items: center;
            background: #f4f4f5;
            padding: 3px;
            border-radius: 7px;
            border: 1px solid #e4e4e7;
            gap: 2px;
        }
        .segmented-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 5px;
            font-size: 12.5px;
            font-weight: 500;
            color: #71717a;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.12s ease;
        }
        .segmented-tab-btn:hover {
            color: #09090b;
        }
        .segmented-tab-btn.active {
            background: #ffffff;
            color: #09090b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }

        /* Search input */
        .search-input-wrap {
            position: relative;
            width: 100%;
            max-width: 340px;
        }
        .search-input-wrap i.search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            font-size: 12px;
            pointer-events: none;
        }
        .search-input-wrap input {
            width: 100%;
            height: 34px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 28px 0 32px;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
        }
        .search-input-wrap input:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }
        .search-input-wrap .clear-btn {
            display: none;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
            padding: 2px 4px;
            line-height: 1;
        }
        .search-input-wrap .clear-btn:hover {
            color: #09090b;
        }

        /* ShadCN Table Standard */
        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .shadcn-table th {
            background: #fafafa;
            padding: 9px 16px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
            border-bottom: 1px solid #e4e4e7;
            text-align: left;
            white-space: nowrap;
        }
        .shadcn-table td {
            padding: 9px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f4f4f5;
            font-size: 13px;
            color: #09090b;
        }
        .shadcn-table tbody tr.cat-main-row:hover td {
            background-color: #fcfcfc;
        }
        .shadcn-table tbody tr.cat-sub-row {
            background-color: #fbfbfb;
        }
        .shadcn-table tbody tr.cat-sub-row:hover td {
            background-color: #f4f4f5;
        }

        /* Toggle Caret */
        .toggle-caret {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
            cursor: pointer;
            color: #a1a1aa;
            transition: transform 0.15s ease, color 0.12s ease;
            margin-right: 6px;
            flex-shrink: 0;
        }
        .toggle-caret:hover {
            color: #09090b;
            background: #e4e4e7;
        }
        .toggle-caret.collapsed {
            transform: rotate(-90deg);
        }

        /* Toast notification */
        .toast-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 500;
            margin-bottom: 16px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            animation: fadeIn 0.15s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
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
            $pageTitle = 'Category Hierarchy';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <?php
            // Calculate Taxonomy Metrics
            $jewelCatCount = count($jewelCat ?? []);
            $jewelSubCount = count($jewelSub ?? []);
            $jewelProdCount = 0;
            foreach ($jewelCat ?? [] as $c) { $jewelProdCount += (int)($c['product_count'] ?? 0); }
            foreach ($jewelSub ?? [] as $s) { $jewelProdCount += (int)($s['product_count'] ?? 0); }

            $garmentCatCount = count($garmentCat ?? []);
            $garmentSubCount = count($garmentSub ?? []);
            $garmentProdCount = 0;
            foreach ($garmentCat ?? [] as $c) { $garmentProdCount += (int)($c['product_count'] ?? 0); }
            foreach ($garmentSub ?? [] as $s) { $garmentProdCount += (int)($s['product_count'] ?? 0); }

            $totalMain = $jewelCatCount + $garmentCatCount;
            $totalSub = $jewelSubCount + $garmentSubCount;
            $totalProds = $jewelProdCount + $garmentProdCount;
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Flash Notification -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="toast-banner text-zinc-900" id="flash-banner">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-zinc-900 text-xs"></i>
                                <span>Category successfully <?php echo htmlspecialchars($_GET['success']); ?>.</span>
                            </div>
                            <button onclick="document.getElementById('flash-banner').remove()" class="text-zinc-400 hover:text-zinc-900 border-none bg-transparent cursor-pointer text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="toast-banner text-zinc-900" id="flash-banner" style="border-left: 3px solid #09090b;">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle text-zinc-900 text-xs"></i>
                                <span>Operation <?php echo htmlspecialchars($_GET['error']); ?>. Please check your data and retry.</span>
                            </div>
                            <button onclick="document.getElementById('flash-banner').remove()" class="text-zinc-400 hover:text-zinc-900 border-none bg-transparent cursor-pointer text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">Category Hierarchy</h1>
                                <span class="shadcn-badge font-mono text-[11px]"><?php echo $totalMain; ?> Mains / <?php echo $totalSub; ?> Subs</span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Manage jewellery and garment taxonomic structures, subcategories, and linked storefront inventories.</p>
                        </div>

                        <!-- Top Quick Actions -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="index.php?controller=category&action=unmapped" class="shadcn-btn">
                                <i class="fas fa-unlink text-[11px] text-zinc-400"></i>
                                <span>Unmapped Items</span>
                            </a>
                            <a href="index.php?controller=category&action=add&type=jewel_cat" id="top-add-btn" class="shadcn-btn shadcn-btn-primary">
                                <i class="fas fa-plus text-[11px]"></i>
                                <span id="top-add-btn-text">Add Jewel Category</span>
                            </a>
                        </div>
                    </div>

                    <!-- Metrics Stat Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Jewellery Taxonomy</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo $jewelCatCount; ?></span>
                                    <span class="text-xs text-zinc-400">mains / <?php echo $jewelSubCount; ?> subs</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-gem"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Garments Taxonomy</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo $garmentCatCount; ?></span>
                                    <span class="text-xs text-zinc-400">mains / <?php echo $garmentSubCount; ?> subs</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-tshirt"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Mappings</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo number_format($totalProds); ?></span>
                                    <span class="text-xs text-zinc-400">products cataloged</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Inventory Health</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo $totalMain + $totalSub; ?></span>
                                    <span class="text-xs text-zinc-400">active nodes</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar: Tabs + Search + View Controls -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <!-- Segmented Tab Switcher -->
                        <div class="segmented-tabs-container">
                            <button type="button" onclick="switchCategoryTab('jewel')" id="tab-jewel" class="segmented-tab-btn active">
                                <i class="fas fa-gem text-xs"></i>
                                <span>Jewellery</span>
                                <span class="shadcn-badge font-mono text-[10px] ml-0.5 px-1.5 py-0"><?php echo $jewelCatCount; ?></span>
                            </button>
                            <button type="button" onclick="switchCategoryTab('garment')" id="tab-garment" class="segmented-tab-btn">
                                <i class="fas fa-tshirt text-xs"></i>
                                <span>Garments</span>
                                <span class="shadcn-badge font-mono text-[10px] ml-0.5 px-1.5 py-0"><?php echo $garmentCatCount; ?></span>
                            </button>
                        </div>

                        <!-- Search & Tree Controls -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="search-input-wrap flex-1 sm:flex-initial">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="catSearch" placeholder="Filter categories or subcategories..." autocomplete="off">
                                <button type="button" id="clearSearch" class="clear-btn" title="Clear filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <button type="button" id="toggleAllBtn" onclick="toggleAllSubcategories()" class="shadcn-btn" title="Expand or collapse all subcategories">
                                <i class="fas fa-bars-staggered text-zinc-400 text-xs"></i>
                                <span id="toggleAllText">Collapse All</span>
                            </button>
                        </div>
                    </div>

                    <!-- JEWELLERY SECTION -->
                    <div id="jewel-section" class="tab-pane">
                        <div class="shadcn-card">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs font-semibold text-zinc-700 uppercase tracking-wider">Jewellery Structure</h3>
                                    <span class="text-xs text-zinc-400" id="jewel-visible-count">Showing <?php echo $jewelCatCount; ?> main categories</span>
                                </div>
                                <span class="text-[11px] text-zinc-400">Click chevron or category row to toggle subcategories</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="shadcn-table" id="jewelTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">ID</th>
                                            <th>Category / Subcategory Name</th>
                                            <th style="width: 140px;">Type</th>
                                            <th style="width: 160px;">Products Mapped</th>
                                            <th style="width: 140px; text-align: right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="jewelTableBody">
                                        <?php if (empty($jewelCat)): ?>
                                            <tr>
                                                <td colspan="5" class="py-12 text-center text-zinc-400 text-xs">
                                                    <i class="fas fa-folder-open text-2xl mb-2 block text-zinc-300"></i>
                                                    No jewellery categories found.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($jewelCat as $cat): 
                                                $subcats = array_values(array_filter($jewelSub ?? [], function($sub) use ($cat) {
                                                    return (int)$sub['maincat_id'] === (int)$cat['id'];
                                                }));
                                                $hasSubs = !empty($subcats);
                                                $subCount = count($subcats);
                                                $rowId = 'jewel-main-' . $cat['id'];
                                            ?>
                                                <!-- Main Category Row -->
                                                <tr class="cat-main-row cat-row" data-name="<?php echo strtolower(htmlspecialchars($cat['name'])); ?>" data-cat-id="<?php echo $rowId; ?>">
                                                    <td class="font-mono text-xs text-zinc-400">#<?php echo $cat['id']; ?></td>
                                                    <td>
                                                        <div class="flex items-center">
                                                            <?php if ($hasSubs): ?>
                                                                <button type="button" class="toggle-caret" onclick="toggleParent('<?php echo $rowId; ?>', event)" title="Toggle subcategories">
                                                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="w-[18px] mr-1.5 flex-shrink-0"></span>
                                                            <?php endif; ?>

                                                            <div class="flex items-center gap-2">
                                                                <span class="font-semibold text-zinc-900"><?php echo htmlspecialchars($cat['name']); ?></span>
                                                                <?php if ($hasSubs): ?>
                                                                    <span class="shadcn-badge font-mono text-[10px] text-zinc-500 bg-zinc-100"><?php echo $subCount; ?> <?php echo $subCount === 1 ? 'sub' : 'subs'; ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="shadcn-badge">Main Category</span>
                                                    </td>
                                                    <td>
                                                        <a href="index.php?controller=product&action=index&category=<?php echo urlencode($cat['name']); ?>" class="shadcn-badge hover:bg-zinc-200 transition-colors" title="View products in this category">
                                                            <i class="fas fa-box text-[9px] text-zinc-400"></i>
                                                            <span class="font-mono font-semibold"><?php echo number_format((int)$cat['product_count']); ?></span>
                                                            <span class="text-zinc-400">items</span>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="flex items-center justify-end gap-1">
                                                            <a href="index.php?controller=category&action=add&type=jewel_sub&parent_id=<?php echo $cat['id']; ?>" class="shadcn-btn shadcn-btn-sm" title="Add subcategory under <?php echo htmlspecialchars($cat['name']); ?>">
                                                                <i class="fas fa-plus text-[10px]"></i>
                                                                <span>Add Sub</span>
                                                            </a>
                                                            <a href="index.php?controller=category&action=edit&type=jewel_cat&id=<?php echo $cat['id']; ?>" class="shadcn-icon-btn" title="Edit Category">
                                                                <i class="fas fa-pen text-xs"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Subcategories Rows -->
                                                <?php foreach ($subcats as $sub): ?>
                                                    <tr class="cat-sub-row cat-row" data-parent-id="<?php echo $rowId; ?>" data-name="<?php echo strtolower(htmlspecialchars($sub['name'])); ?>">
                                                        <td class="font-mono text-xs text-zinc-400 pl-8">#<?php echo $sub['id']; ?></td>
                                                        <td>
                                                            <div class="flex items-center pl-6">
                                                                <span class="text-zinc-300 font-mono text-xs select-none mr-2">└─</span>
                                                                <span class="text-zinc-700 font-normal"><?php echo htmlspecialchars($sub['name']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="shadcn-badge shadcn-badge-sub">Subcategory</span>
                                                        </td>
                                                        <td>
                                                            <a href="index.php?controller=product&action=index&category=<?php echo urlencode($sub['name']); ?>" class="shadcn-badge shadcn-badge-sub hover:bg-zinc-200 transition-colors" title="View products in this subcategory">
                                                                <i class="fas fa-tag text-[9px] text-zinc-400"></i>
                                                                <span class="font-mono font-medium"><?php echo number_format((int)$sub['product_count']); ?></span>
                                                                <span class="text-zinc-400">items</span>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <div class="flex items-center justify-end gap-1">
                                                                <a href="index.php?controller=category&action=edit&type=jewel_sub&id=<?php echo $sub['id']; ?>" class="shadcn-icon-btn" title="Edit Subcategory">
                                                                    <i class="fas fa-pen text-xs"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <tr id="jewelNoResultsRow" style="display: none;">
                                            <td colspan="5" class="py-10 text-center text-zinc-400 text-xs">
                                                <i class="fas fa-search text-xl mb-2 block text-zinc-300"></i>
                                                No jewellery categories or subcategories matched your filter.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- GARMENTS SECTION -->
                    <div id="garment-section" class="tab-pane hidden">
                        <div class="shadcn-card">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs font-semibold text-zinc-700 uppercase tracking-wider">Garments Structure</h3>
                                    <span class="text-xs text-zinc-400" id="garment-visible-count">Showing <?php echo $garmentCatCount; ?> main categories</span>
                                </div>
                                <span class="text-[11px] text-zinc-400">Click chevron or category row to toggle subcategories</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="shadcn-table" id="garmentTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">ID</th>
                                            <th>Category / Subcategory Name</th>
                                            <th style="width: 140px;">Type</th>
                                            <th style="width: 160px;">Products Mapped</th>
                                            <th style="width: 140px; text-align: right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="garmentTableBody">
                                        <?php if (empty($garmentCat)): ?>
                                            <tr>
                                                <td colspan="5" class="py-12 text-center text-zinc-400 text-xs">
                                                    <i class="fas fa-folder-open text-2xl mb-2 block text-zinc-300"></i>
                                                    No garment categories found.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($garmentCat as $cat): 
                                                $subcats = array_values(array_filter($garmentSub ?? [], function($sub) use ($cat) {
                                                    return (int)($sub['gmain_id'] ?? 0) === (int)$cat['id'];
                                                }));
                                                $hasSubs = !empty($subcats);
                                                $subCount = count($subcats);
                                                $rowId = 'garment-main-' . $cat['id'];
                                            ?>
                                                <!-- Main Category Row -->
                                                <tr class="cat-main-row cat-row" data-name="<?php echo strtolower(htmlspecialchars($cat['name'])); ?>" data-cat-id="<?php echo $rowId; ?>">
                                                    <td class="font-mono text-xs text-zinc-400">#<?php echo $cat['id']; ?></td>
                                                    <td>
                                                        <div class="flex items-center">
                                                            <?php if ($hasSubs): ?>
                                                                <button type="button" class="toggle-caret" onclick="toggleParent('<?php echo $rowId; ?>', event)" title="Toggle subcategories">
                                                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="w-[18px] mr-1.5 flex-shrink-0"></span>
                                                            <?php endif; ?>

                                                            <div class="flex items-center gap-2">
                                                                <span class="font-semibold text-zinc-900"><?php echo htmlspecialchars($cat['name']); ?></span>
                                                                <?php if ($hasSubs): ?>
                                                                    <span class="shadcn-badge font-mono text-[10px] text-zinc-500 bg-zinc-100"><?php echo $subCount; ?> <?php echo $subCount === 1 ? 'sub' : 'subs'; ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="shadcn-badge">Main Category</span>
                                                    </td>
                                                    <td>
                                                        <a href="index.php?controller=product&action=index&category=<?php echo urlencode($cat['name']); ?>" class="shadcn-badge hover:bg-zinc-200 transition-colors" title="View products in this category">
                                                            <i class="fas fa-box text-[9px] text-zinc-400"></i>
                                                            <span class="font-mono font-semibold"><?php echo number_format((int)$cat['product_count']); ?></span>
                                                            <span class="text-zinc-400">items</span>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="flex items-center justify-end gap-1">
                                                            <a href="index.php?controller=category&action=add&type=garment_sub&parent_id=<?php echo $cat['id']; ?>" class="shadcn-btn shadcn-btn-sm" title="Add subcategory under <?php echo htmlspecialchars($cat['name']); ?>">
                                                                <i class="fas fa-plus text-[10px]"></i>
                                                                <span>Add Sub</span>
                                                            </a>
                                                            <a href="index.php?controller=category&action=edit&type=garment_cat&id=<?php echo $cat['id']; ?>" class="shadcn-icon-btn" title="Edit Category">
                                                                <i class="fas fa-pen text-xs"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Subcategories Rows -->
                                                <?php foreach ($subcats as $sub): ?>
                                                    <tr class="cat-sub-row cat-row" data-parent-id="<?php echo $rowId; ?>" data-name="<?php echo strtolower(htmlspecialchars($sub['name'])); ?>">
                                                        <td class="font-mono text-xs text-zinc-400 pl-8">#<?php echo $sub['id']; ?></td>
                                                        <td>
                                                            <div class="flex items-center pl-6">
                                                                <span class="text-zinc-300 font-mono text-xs select-none mr-2">└─</span>
                                                                <span class="text-zinc-700 font-normal"><?php echo htmlspecialchars($sub['name']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="shadcn-badge shadcn-badge-sub">Subcategory</span>
                                                        </td>
                                                        <td>
                                                            <a href="index.php?controller=product&action=index&category=<?php echo urlencode($sub['name']); ?>" class="shadcn-badge shadcn-badge-sub hover:bg-zinc-200 transition-colors" title="View products in this subcategory">
                                                                <i class="fas fa-tag text-[9px] text-zinc-400"></i>
                                                                <span class="font-mono font-medium"><?php echo number_format((int)$sub['product_count']); ?></span>
                                                                <span class="text-zinc-400">items</span>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <div class="flex items-center justify-end gap-1">
                                                                <a href="index.php?controller=category&action=edit&type=garment_sub&id=<?php echo $sub['id']; ?>" class="shadcn-icon-btn" title="Edit Subcategory">
                                                                    <i class="fas fa-pen text-xs"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <tr id="garmentNoResultsRow" style="display: none;">
                                            <td colspan="5" class="py-10 text-center text-zinc-400 text-xs">
                                                <i class="fas fa-search text-xl mb-2 block text-zinc-300"></i>
                                                No garment categories or subcategories matched your filter.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        let currentTab = 'jewel';
        let isAllCollapsed = false;

        // Auto-dismiss Flash banner after 4 seconds
        setTimeout(() => {
            const banner = document.getElementById('flash-banner');
            if (banner) {
                banner.style.transition = 'opacity 0.3s ease';
                banner.style.opacity = '0';
                setTimeout(() => banner.remove(), 300);
            }
        }, 4000);

        // Switch between Jewellery and Garments
        function switchCategoryTab(type) {
            currentTab = type;
            const jewelSec = document.getElementById('jewel-section');
            const garmentSec = document.getElementById('garment-section');
            const tabJewel = document.getElementById('tab-jewel');
            const tabGarment = document.getElementById('tab-garment');
            const topAddBtn = document.getElementById('top-add-btn');
            const topAddText = document.getElementById('top-add-btn-text');

            if (type === 'jewel') {
                jewelSec.classList.remove('hidden');
                garmentSec.classList.add('hidden');
                tabJewel.classList.add('active');
                tabGarment.classList.remove('active');
                topAddBtn.href = 'index.php?controller=category&action=add&type=jewel_cat';
                topAddText.textContent = 'Add Jewel Category';
                window.location.hash = 'jewel';
            } else {
                jewelSec.classList.add('hidden');
                garmentSec.classList.remove('hidden');
                tabGarment.classList.add('active');
                tabJewel.classList.remove('active');
                topAddBtn.href = 'index.php?controller=category&action=add&type=garment_cat';
                topAddText.textContent = 'Add Garment Category';
                window.location.hash = 'garment';
            }

            // Re-apply active search term to the visible table
            applyFilter();
        }

        // Toggle subcategories of a specific parent
        function toggleParent(rowId, event) {
            if (event) {
                event.stopPropagation();
            }
            const parentRow = document.querySelector(`tr[data-cat-id="${rowId}"]`);
            if (!parentRow) return;

            const caret = parentRow.querySelector('.toggle-caret');
            const isCurrentlyCollapsed = caret ? caret.classList.contains('collapsed') : false;
            const subRows = document.querySelectorAll(`tr[data-parent-id="${rowId}"]`);

            if (isCurrentlyCollapsed) {
                // Expand
                if (caret) caret.classList.remove('collapsed');
                subRows.forEach(row => {
                    row.style.display = '';
                });
            } else {
                // Collapse
                if (caret) caret.classList.add('collapsed');
                subRows.forEach(row => {
                    row.style.display = 'none';
                });
            }
        }

        // Toggle All Subcategories (Expand / Collapse All)
        function toggleAllSubcategories() {
            isAllCollapsed = !isAllCollapsed;
            const activeTableId = currentTab === 'jewel' ? '#jewelTable' : '#garmentTable';
            const table = document.querySelector(activeTableId);
            if (!table) return;

            const carets = table.querySelectorAll('.toggle-caret');
            const subRows = table.querySelectorAll('.cat-sub-row');
            const toggleText = document.getElementById('toggleAllText');

            if (isAllCollapsed) {
                carets.forEach(c => c.classList.add('collapsed'));
                subRows.forEach(r => r.style.display = 'none');
                toggleText.textContent = 'Expand All';
            } else {
                carets.forEach(c => c.classList.remove('collapsed'));
                subRows.forEach(r => r.style.display = '');
                toggleText.textContent = 'Collapse All';
            }
        }

        // Real-time Search Filter
        const searchInput = document.getElementById('catSearch');
        const clearBtn = document.getElementById('clearSearch');

        function applyFilter() {
            const term = searchInput.value.trim().toLowerCase();
            clearBtn.style.display = term ? 'block' : 'none';

            const activeTableId = currentTab === 'jewel' ? 'jewelTable' : 'garmentTable';
            const noResultsId = currentTab === 'jewel' ? 'jewelNoResultsRow' : 'garmentNoResultsRow';
            const countLabelId = currentTab === 'jewel' ? 'jewel-visible-count' : 'garment-visible-count';

            const table = document.getElementById(activeTableId);
            if (!table) return;

            const mainRows = table.querySelectorAll('.cat-main-row');
            const noResultsRow = document.getElementById(noResultsId);
            const countLabel = document.getElementById(countLabelId);

            if (!term) {
                // Reset all
                mainRows.forEach(mainRow => {
                    mainRow.style.display = '';
                    const catId = mainRow.getAttribute('data-cat-id');
                    const caret = mainRow.querySelector('.toggle-caret');
                    const isCollapsed = caret && caret.classList.contains('collapsed');
                    const subRows = table.querySelectorAll(`tr[data-parent-id="${catId}"]`);
                    subRows.forEach(subRow => {
                        subRow.style.display = isCollapsed ? 'none' : '';
                    });
                });
                if (noResultsRow) noResultsRow.style.display = 'none';
                if (countLabel) countLabel.textContent = `Showing ${mainRows.length} main categories`;
                return;
            }

            let visibleMainCount = 0;

            mainRows.forEach(mainRow => {
                const catId = mainRow.getAttribute('data-cat-id');
                const mainName = mainRow.getAttribute('data-name') || '';
                const mainId = mainRow.querySelector('td:first-child')?.textContent?.toLowerCase() || '';
                const mainMatches = mainName.includes(term) || mainId.includes(term);

                const subRows = table.querySelectorAll(`tr[data-parent-id="${catId}"]`);
                let anySubMatches = false;

                subRows.forEach(subRow => {
                    const subName = subRow.getAttribute('data-name') || '';
                    const subId = subRow.querySelector('td:first-child')?.textContent?.toLowerCase() || '';
                    const subMatches = subName.includes(term) || subId.includes(term);

                    if (subMatches) {
                        subRow.style.display = '';
                        anySubMatches = true;
                    } else if (mainMatches) {
                        subRow.style.display = '';
                    } else {
                        subRow.style.display = 'none';
                    }
                });

                if (mainMatches || anySubMatches) {
                    mainRow.style.display = '';
                    visibleMainCount++;
                    // Auto-expand caret if any sub matched
                    const caret = mainRow.querySelector('.toggle-caret');
                    if (caret && anySubMatches) {
                        caret.classList.remove('collapsed');
                    }
                } else {
                    mainRow.style.display = 'none';
                }
            });

            if (noResultsRow) {
                noResultsRow.style.display = visibleMainCount === 0 ? '' : 'none';
            }
            if (countLabel) {
                countLabel.textContent = `Showing ${visibleMainCount} of ${mainRows.length} main categories`;
            }
        }

        searchInput.addEventListener('input', applyFilter);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
            applyFilter();
        });

        // Keyboard Shortcut: Press '/' to focus search
        document.addEventListener('keydown', (e) => {
            if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                searchInput.focus();
            }
        });

        // Initialize based on URL hash (e.g. #garment)
        document.addEventListener('DOMContentLoaded', () => {
            if (window.location.hash === '#garment') {
                switchCategoryTab('garment');
            }
        });
    </script>
</body>
</html>
