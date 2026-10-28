<!DOCTYPE html>
<html lang="en">
<head>
    <title>Image Optimizer &amp; Responsive Variants Engine - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
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
            max-width: 1560px;
            margin: 0 auto;
        }

        /* Metric cards */
        .metric-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            padding: 16px 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .metric-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .metric-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--slate-500);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .metric-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.2;
            margin-top: 6px;
        }
        .metric-sub {
            font-size: 11.5px;
            color: var(--slate-500);
            margin-top: 4px;
        }

        /* Toolbar controls */
        .toolbar-panel {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        /* Form elements */
        .shadcn-select, .shadcn-input {
            height: 34px;
            font-size: 12.5px;
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            padding: 0 10px;
            background-color: #ffffff;
            color: var(--slate-800);
            transition: all 0.15s ease;
        }
        .shadcn-select:focus, .shadcn-input:focus {
            outline: none;
            border-color: var(--slate-900);
            box-shadow: 0 0 0 1px var(--slate-900);
        }

        /* Buttons */
        .btn-shadcn-primary {
            background-color: var(--slate-900);
            color: #ffffff;
            border: 1px solid var(--slate-900);
            font-weight: 600;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-shadcn-primary:hover:not(:disabled) {
            background-color: var(--slate-800);
            border-color: var(--slate-800);
        }
        .btn-shadcn-secondary {
            background-color: #ffffff;
            color: var(--slate-700);
            border: 1px solid var(--slate-200);
            font-weight: 600;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-shadcn-secondary:hover:not(:disabled) {
            background-color: var(--slate-50);
            color: var(--slate-900);
            border-color: #cbd5e1;
        }

        /* Table */
        .img-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12px;
        }
        .img-table th {
            background-color: var(--slate-50);
            color: var(--slate-600);
            font-weight: 600;
            text-align: left;
            padding: 10px 14px;
            border-bottom: 1px solid var(--slate-200);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .img-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--slate-800);
            background-color: #ffffff;
        }
        .img-table tr:hover td {
            background-color: #f8fafc;
        }

        /* Variant mini pills */
        .variant-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 600;
            line-height: 1.3;
        }
        .variant-pill.exists {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .variant-pill.missing {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px dashed #cbd5e1;
        }

        /* Thumb preview */
        .thumb-preview-box {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            overflow: hidden;
            background: #f1f5f9;
            border: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .thumb-preview-box:hover {
            transform: scale(1.08);
            border-color: var(--slate-900);
        }
        .thumb-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Toast notifications */
        #opt-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background-color: var(--slate-900);
            color: #ffffff;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            display: none;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Image Optimizer & Responsive Variants';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container space-y-6">

                    <!-- Header Banner -->
                    <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-800 text-lg flex-shrink-0">
                                <i class="fa-solid fa-compress"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    Image Optimizer &amp; Responsive Variants Engine
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-bolt mr-1"></i> 4-Variant Auto-Gen
                                    </span>
                                </h1>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    Generates 4 tailored responsive variants (<strong class="text-slate-700">Original</strong>, <strong class="text-slate-700">Large Device 1200px</strong>, <strong class="text-slate-700">Tabs 800px</strong>, <strong class="text-slate-700">Mobile 480px</strong>) in next-gen WebP format and performs safe in-place lossless/lossy compression.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" onclick="openCliGuideModal()" class="btn-shadcn-secondary text-xs">
                                <i class="fa-solid fa-terminal text-slate-500"></i> CLI Bulk Script Guide
                            </button>
                            <button type="button" onclick="startBatchForMissingVariants()" id="btn_quick_missing" class="btn-shadcn-primary text-xs">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i> Generate All Missing Variants (<span id="quick_missing_count">0</span>)
                            </button>
                        </div>
                    </div>

                    <!-- 4 Metric Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Scanned Images -->
                        <div class="metric-card">
                            <div class="metric-title">
                                <span>Total Master Images</span>
                                <i class="fa-regular fa-images text-slate-400"></i>
                            </div>
                            <div class="metric-value" id="metric_total_files">0</div>
                            <div class="metric-sub" id="metric_total_size">0 MB total storage</div>
                        </div>

                        <!-- Heavy Images (>1MB) -->
                        <div class="metric-card">
                            <div class="metric-title">
                                <span>Heavy Images (&gt; 1 MB)</span>
                                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                            </div>
                            <div class="metric-value text-amber-600" id="metric_heavy_files">0</div>
                            <div class="metric-sub" id="metric_heavy_size">0 MB uncompressed payload</div>
                        </div>

                        <!-- Potential Space Recovery -->
                        <div class="metric-card">
                            <div class="metric-title">
                                <span>Potential Space Recovery</span>
                                <i class="fa-solid fa-leaf text-emerald-500"></i>
                            </div>
                            <div class="metric-value text-emerald-600" id="metric_est_savings">~0 MB</div>
                            <div class="metric-sub">~80% - 95% bandwidth saved on mobile</div>
                        </div>

                        <!-- Responsive Variant Coverage -->
                        <div class="metric-card">
                            <div class="metric-title">
                                <span>4-Variant Coverage</span>
                                <i class="fa-solid fa-mobile-screen text-slate-400"></i>
                            </div>
                            <div class="metric-value" id="metric_variant_coverage">0 / 0</div>
                            <div class="metric-sub" id="metric_variant_sub">0 complete &bull; 0 need variants</div>
                        </div>
                    </div>

                    <!-- Filter & Toolbar Panel -->
                    <div class="toolbar-panel p-4 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <!-- Left: Root Folder & Filters -->
                            <div class="flex flex-wrap items-center gap-2.5">
                                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-regular fa-folder-open text-slate-400"></i> Root:
                                </label>
                                <select id="filter_root" class="shadcn-select font-medium" onchange="triggerFullScan()">
                                    <?php foreach ($roots as $rKey => $rInfo): ?>
                                        <option value="<?php echo htmlspecialchars($rKey); ?>" <?php echo $rKey === $defaultRoot ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rInfo['name']); ?> (<?php echo htmlspecialchars($rInfo['folder']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="text-xs font-semibold text-slate-700 ml-1">Filter:</label>
                                <select id="filter_type" class="shadcn-select" onchange="applyClientFilters()">
                                    <option value="missing_variants">Missing Responsive Variants (Recommended)</option>
                                    <option value="all">All Master Images</option>
                                    <option value="heavy">Heavy Files (&gt; 1 MB)</option>
                                    <option value="has_variants">Already Has All Variants</option>
                                </select>

                                <label class="text-xs font-semibold text-slate-700 ml-1">Mode:</label>
                                <select id="config_mode" class="shadcn-select">
                                    <option value="lossy">Lossy (Recommended - 82% Web Retina)</option>
                                    <option value="lossless">Lossless (Preserve Exact Pixels)</option>
                                </select>

                                <label class="text-xs font-semibold text-slate-700 ml-1">Max Dim:</label>
                                <select id="config_max_dim" class="shadcn-select">
                                    <option value="1800">1800 px (Retina Web - Recommended)</option>
                                    <option value="2400">2400 px (Ultra High-Res)</option>
                                    <option value="1200">1200 px (Standard Web)</option>
                                </select>
                            </div>

                            <!-- Right: Search & Rescan -->
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <input type="text" id="search_query" class="shadcn-input pl-8 w-64" placeholder="Filter by SKU or filename..." oninput="debounceSearch()">
                                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                                </div>
                                <button type="button" onclick="triggerFullScan()" id="btn_rescan" class="btn-shadcn-secondary text-xs">
                                    <i class="fa-solid fa-arrows-rotate"></i> Rescan
                                </button>
                            </div>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                            <div class="text-xs text-slate-500 font-medium flex items-center gap-2">
                                <span>Selected: <strong class="text-slate-900 font-bold" id="selected_count_display">0</strong> images</span>
                                <span class="text-slate-300">|</span>
                                <span id="viewing_count_display">Loading images...</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="runBatchAction('generate_variants')" id="btn_batch_variants" class="btn-shadcn-primary text-xs" disabled>
                                    <i class="fa-solid fa-bolt text-amber-300"></i> Generate Variants for Selected (<span class="sel-count-label">0</span>)
                                </button>
                                <button type="button" onclick="runBatchAction('compress_lossy')" id="btn_batch_compress" class="btn-shadcn-secondary text-xs" disabled>
                                    <i class="fa-solid fa-compress text-slate-500"></i> Compress Selected (<span class="sel-count-label">0</span>)
                                </button>
                                <button type="button" onclick="startBatchCompressAllFiltered()" id="btn_compress_all" class="btn-shadcn-secondary text-xs">
                                    <i class="fa-solid fa-layer-group text-slate-500"></i> Compress All Filtered (<span id="filtered_count_label">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Progress Drawer (Smoothly reveals when processing) -->
                    <div id="batch_progress_card" class="bg-white border border-slate-200 rounded-lg p-5 shadow-sm space-y-3 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-spinner fa-spin text-slate-900 text-base"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900" id="batch_title">Processing Batch Tasks...</h4>
                                    <p class="text-[11px] text-slate-500" id="batch_subtitle">Preparing image pipeline...</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="toggleBatchPause()" id="btn_batch_pause" class="btn-shadcn-secondary text-xs">
                                    <i class="fa-solid fa-pause"></i> Pause
                                </button>
                                <button type="button" onclick="stopBatchProcessing()" id="btn_batch_stop" class="btn-shadcn-secondary text-xs text-rose-600 hover:text-rose-700">
                                    <i class="fa-solid fa-stop"></i> Stop
                                </button>
                            </div>
                        </div>

                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div id="batch_progress_bar" class="bg-slate-900 h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
                            <span id="batch_stat_counts">0 / 0 images</span>
                            <span id="batch_stat_savings">0 variants created &bull; 0 KB saved</span>
                        </div>
                    </div>

                    <!-- Images Table Card -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="img-table">
                                <thead>
                                    <tr>
                                        <th class="w-10 text-center">
                                            <input type="checkbox" id="check_all_page" onchange="toggleSelectAllPage(this)">
                                        </th>
                                        <th class="w-16">Preview</th>
                                        <th>File &amp; Subfolder</th>
                                        <th>Dimensions</th>
                                        <th>Original Size</th>
                                        <th>4 Responsive Variants (WebP)</th>
                                        <th>Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    <tr>
                                        <td colspan="8" class="text-center py-12 text-slate-400">
                                            <i class="fa-solid fa-spinner fa-spin text-xl mb-2"></i>
                                            <p class="text-xs font-medium">Scanning upload folders and inspecting variants...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Pagination Footer -->
                        <div class="p-3.5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                            <div id="pagination_info">Showing 0 of 0 images</div>
                            <div class="flex items-center gap-1.5" id="pagination_controls">
                                <button type="button" class="btn-shadcn-secondary py-1 px-2.5 text-xs" onclick="changePage(-1)" id="btn_prev_page" disabled>&larr; Prev</button>
                                <span class="px-2 font-mono text-slate-700" id="page_counter">Page 1 of 1</span>
                                <button type="button" class="btn-shadcn-secondary py-1 px-2.5 text-xs" onclick="changePage(1)" id="btn_next_page" disabled>Next &rarr;</button>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <?php include __DIR__ . '/../partials/scripts.php'; ?>
        </div>
    </div>

    <!-- CLI Guide & Automation Modal -->
    <div id="cliGuideModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg border border-slate-200 shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-800">
                        <i class="fa-solid fa-terminal text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Automation &amp; CLI Background Runner</h3>
                        <p class="text-[11px] text-slate-500">How to handle existing products &amp; automatic uploads</p>
                    </div>
                </div>
                <button type="button" onclick="closeCliGuideModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>
            <div class="p-5 space-y-4 text-xs text-slate-600 leading-relaxed">
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-check-circle text-emerald-600"></i>
                        1. Upload Automation (Always Active)
                    </div>
                    <p class="text-[11.5px]">
                        Whenever products are uploaded via <code class="bg-white px-1.5 py-0.5 rounded border text-slate-800">import_archive.php</code> or the Admin Product Edit/Add screen, the system <strong>automatically creates all 4 variants</strong> in the same directory:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5 text-[11px] text-slate-500 font-mono">
                        <li>photo.jpg (Original master)</li>
                        <li>photo_large.webp (Max 1200px Retina Desktop)</li>
                        <li>photo_tab.webp (Max 800px iPad / Tablets)</li>
                        <li>photo_mobile.webp (Max 480px Smartphones &amp; Thumbnails)</li>
                    </ul>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 space-y-2">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        2. How to Process Already Uploaded Products (Bulk CLI)
                    </div>
                    <p class="text-[11.5px]">
                        For thousands of existing catalog photos, run the high-speed PHP CLI script directly from your terminal or command prompt. It processes hundreds of images safely without web server timeouts:
                    </p>
                    <div class="bg-slate-900 text-slate-100 rounded p-3 font-mono text-[11px] space-y-2">
                        <p class="text-slate-400"># Process all existing upload folders (Sri Shringarr &amp; YN):</p>
                        <p class="text-emerald-400 font-semibold select-all">php new_admin/scripts/generate_variants_cli.php --dir=all</p>
                        <p class="text-slate-400 mt-2"># Or process only Sri Shringarr product uploads:</p>
                        <p class="text-emerald-400 font-semibold select-all">php new_admin/scripts/generate_variants_cli.php --dir=ss_uploads</p>
                        <p class="text-slate-400 mt-2"># With in-place compression on heavy originals (&gt; 1MB):</p>
                        <p class="text-emerald-400 font-semibold select-all">php new_admin/scripts/generate_variants_cli.php --dir=all --compress</p>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 space-y-1.5">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-code text-indigo-500"></i>
                        3. Frontend Usage with HTML &lt;picture&gt; / WebP
                    </div>
                    <p class="text-[11.5px]">
                        Your modern frontend components automatically load the lightweight <code class="bg-white px-1 border rounded text-slate-800">_mobile.webp</code> on phones and <code class="bg-white px-1 border rounded text-slate-800">_large.webp</code> on desktop screens. If a variant hasn't been generated yet, it gracefully falls back to the original image without any 404s.
                    </p>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button type="button" onclick="closeCliGuideModal()" class="btn-shadcn-secondary text-xs">Close</button>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div id="imageLightboxModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4" onclick="closeLightboxModal()">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
            <img id="lightbox_img" src="" alt="Full preview" class="max-w-full max-h-[82vh] rounded-lg shadow-2xl object-contain bg-slate-900">
            <div class="mt-3 flex items-center gap-4 text-xs text-white">
                <span id="lightbox_caption" class="font-mono"></span>
                <button type="button" onclick="closeLightboxModal()" class="px-2.5 py-1 rounded bg-white/20 hover:bg-white/30 text-white font-semibold">Close (Esc)</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="opt-toast">
        <i class="fa-solid fa-check text-emerald-400"></i>
        <span id="opt-toast-msg">Action completed</span>
    </div>

    <script>
        // State variables
        let allImages = [];
        let filteredImages = [];
        let selectedSet = new Set();
        let currentPage = 1;
        const perPage = 50;

        // Batch processing state
        let batchActive = false;
        let batchPaused = false;
        let batchStopRequested = false;
        let batchTotalSavedBytes = 0;
        let batchTotalVariantsCreated = 0;
        let searchTimer = null;

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            triggerFullScan();
        });

        function showToast(msg) {
            const t = document.getElementById('opt-toast');
            const m = document.getElementById('opt-toast-msg');
            m.innerText = msg;
            t.style.display = 'inline-flex';
            clearTimeout(t.timer);
            t.timer = setTimeout(() => { t.style.display = 'none'; }, 3500);
        }

        // Full scan trigger
        async function triggerFullScan() {
            const rootKey = document.getElementById('filter_root').value;
            const tbody = document.getElementById('table_body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-14 text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 text-slate-800"></i>
                        <p class="text-xs font-semibold text-slate-700">Scanning directory files &amp; responsive variants...</p>
                        <p class="text-[11px] text-slate-400 mt-1">Inspecting byte footprints and WebP companions</p>
                    </td>
                </tr>
            `;

            try {
                const res = await fetch(`index.php?controller=imageoptimizer&action=scan&root=${encodeURIComponent(rootKey)}`);
                const data = await res.json();
                if (!data.success) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-rose-600 font-semibold">Error: ${data.error || 'Failed to scan directory'}</td></tr>`;
                    return;
                }

                allImages = data.files || [];
                selectedSet.clear();

                // Update summary metric cards
                const sum = data.summary;
                document.getElementById('metric_total_files').innerText = sum.total_files.toLocaleString();
                document.getElementById('metric_total_size').innerText = sum.total_size_fmt + ' total storage';
                document.getElementById('metric_heavy_files').innerText = sum.count_gt_1mb.toLocaleString();
                document.getElementById('metric_heavy_size').innerText = sum.heavy_size_fmt + ' heavy payload';
                document.getElementById('metric_est_savings').innerText = '~' + sum.est_savings_fmt;

                const comp = sum.variants_complete_count || 0;
                const miss = sum.variants_missing_count || 0;
                const tot = sum.total_files || 1;
                const pct = Math.round((comp / tot) * 100);
                document.getElementById('metric_variant_coverage').innerText = `${comp} / ${tot}`;
                document.getElementById('metric_variant_sub').innerText = `${comp} complete (${pct}%) • ${miss} need variants`;

                document.getElementById('quick_missing_count').innerText = miss.toLocaleString();

                applyClientFilters();
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-rose-600 font-semibold">Scan request failed: ${err.message}</td></tr>`;
            }
        }

        // Apply client filters & search
        function applyClientFilters() {
            const filterType = document.getElementById('filter_type').value;
            const q = (document.getElementById('search_query').value || '').toLowerCase().trim();

            filteredImages = allImages.filter(img => {
                // Secondary filter
                if (filterType === 'missing_variants' && img.variants_status && img.variants_status.has_all) return false;
                if (filterType === 'has_variants' && (!img.variants_status || !img.variants_status.has_all)) return false;
                if (filterType === 'heavy' && img.size < 1048576) return false;

                // Search query
                if (q) {
                    const matchName = img.filename.toLowerCase().includes(q);
                    const matchFolder = (img.folder || '').toLowerCase().includes(q);
                    const matchRel = (img.rel_path || '').toLowerCase().includes(q);
                    if (!matchName && !matchFolder && !matchRel) return false;
                }
                return true;
            });

            currentPage = 1;
            renderTablePage();
            updateSelectionState();
        }

        function debounceSearch() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => { applyClientFilters(); }, 250);
        }

        // Render current page table rows
        function renderTablePage() {
            const tbody = document.getElementById('table_body');
            const total = filteredImages.length;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="fa-regular fa-folder-open text-2xl mb-2"></i>
                            <p class="text-xs font-semibold text-slate-700">No images matched the active filter</p>
                            <p class="text-[11px] text-slate-400 mt-1">Try switching to 'All Master Images' or clear search query</p>
                        </td>
                    </tr>
                `;
                document.getElementById('pagination_info').innerText = 'Showing 0 of 0 images';
                document.getElementById('btn_prev_page').disabled = true;
                document.getElementById('btn_next_page').disabled = true;
                document.getElementById('page_counter').innerText = 'Page 1 of 1';
                document.getElementById('filtered_count_label').innerText = '0';
                return;
            }

            const totalPages = Math.ceil(total / perPage);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = Math.min(startIdx + perPage, total);
            const pageItems = filteredImages.slice(startIdx, endIdx);

            document.getElementById('pagination_info').innerText = `Showing ${startIdx + 1} - ${endIdx} of ${total.toLocaleString()} images`;
            document.getElementById('page_counter').innerText = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('btn_prev_page').disabled = (currentPage <= 1);
            document.getElementById('btn_next_page').disabled = (currentPage >= totalPages);
            document.getElementById('filtered_count_label').innerText = total.toLocaleString();

            const rootKey = document.getElementById('filter_root').value;

            let html = '';
            pageItems.forEach(img => {
                const isSelected = selectedSet.has(img.rel_path);
                const previewUrl = `index.php?controller=imageoptimizer&action=preview&root=${encodeURIComponent(rootKey)}&file=${encodeURIComponent(img.rel_path)}`;

                const vStat = img.variants_status || { has_all: false, variants: {} };
                const vLarge = vStat.variants?.large;
                const vTab = vStat.variants?.tab;
                const vMobile = vStat.variants?.mobile;

                let sizeBadgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                if (img.badge === 'danger') sizeBadgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                else if (img.badge === 'warning') sizeBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                else if (img.badge === 'info') sizeBadgeClass = 'bg-sky-50 text-sky-700 border-sky-200';

                html += `
                    <tr id="row_${img.id}" class="${isSelected ? 'bg-slate-50/80' : ''}">
                        <td class="text-center">
                            <input type="checkbox" class="img-row-check" data-rel="${escapeHtml(img.rel_path)}" ${isSelected ? 'checked' : ''} onchange="toggleSelectImage('${escapeHtml(img.rel_path)}', this.checked)">
                        </td>
                        <td>
                            <div class="thumb-preview-box" onclick="openLightboxModal('${escapeHtml(previewUrl)}', '${escapeHtml(img.filename)}')">
                                <img src="${escapeHtml(previewUrl)}" loading="lazy" alt="${escapeHtml(img.filename)}" onerror="this.src='/placeholder-product.svg'">
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold text-slate-900 truncate max-w-[280px]" title="${escapeHtml(img.filename)}">${escapeHtml(img.filename)}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-600 border border-slate-200">${escapeHtml(img.folder)}</span>
                                <span>${escapeHtml(img.date_fmt)}</span>
                            </div>
                        </td>
                        <td class="font-mono text-xs text-slate-600">${escapeHtml(img.dimensions || 'N/A')}</td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold border ${sizeBadgeClass}">
                                ${escapeHtml(img.size_fmt)}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="variant-pill exists" title="Original Master">
                                    <i class="fa-solid fa-check text-[9px]"></i> Orig
                                </span>
                                <span class="variant-pill ${vLarge && vLarge.exists ? 'exists' : 'missing'}" title="${vLarge && vLarge.exists ? vLarge.size_fmt : 'Missing Large 1200px'}">
                                    <i class="fa-solid ${vLarge && vLarge.exists ? 'fa-check text-[9px]' : 'fa-plus text-[8px]'}"></i> L (1200)
                                </span>
                                <span class="variant-pill ${vTab && vTab.exists ? 'exists' : 'missing'}" title="${vTab && vTab.exists ? vTab.size_fmt : 'Missing Tab 800px'}">
                                    <i class="fa-solid ${vTab && vTab.exists ? 'fa-check text-[9px]' : 'fa-plus text-[8px]'}"></i> Tab (800)
                                </span>
                                <span class="variant-pill ${vMobile && vMobile.exists ? 'exists' : 'missing'}" title="${vMobile && vMobile.exists ? vMobile.size_fmt : 'Missing Mobile 480px'}">
                                    <i class="fa-solid ${vMobile && vMobile.exists ? 'fa-check text-[9px]' : 'fa-plus text-[8px]'}"></i> Mob (480)
                                </span>
                            </div>
                        </td>
                        <td id="status_${img.id}">
                            ${vStat.has_all 
                                ? '<span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-semibold"><i class="fa-solid fa-circle-check text-emerald-600"></i> All 4 Ready</span>' 
                                : '<span class="inline-flex items-center gap-1 text-[11px] text-slate-500"><i class="fa-regular fa-clock"></i> Missing Variants</span>'}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" onclick="generateVariantsSingle('${escapeHtml(img.rel_path)}', '${img.id}')" class="btn-shadcn-primary py-1 px-2 text-[11px]" title="Generate all 3 responsive WebP variants (Mobile, Tab, Large)">
                                    <i class="fa-solid fa-bolt text-amber-300"></i> Variants
                                </button>
                                <button type="button" onclick="compressSingle('${escapeHtml(img.rel_path)}', '${img.id}', 'lossy')" class="btn-shadcn-secondary py-1 px-2 text-[11px]" title="Safe in-place lossy compression">
                                    Lossy
                                </button>
                                <a href="${escapeHtml(previewUrl)}" target="_blank" class="btn-shadcn-secondary py-1 px-2 text-[11px]" title="Open full image in new tab">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function changePage(delta) {
            currentPage += delta;
            renderTablePage();
        }

        // Selection handling
        function toggleSelectImage(relPath, isChecked) {
            if (isChecked) selectedSet.add(relPath);
            else selectedSet.delete(relPath);
            updateSelectionState();
        }

        function toggleSelectAllPage(masterCheck) {
            const isChecked = masterCheck.checked;
            const checkboxes = document.querySelectorAll('.img-row-check');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
                const rel = cb.dataset.rel;
                if (isChecked) selectedSet.add(rel);
                else selectedSet.delete(rel);
            });
            updateSelectionState();
        }

        function updateSelectionState() {
            const count = selectedSet.size;
            document.getElementById('selected_count_display').innerText = count.toLocaleString();
            document.querySelectorAll('.sel-count-label').forEach(el => el.innerText = count.toLocaleString());

            document.getElementById('btn_batch_variants').disabled = (count === 0);
            document.getElementById('btn_batch_compress').disabled = (count === 0);
        }

        // Single Image Actions
        async function generateVariantsSingle(relPath, id) {
            const rootKey = document.getElementById('filter_root').value;
            const statusCell = document.getElementById(`status_${id}`);
            if (statusCell) statusCell.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-slate-800"></i> Generating 3 WebP variants...';

            try {
                const res = await fetch('index.php?controller=imageoptimizer&action=generateVariantsSingle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ root: rootKey, file: relPath, force: true })
                });
                const data = await res.json();
                if (data.success) {
                    if (statusCell) statusCell.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> 4 Variants Created!</span>';
                    showToast('Generated Large, Tab, & Mobile variants!');
                    
                    // Update object in memory
                    const item = allImages.find(x => x.rel_path === relPath);
                    if (item) {
                        item.variants_status = data.variants_status;
                        renderTablePage();
                    }
                } else {
                    if (statusCell) statusCell.innerHTML = `<span class="text-rose-600 text-[11px] font-semibold">${data.error || 'Failed'}</span>`;
                }
            } catch (err) {
                if (statusCell) statusCell.innerHTML = '<span class="text-rose-600 text-[11px]">Error</span>';
            }
        }

        async function compressSingle(relPath, id, mode) {
            const rootKey = document.getElementById('filter_root').value;
            const maxDim = document.getElementById('config_max_dim').value;
            const statusCell = document.getElementById(`status_${id}`);
            if (statusCell) statusCell.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-slate-800"></i> Compressing...';

            try {
                const res = await fetch('index.php?controller=imageoptimizer&action=compressSingle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ root: rootKey, file: relPath, mode: mode, max_dimension: maxDim })
                });
                const data = await res.json();
                if (data.success) {
                    if (!data.unchanged) {
                        if (statusCell) statusCell.innerHTML = `<span class="text-emerald-700 font-bold text-[11px]"><i class="fa-solid fa-check"></i> ${data.message}</span>`;
                        showToast(data.message);
                    } else {
                        if (statusCell) statusCell.innerHTML = `<span class="text-slate-500 text-[11px]">${data.message}</span>`;
                        showToast(data.message);
                    }
                    const item = allImages.find(x => x.rel_path === relPath);
                    if (item) {
                        item.size = data.new_size;
                        item.size_fmt = data.new_size_fmt;
                        item.variants_status = data.variants_status;
                        renderTablePage();
                    }
                } else {
                    if (statusCell) statusCell.innerHTML = `<span class="text-rose-600 text-[11px]">${data.message || 'Error'}</span>`;
                }
            } catch (err) {
                if (statusCell) statusCell.innerHTML = '<span class="text-rose-600 text-[11px]">Error</span>';
            }
        }

        // Batch Action Runner
        async function runBatchAction(actionType, customFileList = null) {
            const rootKey = document.getElementById('filter_root').value;
            const maxDim = document.getElementById('config_max_dim').value;
            const list = customFileList || Array.from(selectedSet);

            if (list.length === 0) {
                alert('Please select at least one image to process.');
                return;
            }

            batchActive = true;
            batchPaused = false;
            batchStopRequested = false;
            batchTotalSavedBytes = 0;
            batchTotalVariantsCreated = 0;

            const progressCard = document.getElementById('batch_progress_card');
            progressCard.classList.remove('hidden');

            const actionLabel = (actionType === 'generate_variants') ? 'Generating Responsive Variants' : 'Compressing Images';
            document.getElementById('batch_title').innerText = `${actionLabel} (${list.length.toLocaleString()} total)`;
            document.getElementById('btn_batch_pause').innerHTML = '<i class="fa-solid fa-pause"></i> Pause';

            const chunkSize = 5;
            let processed = 0;

            for (let i = 0; i < list.length; i += chunkSize) {
                if (batchStopRequested) break;
                while (batchPaused) {
                    await new Promise(r => setTimeout(r, 400));
                    if (batchStopRequested) break;
                }
                if (batchStopRequested) break;

                const chunk = list.slice(i, i + chunkSize);
                try {
                    const res = await fetch('index.php?controller=imageoptimizer&action=batchAction', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            root: rootKey,
                            action: actionType,
                            files: chunk,
                            max_dimension: maxDim,
                            force: true
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        processed += chunk.length;
                        batchTotalVariantsCreated += (data.variants_created_count || 0);
                        batchTotalSavedBytes += (data.bytes_saved || 0);

                        // Update objects in memory
                        if (data.items) {
                            data.items.forEach(it => {
                                const found = allImages.find(x => x.rel_path === it.file);
                                if (found) {
                                    if (it.variants_status) found.variants_status = it.variants_status;
                                    if (it.new_size) {
                                        found.size = it.new_size;
                                        found.size_fmt = it.new_size_fmt;
                                    }
                                }
                            });
                        }
                    }
                } catch (err) {
                    console.error('Batch chunk failed', err);
                    processed += chunk.length;
                }

                // Update progress bar
                const pct = Math.round((processed / list.length) * 100);
                document.getElementById('batch_progress_bar').style.width = `${pct}%`;
                document.getElementById('batch_subtitle').innerText = `Completed ${processed} of ${list.length} images (${pct}%)`;
                document.getElementById('batch_stat_counts').innerText = `${processed} / ${list.length} processed`;
                document.getElementById('batch_stat_savings').innerText = `${batchTotalVariantsCreated} variants created • ${formatBytesJs(batchTotalSavedBytes)} saved`;

                renderTablePage();
            }

            batchActive = false;
            document.getElementById('batch_title').innerText = `Batch Complete!`;
            document.getElementById('batch_subtitle').innerText = `Successfully processed ${processed} images.`;
            showToast(`Batch completed: ${batchTotalVariantsCreated} variants created!`);
            setTimeout(() => { progressCard.classList.add('hidden'); }, 6000);
        }

        function startBatchForMissingVariants() {
            const missing = allImages.filter(x => !x.variants_status || !x.variants_status.has_all).map(x => x.rel_path);
            if (missing.length === 0) {
                alert('All images already have complete responsive variants!');
                return;
            }
            if (confirm(`Start batch variant generation for all ${missing.length} images needing variants?`)) {
                runBatchAction('generate_variants', missing);
            }
        }

        function startBatchCompressAllFiltered() {
            const targets = filteredImages.map(x => x.rel_path);
            if (targets.length === 0) {
                alert('No images in active filter to compress.');
                return;
            }
            if (confirm(`Start batch compression for all ${targets.length} filtered images?`)) {
                runBatchAction('compress_lossy', targets);
            }
        }

        function toggleBatchPause() {
            batchPaused = !batchPaused;
            document.getElementById('btn_batch_pause').innerHTML = batchPaused 
                ? '<i class="fa-solid fa-play"></i> Resume' 
                : '<i class="fa-solid fa-pause"></i> Pause';
        }

        function stopBatchProcessing() {
            batchStopRequested = true;
            batchPaused = false;
        }

        // Lightbox modal
        function openLightboxModal(url, caption) {
            document.getElementById('lightbox_img').src = url;
            document.getElementById('lightbox_caption').innerText = caption;
            document.getElementById('imageLightboxModal').classList.remove('hidden');
            document.getElementById('imageLightboxModal').classList.add('flex');
        }

        function closeLightboxModal() {
            document.getElementById('imageLightboxModal').classList.add('hidden');
            document.getElementById('imageLightboxModal').classList.remove('flex');
        }

        // CLI Guide modal
        function openCliGuideModal() {
            document.getElementById('cliGuideModal').classList.remove('hidden');
            document.getElementById('cliGuideModal').classList.add('flex');
        }

        function closeCliGuideModal() {
            document.getElementById('cliGuideModal').classList.add('hidden');
            document.getElementById('cliGuideModal').classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeLightboxModal();
                closeCliGuideModal();
            }
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function formatBytesJs(bytes) {
            if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return bytes + ' B';
        }
    </script>
</body>
</html>
