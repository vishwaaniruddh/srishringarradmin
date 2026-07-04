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
                    <!-- Instructions -->
                    <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-2xl shadow-sm">
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
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
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
    </script>
</body>
</html>
