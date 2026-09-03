<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bulk AI Product Content Writer - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-zinc-950 font-sans text-zinc-300 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Bulk AI Content Writer';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <!-- Header Banner -->
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-950 via-indigo-950 to-zinc-900 border border-purple-800/40 p-6 shadow-xl">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2.5 bg-purple-500/20 border border-purple-500/30 rounded-xl text-purple-300">
                                        <i class="fas fa-wand-magic-sparkles text-xl"></i>
                                    </div>
                                    <div>
                                        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2.5">
                                            AI Bulk Product Content Writer
                                            <span class="text-[10px] font-bold tracking-wider uppercase px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                Gemini Vision Multimodal
                                            </span>
                                        </h1>
                                        <p class="text-xs text-zinc-400 mt-1">
                                            Examines photoshoot images and category taxonomy with Gemini Vision to generate SEO titles, summaries, and descriptions in bulk.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <?php if ($hasApiKey): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <i class="fas fa-circle-check"></i> Gemini Connected
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                        <i class="fas fa-triangle-exclamation"></i> Set API Key in secrets.php
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Card with Dedicated Filters -->
                    <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-5 backdrop-blur-sm shadow-sm space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                            
                            <!-- Category Dropdown -->
                            <div class="lg:col-span-4">
                                <label class="block text-xs font-bold text-zinc-300 mb-1.5">
                                    <i class="fas fa-folder-tree text-purple-400 mr-1"></i> Category Selection
                                </label>
                                <select id="cat_filter" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-200 focus:border-purple-500 focus:outline-none transition-colors">
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
                                <label class="block text-xs font-bold text-zinc-300 mb-1.5">
                                    <i class="fas fa-filter text-purple-400 mr-1"></i> Quality Preset
                                </label>
                                <select id="status_filter" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-200 focus:border-purple-500 focus:outline-none transition-colors">
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
                                <label class="block text-xs font-bold text-zinc-300 mb-1.5">
                                    Batch Limit
                                </label>
                                <select id="limit_filter" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-2.5 py-2 text-xs text-zinc-200 focus:border-purple-500 focus:outline-none transition-colors">
                                    <option value="25">25 items</option>
                                    <option value="50" selected>50 items</option>
                                    <option value="100">100 items</option>
                                    <option value="200">200 items</option>
                                </select>
                            </div>

                            <!-- Load Button -->
                            <div class="lg:col-span-2">
                                <button type="button" id="load_products_btn" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-2 px-3 rounded-lg text-xs transition-colors flex items-center justify-center gap-1.5 shadow-md">
                                    <i class="fas fa-sync-alt"></i> Load Products
                                </button>
                            </div>
                        </div>

                        <!-- Dedicated Specific Search Inputs Row -->
                        <div class="pt-2 border-t border-zinc-800/60 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Dedicated Product Name Filter -->
                            <div>
                                <label class="block text-[11px] font-bold text-purple-300 mb-1">
                                    <i class="fas fa-tag mr-1"></i> Filter by Product Name / Title:
                                </label>
                                <div class="relative">
                                    <input type="text" id="name_filter" placeholder="e.g. 1 (exact '1') or keyword..." class="w-full bg-zinc-950 border border-purple-500/40 rounded-lg pl-3 pr-14 py-1.5 text-xs text-zinc-200 focus:border-purple-400 focus:outline-none transition-colors">
                                    <button type="button" onclick="setNameFilter('1')" class="absolute right-1 top-1 bottom-1 px-2 text-[10px] font-bold bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded border border-purple-500/30 transition-colors">
                                        = '1'
                                    </button>
                                </div>
                            </div>

                            <!-- Dedicated Description Filter -->
                            <div>
                                <label class="block text-[11px] font-bold text-purple-300 mb-1">
                                    <i class="fas fa-align-left mr-1"></i> Filter by Description:
                                </label>
                                <div class="relative">
                                    <input type="text" id="desc_filter" placeholder="e.g. 1 or keyword..." class="w-full bg-zinc-950 border border-purple-500/40 rounded-lg pl-3 pr-14 py-1.5 text-xs text-zinc-200 focus:border-purple-400 focus:outline-none transition-colors">
                                    <button type="button" onclick="setDescFilter('1')" class="absolute right-1 top-1 bottom-1 px-2 text-[10px] font-bold bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded border border-purple-500/30 transition-colors">
                                        = '1'
                                    </button>
                                </div>
                            </div>

                            <!-- Dedicated SKU Filter -->
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-400 mb-1 flex items-center justify-between">
                                    <span><i class="fas fa-barcode mr-1 text-purple-400"></i> Filter by SKU Code(s):</span>
                                    <span id="sku_count_badge" class="hidden text-[10px] px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 font-normal"></span>
                                </label>
                                <input type="text" id="sku_filter" placeholder="e.g. k2067, set1014 or space separated..." class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-1.5 text-xs text-zinc-200 focus:border-purple-500 focus:outline-none transition-colors">
                                <p class="text-[10px] text-zinc-500 mt-1">Separate multiple SKUs with comma or space</p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Card (Hidden by default) -->
                    <div id="progress_card" class="hidden bg-purple-950/40 border border-purple-800/60 rounded-xl p-5 shadow-lg">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-xs font-bold text-purple-200 flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-purple-400"></i>
                                <span id="progress_title">Gemini Vision is analyzing products...</span>
                            </div>
                            <div class="text-xs font-bold text-purple-300" id="progress_counter">0 / 0</div>
                        </div>

                        <div class="w-full h-2 bg-zinc-800 rounded-full overflow-hidden mb-2">
                            <div id="progress_bar" class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                        </div>

                        <div class="flex justify-between items-center text-xs text-zinc-400">
                            <span id="current_task_status">Starting queue...</span>
                            <button type="button" id="stop_queue_btn" class="px-2.5 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 rounded text-xs font-bold transition-colors">
                                <i class="fas fa-stop mr-1"></i> Stop Queue
                            </button>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 text-xs font-bold text-zinc-300 cursor-pointer">
                                <input type="checkbox" id="select_all_cb" class="rounded bg-zinc-950 border-zinc-700 text-purple-600 focus:ring-0 w-4 h-4 cursor-pointer">
                                <span>Select All Visible</span>
                            </label>
                            <span id="selected_count_badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">0 selected</span>
                            <span id="matched_total_badge" class="text-xs text-zinc-500">Found 0 products</span>
                        </div>

                        <div class="flex items-center gap-2.5 flex-wrap">
                            <button type="button" id="generate_selected_btn" disabled class="px-4 py-2 bg-purple-600 hover:bg-purple-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs rounded-lg transition-all flex items-center gap-2 shadow-md">
                                <i class="fas fa-wand-magic-sparkles"></i> Generate AI Content (<span id="btn_gen_count">0</span>)
                            </button>

                            <button type="button" id="auto_generate_save_btn" disabled class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs rounded-lg transition-all flex items-center gap-2 shadow-md">
                                <i class="fas fa-bolt"></i> 1-Click Generate &amp; Save (<span id="btn_auto_count">0</span>)
                            </button>

                            <button type="button" id="save_all_btn" disabled class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs rounded-lg transition-all flex items-center gap-2 border border-zinc-700 shadow-md">
                                <i class="fas fa-save"></i> Save All to DB (<span id="btn_save_count">0</span>)
                            </button>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                        <div class="max-h-[700px] overflow-y-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead class="bg-zinc-950 text-zinc-400 uppercase tracking-wider text-[10px] sticky top-0 z-10 border-b border-zinc-800">
                                    <tr>
                                        <th class="py-3 px-4 w-10 text-center"></th>
                                        <th class="py-3 px-4 w-16">Image</th>
                                        <th class="py-3 px-4 w-28">SKU / Type</th>
                                        <th class="py-3 px-4 w-72">Product Title (Name)</th>
                                        <th class="py-3 px-4 w-64">Short Summary</th>
                                        <th class="py-3 px-4">Detailed Description &amp; Features</th>
                                        <th class="py-3 px-4 w-32 text-center">Status</th>
                                        <th class="py-3 px-4 w-24 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product_tbody" class="divide-y divide-zinc-800/60">
                                    <tr>
                                        <td colspan="8" class="text-center py-16 text-zinc-500">
                                            <i class="fas fa-mouse-pointer text-3xl mb-3 block text-zinc-700"></i>
                                            Select a category and click <b class="text-zinc-300">"Load Products"</b> to start generating AI titles and descriptions.
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
        loadProductsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-purple-400"><i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i> Fetching products...</td></tr>`;

        try {
            const url = `index.php?controller=product&action=bulkAiLoadProducts&category=${encodeURIComponent(catVal)}&filter_type=${encodeURIComponent(filter)}&name_filter=${encodeURIComponent(nameVal)}&desc_filter=${encodeURIComponent(descVal)}&sku_filter=${encodeURIComponent(skuVal)}&limit=${encodeURIComponent(limit)}`;
            const res = await fetch(url);
            const data = await res.json();

            loadProductsBtn.disabled = false;
            loadProductsBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load Products';

            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-red-400"><i class="fas fa-exclamation-circle mr-1"></i> ${data.error || 'Failed to load products.'}</td></tr>`;
                return;
            }

            loadedProducts = data.products || [];
            matchedTotalBadge.textContent = `Found ${data.total_count} products (Showing ${loadedProducts.length})`;
            renderTable(loadedProducts);
        } catch (err) {
            loadProductsBtn.disabled = false;
            loadProductsBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load Products';
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-red-400">Network error: ${err.message}</td></tr>`;
        }
    }

    function renderTable(products) {
        if (products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-zinc-500"><i class="fas fa-box-open text-2xl mb-2 block text-zinc-700"></i> No products match the filter criteria.</td></tr>`;
            updateSelectionUI();
            return;
        }

        let html = '';
        products.forEach((p) => {
            const imgUrl = p.image_url ? p.image_url : 'assets/placeholder.png';

            html += `
                <tr id="row-${p.type}-${p.id}" data-id="${p.id}" data-type="${p.type}" class="hover:bg-zinc-800/30 transition-colors align-top">
                    <td class="py-3 px-4 text-center pt-4">
                        <input type="checkbox" class="row-checkbox rounded bg-zinc-950 border-zinc-700 text-purple-600 focus:ring-0 w-4 h-4 cursor-pointer" value="${p.id}" data-type="${p.type}" checked>
                    </td>
                    <td class="py-3 px-4 pt-3.5">
                        <img src="${imgUrl}" alt="${p.code}" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'56\' viewBox=\'0 0 48 56\'%3E%3Crect width=\'48\' height=\'56\' fill=\'%2318181b\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2371717a\' font-size=\'9\'%3ENo Image%3C/text%3E%3C/svg%3E';" class="w-12 h-14 object-cover rounded-lg border border-zinc-800 bg-zinc-950 shadow-xs">
                    </td>
                    <td class="py-3 px-4 pt-3.5">
                        <strong class="text-purple-400 font-mono text-xs block">${p.code}</strong>
                        <span class="inline-block text-[9px] px-1.5 py-0.5 rounded bg-zinc-800 text-zinc-400 uppercase font-bold tracking-tight mt-1">${p.type}</span>
                        <div class="text-[10px] text-zinc-500 mt-1 truncate max-w-[120px]">${p.category_name}</div>
                    </td>
                    <td class="py-3 px-4 pt-3">
                        <textarea id="name-${p.type}-${p.id}" rows="2" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-xs text-zinc-200 font-medium focus:border-purple-500 focus:outline-none transition-colors">${p.name}</textarea>
                    </td>
                    <td class="py-3 px-4 pt-3">
                        <textarea id="short-desc-${p.type}-${p.id}" rows="2" placeholder="AI short summary..." class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-[11px] text-zinc-300 focus:border-purple-500 focus:outline-none transition-colors">${p.short_desc || ''}</textarea>
                    </td>
                    <td class="py-3 px-4 pt-3">
                        <textarea id="desc-${p.type}-${p.id}" rows="2" placeholder="AI detailed description & features..." class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2 text-[11px] text-zinc-300 focus:border-purple-500 focus:outline-none transition-colors">${p.description || ''}</textarea>
                    </td>
                    <td class="py-3 px-4 text-center pt-4" id="status-cell-${p.type}-${p.id}">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-400 border border-zinc-700/60">
                            ⏳ Pending
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center pt-3 space-y-1.5">
                        <button type="button" onclick="generateSingle(${p.id}, '${p.type}')" class="w-full py-1 px-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 rounded text-[11px] font-bold transition-colors">
                            <i class="fas fa-wand-magic-sparkles"></i> AI
                        </button>
                        <button type="button" onclick="saveSingle(${p.id}, '${p.type}')" class="w-full py-1 px-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 border border-zinc-700 rounded text-[11px] font-semibold transition-colors">
                            <i class="fas fa-save"></i> Save
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

        if (statusCell) {
            statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30"><i class="fas fa-spinner fa-spin"></i> Vision AI...</span>`;
        }

        try {
            const res = await fetch(`index.php?controller=product&action=aiGenerateBulkContent&id=${productId}&type=${type}`);
            const data = await res.json();

            if (data.success) {
                if (nameInput && data.name) nameInput.value = data.name;
                if (shortDescInput && data.short_description) shortDescInput.value = data.short_description;
                if (descInput && data.description) descInput.value = data.description;

                if (statusCell) {
                    statusCell.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">🟢 Generated</span>`;
                }
                updateSaveButtonCount();
                return true;
            } else {
                if (statusCell) {
                    statusCell.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30" title="${data.error || 'Failed'}">❌ Error</span>`;
                }
                return false;
            }
        } catch (err) {
            if (statusCell) {
                statusCell.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">❌ Network</span>`;
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
            alert('Product title cannot be empty.');
            return false;
        }

        if (statusCell) {
            statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-400"><i class="fas fa-spinner fa-spin"></i> Saving...</span>`;
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
                    statusCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"><i class="fas fa-check"></i> Saved to DB</span>`;
                }
                return true;
            } else {
                alert('Save failed: ' + (data.error || 'Unknown error'));
                if (statusCell) {
                    statusCell.innerHTML = `<span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-400">❌ Save Error</span>`;
                }
                return false;
            }
        } catch (err) {
            alert('Network error: ' + err.message);
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
        progressTitle.textContent = autoSave ? 'Gemini Vision is Generating & Auto-Saving...' : 'Gemini Vision is Generating AI Content...';

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
            await new Promise(r => setTimeout(r, 500));
        }

        isQueueRunning = false;
        currentTaskStatus.textContent = `Completed ${completed} items (${successCount} successful).`;
        updateSelectionUI();
        updateSaveButtonCount();
    }

    generateSelectedBtn.addEventListener('click', () => runBatchQueue(false));
    autoGenerateSaveBtn.addEventListener('click', () => {
        if (confirm(`Run Gemini Vision and DIRECTLY save updated titles & descriptions to the database for ${document.querySelectorAll('.row-checkbox:checked').length} products?`)) {
            runBatchQueue(true);
        }
    });

    stopQueueBtn.addEventListener('click', () => {
        stopRequested = true;
        stopQueueBtn.textContent = 'Stopping...';
    });

    saveAllBtn.addEventListener('click', async () => {
        const rows = Array.from(document.querySelectorAll('#product_tbody tr'));
        const toSave = [];

        rows.forEach(r => {
            const pId = r.getAttribute('data-id');
            const pType = r.getAttribute('data-type') || 'jewellery';
            const nameInput = document.getElementById(`name-${pType}-${pId}`);
            const shortDescInput = document.getElementById(`short-desc-${pType}-${pId}`);
            const descInput = document.getElementById(`desc-${pType}-${pId}`);

            if (pId && nameInput && nameInput.value.trim()) {
                toSave.push({
                    id: pId,
                    type: pType,
                    name: nameInput.value.trim(),
                    short_description: shortDescInput ? shortDescInput.value.trim() : '',
                    description: descInput ? descInput.value.trim() : ''
                });
            }
        });

        if (toSave.length === 0) {
            alert('No generated products found to save.');
            return;
        }

        if (!confirm(`Save all ${toSave.length} product titles and descriptions to the database?`)) {
            return;
        }

        saveAllBtn.disabled = true;
        saveAllBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Saving ${toSave.length}...`;

        let saved = 0;
        for (let item of toSave) {
            const ok = await saveSingle(item.id, item.type);
            if (ok) saved++;
        }

        saveAllBtn.disabled = false;
        saveAllBtn.innerHTML = `<i class="fas fa-save"></i> Save All to DB (<span id="btn_save_count">${saved}</span>)`;
        alert(`Successfully saved ${saved} products to the database!`);
    });

    loadProductsBtn.addEventListener('click', loadProducts);
    catFilter.addEventListener('change', loadProducts);
    statusFilter.addEventListener('change', loadProducts);
    limitFilter.addEventListener('change', loadProducts);

    // Live SKU count badge update
    skuFilter.addEventListener('input', function() {
        const val = this.value.trim();
        const badge = document.getElementById('sku_count_badge');
        if (!badge) return;
        if (!val) {
            badge.classList.add('hidden');
            return;
        }
        const skus = val.split(/[\r\n,\s]+/).filter(s => s.trim().length > 0);
        if (skus.length > 1) {
            badge.textContent = `${skus.length} SKUs`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });

    // Press Enter to load products in text filter inputs
    [nameFilter, descFilter, skuFilter].forEach(input => {
        if (input) {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    loadProducts();
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
</body>
</html>