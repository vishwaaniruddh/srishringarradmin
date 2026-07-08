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
                        <button onclick="switchTab('prices')" id="tab-prices" class="flex-1 py-3 text-center font-bold text-sm rounded-xl text-gray-400 hover:text-gray-600 transition-all cursor-pointer">
                            <i class="fas fa-tags mr-2"></i>Update Prices
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

                                        <!-- Brand Name Option -->
                                        <div class="space-y-2 bg-gray-50/50 border border-gray-100 rounded-2xl p-5 md:col-span-2">
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-sm font-bold text-gray-700">3. Update Brand Name</label>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" id="updateBrandCheckbox" class="sr-only peer" onchange="toggleBrandInput(this.checked)">
                                                    <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-focus:outline-none peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                                    <span class="ml-2 text-xs font-semibold text-gray-500">Enable</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-400 mb-2">Check the toggle above to enable and write the new brand name for these products.</p>
                                            <input type="text" id="brandNameInput" placeholder="Enter Brand Name (e.g. Deepmala, HER CLOSET)" disabled class="w-full bg-gray-100 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary transition-all">
                                        </div>

                                        <!-- Category Option -->
                                        <div class="space-y-2 bg-gray-50/50 border border-gray-100 rounded-2xl p-5 md:col-span-2">
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-sm font-bold text-gray-700">4. Update Category</label>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" id="updateCategoryCheckbox" class="sr-only peer" onchange="toggleCategoryInput(this.checked)">
                                                    <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-focus:outline-none peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                                    <span class="ml-2 text-xs font-semibold text-gray-500">Enable</span>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-400 mb-4">Choose the product type first, then choose the category and subcategory to assign.</p>
                                            
                                            <div id="categoryFieldsContainer" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Product Type</label>
                                                    <select id="categoryTypeSelect" onchange="loadCategoryDropdowns(this.value)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                        <option value="">Select Type</option>
                                                        <option value="jewellery">Jewellery</option>
                                                        <option value="garments">Garments</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                                                    <select id="categorySelect" onchange="loadSubcategories(this.value)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" disabled>
                                                        <option value="">Select Category</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subcategory</label>
                                                    <select id="subcategorySelect" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" disabled>
                                                        <option value="">Select Subcategory</option>
                                                    </select>
                                                </div>
                                            </div>
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

                    <!-- TAB 2: PRICES UPDATE -->
                    <div id="prices-section" class="space-y-6 hidden">
                        <!-- Instructions -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-2xl shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-bold text-blue-800">Update Prices Instructions</h3>
                                    <div class="mt-2 text-sm text-blue-700 space-y-1">
                                        <p>You can update product MRP, Rent, and Deposit prices in bulk using two methods:</p>
                                        <p class="font-semibold mt-2">Method 1: Paste Excel Data</p>
                                        <p>Copy rows directly from your Excel sheet (including columns: SKU, MRP, Rent, Deposit) and paste them in the text area.</p>
                                        <p class="font-semibold mt-2">Method 2: Upload Excel/CSV File</p>
                                        <p>Upload an Excel (.xlsx, .xls) or CSV file. The system will look for column headers like <b>wid/sku</b>, <b>mrp</b>, <b>rental+gst/rent</b>, and <b>sd/deposit</b>.</p>
                                        <p class="text-xs text-blue-600 mt-2 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i>Note: Performing this update will automatically switch the updated products to "Manual" pricing logic so these manual prices take effect on the website.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Update Form -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                            <div class="p-8">
                                <form id="bulkPriceUpdateForm" onsubmit="handleBulkPriceUpdate(event)" class="space-y-6" enctype="multipart/form-data">
                                    <!-- Input Mode Selector -->
                                    <div class="flex space-x-6 border-b border-gray-100 pb-4 mb-4">
                                        <label class="flex items-center space-x-2 cursor-pointer font-semibold text-gray-700 text-sm">
                                            <input type="radio" name="price_input_mode" value="paste" checked onchange="togglePriceInputMode('paste')" class="text-primary focus:ring-primary h-4 w-4">
                                            <span>Paste Excel Data</span>
                                        </label>
                                        <label class="flex items-center space-x-2 cursor-pointer font-semibold text-gray-700 text-sm">
                                            <input type="radio" name="price_input_mode" value="file" onchange="togglePriceInputMode('file')" class="text-primary focus:ring-primary h-4 w-4">
                                            <span>Upload Excel/CSV File</span>
                                        </label>
                                    </div>

                                    <!-- Text Area Input -->
                                    <div id="pricePasteContainer" class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700 ml-1">Paste Price Data (SKU, MRP, Rent, Deposit)</label>
                                        <textarea name="price_data" id="priceDataInput" rows="8" 
                                                  placeholder="Paste columns from Excel here...&#10;e.g.&#10;FM4136-1	75000	18880	12000&#10;FM4160-1	32000	8260	6000" 
                                                  class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-sm font-mono focus:ring-primary focus:border-primary focus:bg-white transition-all"></textarea>
                                        <p class="text-xs text-gray-400 ml-1">Paste tabular data copied from Excel. It expects columns in the order: SKU, MRP, Rent, Deposit. Headers are ignored automatically.</p>
                                    </div>

                                    <!-- File Upload Input -->
                                    <div id="priceFileContainer" class="space-y-2 hidden">
                                        <label class="block text-sm font-bold text-gray-700 ml-1">Upload Spreadsheet File</label>
                                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-primary hover:bg-primary/5 transition-all cursor-pointer relative group">
                                            <input type="file" name="price_file" id="priceFileInput" accept=".xlsx,.xls,.csv" class="hidden" onchange="handlePriceFileSelect(event)">
                                            <div onclick="document.getElementById('priceFileInput').click()">
                                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                                    <i class="fas fa-file-excel text-xl text-gray-400 group-hover:text-primary"></i>
                                                </div>
                                                <p class="text-gray-600 font-bold mb-1" id="priceFileLabel">Click to select Excel/CSV file</p>
                                                <p class="text-gray-400 text-xs">Supports .xlsx, .xls, and .csv formats</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="priceProgressArea" class="hidden space-y-4 pt-4">
                                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div id="priceProgressBar" class="h-full bg-amber-500 w-0 transition-all duration-300"></div>
                                        </div>
                                        <p id="priceStatusMsg" class="text-center text-sm font-medium text-gray-600 italic">Processing updates...</p>
                                    </div>

                                    <button type="submit" id="priceSubmitBtn" class="w-full bg-gray-900 text-white rounded-2xl py-4 font-bold hover:bg-gray-800 transition-all shadow-xl shadow-gray-200 flex items-center justify-center space-x-2 cursor-pointer">
                                        <i class="fas fa-tags"></i>
                                        <span>Update Prices in Bulk</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Results Area -->
                        <div id="priceResultsArea" class="mt-8 hidden space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Updated successfully</p>
                                    <p id="priceUpdatedCount" class="text-3xl font-black text-green-500">0</p>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Not Found</p>
                                    <p id="priceNotFoundCount" class="text-3xl font-black text-amber-500">0</p>
                                </div>
                                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center">
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Errors</p>
                                    <p id="priceErrorCount" class="text-3xl font-black text-red-500">0</p>
                                </div>
                            </div>

                            <!-- Missing SKUs -->
                            <div id="priceMissingSkuContainer" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hidden">
                                <div class="px-6 py-4 bg-amber-50/50 border-b border-gray-100">
                                    <h4 class="font-bold text-amber-800 text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>SKUs Not Found</h4>
                                </div>
                                <div id="priceMissingSkuList" class="p-6 font-mono text-xs text-amber-700 grid grid-cols-2 md:grid-cols-4 gap-2">
                                    <!-- list -->
                                </div>
                            </div>

                            <!-- Errors Log -->
                            <div id="priceErrorsContainer" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hidden">
                                <div class="px-6 py-4 bg-red-50 border-b border-gray-100">
                                    <h4 class="font-bold text-red-800 text-sm"><i class="fas fa-times-circle mr-2"></i>Errors Log</h4>
                                </div>
                                <div id="priceErrorsList" class="p-6 font-mono text-xs text-red-700 space-y-1">
                                    <!-- list -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: IMAGES FOLDER UPLOAD -->
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
    const jewelCategories = <?php echo json_encode($jewelCategories ?? []); ?>;
    const garmentsCategories = <?php echo json_encode($garments ?? []); ?>;
    // TAB SWITCH LOGIC
    function switchTab(tab) {
        const propTab = document.getElementById('tab-properties');
        const priceTab = document.getElementById('tab-prices');
        const imgTab = document.getElementById('tab-images');
        
        const propSec = document.getElementById('properties-section');
        const priceSec = document.getElementById('prices-section');
        const imgSec = document.getElementById('images-section');

        // Reset tabs
        [propTab, priceTab, imgTab].forEach(t => {
            if (t) {
                t.classList.remove('text-primary', 'bg-primary/5');
                t.classList.add('text-gray-400', 'hover:text-gray-600');
            }
        });

        // Reset sections
        [propSec, priceSec, imgSec].forEach(s => {
            if (s) s.classList.add('hidden');
        });

        // Activate selected
        if (tab === 'properties') {
            propTab.classList.add('text-primary', 'bg-primary/5');
            propTab.classList.remove('text-gray-400', 'hover:text-gray-600');
            propSec.classList.remove('hidden');
        } else if (tab === 'prices') {
            priceTab.classList.add('text-primary', 'bg-primary/5');
            priceTab.classList.remove('text-gray-400', 'hover:text-gray-600');
            priceSec.classList.remove('hidden');
        } else if (tab === 'images') {
            imgTab.classList.add('text-primary', 'bg-primary/5');
            imgTab.classList.remove('text-gray-400', 'hover:text-gray-600');
            imgSec.classList.remove('hidden');
        }
    }

    // TOGGLE PRICE INPUT MODE
    function togglePriceInputMode(mode) {
        const pasteContainer = document.getElementById('pricePasteContainer');
        const fileContainer = document.getElementById('priceFileContainer');
        if (mode === 'paste') {
            pasteContainer.classList.remove('hidden');
            fileContainer.classList.add('hidden');
            document.getElementById('priceDataInput').required = true;
            document.getElementById('priceFileInput').required = false;
        } else {
            pasteContainer.classList.add('hidden');
            fileContainer.classList.remove('hidden');
            document.getElementById('priceDataInput').required = false;
            document.getElementById('priceFileInput').required = true;
        }
    }

    // FILE SELECTION LABEL UPDATE
    function handlePriceFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('priceFileLabel').innerHTML = `<span class="text-primary font-bold">${file.name}</span> selected`;
        }
    }

    // BULK PRICE UPDATE LOGIC
    async function handleBulkPriceUpdate(e) {
        e.preventDefault();

        const mode = document.querySelector('input[name="price_input_mode"]:checked').value;
        const submitBtn = document.getElementById('priceSubmitBtn');
        const progressArea = document.getElementById('priceProgressArea');
        const progressBar = document.getElementById('priceProgressBar');
        const statusMsg = document.getElementById('priceStatusMsg');
        const resultsArea = document.getElementById('priceResultsArea');

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        progressArea.classList.remove('hidden');
        resultsArea.classList.add('hidden');

        progressBar.style.width = '30%';
        progressBar.classList.remove('bg-red-500');
        statusMsg.classList.remove('text-red-500');
        statusMsg.innerText = 'Preparing update batch...';

        try {
            let response;
            if (mode === 'paste') {
                const priceData = document.getElementById('priceDataInput').value.trim();
                if (!priceData) {
                    alert('Please paste pricing data.');
                    return;
                }
                
                progressBar.style.width = '50%';
                statusMsg.innerText = 'Uploading pricing text...';

                response = await fetch('index.php?controller=product&action=processBulkPriceUpdate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        price_input_mode: 'paste',
                        price_data: priceData
                    })
                });
            } else {
                const fileInput = document.getElementById('priceFileInput');
                if (!fileInput.files.length) {
                    alert('Please select a file.');
                    return;
                }

                progressBar.style.width = '50%';
                statusMsg.innerText = 'Uploading spreadsheet file...';

                const formData = new FormData();
                formData.append('price_input_mode', 'file');
                formData.append('price_file', fileInput.files[0]);

                response = await fetch('index.php?controller=product&action=processBulkPriceUpdate', {
                    method: 'POST',
                    body: formData
                });
            }

            const data = await response.json();
            progressBar.style.width = '100%';

            if (data.success) {
                statusMsg.innerText = 'Batch completed!';
                setTimeout(() => {
                    progressArea.classList.add('hidden');
                    resultsArea.classList.remove('hidden');

                    document.getElementById('priceUpdatedCount').innerText = data.updatedCount;
                    document.getElementById('priceNotFoundCount').innerText = data.notFoundCount;
                    document.getElementById('priceErrorCount').innerText = data.errors ? data.errors.length : 0;

                    // Display Missing SKUs
                    const missingContainer = document.getElementById('priceMissingSkuContainer');
                    if (data.notFoundCount > 0) {
                        missingContainer.classList.remove('hidden');
                        document.getElementById('priceMissingSkuList').innerHTML = data.notFoundSkus.map(sku => `
                            <div class="bg-amber-50 px-2.5 py-1.5 rounded border border-amber-200 text-center font-bold">${sku}</div>
                        `).join('');
                    } else {
                        missingContainer.classList.add('hidden');
                    }

                    // Display Errors Log
                    const errorsContainer = document.getElementById('priceErrorsContainer');
                    if (data.errors && data.errors.length > 0) {
                        errorsContainer.classList.remove('hidden');
                        document.getElementById('priceErrorsList').innerHTML = data.errors.map(err => `
                            <div class="py-1 border-b border-red-50/50">${err}</div>
                        `).join('');
                    } else {
                        errorsContainer.classList.add('hidden');
                    }
                }, 500);
            } else {
                statusMsg.innerText = 'Error: ' + (data.error || 'Failed to update prices.');
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

    // BRAND INPUT CONTROL
    function toggleBrandInput(checked) {
        const input = document.getElementById('brandNameInput');
        input.disabled = !checked;
        if (checked) {
            input.classList.remove('bg-gray-100');
            input.classList.add('bg-white');
            input.focus();
        } else {
            input.classList.remove('bg-white');
            input.classList.add('bg-gray-100');
            input.value = '';
        }
    }

    // CATEGORY INPUT CONTROL
    function toggleCategoryInput(checked) {
        const container = document.getElementById('categoryFieldsContainer');
        if (checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            document.getElementById('categoryTypeSelect').value = '';
            resetCategoryDropdowns();
        }
    }

    function resetCategoryDropdowns() {
        const catSelect = document.getElementById('categorySelect');
        const subcatSelect = document.getElementById('subcategorySelect');
        catSelect.innerHTML = '<option value="">Select Category</option>';
        catSelect.disabled = true;
        subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
        subcatSelect.disabled = true;
    }

    function loadCategoryDropdowns(type) {
        resetCategoryDropdowns();
        const catSelect = document.getElementById('categorySelect');
        
        if (!type) return;

        catSelect.disabled = false;
        if (type === 'jewellery') {
            jewelCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.subcat_id;
                opt.textContent = cat.categories_name;
                catSelect.appendChild(opt);
            });
        } else {
            garmentsCategories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.garment_id;
                opt.textContent = cat.name;
                catSelect.appendChild(opt);
            });
        }
    }

    async function loadSubcategories(categoryId) {
        const subcatSelect = document.getElementById('subcategorySelect');
        subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
        subcatSelect.disabled = true;

        if (!categoryId) return;

        const type = document.getElementById('categoryTypeSelect').value;
        
        try {
            const response = await fetch(`index.php?controller=product&action=getSubcategories&type=${type}&parent_id=${categoryId}`);
            const data = await response.json();
            
            if (data && data.length > 0) {
                subcatSelect.disabled = false;
                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.subcat_id;
                    opt.textContent = sub.name;
                    subcatSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error("Error loading subcategories:", err);
        }
    }

    // PROPERTIES BATCH UPDATE LOGIC
    async function handleBulkUpdate(e) {
        e.preventDefault();
        
        const skus = document.getElementById('skusInput').value.trim();
        const priceSource = document.getElementById('priceSourceSelect').value;
        const availability = document.getElementById('availabilitySelect').value;

        const updateBrand = document.getElementById('updateBrandCheckbox').checked;
        const brandName = document.getElementById('brandNameInput').value.trim();
        
        const updateCategory = document.getElementById('updateCategoryCheckbox').checked;
        const categoryType = document.getElementById('categoryTypeSelect').value;
        const categoryId = document.getElementById('categorySelect').value;
        const subcategoryId = document.getElementById('subcategorySelect').value;

        if (!skus) {
            alert('Please enter at least one SKU');
            return;
        }

        if (priceSource === 'no_change' && availability === 'no_change' && !updateBrand && !updateCategory) {
            alert('Please select at least one setting to update.');
            return;
        }

        if (updateBrand && !brandName) {
            alert('Please enter a Brand Name.');
            return;
        }

        if (updateCategory && (!categoryType || !categoryId)) {
            alert('Please select a Product Type and Category.');
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
                    availability,
                    update_brand: updateBrand,
                    brand_name: brandName,
                    update_category: updateCategory,
                    category_type: categoryType,
                    category_id: categoryId,
                    subcategory_id: subcategoryId
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
