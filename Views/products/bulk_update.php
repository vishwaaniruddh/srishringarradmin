<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bulk Update Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
         <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Bulk Update Products';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-4xl mx-auto">
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200 mb-8 bg-white p-2 rounded-2xl shadow-sm border border-gray-100/50">
                        <button onclick="switchTab('properties')" id="tab-properties" class="flex-1 py-3 text-center font-bold text-sm rounded-xl text-primary bg-primary/5 transition-all cursor-pointer">
                            <i class="fas fa-edit mr-2"></i>Update Properties
                        </button>
                        <button onclick="switchTab('images')" id="tab-images" class="flex-1 py-3 text-center font-bold text-sm rounded-xl text-gray-400 hover:text-gray-600 transition-all cursor-pointer">
                            <i class="fas fa-images mr-2"></i>Upload SKU Images Folder
                        </button>
                    </div>

                    <!-- TAB 1: PROPERTIES UPDATE -->
                    <div id="properties-section" class="space-y-6">
                        <!-- Instructions -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-2xl shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-bold text-blue-800">Instructions</h3>
                                    <div class="mt-2 text-sm text-blue-700 space-y-1">
                                        <p>1. Paste your list of product SKU codes into the text area below (one per line, space-separated, or comma-separated).</p>
                                        <p>2. Select the actions you want to apply to these SKUs. Choose "Keep Unchanged" for settings you don't want to modify.</p>
                                        <p>3. Click <b>Update Products in Bulk</b>. The system will search and apply modifications instantly.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                            <div class="p-8">
                                <form id="bulkUpdateForm" onsubmit="handleBulkUpdate(event)" class="space-y-6">
                                    <!-- SKUs Input -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700 ml-1">SKU Codes</label>
                                        <textarea name="skus" id="skusInput" required rows="6" 
                                                  placeholder="Enter SKU codes here...&#10;e.g.&#10;FM4025-3&#10;FM4116-3&#10;FM4539-1" 
                                                  class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm font-mono focus:ring-primary focus:border-primary focus:bg-white transition-all"></textarea>
                                        <p class="text-xs text-gray-400 ml-1">Supports one SKU per line, comma-separated, or space-separated lists.</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Price Source Option -->
                                        <div class="space-y-2 bg-gray-50/50 border border-gray-100 rounded-2xl p-5">
                                            <label class="block text-sm font-bold text-gray-700">1. Update Pricing Logic</label>
                                            <p class="text-xs text-gray-400 mb-4">Choose where these products should read their prices from.</p>
                                            <select name="price_source" id="priceSourceSelect" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                <option value="no_change">Keep Unchanged</option>
                                                <option value="pos">Use POS Database (Calculated)</option>
                                                <option value="manual">Use Primary Database (Manual override)</option>
                                            </select>
                                        </div>

                                        <!-- Availability Option -->
                                        <div class="space-y-2 bg-gray-50/50 border border-gray-100 rounded-2xl p-5">
                                            <label class="block text-sm font-bold text-gray-700">2. Update Availability Nature</label>
                                            <p class="text-xs text-gray-400 mb-4">Choose the availability mode for these products.</p>
                                            <select name="availability" id="availabilitySelect" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                <option value="no_change">Keep Unchanged</option>
                                                <option value="both">Rent & Sell (Both)</option>
                                                <option value="rent">Rent Only</option>
                                                <option value="sell">Sell Only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="progressArea" class="hidden space-y-4 pt-4">
                                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div id="progressBar" class="h-full bg-amber-500 w-0 transition-all duration-300"></div>
                                        </div>
                                        <p id="statusMsg" class="text-center text-sm font-medium text-gray-600 italic">Processing updates...</p>
                                    </div>

                                    <button type="submit" id="submitBtn" class="w-full bg-gray-900 text-white rounded-2xl py-4 font-bold hover:bg-gray-800 transition-all shadow-xl shadow-gray-200 flex items-center justify-center space-x-2 cursor-pointer">
                                        <i class="fas fa-magic"></i>
                                        <span>Update Products in Bulk</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Results Area -->
                        <div id="resultsArea" class="mt-8 hidden space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Updated successfully</p>
                                    <p id="updatedCount" class="text-3xl font-black text-green-500">0</p>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Not Found</p>
                                    <p id="notFoundCount" class="text-3xl font-black text-amber-500">0</p>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Errors</p>
                                    <p id="errorCount" class="text-3xl font-black text-red-500">0</p>
                                </div>
                            </div>

                            <!-- Missing SKUs -->
                            <div id="missingSkuContainer" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hidden">
                                <div class="px-6 py-4 bg-amber-50/50 border-b border-gray-100">
                                    <h4 class="font-bold text-amber-800 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>SKUs Not Found</h4>
                                </div>
                                <div id="missingSkuList" class="p-6 font-mono text-xs text-amber-700 grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <!-- list -->
                                </div>
                            </div>

                            <!-- Errors Log -->
                            <div id="errorsContainer" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hidden">
                                <div class="px-6 py-4 bg-red-50 border-b border-gray-100">
                                    <h4 class="font-bold text-red-800 text-sm"><i class="fas fa-times-circle mr-2"></i>Errors Log</h4>
                                </div>
                                <div id="errorsList" class="p-6 font-mono text-xs text-red-700 space-y-1">
                                    <!-- list -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: IMAGES FOLDER UPLOAD -->
                    <div id="images-section" class="space-y-6 hidden">
                        <!-- Instructions -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-2xl shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-bold text-blue-800">SKU Folder Upload Instructions</h3>
                                    <div class="mt-2 text-sm text-blue-700 space-y-1">
                                        <p>1. Make sure your local folder (e.g. <b>FM-images</b>) has subfolders named exactly after product SKU codes (e.g. <i>FM4025-3</i>, <i>FM4116-3</i>).</p>
                                        <p>2. Keep the respective product images inside these SKU subfolders.</p>
                                        <p>3. Select the parent folder. The browser will read all images, group them by SKU, and upload them sequentially.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dropzone Container -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden p-8 space-y-6">
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center hover:border-primary hover:bg-primary/5 transition-all cursor-pointer relative group">
                                <input type="file" id="folderInput" webkitdirectory directory multiple class="hidden" onchange="handleFolderSelect(event)">
                                <div onclick="document.getElementById('folderInput').click()">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-folder-open text-2xl text-gray-400 group-hover:text-primary"></i>
                                    </div>
                                    <p class="text-gray-600 font-bold mb-1" id="folderLabel">Click to select source folder (e.g. FM-images)</p>
                                    <p class="text-gray-400 text-xs">Supports selecting a parent directory containing SKU subfolders</p>
                                </div>
                            </div>

                            <!-- Batch stats preview -->
                            <div id="imageUploadStats" class="hidden bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="font-bold text-gray-700 text-sm mb-4">Detected Batch</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Total SKU Folders</span>
                                        <span id="detectedSkusCount" class="text-2xl font-black text-gray-800">0</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Total Image Files</span>
                                        <span id="detectedFilesCount" class="text-2xl font-black text-gray-800">0</span>
                                    </div>
                                </div>
                                
                                <button id="startImageUploadBtn" onclick="startFolderUpload()" class="mt-6 w-full bg-gray-900 text-white rounded-2xl py-4 font-bold hover:bg-gray-800 transition-all shadow-md flex items-center justify-center space-x-2 cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Start Uploading Images</span>
                                </button>
                            </div>

                            <!-- Progress log -->
                            <div id="folderProgressArea" class="hidden space-y-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between text-xs font-semibold text-gray-600">
                                    <span id="folderProgressStatus">Uploading...</span>
                                    <span id="folderProgressPercent">0%</span>
                                </div>
                                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div id="folderProgressBar" class="h-full bg-primary w-0 transition-all duration-300"></div>
                                </div>
                                <div id="folderUploadLog" class="bg-gray-950 text-green-400 font-mono text-[11px] p-4 rounded-2xl max-h-60 overflow-y-auto space-y-1 shadow-inner">
                                    <!-- Logs -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    // TAB SWITCH LOGIC
    function switchTab(tab) {
        const propTab = document.getElementById('tab-properties');
        const imgTab = document.getElementById('tab-images');
        const propSec = document.getElementById('properties-section');
        const imgSec = document.getElementById('images-section');

        if (tab === 'properties') {
            propTab.classList.add('text-primary', 'bg-primary/5');
            propTab.classList.remove('text-gray-400', 'hover:text-gray-600');
            imgTab.classList.remove('text-primary', 'bg-primary/5');
            imgTab.classList.add('text-gray-400', 'hover:text-gray-600');
            propSec.classList.remove('hidden');
            imgSec.classList.add('hidden');
        } else {
            imgTab.classList.add('text-primary', 'bg-primary/5');
            imgTab.classList.remove('text-gray-400', 'hover:text-gray-600');
            propTab.classList.remove('text-primary', 'bg-primary/5');
            propTab.classList.add('text-gray-400', 'hover:text-gray-600');
            imgSec.classList.remove('hidden');
            propSec.classList.add('hidden');
        }
    }

    // PROPERTIES BATCH UPDATE LOGIC
    async function handleBulkUpdate(e) {
        e.preventDefault();
        
        const skus = document.getElementById('skusInput').value.trim();
        const priceSource = document.getElementById('priceSourceSelect').value;
        const availability = document.getElementById('availabilitySelect').value;

        if (!skus) {
            alert('Please enter at least one SKU');
            return;
        }

        if (priceSource === 'no_change' && availability === 'no_change') {
            alert('Please select at least one action to perform (Pricing Logic or Availability)');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        const progressArea = document.getElementById('progressArea');
        const progressBar = document.getElementById('progressBar');
        const statusMsg = document.getElementById('statusMsg');
        const resultsArea = document.getElementById('resultsArea');
        
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        progressArea.classList.remove('hidden');
        resultsArea.classList.add('hidden');
        
        progressBar.style.width = '40%';
        statusMsg.innerText = 'Sending batch data...';

        try {
            const response = await fetch('index.php?controller=product&action=processBulkUpdate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    skus,
                    price_source: priceSource,
                    availability
                })
            });

            const data = await response.json();
            progressBar.style.width = '100%';

            if (data.success) {
                statusMsg.innerText = 'Batch completed!';
                setTimeout(() => {
                    progressArea.classList.add('hidden');
                    resultsArea.classList.remove('hidden');

                    document.getElementById('updatedCount').innerText = data.updatedCount;
                    document.getElementById('notFoundCount').innerText = data.notFoundCount;
                    document.getElementById('errorCount').innerText = data.errors ? data.errors.length : 0;

                    // Display Missing SKUs
                    const missingContainer = document.getElementById('missingSkuContainer');
                    if (data.notFoundCount > 0) {
                        missingContainer.classList.remove('hidden');
                        document.getElementById('missingSkuList').innerHTML = data.notFoundSkus.map(sku => `
                            <div class="bg-amber-50 px-2.5 py-1.5 rounded border border-amber-200 text-center">${sku}</div>
                        `).join('');
                    } else {
                        missingContainer.classList.add('hidden');
                    }

                    // Display Errors Log
                    const errorsContainer = document.getElementById('errorsContainer');
                    if (data.errors && data.errors.length > 0) {
                        errorsContainer.classList.remove('hidden');
                        document.getElementById('errorsList').innerHTML = data.errors.map(err => `
                            <div class="py-1 border-b border-red-50/50">${err}</div>
                        `).join('');
                    } else {
                        errorsContainer.classList.add('hidden');
                    }
                }, 500);
            } else {
                statusMsg.innerText = 'Error: ' + (data.error || 'Failed to complete bulk update');
                statusMsg.classList.add('text-red-500');
                progressBar.classList.add('bg-red-500');
            }
        } catch (error) {
            console.error('Error:', error);
            statusMsg.innerText = 'A network error occurred. Please try again.';
            statusMsg.classList.add('text-red-500');
            progressBar.classList.add('bg-red-500');
        } finally {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // DIRECTORY SCAN & UPLOAD LOGIC
    let selectedSkuGroups = {};

    function handleFolderSelect(e) {
        const files = e.target.files;
        if (!files.length) return;

        selectedSkuGroups = {};
        let totalFiles = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const relativePath = file.webkitRelativePath;
            if (!relativePath) continue;

            const parts = relativePath.split('/');
            // FM-images/FM4025-3/photo.jpg -> length 3, parent is FM4025-3
            if (parts.length < 3) continue;

            const sku = parts[parts.length - 2].trim();
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext)) continue;

            if (!selectedSkuGroups[sku]) {
                selectedSkuGroups[sku] = [];
            }
            selectedSkuGroups[sku].push(file);
            totalFiles++;
        }

        const skuKeys = Object.keys(selectedSkuGroups);
        if (skuKeys.length === 0) {
            alert("No valid product images found in the selected folder. Please verify the folder structure.");
            return;
        }

        // Show folder name
        const folderName = files[0].webkitRelativePath.split('/')[0];
        document.getElementById('folderLabel').innerHTML = `<span class="text-primary font-bold">${folderName}</span> folder selected`;

        document.getElementById('imageUploadStats').classList.remove('hidden');
        document.getElementById('detectedSkusCount').innerText = skuKeys.length;
        document.getElementById('detectedFilesCount').innerText = totalFiles;
    }

    async function startFolderUpload() {
        const skuKeys = Object.keys(selectedSkuGroups);
        if (!skuKeys.length) return;

        const startBtn = document.getElementById('startImageUploadBtn');
        const progressArea = document.getElementById('folderProgressArea');
        const progressBar = document.getElementById('folderProgressBar');
        const progressStatus = document.getElementById('folderProgressStatus');
        const progressPercent = document.getElementById('folderProgressPercent');
        const uploadLog = document.getElementById('folderUploadLog');

        startBtn.disabled = true;
        startBtn.classList.add('opacity-50', 'cursor-not-allowed');
        progressArea.classList.remove('hidden');
        uploadLog.innerHTML = `<div class="text-zinc-500">// Starting batch upload for ${skuKeys.length} SKUs...</div>`;

        let successCount = 0;
        let failedCount = 0;

        for (let i = 0; i < skuKeys.length; i++) {
            const sku = skuKeys[i];
            const files = selectedSkuGroups[sku];

            progressStatus.innerText = `Uploading SKU ${sku} (${i+1}/${skuKeys.length})...`;
            const pct = Math.round((i / skuKeys.length) * 100);
            progressBar.style.width = pct + '%';
            progressPercent.innerText = pct + '%';

            // Create formData
            const formData = new FormData();
            formData.append('sku', sku);
            for (let f = 0; f < files.length; f++) {
                formData.append('files[]', files[f]);
            }

            try {
                const response = await fetch('index.php?controller=product&action=uploadSkuImages', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (response.ok && data.success) {
                    successCount++;
                    uploadLog.innerHTML += `<div class="text-emerald-400"><i class="fas fa-check-circle mr-1.5"></i>SKU ${sku}: Uploaded ${files.length} images successfully.</div>`;
                } else {
                    failedCount++;
                    uploadLog.innerHTML += `<div class="text-red-400"><i class="fas fa-times-circle mr-1.5"></i>SKU ${sku}: ${data.error || 'Unknown error'}</div>`;
                }
            } catch (err) {
                failedCount++;
                uploadLog.innerHTML += `<div class="text-red-400"><i class="fas fa-times-circle mr-1.5"></i>SKU ${sku}: Network request failed</div>`;
            }

            uploadLog.scrollTop = uploadLog.scrollHeight;
        }

        progressBar.style.width = '100%';
        progressPercent.innerText = '100%';
        progressStatus.innerText = 'Batch completed!';
        uploadLog.innerHTML += `<div class="text-white mt-2 font-bold">// Done! ${successCount} SKUs updated, ${failedCount} SKUs failed.</div>`;
        startBtn.disabled = false;
        startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    </script>
</body>
</html>
