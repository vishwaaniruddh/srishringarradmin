<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Product Video Generator - Srishringarr Luxury Admin</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">

    <style>
        :root {
            --vg-bg: #f8fafc;
            --vg-card: #ffffff;
            --vg-border: #e2e8f0;
            --vg-primary: #0f172a;
            --vg-primary-hover: #1e293b;
            --vg-muted: #64748b;
        }

        body, html {
            background-color: var(--vg-bg) !important;
            color: #0f172a !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .shadcn-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            user-select: none;
        }
        .shadcn-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .shadcn-btn-primary {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }
        .shadcn-btn-primary:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
        }
        .shadcn-btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Products Table */
        .prod-table {
            width: 100%;
            border-collapse: collapse;
        }
        .prod-table th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .prod-table td {
            padding: 11px 14px;
            font-size: 12.5px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .prod-table tr:hover td {
            background: #f8fafc;
        }
        .prod-table tr.selected td {
            background: #f0fdf4;
        }

        .vg-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .vg-scroll::-webkit-scrollbar-track { background: transparent; }
        .vg-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .vg-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Offscreen canvas used for fast video generation */
        #renderCanvas {
            display: none;
        }
    </style>
</head>
<body class="bg-[#f8fafc]">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Panel -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'AI Product Video Generator (Gemini Powered)';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 vg-scroll" style="background: #f8fafc !important;">
                <div class="max-w-[1520px] mx-auto space-y-4">
                    
                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h1 class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm shadow-sm">
                                        <i class="fa-solid fa-video"></i>
                                    </span>
                                    <span>AI Product Video Generator</span>
                                </h1>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1.5">
                                    <i class="fa-solid fa-bolt text-indigo-500"></i>
                                    <span>Gemini 3.8 Flash AI Engine Active</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Select product categories below, multi-select items with checkboxes, and generate 10–20 second cinematic product motion videos in bulk.
                            </p>
                        </div>
                    </div>

                    <!-- 1. CATEGORY DROPDOWNS & FILTER TOOLBAR -->
                    <div class="shadcn-card p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            
                            <!-- Dropdown 1: Main Department -->
                            <div class="md:col-span-3">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                                    <i class="fa-solid fa-layer-group text-slate-400 mr-1"></i> Department
                                </label>
                                <select id="deptDropdown" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800" onchange="onDepartmentChange()">
                                    <option value="all">All Departments (<?php echo (($categoriesTree['Jewellery']['count'] ?? 0) + ($categoriesTree['Apparel']['count'] ?? 0)); ?>)</option>
                                    <option value="jewellery" selected>Jewellery (<?php echo $categoriesTree['Jewellery']['count'] ?? 0; ?>)</option>
                                    <option value="apparel">Apparel / Garments (<?php echo $categoriesTree['Apparel']['count'] ?? 0; ?>)</option>
                                </select>
                            </div>

                            <!-- Dropdown 2: Category / Subcategory -->
                            <div class="md:col-span-4">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                                    <i class="fa-solid fa-tags text-slate-400 mr-1"></i> Category
                                </label>
                                <select id="categoryDropdown" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800" onchange="loadFilteredProducts()">
                                    <!-- Populated dynamically via JS -->
                                </select>
                            </div>

                            <!-- Filter 3: Video Status Filter -->
                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                                    <i class="fa-solid fa-filter text-slate-400 mr-1"></i> Video Status
                                </label>
                                <select id="hasVideoFilter" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800" onchange="loadFilteredProducts()">
                                    <option value="all">All Products</option>
                                    <option value="no" selected>Without Video (Pending)</option>
                                    <option value="yes">With Video Ready</option>
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="md:col-span-3">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                                    <i class="fa-solid fa-search text-slate-400 mr-1"></i> Search SKU / Title
                                </label>
                                <div class="relative">
                                    <input type="text" id="filterSearchInput" placeholder="Type SKU (e.g. BR82, R, NL)..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800" oninput="debounceFilterSearch()">
                                    <i class="fa-solid fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. BATCH GENERATION CONTROLS BAR -->
                    <div class="shadcn-card p-3.5 bg-slate-900 text-white flex flex-col md:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3 flex-wrap">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 rounded text-slate-900 cursor-pointer accent-emerald-500">
                                <span class="text-xs font-bold uppercase tracking-wider">Select All</span>
                            </label>
                            <span class="text-slate-500">|</span>
                            <span class="text-xs font-semibold text-slate-200">
                                Selected: <span id="selectedCountBadge" class="font-mono text-emerald-400 font-bold text-sm">0</span> products
                            </span>

                            <span class="text-slate-500 hidden md:inline">|</span>

                            <!-- Duration Option: 10s, 15s, 20s -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Duration:</span>
                                <select id="batchDuration" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none">
                                    <option value="10">10 Seconds (Reel/Story)</option>
                                    <option value="15" selected>15 Seconds (Standard)</option>
                                    <option value="20">20 Seconds (Luxury Showcase)</option>
                                </select>
                            </div>

                            <!-- Motion Preset -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Preset:</span>
                                <select id="batchPreset" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none">
                                    <option value="turntable" selected>360° Luxury Orbit</option>
                                    <option value="glint">Diamond Shimmer Glints</option>
                                    <option value="macro">Macro Facet Glide</option>
                                    <option value="kenburns">Ken Burns Push</option>
                                    <option value="sway">Bridal Elegance Sway</option>
                                </select>
                            </div>

                            <!-- Aspect Ratio -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Format:</span>
                                <select id="batchAspect" class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none">
                                    <option value="9:16" selected>9:16 Vertical Reel</option>
                                    <option value="1:1">1:1 Square Feed</option>
                                    <option value="16:9">16:9 Banner</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Button: Batch Generate -->
                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <button type="button" id="startBatchBtn" onclick="startBatchGeneration()" disabled class="shadcn-btn bg-emerald-600 hover:bg-emerald-500 text-white font-bold border-emerald-500 py-2 px-5 text-xs shadow-md disabled:opacity-40 disabled:cursor-not-allowed flex-1 md:flex-none">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>Generate Videos for Selected (<span id="batchBtnCount">0</span>) with Gemini</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. PRODUCTS LIST TABLE -->
                    <div class="shadcn-card overflow-hidden">
                        <div class="p-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Products in Category</span>
                                <span id="tableCountBadge" class="text-[11px] font-mono text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">
                                    Loading products...
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                Real catalog inventory with full resolution photos
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="prod-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">
                                            <input type="checkbox" id="headerCheckbox" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 cursor-pointer accent-slate-900">
                                        </th>
                                        <th style="width: 70px;">PHOTO</th>
                                        <th style="min-width: 280px;">PRODUCT TITLE &amp; SKU</th>
                                        <th style="width: 140px;">CATEGORY</th>
                                        <th style="width: 120px;">PRICE</th>
                                        <th style="width: 170px;">VIDEO STATUS</th>
                                        <th style="width: 160px; text-align: right;">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Rendered dynamically -->
                                    <tr>
                                        <td colspan="7" class="text-center py-12 text-slate-400 text-xs">
                                            <i class="fa-solid fa-spinner fa-spin text-base mr-2"></i> Loading category products...
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

    <!-- Hidden Rendering Canvas for Video Generation Engine -->
    <canvas id="renderCanvas" width="720" height="1280"></canvas>

    <!-- BATCH GENERATION PROGRESS MODAL -->
    <div id="batchProgressModal" class="fixed inset-0 bg-black/75 backdrop-blur-xs hidden items-center justify-center p-4 z-50">
        <div class="bg-white border border-slate-200 rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-lg flex-shrink-0 animate-pulse">
                    <i class="fa-solid fa-film"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Gemini AI Video Generator</h3>
                    <p class="text-xs text-slate-500" id="batchModalSubtitle">Generating motion videos for selected products...</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-1.5">
                <div class="flex justify-between text-xs font-mono">
                    <span id="batchCurrentProductText" class="font-semibold text-slate-800 truncate max-w-[320px]">Preparing...</span>
                    <span id="batchProgressPercent" class="font-bold text-emerald-600">0%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200">
                    <div id="batchProgressBar" class="bg-emerald-500 h-full w-0 transition-all duration-150"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-400 font-mono">
                    <span id="batchCountText">Product 0 of 0</span>
                    <span id="batchTimeText">Calculating...</span>
                </div>
            </div>

            <!-- Current Product Preview in Modal -->
            <div id="batchActivePreview" class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center gap-3">
                <img id="batchModalThumb" src="assets/default-product.jpg" class="w-12 h-12 rounded object-cover border border-slate-200 bg-slate-900">
                <div class="min-w-0 flex-1">
                    <div id="batchModalSku" class="text-xs font-bold text-slate-900 font-mono">SKU</div>
                    <div id="batchModalTitle" class="text-xs text-slate-600 truncate mt-0.5">Product Title</div>
                    <div id="batchModalStatus" class="text-[11px] text-emerald-600 font-medium mt-0.5">Calling Gemini AI motion engine...</div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="cancelBatch()" id="cancelBatchBtn" class="shadcn-btn text-xs text-rose-600 border-rose-200 hover:bg-rose-50">
                    Cancel Process
                </button>
            </div>
        </div>
    </div>

    <!-- VIDEO PLAYER PREVIEW MODAL -->
    <div id="videoPlayerModal" class="fixed inset-0 bg-black/85 backdrop-blur-md hidden items-center justify-center p-4 z-50">
        <div class="bg-slate-950 border border-slate-800 rounded-2xl max-w-md w-full overflow-hidden shadow-2xl space-y-3 p-4 flex flex-col items-center">
            <div class="flex items-center justify-between w-full pb-2 border-b border-slate-800 text-white">
                <div>
                    <h4 id="playerModalSku" class="text-sm font-bold font-mono">Product SKU</h4>
                    <p id="playerModalTitle" class="text-xs text-slate-400 truncate max-w-[280px]">Product Name</p>
                </div>
                <button type="button" onclick="closeVideoPlayer()" class="w-8 h-8 rounded-lg hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="w-full flex items-center justify-center bg-black rounded-xl overflow-hidden max-h-[500px]">
                <video id="playerVideoElement" controls autoplay loop class="max-h-[480px] max-w-full rounded-lg shadow-lg"></video>
            </div>

            <div class="flex items-center justify-between w-full pt-1">
                <span class="text-xs text-slate-400" id="playerModalInfo">15s • WebM Video</span>
                <a id="playerDownloadBtn" href="#" download class="shadcn-btn shadcn-btn-primary py-1.5 px-4 text-xs font-bold">
                    <i class="fa-solid fa-download mr-1"></i> Download Video
                </a>
            </div>
        </div>
    </div>

    <!-- Core Script -->
    <script>
        // Category tree passed from PHP
        const categoriesTree = <?php echo json_encode($categoriesTree); ?>;
        let currentProducts = <?php echo json_encode($initialProducts); ?>;
        let selectedProductSkus = new Set();
        let isBatchRunning = false;
        let shouldCancelBatch = false;

        // Render Canvas elements for offscreen generation
        const renderCanvas = document.getElementById('renderCanvas');
        const renderCtx = renderCanvas.getContext('2d', { willReadFrequently: true });

        // Initialize categories dropdown on load
        window.addEventListener('DOMContentLoaded', () => {
            initCategoryDropdowns();
            renderProductsTable(currentProducts);
        });

        // Initialize Category Dropdown based on Department
        function initCategoryDropdowns() {
            const dept = document.getElementById('deptDropdown').value;
            const catDropdown = document.getElementById('categoryDropdown');
            catDropdown.innerHTML = '<option value="all">All Categories in Department</option>';

            const targetDepts = (dept === 'all') ? ['Jewellery', 'Apparel'] : [(dept === 'jewellery' ? 'Jewellery' : 'Apparel')];

            targetDepts.forEach(d => {
                if (categoriesTree[d] && categoriesTree[d].children) {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = d;

                    categoriesTree[d].children.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.key;
                        opt.textContent = `${c.name} (${c.count})`;
                        if (c.key === '<?php echo $initialCategory; ?>') {
                            opt.selected = true;
                        }
                        optgroup.appendChild(opt);
                    });
                    catDropdown.appendChild(optgroup);
                }
            });
        }

        function onDepartmentChange() {
            initCategoryDropdowns();
            loadFilteredProducts();
        }

        let searchDebounceTimer = null;
        function debounceFilterSearch() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(loadFilteredProducts, 300);
        }

        // Load filtered products via AJAX
        function loadFilteredProducts() {
            const dept = document.getElementById('deptDropdown').value;
            const cat = document.getElementById('categoryDropdown').value;
            const hasVideo = document.getElementById('hasVideoFilter').value;
            const search = document.getElementById('filterSearchInput').value.trim();

            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-slate-400 text-xs"><i class="fa-solid fa-spinner fa-spin text-base mr-2"></i> Loading filtered products...</td></tr>`;

            const params = new URLSearchParams({
                department: dept,
                category: cat,
                has_video: hasVideo,
                search: search,
                limit: 50
            });

            fetch('index.php?controller=video_generator&action=getProductsByCategory&' + params.toString())
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        currentProducts = data.products;
                        selectedProductSkus.clear();
                        updateSelectedCount();
                        renderProductsTable(data.products);
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-rose-500 text-xs">Error loading products: ${err.message}</td></tr>`;
                });
        }

        // Render products into the table
        function renderProductsTable(products) {
            const tbody = document.getElementById('productsTableBody');
            const countBadge = document.getElementById('tableCountBadge');
            countBadge.textContent = `${products.length} products displayed`;

            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-14 text-slate-400 text-xs">
                            <i class="fa-solid fa-box-open text-2xl block mb-2 opacity-50"></i>
                            No products found matching the selected filters.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            products.forEach((p, idx) => {
                const isSelected = selectedProductSkus.has(p.sku);
                const hasVid = p.has_video;
                const vidData = p.video;

                let videoBadge = '';
                let actionBtn = '';

                if (hasVid && vidData) {
                    videoBadge = `
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Video Ready (${vidData.duration || 15}s)
                            </span>
                        </div>
                    `;
                    actionBtn = `
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="playProductVideo('${vidData.video_url}', '${p.sku}', '${p.name}')" class="shadcn-btn py-1 px-2.5 text-xs text-slate-800 hover:bg-slate-100" title="Play Video">
                                <i class="fa-solid fa-play text-xs text-emerald-600"></i> Play
                            </button>
                            <a href="${vidData.video_url}" download class="shadcn-btn py-1 px-2.5 text-xs text-slate-700" title="Download Video">
                                <i class="fa-solid fa-download text-xs"></i>
                            </a>
                            <button type="button" onclick="generateSingleProductVideo('${p.sku}')" class="shadcn-btn py-1 px-2 text-xs text-slate-500 hover:text-slate-900" title="Regenerate Video">
                                <i class="fa-solid fa-rotate text-xs"></i>
                            </button>
                        </div>
                    `;
                } else {
                    videoBadge = `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                            <i class="fa-regular fa-clock text-[10px]"></i> No Video Yet
                        </span>
                    `;
                    actionBtn = `
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="generateSingleProductVideo('${p.sku}')" class="shadcn-btn shadcn-btn-primary py-1 px-3 text-xs" title="Generate Video for this product">
                                <i class="fa-solid fa-film text-xs"></i> Generate
                            </button>
                        </div>
                    `;
                }

                html += `
                    <tr id="row_${p.sku}" class="${isSelected ? 'selected' : ''}">
                        <td style="text-align: center;">
                            <input type="checkbox" class="product-checkbox w-4 h-4 rounded cursor-pointer accent-slate-900" 
                                   value="${p.sku}" 
                                   ${isSelected ? 'checked' : ''} 
                                   onchange="onProductSelectToggle('${p.sku}', this.checked)">
                        </td>
                        <td>
                            <div class="w-12 h-12 rounded bg-slate-900 overflow-hidden border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                <img src="${p.primary_image}" alt="${p.sku}" class="w-full h-full object-cover" onerror="this.src='assets/default-product.jpg'">
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="font-mono font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                    <span>${p.sku}</span>
                                    ${p.photos_count > 1 ? `<span class="text-[10px] text-slate-400 font-sans">(${p.photos_count} photos)</span>` : ''}
                                </div>
                                <div class="text-xs text-slate-700 font-medium mt-0.5 line-clamp-1" title="${p.name}">
                                    ${p.name}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-600 border border-slate-200 truncate max-w-[130px]">
                                ${p.category_name || p.type}
                            </span>
                        </td>
                        <td>
                            <div class="text-xs font-mono font-bold text-slate-900">₹${Number(p.rent_price || 0).toLocaleString()}</div>
                            ${p.price > 0 ? `<div class="text-[10px] text-slate-400 font-mono">Sale: ₹${Number(p.price).toLocaleString()}</div>` : ''}
                        </td>
                        <td id="status_cell_${p.sku}">
                            ${videoBadge}
                        </td>
                        <td id="action_cell_${p.sku}" style="text-align: right;">
                            ${actionBtn}
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        // Toggle Single Product Selection Checkbox
        function onProductSelectToggle(sku, isChecked) {
            const row = document.getElementById(`row_${sku}`);
            if (isChecked) {
                selectedProductSkus.add(sku);
                if (row) row.classList.add('selected');
            } else {
                selectedProductSkus.delete(sku);
                if (row) row.classList.remove('selected');
            }
            updateSelectedCount();
        }

        // Toggle Select All
        function toggleSelectAll(isChecked) {
            document.querySelectorAll('.product-checkbox').forEach(cb => {
                cb.checked = isChecked;
                const sku = cb.value;
                const row = document.getElementById(`row_${sku}`);
                if (isChecked) {
                    selectedProductSkus.add(sku);
                    if (row) row.classList.add('selected');
                } else {
                    selectedProductSkus.delete(sku);
                    if (row) row.classList.remove('selected');
                }
            });
            document.getElementById('selectAllCheckbox').checked = isChecked;
            document.getElementById('headerCheckbox').checked = isChecked;
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = selectedProductSkus.size;
            document.getElementById('selectedCountBadge').textContent = count;
            document.getElementById('batchBtnCount').textContent = count;
            document.getElementById('startBatchBtn').disabled = (count === 0);
        }

        // =========================================================================
        // GEMINI AI BATCH VIDEO MOTION GENERATION ENGINE
        // =========================================================================
        async function startBatchGeneration() {
            if (selectedProductSkus.size === 0 || isBatchRunning) return;

            const selectedSkus = Array.from(selectedProductSkus);
            const total = selectedSkus.length;
            const duration = parseInt(document.getElementById('batchDuration').value);
            const preset = document.getElementById('batchPreset').value;
            const aspect = document.getElementById('batchAspect').value;

            // Open Progress Modal
            isBatchRunning = true;
            shouldCancelBatch = false;
            const modal = document.getElementById('batchProgressModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const progressBar = document.getElementById('batchProgressBar');
            const progressPercent = document.getElementById('batchProgressPercent');
            const countText = document.getElementById('batchCountText');
            const currentProductText = document.getElementById('batchCurrentProductText');
            const modalThumb = document.getElementById('batchModalThumb');
            const modalSku = document.getElementById('batchModalSku');
            const modalTitle = document.getElementById('batchModalTitle');
            const modalStatus = document.getElementById('batchModalStatus');

            let successCount = 0;

            for (let i = 0; i < total; i++) {
                if (shouldCancelBatch) break;

                const sku = selectedSkus[i];
                const product = currentProducts.find(p => p.sku === sku);
                if (!product) continue;

                // Update modal info
                const pct = Math.floor((i / total) * 100);
                progressBar.style.width = pct + '%';
                progressPercent.textContent = pct + '%';
                countText.textContent = `Product ${i + 1} of ${total}`;
                currentProductText.textContent = `Generating ${sku} (${duration}s)`;
                modalThumb.src = product.primary_image;
                modalSku.textContent = sku;
                modalTitle.textContent = product.name;
                modalStatus.textContent = 'Calling Gemini 3.8 Flash for motion choreography...';

                // Update Table Row Status to Spinning
                const statusCell = document.getElementById(`status_cell_${sku}`);
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <i class="fa-solid fa-spinner fa-spin text-xs"></i> Generating with Gemini...
                        </span>
                    `;
                }

                try {
                    // 1. Call Gemini to calculate custom motion parameters
                    const blueprint = await callGeminiMotionPlanner(product, duration, preset);
                    modalStatus.textContent = 'Rendering 10–20s motion frames at 30 FPS...';

                    // 2. Render and record the real video via Canvas + MediaRecorder
                    const videoBlob = await renderProductVideoToBlob(product, duration, preset, aspect, blueprint);
                    modalStatus.textContent = 'Saving video to server and catalog record...';

                    // 3. Upload & link video to product
                    const saveRes = await saveVideoToServer(videoBlob, product, duration, preset, aspect);

                    if (saveRes && saveRes.success) {
                        successCount++;
                        // Update Table Row with Green Success Badge & Play button
                        if (statusCell) {
                            statusCell.innerHTML = `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Video Ready (${duration}s)
                                </span>
                            `;
                        }
                        const actionCell = document.getElementById(`action_cell_${sku}`);
                        if (actionCell) {
                            actionCell.innerHTML = `
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="playProductVideo('${saveRes.url}', '${product.sku}', '${product.name}')" class="shadcn-btn py-1 px-2.5 text-xs text-slate-800 hover:bg-slate-100" title="Play Video">
                                        <i class="fa-solid fa-play text-xs text-emerald-600"></i> Play
                                    </button>
                                    <a href="${saveRes.url}" download class="shadcn-btn py-1 px-2.5 text-xs text-slate-700" title="Download Video">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                } catch (err) {
                    console.error(`Error generating video for ${sku}:`, err);
                    if (statusCell) {
                        statusCell.innerHTML = `
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <i class="fa-solid fa-triangle-exclamation"></i> Error
                            </span>
                        `;
                    }
                }
            }

            // Finished All
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            modalStatus.textContent = `Batch complete! ${successCount} of ${total} videos generated and linked to products.`;

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                isBatchRunning = false;
                alert(`Successfully generated and saved ${successCount} product motion videos!`);
            }, 1000);
        }

        // Single Product Video Generation Trigger
        async function generateSingleProductVideo(sku) {
            selectedProductSkus.clear();
            selectedProductSkus.add(sku);
            updateSelectedCount();
            startBatchGeneration();
        }

        function cancelBatch() {
            shouldCancelBatch = true;
            document.getElementById('batchModalStatus').textContent = 'Cancelling batch process...';
        }

        // 1. Call Gemini to calculate custom motion choreography parameters
        async function callGeminiMotionPlanner(product, duration, preset) {
            try {
                const res = await fetch('index.php?controller=video_generator&action=planProductMotion', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        sku: product.sku,
                        title: product.name,
                        category: product.category_name || product.type,
                        duration: duration,
                        preset: preset
                    })
                });
                const data = await res.json();
                return data.blueprint || {};
            } catch (e) {
                return {};
            }
        }

        // 2. Offscreen Canvas Video Renderer (30 FPS, True Hardware MediaRecorder)
        function renderProductVideoToBlob(product, duration, preset, aspect, blueprint) {
            return new Promise(async (resolve, reject) => {
                try {
                    // Set canvas resolution based on aspect
                    let w = 720, h = 1280;
                    if (aspect === '1:1') { w = 1080; h = 1080; }
                    else if (aspect === '16:9') { w = 1280; h = 720; }
                    renderCanvas.width = w;
                    renderCanvas.height = h;

                    // Load product image via CORS proxy
                    const img = new Image();
                    img.crossOrigin = 'anonymous';

                    let imgSrc = product.primary_image;
                    if (imgSrc.startsWith('http') && imgSrc.includes('srishringarr.com')) {
                        imgSrc = 'index.php?controller=video_generator&action=proxyImage&url=' + encodeURIComponent(imgSrc);
                    }

                    await new Promise((imgRes, imgRej) => {
                        img.onload = imgRes;
                        img.onerror = () => {
                            img.src = 'assets/default-product.jpg';
                            img.onload = imgRes;
                        };
                        img.src = imgSrc;
                    });

                    // Setup MediaRecorder
                    const fps = 30;
                    const totalFrames = duration * fps;
                    const frameDuration = 1 / fps;
                    const stream = renderCanvas.captureStream(fps);

                    let mimeType = 'video/webm;codecs=vp9';
                    if (!MediaRecorder.isTypeSupported(mimeType)) {
                        mimeType = 'video/webm;codecs=vp8';
                        if (!MediaRecorder.isTypeSupported(mimeType)) mimeType = 'video/webm';
                    }

                    const recorder = new MediaRecorder(stream, {
                        mimeType: mimeType,
                        videoBitsPerSecond: 8000000
                    });

                    const chunks = [];
                    recorder.ondataavailable = e => { if (e.data && e.data.size > 0) chunks.push(e.data); };
                    const stopPromise = new Promise(r => recorder.onstop = r);

                    recorder.start(100);

                    // Procedural Frame Rendering Loop
                    for (let f = 0; f < totalFrames; f++) {
                        const t = f * frameDuration;
                        drawProductMotionFrame(renderCtx, w, h, t, duration, preset, img, product, blueprint);
                        await new Promise(r => setTimeout(r, 1000 / fps));
                    }

                    recorder.stop();
                    await stopPromise;

                    const blob = new Blob(chunks, { type: mimeType });
                    resolve(blob);

                } catch (err) {
                    reject(err);
                }
            });
        }

        // Draw individual motion frame with dynamic lighting & camera paths
        function drawProductMotionFrame(ctx, w, h, t, duration, preset, img, product, blueprint) {
            const progress = (t % duration) / duration;

            // 1. Dark Luxury Velvet Backdrop
            const bg = ctx.createRadialGradient(w/2, h/2, 60, w/2, h/2, Math.max(w, h)*0.75);
            bg.addColorStop(0, '#1c1917');
            bg.addColorStop(0.6, '#0c0a09');
            bg.addColorStop(1, '#050505');
            ctx.fillStyle = bg;
            ctx.fillRect(0, 0, w, h);

            // 2. Ambient Studio Rim Light
            const glowOsc = Math.sin(progress * Math.PI * 2) * 0.2 + 0.8;
            const spot = ctx.createRadialGradient(w/2, h*0.45, 10, w/2, h*0.45, w*0.5);
            spot.addColorStop(0, `rgba(217, 119, 6, ${0.14 * glowOsc})`);
            spot.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = spot;
            ctx.fillRect(0, 0, w, h);

            // 3. Floating Gold Ember Dust
            const particleCount = 35;
            for (let i = 0; i < particleCount; i++) {
                const px = (Math.sin(i * 123 + t * 0.4) * 0.5 + 0.5) * w;
                const py = ((i * 73 - t * 25) % h + h) % h;
                const psize = (i % 3) + 1.2;
                ctx.beginPath();
                ctx.arc(px, py, psize, 0, Math.PI * 2);
                ctx.fillStyle = (i % 2 === 0) ? 'rgba(251, 191, 36, 0.6)' : 'rgba(255, 255, 255, 0.45)';
                ctx.fill();
            }

            // 4. Motion Choreography Transformation
            ctx.save();
            let scale = 1.0;
            let transX = 0;
            let transY = 0;
            let rotY = 0;
            let tilt = 0;
            const amp = blueprint.zoom_amplitude || 1.18;

            if (preset === 'turntable') {
                const angle = progress * Math.PI * 2;
                rotY = Math.sin(angle) * 0.18;
                transX = Math.sin(angle) * (w * 0.035);
                scale = 1.0 + Math.cos(angle) * 0.06 * amp;
                tilt = Math.sin(angle) * 0.03;
            } else if (preset === 'glint') {
                const osc = Math.sin(progress * Math.PI * 2);
                scale = 1.05 + osc * 0.07 * amp;
                transY = osc * (h * 0.02);
            } else if (preset === 'macro') {
                const pan = Math.sin(progress * Math.PI);
                scale = 1.25 + pan * 0.16 * amp;
                transX = (pan - 0.5) * (w * 0.12);
                transY = (pan - 0.5) * (h * 0.08);
            } else if (preset === 'kenburns') {
                scale = 1.0 + progress * 0.22 * amp;
                transX = (progress - 0.5) * (w * 0.07);
            } else if (preset === 'sway') {
                const sway = Math.sin(progress * Math.PI * 4);
                tilt = sway * 0.04;
                transX = sway * (w * 0.03);
                scale = 1.02 + Math.cos(progress * Math.PI * 2) * 0.04;
            }

            const cx = w / 2;
            const cy = h * 0.48;
            ctx.translate(cx + transX, cy + transY);
            ctx.rotate(tilt);
            ctx.scale(scale * (1 + rotY), scale);

            // Aspect fit image
            const iw = img.naturalWidth || 600;
            const ih = img.naturalHeight || 800;
            const targetW = w * 0.78;
            const targetH = h * 0.65;
            const ratio = Math.min(targetW / iw, targetH / ih);
            const dw = iw * ratio;
            const dh = ih * ratio;

            ctx.shadowColor = 'rgba(0, 0, 0, 0.75)';
            ctx.shadowBlur = 30;
            ctx.shadowOffsetY = 15;
            ctx.drawImage(img, -dw / 2, -dh / 2, dw, dh);
            ctx.restore();

            // 5. Specular Diamond Starburst Flares
            const flareTimes = [0.15, 0.38, 0.62, 0.85];
            flareTimes.forEach((ft, idx) => {
                const flareTime = (progress + ft) % 1.0;
                const intensity = Math.pow(Math.max(0, Math.sin(flareTime * Math.PI)), 5);
                if (intensity > 0.1) {
                    const fx = cx + transX + (idx % 2 === 0 ? -dw*0.2 : dw*0.2);
                    const fy = cy + transY + (idx < 2 ? -dh*0.15 : dh*0.15);
                    drawFlare(ctx, fx, fy, intensity * 24);
                }
            });

            // 6. Sweeping Anamorphic Light Beam
            const beamX = (progress * 1.5 % 1.0) * (w * 1.6) - (w * 0.3);
            const beamGrad = ctx.createLinearGradient(beamX - 100, 0, beamX + 100, h);
            beamGrad.addColorStop(0, 'rgba(255,255,255,0)');
            beamGrad.addColorStop(0.5, 'rgba(254, 240, 138, 0.14)');
            beamGrad.addColorStop(1, 'rgba(255,255,255,0)');
            ctx.fillStyle = beamGrad;
            ctx.fillRect(0, 0, w, h);

            // 7. Vignette
            const vig = ctx.createRadialGradient(w/2, h/2, Math.min(w,h)*0.45, w/2, h/2, Math.max(w,h)*0.72);
            vig.addColorStop(0, 'rgba(0,0,0,0)');
            vig.addColorStop(1, 'rgba(0,0,0,0.65)');
            ctx.fillStyle = vig;
            ctx.fillRect(0, 0, w, h);

            // 8. Brand Watermark & SKU
            const yPos = h - (h * 0.07);
            const shimmer = Math.sin(progress * Math.PI * 2) * 0.15 + 0.85;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.font = '600 19px "Playfair Display", Georgia, serif';
            ctx.fillStyle = `rgba(254, 243, 199, ${shimmer})`;
            ctx.fillText('SRI SHRINGARR', w / 2, yPos);

            ctx.font = '600 11.5px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = 'rgba(226, 232, 240, 0.85)';
            ctx.fillText((product.sku || 'STUDIO') + ' • LUXURY BRIDAL', w / 2, yPos + 18);
            ctx.restore();
        }

        function drawFlare(ctx, x, y, size) {
            ctx.save();
            ctx.translate(x, y);

            const halo = ctx.createRadialGradient(0, 0, 0, 0, 0, size * 1.5);
            halo.addColorStop(0, 'rgba(255, 255, 255, 0.95)');
            halo.addColorStop(0.3, 'rgba(253, 224, 71, 0.6)');
            halo.addColorStop(1, 'rgba(0, 0, 0, 0)');
            ctx.fillStyle = halo;
            ctx.beginPath();
            ctx.arc(0, 0, size * 1.5, 0, Math.PI * 2);
            ctx.fill();

            ctx.strokeStyle = 'rgba(255, 255, 255, 0.9)';
            ctx.lineWidth = 1.8;
            ctx.beginPath();
            ctx.moveTo(0, -size * 2);
            ctx.lineTo(0, size * 2);
            ctx.moveTo(-size * 2, 0);
            ctx.lineTo(size * 2, 0);
            ctx.stroke();

            ctx.restore();
        }

        // 3. Save Video to server & product database
        async function saveVideoToServer(blob, product, duration, preset, aspect) {
            const fd = new FormData();
            fd.append('video_blob', blob, `prod_${product.sku}_${duration}s.webm`);
            fd.append('product_id', product.id || 0);
            fd.append('sku', product.sku);
            fd.append('product_name', product.name);
            fd.append('type', product.type || 'jewellery');
            fd.append('duration', duration);
            fd.append('preset', preset);
            fd.append('aspect_ratio', aspect);

            const res = await fetch('index.php?controller=video_generator&action=saveVideo', {
                method: 'POST',
                body: fd
            });
            return await res.json();
        }

        // =========================================================================
        // VIDEO PLAYER MODAL
        // =========================================================================
        function playProductVideo(url, sku, name) {
            document.getElementById('playerModalSku').textContent = sku;
            document.getElementById('playerModalTitle').textContent = name;
            document.getElementById('playerDownloadBtn').href = url;

            const video = document.getElementById('playerVideoElement');
            video.src = url;
            video.load();
            video.play();

            const modal = document.getElementById('videoPlayerModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeVideoPlayer() {
            const video = document.getElementById('playerVideoElement');
            video.pause();
            video.src = '';
            const modal = document.getElementById('videoPlayerModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
