<<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Writer Settings - Srishringarr</title>
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

        .settings-container {
            max-width: 1000px;
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
            transition: all 0.15s ease;
        }

        .shadcn-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--slate-100);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shadcn-card-body {
            padding: 20px;
        }

        /* Mode Selection Card Option */
        .mode-option-card {
            position: relative;
            border: 2px solid var(--slate-200);
            border-radius: 8px;
            padding: 16px 18px;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #ffffff;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            user-select: none;
        }

        .mode-option-card:hover {
            border-color: var(--slate-400);
            background: #fbfcfd;
        }

        .mode-option-card.selected {
            border-color: var(--slate-900) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 1px var(--slate-900);
        }

        .mode-radio-circle {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--slate-300);
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .mode-option-card.selected .mode-radio-circle {
            border-color: var(--slate-900);
        }

        .mode-radio-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--slate-900);
            display: none;
        }

        .mode-option-card.selected .mode-radio-dot {
            display: block;
        }

        /* Buttons adhering strictly to ShadCN standard */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 500;
            height: 36px;
            padding: 0 14px;
            border-radius: 6px;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none !important;
        }

        .shadcn-btn-primary {
            background-color: var(--slate-900) !important;
            color: #ffffff !important;
        }

        .shadcn-btn-primary:hover {
            background-color: var(--slate-800) !important;
        }

        .shadcn-btn-outline {
            background-color: #ffffff !important;
            border-color: var(--slate-200) !important;
            color: var(--slate-700) !important;
        }

        .shadcn-btn-outline:hover {
            background-color: var(--slate-50) !important;
            border-color: var(--slate-300) !important;
        }

        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 9999px;
            background: var(--slate-100);
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
        }

        .shadcn-badge-success {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .shadcn-badge-dark {
            background: var(--slate-900);
            color: #ffffff;
            border-color: var(--slate-900);
        }

        /* Custom Slider */
        .range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 4px;
            background: var(--slate-200);
            outline: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--slate-900);
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            transition: transform 0.1s ease;
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }

        /* Toast Popup */
        #toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 99999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        #toast-notification.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
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
            $pageTitle = 'AI Writer Settings';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="settings-container">

                    <!-- Header Section -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-xl font-bold tracking-tight text-slate-900">AI Writer Settings</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <i class="fa-solid fa-gear text-xs"></i> System Settings
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Configure Gemini Vision image analysis modes, multi-photo photoshoot analysis, and copywriter synergy for <strong>All in One Writer</strong>.
                            </p>
                        </div>

                        <!-- Top Quick Action Bar -->
                        <div class="flex items-center gap-2">
                            <a href="index.php?controller=allinonewriter" class="shadcn-btn shadcn-btn-outline" title="Return to All in One Writer Studio">
                                <i class="fa-solid fa-arrow-left"></i> Open All in One Writer
                            </a>
                            <button type="submit" form="aiWriterSettingsForm" class="shadcn-btn shadcn-btn-primary save-btn-global shadow-xs">
                                <i class="fa-solid fa-floppy-disk"></i> Save Settings
                            </button>
                        </div>
                    </div>

                    <?php if (isset($_GET['success'])): ?>
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span>Settings saved and updated successfully! All in One Writer will now use this configuration.</span>
                        </div>
                        <a href="index.php?controller=allinonewriter" class="font-semibold text-emerald-900 hover:underline">Go to Writer &rarr;</a>
                    </div>
                    <?php endif; ?>

                    <!-- Form for Settings -->
                    <form id="aiWriterSettingsForm" method="POST" action="index.php?controller=setting&action=saveAiWriter">
                        
                        <!-- ==================== CARD 1: IMAGE ANALYSIS MODE ==================== -->
                        <div class="shadcn-card">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-camera text-slate-700"></i>
                                    <span class="font-semibold text-xs text-slate-900 uppercase tracking-wider">Gemini Image Analysis Mode</span>
                                </div>
                                <span class="shadcn-badge">
                                    <i class="fa-solid fa-microchip text-slate-500"></i> Gemini 2.5 / 3.1 Flash Vision
                                </span>
                            </div>

                            <div class="shadcn-card-body">
                                <p class="text-xs text-slate-600 mb-4">
                                    Select how product visual photos are processed by Google Gemini when generating high-end photoshoot images (With Model &amp; Without Model):
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    
                                    <!-- Option 1: Default (Single Image Analysis) -->
                                    <div class="mode-option-card <?php echo ($settings['image_analysis_mode'] === 'default') ? 'selected' : ''; ?>" id="card_mode_default" onclick="selectMode('default')">
                                        <input type="radio" name="image_analysis_mode" id="radio_mode_default" value="default" class="sr-only" <?php echo ($settings['image_analysis_mode'] === 'default') ? 'checked' : ''; ?>>
                                        <div class="mode-radio-circle">
                                            <div class="mode-radio-dot"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-xs font-bold text-slate-900">Default (Single Image Analysis)</h3>
                                                <span class="shadcn-badge font-mono text-[10px]">Standard Default</span>
                                            </div>
                                            <p class="text-xs text-slate-500 leading-relaxed mb-2">
                                                USE THE DEFAULT WE CURRENTLY HAVE. Only the primary/first photo of the product is sent to Gemini for image generation.
                                            </p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                                <span><i class="fa-solid fa-bolt text-amber-500"></i> Fastest API execution</span>
                                                <span><i class="fa-solid fa-image"></i> 1 photo per run</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Option 2: Multiple Images Analysis (Up to 10 Images) -->
                                    <div class="mode-option-card <?php echo ($settings['image_analysis_mode'] === 'multi') ? 'selected' : ''; ?>" id="card_mode_multi" onclick="selectMode('multi')">
                                        <input type="radio" name="image_analysis_mode" id="radio_mode_multi" value="multi" class="sr-only" <?php echo ($settings['image_analysis_mode'] === 'multi') ? 'checked' : ''; ?>>
                                        <div class="mode-radio-circle">
                                            <div class="mode-radio-dot"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-xs font-bold text-slate-900">Multiple Images Analysis (Max 10)</h3>
                                                <span class="shadcn-badge shadcn-badge-success font-mono text-[10px]">High Accuracy</span>
                                            </div>
                                            <p class="text-xs text-slate-500 leading-relaxed mb-2">
                                                Generates images using comprehensive multi-photo analysis if the product has multiple photos (up to 10 images).
                                            </p>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                                <span><i class="fa-solid fa-circle-check text-emerald-600"></i> Eliminates improper results</span>
                                                <span><i class="fa-solid fa-images"></i> Up to 10 photos</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deep Explanation Box -->
                                <div class="mt-4 p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600 flex items-start gap-3">
                                    <i class="fa-solid fa-circle-info text-slate-500 mt-0.5 text-sm"></i>
                                    <div>
                                        <strong class="text-slate-900 font-semibold">Why Multi-Image Analysis matters:</strong>
                                        Single photos can miss subtle latkans, side chains (sari), back ties, waistband hooks, or gemstone nuances. 
                                        In <strong>Multiple Images Analysis</strong> mode, Gemini synthesizes all angles (front, side, close-up details, model shots) so the generated model photoshoot faithfully matches the exact authentic jewelry.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== CARD 2: MULTI-IMAGE CONFIGURATION ==================== -->
                        <div class="shadcn-card" id="multi_image_config_panel">
                            <div class="shadcn-card-header">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-sliders text-slate-700"></i>
                                    <span class="font-semibold text-xs text-slate-900 uppercase tracking-wider">Multi-Image Analysis Parameters</span>
                                </div>
                                <span class="shadcn-badge font-mono" id="maxImagesBadgeDisplay">
                                    Limit: <?php echo (int)($settings['max_images'] ?? 10); ?> Images
                                </span>
                            </div>

                            <div class="shadcn-card-body space-y-5">
                                
                                <!-- Max Images Slider -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="max_images_number" class="text-xs font-semibold text-slate-900">
                                            Maximum Photos to Analyze per Product (Max 10)
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-500">Max limit:</span>
                                            <input type="number" id="max_images_number" name="max_images" min="1" max="10" value="<?php echo (int)($settings['max_images'] ?? 10); ?>" class="w-16 h-7 text-center font-bold text-xs bg-slate-100 border border-slate-300 rounded" oninput="syncMaxImages(this.value)">
                                            <span class="text-xs font-medium text-slate-600">images</span>
                                        </div>
                                    </div>

                                    <input type="range" id="max_images_slider" min="1" max="10" step="1" value="<?php echo (int)($settings['max_images'] ?? 10); ?>" class="range-slider" oninput="syncMaxImages(this.value)">
                                    
                                    <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 font-mono">
                                        <span>1 (Single)</span>
                                        <span>2</span>
                                        <span>3</span>
                                        <span>4</span>
                                        <span>5</span>
                                        <span>6</span>
                                        <span>7</span>
                                        <span>8</span>
                                        <span>9</span>
                                        <span>10 (Max Allowed)</span>
                                    </div>
                                    
                                    <p class="text-[11.5px] text-slate-500 mt-2">
                                        * Note: If a product has fewer photos than the limit (for example, 3 photos), it will analyze all 3 photos. If it has 15 photos, it will analyze the top 10 photos in rank order.
                                    </p>
                                </div>

                                <hr class="border-slate-100">

                                <!-- Also apply to Copywriter -->
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" id="include_content_analysis" name="include_content_analysis" value="1" <?php echo !empty($settings['include_content_analysis']) ? 'checked' : ''; ?> class="mt-0.5 rounded border-slate-300 text-slate-900 focus:ring-0 cursor-pointer" style="width: 15px; height: 15px;">
                                    <div>
                                        <label for="include_content_analysis" class="text-xs font-semibold text-slate-900 cursor-pointer">
                                            Also apply multi-image analysis to AI Copywriting (Titles, Short &amp; Long Descriptions)
                                        </label>
                                        <p class="text-[11.5px] text-slate-500 mt-0.5">
                                            When enabled, AI Copywriting (Gemini/OpenAI) will also inspect all photos to identify accurate gemstone colors, intricate embroidery, clasps, and latkans in generated descriptions.
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ==================== DEDICATED SAVE ACTION CARD ==================== -->
                        <div class="shadcn-card" style="margin-top: -6px; border-color: var(--slate-300);">
                            <div class="p-4 flex items-center justify-between flex-wrap gap-3 bg-white">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock-rotate-left text-slate-400 text-xs"></i>
                                    <span class="text-xs text-slate-500" id="saveTimestamp">
                                        <?php echo !empty($settings['updated_at']) ? 'Last updated: ' . htmlspecialchars($settings['updated_at']) : 'Configuration active'; ?>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <button type="button" onclick="resetToDefaults()" class="shadcn-btn shadcn-btn-outline">
                                        <i class="fa-solid fa-rotate-left"></i> Reset to Default
                                    </button>
                                    <button type="submit" id="saveSettingsBtn" form="aiWriterSettingsForm" class="shadcn-btn shadcn-btn-primary save-btn-global shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-floppy-disk"></i> Save Settings
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>

                    <!-- ==================== CARD 3: LIVE SKU PHOTO INSPECTOR ==================== -->
                    <div class="shadcn-card">
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-slate-700"></i>
                                <span class="font-semibold text-xs text-slate-900 uppercase tracking-wider">Test SKU Photo Inspection</span>
                            </div>
                            <span class="shadcn-badge">
                                Live Verification
                            </span>
                        </div>

                        <div class="shadcn-card-body">
                            <p class="text-xs text-slate-600 mb-3">
                                Verify how any product's photos will be analyzed under your current settings. Enter any SKU below:
                            </p>

                            <div class="flex items-center gap-2 max-w-md mb-4">
                                <input type="text" id="inspectSkuInput" placeholder="Enter SKU (e.g. J100, KP101, NL101)..." class="flex-1 h-9 px-3 bg-white border border-slate-200 rounded-md text-xs text-slate-900 font-mono focus:border-slate-900 focus:outline-none">
                                <button type="button" onclick="runSkuInspection()" id="inspectSkuBtn" class="shadcn-btn shadcn-btn-primary h-9">
                                    <i class="fa-solid fa-search"></i> Inspect SKU
                                </button>
                            </div>

                            <!-- Inspection Results Area -->
                            <div id="skuInspectionResults" class="hidden border border-slate-200 rounded-lg p-4 bg-slate-50/50">
                                <div id="skuInspectMeta" class="mb-3"></div>
                                <div id="skuInspectGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notification Element -->
    <div id="toast-notification" class="bg-slate-900 text-white px-4 py-3 rounded-lg shadow-xl border border-slate-700 text-xs flex items-center gap-2.5 font-medium">
        <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
        <span id="toast-message">Settings saved successfully!</span>
    </div>

    <script>
    function selectMode(mode) {
        const radDef = document.getElementById('radio_mode_default');
        const radMulti = document.getElementById('radio_mode_multi');
        if (mode === 'default' && radDef) {
            radDef.checked = true;
        } else if (mode === 'multi' && radMulti) {
            radMulti.checked = true;
        }
        updateModeSelection();
    }

    function updateModeSelection() {
        const radDef = document.getElementById('radio_mode_default');
        const radMulti = document.getElementById('radio_mode_multi');
        let selected = (radMulti && radMulti.checked) ? 'multi' : 'default';

        const cardDefault = document.getElementById('card_mode_default');
        const cardMulti = document.getElementById('card_mode_multi');
        const configPanel = document.getElementById('multi_image_config_panel');

        if (selected === 'default') {
            if (cardDefault) cardDefault.classList.add('selected');
            if (cardMulti) cardMulti.classList.remove('selected');
            if (configPanel) {
                configPanel.style.borderColor = 'var(--slate-200)';
            }
        } else {
            if (cardDefault) cardDefault.classList.remove('selected');
            if (cardMulti) cardMulti.classList.add('selected');
            if (configPanel) {
                configPanel.style.borderColor = 'var(--slate-400)';
            }
        }
    }

    function syncMaxImages(val) {
        val = parseInt(val, 10);
        if (isNaN(val) || val < 1) val = 1;
        if (val > 10) val = 10;

        const slider = document.getElementById('max_images_slider');
        const num = document.getElementById('max_images_number');
        const badge = document.getElementById('maxImagesBadgeDisplay');

        if (slider) slider.value = val;
        if (num) num.value = val;
        if (badge) badge.textContent = `Limit: ${val} Images`;
    }

    function resetToDefaults() {
        selectMode('default');
        syncMaxImages(10);
        const chk = document.getElementById('include_content_analysis');
        if (chk) chk.checked = true;
        showToast('Reset to default configuration. Click "Save Settings" to confirm.');
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateModeSelection();

        const form = document.getElementById('aiWriterSettingsForm');
        if (form) {
            form.addEventListener('submit', function() {
                const saveBtns = document.querySelectorAll('.save-btn-global');
                saveBtns.forEach(b => {
                    b.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
                    b.style.pointerEvents = 'none';
                    b.style.opacity = '0.85';
                });
                setTimeout(() => {
                    saveBtns.forEach(b => {
                        b.disabled = true;
                    });
                }, 100);
            });
        }
    });

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const text = document.getElementById('toast-message');
        if (!toast || !text) return;

        text.textContent = msg;
        if (type === 'error') {
            toast.className = 'bg-rose-900 text-white px-4 py-3 rounded-lg shadow-xl border border-rose-700 text-xs flex items-center gap-2.5 font-medium show';
        } else {
            toast.className = 'bg-slate-900 text-white px-4 py-3 rounded-lg shadow-xl border border-slate-700 text-xs flex items-center gap-2.5 font-medium show';
        }
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }

    async function runSkuInspection() {
        const skuInput = document.getElementById('inspectSkuInput');
        const sku = skuInput ? skuInput.value.trim() : '';
        if (!sku) {
            showToast('Please enter a SKU code to test.', 'error');
            return;
        }

        const btn = document.getElementById('inspectSkuBtn');
        const resultsBox = document.getElementById('skuInspectionResults');
        const metaBox = document.getElementById('skuInspectMeta');
        const gridBox = document.getElementById('skuInspectGrid');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        try {
            const res = await fetch(`index.php?controller=setting&action=inspectSku&sku=${encodeURIComponent(sku)}`);
            const data = await res.json();

            if (!data.success) {
                showToast(data.error || 'SKU not found', 'error');
                resultsBox.classList.add('hidden');
                return;
            }

            resultsBox.classList.remove('hidden');

            metaBox.innerHTML = `
                <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-white border border-slate-200 rounded-md">
                    <div>
                        <div class="flex items-center gap-2">
                            <strong class="text-xs text-slate-900">${data.sku}</strong>
                            <span class="text-xs text-slate-600">&bull; ${data.name}</span>
                            <span class="shadcn-badge font-mono text-[10px]">${data.type}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            Total Gallery Photos in DB: <strong>${data.total_images_found}</strong>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="shadcn-badge ${data.active_mode === 'default' ? 'shadcn-badge-dark' : ''}">
                            Default Mode: Analyzes ${data.default_analyzed_count} Photo
                        </span>
                        <span class="shadcn-badge ${data.active_mode === 'multi' ? 'shadcn-badge-success' : ''}">
                            Multi Mode: Analyzes ${data.multi_analyzed_count} of ${data.total_images_found} Photos (Max ${data.max_allowed})
                        </span>
                    </div>
                </div>
            `;

            if (!data.images || data.images.length === 0) {
                gridBox.innerHTML = `<p class="col-span-full text-xs text-slate-500 py-3 text-center">No images found for SKU ${data.sku}</p>`;
            } else {
                gridBox.innerHTML = data.images.map(img => {
                    const isDefault = img.default_mode_analyzed;
                    const isMulti = img.multi_mode_analyzed;

                    let badgeHtml = '';
                    if (isDefault && isMulti) {
                        badgeHtml = '<span class="bg-emerald-600 text-white text-[9px] px-1.5 py-0.5 rounded font-bold">Both Modes</span>';
                    } else if (isMulti) {
                        badgeHtml = '<span class="bg-blue-600 text-white text-[9px] px-1.5 py-0.5 rounded font-bold">Multi Only</span>';
                    } else {
                        badgeHtml = '<span class="bg-slate-300 text-slate-700 text-[9px] px-1.5 py-0.5 rounded">Exceeds Max</span>';
                    }

                    return `
                        <div class="border border-slate-200 rounded-md overflow-hidden bg-white shadow-xs">
                            <div class="relative aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img src="${img.url}" alt="${img.raw_name}" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'50\\' height=\\'50\\'><rect fill=\\'%23eee\\' width=\\'50\\' height=\\'50\\'/></svg>'">
                                <div class="absolute top-1 right-1">
                                    ${badgeHtml}
                                </div>
                                ${img.is_main ? '<span class="absolute bottom-1 left-1 bg-slate-900/80 text-white text-[9px] px-1 rounded">Main</span>' : ''}
                            </div>
                            <div class="p-1.5 text-[10px] text-slate-600 truncate font-mono" title="${img.raw_name}">
                                #${img.index} ${img.raw_name.split('/').pop()}
                            </div>
                        </div>
                    `;
                }).join('');
            }

        } catch (err) {
            console.error('Inspection error:', err);
            showToast('Failed to inspect SKU.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-search"></i> Inspect SKU';
        }
    }

    // Initialize mode selection on load
    document.addEventListener('DOMContentLoaded', () => {
        updateModeSelection();
    });
    </script>
</body>
</html>
