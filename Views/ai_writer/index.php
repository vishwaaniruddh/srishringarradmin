<!DOCTYPE html>
<html lang="en">
<head>
    <title>All in One AI Writer - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: var(--slate-50) !important;
            color: var(--slate-900) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Strict ShadCN Card System */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
            overflow: hidden;
            transition: border-color 0.15s ease;
        }

        .shadcn-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--slate-100);
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

        /* Strict ShadCN Button Tokens */
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
            border: 1px solid var(--slate-200);
            background: #ffffff;
            color: var(--slate-700);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: var(--slate-50);
            color: var(--slate-900);
            border-color: var(--slate-300);
        }
        .shadcn-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .shadcn-btn-primary {
            background: var(--slate-900) !important;
            border-color: var(--slate-900) !important;
            color: #ffffff !important;
        }
        .shadcn-btn-primary:hover {
            background: var(--slate-800) !important;
            border-color: var(--slate-800) !important;
            color: #ffffff !important;
        }

        .shadcn-btn-sm {
            height: 28px;
            padding: 0 9px;
            font-size: 11.5px;
            border-radius: 5px;
        }

        /* Unified Neutral Badges & Pills */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 9999px;
            background: var(--slate-100);
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
        }
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--slate-400);
        }
        .status-dot.active {
            background: #10b981;
        }

        /* Form Inputs */
        .field-input, .field-textarea, .field-select {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 12.5px;
            color: var(--slate-900);
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
            font-family: inherit;
        }
        .field-input:focus, .field-textarea:focus, .field-select:focus {
            border-color: var(--slate-900);
            box-shadow: 0 0 0 1px var(--slate-900);
        }
        .field-textarea {
            resize: vertical;
            line-height: 1.45;
        }

        /* Product Card Layout - Allow Popout without Clipping */
        .product-item-card {
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.15s ease;
            position: relative;
        }
        .product-item-card.is-processing {
            border-color: var(--slate-900);
            box-shadow: 0 0 0 1px var(--slate-900);
        }
        .product-item-header {
            padding: 12px 18px;
            background: #fafafa;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            border-radius: 8px 8px 0 0;
        }

        /* Image Display Cards & Smooth Hover Enlarge */
        .photo-card {
            background: #fafafa;
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .photo-card-media {
            width: 100%;
            height: 145px;
            background: var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 5px 5px 0 0;
        }
        .photo-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px 5px 0 0;
            cursor: zoom-in;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
            position: relative;
            z-index: 10;
        }
        /* On hover enlarge: smooth 2.1x expansion with rich drop shadow & white border */
        .photo-card-media img:hover {
            transform: scale(2.1);
            transform-origin: center center;
            z-index: 999;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.5), 0 0 0 1px rgba(0, 0, 0, 0.1);
            border: 2px solid #ffffff;
            border-radius: 6px;
        }
        .photo-card-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            color: var(--slate-400);
            font-size: 11px;
            text-align: center;
            padding: 10px;
            width: 100%;
            height: 100%;
        }
        .photo-card-body {
            padding: 8px 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background: #ffffff;
            border-top: 1px solid var(--slate-200);
            border-radius: 0 0 5px 5px;
        }

        /* Character Counter & Helper */
        .char-counter {
            font-size: 11px;
            color: var(--slate-400);
            font-family: monospace;
        }
        .char-counter.warning {
            color: #d97706;
            font-weight: 600;
        }

        /* Custom Toast Notification */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .shadcn-toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: var(--slate-900);
            color: #ffffff;
            border-radius: 6px;
            font-size: 12.5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.2s ease forwards;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modal Lightbox */
        #image-lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            z-index: 100000;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        #image-lightbox.open {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Panel -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'All in One AI Writer';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-xl font-bold tracking-tight text-slate-900">All in One AI Writer</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Studio Suite
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Generate high-converting <strong>Titles</strong>, <strong>Short Descriptions</strong>, <strong>Long Descriptions</strong>, and <strong>Dual Photoshoot Imagery</strong> (with model &amp; without model) with precise category anatomical positioning.
                            </p>
                        </div>

                        <!-- System Status Indicators -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="shadcn-badge">
                                <span class="status-dot <?php echo !empty($hasGemini) ? 'active' : ''; ?>"></span>
                                Gemini Vision <?php echo !empty($hasGemini) ? 'Ready' : 'No Key'; ?>
                            </span>
                            <span class="shadcn-badge">
                                <span class="status-dot <?php echo !empty($hasOpenAi) ? 'active' : ''; ?>"></span>
                                OpenAI <?php echo !empty($hasOpenAi) ? 'Ready' : 'No Key'; ?>
                            </span>
                            <span class="shadcn-badge">
                                <i class="fa-solid fa-user-astronaut text-slate-400"></i>
                                <?php echo count($availableModels); ?> Models Active
                            </span>
                        </div>
                    </div>

                    <!-- ==================== SKU & CATEGORY INPUT CONTROLS ==================== -->
                    <div class="shadcn-card">
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-barcode text-slate-600"></i>
                                <span class="font-semibold text-xs text-slate-900 uppercase tracking-wider">SKU Queue &amp; Category Filters</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="insertSampleSkus()" class="shadcn-btn shadcn-btn-sm text-slate-600">
                                    <i class="fa-solid fa-lightbulb"></i> Load Sample SKUs
                                </button>
                                <button type="button" onclick="clearSkus()" class="shadcn-btn shadcn-btn-sm text-slate-500">
                                    <i class="fa-solid fa-trash-can"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div class="shadcn-card-body">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                                        Enter SKUs (Separated by comma, space, or newline)
                                    </label>
                                    <textarea id="sku_input" rows="3" class="field-textarea font-mono text-xs" placeholder="e.g. KP84, KP87ON, KP123ON or YNL012 YNL190"></textarea>
                                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-400">
                                        <span>Accepts both Jewellery and Apparel SKUs. Auto-detects proper body placement (Kamar Patta, Necklaces, etc.).</span>
                                        <span id="detected_sku_count">0 SKUs detected</span>
                                    </div>
                                </div>

                                <!-- Configuration Toggles & Toolbar -->
                                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        
                                        <!-- Category Filter -->
                                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-filter text-slate-400"></i> Category:
                                            </span>
                                            <select id="config_category_filter" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer">
                                                <option value="all">All Categories</option>
                                                <optgroup label="Jewellery">
                                                    <option value="kamar_patta">Kamar Patta (Waist Belt / Belly Chain)</option>
                                                    <option value="necklace_set">Necklace Sets &amp; Chokers</option>
                                                    <option value="earrings">Earrings / Jhumkas</option>
                                                    <option value="damini_mathapatti">Damini / Mathapatti / Maang Tikka</option>
                                                    <option value="nath">Nath (Nose Ring)</option>
                                                    <option value="hath_phool">Hath Phool / Hath Panja</option>
                                                    <option value="bangles_bracelets">Bangles, Bracelets &amp; Kadas</option>
                                                    <option value="baju_bandh">Baju Bandh (Armlet)</option>
                                                    <option value="payal_pagpan">Payal / Pag Pan (Anklets)</option>
                                                    <option value="rings">Finger Rings</option>
                                                    <option value="mala">Mala / Long Chains</option>
                                                    <option value="pendant_set">Pendant Sets</option>
                                                    <option value="bridal_set">Bridal Complete Set</option>
                                                </optgroup>
                                                <optgroup label="Apparel">
                                                    <option value="lehenga_choli">Lehenga Choli</option>
                                                    <option value="evening_gowns">Evening &amp; Trail Gowns</option>
                                                    <option value="designer_blouse">Designer Blouses</option>
                                                    <option value="indo_western">Indo Western Outfits</option>
                                                    <option value="kurtis">Kurtis &amp; Tunics</option>
                                                </optgroup>
                                            </select>
                                        </div>

                                        <!-- AI Provider -->
                                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">AI Model:</span>
                                            <select id="config_provider" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer">
                                                <?php if (!empty($hasGemini)): ?>
                                                    <option value="gemini" selected>Google Gemini (Flash Vision)</option>
                                                <?php endif; ?>
                                                <?php if (!empty($hasOpenAi)): ?>
                                                    <option value="openai" <?php echo empty($hasGemini) ? 'selected' : ''; ?>>OpenAI GPT-4o mini</option>
                                                <?php endif; ?>
                                                <?php if (empty($hasGemini) && empty($hasOpenAi)): ?>
                                                    <option value="gemini">Gemini (Add key in secrets.php)</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- Face Reference Selector -->
                                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Model Face:</span>
                                            <select id="config_face_model" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer">
                                                <option value="" selected>Default Indian Fashion Model</option>
                                                <?php foreach ($availableModels as $m): ?>
                                                    <option value="<?php echo htmlspecialchars($m['file']); ?>">
                                                        <?php echo htmlspecialchars($m['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Studio Backdrop Theme (Without Model) -->
                                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-gem text-slate-400"></i> Studio Backdrop:
                                            </span>
                                            <select id="config_studio_theme" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer">
                                                <option value="royal_velvet" selected>Royal Wine Velvet Cushion (Signature)</option>
                                                <option value="velvet_rose_petals">Velvet Cushion + Fresh Rose Petals</option>
                                                <option value="royal_navy_velvet">Royal Navy Blue Velvet Cushion</option>
                                                <option value="dark_emerald_velvet">Emerald Green Velvet &amp; Brocade</option>
                                                <option value="black_velvet">Deep Black Velvet (High Contrast)</option>
                                            </select>
                                        </div>

                                        <!-- Generation Checkboxes -->
                                        <div class="flex items-center gap-3 text-xs text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-md">
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" id="gen_opt_copy" checked class="rounded border-slate-300 text-slate-900 focus:ring-0">
                                                <span>Title &amp; Descriptions</span>
                                            </label>
                                            <span class="text-slate-300">|</span>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" id="gen_opt_model" checked class="rounded border-slate-300 text-slate-900 focus:ring-0">
                                                <span>Photo With Model</span>
                                            </label>
                                            <span class="text-slate-300">|</span>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" id="gen_opt_studio" checked class="rounded border-slate-300 text-slate-900 focus:ring-0">
                                                <span>Photo Without Model</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Search / Load Buttons -->
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="btn_lookup_skus" onclick="lookupProducts()" class="shadcn-btn shadcn-btn-primary">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <span>Search &amp; Load Products</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== QUEUE STATUS & GLOBAL ACTIONS ==================== -->
                    <div id="queue_toolbar" class="hidden shadcn-card" style="margin-bottom: 16px;">
                        <div class="shadcn-card-header" style="background: #fafafa;">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-xs text-slate-900">
                                    <span id="queue_total_count">0</span> Product(s) Ready
                                </span>
                                <div id="missing_skus_alert" class="hidden">
                                    <span class="shadcn-badge text-amber-700 bg-amber-50 border-amber-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span id="missing_skus_text">0 missing</span>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button" id="btn_generate_all" onclick="generateAllBatch()" class="shadcn-btn shadcn-btn-primary">
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                    <span>Generate All in One (Batch)</span>
                                </button>
                                <button type="button" id="btn_save_all_global" onclick="saveAllGlobal()" class="shadcn-btn">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Save All to Database</span>
                                </button>
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div id="batch_progress_bar_container" class="hidden w-full bg-slate-100 h-1.5">
                            <div id="batch_progress_bar" class="bg-slate-900 h-1.5 transition-all duration-300" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- ==================== PRODUCT CARDS CONTAINER ==================== -->
                    <div id="products_container" class="space-y-6">
                        <!-- Empty State -->
                        <div id="empty_state" class="shadcn-card py-16 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 mx-auto flex items-center justify-center text-slate-400 mb-3">
                                <i class="fa-solid fa-pen-nib text-lg"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">No Products Loaded</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                                Enter your product SKUs above (or select a category) and click "Search &amp; Load Products" to start generating content and photos.
                            </p>
                            <button type="button" onclick="insertSampleSkus(); lookupProducts();" class="shadcn-btn shadcn-btn-sm text-slate-700">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Try With KP84 (Kamar Patta Demo)
                            </button>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div id="image-lightbox" onclick="closeLightbox()">
        <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-lg p-2 shadow-2xl" onclick="event.stopPropagation()">
            <img id="lightbox-img" src="" alt="Zoom" class="max-h-[82vh] w-auto mx-auto rounded object-contain">
            <div class="flex justify-between items-center px-3 py-2 border-t border-slate-100 mt-2">
                <span id="lightbox-caption" class="text-xs font-semibold text-slate-700">Preview</span>
                <div class="flex items-center gap-2">
                    <a id="lightbox-download" href="#" download class="shadcn-btn shadcn-btn-sm">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                    <button type="button" onclick="closeLightbox()" class="shadcn-btn shadcn-btn-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <!-- Script Application Logic -->
    <script>
        // Available category definitions passed from backend
        const categoryOptions = <?php echo json_encode(array_map(function($c) { return ['name' => $c['name'], 'type' => $c['type']]; }, $categories)); ?>;

        let loadedProducts = [];
        let isBatchRunning = false;

        // Auto-detect SKU count on input
        document.getElementById('sku_input').addEventListener('input', function(e) {
            const raw = e.target.value.trim();
            if (!raw) {
                document.getElementById('detected_sku_count').innerText = '0 SKUs detected';
                return;
            }
            const tokens = raw.split(/[\r\n,\s]+/).map(s => s.trim()).filter(Boolean);
            const unique = [...new Set(tokens)];
            document.getElementById('detected_sku_count').innerText = `${unique.length} SKU(s) detected`;
        });

        function insertSampleSkus() {
            document.getElementById('sku_input').value = 'KP84, KP87ON, KP123ON, YNL012';
            document.getElementById('sku_input').dispatchEvent(new Event('input'));
        }

        function clearSkus() {
            document.getElementById('sku_input').value = '';
            document.getElementById('detected_sku_count').innerText = '0 SKUs detected';
            loadedProducts = [];
            renderProducts();
        }

        // ==================== LOOKUP PRODUCTS ====================
        async function lookupProducts() {
            const raw = document.getElementById('sku_input').value.trim();
            const catFilter = document.getElementById('config_category_filter').value;

            if (!raw && (!catFilter || catFilter === 'all')) {
                showToast('Please enter at least one SKU or choose a category filter.', 'error');
                return;
            }

            const btn = document.getElementById('btn_lookup_skus');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Searching...';

            try {
                const response = await fetch('index.php?controller=allinonewriter&action=lookup', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        skus: raw,
                        category_filter: catFilter 
                    })
                });

                const data = await response.json();
                if (!data.success) {
                    showToast(data.error || 'Failed to lookup products.', 'error');
                    return;
                }

                loadedProducts = data.products.map(p => ({
                    ...p,
                    category_key: p.category_key || 'necklace_set',
                    gen_name: p.name || '',
                    gen_short: p.short_desc || '',
                    gen_desc: p.description || '',
                    photo_with_model: null,
                    photo_without_model: null,
                    saved_with_model: false,
                    saved_without_model: false,
                    status_text: 'Ready'
                }));

                // Handle missing SKUs notification
                const missingContainer = document.getElementById('missing_skus_alert');
                if (data.missing_skus && data.missing_skus.length > 0) {
                    missingContainer.classList.remove('hidden');
                    document.getElementById('missing_skus_text').innerText = `${data.missing_skus.length} SKU(s) not found: ${data.missing_skus.join(', ')}`;
                } else {
                    missingContainer.classList.add('hidden');
                }

                renderProducts();
                showToast(`Loaded ${loadedProducts.length} product(s) successfully!`);

            } catch (err) {
                console.error(err);
                showToast('An error occurred while fetching products.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // ==================== RENDER PRODUCTS ====================
        function renderProducts() {
            const container = document.getElementById('products_container');
            const toolbar = document.getElementById('queue_toolbar');

            if (loadedProducts.length === 0) {
                container.innerHTML = `
                    <div id="empty_state" class="shadcn-card py-16 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 mx-auto flex items-center justify-center text-slate-400 mb-3">
                            <i class="fa-solid fa-pen-nib text-lg"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">No Products Loaded</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                            Enter your product SKUs above (or select a category) and click "Search &amp; Load Products".
                        </p>
                    </div>
                `;
                toolbar.classList.add('hidden');
                return;
            }

            toolbar.classList.remove('hidden');
            document.getElementById('queue_total_count').innerText = loadedProducts.length;

            let html = '';
            loadedProducts.forEach((item, index) => {
                const thumb = item.primary_image_url || 'assets/placeholder.png';
                const typeBadge = item.type === 'jewellery' ? 'Jewellery' : 'Apparel';
                
                // Build category select dropdown HTML
                let catSelectHtml = `<select id="card_cat_${item.id}" onchange="changeProductCategory(${index}, this.value)" class="text-xs font-semibold text-slate-800 bg-transparent outline-none cursor-pointer">`;
                for (const [key, def] of Object.entries(categoryOptions)) {
                    const sel = (key === item.category_key) ? 'selected' : '';
                    catSelectHtml += `<option value="${key}" ${sel}>${escapeHtml(def.name)}</option>`;
                }
                catSelectHtml += `</select>`;

                // Placement badge text
                const placementHint = getPlacementHint(item.category_key);

                html += `
                <div class="product-item-card" id="card_prod_${item.id}">
                    <!-- Card Header -->
                    <div class="product-item-header">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-md border border-slate-200 overflow-hidden bg-slate-100 flex-shrink-0 cursor-pointer" onclick="openLightbox('${thumb}', '${escapeHtml(item.name || item.code)}')">
                                <img src="${thumb}" alt="${escapeHtml(item.code)}" class="w-full h-full object-cover" onerror="this.src='assets/placeholder.png'">
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono font-bold text-xs text-slate-900 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">
                                        ${escapeHtml(item.code)}
                                    </span>
                                    <span class="shadcn-badge font-semibold text-[10.5px]">
                                        ${typeBadge}
                                    </span>
                                    
                                    <!-- Category Selector -->
                                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 px-2 py-0.5 rounded shadow-2xs">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Category:</span>
                                        ${catSelectHtml}
                                    </div>

                                    <span id="cat_hint_${item.id}" class="shadcn-badge font-mono text-[10px] text-amber-800 bg-amber-50 border-amber-200">
                                        <i class="fa-solid fa-crosshairs"></i> ${placementHint}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                                    <span id="status_pill_${item.id}" class="shadcn-badge text-[10.5px]">
                                        <span class="status-dot"></span>
                                        ${item.status_text || 'Ready'}
                                    </span>
                                    <span class="text-slate-400">|</span>
                                    <span>${escapeHtml(item.category_name || '')}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button type="button" id="btn_run_single_${item.id}" onclick="generateSingleAllInOne(${index})" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate for This Item
                            </button>
                            <button type="button" id="btn_save_single_${item.id}" onclick="saveSingleProduct(${index})" class="shadcn-btn shadcn-btn-sm">
                                <i class="fa-solid fa-floppy-disk"></i> Save to DB
                            </button>
                        </div>
                    </div>

                    <!-- Card Body: 30% Images on Left, 70% Writes on Right -->
                    <div class="p-4">
                        <div class="flex flex-col lg:flex-row gap-5 items-start">

                            <!-- LEFT COLUMN: Dual Photoshoot Imagery (30%) -->
                            <div class="w-full lg:w-[32%] xl:w-[30%] flex-shrink-0 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-xs font-semibold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-camera text-slate-500"></i> Photos (Dual)
                                    </span>
                                    <button type="button" onclick="generateOnlyPhotos(${index})" class="shadcn-btn shadcn-btn-sm text-slate-600 text-[11px] h-6 px-2" title="Regenerate Both Photos">
                                        <i class="fa-solid fa-arrows-rotate"></i> Regenerate
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    
                                    <!-- Photo 1: With Model -->
                                    <div class="photo-card" id="photo_card_model_${item.id}">
                                        <div class="photo-card-media" id="media_model_${item.id}">
                                            ${item.photo_with_model ? `
                                                <img src="${item.photo_with_model.url}" alt="With Model" title="Click to view full preview (Hover to zoom)" onclick="openLightbox('${item.photo_with_model.url}', 'With Model - ${escapeHtml(item.code)}')">
                                            ` : `
                                                <div class="photo-card-empty">
                                                    <i class="fa-solid fa-user-astronaut text-xl text-slate-300"></i>
                                                    <span class="font-semibold text-[11px] text-slate-600">With Model</span>
                                                    <span class="text-[9.5px] text-slate-400 line-clamp-2">${placementHint}</span>
                                                </div>
                                            `}
                                        </div>
                                        <div class="photo-card-body">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] font-semibold text-slate-800 truncate">
                                                    <i class="fa-solid fa-user text-slate-400 text-[10px]"></i> Model
                                                </span>
                                                <span id="badge_model_${item.id}" class="text-[10px] text-slate-500 font-medium truncate">
                                                    ${item.photo_with_model ? (item.saved_with_model ? 'Saved' : 'Ready') : 'None'}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 pt-0.5">
                                                <button type="button" onclick="generatePhotoSingleMode(${index}, 'with_model')" class="shadcn-btn shadcn-btn-sm flex-1 text-[11px] h-6 px-1 text-slate-700" title="Generate Model Photo">
                                                    <i class="fa-solid fa-wand-magic text-[10px]"></i> Gen
                                                </button>
                                                <button type="button" id="btn_save_model_${item.id}" ${item.photo_with_model ? '' : 'disabled'} onclick="saveImageIndividual(${index}, 'with_model')" class="shadcn-btn shadcn-btn-sm text-[11px] h-6 px-1.5 text-slate-700" title="Save to Gallery">
                                                    <i class="fa-solid fa-image text-[10px]"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Photo 2: Without Model (Velvet Still Life) -->
                                    <div class="photo-card" id="photo_card_studio_${item.id}">
                                        <div class="photo-card-media" id="media_studio_${item.id}">
                                            ${item.photo_without_model ? `
                                                <img src="${item.photo_without_model.url}" alt="Without Model" title="Click to view full preview (Hover to zoom)" onclick="openLightbox('${item.photo_without_model.url}', 'Still Life - ${escapeHtml(item.code)}')">
                                            ` : `
                                                <div class="photo-card-empty">
                                                    <i class="fa-solid fa-gem text-xl text-slate-300"></i>
                                                    <span class="font-semibold text-[11px] text-slate-600">Still Life</span>
                                                    <span class="text-[9.5px] text-slate-400 line-clamp-2">Velvet Cushion</span>
                                                </div>
                                            `}
                                        </div>
                                        <div class="photo-card-body">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] font-semibold text-slate-800 truncate">
                                                    <i class="fa-solid fa-gem text-slate-400 text-[10px]"></i> Velvet
                                                </span>
                                                <span id="badge_studio_${item.id}" class="text-[10px] text-slate-500 font-medium truncate">
                                                    ${item.photo_without_model ? (item.saved_without_model ? 'Saved' : 'Ready') : 'None'}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 pt-0.5">
                                                <button type="button" onclick="generatePhotoSingleMode(${index}, 'without_model')" class="shadcn-btn shadcn-btn-sm flex-1 text-[11px] h-6 px-1 text-slate-700" title="Generate Still Life Photo">
                                                    <i class="fa-solid fa-wand-magic text-[10px]"></i> Gen
                                                </button>
                                                <button type="button" id="btn_save_studio_${item.id}" ${item.photo_without_model ? '' : 'disabled'} onclick="saveImageIndividual(${index}, 'without_model')" class="shadcn-btn shadcn-btn-sm text-[11px] h-6 px-1.5 text-slate-700" title="Save to Gallery">
                                                    <i class="fa-solid fa-image text-[10px]"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="text-[10px] text-slate-400 text-center flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-magnifying-glass-plus text-slate-400"></i> Hover image to enlarge
                                </div>
                            </div>

                            <!-- RIGHT COLUMN: Writes / Copy (70%) -->
                            <div class="w-full lg:w-[68%] xl:w-[70%] min-w-0 flex-1 space-y-3.5">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-xs font-semibold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa-solid fa-align-left text-slate-500"></i> AI E-Commerce Copy
                                    </span>
                                    <button type="button" onclick="generateOnlyCopy(${index})" class="shadcn-btn shadcn-btn-sm text-slate-600">
                                        <i class="fa-solid fa-arrows-rotate"></i> Regenerate Copy Only
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
                                    <!-- Title Field (6 cols) -->
                                    <div class="md:col-span-6">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-xs font-medium text-slate-700">Product Title (Name)</label>
                                            <div class="flex items-center gap-1.5">
                                                <span id="char_name_${item.id}" class="char-counter">${(item.gen_name || '').length}/95</span>
                                                <button type="button" onclick="copyText('input_name_${item.id}')" class="text-slate-400 hover:text-slate-700 text-xs" title="Copy Title">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="text" id="input_name_${item.id}" value="${escapeHtml(item.gen_name || '')}" oninput="updateCharCount(${item.id}, 'name', 95)" class="field-input font-medium" placeholder="E-commerce title">
                                    </div>

                                    <!-- Short Description Field (6 cols) -->
                                    <div class="md:col-span-6">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-xs font-medium text-slate-700">Short Description (Catalog Preview)</label>
                                            <div class="flex items-center gap-1.5">
                                                <span id="char_short_${item.id}" class="char-counter">${(item.gen_short || '').length}/245</span>
                                                <button type="button" onclick="copyText('input_short_${item.id}')" class="text-slate-400 hover:text-slate-700 text-xs" title="Copy Short Description">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <textarea id="input_short_${item.id}" rows="2" oninput="updateCharCount(${item.id}, 'short', 245)" class="field-textarea text-xs" placeholder="1-2 sentences highlighting craftsmanship and occasion">${escapeHtml(item.gen_short || '')}</textarea>
                                    </div>

                                    <!-- Long Description Field (12 cols) -->
                                    <div class="md:col-span-12">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-xs font-medium text-slate-700">Detailed Description (Features &amp; Occasions)</label>
                                            <div class="flex items-center gap-1.5">
                                                <button type="button" onclick="copyText('input_desc_${item.id}')" class="text-slate-400 hover:text-slate-700 text-xs" title="Copy Detailed Description">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <textarea id="input_desc_${item.id}" rows="5" class="field-textarea text-xs" placeholder="Comprehensive product description with Key Features bullet points">${escapeHtml(item.gen_desc || '')}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;
        }

        function changeProductCategory(index, newCategoryKey) {
            const item = loadedProducts[index];
            if (!item) return;

            item.category_key = newCategoryKey;
            const hintElem = document.getElementById(`cat_hint_${item.id}`);
            if (hintElem) {
                hintElem.innerHTML = `<i class="fa-solid fa-crosshairs"></i> ${getPlacementHint(newCategoryKey)}`;
            }
            showToast(`Category updated to ${categoryOptions[newCategoryKey]?.name || newCategoryKey} for ${item.code}`);
        }

        function getPlacementHint(categoryKey) {
            switch (categoryKey) {
                case 'kamar_patta': return 'Worn around WAIST & HIPS (Not Neck)';
                case 'necklace_set': return 'Worn around Neck & Collarbone';
                case 'earrings': return 'Worn on Earlobes';
                case 'damini_mathapatti': return 'Worn on Forehead & Hairline';
                case 'nath': return 'Worn on Nose';
                case 'hath_phool': return 'Worn on Back of Hand & Wrist';
                case 'bangles_bracelets': return 'Worn around Wrist / Forearm';
                case 'baju_bandh': return 'Worn on Upper Arm / Bicep';
                case 'payal_pagpan': return 'Worn around Ankles & Feet';
                case 'rings': return 'Worn on Fingers';
                case 'lehenga_choli': return 'Flared Skirt & Choli Blouse';
                case 'evening_gowns': return 'Full Length Gown Silhouette';
                case 'designer_blouse': return 'Blouse Neckline & Sleeves';
                default: return 'Category-specific placement active';
            }
        }

        // ==================== GENERATE SINGLE ITEM (ALL-IN-ONE) ====================
        async function generateSingleAllInOne(index) {
            const item = loadedProducts[index];
            if (!item) return;

            const card = document.getElementById(`card_prod_${item.id}`);
            const pill = document.getElementById(`status_pill_${item.id}`);
            const btn = document.getElementById(`btn_run_single_${item.id}`);

            card.classList.add('is-processing');
            btn.disabled = true;
            pill.innerHTML = '<span class="status-dot active"></span> Processing AI...';

            const provider = document.getElementById('config_provider').value;
            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = document.getElementById('config_studio_theme').value;
            const doCopy = document.getElementById('gen_opt_copy').checked;
            const doModel = document.getElementById('gen_opt_model').checked;
            const doStudio = document.getElementById('gen_opt_studio').checked;
            const currentCatKey = item.category_key;

            try {
                // 1. Generate Copy (Title, Short Desc, Long Desc)
                if (doCopy) {
                    pill.innerHTML = '<span class="status-dot active"></span> Writing Copy...';
                    const cRes = await fetch('index.php?controller=allinonewriter&action=generateContent', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id: item.id,
                            type: item.type,
                            provider: provider,
                            category_key: currentCatKey
                        })
                    });
                    const cData = await cRes.json();
                    if (cData.success) {
                        item.gen_name = cData.name;
                        item.gen_short = cData.short_description;
                        item.gen_desc = cData.description;

                        document.getElementById(`input_name_${item.id}`).value = cData.name;
                        document.getElementById(`input_short_${item.id}`).value = cData.short_description;
                        document.getElementById(`input_desc_${item.id}`).value = cData.description;

                        updateCharCount(item.id, 'name', 95);
                        updateCharCount(item.id, 'short', 245);
                    } else {
                        showToast(`Copy generation warning: ${cData.error}`, 'error');
                    }
                }

                // 2. Generate Photos (With Model & Without Model)
                let photoMode = null;
                if (doModel && doStudio) photoMode = 'both';
                else if (doModel) photoMode = 'with_model';
                else if (doStudio) photoMode = 'without_model';

                if (photoMode) {
                    pill.innerHTML = '<span class="status-dot active"></span> Generating Photos...';
                    const pRes = await fetch('index.php?controller=allinonewriter&action=generateImage', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id: item.id,
                            type: item.type,
                            mode: photoMode,
                            face_reference: faceModel,
                            studio_theme: studioTheme,
                            category_key: currentCatKey
                        })
                    });
                    const pData = await pRes.json();
                    if (pData.success && pData.images) {
                        if (pData.images.with_model) {
                            item.photo_with_model = pData.images.with_model;
                            updatePhotoCard(item.id, 'with_model', pData.images.with_model.url, index);
                        }
                        if (pData.images.without_model) {
                            item.photo_without_model = pData.images.without_model;
                            updatePhotoCard(item.id, 'without_model', pData.images.without_model.url, index);
                        }
                    } else if (pData.error) {
                        showToast(`Photo generation warning: ${pData.error}`, 'error');
                    }
                }

                pill.innerHTML = '<span class="status-dot active"></span> Generated Successfully';
                showToast(`Generated All in One for ${item.code}!`);

            } catch (err) {
                console.error(err);
                pill.innerHTML = '<span class="status-dot"></span> Generation Error';
                showToast(`Error processing ${item.code}`, 'error');
            } finally {
                card.classList.remove('is-processing');
                btn.disabled = false;
            }
        }

        // Generate only Copy
        async function generateOnlyCopy(index) {
            const item = loadedProducts[index];
            if (!item) return;

            const provider = document.getElementById('config_provider').value;
            const pill = document.getElementById(`status_pill_${item.id}`);
            pill.innerHTML = '<span class="status-dot active"></span> Rewriting Copy...';

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=generateContent', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: item.id, 
                        type: item.type, 
                        provider: provider,
                        category_key: item.category_key 
                    })
                });
                const data = await res.json();
                if (data.success) {
                    item.gen_name = data.name;
                    item.gen_short = data.short_description;
                    item.gen_desc = data.description;

                    document.getElementById(`input_name_${item.id}`).value = data.name;
                    document.getElementById(`input_short_${item.id}`).value = data.short_description;
                    document.getElementById(`input_desc_${item.id}`).value = data.description;

                    updateCharCount(item.id, 'name', 95);
                    updateCharCount(item.id, 'short', 245);
                    pill.innerHTML = '<span class="status-dot active"></span> Copy Updated';
                    showToast(`Copy rewritten for ${item.code}!`);
                } else {
                    showToast(data.error || 'Failed to rewrite copy', 'error');
                }
            } catch (err) {
                showToast('Error generating copy', 'error');
            }
        }

        // Generate only Photos
        async function generateOnlyPhotos(index) {
            const item = loadedProducts[index];
            if (!item) return;

            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = document.getElementById('config_studio_theme').value;
            const pill = document.getElementById(`status_pill_${item.id}`);
            pill.innerHTML = '<span class="status-dot active"></span> Generating Photos...';

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=generateImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        mode: 'both',
                        face_reference: faceModel,
                        studio_theme: studioTheme,
                        category_key: item.category_key
                    })
                });
                const data = await res.json();
                if (data.success && data.images) {
                    if (data.images.with_model) {
                        item.photo_with_model = data.images.with_model;
                        updatePhotoCard(item.id, 'with_model', data.images.with_model.url, index);
                    }
                    if (data.images.without_model) {
                        item.photo_without_model = data.images.without_model;
                        updatePhotoCard(item.id, 'without_model', data.images.without_model.url, index);
                    }
                    pill.innerHTML = '<span class="status-dot active"></span> Photos Updated';
                    showToast(`Photos generated for ${item.code}!`);
                } else {
                    showToast(data.error || 'Photo generation failed', 'error');
                }
            } catch (err) {
                showToast('Error generating photos', 'error');
            }
        }

        // Generate Single Photo Mode (with_model or without_model)
        async function generatePhotoSingleMode(index, mode) {
            const item = loadedProducts[index];
            if (!item) return;

            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = document.getElementById('config_studio_theme').value;
            const cardId = mode === 'with_model' ? `media_model_${item.id}` : `media_studio_${item.id}`;
            const mediaBox = document.getElementById(cardId);
            const originalHtml = mediaBox.innerHTML;

            mediaBox.innerHTML = `
                <div class="photo-card-empty">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                    <span class="font-semibold text-slate-600">Generating ${mode === 'with_model' ? 'Model' : 'Velvet Still Life'} Photo...</span>
                </div>
            `;

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=generateImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        mode: mode,
                        face_reference: faceModel,
                        studio_theme: studioTheme,
                        category_key: item.category_key
                    })
                });
                const data = await res.json();
                if (data.success && data.images && data.images[mode]) {
                    if (mode === 'with_model') {
                        item.photo_with_model = data.images.with_model;
                        updatePhotoCard(item.id, 'with_model', data.images.with_model.url, index);
                    } else {
                        item.photo_without_model = data.images.without_model;
                        updatePhotoCard(item.id, 'without_model', data.images.without_model.url, index);
                    }
                    showToast(`Photo (${mode}) generated!`);
                } else {
                    mediaBox.innerHTML = originalHtml;
                    showToast(data.error || 'Failed to generate photo', 'error');
                }
            } catch (err) {
                mediaBox.innerHTML = originalHtml;
                showToast('Error generating photo', 'error');
            }
        }

        function updatePhotoCard(itemId, mode, url, index) {
            const isModel = mode === 'with_model';
            const mediaId = isModel ? `media_model_${itemId}` : `media_studio_${itemId}`;
            const badgeId = isModel ? `badge_model_${itemId}` : `badge_studio_${itemId}`;
            const btnId = isModel ? `btn_save_model_${itemId}` : `btn_save_studio_${itemId}`;

            const mediaBox = document.getElementById(mediaId);
            const badge = document.getElementById(badgeId);
            const saveBtn = document.getElementById(btnId);

            if (mediaBox) {
                mediaBox.innerHTML = `<img src="${url}" alt="${mode}" onclick="openLightbox('${url}', '${mode}')">`;
            }
            if (badge) {
                badge.innerText = 'Generated (Ready to Save)';
                badge.style.color = '#0284c7';
            }
            if (saveBtn) {
                saveBtn.disabled = false;
            }
        }

        // ==================== BATCH GENERATION ====================
        async function generateAllBatch() {
            if (isBatchRunning) return;
            if (loadedProducts.length === 0) {
                showToast('No products loaded in queue.', 'error');
                return;
            }

            isBatchRunning = true;
            const btn = document.getElementById('btn_generate_all');
            const originalText = btn.innerHTML;
            btn.disabled = true;

            const progressContainer = document.getElementById('batch_progress_bar_container');
            const progressBar = document.getElementById('batch_progress_bar');
            progressContainer.classList.remove('hidden');
            progressBar.style.width = '0%';

            for (let i = 0; i < loadedProducts.length; i++) {
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Processing ${i + 1}/${loadedProducts.length}...`;
                await generateSingleAllInOne(i);

                const pct = Math.round(((i + 1) / loadedProducts.length) * 100);
                progressBar.style.width = `${pct}%`;
            }

            btn.disabled = false;
            btn.innerHTML = originalText;
            isBatchRunning = false;
            showToast('Batch generation completed for all products!');
        }

        // ==================== SAVE ACTIONS ====================
        async function saveSingleProduct(index) {
            const item = loadedProducts[index];
            if (!item) return;

            const name = document.getElementById(`input_name_${item.id}`).value.trim();
            const shortDesc = document.getElementById(`input_short_${item.id}`).value.trim();
            const desc = document.getElementById(`input_desc_${item.id}`).value.trim();

            const withModelB64 = item.photo_with_model ? item.photo_with_model.base64 : '';
            const withoutModelB64 = item.photo_without_model ? item.photo_without_model.base64 : '';

            const btn = document.getElementById(`btn_save_single_${item.id}`);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=saveAll', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        name: name,
                        short_description: shortDesc,
                        description: desc,
                        with_model_base64: withModelB64,
                        without_model_base64: withoutModelB64
                    })
                });
                const data = await res.json();
                if (data.success) {
                    item.saved_with_model = true;
                    item.saved_without_model = true;
                    const pill = document.getElementById(`status_pill_${item.id}`);
                    if (pill) pill.innerHTML = '<span class="status-dot active"></span> Saved to DB';

                    if (item.photo_with_model) {
                        const b1 = document.getElementById(`badge_model_${item.id}`);
                        if (b1) b1.innerText = 'Saved to Gallery';
                    }
                    if (item.photo_without_model) {
                        const b2 = document.getElementById(`badge_studio_${item.id}`);
                        if (b2) b2.innerText = 'Saved to Gallery';
                    }

                    showToast(`Product ${item.code} saved to database successfully!`);
                } else {
                    showToast(data.error || 'Failed to save product', 'error');
                }
            } catch (err) {
                showToast('Error saving product', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save to DB';
            }
        }

        async function saveImageIndividual(index, mode) {
            const item = loadedProducts[index];
            if (!item) return;

            const photo = mode === 'with_model' ? item.photo_with_model : item.photo_without_model;
            if (!photo || !photo.base64) {
                showToast('No generated image data found', 'error');
                return;
            }

            const btnId = mode === 'with_model' ? `btn_save_model_${item.id}` : `btn_save_studio_${item.id}`;
            const btn = document.getElementById(btnId);
            btn.disabled = true;

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=saveImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        base64: photo.base64,
                        mode: mode,
                        set_as_main: false
                    })
                });
                const data = await res.json();
                if (data.success) {
                    const badgeId = mode === 'with_model' ? `badge_model_${item.id}` : `badge_studio_${item.id}`;
                    document.getElementById(badgeId).innerText = 'Saved to Gallery';
                    showToast('Image saved to product gallery!');
                } else {
                    showToast(data.error || 'Failed to save image', 'error');
                }
            } catch (err) {
                showToast('Error saving image', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        async function saveAllGlobal() {
            if (loadedProducts.length === 0) return;
            const btn = document.getElementById('btn_save_all_global');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving All...';

            for (let i = 0; i < loadedProducts.length; i++) {
                await saveSingleProduct(i);
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save All to Database';
            showToast('All products saved to database!');
        }

        // ==================== UTILITIES ====================
        function updateCharCount(id, type, max) {
            const input = document.getElementById(`input_${type}_${id}`);
            const counter = document.getElementById(`char_${type}_${id}`);
            if (!input || !counter) return;

            const len = input.value.length;
            counter.innerText = `${len}/${max}`;
            if (len > max) {
                counter.classList.add('warning');
            } else {
                counter.classList.remove('warning');
            }
        }

        function copyText(elementId) {
            const el = document.getElementById(elementId);
            if (!el) return;
            navigator.clipboard.writeText(el.value);
            showToast('Copied to clipboard!');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function openLightbox(url, title) {
            const box = document.getElementById('image-lightbox');
            const img = document.getElementById('lightbox-img');
            const caption = document.getElementById('lightbox-caption');
            const download = document.getElementById('lightbox-download');

            img.src = url;
            caption.innerText = title || 'Preview';
            download.href = url;
            box.classList.add('open');
        }

        function closeLightbox() {
            document.getElementById('image-lightbox').classList.remove('open');
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'shadcn-toast';
            
            const icon = type === 'error' ? 'fa-triangle-exclamation text-rose-400' : 'fa-circle-check text-emerald-400';
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${escapeHtml(message)}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.2s ease';
                setTimeout(() => toast.remove(), 200);
            }, 3200);
        }
    </script>
</body>
</html>
