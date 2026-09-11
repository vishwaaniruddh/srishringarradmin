<!DOCTYPE html>
<html lang="en">
<head>
    <title>Category Image Downloader - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Strict Shadcn UI Neutral / Slate Styling */
        body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }
        .shadcn-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03), 0 1px 2px -1px rgba(0, 0, 0, 0.03) !important;
        }
        .shadcn-btn-primary {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid #0f172a !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }
        .shadcn-btn-primary:hover:not(:disabled) {
            background-color: #1e293b !important;
            border-color: #1e293b !important;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.12) !important;
        }
        .shadcn-btn-primary:disabled {
            opacity: 0.55 !important;
            cursor: not-allowed !important;
        }
        .shadcn-btn-secondary {
            background-color: #ffffff !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }
        .shadcn-btn-secondary:hover:not(:disabled) {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        .shadcn-badge {
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
        }
        .shadcn-select {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            font-size: 14px !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }
        .shadcn-select:focus {
            outline: none !important;
            border-color: #0f172a !important;
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.08) !important;
        }
        .progress-bar-inner {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar (Untouched per instructions) -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Category Image Downloader';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto w-full">
                <!-- Header Banner -->
                <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h1 class="text-2xl font-bold tracking-tight text-[#0f172a]">Category Image Downloader</h1>
                            <span class="shadcn-badge">Tools</span>
                        </div>
                        <p class="text-sm text-[#64748b]">
                            Export all product images for any category into a single organized ZIP package.
                        </p>
                    </div>

                    <!-- Direct URL helper badge -->
                    <div class="hidden sm:flex items-center gap-2 text-xs text-[#64748b] bg-white border border-[#e2e8f0] px-3 py-2 rounded-lg">
                        <i class="fas fa-link text-[#94a3b8]"></i>
                        <span class="font-mono">index.php?controller=categoryImage</span>
                    </div>
                </div>

                <!-- Main Selector Card -->
                <div class="shadcn-card p-6 sm:p-8 mb-8">
                    <form id="downloadForm" onsubmit="event.preventDefault(); startDownloadProcess();">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                            <!-- Category Select -->
                            <div class="md:col-span-7 space-y-2">
                                <label for="categorySelect" class="block text-xs font-semibold uppercase tracking-wider text-[#475569]">
                                    Select Category <span class="text-red-500">*</span>
                                </label>
                                <select id="categorySelect" class="shadcn-select w-full" onchange="onCategoryChanged()">
                                    <option value="">-- Choose a Category --</option>
                                    
                                    <optgroup label="Apparel / Garments">
                                        <?php foreach ($apparelList as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat['key']) ?>" <?= $cat['key'] === 'garment:22' ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?> (<?= $cat['count'] ?> products)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>

                                    <optgroup label="Jewellery">
                                        <?php foreach ($jewelList as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat['key']) ?>">
                                                <?= htmlspecialchars($cat['name']) ?> (<?= $cat['count'] ?> products)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>

                                <p class="text-xs text-[#64748b] mt-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-[#94a3b8]"></i>
                                    Images are named strictly as <code class="font-mono bg-slate-100 text-[#0f172a] px-1 py-0.5 rounded text-[11px]">{sku}_1.jpg</code>, <code class="font-mono bg-slate-100 text-[#0f172a] px-1 py-0.5 rounded text-[11px]">{sku}_2.jpg</code>, etc.
                                </p>
                            </div>

                            <!-- Options & Actions -->
                            <div class="md:col-span-5 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#475569] mb-2">
                                        ZIP Directory Structure
                                    </label>
                                    <label class="flex items-start gap-2.5 text-xs text-[#334155] cursor-pointer select-none">
                                        <input type="checkbox" id="includeFolder" checked class="mt-0.5 rounded text-slate-900 focus:ring-slate-900 border-[#cbd5e1]">
                                        <span>
                                            <strong class="font-medium text-[#0f172a]" id="folderLabel">evening-gowns/</strong> folder in ZIP root
                                            <span class="block text-[11px] text-[#64748b]">Images are placed directly in this folder with no other subfolders inside.</span>
                                        </span>
                                    </label>
                                </div>

                                <div class="pt-2 flex flex-col sm:flex-row gap-2.5">
                                    <button type="submit" id="startBtn" class="shadcn-btn-primary flex-1 py-2.5 px-4 text-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-file-zipper text-sm"></i>
                                        <span>Download Images</span>
                                    </button>
                                    <button type="button" id="directBtn" onclick="triggerDirectDownload()" class="shadcn-btn-secondary py-2.5 px-4 text-sm flex items-center justify-center gap-2" title="Direct single-request download">
                                        <i class="fas fa-download text-xs text-[#64748b]"></i>
                                        <span class="hidden sm:inline">Direct</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Progress State Container (Hidden by default) -->
                <div id="progressCard" class="shadcn-card p-6 mb-8 hidden">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-800">
                                <i class="fas fa-spinner fa-spin text-sm" id="progressIcon"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-[#0f172a]" id="progressTitle">Packaging Images...</h3>
                                <p class="text-xs text-[#64748b]" id="progressSubtitle">Preparing download package</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-bold text-[#0f172a]" id="progressPercent">0%</span>
                            <span class="block text-[11px] text-[#64748b]" id="progressFraction">0 / 0</span>
                        </div>
                    </div>

                    <!-- Progress Bar Track -->
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mb-3 border border-[#e2e8f0]">
                        <div id="progressBar" class="progress-bar-inner bg-[#0f172a] h-2.5 rounded-full" style="width: 0%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-[#64748b]">
                        <span id="progressCurrent">Current SKU: <strong class="text-[#0f172a] font-mono">--</strong></span>
                        <span id="progressTimer">Elapsed: 0s</span>
                    </div>
                </div>

                <!-- Statistics & Information Grid -->
                <div id="infoSection" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <!-- Stat Card 1 -->
                    <div class="shadcn-card p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">Selected Category</span>
                            <div class="w-7 h-7 rounded-md bg-slate-100 flex items-center justify-center text-slate-600">
                                <i class="fas fa-tag text-xs"></i>
                            </div>
                        </div>
                        <div class="text-xl font-bold text-[#0f172a] truncate" id="statCategoryName">Evening Gowns</div>
                        <span class="text-xs font-mono text-[#64748b]" id="statCategorySlug">evening-gowns.zip</span>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="shadcn-card p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">Total Products</span>
                            <div class="w-7 h-7 rounded-md bg-slate-100 flex items-center justify-center text-slate-600">
                                <i class="fas fa-boxes-stacked text-xs"></i>
                            </div>
                        </div>
                        <div class="text-xl font-bold text-[#0f172a]" id="statProductsCount">--</div>
                        <span class="text-xs text-[#64748b]">Distinct catalog SKUs</span>
                    </div>

                    <!-- Stat Card 3 -->
                    <div class="shadcn-card p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#64748b]">Total Images</span>
                            <div class="w-7 h-7 rounded-md bg-slate-100 flex items-center justify-center text-slate-600">
                                <i class="fas fa-images text-xs"></i>
                            </div>
                        </div>
                        <div class="text-xl font-bold text-[#0f172a]" id="statImagesCount">--</div>
                        <span class="text-xs text-[#64748b]">Files in output ZIP</span>
                    </div>
                </div>

                <!-- Preview Grid Card -->
                <div class="shadcn-card p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#e2e8f0]">
                        <div>
                            <h3 class="text-sm font-semibold text-[#0f172a]">Category Sample Preview</h3>
                            <p class="text-xs text-[#64748b]">Sample products and their ranked image counts</p>
                        </div>
                        <span class="shadcn-badge" id="previewBadge">Showing first items</span>
                    </div>

                    <div id="previewGrid" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3">
                        <!-- Loaded dynamically via AJAX -->
                        <div class="col-span-full py-8 text-center text-xs text-[#94a3b8]">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Loading preview...
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript Controller Logic -->
    <script>
        let currentCategorySlug = 'evening-gowns';
        let isDownloading = false;
        let timerInterval = null;
        let startTime = 0;

        document.addEventListener('DOMContentLoaded', () => {
            onCategoryChanged();
        });

        async function onCategoryChanged() {
            const select = document.getElementById('categorySelect');
            const categoryKey = select.value;
            if (!categoryKey) {
                return;
            }

            // Update folder label
            const selectedText = select.options[select.selectedIndex].text.split('(')[0].trim();
            const slug = slugify(selectedText);
            currentCategorySlug = slug;
            document.getElementById('folderLabel').innerText = slug + '/';
            document.getElementById('statCategoryName').innerText = selectedText;
            document.getElementById('statCategorySlug').innerText = slug + '.zip';

            // Fetch info from backend
            document.getElementById('statProductsCount').innerText = '...';
            document.getElementById('statImagesCount').innerText = '...';
            document.getElementById('previewGrid').innerHTML = '<div class="col-span-full py-6 text-center text-xs text-[#94a3b8]"><i class="fas fa-spinner fa-spin mr-1"></i> Fetching details...</div>';

            try {
                const res = await fetch(`index.php?controller=categoryImage&action=info&category=${encodeURIComponent(categoryKey)}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('statProductsCount').innerText = Number(data.total_products).toLocaleString();
                    document.getElementById('statImagesCount').innerText = Number(data.total_images).toLocaleString();
                    currentCategorySlug = data.slug;
                    document.getElementById('statCategorySlug').innerText = data.slug + '.zip';

                    renderPreview(data.preview_products || []);
                } else {
                    document.getElementById('statProductsCount').innerText = '0';
                    document.getElementById('statImagesCount').innerText = '0';
                    document.getElementById('previewGrid').innerHTML = `<div class="col-span-full py-4 text-center text-xs text-red-500">${data.message || 'Error'}</div>`;
                }
            } catch (err) {
                console.error(err);
                document.getElementById('statProductsCount').innerText = 'Error';
                document.getElementById('statImagesCount').innerText = 'Error';
            }
        }

        function renderPreview(products) {
            const container = document.getElementById('previewGrid');
            if (!products.length) {
                container.innerHTML = '<div class="col-span-full py-6 text-center text-xs text-[#94a3b8]">No products with images found in this category.</div>';
                return;
            }

            container.innerHTML = products.map(p => {
                const thumb = p.thumbnail || 'assets/default-product.jpg';
                return `
                    <div class="group border border-[#e2e8f0] rounded-lg p-2 bg-white hover:border-[#cbd5e1] transition-all text-center">
                        <div class="aspect-square bg-slate-50 rounded-md overflow-hidden mb-1.5 flex items-center justify-center relative">
                            <img src="${thumb}" 
                                 class="w-full h-full object-contain p-1 group-hover:scale-105 transition-transform" 
                                 alt="${p.sku}"
                                 onerror="this.onerror=null; this.src='https://srishringarr.com/static/images/default.jpg';">
                            <span class="absolute bottom-1 right-1 bg-black/75 text-white text-[10px] font-mono px-1 py-0.2 rounded">
                                ${p.image_count} img
                            </span>
                        </div>
                        <div class="text-[11px] font-mono font-bold text-[#0f172a] truncate" title="${p.sku}">
                            ${p.sku}
                        </div>
                        <div class="text-[10px] text-[#64748b] truncate" title="${p.name}">
                            ${p.name}
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function startDownloadProcess() {
            if (isDownloading) return;

            const categoryKey = document.getElementById('categorySelect').value;
            if (!categoryKey) {
                alert('Please select a category first.');
                return;
            }

            const includeFolder = document.getElementById('includeFolder').checked ? 1 : 0;
            const startBtn = document.getElementById('startBtn');
            const progressCard = document.getElementById('progressCard');

            isDownloading = true;
            startBtn.disabled = true;
            progressCard.classList.remove('hidden');

            updateProgressUI({
                title: 'Initializing ZIP Package...',
                subtitle: 'Gathers all SKUs and assigning {sku}_1, {sku}_2 filenames...',
                percent: 0,
                fraction: '0 / 0',
                sku: 'Starting'
            });

            startTimer();

            try {
                // Step 1: Initialize Download Session
                const formData = new FormData();
                formData.append('category', categoryKey);
                formData.append('include_folder', includeFolder);

                const initRes = await fetch('index.php?controller=categoryImage&action=initDownload', {
                    method: 'POST',
                    body: formData
                });
                const initData = await initRes.json();

                if (!initData.success) {
                    throw new Error(initData.message || 'Failed to initialize download.');
                }

                const sessionId = initData.session_id;
                const totalImages = initData.total_images;
                const batchSize = 25; // Fetch 25 images concurrently
                let offset = 0;

                updateProgressUI({
                    title: `Downloading ${initData.category_name} Images...`,
                    subtitle: `Total ${totalImages.toLocaleString()} images to package into ${initData.slug}.zip`,
                    percent: 0,
                    fraction: `0 / ${totalImages.toLocaleString()}`,
                    sku: 'Initializing...'
                });

                // Step 2: Loop through batches
                while (offset < totalImages) {
                    const batchFormData = new FormData();
                    batchFormData.append('session_id', sessionId);
                    batchFormData.append('offset', offset);
                    batchFormData.append('limit', batchSize);

                    const batchRes = await fetch('index.php?controller=categoryImage&action=processBatch', {
                        method: 'POST',
                        body: batchFormData
                    });
                    const batchData = await batchRes.json();

                    if (!batchData.success) {
                        throw new Error(batchData.message || 'Batch processing failed.');
                    }

                    offset = batchData.offset;
                    const percent = batchData.percent;
                    const curSku = batchData.current_sku || '--';

                    updateProgressUI({
                        title: `Packaging Images: ${percent}%`,
                        subtitle: `Adding ${curSku} images to ZIP...`,
                        percent: percent,
                        fraction: `${offset.toLocaleString()} / ${totalImages.toLocaleString()}`,
                        sku: curSku
                    });

                    if (batchData.completed || offset >= totalImages) {
                        break;
                    }
                }

                // Step 3: Trigger Browser File Download
                stopTimer();
                updateProgressUI({
                    title: 'Download Ready!',
                    subtitle: 'Triggering file download to your computer...',
                    percent: 100,
                    fraction: `${totalImages.toLocaleString()} / ${totalImages.toLocaleString()}`,
                    sku: 'Complete',
                    completed: true
                });

                // Direct download trigger
                const downloadUrl = `index.php?controller=categoryImage&action=downloadZip&session_id=${encodeURIComponent(sessionId)}`;
                window.location.href = downloadUrl;

            } catch (err) {
                console.error(err);
                stopTimer();
                alert('Error downloading images: ' + err.message);
                updateProgressUI({
                    title: 'Error Occurred',
                    subtitle: err.message,
                    percent: 0,
                    fraction: 'Failed',
                    sku: 'Error',
                    error: true
                });
            } finally {
                isDownloading = false;
                startBtn.disabled = false;
            }
        }

        function triggerDirectDownload() {
            const categoryKey = document.getElementById('categorySelect').value;
            if (!categoryKey) {
                alert('Please select a category first.');
                return;
            }
            const includeFolder = document.getElementById('includeFolder').checked ? 1 : 0;
            const url = `index.php?controller=categoryImage&action=directZip&category=${encodeURIComponent(categoryKey)}&include_folder=${includeFolder}`;
            window.open(url, '_blank');
        }

        function updateProgressUI({ title, subtitle, percent, fraction, sku, completed = false, error = false }) {
            document.getElementById('progressTitle').innerText = title;
            document.getElementById('progressSubtitle').innerText = subtitle;
            document.getElementById('progressPercent').innerText = `${percent}%`;
            document.getElementById('progressFraction').innerText = fraction;
            document.getElementById('progressBar').style.width = `${percent}%`;
            document.getElementById('progressCurrent').innerHTML = `Current SKU: <strong class="text-[#0f172a] font-mono">${sku}</strong>`;

            const icon = document.getElementById('progressIcon');
            if (completed) {
                icon.className = 'fas fa-check text-green-600';
            } else if (error) {
                icon.className = 'fas fa-exclamation-triangle text-red-600';
            } else {
                icon.className = 'fas fa-spinner fa-spin text-slate-800';
            }
        }

        function startTimer() {
            startTime = Date.now();
            timerInterval = setInterval(() => {
                const sec = Math.floor((Date.now() - startTime) / 1000);
                document.getElementById('progressTimer').innerText = `Elapsed: ${sec}s`;
            }, 1000);
        }

        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        function slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    </script>
</body>
</html>
