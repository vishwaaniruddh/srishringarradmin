<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Sync Manager - Srishringarr</title>
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
        .status-dot-failed { background-color: #ef4444; }
        .status-dot-skipped { background-color: #94a3b8; }

        /* Search input */
        .search-input-wrap {
            position: relative;
            width: 100%;
            max-width: 260px;
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
            height: 32px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px 0 30px;
            font-size: 12px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
        }
        .search-input-wrap input:focus {
            border-color: #09090b;
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
            padding: 10px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f4f4f5;
            font-size: 12.5px;
            color: #09090b;
        }
        .shadcn-table tbody tr:hover td {
            background-color: #fafafa;
        }

        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }

        .terminal-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .terminal-scrollbar::-webkit-scrollbar-track { background: #09090b; }
        .terminal-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }

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
            $pageTitle = 'Product Sync Manager';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">Product Sync Manager</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-success mr-1"></span>
                                    Parent &rarr; Child (Store Sync)
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Synchronize jewellery and apparel inventories from Srishringarr POS to Yosshitaneha web storefront.</p>
                        </div>

                        <!-- Top Action Button -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button id="btnBulkSync" onclick="startBulkSync()" class="shadcn-btn shadcn-btn-primary">
                                <i class="fas fa-sync-alt text-[11px]" id="syncIcon"></i>
                                <span>Sync All Products to Yosshitaneha</span>
                            </button>
                        </div>
                    </div>

                    <!-- Metrics Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Operations</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo number_format($stats['total_synced']); ?></span>
                                    <span class="text-xs text-zinc-400">logged cycles</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-database"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Successful Syncs</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo number_format($stats['success_count']); ?></span>
                                    <span class="text-xs text-zinc-400">products pushed</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Failed Syncs</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900"><?php echo number_format($stats['failed_count']); ?></span>
                                    <span class="text-xs text-zinc-400">errors flagged</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Last Sync Activity</span>
                                <div class="mt-1">
                                    <span class="text-sm font-semibold text-zinc-900 block truncate"><?php echo htmlspecialchars($stats['last_sync']); ?></span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Interactive Sync Console (Visible when sync is running) -->
                    <div id="syncStatusAlert" class="shadcn-card hidden mb-6" style="border-color: #09090b;">
                        <div class="shadcn-card-header bg-zinc-50/70">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded bg-zinc-900 text-white flex items-center justify-center text-xs">
                                    <i class="fas fa-sync-alt fa-spin" id="statusSpinner"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-zinc-900 flex items-center gap-2">
                                        <span id="syncStatusTitle">Bulk Syncing Products to Yosshitaneha</span>
                                        <span id="syncPercentBadge" class="shadcn-badge font-mono text-[10px]">0%</span>
                                    </div>
                                    <div id="syncStatusMsg" class="text-[11px] text-zinc-500 mt-0.5">Preparing product sync queue...</div>
                                </div>
                            </div>

                            <button type="button" id="btnCancelSync" onclick="cancelBulkSync()" style="display:none;" class="shadcn-btn shadcn-btn-sm">
                                <i class="fas fa-stop-circle text-xs text-zinc-400"></i>
                                <span>Stop Sync</span>
                            </button>
                        </div>

                        <div class="p-4 space-y-4">
                            <!-- Progress Bar Track -->
                            <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden border border-zinc-200">
                                <div id="syncProgressBar" class="bg-zinc-900 h-full w-0 transition-all duration-200 rounded-full"></div>
                            </div>

                            <!-- Realtime Counter Badges -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Processed</span>
                                    <span id="cntProcessed" class="text-sm font-semibold text-zinc-900 font-mono mt-0.5 block">0 / 0</span>
                                </div>
                                <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Synced</span>
                                    <span id="cntSynced" class="text-sm font-semibold text-zinc-900 font-mono mt-0.5 block">0</span>
                                </div>
                                <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Skipped</span>
                                    <span id="cntSkipped" class="text-sm font-semibold text-zinc-900 font-mono mt-0.5 block">0</span>
                                </div>
                                <div class="bg-zinc-50 border border-zinc-200/80 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-semibold text-zinc-500 block tracking-wider">Failed</span>
                                    <span id="cntFailed" class="text-sm font-semibold text-zinc-900 font-mono mt-0.5 block">0</span>
                                </div>
                            </div>

                            <!-- Realtime Live Log Terminal Window -->
                            <div>
                                <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 mb-1.5">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-terminal text-[10px] text-zinc-400"></i> Terminal Output</span>
                                    <span class="text-[10px]">Real-time per-item status</span>
                                </div>
                                <div id="syncLiveConsole" class="w-full h-44 bg-zinc-900 text-zinc-200 rounded-md p-3 font-mono text-[11px] overflow-y-auto terminal-scrollbar flex flex-col gap-1 border border-zinc-800">
                                    <div class="text-zinc-500 italic">[System ready] Click "Sync All Products" to trigger live pipeline.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Sync Filter Configuration Card -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div>
                                <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-zinc-400 text-xs"></i>
                                    <span>Category Sync Filter Configuration</span>
                                </h2>
                                <p class="text-xs text-zinc-500 mt-0.5">Control which product categories from Srishringarr auto-sync and bulk-sync to Yosshitaneha.</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllCategories(true)" class="text-xs text-zinc-500 hover:text-zinc-900 font-medium px-2 py-1">Select All</button>
                                <span class="text-zinc-300">|</span>
                                <button type="button" onclick="selectAllCategories(false)" class="text-xs text-zinc-500 hover:text-zinc-900 font-medium px-2 py-1">Deselect All</button>
                                <button type="button" id="btnSaveConfig" onclick="saveCategoryConfig()" class="shadcn-btn shadcn-btn-primary ml-1">
                                    <i class="fas fa-check text-[10px]" id="saveIcon"></i>
                                    <span>Save Configuration</span>
                                </button>
                            </div>
                        </div>

                        <?php
                            $syncAll = !empty($syncSettings['sync_all']);
                            $enabledCats = $syncSettings['enabled_categories'] ?? [];
                        ?>

                        <form id="formCatConfig" class="p-6 space-y-5">
                            <!-- Sync All Override Toggle -->
                            <div class="flex items-center gap-3 bg-zinc-50 p-3.5 rounded-lg border border-zinc-200">
                                <input type="checkbox" id="chkSyncAll" name="sync_all" value="1" <?php echo $syncAll ? 'checked' : ''; ?> onchange="toggleSyncAllMode()" class="w-4 h-4 accent-zinc-900 rounded cursor-pointer">
                                <div>
                                    <label for="chkSyncAll" class="text-xs font-semibold text-zinc-900 cursor-pointer select-none">
                                        Sync All Categories (Unrestricted - Overrides individual filters below)
                                    </label>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">When checked, every product will be synchronized regardless of its category assignment.</p>
                                </div>
                            </div>

                            <!-- Category Selection Columns -->
                            <div id="catSelectionGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-5 <?php echo $syncAll ? 'opacity-40 pointer-events-none' : ''; ?> transition-opacity">
                                
                                <!-- Garments / Apparel Categories -->
                                <div class="border border-zinc-200 rounded-lg p-4 bg-white flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-zinc-100 pb-2.5">
                                        <h3 class="text-xs font-semibold text-zinc-900 flex items-center gap-2">
                                            <i class="fas fa-tshirt text-zinc-400"></i>
                                            <span>Apparel & Garments Categories</span>
                                        </h3>
                                        <span class="shadcn-badge font-mono text-[10px]"><?php echo count($categories['Apparel']['children'] ?? []); ?> categories</span>
                                    </div>
                                    <div class="space-y-1 max-h-72 overflow-y-auto custom-scrollbar pr-1.5">
                                        <?php if (!empty($categories['Apparel']['children'])): ?>
                                            <?php foreach ($categories['Apparel']['children'] as $catKey => $catData): ?>
                                                <?php $isChecked = $syncAll || in_array($catKey, $enabledCats); ?>
                                                <label class="flex items-center justify-between p-2 rounded hover:bg-zinc-50 transition-colors cursor-pointer group">
                                                    <div class="flex items-center gap-2.5">
                                                        <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox w-4 h-4 accent-zinc-900 rounded cursor-pointer">
                                                        <span class="text-xs text-zinc-700 group-hover:text-zinc-900 font-medium"><?php echo htmlspecialchars($catData['name']); ?></span>
                                                    </div>
                                                    <span class="shadcn-badge font-mono text-[10px] text-zinc-400 bg-zinc-50"><?php echo (int)$catData['count']; ?> items</span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-xs text-zinc-400 py-3 text-center">No apparel categories found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Jewellery Categories & Subcategories -->
                                <div class="border border-zinc-200 rounded-lg p-4 bg-white flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-zinc-100 pb-2.5">
                                        <h3 class="text-xs font-semibold text-zinc-900 flex items-center gap-2">
                                            <i class="fas fa-gem text-zinc-400"></i>
                                            <span>Jewellery Hierarchy</span>
                                        </h3>
                                        <span class="shadcn-badge font-mono text-[10px]"><?php echo count($categories['Jewellery']['children'] ?? []); ?> nodes</span>
                                    </div>
                                    <div class="space-y-1 max-h-72 overflow-y-auto custom-scrollbar pr-1.5">
                                        <?php if (!empty($categories['Jewellery']['children'])): ?>
                                            <?php foreach ($categories['Jewellery']['children'] as $catKey => $catData): ?>
                                                <?php 
                                                    $isChecked = $syncAll || in_array($catKey, $enabledCats);
                                                    $isSub = str_starts_with($catKey, 'jewel_child:');
                                                ?>
                                                <label class="flex items-center justify-between p-2 rounded hover:bg-zinc-50 transition-colors cursor-pointer group <?php echo $isSub ? 'ml-4 bg-zinc-50/50' : ''; ?>">
                                                    <div class="flex items-center gap-2.5">
                                                        <?php if ($isSub): ?>
                                                            <span class="text-zinc-300 font-mono text-xs select-none">└─</span>
                                                        <?php endif; ?>
                                                        <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox w-4 h-4 accent-zinc-900 rounded cursor-pointer">
                                                        <span class="text-xs <?php echo $isSub ? 'text-zinc-600 font-normal' : 'text-zinc-900 font-semibold'; ?> group-hover:text-zinc-900">
                                                            <?php echo htmlspecialchars($catData['name']); ?>
                                                        </span>
                                                    </div>
                                                    <span class="shadcn-badge font-mono text-[10px] text-zinc-400 bg-zinc-50"><?php echo (int)$catData['count']; ?> items</span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-xs text-zinc-400 py-3 text-center">No jewellery categories found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- Sync Audit Logs Table -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div>
                                <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-list-alt text-zinc-400 text-xs"></i>
                                    <span>Product Sync Audit Log</span>
                                </h2>
                                <span class="text-xs text-zinc-400" id="logCountLabel">Showing <?php echo count($logs); ?> most recent sync transactions</span>
                            </div>

                            <!-- Search Filter for Logs -->
                            <div class="flex items-center gap-2">
                                <div class="search-input-wrap">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" id="logSearch" placeholder="Filter by SKU or message..." autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <?php if (empty($logs)): ?>
                            <div class="py-16 text-center text-zinc-400 text-xs">
                                <i class="fas fa-exchange-alt text-3xl text-zinc-300 mb-2 block"></i>
                                No synchronization logs recorded yet.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                                <table class="shadcn-table" id="logsTable">
                                    <thead class="sticky top-0 z-10">
                                        <tr>
                                            <th style="width: 170px;">Date & Time</th>
                                            <th style="width: 130px;">SKU</th>
                                            <th style="width: 110px;">Type</th>
                                            <th style="width: 100px;">Trigger</th>
                                            <th style="width: 110px;">Status</th>
                                            <th>Log Message</th>
                                        </tr>
                                    </thead>
                                    <tbody id="logsTableBody">
                                        <?php foreach ($logs as $log): 
                                            $searchBlob = strtolower(($log['sku'] ?? '') . ' ' . ($log['message'] ?? '') . ' ' . ($log['product_type'] ?? ''));
                                        ?>
                                            <tr class="log-row" data-search="<?php echo htmlspecialchars($searchBlob); ?>">
                                                <td class="font-mono text-xs text-zinc-500 whitespace-nowrap">
                                                    <?php echo date('M j, Y g:i A', strtotime($log['synced_at'])); ?>
                                                </td>
                                                <td>
                                                    <span class="font-mono font-semibold text-zinc-900 text-xs">
                                                        <?php echo htmlspecialchars($log['sku']); ?>
                                                    </span>
                                                </td>
                                                <td class="capitalize text-zinc-600 text-xs">
                                                    <?php echo htmlspecialchars($log['product_type']); ?>
                                                </td>
                                                <td>
                                                    <span class="shadcn-badge font-mono text-[10px]">
                                                        <?php echo htmlspecialchars(strtoupper($log['sync_mode'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($log['status'] === 'success'): ?>
                                                        <span class="shadcn-badge text-zinc-800">
                                                            <span class="status-dot status-dot-success"></span>
                                                            <span>Success</span>
                                                        </span>
                                                    <?php elseif ($log['status'] === 'skipped'): ?>
                                                        <span class="shadcn-badge text-zinc-500">
                                                            <span class="status-dot status-dot-skipped"></span>
                                                            <span>Skipped</span>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="shadcn-badge text-zinc-800">
                                                            <span class="status-dot status-dot-failed"></span>
                                                            <span>Failed</span>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-xs text-zinc-600 max-w-md truncate" title="<?php echo htmlspecialchars($log['message']); ?>">
                                                    <?php echo htmlspecialchars($log['message']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-box"></div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        // Toast notification helper
        function showToast(text, isError = false) {
            const toastBox = document.getElementById('toast-box');
            const msg = document.createElement('div');
            msg.className = 'toast-msg';
            msg.innerHTML = `
                <i class="fas ${isError ? 'fa-exclamation-circle text-rose-400' : 'fa-check-circle text-emerald-400'}"></i>
                <span>${text}</span>
            `;
            toastBox.appendChild(msg);
            setTimeout(() => {
                msg.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                msg.style.opacity = '0';
                msg.style.transform = 'translateY(6px)';
                setTimeout(() => msg.remove(), 250);
            }, 3500);
        }

        // Toggle Sync All Categories Mode
        function toggleSyncAllMode() {
            const chkSyncAll = document.getElementById('chkSyncAll');
            const grid = document.getElementById('catSelectionGrid');
            if (chkSyncAll.checked) {
                grid.classList.add('opacity-40', 'pointer-events-none');
            } else {
                grid.classList.remove('opacity-40', 'pointer-events-none');
            }
        }

        // Select / Deselect All Categories
        function selectAllCategories(state) {
            const chkSyncAll = document.getElementById('chkSyncAll');
            if (chkSyncAll.checked) {
                chkSyncAll.checked = false;
                toggleSyncAllMode();
            }
            const checkboxes = document.querySelectorAll('.cat-checkbox');
            checkboxes.forEach(cb => cb.checked = state);
        }

        // Save Category Filter Configuration via AJAX
        function saveCategoryConfig() {
            const btn = document.getElementById('btnSaveConfig');
            const icon = document.getElementById('saveIcon');
            const form = document.getElementById('formCatConfig');
            const formData = new FormData(form);

            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin text-[10px]';

            fetch('index.php?controller=sync&action=saveSettings', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                icon.className = 'fas fa-check text-[10px]';

                if (data.success) {
                    showToast(data.message || 'Configuration saved successfully!');
                } else {
                    showToast(data.message || 'Error saving configuration', true);
                }
            })
            .catch(err => {
                btn.disabled = false;
                icon.className = 'fas fa-check text-[10px]';
                showToast('Network error while saving settings: ' + err, true);
            });
        }

        // Filter Audit Logs in Real-time
        const logSearchInput = document.getElementById('logSearch');
        if (logSearchInput) {
            logSearchInput.addEventListener('input', function(e) {
                const term = e.target.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.log-row');
                let visible = 0;
                rows.forEach(r => {
                    const blob = r.getAttribute('data-search') || '';
                    if (!term || blob.includes(term)) {
                        r.style.display = '';
                        visible++;
                    } else {
                        r.style.display = 'none';
                    }
                });
                const countLabel = document.getElementById('logCountLabel');
                if (countLabel) {
                    countLabel.textContent = `Showing ${visible} of ${rows.length} sync transactions`;
                }
            });
        }

        // Live Bulk Sync Pipeline
        let isSyncCancelled = false;

        async function startBulkSync() {
            if (!confirm("Are you sure you want to trigger product synchronization from Srishringarr to Yosshitaneha? You will monitor real-time progress for each product.")) return;

            isSyncCancelled = false;

            const btn = document.getElementById('btnBulkSync');
            const icon = document.getElementById('syncIcon');
            const alertBox = document.getElementById('syncStatusAlert');
            const statusMsg = document.getElementById('syncStatusMsg');
            const statusTitle = document.getElementById('syncStatusTitle');
            const progressBar = document.getElementById('syncProgressBar');
            const percentBadge = document.getElementById('syncPercentBadge');
            const consoleBox = document.getElementById('syncLiveConsole');
            const btnCancel = document.getElementById('btnCancelSync');

            const cntProcessed = document.getElementById('cntProcessed');
            const cntSynced = document.getElementById('cntSynced');
            const cntSkipped = document.getElementById('cntSkipped');
            const cntFailed = document.getElementById('cntFailed');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.classList.add('fa-spin');
            alertBox.classList.remove('hidden');
            btnCancel.style.display = 'inline-flex';

            statusTitle.textContent = "Bulk Syncing Products to Yosshitaneha";
            statusMsg.textContent = "Fetching product sync queue from database...";
            progressBar.style.width = '0%';
            percentBadge.textContent = '0%';
            consoleBox.innerHTML = `<div class="text-zinc-400 font-semibold">[${new Date().toLocaleTimeString()}] Fetching product sync queue...</div>`;

            let queue = [];
            try {
                const res = await fetch('index.php?controller=sync&action=getSyncQueue');
                const data = await res.json();
                if (!data.success || !data.items) {
                    statusMsg.textContent = "Failed to fetch sync queue: " + (data.message || "Unknown error");
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    icon.classList.remove('fa-spin');
                    return;
                }
                queue = data.items;
            } catch (err) {
                statusMsg.textContent = "Network error fetching sync queue: " + err;
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');
                return;
            }

            const total = queue.length;
            if (total === 0) {
                statusMsg.textContent = "No products found to sync with current category settings.";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');
                return;
            }

            let processed = 0;
            let synced = 0;
            let skipped = 0;
            let failed = 0;

            cntProcessed.textContent = `0 / ${total}`;
            cntSynced.textContent = '0';
            cntSkipped.textContent = '0';
            cntFailed.textContent = '0';

            consoleBox.innerHTML += `<div class="text-emerald-400 font-semibold">[${new Date().toLocaleTimeString()}] Queue initialized. ${total} items queued. Beginning live synchronization...</div>`;

            const throttleDelay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

            for (let i = 0; i < total; i++) {
                if (isSyncCancelled) {
                    consoleBox.innerHTML += `<div class="text-zinc-400 font-semibold mt-2">[${new Date().toLocaleTimeString()}] 🛑 Sync stopped by user. Processed ${processed} of ${total}.</div>`;
                    statusTitle.textContent = "Sync Cancelled by User";
                    statusMsg.textContent = `Stopped at ${processed} / ${total} products.`;
                    break;
                }

                const item = queue[i];
                const code = item.code || `ID:${item.id}`;
                const name = item.name ? (item.name.length > 40 ? item.name.substring(0, 40) + '...' : item.name) : 'Product';

                statusMsg.innerHTML = `<span class="font-semibold text-zinc-900">Syncing ${i + 1} of ${total}:</span> <span class="font-mono text-zinc-700">[${code}]</span> ${name} (${item.type})`;

                try {
                    const formData = new FormData();
                    formData.append('id', item.id);
                    formData.append('type', item.type);

                    const syncRes = await fetch('index.php?controller=sync&action=syncSingle', {
                        method: 'POST',
                        body: formData
                    });
                    const resData = await syncRes.json();

                    processed++;
                    const pct = Math.round((processed / total) * 100);
                    progressBar.style.width = pct + '%';
                    percentBadge.textContent = pct + '%';
                    cntProcessed.textContent = `${processed} / ${total}`;

                    const timeStr = new Date().toLocaleTimeString();

                    if (resData.success) {
                        if (resData.skipped) {
                            skipped++;
                            cntSkipped.textContent = skipped;
                            consoleBox.innerHTML += `<div><span class="text-zinc-600">[${timeStr}]</span> <span class="text-zinc-400 font-semibold">SKIPPED</span> <span class="font-bold text-zinc-300">[${code}]</span> ${name} &rarr; <span class="text-zinc-500 italic">Category disabled</span></div>`;
                        } else {
                            synced++;
                            cntSynced.textContent = synced;
                            consoleBox.innerHTML += `<div><span class="text-zinc-600">[${timeStr}]</span> <span class="text-emerald-400 font-semibold">SYNCED</span> <span class="font-bold text-zinc-200">[${code}]</span> ${name}</div>`;
                        }
                    } else {
                        failed++;
                        cntFailed.textContent = failed;
                        const err = resData.message || 'Unknown failure';
                        consoleBox.innerHTML += `<div><span class="text-zinc-600">[${timeStr}]</span> <span class="text-rose-400 font-semibold">FAILED</span> <span class="font-bold text-zinc-300">[${code}]</span> ${name} &rarr; <span class="text-rose-300">${err}</span></div>`;
                    }

                    consoleBox.scrollTop = consoleBox.scrollHeight;

                } catch (err) {
                    processed++;
                    failed++;
                    cntFailed.textContent = failed;
                    cntProcessed.textContent = `${processed} / ${total}`;
                    consoleBox.innerHTML += `<div><span class="text-zinc-600">[${new Date().toLocaleTimeString()}]</span> <span class="text-rose-400 font-semibold">ERROR</span> <span class="font-bold text-zinc-300">[${code}]</span> Network Error: ${err}</div>`;
                    consoleBox.scrollTop = consoleBox.scrollHeight;
                }

                // Throttle: wait 150ms between requests
                await throttleDelay(150);
            }

            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            icon.classList.remove('fa-spin');
            btnCancel.style.display = 'none';

            if (!isSyncCancelled) {
                statusTitle.textContent = "Bulk Synchronization Completed";
                statusMsg.textContent = `Processed ${total} products. Synced: ${synced}, Skipped: ${skipped}, Failed: ${failed}.`;
                consoleBox.innerHTML += `<div class="text-emerald-400 font-semibold mt-2">[${new Date().toLocaleTimeString()}] Completed! Processed ${total} items.</div>`;
                consoleBox.scrollTop = consoleBox.scrollHeight;
                showToast(`Sync completed: ${synced} synced, ${skipped} skipped, ${failed} failed.`);
            }
        }

        function cancelBulkSync() {
            isSyncCancelled = true;
            document.getElementById('syncStatusMsg').textContent = "Stopping sync pipeline after current item completes...";
        }
    </script>
</body>
</html>
