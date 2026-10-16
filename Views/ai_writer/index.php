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

        /* Dedicated Studio Surface Pills (override any vercel.css main .text-white reset) */
        .surface-pill {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid var(--slate-200);
            background: #ffffff !important;
            color: #334155 !important;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            line-height: 1.25;
        }
        .surface-pill:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        .surface-pill.active {
            background: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
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
                                <button type="button" onclick="openSaturdayModal()" class="shadcn-btn shadcn-btn-sm text-slate-800 bg-slate-100 hover:bg-slate-200 border-slate-300 font-medium" title="Browse and load products uploaded on Saturday (Oct 10, 2026)">
                                    <i class="fa-solid fa-calendar-day text-slate-600"></i> Saturday's Uploads (Oct 10) <span class="bg-slate-900 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1 font-semibold">664</span>
                                </button>
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
                                            <select id="config_category_filter" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer max-w-[280px]">
                                                <option value="">All Categories</option>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $parent => $data): ?>
                                                        <optgroup label="<?php echo htmlspecialchars($parent); ?> (<?php echo $data['count']; ?>)">
                                                            <?php foreach ($data['children'] as $value => $childData): ?>
                                                                <option value="<?php echo htmlspecialchars($value); ?>">
                                                                    <?php echo $childData['name']; ?> (<?php echo $childData['count']; ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </optgroup>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
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
                                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md" title="Display Surface & Backdrop preset for Photo 2">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-gem text-slate-400"></i> Studio Surface:
                                            </span>
                                            <select id="config_studio_theme" onchange="syncStudioSurfaceFromSelect(this.value)" class="bg-transparent text-xs font-medium text-slate-800 outline-none cursor-pointer">
                                                <option value="dark_marble" selected>Dark Marble (Luxury Studio)</option>
                                                <option value="velvet_cushion">Velvet Cushion (Royal Warmth)</option>
                                                <option value="wooden_stand">Wooden Stand (Minimalist)</option>
                                                <option value="reflective_glass">Reflective Glass (High Mirror)</option>
                                                <option value="champagne_silk">Champagne Silk (Soft Drapery)</option>
                                                <option value="white_studio">White Studio (Pristine Clean)</option>
                                                <option value="royal_velvet">Wine Velvet Cushion (Signature)</option>
                                                <option value="velvet_rose_petals">Velvet + Fresh Rose Petals</option>
                                                <option value="dark_emerald_velvet">Emerald Green Velvet</option>
                                                <option value="royal_navy_velvet">Midnight Navy Velvet</option>
                                                <option value="black_velvet">Deep Black Velvet</option>
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

                                        <!-- Main Product Image Selector (Applies to all products) -->
                                        <div class="flex items-center gap-2 bg-amber-50/70 border border-amber-200/90 px-2.5 py-1 rounded-md" title="Select which photo will be set as the Main Product Image across all items">
                                            <span class="text-[11px] font-bold text-amber-950 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fa-solid fa-star text-amber-500 text-xs"></i> Set as Main Image:
                                            </span>
                                            <select id="config_main_image_choice" onchange="onGlobalMainImageChange(this.value)" class="bg-transparent text-xs font-semibold text-slate-900 outline-none cursor-pointer">
                                                <option value="without_model" selected>Photo Without Model (Still Life)</option>
                                                <option value="with_model">Photo With Model</option>
                                                <option value="keep_existing">Keep Existing Image</option>
                                            </select>
                                        </div>

                                        <!-- Parallel Concurrency Selector -->
                                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md" title="Number of products generated concurrently in parallel">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fa-solid fa-gauge-high text-slate-400"></i> Speed:
                                            </span>
                                            <select id="config_concurrency" onchange="syncConcurrency(this.value)" class="bg-transparent text-xs font-semibold text-slate-900 outline-none cursor-pointer">
                                                <option value="2">2 Parallel</option>
                                                <option value="3" selected>3 Parallel (Fast)</option>
                                                <option value="4">4 Parallel (Turbo)</option>
                                                <option value="1">1 (Sequential)</option>
                                            </select>
                                        </div>

                                        <!-- Auto-Save to DB Toggle -->
                                        <label class="flex items-center gap-1.5 cursor-pointer bg-emerald-50/80 border border-emerald-200 px-2.5 py-1 rounded-md text-xs font-semibold text-emerald-950" title="Automatically save generated title, descriptions, and photos directly to database upon generation">
                                            <input type="checkbox" id="config_auto_save" checked onchange="syncAutoSave(this.checked)" class="rounded border-emerald-400 text-emerald-700 focus:ring-0">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-bolt text-emerald-600 text-[11px]"></i> Auto-Save to DB
                                            </span>
                                        </label>
                                    </div>

                                    <!-- Search / Load Buttons -->
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="btn_lookup_skus" onclick="lookupProducts()" class="shadcn-btn shadcn-btn-primary">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <span>Search &amp; Load Products</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- ==================== AI STUDIO PHOTO (WITHOUT MODEL) SETTINGS PANEL ==================== -->
                                <div id="studio_settings_panel" class="pt-3.5 border-t border-slate-100 bg-slate-50/60 -mx-4 -mb-4 p-4 rounded-b-lg">
                                    <div class="flex items-center justify-between mb-2.5 flex-wrap gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded bg-slate-900 text-white flex items-center justify-center text-[11px]">
                                                <i class="fa-solid fa-camera-retro"></i>
                                            </span>
                                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">AI Image Studio &mdash; Photo Without Model Settings</span>
                                            <span class="shadcn-badge font-semibold text-[10px] bg-white border border-slate-200 text-slate-700">Product Edit Standard</span>
                                        </div>
                                        <span class="text-[11px] text-slate-500">
                                            Controls surface backdrop &amp; shot angle for Photo 2 &amp; Batch Still Life
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                        <!-- DISPLAY SURFACE / BACKDROP (8 cols) -->
                                        <div class="lg:col-span-7 xl:col-span-8 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                                    <i class="fa-solid fa-gem text-slate-400"></i> Display Surface / Backdrop
                                                </label>
                                                <span class="text-[10px] text-slate-400">Click a preset or customize prompt</span>
                                            </div>
                                            <!-- Preset buttons matching product edit page -->
                                            <div class="flex flex-wrap gap-1.5" id="studio_surface_pills"></div>
                                            <!-- Editable Custom Surface Input -->
                                            <div class="relative">
                                                <input type="text" id="config_studio_bg_custom" class="field-input text-xs font-medium text-slate-800" value="luxury black marble surface with soft warm directional studio spotlighting" placeholder="Describe the background, display surface, or props..." oninput="onCustomBgInputChanged(this.value)">
                                            </div>
                                        </div>

                                        <!-- SHOT TYPE (4 cols) -->
                                        <div class="lg:col-span-5 xl:col-span-4 space-y-2">
                                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fa-solid fa-crosshairs text-slate-400"></i> Shot Type
                                            </label>
                                            <div class="space-y-1.5" id="studio_shot_type_radios"></div>
                                        </div>
                                    </div>

                                    <!-- Live Prompt Preview -->
                                    <div class="mt-3 pt-2.5 border-t border-slate-200/70 flex flex-col gap-1">
                                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                                            <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                                <i class="fa-solid fa-terminal text-slate-400 text-[10px]"></i> Live Studio Prompt Preview:
                                            </span>
                                            <span id="prompt_preview_chars" class="font-mono text-[10px] text-slate-400">0 chars</span>
                                        </div>
                                        <textarea id="studio_live_prompt_preview" rows="2" class="field-textarea font-mono text-[11px] text-slate-600 bg-white" readonly></textarea>
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
                            <div class="flex items-center gap-2 flex-wrap">
                                <!-- Synchronized Main Image Switcher on Queue Bar -->
                                <div class="flex items-center gap-1.5 bg-amber-50/80 border border-amber-200 px-2 py-1 rounded text-xs">
                                    <span class="text-[10.5px] font-bold text-amber-950 uppercase tracking-wider flex items-center gap-1">
                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i> Main:
                                    </span>
                                    <select id="queue_main_image_choice" onchange="onGlobalMainImageChange(this.value)" class="bg-transparent text-xs font-semibold text-slate-900 outline-none cursor-pointer">
                                        <option value="without_model" selected>Photo Without Model</option>
                                        <option value="with_model">Photo With Model</option>
                                        <option value="keep_existing">Keep Existing</option>
                                    </select>
                                </div>

                                <!-- Parallel Concurrency Quick Switcher -->
                                <div class="flex items-center gap-1.5 bg-slate-100 border border-slate-200 px-2 py-1 rounded text-xs">
                                    <span class="text-[10.5px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                                        <i class="fa-solid fa-gauge-high text-slate-500 text-[10px]"></i> Speed:
                                    </span>
                                    <select id="queue_concurrency" onchange="syncConcurrency(this.value)" class="bg-transparent text-xs font-semibold text-slate-900 outline-none cursor-pointer">
                                        <option value="2">2 Parallel</option>
                                        <option value="3" selected>3 Parallel</option>
                                        <option value="4">4 Parallel</option>
                                        <option value="1">1 (Sequential)</option>
                                    </select>
                                </div>

                                <!-- Auto-Save Toggle on Queue Bar -->
                                <label class="flex items-center gap-1.5 cursor-pointer bg-emerald-50 border border-emerald-200 px-2 py-1 rounded text-xs font-semibold text-emerald-950" title="Auto-save to database upon generation">
                                    <input type="checkbox" id="queue_auto_save" checked onchange="syncAutoSave(this.checked)" class="rounded border-emerald-400 text-emerald-700 focus:ring-0">
                                    <span><i class="fa-solid fa-bolt text-emerald-600 text-[10px]"></i> Auto-Save</span>
                                </label>

                                <!-- Primary Action: Auto Generate & Save to DB (Batch) -->
                                <button type="button" id="btn_generate_auto_all" onclick="runBatchQueue(true)" class="shadcn-btn shadcn-btn-primary shadow-2xs" title="Generate Copy & Photos in parallel and immediately save directly to Database">
                                    <i class="fa-solid fa-bolt text-amber-300"></i>
                                    <span>Auto Generate &amp; Save (<span id="queue_btn_count">0</span>)</span>
                                </button>

                                <!-- Secondary Action: Generate Only (Batch) -->
                                <button type="button" id="btn_generate_all" onclick="runBatchQueue(false)" class="shadcn-btn" title="Generate Content for review without saving immediately">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                                    <span>Generate Only</span>
                                </button>

                                <!-- Stop Button (hidden until running) -->
                                <button type="button" id="btn_stop_batch" onclick="stopBatchQueue()" class="hidden shadcn-btn text-rose-700 border-rose-300 hover:bg-rose-50">
                                    <i class="fa-solid fa-circle-stop"></i>
                                    <span>Stop</span>
                                </button>

                                <!-- Manual Save All Button (fallback) -->
                                <button type="button" id="btn_save_all_global" onclick="saveAllGlobal()" class="shadcn-btn" title="Save all generated products to Database">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Save All</span>
                                </button>
                            </div>
                        </div>
                        <!-- Progress bar & Live Status -->
                        <div id="batch_progress_bar_container" class="hidden w-full border-t border-slate-100 bg-slate-50 px-4 py-2.5">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span id="batch_progress_label" class="font-medium text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-spinner fa-spin text-slate-500"></i>
                                    <span>Processing batch queue in parallel...</span>
                                </span>
                                <span id="batch_progress_pct" class="font-mono text-slate-600 font-semibold">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                <div id="batch_progress_bar" class="bg-slate-900 h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                            </div>
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

    <!-- Studio Product Specific Customizer Modal -->
    <div id="studio-custom-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100000] flex items-center justify-center p-4" onclick="closeStudioCustomModal()">
        <div class="bg-white border border-slate-200 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">Customize Studio Photo (Without Model)</h2>
                            <span id="modal_studio_sku_badge" class="text-[11px] font-mono font-bold text-slate-700 bg-slate-200/80 px-2 py-0.5 rounded">SKU</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5" id="modal_studio_product_title">Configure surface backdrop, shot angle, and custom prompt for this specific item.</p>
                    </div>
                </div>
                <button type="button" onclick="closeStudioCustomModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-sm transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto space-y-4">
                <!-- Surface Presets -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Display Surface / Backdrop
                    </label>
                    <div class="flex flex-wrap gap-1.5" id="modal_surface_pills"></div>
                    <input type="text" id="modal_custom_bg" class="field-input text-xs font-medium mt-2" placeholder="Describe the background, display surface, or props..." oninput="updateModalPromptPreview()">
                </div>

                <!-- Shot Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Shot Type
                    </label>
                    <div class="grid grid-cols-2 gap-2" id="modal_shot_type_radios"></div>
                </div>

                <!-- Editable Prompt Textarea -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Final Studio Prompt (Edit If Needed)
                        </label>
                        <button type="button" onclick="resetModalPromptToDefault()" class="text-[11px] text-slate-500 hover:text-slate-800 underline">
                            Reset to default
                        </button>
                    </div>
                    <textarea id="modal_studio_prompt" rows="4" class="field-textarea text-xs font-mono" placeholder="Prompt used for generating the still life photo..."></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <button type="button" onclick="closeStudioCustomModal()" class="shadcn-btn shadcn-btn-sm">
                    Cancel
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="saveStudioCustomModal(false)" class="shadcn-btn shadcn-btn-sm text-slate-700">
                        <i class="fa-solid fa-check"></i> Apply Settings
                    </button>
                    <button type="button" onclick="saveStudioCustomModal(true)" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Studio Photo Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Saturday Uploads Browser Modal -->
    <div id="saturday-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100000] flex items-center justify-center p-4" onclick="closeSaturdayModal()">
        <div class="bg-white border border-slate-200 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">Saturday's Uploaded Products</h2>
                            <span class="text-[11px] font-semibold text-slate-600 bg-slate-200/80 px-2 py-0.5 rounded-full">October 10, 2026</span>
                            <span id="sat-modal-total-badge" class="text-[11px] font-semibold text-white bg-slate-900 px-2 py-0.5 rounded-full">664 Items</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Filter by category, search SKUs, and load items directly into the AI Content Studio queue.</p>
                    </div>
                </div>
                <button type="button" onclick="closeSaturdayModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-sm transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Category Filter Tabs & Search Bar -->
            <div class="p-4 border-b border-slate-200 bg-white flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                <!-- Group Tabs -->
                <div class="flex items-center gap-1.5 flex-wrap" id="sat-group-tabs">
                    <button type="button" onclick="filterSatGroup('all', this)" class="sat-tab active px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-900 text-white">All (<span id="sat-count-all">664</span>)</button>
                    <button type="button" onclick="filterSatGroup('tikka', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">Maang Tikka (<span id="sat-count-tikka">407</span>)</button>
                    <button type="button" onclick="filterSatGroup('damini', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">Damini (<span id="sat-count-damini">136</span>)</button>
                    <button type="button" onclick="filterSatGroup('mala', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">Mala (<span id="sat-count-mala">49</span>)</button>
                    <button type="button" onclick="filterSatGroup('kamar_patta', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">Kamar Patta (<span id="sat-count-kamar_patta">47</span>)</button>
                    <button type="button" onclick="filterSatGroup('tikka_damini', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">TD (<span id="sat-count-tikka_damini">15</span>)</button>
                    <button type="button" onclick="filterSatGroup('apparel', this)" class="sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200">Garments (<span id="sat-count-apparel">7</span>)</button>
                </div>

                <!-- Instant Search -->
                <div class="relative min-w-[200px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="sat-search-input" oninput="renderSatTable()" placeholder="Search SKU or name..." class="w-full text-xs pl-8 pr-3 py-1.5 border border-slate-200 rounded-md focus:outline-none focus:border-slate-400">
                </div>
            </div>

            <!-- Products Table Container -->
            <div class="flex-1 overflow-y-auto p-4 bg-slate-50/30" id="sat-products-table-container" style="min-height: 360px; max-height: 480px;">
                <div class="flex items-center justify-center p-8 text-slate-400 text-xs">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading Saturday's uploaded products...
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="px-5 py-3 border-t border-slate-200 bg-white flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
                        <input type="checkbox" id="sat-select-all" onchange="toggleSelectAllSat(this.checked)" class="rounded text-slate-900">
                        Select All Filtered (<span id="sat-filtered-count">0</span>)
                    </label>
                    <span>|</span>
                    <span id="sat-selected-badge" class="text-slate-700 font-semibold">0 Selected</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="copySatSkus()" class="shadcn-btn shadcn-btn-sm text-slate-700">
                        <i class="fa-solid fa-copy"></i> Copy SKUs
                    </button>
                    <a href="index.php?controller=product&filter=saturday" target="_blank" class="shadcn-btn shadcn-btn-sm text-slate-700">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Open in Catalog
                    </a>
                    <button type="button" onclick="loadSatSelectedIntoQueue()" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Load Selected into AI Queue
                    </button>
                    <button type="button" onclick="loadSatAllFilteredIntoQueue()" class="shadcn-btn shadcn-btn-sm bg-slate-800 text-white hover:bg-slate-900">
                        <i class="fa-solid fa-bolt"></i> Load All Filtered (<span id="sat-btn-filtered-count">0</span>)
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
        const categoryOptions = <?php echo json_encode(array_map(function($c) { return ['name' => $c['name'], 'type' => $c['type']]; }, $categoryDefinitions ?? [])); ?>;

        let loadedProducts = [];

        // ==================== STUDIO PHOTO (WITHOUT MODEL) CONFIGURATION ====================
        const STUDIO_SURFACES = [
            { key: 'dark_marble', label: 'Dark Marble', prompt: 'luxury black marble surface with soft warm directional studio spotlighting' },
            { key: 'velvet_cushion', label: 'Velvet Cushion', prompt: 'royal velvet jewelry display cushion with subtle warm accent lighting' },
            { key: 'wooden_stand', label: 'Wooden Stand', prompt: 'minimalist rustic wooden jewelry stand with soft natural shadows' },
            { key: 'reflective_glass', label: 'Reflective Glass', prompt: 'reflective dark mirror glass surface with sharp luxury reflections' },
            { key: 'champagne_silk', label: 'Champagne Silk', prompt: 'draped champagne silk fabric backdrop with soft diffuse studio lighting' },
            { key: 'white_studio', label: 'White Studio', prompt: 'pristine white minimalist studio display backdrop' },
            { key: 'royal_velvet', label: 'Wine Velvet', prompt: 'plush angled royal wine-burgundy / deep maroon micro-velvet jewelry cushion nestled amidst banarasi silk drapery' },
            { key: 'velvet_rose_petals', label: 'Rose Petals Velvet', prompt: 'luxurious deep maroon velvet jewelry pillow with vibrant fresh crimson rose petals delicately scattered' },
            { key: 'dark_emerald_velvet', label: 'Emerald Velvet', prompt: 'plush deep emerald green royal micro-velvet jewelry cushion with warm golden zari accents' }
        ];

        const STUDIO_SHOT_TYPES = [
            { key: 'macro', label: 'Macro Close-Up', prompt: 'ultra-sharp macro close-up product photography highlighting fine details, gemstone sparkle, and metal texture' },
            { key: 'flat_lay', label: 'Flat Lay (Top-Down)', prompt: 'professional flat lay top-down product photography layout' },
            { key: 'angle_3_4', label: '3/4 Display Angle', prompt: 'elegant 3/4 perspective product display shot showing side and front angles' },
            { key: 'floating', label: 'Floating Display', prompt: 'dramatic floating product display with soft realistic shadow underneath' }
        ];

        let selectedStudioSurfaceKey = 'dark_marble';
        let selectedStudioShotTypeKey = 'macro';

        let modalActiveIndex = null;
        let modalSelectedSurfaceKey = 'dark_marble';
        let modalSelectedShotTypeKey = 'macro';

        function getSelectedStudioSurface() {
            return selectedStudioSurfaceKey;
        }

        function getSelectedStudioCustomBg() {
            const input = document.getElementById('config_studio_bg_custom');
            return input ? input.value.trim() : 'luxury black marble surface with soft warm directional studio spotlighting';
        }

        function getSelectedStudioShotType() {
            return selectedStudioShotTypeKey;
        }

        function getActiveStudioSurfaceLabel() {
            const found = STUDIO_SURFACES.find(s => s.key === selectedStudioSurfaceKey);
            return found ? found.label : 'Dark Marble';
        }

        function renderStudioSurfacePills() {
            const container = document.getElementById('studio_surface_pills');
            if (!container) return;
            let html = '';
            STUDIO_SURFACES.forEach(s => {
                const isActive = s.key === selectedStudioSurfaceKey;
                html += `
                    <button type="button" onclick="selectStudioSurfacePreset('${s.key}')" class="surface-pill ${isActive ? 'active' : ''}">
                        ${escapeHtml(s.label)}
                    </button>
                `;
            });
            container.innerHTML = html;
        }

        function selectStudioSurfacePreset(key) {
            selectedStudioSurfaceKey = key;
            renderStudioSurfacePills();

            const preset = STUDIO_SURFACES.find(s => s.key === key);
            if (preset) {
                const input = document.getElementById('config_studio_bg_custom');
                if (input) {
                    input.value = preset.prompt;
                    input.style.transition = 'all 0.3s ease';
                    input.style.boxShadow = '0 0 0 2px rgba(15, 23, 42, 0.25)';
                    setTimeout(() => { if (input) input.style.boxShadow = ''; }, 600);
                }
                const sel = document.getElementById('config_studio_theme');
                if (sel) sel.value = key;
            }
            updateStudioLivePromptPreview();
            updateLoadedProductSurfaceLabels();
        }

        function syncStudioSurfaceFromSelect(val) {
            const match = STUDIO_SURFACES.find(s => s.key === val);
            if (match) {
                selectStudioSurfacePreset(val);
            } else {
                selectedStudioSurfaceKey = val;
                renderStudioSurfacePills();
                updateStudioLivePromptPreview();
            }
        }

        function onCustomBgInputChanged(val) {
            updateStudioLivePromptPreview();
        }

        function renderStudioShotTypes() {
            const container = document.getElementById('studio_shot_type_radios');
            if (!container) return;
            let html = '';
            STUDIO_SHOT_TYPES.forEach(st => {
                const isChecked = st.key === selectedStudioShotTypeKey;
                html += `
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer p-1 rounded hover:bg-white transition-colors">
                        <input type="radio" name="studio_shot_type" value="${st.key}" ${isChecked ? 'checked' : ''} onchange="selectStudioShotType('${st.key}')" class="rounded-full text-slate-900 focus:ring-0">
                        <span class="${isChecked ? 'font-semibold text-slate-900' : 'text-slate-600'}">${escapeHtml(st.label)}</span>
                    </label>
                `;
            });
            container.innerHTML = html;
        }

        function selectStudioShotType(key) {
            selectedStudioShotTypeKey = key;
            renderStudioShotTypes();
            updateStudioLivePromptPreview();
        }

        function updateStudioLivePromptPreview() {
            const previewEl = document.getElementById('studio_live_prompt_preview');
            if (!previewEl) return;
            const customBg = getSelectedStudioCustomBg() || 'luxury black marble surface with soft warm directional studio spotlighting';
            const shotObj = STUDIO_SHOT_TYPES.find(st => st.key === selectedStudioShotTypeKey) || STUDIO_SHOT_TYPES[0];
            const prompt = `Ultra-high resolution professional studio product photography of this exact item placed on ${customBg}. Shot type: ${shotObj.prompt}. No human model, no hands, product-only studio display photoshoot with soft diffuse studio lighting, 8k resolution, razor-sharp focus on details, gemstone sparkle and fine metal craftsmanship. Do not change product design or features. Aspect ratio: 2:3 vertical format.`;
            previewEl.value = prompt;
            const charCounter = document.getElementById('prompt_preview_chars');
            if (charCounter) charCounter.innerText = `${prompt.length} chars`;
        }

        function updateLoadedProductSurfaceLabels() {
            const activeLabel = getActiveStudioSurfaceLabel();
            loadedProducts.forEach(item => {
                if (!item.custom_studio_surface_label) {
                    const hint = document.getElementById(`surface_hint_${item.id}`);
                    if (hint) hint.innerText = activeLabel;
                    const title = document.getElementById(`card_studio_title_${item.id}`);
                    if (title) title.innerHTML = `<i class="fa-solid fa-camera text-slate-400 text-[10px]"></i> ${escapeHtml(activeLabel)}`;
                }
            });
        }

        // ==================== INDIVIDUAL PRODUCT STUDIO CUSTOMIZER MODAL ====================
        function openStudioCustomModal(index) {
            const item = loadedProducts[index];
            if (!item) return;
            modalActiveIndex = index;
            modalSelectedSurfaceKey = item.custom_studio_surface || selectedStudioSurfaceKey;
            modalSelectedShotTypeKey = item.custom_studio_shot_type || selectedStudioShotTypeKey;

            const skuBadge = document.getElementById('modal_studio_sku_badge');
            if (skuBadge) skuBadge.innerText = item.code || 'SKU';
            const prodTitle = document.getElementById('modal_studio_product_title');
            if (prodTitle) prodTitle.innerText = `${item.name || item.code} (${categoryOptions[item.category_key]?.name || item.category_key})`;

            const customBgInput = document.getElementById('modal_custom_bg');
            if (customBgInput) {
                const defPreset = STUDIO_SURFACES.find(s => s.key === modalSelectedSurfaceKey);
                customBgInput.value = item.custom_studio_bg || (defPreset ? defPreset.prompt : getSelectedStudioCustomBg());
            }

            renderModalSurfacePills();
            renderModalShotTypes();

            const promptTextarea = document.getElementById('modal_studio_prompt');
            if (promptTextarea) {
                promptTextarea.value = item.custom_studio_prompt || buildModalDefaultPrompt(item);
            }

            const modal = document.getElementById('studio-custom-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeStudioCustomModal() {
            const modal = document.getElementById('studio-custom-modal');
            if (modal) modal.classList.add('hidden');
            modalActiveIndex = null;
        }

        function renderModalSurfacePills() {
            const container = document.getElementById('modal_surface_pills');
            if (!container) return;
            let html = '';
            STUDIO_SURFACES.forEach(s => {
                const isActive = s.key === modalSelectedSurfaceKey;
                html += `
                    <button type="button" onclick="selectModalSurfacePreset('${s.key}')" class="surface-pill ${isActive ? 'active' : ''}">
                        ${escapeHtml(s.label)}
                    </button>
                `;
            });
            container.innerHTML = html;
        }

        function selectModalSurfacePreset(key) {
            modalSelectedSurfaceKey = key;
            renderModalSurfacePills();
            const preset = STUDIO_SURFACES.find(s => s.key === key);
            if (preset) {
                const input = document.getElementById('modal_custom_bg');
                if (input) input.value = preset.prompt;
            }
            updateModalPromptPreview();
        }

        function renderModalShotTypes() {
            const container = document.getElementById('modal_shot_type_radios');
            if (!container) return;
            let html = '';
            STUDIO_SHOT_TYPES.forEach(st => {
                const isChecked = st.key === modalSelectedShotTypeKey;
                html += `
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer p-1.5 rounded border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
                        <input type="radio" name="modal_shot_type" value="${st.key}" ${isChecked ? 'checked' : ''} onchange="selectModalShotType('${st.key}')" class="rounded-full text-slate-900 focus:ring-0">
                        <span class="${isChecked ? 'font-semibold text-slate-900' : 'text-slate-600'}">${escapeHtml(st.label)}</span>
                    </label>
                `;
            });
            container.innerHTML = html;
        }

        function selectModalShotType(key) {
            modalSelectedShotTypeKey = key;
            renderModalShotTypes();
            updateModalPromptPreview();
        }

        function buildModalDefaultPrompt(item) {
            const customBg = document.getElementById('modal_custom_bg')?.value.trim() || 'luxury black marble surface with soft warm directional studio spotlighting';
            const shotObj = STUDIO_SHOT_TYPES.find(st => st.key === modalSelectedShotTypeKey) || STUDIO_SHOT_TYPES[0];
            const catName = categoryOptions[item.category_key]?.name || 'jewellery';
            return `Ultra-high resolution professional studio product photography of this exact ${catName} placed on ${customBg}. Shot type: ${shotObj.prompt}. No human model, no hands, product-only studio display photoshoot with soft diffuse studio lighting, 8k resolution, razor-sharp focus on details, gemstone sparkle and fine metal craftsmanship. Do not change the ${catName} design or features. Aspect ratio: 2:3 vertical format.`;
        }

        function updateModalPromptPreview() {
            if (modalActiveIndex === null) return;
            const item = loadedProducts[modalActiveIndex];
            if (!item) return;
            const promptTextarea = document.getElementById('modal_studio_prompt');
            if (promptTextarea) {
                promptTextarea.value = buildModalDefaultPrompt(item);
            }
        }

        function resetModalPromptToDefault() {
            if (modalActiveIndex === null) return;
            const item = loadedProducts[modalActiveIndex];
            if (!item) return;
            const promptTextarea = document.getElementById('modal_studio_prompt');
            if (promptTextarea) {
                promptTextarea.value = buildModalDefaultPrompt(item);
            }
        }

        function saveStudioCustomModal(generateNow = false) {
            if (modalActiveIndex === null) return;
            const item = loadedProducts[modalActiveIndex];
            if (!item) return;

            const surfaceObj = STUDIO_SURFACES.find(s => s.key === modalSelectedSurfaceKey);
            item.custom_studio_surface = modalSelectedSurfaceKey;
            item.custom_studio_surface_label = surfaceObj ? surfaceObj.label : 'Custom Studio';
            item.custom_studio_bg = document.getElementById('modal_custom_bg')?.value.trim() || '';
            item.custom_studio_shot_type = modalSelectedShotTypeKey;
            item.custom_studio_prompt = document.getElementById('modal_studio_prompt')?.value.trim() || '';

            // Update card UI
            const hint = document.getElementById(`surface_hint_${item.id}`);
            if (hint) hint.innerText = item.custom_studio_surface_label;
            const title = document.getElementById(`card_studio_title_${item.id}`);
            if (title) title.innerHTML = `<i class="fa-solid fa-camera text-slate-400 text-[10px]"></i> ${escapeHtml(item.custom_studio_surface_label)}`;

            const savedIndex = modalActiveIndex;
            closeStudioCustomModal();
            showToast(`Studio options customized for ${item.code}!`);

            if (generateNow) {
                generatePhotoSingleMode(savedIndex, 'without_model');
            }
        }

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

                const globalMainChoice = document.getElementById('config_main_image_choice')?.value || 'without_model';
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
                    main_image_choice: globalMainChoice,
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
            const qBtnCount = document.getElementById('queue_btn_count');
            if (qBtnCount) qBtnCount.innerText = loadedProducts.length;

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
                            <button type="button" id="btn_run_single_${item.id}" onclick="generateSingleAllInOne(${index})" class="shadcn-btn shadcn-btn-sm shadcn-btn-primary" title="Generate copy & photos in parallel and auto-save directly to Database">
                                <i class="fa-solid fa-bolt text-amber-300"></i> Generate &amp; Save
                            </button>
                            <button type="button" id="btn_save_single_${item.id}" onclick="saveSingleProduct(${index})" class="shadcn-btn shadcn-btn-sm" title="Manual Save or Update">
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
                                    <div class="photo-card ${item.main_image_choice === 'with_model' ? 'ring-2 ring-amber-400' : ''}" id="photo_card_model_${item.id}">
                                        <div class="photo-card-media relative" id="media_model_${item.id}">
                                            ${item.main_image_choice === 'with_model' ? `
                                                <span class="absolute top-1.5 left-1.5 bg-amber-500 text-slate-950 text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow z-10 flex items-center gap-1 tracking-wider">
                                                    <i class="fa-solid fa-star text-[8px]"></i> MAIN
                                                </span>
                                            ` : ''}
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
                                            <div class="pt-1.5 border-t border-slate-100">
                                                ${item.main_image_choice === 'with_model' ? `
                                                    <button type="button" class="w-full py-1 px-1.5 text-[10px] font-bold rounded bg-amber-500 text-slate-950 flex items-center justify-center gap-1 shadow-2xs cursor-default">
                                                        <i class="fa-solid fa-star text-[9px]"></i> Main Image
                                                    </button>
                                                ` : `
                                                    <button type="button" onclick="setProductMainImage(${index}, 'with_model')" class="w-full py-1 px-1.5 text-[10px] font-medium rounded bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center gap-1 transition-colors" title="Set Photo With Model as Main Product Image">
                                                        <i class="fa-regular fa-star text-[9px]"></i> Set as Main
                                                    </button>
                                                `}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Photo 2: Without Model (Studio Still Life) -->
                                    <div class="photo-card ${item.main_image_choice === 'without_model' ? 'ring-2 ring-amber-400' : ''}" id="photo_card_studio_${item.id}">
                                        <div class="photo-card-media relative" id="media_studio_${item.id}">
                                            ${item.main_image_choice === 'without_model' ? `
                                                <span class="absolute top-1.5 left-1.5 bg-amber-500 text-slate-950 text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow z-10 flex items-center gap-1 tracking-wider">
                                                    <i class="fa-solid fa-star text-[8px]"></i> MAIN
                                                </span>
                                            ` : ''}
                                            ${item.photo_without_model ? `
                                                <img src="${item.photo_without_model.url}" alt="Without Model" title="Click to view full preview (Hover to zoom)" onclick="openLightbox('${item.photo_without_model.url}', 'Still Life - ${escapeHtml(item.code)}')">
                                            ` : `
                                                <div class="photo-card-empty">
                                                    <i class="fa-solid fa-camera text-xl text-slate-300"></i>
                                                    <span class="font-semibold text-[11px] text-slate-600">Without Model</span>
                                                    <span class="text-[9.5px] text-slate-400 line-clamp-2" id="surface_hint_${item.id}">
                                                        ${escapeHtml(item.custom_studio_surface_label || getActiveStudioSurfaceLabel())}
                                                    </span>
                                                </div>
                                            `}
                                        </div>
                                        <div class="photo-card-body">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] font-semibold text-slate-800 truncate" id="card_studio_title_${item.id}">
                                                    <i class="fa-solid fa-camera text-slate-400 text-[10px]"></i> ${escapeHtml(item.custom_studio_surface_label || getActiveStudioSurfaceLabel())}
                                                </span>
                                                <span id="badge_studio_${item.id}" class="text-[10px] text-slate-500 font-medium truncate">
                                                    ${item.photo_without_model ? (item.saved_without_model ? 'Saved' : 'Ready') : 'None'}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 pt-0.5">
                                                <button type="button" onclick="generatePhotoSingleMode(${index}, 'without_model')" class="shadcn-btn shadcn-btn-sm flex-1 text-[11px] h-6 px-1 text-slate-700" title="Generate Studio Photo">
                                                    <i class="fa-solid fa-wand-magic text-[10px]"></i> Gen
                                                </button>
                                                <button type="button" onclick="openStudioCustomModal(${index})" class="shadcn-btn shadcn-btn-sm text-[11px] h-6 px-1.5 text-slate-700 hover:bg-slate-100" title="Customize Surface Backdrop & Shot Type for this product">
                                                    <i class="fa-solid fa-sliders text-[10px]"></i>
                                                </button>
                                                <button type="button" id="btn_save_studio_${item.id}" ${item.photo_without_model ? '' : 'disabled'} onclick="saveImageIndividual(${index}, 'without_model')" class="shadcn-btn shadcn-btn-sm text-[11px] h-6 px-1.5 text-slate-700" title="Save to Gallery">
                                                    <i class="fa-solid fa-image text-[10px]"></i>
                                                </button>
                                            </div>
                                            <div class="pt-1.5 border-t border-slate-100">
                                                ${item.main_image_choice === 'without_model' ? `
                                                    <button type="button" class="w-full py-1 px-1.5 text-[10px] font-bold rounded bg-amber-500 text-slate-950 flex items-center justify-center gap-1 shadow-2xs cursor-default">
                                                        <i class="fa-solid fa-star text-[9px]"></i> Main Image
                                                    </button>
                                                ` : `
                                                    <button type="button" onclick="setProductMainImage(${index}, 'without_model')" class="w-full py-1 px-1.5 text-[10px] font-medium rounded bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center gap-1 transition-colors" title="Set Photo Without Model as Main Product Image">
                                                        <i class="fa-regular fa-star text-[9px]"></i> Set as Main
                                                    </button>
                                                `}
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

        // Synchronize Concurrency & Auto-Save
        function syncConcurrency(val) {
            const c1 = document.getElementById('config_concurrency');
            const c2 = document.getElementById('queue_concurrency');
            if (c1 && c1.value !== val) c1.value = val;
            if (c2 && c2.value !== val) c2.value = val;
        }

        function syncAutoSave(checked) {
            const a1 = document.getElementById('config_auto_save');
            const a2 = document.getElementById('queue_auto_save');
            if (a1 && a1.checked !== checked) a1.checked = checked;
            if (a2 && a2.checked !== checked) a2.checked = checked;
        }

        // ==================== GENERATE SINGLE ITEM (PARALLEL ALL-IN-ONE) ====================
        async function generateSingleAllInOne(index, autoSave = null) {
            const item = loadedProducts[index];
            if (!item) return false;

            const card = document.getElementById(`card_prod_${item.id}`);
            const pill = document.getElementById(`status_pill_${item.id}`);
            const btn = document.getElementById(`btn_run_single_${item.id}`);

            if (card) card.classList.add('is-processing');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            }
            if (pill) pill.innerHTML = '<span class="status-dot active"></span> Generating in Parallel...';

            const provider = document.getElementById('config_provider').value;
            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = document.getElementById('config_studio_theme').value;
            const doCopy = document.getElementById('gen_opt_copy').checked;
            const doModel = document.getElementById('gen_opt_model').checked;
            const doStudio = document.getElementById('gen_opt_studio').checked;
            const currentCatKey = item.category_key;

            try {
                const parallelTasks = [];

                // 1. Parallel Task: AI E-commerce Copywriting
                if (doCopy) {
                    parallelTasks.push((async () => {
                        try {
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

                                const inName = document.getElementById(`input_name_${item.id}`);
                                const inShort = document.getElementById(`input_short_${item.id}`);
                                const inDesc = document.getElementById(`input_desc_${item.id}`);
                                if (inName) inName.value = cData.name;
                                if (inShort) inShort.value = cData.short_description;
                                if (inDesc) inDesc.value = cData.description;

                                updateCharCount(item.id, 'name', 95);
                                updateCharCount(item.id, 'short', 245);
                                return { success: true, task: 'copy' };
                            } else {
                                return { success: false, task: 'copy', error: cData.error };
                            }
                        } catch (err) {
                            return { success: false, task: 'copy', error: err.message };
                        }
                    })());
                }

                // 2. Parallel Task: Photo With Model
                if (doModel) {
                    parallelTasks.push((async () => {
                        try {
                            const pRes = await fetch('index.php?controller=allinonewriter&action=generateImage', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    id: item.id,
                                    type: item.type,
                                    mode: 'with_model',
                                    face_reference: faceModel,
                                    studio_theme: studioTheme,
                                    category_key: currentCatKey
                                })
                            });
                            const pData = await pRes.json();
                            if (pData.success && pData.images && pData.images.with_model) {
                                item.photo_with_model = pData.images.with_model;
                                updatePhotoCard(item.id, 'with_model', pData.images.with_model.url, index);
                                return { success: true, task: 'with_model' };
                            } else {
                                return { success: false, task: 'with_model', error: pData.error || 'Failed' };
                            }
                        } catch (err) {
                            return { success: false, task: 'with_model', error: err.message };
                        }
                    })());
                }

                // 3. Parallel Task: Photo Without Model (Still Life)
                if (doStudio) {
                    parallelTasks.push((async () => {
                        try {
                            const pRes = await fetch('index.php?controller=allinonewriter&action=generateImage', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    id: item.id,
                                    type: item.type,
                                    mode: 'without_model',
                                    face_reference: faceModel,
                                    studio_theme: item.custom_studio_surface || getSelectedStudioSurface(),
                                    studio_surface: item.custom_studio_surface || getSelectedStudioSurface(),
                                    studio_bg_custom: item.custom_studio_bg || getSelectedStudioCustomBg(),
                                    studio_shot_type: item.custom_studio_shot_type || getSelectedStudioShotType(),
                                    custom_studio_prompt: item.custom_studio_prompt || '',
                                    category_key: currentCatKey
                                })
                            });
                            const pData = await pRes.json();
                            if (pData.success && pData.images && pData.images.without_model) {
                                item.photo_without_model = pData.images.without_model;
                                updatePhotoCard(item.id, 'without_model', pData.images.without_model.url, index);
                                return { success: true, task: 'without_model' };
                            } else {
                                return { success: false, task: 'without_model', error: pData.error || 'Failed' };
                            }
                        } catch (err) {
                            return { success: false, task: 'without_model', error: err.message };
                        }
                    })());
                }

                // Await all parallel tasks simultaneously
                await Promise.all(parallelTasks);

                // Auto-save check
                const shouldAutoSave = (autoSave !== null) 
                    ? autoSave 
                    : (document.getElementById('config_auto_save')?.checked || document.getElementById('queue_auto_save')?.checked);

                if (shouldAutoSave) {
                    if (pill) pill.innerHTML = '<span class="status-dot active"></span> Auto-Saving to DB...';
                    const saveOk = await saveSingleProduct(index, true);
                    if (saveOk) {
                        if (pill) pill.innerHTML = '<span class="status-dot active"></span> Generated &amp; Saved';
                        showToast(`Generated &amp; Saved ${item.code} to catalog!`);
                    } else {
                        if (pill) pill.innerHTML = '<span class="status-dot"></span> Generated (Save Issue)';
                    }
                } else {
                    if (pill) pill.innerHTML = '<span class="status-dot active"></span> Generated (Ready to Save)';
                    showToast(`Generated All in One for ${item.code}!`);
                }

                return true;

            } catch (err) {
                console.error(err);
                if (pill) pill.innerHTML = '<span class="status-dot"></span> Generation Error';
                showToast(`Error processing ${item.code}`, 'error');
                return false;
            } finally {
                if (card) card.classList.remove('is-processing');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-bolt text-amber-300"></i> Generate &amp; Save';
                }
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

        // Generate only Photos (in parallel)
        async function generateOnlyPhotos(index) {
            const item = loadedProducts[index];
            if (!item) return;

            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = item.custom_studio_surface || getSelectedStudioSurface();
            const studioCustomBg = item.custom_studio_bg || getSelectedStudioCustomBg();
            const studioShotType = item.custom_studio_shot_type || getSelectedStudioShotType();
            const studioPrompt = item.custom_studio_prompt || '';
            const pill = document.getElementById(`status_pill_${item.id}`);
            if (pill) pill.innerHTML = '<span class="status-dot active"></span> Generating Photos in Parallel...';

            try {
                const reqModel = fetch('index.php?controller=allinonewriter&action=generateImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        mode: 'with_model',
                        face_reference: faceModel,
                        studio_theme: studioTheme,
                        category_key: item.category_key
                    })
                }).then(r => r.json());

                const reqStudio = fetch('index.php?controller=allinonewriter&action=generateImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        mode: 'without_model',
                        face_reference: faceModel,
                        studio_theme: studioTheme,
                        studio_surface: studioTheme,
                        studio_bg_custom: studioCustomBg,
                        studio_shot_type: studioShotType,
                        custom_studio_prompt: studioPrompt,
                        category_key: item.category_key
                    })
                }).then(r => r.json());

                const [resModel, resStudio] = await Promise.all([reqModel, reqStudio]);

                if (resModel && resModel.success && resModel.images && resModel.images.with_model) {
                    item.photo_with_model = resModel.images.with_model;
                    updatePhotoCard(item.id, 'with_model', resModel.images.with_model.url, index);
                }
                if (resStudio && resStudio.success && resStudio.images && resStudio.images.without_model) {
                    item.photo_without_model = resStudio.images.without_model;
                    updatePhotoCard(item.id, 'without_model', resStudio.images.without_model.url, index);
                }

                if (pill) pill.innerHTML = '<span class="status-dot active"></span> Photos Updated';
                showToast(`Photos generated in parallel for ${item.code}!`);
            } catch (err) {
                showToast('Error generating photos', 'error');
            }
        }

        // Generate Single Photo Mode (with_model or without_model)
        async function generatePhotoSingleMode(index, mode) {
            const item = loadedProducts[index];
            if (!item) return;

            const faceModel = document.getElementById('config_face_model').value;
            const studioTheme = item.custom_studio_surface || getSelectedStudioSurface();
            const studioCustomBg = item.custom_studio_bg || getSelectedStudioCustomBg();
            const studioShotType = item.custom_studio_shot_type || getSelectedStudioShotType();
            const studioPrompt = item.custom_studio_prompt || '';
            const cardId = mode === 'with_model' ? `media_model_${item.id}` : `media_studio_${item.id}`;
            const mediaBox = document.getElementById(cardId);
            const originalHtml = mediaBox.innerHTML;
            const surfaceTitle = item.custom_studio_surface_label || getActiveStudioSurfaceLabel();

            mediaBox.innerHTML = `
                <div class="photo-card-empty">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-400"></i>
                    <span class="font-semibold text-slate-600">Generating ${mode === 'with_model' ? 'Model' : surfaceTitle} Photo...</span>
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
                        studio_surface: studioTheme,
                        studio_bg_custom: studioCustomBg,
                        studio_shot_type: studioShotType,
                        custom_studio_prompt: studioPrompt,
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
                const item = (typeof index !== 'undefined' && loadedProducts[index]) ? loadedProducts[index] : null;
                const isMain = item && item.main_image_choice === mode;
                const mainBadgeHtml = isMain ? `
                    <span class="absolute top-1.5 left-1.5 bg-amber-500 text-slate-950 text-[9px] font-extrabold px-1.5 py-0.5 rounded shadow z-10 flex items-center gap-1 tracking-wider">
                        <i class="fa-solid fa-star text-[8px]"></i> MAIN
                    </span>
                ` : '';
                mediaBox.innerHTML = `${mainBadgeHtml}<img src="${url}" alt="${mode}" title="Click to view full preview (Hover to zoom)" onclick="openLightbox('${url}', '${mode}')">`;
            }
            if (badge) {
                badge.innerText = 'Generated (Ready to Save)';
                badge.style.color = '#0284c7';
            }
            if (saveBtn) {
                saveBtn.disabled = false;
            }
        }

        // ==================== MAIN IMAGE SELECTION ====================
        function onGlobalMainImageChange(choice) {
            const configSel = document.getElementById('config_main_image_choice');
            const queueSel = document.getElementById('queue_main_image_choice');
            if (configSel && configSel.value !== choice) configSel.value = choice;
            if (queueSel && queueSel.value !== choice) queueSel.value = choice;

            loadedProducts.forEach(p => {
                p.main_image_choice = choice;
            });

            renderProducts();

            const label = choice === 'with_model' 
                ? 'Photo With Model' 
                : (choice === 'without_model' ? 'Photo Without Model (Still Life)' : 'Keep Existing Image');
            showToast(`Main image set to "${label}" across all products!`);
        }

        async function setProductMainImage(index, mode) {
            const item = loadedProducts[index];
            if (!item) return;

            item.main_image_choice = mode;
            renderProducts();

            const photo = mode === 'with_model' ? item.photo_with_model : item.photo_without_model;
            if (photo) {
                try {
                    const payload = {
                        id: item.id,
                        type: item.type,
                        mode: mode
                    };
                    if (photo.file_path || photo.url) {
                        payload.image_path = photo.file_path || photo.url;
                    }
                    if (photo.base64) {
                        payload.base64 = photo.base64;
                    }

                    const res = await fetch('index.php?controller=allinonewriter&action=setMainImage', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(`Set as main image and saved to database for ${item.code}!`);
                    } else {
                        showToast(`Main image selected for ${item.code} (will update when saving)`);
                    }
                } catch (e) {
                    showToast(`Main image selected for ${item.code}`);
                }
            } else {
                const modeLabel = mode === 'with_model' ? 'Photo With Model' : 'Photo Without Model';
                showToast(`Selected ${modeLabel} as Main Image for ${item.code}. Will save to DB upon generation.`);
            }
        }

        // ==================== PARALLEL BATCH QUEUE ====================
        let isBatchRunning = false;
        let stopBatchRequested = false;

        async function runBatchQueue(autoSave = true) {
            if (isBatchRunning) return;
            if (loadedProducts.length === 0) {
                showToast('No products loaded in queue.', 'error');
                return;
            }

            isBatchRunning = true;
            stopBatchRequested = false;

            const btnAuto = document.getElementById('btn_generate_auto_all');
            const btnGen = document.getElementById('btn_generate_all');
            const btnStop = document.getElementById('btn_stop_batch');
            const progressContainer = document.getElementById('batch_progress_bar_container');
            const progressBar = document.getElementById('batch_progress_bar');
            const progressLabel = document.getElementById('batch_progress_label');
            const progressPct = document.getElementById('batch_progress_pct');

            if (btnAuto) btnAuto.disabled = true;
            if (btnGen) btnGen.disabled = true;
            if (btnStop) {
                btnStop.classList.remove('hidden');
                btnStop.innerHTML = '<i class="fa-solid fa-circle-stop"></i> <span>Stop</span>';
            }

            if (progressContainer) progressContainer.classList.remove('hidden');
            if (progressBar) progressBar.style.width = '0%';
            if (progressPct) progressPct.innerText = '0%';

            const concurrencyVal = parseInt(document.getElementById('config_concurrency')?.value || '3');
            const total = loadedProducts.length;
            let nextIndex = 0;
            let completedCount = 0;
            let successCount = 0;

            const activeWorkers = Math.min(concurrencyVal, total);
            if (progressLabel) {
                progressLabel.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-slate-500"></i> <span>Running ${activeWorkers} parallel workers (0/${total} completed)...</span>`;
            }

            async function worker(workerId) {
                while (nextIndex < total && !stopBatchRequested) {
                    const i = nextIndex++;
                    const item = loadedProducts[i];
                    if (progressLabel) {
                        progressLabel.innerHTML = `<i class="fa-solid fa-bolt text-amber-500"></i> <span>Worker ${workerId + 1} processing ${item.code} (${completedCount + 1}/${total})...</span>`;
                    }

                    try {
                        const ok = await generateSingleAllInOne(i, autoSave);
                        if (ok !== false) successCount++;
                    } catch (e) {
                        console.error('Batch item error:', e);
                    }

                    completedCount++;
                    const pct = Math.round((completedCount / total) * 100);
                    if (progressBar) progressBar.style.width = `${pct}%`;
                    if (progressPct) progressPct.innerText = `${pct}%`;

                    if (progressLabel) {
                        progressLabel.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-slate-500"></i> <span>${completedCount}/${total} products processed (${successCount} successful)...</span>`;
                    }
                }
            }

            const workerPromises = [];
            for (let w = 0; w < activeWorkers; w++) {
                workerPromises.push(worker(w));
            }

            await Promise.all(workerPromises);

            isBatchRunning = false;
            if (btnAuto) btnAuto.disabled = false;
            if (btnGen) btnGen.disabled = false;
            if (btnStop) btnStop.classList.add('hidden');

            if (stopBatchRequested) {
                showToast(`Batch stopped by user. (${completedCount} of ${total} processed)`);
                if (progressLabel) {
                    progressLabel.innerHTML = `<i class="fa-solid fa-circle-exclamation text-amber-500"></i> <span>Queue stopped: ${completedCount}/${total} processed.</span>`;
                }
            } else {
                const msg = autoSave 
                    ? `Batch complete! Generated & saved ${completedCount} products directly to database.`
                    : `Batch complete! Generated ${completedCount} products.`;
                showToast(msg);
                if (progressLabel) {
                    progressLabel.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500"></i> <span>All ${completedCount} products finished successfully!</span>`;
                }
            }
        }

        function stopBatchQueue() {
            if (!isBatchRunning) return;
            stopBatchRequested = true;
            const btnStop = document.getElementById('btn_stop_batch');
            if (btnStop) {
                btnStop.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Stopping...';
            }
            showToast('Stopping queue after current workers finish...');
        }

        // Alias for backwards compatibility
        async function generateAllBatch() {
            await runBatchQueue(true);
        }

        // ==================== SAVE ACTIONS ====================
        async function saveSingleProduct(index, isAuto = false) {
            const item = loadedProducts[index];
            if (!item) return false;

            const inName = document.getElementById(`input_name_${item.id}`);
            const inShort = document.getElementById(`input_short_${item.id}`);
            const inDesc = document.getElementById(`input_desc_${item.id}`);

            const name = inName ? inName.value.trim() : (item.gen_name || '');
            const shortDesc = inShort ? inShort.value.trim() : (item.gen_short || '');
            const desc = inDesc ? inDesc.value.trim() : (item.gen_desc || '');

            const withModelB64 = item.photo_with_model ? item.photo_with_model.base64 : '';
            const withoutModelB64 = item.photo_without_model ? item.photo_without_model.base64 : '';
            const mainChoice = item.main_image_choice || document.getElementById('config_main_image_choice')?.value || 'without_model';

            const btn = document.getElementById(`btn_save_single_${item.id}`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            }

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
                        without_model_base64: withoutModelB64,
                        main_image_choice: mainChoice
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
                        if (b1) b1.innerText = (mainChoice === 'with_model') ? 'Saved (Main Image)' : 'Saved to Gallery';
                    }
                    if (item.photo_without_model) {
                        const b2 = document.getElementById(`badge_studio_${item.id}`);
                        if (b2) b2.innerText = (mainChoice === 'without_model') ? 'Saved (Main Image)' : 'Saved to Gallery';
                    }

                    if (!isAuto) {
                        showToast(`Product ${item.code} saved to database successfully!`);
                    }
                    return true;
                } else {
                    if (!isAuto) showToast(data.error || 'Failed to save product', 'error');
                    return false;
                }
            } catch (err) {
                if (!isAuto) showToast('Error saving product', 'error');
                return false;
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save to DB';
                }
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

            const isMain = (item.main_image_choice === mode);

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=saveImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: item.id,
                        type: item.type,
                        base64: photo.base64,
                        mode: mode,
                        set_as_main: isMain
                    })
                });
                const data = await res.json();
                if (data.success) {
                    const badgeId = mode === 'with_model' ? `badge_model_${item.id}` : `badge_studio_${item.id}`;
                    document.getElementById(badgeId).innerText = isMain ? 'Saved (Main Catalog Image)' : 'Saved to Gallery';
                    showToast(isMain ? 'Image saved and set as Main Product Image!' : 'Image saved to product gallery!');
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

        // ==================== SATURDAY'S UPLOADS MODAL LOGIC ====================
        let satAllProducts = [];
        let satCurrentGroup = 'all';
        let satSelectedSkus = new Set();
        let satIsLoading = false;

        async function openSaturdayModal() {
            const modal = document.getElementById('saturday-modal');
            if (!modal) return;
            modal.classList.remove('hidden');

            if (satAllProducts.length === 0) {
                await fetchSaturdayProducts();
            } else {
                renderSatTable();
            }
        }

        function closeSaturdayModal() {
            const modal = document.getElementById('saturday-modal');
            if (modal) modal.classList.add('hidden');
        }

        async function fetchSaturdayProducts() {
            const container = document.getElementById('sat-products-table-container');
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center p-12 text-slate-400 text-xs">
                    <i class="fa-solid fa-spinner fa-spin text-lg mb-2 text-slate-600"></i>
                    <span>Loading 664 Saturday uploaded products...</span>
                </div>
            `;

            try {
                const res = await fetch('index.php?controller=allinonewriter&action=getSaturdayProducts&date=2026-10-10');
                const data = await res.json();
                if (!data.success) {
                    container.innerHTML = `<div class="p-6 text-center text-rose-500 text-xs">${escapeHtml(data.error || 'Failed to load Saturday products.')}</div>`;
                    return;
                }

                satAllProducts = data.products || [];

                // Update tab counters
                const gc = data.group_counts || {};
                document.getElementById('sat-count-all').innerText = gc.all || satAllProducts.length;
                if (document.getElementById('sat-count-tikka')) document.getElementById('sat-count-tikka').innerText = gc.tikka || 0;
                if (document.getElementById('sat-count-damini')) document.getElementById('sat-count-damini').innerText = gc.damini || 0;
                if (document.getElementById('sat-count-mala')) document.getElementById('sat-count-mala').innerText = gc.mala || 0;
                if (document.getElementById('sat-count-kamar_patta')) document.getElementById('sat-count-kamar_patta').innerText = gc.kamar_patta || 0;
                if (document.getElementById('sat-count-tikka_damini')) document.getElementById('sat-count-tikka_damini').innerText = gc.tikka_damini || 0;
                if (document.getElementById('sat-count-apparel')) document.getElementById('sat-count-apparel').innerText = gc.apparel || 0;
                document.getElementById('sat-modal-total-badge').innerText = `${satAllProducts.length} Items`;

                renderSatTable();
            } catch (e) {
                console.error(e);
                container.innerHTML = `<div class="p-6 text-center text-rose-500 text-xs">Error connecting to server.</div>`;
            }
        }

        function filterSatGroup(groupKey, btn) {
            satCurrentGroup = groupKey;
            document.querySelectorAll('#sat-group-tabs .sat-tab').forEach(b => {
                b.className = 'sat-tab px-2.5 py-1 text-xs font-medium rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200';
            });
            if (btn) {
                btn.className = 'sat-tab active px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-900 text-white';
            }
            renderSatTable();
        }

        function getFilteredSatProducts() {
            const query = (document.getElementById('sat-search-input')?.value || '').toLowerCase().trim();
            return satAllProducts.filter(p => {
                const matchesGroup = (satCurrentGroup === 'all') || (p.group_key === satCurrentGroup);
                if (!matchesGroup) return false;
                if (!query) return true;
                const code = (p.code || '').toLowerCase();
                const name = (p.name || '').toLowerCase();
                return code.includes(query) || name.includes(query);
            });
        }

        function renderSatTable() {
            const container = document.getElementById('sat-products-table-container');
            const filtered = getFilteredSatProducts();

            document.getElementById('sat-filtered-count').innerText = filtered.length;
            document.getElementById('sat-btn-filtered-count').innerText = filtered.length;
            document.getElementById('sat-selected-badge').innerText = `${satSelectedSkus.size} Selected`;

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="p-10 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300"></i>
                        <p>No products match the selected category or search filter.</p>
                    </div>
                `;
                return;
            }

            let html = `
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-semibold bg-slate-100/60 sticky top-0 z-10">
                            <th class="py-2 px-3 w-8 text-center">
                                <span class="sr-only">Select</span>
                            </th>
                            <th class="py-2 px-3 w-14">Image</th>
                            <th class="py-2 px-3">SKU & Category</th>
                            <th class="py-2 px-3">Product Name</th>
                            <th class="py-2 px-3 text-right">Rent / Sale</th>
                            <th class="py-2 px-3 w-20 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
            `;

            filtered.forEach(p => {
                const isChecked = satSelectedSkus.has(p.code);
                const thumb = p.image_url || 'assets/images/placeholder.png';
                html += `
                    <tr class="hover:bg-slate-50 transition-colors ${isChecked ? 'bg-slate-50/80' : ''}">
                        <td class="py-2 px-3 text-center">
                            <input type="checkbox" onchange="toggleSatSku('${escapeHtml(p.code)}', this.checked)" ${isChecked ? 'checked' : ''} class="rounded text-slate-900 cursor-pointer">
                        </td>
                        <td class="py-2 px-3">
                            <img src="${escapeHtml(thumb)}" alt="${escapeHtml(p.code)}" class="w-10 h-12 object-cover rounded border border-slate-200 bg-slate-100" onerror="this.src='https://placehold.co/40x50?text=No+Img'">
                        </td>
                        <td class="py-2 px-3">
                            <div class="font-mono font-bold text-slate-900">${escapeHtml(p.code)}</div>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-600 font-medium">${escapeHtml(p.group_name || p.category_name)}</span>
                        </td>
                        <td class="py-2 px-3">
                            <div class="text-slate-800 font-medium line-clamp-1">${escapeHtml(p.name)}</div>
                            <div class="text-[10px] text-slate-400">Added: ${escapeHtml(p.date_added)}</div>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <div class="text-slate-900 font-semibold">₹${Number(p.rent_price || 0).toLocaleString('en-IN')}</div>
                            <div class="text-[10px] text-slate-400">Sale: ₹${Number(p.sales_price || 0).toLocaleString('en-IN')}</div>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button type="button" onclick="loadSingleSatSkuToQueue('${escapeHtml(p.code)}')" class="shadcn-btn shadcn-btn-sm text-slate-700 hover:text-slate-900" title="Load SKU into AI queue">
                                <i class="fa-solid fa-plus"></i> Load
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        }

        function toggleSatSku(code, isChecked) {
            if (isChecked) {
                satSelectedSkus.add(code);
            } else {
                satSelectedSkus.delete(code);
            }
            document.getElementById('sat-selected-badge').innerText = `${satSelectedSkus.size} Selected`;
        }

        function toggleSelectAllSat(checked) {
            const filtered = getFilteredSatProducts();
            filtered.forEach(p => {
                if (checked) {
                    satSelectedSkus.add(p.code);
                } else {
                    satSelectedSkus.delete(p.code);
                }
            });
            renderSatTable();
        }

        function copySatSkus() {
            let skus = [];
            if (satSelectedSkus.size > 0) {
                skus = Array.from(satSelectedSkus);
            } else {
                skus = getFilteredSatProducts().map(p => p.code);
            }
            if (skus.length === 0) {
                showToast('No SKUs to copy.', 'error');
                return;
            }
            navigator.clipboard.writeText(skus.join(', '));
            showToast(`Copied ${skus.length} SKUs to clipboard!`);
        }

        function loadSatSelectedIntoQueue() {
            if (satSelectedSkus.size === 0) {
                showToast('Please select at least one SKU to load.', 'error');
                return;
            }
            const skus = Array.from(satSelectedSkus);
            document.getElementById('sku_input').value = skus.join(', ');
            closeSaturdayModal();
            lookupProducts();
            showToast(`Loaded ${skus.length} selected SKU(s) into queue!`);
        }

        function loadSatAllFilteredIntoQueue() {
            const filtered = getFilteredSatProducts();
            if (filtered.length === 0) {
                showToast('No products match the filter.', 'error');
                return;
            }
            const skus = filtered.slice(0, 100).map(p => p.code);
            document.getElementById('sku_input').value = skus.join(', ');
            closeSaturdayModal();
            lookupProducts();
            showToast(`Loaded ${skus.length} SKU(s) into queue!`);
        }

        function loadSingleSatSkuToQueue(code) {
            const existing = document.getElementById('sku_input').value.trim();
            const set = new Set(existing ? existing.split(/[\r\n,\s]+/) : []);
            set.add(code);
            document.getElementById('sku_input').value = Array.from(set).join(', ');
            closeSaturdayModal();
            lookupProducts();
            showToast(`Added ${code} to queue!`);
        }

        // Initialize Studio Photo (Without Model) UI Controls
        document.addEventListener('DOMContentLoaded', function() {
            renderStudioSurfacePills();
            renderStudioShotTypes();
            updateStudioLivePromptPreview();
        });
        // Also call immediately in case DOM is already loaded
        renderStudioSurfacePills();
        renderStudioShotTypes();
        updateStudioLivePromptPreview();
    </script>
</body>
</html>
