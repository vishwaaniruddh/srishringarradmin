<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Product Video Generator &amp; Showcase Studio - Srishringarr Luxury Admin</title>
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
            $pageTitle = 'AI Product Video Generator & Showcase Studio';
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
                                    <span>AI Product Video Generator &amp; Showcase Studio</span>
                                </h1>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                                    <i class="fa-solid fa-sparkles text-emerald-600"></i>
                                    <span>Up to 30 Seconds Showcase Ready</span>
                                </span>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1.5">
                                    <i class="fa-solid fa-bolt text-slate-600"></i>
                                    <span>Gemini 3.8 Flash Engine</span>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Create luxury 5s to 30s product showcases, 360° turntable orbits, and multi-angle reels with specular diamond sparkles, mirror reflections, and audio for WhatsApp Status &amp; Instagram.
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
                    <div class="shadcn-card p-3.5 bg-slate-900 text-white flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3">
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

                            <!-- Engine Selector -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-amber-300 uppercase font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i> Engine:
                                </span>
                                <select id="videoEngine" onchange="onEngineChange(this.value)" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded text-xs text-white font-semibold outline-none focus:border-slate-500">
                                    <option value="studio" selected>AI Luxury Studio Showcase (Up to 30s • Multi-Angle &amp; Audio)</option>
                                    <option value="veo">Google Veo 3.1 AI (4s – 8s Preview)</option>
                                </select>
                            </div>

                            <!-- Duration Option (Up to 30 Seconds) -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Duration:</span>
                                <select id="batchDuration" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none focus:border-slate-500">
                                    <option value="30" selected>30 Seconds (Full 360° Masterpiece Showcase • WhatsApp &amp; Reels)</option>
                                    <option value="25">25 Seconds (High-Fashion Runway Presentation)</option>
                                    <option value="20">20 Seconds (Extended Luxury Showcase)</option>
                                    <option value="15">15 Seconds (Standard Commercial Reel)</option>
                                    <option value="10">10 Seconds (Instagram Story / Snapshot)</option>
                                    <option value="5">5 Seconds (Quick Social Teaser)</option>
                                </select>
                            </div>

                            <!-- Motion Preset -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Style:</span>
                                <select id="batchPreset" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none focus:border-slate-500">
                                    <option value="luxury_showcase" selected>Royal 360° Masterpiece &amp; Detail Tour</option>
                                    <option value="turntable_360">360° Orbit Turntable &amp; Reflection</option>
                                    <option value="macro_sparkle">Macro Diamond Sparkle &amp; Detail Zoom</option>
                                    <option value="bridal_glamour">Royal Bridal Heritage &amp; Golden Radiance</option>
                                    <option value="runway_spotlight">High-Fashion Runway Spotlight</option>
                                    <option value="cinematic_kenburns">Cinematic Slow Pan &amp; Flare</option>
                                </select>
                            </div>

                            <!-- Aspect Ratio -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Format:</span>
                                <select id="batchAspect" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none focus:border-slate-500">
                                    <option value="9:16" selected>9:16 Vertical Reel (Instagram / WhatsApp Status)</option>
                                    <option value="1:1">1:1 Square (Feed &amp; Catalog)</option>
                                    <option value="16:9">16:9 Landscape (YouTube &amp; Web Banner)</option>
                                    <option value="4:5">4:5 Portrait (Instagram Feed)</option>
                                </select>
                            </div>

                            <!-- Audio Selector -->
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] text-slate-400 uppercase font-semibold">Audio:</span>
                                <select id="batchAudio" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded text-xs text-white font-medium outline-none focus:border-slate-500">
                                    <option value="royal_ambient" selected>Royal Bridal Chimes &amp; Ambient Harmony</option>
                                    <option value="none">Mute (Silent Video)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Button: Batch Generate -->
                        <div class="flex items-center gap-2 w-full xl:w-auto mt-2 xl:mt-0">
                            <button type="button" id="startBatchBtn" onclick="startBatchGeneration()" disabled class="shadcn-btn bg-emerald-600 hover:bg-emerald-500 text-white font-bold border-emerald-500 py-2 px-5 text-xs shadow-md disabled:opacity-40 disabled:cursor-not-allowed flex-1 xl:flex-none">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>Generate Videos for Selected (<span id="batchBtnCount">0</span>)</span>
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
                                Real catalog inventory with full resolution photos &amp; multi-angle transitions
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
                                        <th style="width: 200px; text-align: right;">ACTION</th>
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

    <!-- SINGLE PRODUCT CUSTOMIZE & GENERATE MODAL -->
    <div id="customConfigModal" class="fixed inset-0 bg-black/75 backdrop-blur-xs hidden items-center justify-center p-4 z-50">
        <div class="bg-white border border-slate-200 rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Custom Showcase Settings</h3>
                        <p class="text-xs text-slate-500" id="customModalSkuBadge">SKU: PROD</p>
                    </div>
                </div>
                <button type="button" onclick="closeCustomModal()" class="w-7 h-7 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Product Summary & Gallery Photos -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center gap-3">
                <img id="customModalImg" src="assets/default-product.jpg" class="w-14 h-14 rounded object-cover border border-slate-200 bg-slate-900">
                <div class="min-w-0 flex-1">
                    <div id="customModalTitle" class="text-xs font-bold text-slate-900 truncate">Product Name</div>
                    <div class="text-[11px] text-slate-500 mt-0.5" id="customModalPrices">Rent: ₹0 • Sale: ₹0</div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1.5" id="customModalPhotosCount">
                        <i class="fa-solid fa-images"></i> <span>1 Photo Available</span>
                    </div>
                </div>
            </div>

            <!-- Modal Form Controls -->
            <div class="space-y-3">
                <!-- Duration Selector (5s to 30s) -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block mb-1">
                        Showcase Duration (Upto 30 Seconds)
                    </label>
                    <select id="singleDuration" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800">
                        <option value="30" selected>30 Seconds (Full 360° Masterpiece Showcase • WhatsApp &amp; Reels)</option>
                        <option value="25">25 Seconds (High-Fashion Runway Presentation)</option>
                        <option value="20">20 Seconds (Extended Luxury Showcase)</option>
                        <option value="15">15 Seconds (Standard Commercial Reel)</option>
                        <option value="10">10 Seconds (Instagram Story / Snapshot)</option>
                        <option value="5">5 Seconds (Quick Social Teaser)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Style Preset -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block mb-1">
                            Motion Style
                        </label>
                        <select id="singlePreset" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800">
                            <option value="luxury_showcase" selected>Royal 360° Masterpiece</option>
                            <option value="turntable_360">360° Orbit Turntable</option>
                            <option value="macro_sparkle">Macro Sparkle &amp; Zoom</option>
                            <option value="bridal_glamour">Royal Bridal Heritage</option>
                            <option value="runway_spotlight">High-Fashion Runway</option>
                            <option value="cinematic_kenburns">Cinematic Slow Pan</option>
                        </select>
                    </div>

                    <!-- Aspect Ratio -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block mb-1">
                            Aspect Ratio
                        </label>
                        <select id="singleAspect" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800">
                            <option value="9:16" selected>9:16 Vertical Reel</option>
                            <option value="1:1">1:1 Square Feed</option>
                            <option value="16:9">16:9 Landscape</option>
                            <option value="4:5">4:5 Portrait</option>
                        </select>
                    </div>
                </div>

                <!-- Audio Track -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block mb-1">
                        Audio Soundtrack
                    </label>
                    <select id="singleAudio" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 outline-none focus:border-slate-800">
                        <option value="royal_ambient" selected>Royal Bridal Chimes &amp; Ambient Harmony (Web Audio)</option>
                        <option value="none">Mute (Silent Video)</option>
                    </select>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCustomModal()" class="shadcn-btn text-xs text-slate-600 hover:bg-slate-100">
                    Cancel
                </button>
                <button type="button" onclick="launchSingleCustomGeneration()" class="shadcn-btn shadcn-btn-primary text-xs py-2 px-4 font-bold bg-emerald-600 hover:bg-emerald-500 border-emerald-500">
                    <i class="fa-solid fa-film mr-1"></i> Start Generation
                </button>
            </div>
        </div>
    </div>

    <!-- BATCH / SINGLE GENERATION PROGRESS MODAL -->
    <div id="batchProgressModal" class="fixed inset-0 bg-black/75 backdrop-blur-xs hidden items-center justify-center p-4 z-50">
        <div class="bg-white border border-slate-200 rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-lg flex-shrink-0 animate-pulse">
                    <i class="fa-solid fa-film"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Generating Product Showcase Video</h3>
                    <p class="text-xs text-slate-500" id="batchModalSubtitle">Rendering cinematic frames &amp; audio track...</p>
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
                    <div id="batchModalStatus" class="text-[11px] text-emerald-600 font-medium mt-0.5">Calling AI motion blueprint...</div>
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

            <div class="w-full flex items-center justify-center bg-black rounded-xl overflow-hidden max-h-[520px]">
                <video id="playerVideoElement" controls autoplay loop playsinline class="max-h-[500px] max-w-full rounded-lg shadow-lg"></video>
            </div>

            <div class="flex items-center justify-between w-full pt-1">
                <span class="text-xs text-slate-400" id="playerModalInfo">30s • Full HD Showcase</span>
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
        let targetCustomSku = null;

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
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Showcase Ready (${vidData.duration || 30}s)
                            </span>
                        </div>
                    `;
                    actionBtn = `
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" onclick="playProductVideo('${vidData.video_url}', '${p.sku}', \`${(p.name || '').replace(/`/g, '\\`')}\`)" class="shadcn-btn py-1 px-2.5 text-xs text-slate-800 hover:bg-slate-100" title="Play Showcase Video">
                                <i class="fa-solid fa-play text-xs text-emerald-600"></i> Play
                            </button>
                            <a href="${resolveVideoUrl(vidData.video_url)}" download="${p.sku}_showcase.${(vidData.video_url && vidData.video_url.endsWith('.mp4')) ? 'mp4' : 'webm'}" class="shadcn-btn py-1 px-2 text-xs text-slate-700" title="Download Video">
                                <i class="fa-solid fa-download text-xs"></i>
                            </a>
                            <button type="button" onclick="openCustomConfigModal('${p.sku}')" class="shadcn-btn py-1 px-2 text-xs text-slate-500 hover:text-slate-900" title="Customize & Regenerate (Upto 30s)">
                                <i class="fa-solid fa-sliders text-xs"></i>
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
                            <button type="button" onclick="generateSingleProductVideo('${p.sku}')" class="shadcn-btn shadcn-btn-primary py-1 px-2.5 text-xs font-semibold" title="Quick Generate Video">
                                <i class="fa-solid fa-film text-xs"></i> Generate
                            </button>
                            <button type="button" onclick="openCustomConfigModal('${p.sku}')" class="shadcn-btn py-1 px-2 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-50" title="Configure Duration (upto 30s) & Style">
                                <i class="fa-solid fa-sliders text-xs"></i>
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
                                    ${p.photos_count > 1 ? `<span class="text-[10px] text-emerald-600 font-sans font-semibold">(${p.photos_count} photos)</span>` : ''}
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
        // AI ENGINE SWITCHER & DURATION LOGIC
        // =========================================================================
        function onEngineChange(engine) {
            const durationSelect = document.getElementById('batchDuration');
            const presetSelect = document.getElementById('batchPreset');
            const btnText = document.querySelector('#startBatchBtn span');

            if (engine === 'veo') {
                durationSelect.innerHTML = `
                    <option value="6" selected>6 Seconds (Cinematic Commercial)</option>
                    <option value="8">8 Seconds (Extended Reel)</option>
                    <option value="4">4 Seconds (Quick Sparkle)</option>
                `;
                presetSelect.innerHTML = `
                    <option value="turntable" selected>360° Luxury Orbit Turntable</option>
                    <option value="macro_sparkle">Macro Sparkle &amp; Diamond Glints</option>
                    <option value="bridal_glamour">Bridal Glamour &amp; Golden Light</option>
                    <option value="editorial">Runway Editorial Commercial</option>
                `;
                if (btnText) {
                    btnText.innerHTML = `Generate Videos for Selected (<span id="batchBtnCount">${selectedProductSkus.size}</span>) with Veo 3.1`;
                }
            } else {
                durationSelect.innerHTML = `
                    <option value="30" selected>30 Seconds (Full 360° Masterpiece Showcase • WhatsApp &amp; Reels)</option>
                    <option value="25">25 Seconds (High-Fashion Runway Presentation)</option>
                    <option value="20">20 Seconds (Extended Luxury Showcase)</option>
                    <option value="15">15 Seconds (Standard Commercial Reel)</option>
                    <option value="10">10 Seconds (Instagram Story / Snapshot)</option>
                    <option value="5">5 Seconds (Quick Social Teaser)</option>
                `;
                presetSelect.innerHTML = `
                    <option value="luxury_showcase" selected>Royal 360° Masterpiece &amp; Detail Tour</option>
                    <option value="turntable_360">360° Orbit Turntable &amp; Reflection</option>
                    <option value="macro_sparkle">Macro Diamond Sparkle &amp; Detail Zoom</option>
                    <option value="bridal_glamour">Royal Bridal Heritage &amp; Golden Radiance</option>
                    <option value="runway_spotlight">High-Fashion Runway Spotlight</option>
                    <option value="cinematic_kenburns">Cinematic Slow Pan &amp; Flare</option>
                `;
                if (btnText) {
                    btnText.innerHTML = `Generate Videos for Selected (<span id="batchBtnCount">${selectedProductSkus.size}</span>)`;
                }
            }
        }

        // =========================================================================
        // CUSTOM SINGLE PRODUCT MODAL
        // =========================================================================
        function openCustomConfigModal(sku) {
            const product = currentProducts.find(p => p.sku === sku);
            if (!product) return;
            targetCustomSku = sku;

            document.getElementById('customModalSkuBadge').textContent = 'SKU: ' + sku;
            document.getElementById('customModalTitle').textContent = product.name || sku;
            document.getElementById('customModalImg').src = product.primary_image || 'assets/default-product.jpg';
            document.getElementById('customModalPrices').textContent = `Rent: ₹${Number(product.rent_price || 0).toLocaleString()} • Sale: ₹${Number(product.price || 0).toLocaleString()}`;
            
            const photoCount = product.photos_count || (product.photos ? product.photos.length : 1);
            document.getElementById('customModalPhotosCount').innerHTML = `
                <i class="fa-solid fa-images"></i> 
                <span>${photoCount} Photo${photoCount > 1 ? 's (Multi-Angle Tour Available)' : ' Available'}</span>
            `;

            // Preselect batch values
            document.getElementById('singleDuration').value = document.getElementById('batchDuration').value || '30';
            document.getElementById('singlePreset').value = document.getElementById('batchPreset').value || 'luxury_showcase';
            document.getElementById('singleAspect').value = document.getElementById('batchAspect').value || '9:16';
            document.getElementById('singleAudio').value = document.getElementById('batchAudio').value || 'royal_ambient';

            const modal = document.getElementById('customConfigModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCustomModal() {
            const modal = document.getElementById('customConfigModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            targetCustomSku = null;
        }

        function launchSingleCustomGeneration() {
            if (!targetCustomSku) return;
            const sku = targetCustomSku;
            const duration = parseInt(document.getElementById('singleDuration').value) || 30;
            const preset = document.getElementById('singlePreset').value;
            const aspect = document.getElementById('singleAspect').value;
            const audio = document.getElementById('singleAudio').value;

            closeCustomModal();

            // Run batch for this single product with custom overrides
            selectedProductSkus.clear();
            selectedProductSkus.add(sku);
            updateSelectedCount();

            // Override temporary settings
            startBatchGeneration({
                duration: duration,
                preset: preset,
                aspect: aspect,
                audio: audio
            });
        }

        // Quick Single Product Video Generation Trigger
        async function generateSingleProductVideo(sku) {
            selectedProductSkus.clear();
            selectedProductSkus.add(sku);
            updateSelectedCount();
            startBatchGeneration();
        }

        // =========================================================================
        // GOOGLE VEO 3.1 GENERATION (MAX 8 SECONDS)
        // =========================================================================
        async function generateVeoVideo(product, duration, preset, aspect, onProgress) {
            if (duration > 8) {
                duration = 8; // Google Veo API max duration
            }
            if (onProgress) onProgress('Submitting to Google Veo 3.1 AI...');

            const startRes = await fetch('index.php?controller=video_generator&action=startVeoGeneration', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: product.id,
                    sku: product.sku,
                    name: product.name,
                    type: product.type || 'jewellery',
                    duration: duration,
                    preset: preset,
                    aspect_ratio: aspect,
                    image_url: product.primary_image
                })
            });

            const startData = await startRes.json();
            if (!startRes.ok || !startData.success || !startData.operation_name) {
                throw new Error(startData.error || 'Failed to initialize Google Veo generation.');
            }

            const opName = startData.operation_name;
            const startTime = Date.now();

            while (true) {
                if (shouldCancelBatch) {
                    throw new Error('Cancelled by user.');
                }

                await new Promise(r => setTimeout(r, 3500));
                const elapsedSec = Math.round((Date.now() - startTime) / 1000);
                if (onProgress) {
                    onProgress(`Veo 3.1 rendering photorealistic commercial (${elapsedSec}s elapsed)...`);
                }

                const checkRes = await fetch('index.php?controller=video_generator&action=checkVeoOperation', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation_name: opName,
                        product_id: product.id,
                        sku: product.sku,
                        name: product.name,
                        type: product.type || 'jewellery',
                        duration: duration,
                        preset: preset,
                        aspect_ratio: aspect
                    })
                });

                const checkData = await checkRes.json();
                if (!checkRes.ok || checkData.error) {
                    throw new Error(checkData.error || 'Veo generation error');
                }

                if (checkData.done) {
                    if (!checkData.success) {
                        throw new Error(checkData.error || 'Veo did not return a valid video.');
                    }
                    return checkData;
                }

                if (elapsedSec > 180) {
                    throw new Error('Google Veo operation timed out after 3 minutes.');
                }
            }
        }

        // =========================================================================
        // BATCH & SINGLE VIDEO MOTION GENERATION ENGINE
        // =========================================================================
        let lastSavedVideo = null;

        async function startBatchGeneration(overrides = null) {
            if (selectedProductSkus.size === 0 || isBatchRunning) return;
            lastSavedVideo = null;

            const selectedSkus = Array.from(selectedProductSkus);
            const total = selectedSkus.length;
            const engine = document.getElementById('videoEngine')?.value || 'studio';
            
            const duration = overrides?.duration || parseInt(document.getElementById('batchDuration').value) || 30;
            const preset = overrides?.preset || document.getElementById('batchPreset').value || 'luxury_showcase';
            const aspect = overrides?.aspect || document.getElementById('batchAspect').value || '9:16';
            const audioOption = overrides?.audio || document.getElementById('batchAudio').value || 'royal_ambient';

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
                currentProductText.textContent = `Generating ${sku} (${duration}s • ${engine === 'veo' ? 'Veo 3.1 AI' : 'Studio Showcase'})`;
                modalThumb.src = product.primary_image;
                modalSku.textContent = sku;
                modalTitle.textContent = product.name;

                // Update Table Row Status to Spinning
                const statusCell = document.getElementById(`status_cell_${sku}`);
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                            <i class="fa-solid fa-spinner fa-spin text-xs text-amber-600"></i> Rendering (${duration}s)...
                        </span>
                    `;
                }

                try {
                    let saveRes = null;

                    if (engine === 'veo') {
                        modalStatus.textContent = 'Submitting prompt & product photography to Google Veo 3.1...';
                        saveRes = await generateVeoVideo(product, duration, preset, aspect, (msg) => {
                            modalStatus.textContent = msg;
                        });
                    } else {
                        modalStatus.textContent = `Planning choreography for ${duration}s showcase...`;
                        const blueprint = await callGeminiMotionPlanner(product, duration, preset);
                        modalStatus.textContent = `Preloading photos & synthesizing royal audio (${duration}s)...`;
                        const videoBlob = await renderProductVideoToBlob(product, duration, preset, aspect, blueprint, audioOption, (progPct, note) => {
                            const subPct = Math.floor(pct + (progPct / total));
                            progressBar.style.width = subPct + '%';
                            progressPercent.textContent = subPct + '%';
                            modalStatus.textContent = note;
                        });
                        modalStatus.textContent = 'Saving showcase video to server...';
                        saveRes = await saveVideoToServer(videoBlob, product, duration, preset, aspect);
                    }

                    if (saveRes && saveRes.success) {
                        successCount++;
                        lastSavedVideo = {
                            url: saveRes.url,
                            sku: product.sku,
                            name: product.name,
                            duration: saveRes.duration || duration
                        };
                        // Update Table Row with Green Success Badge & Play button
                        if (statusCell) {
                            statusCell.innerHTML = `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Showcase Ready (${saveRes.duration || duration}s)
                                </span>
                            `;
                        }
                        const actionCell = document.getElementById(`action_cell_${sku}`);
                        if (actionCell) {
                            const dlExt = (saveRes.url && saveRes.url.endsWith('.mp4')) ? 'mp4' : 'webm';
                            actionCell.innerHTML = `
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="playProductVideo('${saveRes.url}', '${product.sku}', \`${(product.name || '').replace(/`/g, '\\`')}\`)" class="shadcn-btn py-1 px-2.5 text-xs text-slate-800 hover:bg-slate-100" title="Play Video">
                                        <i class="fa-solid fa-play text-xs text-emerald-600"></i> Play
                                    </button>
                                    <a href="${resolveVideoUrl(saveRes.url)}" download="${product.sku}_showcase_${duration}s.${dlExt}" class="shadcn-btn py-1 px-2 text-xs text-slate-700" title="Download Video">
                                        <i class="fa-solid fa-download text-xs"></i>
                                    </a>
                                    <button type="button" onclick="openCustomConfigModal('${product.sku}')" class="shadcn-btn py-1 px-2 text-xs text-slate-500 hover:text-slate-900" title="Customize (Upto 30s)">
                                        <i class="fa-solid fa-sliders text-xs"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                } catch (err) {
                    console.error(`Error generating video for ${sku}:`, err);
                    if (statusCell) {
                        statusCell.innerHTML = `
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="${(err.message || '').replace(/"/g, '&quot;')}">
                                <i class="fa-solid fa-triangle-exclamation"></i> Error
                            </span>
                        `;
                    }
                }
            }

            // Finished All
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            modalStatus.textContent = `Complete! ${successCount} of ${total} videos generated.`;

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                isBatchRunning = false;
                if (total === 1 && successCount === 1 && lastSavedVideo) {
                    playProductVideo(lastSavedVideo.url, lastSavedVideo.sku, lastSavedVideo.name);
                } else if (successCount > 0) {
                    alert(`Successfully generated and saved ${successCount} cinematic product showcase videos!`);
                }
            }, 600);
        }

        function cancelBatch() {
            shouldCancelBatch = true;
            document.getElementById('batchModalStatus').textContent = 'Cancelling process...';
        }

        // Call Gemini to calculate custom motion choreography parameters
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

        // =========================================================================
        // ROYAL AUDIO SYNTHESIZER (WEB AUDIO API)
        // Generates an ethereal, high-luxury bridal ambient score with chimes
        // =========================================================================
        function startRoyalAmbientAudio(audioCtx, dest, duration) {
            const masterGain = audioCtx.createGain();
            masterGain.gain.setValueAtTime(0.01, audioCtx.currentTime);
            masterGain.gain.exponentialRampToValueAtTime(0.35, audioCtx.currentTime + 1.2);
            masterGain.gain.setValueAtTime(0.35, audioCtx.currentTime + duration - 1.5);
            masterGain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
            masterGain.connect(dest);

            // 1. Warm Royal Tanpura Drone Pad (D Root chord: 73.4Hz & 146.8Hz)
            const rootFreqs = [73.42, 110.0, 146.83, 220.0];
            rootFreqs.forEach((freq, idx) => {
                const osc = audioCtx.createOscillator();
                const padGain = audioCtx.createGain();
                const filter = audioCtx.createBiquadFilter();

                osc.type = (idx % 2 === 0) ? 'sawtooth' : 'triangle';
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);

                filter.type = 'lowpass';
                filter.frequency.setValueAtTime(450, audioCtx.currentTime);

                padGain.gain.setValueAtTime(0.06 / (idx + 1), audioCtx.currentTime);

                osc.connect(filter);
                filter.connect(padGain);
                padGain.connect(masterGain);

                osc.start();
                osc.stop(audioCtx.currentTime + duration);
            });

            // 2. Melodic Celestial Harp & Sitar Chimes (Pentatonic notes: D, F#, A, B, C#)
            const pentatonic = [293.66, 369.99, 440.0, 493.88, 554.37, 587.33, 739.99, 880.0];
            const chimeInterval = 2.4; // seconds between notes
            const numNotes = Math.floor(duration / chimeInterval);

            for (let i = 0; i < numNotes; i++) {
                const noteTime = audioCtx.currentTime + 0.6 + (i * chimeInterval);
                const noteFreq = pentatonic[i % pentatonic.length];

                const osc = audioCtx.createOscillator();
                const noteGain = audioCtx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(noteFreq, noteTime);

                noteGain.gain.setValueAtTime(0.0001, noteTime);
                noteGain.gain.exponentialRampToValueAtTime(0.18, noteTime + 0.04);
                noteGain.gain.exponentialRampToValueAtTime(0.0001, noteTime + 2.0);

                osc.connect(noteGain);
                noteGain.connect(masterGain);

                osc.start(noteTime);
                osc.stop(noteTime + 2.1);
            }
        }

        // =========================================================================
        // OFFSCREEN CANVAS VIDEO RENDERER (UP TO 30s, MULTI-PHOTO & AUDIO)
        // =========================================================================
        function renderProductVideoToBlob(product, duration, preset, aspect, blueprint, audioOption, onProgress) {
            return new Promise(async (resolve, reject) => {
                try {
                    // Set canvas resolution based on aspect ratio
                    let w = 720, h = 1280;
                    if (aspect === '1:1') { w = 1080; h = 1080; }
                    else if (aspect === '16:9') { w = 1280; h = 720; }
                    else if (aspect === '4:5') { w = 864; h = 1080; }
                    renderCanvas.width = w;
                    renderCanvas.height = h;

                    // 1. Preload ALL Product Photos for Multi-Angle Storyboard
                    const rawPhotos = (product.photos && product.photos.length > 0)
                        ? product.photos.map(p => p.url)
                        : [product.primary_image || 'assets/default-product.jpg'];

                    const loadedImages = await Promise.all(rawPhotos.slice(0, 5).map(src => {
                        return new Promise((imgRes) => {
                            const img = new Image();
                            img.crossOrigin = 'anonymous';
                            let url = src;
                            if (url.startsWith('http') && url.includes('srishringarr.com')) {
                                url = 'index.php?controller=video_generator&action=proxyImage&url=' + encodeURIComponent(url);
                            }
                            img.onload = () => imgRes(img);
                            img.onerror = () => {
                                const fallback = new Image();
                                fallback.onload = () => imgRes(fallback);
                                fallback.src = 'assets/default-product.jpg';
                            };
                            img.src = url;
                        });
                    }));

                    // 2. Setup Canvas Stream & Optional Royal Audio
                    const fps = 30;
                    const totalFrames = duration * fps;
                    const frameDuration = 1 / fps;
                    const stream = renderCanvas.captureStream(fps);

                    let audioCtx = null;
                    if (audioOption === 'royal_ambient') {
                        try {
                            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                            audioCtx = new AudioContextClass();
                            const audioDest = audioCtx.createMediaStreamDestination();
                            startRoyalAmbientAudio(audioCtx, audioDest, duration);
                            const audioTracks = audioDest.stream.getAudioTracks();
                            if (audioTracks.length > 0) {
                                stream.addTrack(audioTracks[0]);
                            }
                        } catch (audioErr) {
                            console.warn('Web Audio synthesis not supported:', audioErr);
                        }
                    }

                    // 3. Setup Hardware MediaRecorder
                    let mimeType = 'video/webm;codecs=vp9';
                    if (!MediaRecorder.isTypeSupported(mimeType)) {
                        mimeType = 'video/webm;codecs=vp8';
                        if (!MediaRecorder.isTypeSupported(mimeType)) mimeType = 'video/webm';
                    }

                    const recorder = new MediaRecorder(stream, {
                        mimeType: mimeType,
                        videoBitsPerSecond: 10000000 // 10 Mbps pristine ultra-high bitrate
                    });

                    const chunks = [];
                    recorder.ondataavailable = e => { if (e.data && e.data.size > 0) chunks.push(e.data); };
                    const stopPromise = new Promise(r => recorder.onstop = r);

                    recorder.start(100);

                    // 4. Procedural Frame Rendering Loop
                    for (let f = 0; f < totalFrames; f++) {
                        if (shouldCancelBatch) {
                            recorder.stop();
                            if (audioCtx) audioCtx.close();
                            throw new Error('Cancelled by user.');
                        }

                        const t = f * frameDuration;
                        drawProductMotionFrame(renderCtx, w, h, t, duration, preset, loadedImages, product, blueprint);

                        if (f % 15 === 0 && onProgress) {
                            const pct = Math.floor((f / totalFrames) * 100);
                            onProgress(pct, `Rendering frame ${f + 1} / ${totalFrames} (${Math.round(t)}s / ${duration}s)...`);
                        }

                        await new Promise(r => setTimeout(r, 1000 / fps));
                    }

                    recorder.stop();
                    await stopPromise;
                    if (audioCtx) audioCtx.close();

                    const blob = new Blob(chunks, { type: mimeType });
                    resolve(blob);

                } catch (err) {
                    reject(err);
                }
            });
        }

        // =========================================================================
        // PROCEDURAL CANVAS FRAME DRAWING ENGINE ("EKDUM MAST SHOWCASE")
        // Mirror floor reflection, 3D turntable, golden bokeh, diamond starbursts
        // =========================================================================
        function drawProductMotionFrame(ctx, w, h, t, duration, preset, images, product, blueprint) {
            const progress = (t % duration) / duration;
            const imgCount = images.length;

            // 1. Scene Angle & Multi-Photo Switching
            const sceneDuration = duration / Math.max(1, imgCount);
            const sceneIndex = Math.min(imgCount - 1, Math.floor(t / sceneDuration));
            const sceneProgress = (t - (sceneIndex * sceneDuration)) / sceneDuration;
            const currentImg = images[sceneIndex];
            const nextImg = images[(sceneIndex + 1) % imgCount];

            // 2. Dark Luxury Obsidian Studio Gradient
            const bg = ctx.createRadialGradient(w/2, h*0.46, 50, w/2, h/2, Math.max(w, h)*0.78);
            bg.addColorStop(0, '#1c1917');
            bg.addColorStop(0.55, '#0c0a09');
            bg.addColorStop(1, '#050404');
            ctx.fillStyle = bg;
            ctx.fillRect(0, 0, w, h);

            // 3. Volumetric Warm Champagne Overhead Spotlight (Breathes softly)
            const glowOsc = Math.sin(progress * Math.PI * 4) * 0.15 + 0.85;
            const spot = ctx.createRadialGradient(w/2, h*0.42, 20, w/2, h*0.42, w*0.58);
            spot.addColorStop(0, `rgba(245, 158, 11, ${0.18 * glowOsc})`);
            spot.addColorStop(0.7, `rgba(217, 119, 6, ${0.05 * glowOsc})`);
            spot.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = spot;
            ctx.fillRect(0, 0, w, h);

            // 4. Parallax 3D Golden Dust & Floating Bokeh Embers
            const particleCount = 45;
            for (let i = 0; i < particleCount; i++) {
                const px = (Math.sin(i * 137.5 + t * 0.35) * 0.5 + 0.5) * w;
                const py = ((i * 89 - t * 28) % h + h) % h;
                const psize = (i % 4) + 1.2;
                const popac = Math.sin((py / h) * Math.PI) * 0.7;

                ctx.beginPath();
                ctx.arc(px, py, psize, 0, Math.PI * 2);
                ctx.fillStyle = (i % 2 === 0) ? `rgba(251, 191, 36, ${popac})` : `rgba(255, 255, 255, ${popac * 0.7})`;
                ctx.fill();
            }

            // 5. Motion Transformation Calculations
            let scale = 1.0;
            let transX = 0;
            let transY = 0;
            let rotY = 0;
            let tilt = 0;
            const amp = blueprint.zoom_amplitude || 1.16;

            if (preset === 'luxury_showcase' || preset === 'turntable_360') {
                // Multi-Phase 360° Luxury Orbit with Turntable Rotation
                const angle = (t / duration) * Math.PI * 2 * (duration > 15 ? 1.5 : 1.0);
                rotY = Math.sin(angle) * 0.16;
                transX = Math.sin(angle) * (w * 0.035);
                transY = Math.cos(angle * 0.5) * (h * 0.015);
                tilt = Math.sin(angle) * 0.025;
                scale = 1.02 + Math.cos(angle * 2) * 0.05 * amp;
            } else if (preset === 'macro_sparkle') {
                const macroP = Math.sin(progress * Math.PI);
                scale = 1.28 + macroP * 0.18 * amp;
                transX = (macroP - 0.5) * (w * 0.10);
                transY = (macroP - 0.5) * (h * 0.06);
            } else if (preset === 'runway_spotlight') {
                const sway = Math.sin(progress * Math.PI * 3);
                transX = sway * (w * 0.04);
                transY = Math.cos(progress * Math.PI * 3) * (h * 0.02);
                tilt = sway * 0.03;
                scale = 1.05 + Math.sin(progress * Math.PI * 2) * 0.06;
            } else if (preset === 'bridal_glamour') {
                scale = 1.04 + Math.sin(progress * Math.PI) * 0.08 * amp;
                tilt = Math.sin(progress * Math.PI * 2) * 0.02;
                transY = (progress - 0.5) * (h * 0.03);
            } else {
                // Cinematic Ken Burns Pan
                scale = 1.0 + progress * 0.20 * amp;
                transX = (progress - 0.5) * (w * 0.06);
            }

            const cx = w / 2;
            const cy = h * 0.46;

            // Target dimensions
            const iw = currentImg.naturalWidth || 600;
            const ih = currentImg.naturalHeight || 800;
            const targetW = w * 0.76;
            const targetH = h * 0.62;
            const ratio = Math.min(targetW / iw, targetH / ih);
            const dw = iw * ratio;
            const dh = ih * ratio;

            // 6. MIRROR FLOOR REFLECTION ON GLOSSY TURNTABLE
            const floorY = cy + (dh * 0.5) - 4;
            ctx.save();
            ctx.translate(cx + transX, floorY);
            ctx.scale(scale * (1 + rotY), -scale * 0.55); // Flipped & compressed perspective

            // Floor oval shadow
            ctx.beginPath();
            ctx.ellipse(0, 0, dw * 0.42, 18, 0, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
            ctx.fill();

            // Draw faded reflected image
            ctx.globalAlpha = 0.28;
            ctx.drawImage(currentImg, -dw / 2, -dh, dw, dh);

            // Gradient mask over reflection
            ctx.globalCompositeOperation = 'destination-out';
            const refMask = ctx.createLinearGradient(0, 0, 0, -dh);
            refMask.addColorStop(0, 'rgba(0,0,0,0)');
            refMask.addColorStop(0.6, 'rgba(0,0,0,0.85)');
            refMask.addColorStop(1, 'rgba(0,0,0,1)');
            ctx.fillStyle = refMask;
            ctx.fillRect(-dw, -dh, dw * 2, dh);
            ctx.restore();

            // 7. PRIMARY PRODUCT IMAGE WITH 3D TURNTABLE TRANSFORMATION
            ctx.save();
            ctx.translate(cx + transX, cy + transY);
            ctx.rotate(tilt);
            ctx.scale(scale * (1 + rotY), scale);

            // Deep studio drop shadow
            ctx.shadowColor = 'rgba(0, 0, 0, 0.85)';
            ctx.shadowBlur = 35;
            ctx.shadowOffsetY = 18;

            // Crossfade transition between multiple photos if at transition boundary
            const transitionWindow = 0.85; // last 15% of scene
            if (imgCount > 1 && sceneProgress > transitionWindow) {
                const crossfade = (sceneProgress - transitionWindow) / (1.0 - transitionWindow);
                ctx.globalAlpha = 1.0 - crossfade;
                ctx.drawImage(currentImg, -dw / 2, -dh / 2, dw, dh);

                ctx.globalAlpha = crossfade;
                const nIw = nextImg.naturalWidth || 600;
                const nIh = nextImg.naturalHeight || 800;
                const nRatio = Math.min(targetW / nIw, targetH / nIh);
                const ndw = nIw * nRatio;
                const ndh = nIh * nRatio;
                ctx.drawImage(nextImg, -ndw / 2, -ndh / 2, ndw, ndh);
            } else {
                ctx.drawImage(currentImg, -dw / 2, -dh / 2, dw, dh);
            }
            ctx.restore();

            // 8. SPECULAR DIAMOND STARBURST FLARES (Twinkling on jewelry)
            const flareBeats = [0.12, 0.28, 0.45, 0.62, 0.78, 0.92];
            flareBeats.forEach((fb, idx) => {
                const fPhase = (progress + fb) % 1.0;
                const intensity = Math.pow(Math.max(0, Math.sin(fPhase * Math.PI)), 7);
                if (intensity > 0.08) {
                    const fx = cx + transX + (idx % 2 === 0 ? -dw * 0.24 : dw * 0.24);
                    const fy = cy + transY + (idx % 3 === 0 ? -dh * 0.18 : (idx % 3 === 1 ? 0 : dh * 0.18));
                    drawDiamondFlare(ctx, fx, fy, intensity * 28, t * 2 + idx);
                }
            });

            // 9. SWEEPING ANAMORPHIC GOLDEN LIGHT STREAK
            const beamProgress = (progress * 1.6) % 1.0;
            const beamX = beamProgress * (w * 1.8) - (w * 0.4);
            const beam = ctx.createLinearGradient(beamX - 120, 0, beamX + 120, h);
            beam.addColorStop(0, 'rgba(255, 255, 255, 0)');
            beam.addColorStop(0.5, 'rgba(254, 240, 138, 0.16)');
            beam.addColorStop(1, 'rgba(255, 255, 255, 0)');
            ctx.fillStyle = beam;
            ctx.fillRect(0, 0, w, h);

            // 10. LUXURY VIGNETTE
            const vig = ctx.createRadialGradient(w/2, h/2, Math.min(w,h)*0.45, w/2, h/2, Math.max(w,h)*0.74);
            vig.addColorStop(0, 'rgba(0,0,0,0)');
            vig.addColorStop(1, 'rgba(0,0,0,0.68)');
            ctx.fillStyle = vig;
            ctx.fillRect(0, 0, w, h);

            // 11. TOP LUXURY WATERMARK & BRAND CREST
            const topY = h * 0.065;
            const shimmer = Math.sin(progress * Math.PI * 2) * 0.15 + 0.85;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.font = '700 17px "Playfair Display", Georgia, serif';
            ctx.letterSpacing = '3px';
            ctx.fillStyle = `rgba(254, 243, 199, ${shimmer})`;
            ctx.fillText('SRI SHRINGARR', w / 2, topY);

            ctx.font = '600 10px "Plus Jakarta Sans", sans-serif';
            ctx.letterSpacing = '2px';
            ctx.fillStyle = 'rgba(203, 213, 225, 0.80)';
            ctx.fillText('HAUTE JOAILLERIE & COUTURE • EST. 2004', w / 2, topY + 16);
            ctx.restore();

            // 12. BOTTOM GLASSMORPHIC PRODUCT CARD
            const cardW = w * 0.90;
            const cardH = 92;
            const cardX = (w - cardW) / 2;
            const cardY = h - cardH - (h * 0.04);

            ctx.save();
            // Frosted dark pill background
            ctx.fillStyle = 'rgba(15, 23, 42, 0.88)';
            ctx.strokeStyle = 'rgba(245, 158, 11, 0.35)';
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            ctx.roundRect(cardX, cardY, cardW, cardH, 12);
            ctx.fill();
            ctx.stroke();

            // SKU Badge & Authenticity Guarantee
            ctx.textAlign = 'left';
            ctx.font = '700 12px "Plus Jakarta Sans", monospace';
            ctx.fillStyle = '#fef08a';
            ctx.fillText(product.sku || 'STUDIO', cardX + 16, cardY + 24);

            ctx.font = '500 10px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = 'rgba(148, 163, 184, 0.9)';
            ctx.fillText('100% HANDCRAFTED LUXURY', cardX + 90, cardY + 24);

            // Product Title
            ctx.font = '600 12.5px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#ffffff';
            let titleText = product.name || 'Luxury Bridal Collection';
            if (titleText.length > 38) titleText = titleText.substring(0, 36) + '...';
            ctx.fillText(titleText, cardX + 16, cardY + 45);

            // Pricing Banner
            ctx.font = '700 12px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#34d399';
            const rentPriceText = `RENT: ₹${Number(product.rent_price || 0).toLocaleString()} / 3-DAYS`;
            ctx.fillText(rentPriceText, cardX + 16, cardY + 70);

            if (product.price && product.price > 0) {
                ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                ctx.fillStyle = 'rgba(203, 213, 225, 0.8)';
                ctx.fillText(`BUY: ₹${Number(product.price).toLocaleString()}`, cardX + 200, cardY + 70);
            }
            ctx.restore();

            // 13. TOP THIN GOLD TIMELINE PROGRESS BAR
            ctx.fillStyle = 'rgba(245, 158, 11, 0.85)';
            ctx.fillRect(0, 0, w * progress, 3);
        }

        // Helper: Draw sparkling multi-point diamond starburst flare
        function drawDiamondFlare(ctx, x, y, size, rotOffset) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rotOffset || 0);

            // Central golden halo
            const halo = ctx.createRadialGradient(0, 0, 0, 0, 0, size * 1.4);
            halo.addColorStop(0, 'rgba(255, 255, 255, 1.0)');
            halo.addColorStop(0.3, 'rgba(254, 240, 138, 0.7)');
            halo.addColorStop(1, 'rgba(0, 0, 0, 0)');
            ctx.fillStyle = halo;
            ctx.beginPath();
            ctx.arc(0, 0, size * 1.4, 0, Math.PI * 2);
            ctx.fill();

            // 4 Primary Diffraction Spikes
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.95)';
            ctx.lineWidth = 1.6;
            ctx.beginPath();
            ctx.moveTo(0, -size * 2.2);
            ctx.lineTo(0, size * 2.2);
            ctx.moveTo(-size * 2.2, 0);
            ctx.lineTo(size * 2.2, 0);
            ctx.stroke();

            // 4 Secondary Diagonal Spikes
            ctx.strokeStyle = 'rgba(254, 243, 199, 0.65)';
            ctx.lineWidth = 1.0;
            ctx.beginPath();
            const d = size * 1.4;
            ctx.moveTo(-d, -d);
            ctx.lineTo(d, d);
            ctx.moveTo(-d, d);
            ctx.lineTo(d, -d);
            ctx.stroke();

            ctx.restore();
        }

        // Save generated video blob to server & database
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
        // VIDEO PLAYER MODAL & URL RESOLVER
        // =========================================================================
        function resolveVideoUrl(url) {
            if (!url) return '';
            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('blob:')) {
                return url;
            }
            const clean = url.replace(/^(\.\.\/|\.\/|\/)+/, '');
            return '../' + clean;
        }

        function playProductVideo(url, sku, name) {
            const playUrl = resolveVideoUrl(url);
            document.getElementById('playerModalSku').textContent = sku;
            document.getElementById('playerModalTitle').textContent = name || sku;
            
            const dlBtn = document.getElementById('playerDownloadBtn');
            const dlExt = playUrl.endsWith('.mp4') ? 'mp4' : 'webm';
            dlBtn.setAttribute('href', playUrl);
            dlBtn.setAttribute('download', (sku || 'product') + '_showcase.' + dlExt);

            const video = document.getElementById('playerVideoElement');
            video.onerror = () => {
                console.error('Failed to load video from:', playUrl);
            };
            video.src = playUrl;
            video.load();
            video.play().catch(e => {
                console.warn('Autoplay prevented or pending gesture:', e);
            });

            const modal = document.getElementById('videoPlayerModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeVideoPlayer() {
            const video = document.getElementById('playerVideoElement');
            video.pause();
            video.removeAttribute('src');
            video.load();
            const modal = document.getElementById('videoPlayerModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
