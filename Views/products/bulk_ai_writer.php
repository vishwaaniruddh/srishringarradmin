<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bulk AI Content Writer - Srishringarr</title>
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

        .shadcn-btn-outline {
            background: #ffffff;
            border-color: #e4e4e7;
            color: #09090b;
        }
        .shadcn-btn-outline:hover {
            background: #f4f4f5;
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
        .status-dot-warning { background-color: #f59e0b; }
        .status-dot-neutral { background-color: #94a3b8; }
        .status-dot-danger { background-color: #ef4444; }

        /* Form Controls */
        .field-input, .field-select {
            height: 34px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
            font-family: inherit;
        }
        .field-input:focus, .field-select:focus {
            border-color: #09090b;
        }

        .table-textarea {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 6px 8px;
            font-size: 12px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
            font-family: inherit;
            line-height: 1.4;
            resize: vertical;
        }
        .table-textarea:focus {
            border-color: #09090b;
            background: #fafafa;
        }

        /* ShadCN Table Standard */
        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .shadcn-table th {
            background: #fafafa;
            padding: 9px 14px;
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
            padding: 10px 14px;
            vertical-align: top;
            border-bottom: 1px solid #f4f4f5;
            font-size: 12.5px;
            color: #09090b;
        }
        .shadcn-table tbody tr:hover td {
            background-color: #fbfbfb;
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
            $pageTitle = 'Bulk AI Content Writer';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">AI Bulk Product Content Writer</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    Multimodal Vision
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Examines photoshoot imagery and category taxonomy with Multimodal Vision AI to generate high-converting SEO titles, summaries, and descriptions in bulk.</p>
                        </div>

                        <!-- AI Provider Selector & Status -->
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <div class="flex items-center gap-2 bg-white border border-zinc-200 p-1.5 rounded-lg shadow-xs">
                                <span class="text-[11px] font-semibold text-zinc-500 uppercase px-2 tracking-wider">AI Model:</span>
                                <select id="ai_provider_select" class="field-select" style="height: 28px; font-size: 12px; padding: 0 8px; border: 1px solid #e4e4e7;">
                                    <?php if (!empty($hasGemini)): ?>
                                        <option value="gemini" selected>Google Gemini (Gemini Flash)</option>
                                    <?php endif; ?>
                                    <?php if (!empty($hasOpenAi)): ?>
                                        <option value="openai" <?php echo empty($hasGemini) ? 'selected' : ''; ?>>OpenAI (GPT-4o mini Vision)</option>
                                    <?php endif; ?>
                                    <?php if (empty($hasOpenAi) && empty($hasGemini)): ?>
                                        <option value="gemini">Google Gemini (Needs Key in secrets.php)</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if ($hasApiKey): ?>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-success mr-1"></span>
                                    AI Active
                                </span>
                            <?php else: ?>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-danger mr-1"></span>
                                    Configure API Key
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Filter Control Card -->
                    <div class="shadcn-card mb-6">
                        <div class="p-5 space-y-4">
                            <!-- Main Filter Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                                <!-- Category Selection -->
                                <div class="lg:col-span-4">
                                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                                        <i class="fas fa-folder-tree text-zinc-400 mr-1"></i> Category Selection
                                    </label>
                                    <select id="cat_filter" class="field-select w-full">
                                        <option value="">-- All Categories (Jewellery & Apparel) --</option>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $groupName => $groupData): ?>
                                                <optgroup label="<?php echo htmlspecialchars($groupName); ?> (<?php echo $groupData['count']; ?>)">
                                                    <?php foreach ($groupData['children'] as $catKey => $catInfo): ?>
                                                        <option value="<?php echo htmlspecialchars($catKey); ?>">
                                                            <?php echo htmlspecialchars($catInfo['name']); ?> (<?php echo $catInfo['count']; ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Quality Preset Filter -->
                                <div class="lg:col-span-4">
                                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                                        <i class="fas fa-filter text-zinc-400 mr-1"></i> Quality Preset
                                    </label>
                                    <select id="status_filter" class="field-select w-full">
                                        <option value="name_or_desc_is_1">⚠️ Name or Description is '1' (Raw Imports)</option>
                                        <option value="name_is_1">🎯 Exact Name is '1'</option>
                                        <option value="desc_is_1">🎯 Exact Description is '1'</option>
                                        <option value="needs_content" selected>⚠️ Needs AI Content (Name is '1'/SKU/Missing Desc)</option>
                                        <option value="missing_desc">📝 Missing Detailed Description</option>
                                        <option value="missing_short_desc">📄 Missing Short Summary</option>
                                        <option value="all">📋 All Products</option>
                                    </select>
                                </div>

                                <!-- Batch Limit -->
                                <div class="lg:col-span-2">
                                    <label class="block text-xs font-semibold text-zinc-700 mb-1.5">
                                        Batch Limit
                                    </label>
                                    <select id="limit_filter" class="field-select w-full">
                                        <option value="25">25 items</option>
                                        <option value="50" selected>50 items</option>
                                        <option value="100">100 items</option>
                                        <option value="200">200 items</option>
                                    </select>
                                </div>

                                <!-- Load Button -->
                                <div class="lg:col-span-2">
                                    <button type="button" id="load_products_btn" class="shadcn-btn shadcn-btn-primary w-full" style="height: 34px;">
                                        <i class="fas fa-sync-alt text-[10px]"></i>
                                        <span>Load Products</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dedicated Specific Search Inputs Row -->
                            <div class="pt-3 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Product Name Filter -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-zinc-500 uppercase tracking-wider mb-1">
                                        <i class="fas fa-tag mr-1 text-zinc-400"></i> Title Filter:
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" id="name_filter" placeholder="e.g. 1 (exact '1') or keyword..." class="field-input w-full pr-14">
                                        <button type="button" onclick="setNameFilter('1')" class="absolute right-1 px-2 py-0.5 text-[10px] font-mono font-semibold bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded border border-zinc-200">
                                            = '1'
                                        </button>
                                    </div>
                                </div>

                                <!-- Description Filter -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-zinc-500 uppercase tracking-wider mb-1">
                                        <i class="fas fa-align-left mr-1 text-zinc-400"></i> Description Filter:
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" id="desc_filter" placeholder="e.g. 1 or keyword..." class="field-input w-full pr-14">
                                        <button type="button" onclick="setDescFilter('1')" class="absolute right-1 px-2 py-0.5 text-[10px] font-mono font-semibold bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded border border-zinc-200">
                                            = '1'
                                        </button>
                                    </div>
                                </div>

                                <!-- SKU Filter -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">
                                            <i class="fas fa-barcode mr-1 text-zinc-400"></i> SKU Code(s):
                                        </label>
                                        <span id="sku_count_badge" class="hidden text-[10px] px-1.5 py-0.2 rounded bg-zinc-100 text-zinc-600 font-mono"></span>
                                    </div>
                                    <input type="text" id="sku_filter" placeholder="e.g. k2067, set1014 or space separated..." class="field-input w-full">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Card (Hidden by default) -->
                    <div id="progress_card" class="shadcn-card hidden mb-6" style="border-color: #09090b;">
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <div class="text-xs font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin text-zinc-600"></i>
                                    <span id="progress_title">Vision AI is analyzing products...</span>
                                </div>
                                <div class="text-xs font-mono font-semibold text-zinc-700" id="progress_counter">0 / 0</div>
                            </div>

                            <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden border border-zinc-200">
                                <div id="progress_bar" class="h-full bg-zinc-900 rounded-full transition-all duration-300" style="width: 0%;"></div>
                            </div>

                            <div class="flex justify-between items-center text-xs text-zinc-500">
                                <span id="current_task_status">Starting queue...</span>
                                <button type="button" id="stop_queue_btn" class="shadcn-btn shadcn-btn-sm">
                                    <i class="fas fa-stop text-[10px] text-zinc-400"></i>
                                    <span>Stop Queue</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="shadcn-card mb-4">
                        <div class="p-3.5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex items-center gap-3 flex-wrap">
                                <label class="flex items-center gap-2 text-xs font-semibold text-zinc-800 cursor-pointer select-none">
                                    <input type="checkbox" id="select_all_cb" class="w-4 h-4 accent-zinc-900 rounded cursor-pointer">
                                    <span>Select All Visible</span>
                                </label>
                                <span id="selected_count_badge" class="shadcn-badge font-mono text-[10px]">0 selected</span>
                                <span id="matched_total_badge" class="text-xs text-zinc-400">Found 0 products</span>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <button type="button" id="generate_selected_btn" disabled class="shadcn-btn shadcn-btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-wand-magic-sparkles text-[10px]"></i>
                                    <span>Generate AI Content (<span id="btn_gen_count">0</span>)</span>
                                </button>

                                <button type="button" id="auto_generate_save_btn" disabled class="shadcn-btn shadcn-btn-outline disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-bolt text-[10px] text-zinc-500"></i>
                                    <span>1-Click Generate &amp; Save (<span id="btn_auto_count">0</span>)</span>
                                </button>

                                <button type="button" id="save_all_btn" disabled class="shadcn-btn shadcn-btn-outline disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-save text-[10px] text-zinc-500"></i>
                                    <span>Save All to DB (<span id="btn_save_count">0</span>)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table Card -->
                    <div class="shadcn-card mb-6">
                        <div class="overflow-x-auto max-h-[720px] overflow-y-auto custom-scrollbar">
                            <table class="shadcn-table">
                                <thead class="sticky top-0 z-10">
                                    <tr>
                                        <th style="width: 40px; text-align: center;"></th>
                                        <th style="width: 65px;">Image</th>
                                        <th style="width: 130px;">SKU / Type</th>
                                        <th style="width: 290px;">Product Title (Name)</th>
                                        <th style="width: 260px;">Short Summary</th>
                                        <th>Detailed Description &amp; Features</th>
                                        <th style="width: 120px; text-align: center;">Status</th>
                                        <th style="width: 100px; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product_tbody">
                                    <tr>
                                        <td colspan="8" class="text-center py-16 text-zinc-400 text-xs">
                                            <i class="fas fa-mouse-pointer text-2xl mb-2 block text-zinc-300"></i>
                                            Select a category or quality filter and click <b class="text-zinc-700">"Load Products"</b> to review or generate content in bulk.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-box"></div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
    let loadedProducts = [];
    let isQueueRunning = false;
    let stopRequested = false;

    const catFilter = document.getElementById('cat_filter');
    const statusFilter = document.getElementById('status_filter');
    const nameFilter = document.getElementById('name_filter');
    const descFilter = document.getElementById('desc_filter');
    const skuFilter = document.getElementById('sku_filter');
    const limitFilter = document.getElementById('limit_filter');
    const loadProductsBtn = document.getElementById('load_products_btn');
    const tbody = document.getElementById('product_tbody');

    const selectAllCb = document.getElementById('select_all_cb');
    const selectedCountBadge = document.getElementById('selected_count_badge');
    const matchedTotalBadge = document.getElementById('matched_total_badge');
    const generateSelectedBtn = document.getElementById('generate_selected_btn');
    const btnGenCount = document.getElementById('btn_gen_count');
    const autoGenerateSaveBtn = document.getElementById('auto_generate_save_btn');
    const btnAutoCount = document.getElementById('btn_auto_count');
    const saveAllBtn = document.getElementById('save_all_btn');
    const btnSaveCount = document.getElementById('btn_save_count');

    const progressCard = document.getElementById('progress_card');
    const progressTitle = document.getElementById('progress_title');
    const progressCounter = document.getElementById('progress_counter');
    const progressBar = document.getElementById('progress_bar');
    const currentTaskStatus = document.getElementById('current_task_status');
    const stopQueueBtn = document.getElementById('stop_queue_btn');
    const aiProviderSelect = document.getElementById('ai_provider_select');

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

    function setNameFilter(val) {
        nameFilter.value = val;
        loadProducts();
    }

    function setDescFilter(val) {
        descFilter.value = val;
        loadProducts();
    }

    async function loadProducts() {
        const catVal = catFilter.value;
        const filter = statusFilter.value;
        const nameVal = nameFilter.value.trim();
        const descVal = descFilter.value.trim();
        const skuVal = skuFilter.value.trim();
        const limit = limitFilter.value;

        loadProductsBtn.disabled = true;
        loadProductsBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i>';
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-zinc-400 text-xs"><i class="fas fa-spinner fa-spin text-xl mb-2 block text-zinc-300"></i> Fetching products from catalog...</td></tr>`;

        try {
            const url = `index.php?controller=product&action=bulkAiLoadProducts&category=${encodeURIComponent(catVal)}&filter_type=${encodeURIComponent(filter)}&name_filter=${encodeURIComponent(nameVal)}&desc_filter=${encodeURIComponent(descVal)}&sku_filter=${encodeURIComponent(skuVal)}&limit=${encodeURIComponent(limit)}`;
            const res = await fetch(url);
            const data = await res.json();

            loadProductsBtn.disabled = false;
            loadProductsBtn.innerHTML = '<i class="fas fa-sync-alt text-[10px]"></i><span>Load Products</span>';

            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-zinc-500 text-xs"><i class="fas fa-exclamation-circle mr-1"></i> ${data.error || 'Failed to load products.'}</td></tr>`;
                return;
            }

            loadedProducts = data.products || [];
            matchedTotalBadge.textContent = `Found ${data.total_count} products (Showing ${loadedProducts.length})`;
            renderTable(loadedProducts);
        } catch (err) {
            loadProductsBtn.disabled = false;
            loadProductsBtn.innerHTML = '<i class="fas fa-sync-alt text-[10px]"></i><span>Load Products</span>';
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-zinc-500 text-xs">Network error: ${err.message}</td></tr>`;
        }
    }

    loadProductsBtn.addEventListener('click', loadProducts);

    function renderTable(products) {
        if (products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-zinc-400 text-xs"><i class="fas fa-box-open text-2xl mb-2 block text-zinc-300"></i> No products match the filter criteria.</td></tr>`;
            updateSelectionUI();
            return;
        }

        let html = '';
        products.forEach((p) => {
            const imgUrl = p.image_url ? p.image_url : 'assets/placeholder.png';

            html += `
                <tr id="row-${p.type}-${p.id}" data-id="${p.id}" data-type="${p.type}">
                    <td class="text-center pt-3">
                        <input type="checkbox" class="row-checkbox w-4 h-4 accent-zinc-900 rounded cursor-pointer" value="${p.id}" data-type="${p.type}" checked>
                    </td>
                    <td class="pt-2.5">
                        <img src="${imgUrl}" alt="${p.code}" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'56\' viewBox=\'0 0 48 56\'%3E%3Crect width=\'48\' height=\'56\' fill=\'%23f4f4f5\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2371717a\' font-size=\'9\'%3ENo Image%3C/text%3E%3C/svg%3E';" class="w-12 h-14 object-cover rounded border border-zinc-200 bg-zinc-50">
                    </td>
                    <td class="pt-2.5">
                        <strong class="font-mono text-xs text-zinc-900 block">${p.code}</strong>
                        <span class="shadcn-badge font-mono text-[9px] uppercase mt-1">${p.type}</span>
                        <div class="text-[11px] text-zinc-400 mt-1 truncate max-w-[130px]" title="${p.category_name}">${p.category_name}</div>
                    </td>
                    <td class="pt-2">
                        <textarea id="name-${p.type}-${p.id}" rows="2" class="table-textarea font-medium">${p.name}</textarea>
                    </td>
                    <td class="pt-2">
                        <textarea id="short-desc-${p.type}-${p.id}" rows="2" placeholder="AI short summary..." class="table-textarea text-zinc-600">${p.short_desc || ''}</textarea>
                    </td>
                    <td class="pt-2">
                        <textarea id="desc-${p.type}-${p.id}" rows="2" placeholder="AI detailed description & features..." class="table-textarea text-zinc-600">${p.description || ''}</textarea>
                    </td>
                    <td class="text-center pt-3" id="status-cell-${p.type}-${p.id}">
                        <span class="shadcn-badge font-mono text-[10px]">
                            <span class="status-dot status-dot-neutral"></span>
                            <span>Pending</span>
                        </span>
                    </td>
                    <td class="text-center pt-2.5 space-y-1">
                        <button type="button" onclick="generateSingle(${p.id}, '${p.type}')" class="shadcn-btn shadcn-btn-sm w-full" title="Generate with AI Vision">
                            <i class="fas fa-wand-magic-sparkles text-[10px]"></i> AI
                        </button>
                        <button type="button" onclick="saveSingle(${p.id}, '${p.type}')" class="shadcn-btn shadcn-btn-sm w-full" title="Save changes to Database">
                            <i class="fas fa-save text-[10px]"></i> Save
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        updateSelectionUI();

        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectionUI);
        });
    }

    function updateSelectionUI() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        selectedCountBadge.textContent = `${count} selected`;
        btnGenCount.textContent = count;
        btnAutoCount.textContent = count;

        if (count > 0 && !isQueueRunning) {
            generateSelectedBtn.disabled = false;
            autoGenerateSaveBtn.disabled = false;
        } else {
            generateSelectedBtn.disabled = true;
            autoGenerateSaveBtn.disabled = true;
        }
    }

    selectAllCb.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = isChecked;
        });
        updateSelectionUI();
    });

    async function generateSingle(productId, type) {
        const statusCell = document.getElementById(`status-cell-${type}-${productId}`);
        const nameInput = document.getElementById(`name-${type}-${productId}`);
        const shortDescInput = document.getElementById(`short-desc-${type}-${productId}`);
        const descInput = document.getElementById(`desc-${type}-${productId}`);
        const selectedProvider = aiProviderSelect ? aiProviderSelect.value : 'gemini';

        if (statusCell) {
            statusCell.innerHTML = `<span class="shadcn-badge font-mono text-[10px]"><i class="fas fa-spinner fa-spin text-zinc-500"></i> AI Vision...</span>`;
        }

        try {
            const res = await fetch(`index.php?controller=product&action=aiGenerateBulkContent&id=${productId}&type=${type}&ai_provider=${selectedProvider}`);
            const data = await res.json();

            if (data.success) {
                if (nameInput && data.name) nameInput.value = data.name;
                if (shortDescInput && data.short_description) shortDescInput.value = data.short_description;
                if (descInput && data.description) descInput.value = data.description;

                if (statusCell) {
                    statusCell.innerHTML = `<span class="shadcn-badge text-zinc-900 font-mono text-[10px]"><span class="status-dot status-dot-success"></span> Generated</span>`;
                }
                updateSaveButtonCount();
                return true;
            } else {
                if (statusCell) {
                    statusCell.innerHTML = `<span class="shadcn-badge text-zinc-700 font-mono text-[10px]" title="${data.error || 'Failed'}"><span class="status-dot status-dot-danger"></span> Error</span>`;
                }
                return false;
            }
        } catch (err) {
            if (statusCell) {
                statusCell.innerHTML = `<span class="shadcn-badge text-zinc-700 font-mono text-[10px]"><span class="status-dot status-dot-danger"></span> Network</span>`;
            }
            return false;
        }
    }

    async function saveSingle(productId, type) {
        const statusCell = document.getElementById(`status-cell-${type}-${productId}`);
        const nameInput = document.getElementById(`name-${type}-${productId}`);
        const shortDescInput = document.getElementById(`short-desc-${type}-${productId}`);
        const descInput = document.getElementById(`desc-${type}-${productId}`);

        const name = nameInput ? nameInput.value.trim() : '';
        const shortDesc = shortDescInput ? shortDescInput.value.trim() : '';
        const desc = descInput ? descInput.value.trim() : '';

        if (!name) {
            showToast('Product title cannot be empty', true);
            return false;
        }

        if (statusCell) {
            statusCell.innerHTML = `<span class="shadcn-badge font-mono text-[10px]"><i class="fas fa-spinner fa-spin"></i> Saving...</span>`;
        }

        try {
            const res = await fetch('index.php?controller=product&action=saveBulkAiContent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: productId,
                    type: type,
                    name: name,
                    short_description: shortDesc,
                    description: desc
                })
            });
            const data = await res.json();

            if (data.success) {
                if (statusCell) {
                    statusCell.innerHTML = `<span class="shadcn-badge text-zinc-900 font-mono text-[10px]"><span class="status-dot status-dot-success"></span> Saved</span>`;
                }
                showToast(`Product #${productId} saved to catalog.`);
                return true;
            } else {
                showToast('Save failed: ' + (data.error || 'Unknown error'), true);
                if (statusCell) {
                    statusCell.innerHTML = `<span class="shadcn-badge text-zinc-700 font-mono text-[10px]"><span class="status-dot status-dot-danger"></span> Error</span>`;
                }
                return false;
            }
        } catch (err) {
            showToast('Network error: ' + err.message, true);
            return false;
        }
    }

    function updateSaveButtonCount() {
        const generatedRows = document.querySelectorAll('#product_tbody tr');
        let count = 0;
        generatedRows.forEach(row => {
            const status = row.querySelector('span');
            if (status && (status.textContent.includes('Generated') || status.textContent.includes('Saved'))) {
                count++;
            }
        });
        btnSaveCount.textContent = count;
        if (count > 0 && !isQueueRunning) {
            saveAllBtn.disabled = false;
        }
    }

    async function runBatchQueue(autoSave = false) {
        const checked = Array.from(document.querySelectorAll('.row-checkbox:checked'));
        if (checked.length === 0) return;

        isQueueRunning = true;
        stopRequested = false;
        generateSelectedBtn.disabled = true;
        autoGenerateSaveBtn.disabled = true;

        progressCard.classList.remove('hidden');
        progressTitle.textContent = autoSave ? 'AI Vision is Generating & Auto-Saving...' : 'AI Vision is Generating Content...';

        const total = checked.length;
        let completed = 0;
        let successCount = 0;

        for (let i = 0; i < total; i++) {
            if (stopRequested) {
                currentTaskStatus.textContent = 'Queue stopped by user.';
                break;
            }

            const pId = checked[i].value;
            const pType = checked[i].getAttribute('data-type') || 'jewellery';
            const row = document.getElementById(`row-${pType}-${pId}`);
            const sku = row ? row.querySelector('strong').textContent : pId;

            progressCounter.textContent = `${i + 1} / ${total}`;
            progressBar.style.width = `${Math.round(((i + 1) / total) * 100)}%`;
            currentTaskStatus.textContent = `Analyzing image for SKU: ${sku}...`;

            const success = await generateSingle(pId, pType);
            if (success) {
                successCount++;
                if (autoSave) {
                    currentTaskStatus.textContent = `Saving SKU: ${sku} to database...`;
                    await saveSingle(pId, pType);
                }
            }

            completed++;
            await new Promise(r => setTimeout(r, 400));
        }

        isQueueRunning = false;
        currentTaskStatus.textContent = `Completed ${completed} items (${successCount} successful).`;
        showToast(`Batch completed: ${successCount} of ${completed} processed successfully.`);
        updateSelectionUI();
        updateSaveButtonCount();
    }

    generateSelectedBtn.addEventListener('click', () => runBatchQueue(false));
    autoGenerateSaveBtn.addEventListener('click', () => runBatchQueue(true));

    stopQueueBtn.addEventListener('click', () => {
        stopRequested = true;
        stopQueueBtn.textContent = 'Stopping...';
    });

    saveAllBtn.addEventListener('click', async () => {
        const rows = Array.from(document.querySelectorAll('#product_tbody tr'));
        const toSave = [];

        rows.forEach(r => {
            const id = r.getAttribute('data-id');
            const type = r.getAttribute('data-type');
            if (id && type) {
                toSave.push({ id, type });
            }
        });

        if (toSave.length === 0) return;

        saveAllBtn.disabled = true;
        saveAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i> Saving...';

        let savedCount = 0;
        for (const item of toSave) {
            const ok = await saveSingle(item.id, item.type);
            if (ok) savedCount++;
            await new Promise(r => setTimeout(r, 100));
        }

        saveAllBtn.disabled = false;
        saveAllBtn.innerHTML = '<i class="fas fa-save text-[10px]"></i><span>Save All to DB</span> (<span id="btn_save_count">0</span>)';
        showToast(`Saved ${savedCount} products to database.`);
    });
    </script>
</body>
</html>