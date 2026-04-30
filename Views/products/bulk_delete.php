<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bulk Delete Products - Srishringarr</title>
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
            $pageTitle = 'Bulk Delete Products';
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
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>1. Download the CSV template using the button below.</p>
                                    <p>2. Fill in the <b>sku</b> column with the product codes you want to delete.</p>
                                    <p>3. Upload the file. Products will be deleted <b>only from the local database</b>.</p>
                                    <p class="mt-2 font-bold text-red-600 underline">Warning: This action is permanent and cannot be undone!</p>
                                </div>
                                <div class="mt-4">
                                    <a href="index.php?controller=product&action=downloadDeleteTemplate" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl text-blue-700 bg-blue-100 hover:bg-blue-200 transition-all">
                                        <i class="fas fa-download mr-2"></i> Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <form id="bulkDeleteForm" onsubmit="handleBulkDelete(event)" class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700 ml-1">Select CSV File</label>
                                    <div id="dropzone" class="relative group cursor-pointer">
                                        <input type="file" id="fileInput" name="file" accept=".csv" class="hidden" onchange="updateFileName(this)">
                                        <div onclick="document.getElementById('fileInput').click()" class="border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center group-hover:border-primary transition-all bg-gray-50/50 group-hover:bg-primary/5">
                                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 group-hover:text-primary"></i>
                                            </div>
                                            <p class="text-gray-600 font-medium mb-1" id="fileLabel">Click to upload or drag and drop</p>
                                            <p class="text-gray-400 text-xs">CSV files only</p>
                                        </div>
                                    </div>
                                </div>

                                <div id="progressArea" class="hidden space-y-4">
                                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                        <div id="progressBar" class="h-full bg-primary w-0 transition-all duration-300"></div>
                                    </div>
                                    <p id="statusMsg" class="text-center text-sm font-medium text-gray-600 italic">Processing...</p>
                                </div>

                                <button type="submit" id="submitBtn" class="w-full bg-gray-900 text-white rounded-2xl py-4 font-bold hover:bg-gray-800 transition-all shadow-xl shadow-gray-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>Delete Products in Bulk</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Results Area -->
                    <div id="resultsArea" class="mt-8 hidden space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Deleted</p>
                                <p id="deletedCount" class="text-3xl font-black text-green-500">0</p>
                            </div>
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Failed/Not Found</p>
                                <p id="failedCount" class="text-3xl font-black text-gray-300">0</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="font-bold text-gray-700 text-sm">Processed SKUs</h4>
                                <span class="text-xs text-gray-400" id="processedTimestamp"></span>
                            </div>
                            <div id="skuList" class="p-6 max-h-64 overflow-y-auto font-mono text-xs text-gray-600 grid grid-cols-2 md:grid-cols-4 gap-2">
                                <!-- SKUs will be listed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    function updateFileName(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files[0]) {
            label.innerHTML = `<span class="text-primary font-bold">${input.files[0].name}</span> selected`;
        }
    }

    async function handleBulkDelete(e) {
        e.preventDefault();
        const form = e.target;
        const fileInput = document.getElementById('fileInput');
        
        if (!fileInput.files[0]) {
            alert('Please select a CSV file first');
            return;
        }

        if (!confirm('Are you absolutely sure you want to delete these products? This cannot be undone.')) {
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        const submitBtn = document.getElementById('submitBtn');
        const progressArea = document.getElementById('progressArea');
        const progressBar = document.getElementById('progressBar');
        const statusMsg = document.getElementById('statusMsg');
        const resultsArea = document.getElementById('resultsArea');
        
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        progressArea.classList.remove('hidden');
        resultsArea.classList.add('hidden');
        
        progressBar.style.width = '30%';
        statusMsg.innerText = 'Uploading and parsing file...';

        try {
            const response = await fetch('index.php?controller=product&action=processBulkDelete', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            progressBar.style.width = '100%';
            
            if (data.success) {
                statusMsg.innerText = 'Complete!';
                setTimeout(() => {
                    progressArea.classList.add('hidden');
                    resultsArea.classList.remove('hidden');
                    document.getElementById('deletedCount').innerText = data.deletedCount;
                    document.getElementById('failedCount').innerText = data.failedCount;
                    document.getElementById('processedTimestamp').innerText = new Date().toLocaleTimeString();
                    
                    const skuList = document.getElementById('skuList');
                    skuList.innerHTML = data.skus.map(sku => `
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>${sku}</span>
                        </div>
                    `).join('');
                }, 500);
            } else {
                statusMsg.innerText = 'Error: ' + (data.error || data.message);
                statusMsg.classList.add('text-red-500');
                progressBar.classList.add('bg-red-500');
            }
        } catch (error) {
            console.error('Error:', error);
            statusMsg.innerText = 'A network error occurred. Please try again.';
            statusMsg.classList.add('text-red-500');
        } finally {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    </script>
</body>
</html>
