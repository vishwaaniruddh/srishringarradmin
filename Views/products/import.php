<!DOCTYPE html>
<html lang="en">
<head>
    <title>Import Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .progress-bar { transition: width 0.3s ease; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.03); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        .copy-pill { transition: all 0.15s ease; }
        .copy-pill:hover { transform: scale(1.05); }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.25s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Bulk Product Import (ZIP / Excel)';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="w-full mx-auto max-w-7xl">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <!-- Step 1: Upload -->
                            <div id="upload_section">
                                <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                            <i class="fas fa-file-archive text-indigo-600"></i> 1. Upload Bulk ZIP Package or Excel
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-0.5">Upload a compressed .zip containing your Excel spreadsheet and product image folders named after each SKU.</p>
                                    </div>
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <button type="button" onclick="toggleReference()" class="px-3.5 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-all border border-indigo-100 flex items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-sitemap"></i> Category Reference
                                        </button>
                                        <a href="index.php?controller=product&action=downloadTemplate" class="px-3.5 py-2 bg-white text-gray-700 hover:text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-50 transition-all border border-gray-200 flex items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-file-excel text-emerald-600"></i> Excel Only Template
                                        </a>
                                        <a href="index.php?controller=product&action=downloadSampleZip" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20 flex items-center gap-1.5">
                                            <i class="fas fa-download"></i> Download Sample ZIP Package
                                        </a>
                                    </div>
                                </div>

                                <!-- ZIP Structure How-To Card -->
                                <div class="mb-6 p-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl border border-slate-800 shadow-md">
                                    <div class="flex items-start justify-between gap-4 flex-wrap md:flex-nowrap">
                                        <div class="flex gap-3">
                                            <div class="w-10 h-10 bg-indigo-600/30 rounded-xl border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-lg flex-shrink-0 mt-0.5">
                                                <i class="fas fa-folder-tree"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-yellow-400 uppercase tracking-wider flex items-center gap-2">
                                                    How to organize your ZIP Archive:
                                                </h4>
                                                <p class="text-[12px] text-slate-300 mt-1 leading-relaxed">
                                                    No need to paste image links in Excel! Put all product photos inside a folder named after its <b>SKU</b>, and place the <b>Excel/CSV</b> file alongside them in a <b>.zip</b> archive.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="bg-slate-800/90 rounded-xl p-3 border border-slate-700 text-[11px] font-mono text-slate-300 flex-shrink-0 w-full md:w-auto">
                                            <div class="text-yellow-400 font-bold mb-1 flex items-center gap-1"><i class="fas fa-file-zipper"></i> YourArchive.zip</div>
                                            <div class="pl-2 border-l border-slate-600 space-y-0.5">
                                                <div>├── <span class="text-emerald-400 font-bold">products.xlsx</span> <span class="text-slate-500">(Spreadsheet)</span></div>
                                                <div>├── <span class="text-indigo-300 font-bold">JW101/</span> ── 1.jpg, 2.png <span class="text-slate-500">(Photos)</span></div>
                                                <div>└── <span class="text-pink-300 font-bold">GM202/</span> ── front.jpg, back.jpg</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Dropzone & File Status Card -->
                                <div id="drop_zone" class="border-2 border-dashed border-gray-200 hover:border-indigo-500 rounded-2xl p-8 text-center transition-all group relative bg-gray-50/40">
                                    <input type="file" id="csv_file" accept=".zip,.xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    
                                    <!-- Default Prompt (Before File Selection) -->
                                    <div id="drop_prompt" class="transition-all">
                                        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl group-hover:scale-110 transition-transform shadow-sm">
                                            <i class="fas fa-file-zipper"></i>
                                        </div>
                                        <p class="text-gray-800 mb-1 font-bold text-sm" id="drop_title">Click to browse or drag and drop your ZIP Archive (.zip) or Excel (.xlsx / .csv) file here</p>
                                        <p class="text-xs text-gray-500">Auto-extracts product spreadsheet and links image folders matching SKU names automatically</p>
                                        <div class="mt-4 flex items-center justify-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-[11px] font-bold text-indigo-700 shadow-xs">
                                                <i class="fas fa-file-zipper text-indigo-600"></i> .zip (Recommended)
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-md text-[11px] font-medium text-gray-600 shadow-xs">
                                                <i class="fas fa-file-excel text-emerald-600"></i> .xlsx / .xls
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-200 rounded-md text-[11px] font-medium text-gray-600 shadow-xs">
                                                <i class="fas fa-file-csv text-blue-600"></i> .csv
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Active Dragging Overlay -->
                                    <div id="drag_overlay" class="hidden pointer-events-none py-4">
                                        <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-3xl animate-bounce">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                        <p class="text-indigo-700 font-bold text-base">Drop your file package here to extract & load!</p>
                                    </div>

                                    <!-- Uploading / Extracting Spinner Overlay -->
                                    <div id="upload_spinner_overlay" class="hidden py-6">
                                        <div class="w-14 h-14 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                                        <p id="upload_spinnerText" class="text-sm font-bold text-indigo-800">Extracting archive and indexing SKU image folders...</p>
                                        <p class="text-xs text-gray-500 mt-1">Please wait while we parse your spreadsheet and photos.</p>
                                    </div>
                                </div>

                                <!-- File Loaded Confirmation Card (Shown when file is selected) -->
                                <div id="file_confirmation_card" class="hidden mt-6 bg-gradient-to-r from-emerald-50/80 via-white to-teal-50/60 border-2 border-emerald-300/80 rounded-2xl p-5 shadow-sm transition-all animate-fadeIn">
                                    <div class="flex items-center justify-between flex-wrap gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-emerald-600 text-white rounded-xl flex items-center justify-center text-2xl shadow-md shadow-emerald-600/30 flex-shrink-0 relative">
                                                <i id="file_type_icon" class="fas fa-file-zipper"></i>
                                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-white text-emerald-600 rounded-full flex items-center justify-center text-[10px] shadow border border-emerald-100">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 id="file_name_display" class="text-sm font-bold text-gray-800 break-all">filename.zip</h4>
                                                    <span id="file_status_badge" class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold tracking-wide uppercase flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> Package Ready
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 text-xs text-gray-600 mt-1.5 flex-wrap font-medium">
                                                    <span id="file_size_display"><i class="fas fa-weight-hanging text-gray-400 mr-1"></i>0 KB</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span id="file_sheet_display"><i class="fas fa-file-excel text-emerald-600 mr-1"></i>Spreadsheet</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span id="file_rows_display" class="font-bold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-md"><i class="fas fa-box text-emerald-600 mr-1"></i>0 Products</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span id="file_images_display" class="font-bold text-indigo-800 bg-indigo-100/80 px-2 py-0.5 rounded-md"><i class="fas fa-images text-indigo-600 mr-1"></i>0 Images Matched</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="changeFile()" class="px-3.5 py-2 bg-white text-gray-700 hover:text-indigo-600 rounded-xl text-xs font-bold border border-gray-200 hover:border-indigo-200 transition-all shadow-sm flex items-center gap-1.5">
                                                <i class="fas fa-sync-alt"></i> Change File
                                            </button>
                                            <button type="button" onclick="resetUploadedFile()" class="px-3.5 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold border border-rose-100 transition-all shadow-sm flex items-center gap-1.5" title="Remove File">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mini Preview Section -->
                                    <div id="file_preview_wrapper" class="mt-4 pt-4 border-t border-emerald-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[11px] font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fas fa-eye text-emerald-600"></i> Sample Preview (First <span id="preview_count">0</span> Products)
                                            </span>
                                            <span class="text-[11px] text-gray-500 font-medium">Review rows and matched image counts below</span>
                                        </div>
                                        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white custom-scrollbar max-h-56">
                                            <table class="w-full text-left text-xs">
                                                <thead class="bg-gray-50 text-gray-500 text-[10px] font-bold uppercase tracking-wider border-b border-gray-100 sticky top-0">
                                                    <tr>
                                                        <th class="px-3 py-2.5">#</th>
                                                        <th class="px-3 py-2.5">SKU</th>
                                                        <th class="px-3 py-2.5">Product Name</th>
                                                        <th class="px-3 py-2.5">Type</th>
                                                        <th class="px-3 py-2.5">Categories</th>
                                                        <th class="px-3 py-2.5">Rent / Sale</th>
                                                        <th class="px-3 py-2.5 text-right">Images Matched</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="preview_table_body" class="divide-y divide-gray-100 text-gray-700">
                                                    <!-- Injected dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reference Section (Aligned with Product Edit Category Structure) -->
                                <div id="reference_section" class="hidden mt-6 p-6 bg-slate-900 text-white rounded-2xl border border-slate-800 shadow-xl">
                                    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                                        <div>
                                            <h4 class="text-sm font-bold text-yellow-400 flex items-center gap-2">
                                                <i class="fas fa-sitemap"></i> Unified Category & Subcategory ID Reference
                                            </h4>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Use either the <b>Numeric IDs</b> or the <b>Category Names</b> in your template. Click any ID badge to copy.</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="relative">
                                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                                <input type="text" id="ref_search" oninput="filterReferenceCategories()" placeholder="Search categories or subcategories..." class="bg-slate-800 border border-slate-700 text-xs text-white pl-8 pr-3 py-1.5 rounded-lg focus:outline-none focus:border-yellow-400 w-64 placeholder-slate-500">
                                            </div>
                                            <button type="button" onclick="toggleReference()" class="text-slate-400 hover:text-white text-xs px-2 py-1">
                                                <i class="fas fa-times"></i> Close
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Jewellery Section -->
                                        <div class="bg-slate-800/60 rounded-xl p-4 border border-slate-700/60">
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-700">
                                                <h5 class="text-xs font-bold text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                                                    <i class="fas fa-gem text-indigo-400"></i> Jewellery Categories
                                                </h5>
                                                <span class="text-[10px] bg-indigo-950 text-indigo-300 px-2 py-0.5 rounded-full font-mono font-bold">
                                                    <?php echo count($jewelCategoriesTree ?? []); ?> Main
                                                </span>
                                            </div>
                                            <div id="jewel_ref_tree" class="space-y-2.5 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                                <?php if (!empty($jewelCategoriesTree)): ?>
                                                    <?php foreach ($jewelCategoriesTree as $cat): ?>
                                                        <div class="ref-cat-node bg-slate-900/80 rounded-lg p-2.5 border border-slate-700/80" data-name="<?php echo htmlspecialchars(strtolower($cat['name'])); ?>">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <span class="text-xs font-bold text-yellow-300 flex items-center gap-1.5">
                                                                    <i class="fas fa-folder text-[10px] text-yellow-500"></i> <?php echo htmlspecialchars($cat['name']); ?>
                                                                </span>
                                                                <button type="button" onclick="copyId('<?php echo $cat['id']; ?>', this)" class="copy-pill text-[10px] bg-slate-800 hover:bg-yellow-400 hover:text-slate-950 text-yellow-300 border border-yellow-500/30 px-2 py-0.5 rounded font-mono font-bold flex items-center gap-1" title="Click to copy Main Category ID">
                                                                    <span>cat_id: <b><?php echo $cat['id']; ?></b></span>
                                                                    <i class="fas fa-copy text-[8px] opacity-70"></i>
                                                                </button>
                                                            </div>
                                                            <?php if (!empty($cat['subcategories'])): ?>
                                                                <div class="mt-2 pl-3 border-l-2 border-slate-700 space-y-1.5">
                                                                    <?php foreach ($cat['subcategories'] as $sub): ?>
                                                                        <div class="ref-sub-node flex items-center justify-between text-[11px] py-0.5 text-slate-300" data-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>">
                                                                            <span class="flex items-center gap-1 text-slate-300">
                                                                                <i class="fas fa-tag text-[9px] text-indigo-400"></i> <?php echo htmlspecialchars($sub['name']); ?>
                                                                            </span>
                                                                            <button type="button" onclick="copyId('<?php echo $sub['id']; ?>', this)" class="copy-pill text-[9px] bg-slate-800/80 hover:bg-indigo-400 hover:text-slate-950 text-indigo-300 border border-indigo-500/30 px-1.5 py-0.5 rounded font-mono" title="Click to copy Subcategory ID">
                                                                                <span>sub_id: <b><?php echo $sub['id']; ?></b></span>
                                                                                <i class="fas fa-copy text-[7px] opacity-70"></i>
                                                                            </button>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Apparel Section -->
                                        <div class="bg-slate-800/60 rounded-xl p-4 border border-slate-700/60">
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-700">
                                                <h5 class="text-xs font-bold text-pink-300 uppercase tracking-wider flex items-center gap-2">
                                                    <i class="fas fa-tshirt text-pink-400"></i> Apparel / Garment Categories
                                                </h5>
                                                <span class="text-[10px] bg-pink-950 text-pink-300 px-2 py-0.5 rounded-full font-mono font-bold">
                                                    <?php echo count($garmentCategoriesTree ?? []); ?> Main
                                                </span>
                                            </div>
                                            <div id="garment_ref_tree" class="space-y-2.5 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                                <?php if (!empty($garmentCategoriesTree)): ?>
                                                    <?php foreach ($garmentCategoriesTree as $cat): ?>
                                                        <div class="ref-cat-node bg-slate-900/80 rounded-lg p-2.5 border border-slate-700/80" data-name="<?php echo htmlspecialchars(strtolower($cat['name'])); ?>">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <span class="text-xs font-bold text-pink-300 flex items-center gap-1.5">
                                                                    <i class="fas fa-folder text-[10px] text-pink-500"></i> <?php echo htmlspecialchars($cat['name']); ?>
                                                                </span>
                                                                <button type="button" onclick="copyId('<?php echo $cat['id']; ?>', this)" class="copy-pill text-[10px] bg-slate-800 hover:bg-pink-400 hover:text-slate-950 text-pink-300 border border-pink-500/30 px-2 py-0.5 rounded font-mono font-bold flex items-center gap-1" title="Click to copy Main Garment ID">
                                                                    <span>cat_id: <b><?php echo $cat['id']; ?></b></span>
                                                                    <i class="fas fa-copy text-[8px] opacity-70"></i>
                                                                </button>
                                                            </div>
                                                            <?php if (!empty($cat['subcategories'])): ?>
                                                                <div class="mt-2 pl-3 border-l-2 border-slate-700 space-y-1.5">
                                                                    <?php foreach ($cat['subcategories'] as $sub): ?>
                                                                        <div class="ref-sub-node flex items-center justify-between text-[11px] py-0.5 text-slate-300" data-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>">
                                                                            <span class="flex items-center gap-1 text-slate-300">
                                                                                <i class="fas fa-tag text-[9px] text-pink-400"></i> <?php echo htmlspecialchars($sub['name']); ?>
                                                                            </span>
                                                                            <button type="button" onclick="copyId('<?php echo $sub['id']; ?>', this)" class="copy-pill text-[9px] bg-slate-800/80 hover:bg-pink-400 hover:text-slate-950 text-pink-300 border border-pink-500/30 px-1.5 py-0.5 rounded font-mono" title="Click to copy Subcategory ID">
                                                                                <span>sub_id: <b><?php echo $sub['id']; ?></b></span>
                                                                                <i class="fas fa-copy text-[7px] opacity-70"></i>
                                                                            </button>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-slate-800/90 rounded-xl border border-slate-700 flex items-center justify-between text-[11px] text-slate-300">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-lightbulb text-yellow-400"></i>
                                            <span><b>Multi-Category Support:</b> You can pass comma-separated IDs or names (e.g. <code>1, 3</code> or <code>Necklace Set, Bangles</code>) under the <code>categories</code> and <code>sub_categories</code> columns.</span>
                                        </div>
                                        <span id="copy_toast" class="text-xs text-green-400 font-bold hidden"><i class="fas fa-check"></i> Copied!</span>
                                    </div>
                                </div>

                                <!-- Supported Columns Guide Card -->
                                <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-table text-indigo-500"></i> Excel Column Mapping Guide (No Image Column Needed)
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-[11px]">
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">sku</span> <span class="text-red-500">*</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Unique code matching folder name (e.g. JW101)</p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">name</span> <span class="text-red-500">*</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Product title / name</p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">type</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5"><code>jewellery</code> or <code>garments</code></p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">categories</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Main Category ID(s) or Name(s) e.g. <code>1, 3</code></p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">sub_categories</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Subcategory ID(s) or Name(s) e.g. <code>5, 8</code></p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">rental_price / s_price</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Rent and Sale prices (e.g. 1500, 5000)</p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">deposit</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Security deposit amount (e.g. 2000)</p>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-lg border border-gray-200/80">
                                            <span class="font-bold text-gray-800 font-mono">colors</span>
                                            <p class="text-gray-500 text-[10px] mt-0.5">Colors (e.g. Gold, Maroon)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-between items-center">
                                    <button type="button" onclick="toggleReference()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-all flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> View Category Reference
                                    </button>
                                    <button id="start_import" disabled class="px-10 py-3.5 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <i class="fas fa-rocket"></i> Start Professional Import
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Progress & Table -->
                            <div id="progress_section" class="hidden">
                                <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">2. Professional Review & Sync</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">Importing products, linking SKU images, and assigning category trees.</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span id="overall_status_badge" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-100">Initializing...</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Products</div>
                                        <div id="count_total" class="text-2xl font-bold text-gray-800">0</div>
                                    </div>
                                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 text-center">
                                        <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">New Inserted</div>
                                        <div id="count_success" class="text-2xl font-bold text-emerald-600">0</div>
                                    </div>
                                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 text-center">
                                        <div class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1">Skipped (Exists)</div>
                                        <div id="count_skipped" class="text-2xl font-bold text-amber-600">0</div>
                                    </div>
                                    <div class="bg-rose-50 p-4 rounded-xl border border-rose-100 text-center">
                                        <div class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1">Errors</div>
                                        <div id="count_error" class="text-2xl font-bold text-rose-600">0</div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span id="progress_text" class="text-xs font-medium text-gray-600 font-mono">Ready to sync...</span>
                                        <span id="progress_percent" class="text-xs font-bold text-indigo-600">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                                        <div id="progress_bar" class="progress-bar bg-indigo-600 h-full w-0 transition-all duration-300"></div>
                                    </div>
                                </div>

                                <!-- Professional Data Table -->
                                <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm bg-white mb-8">
                                    <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="sticky top-0 z-20 bg-gray-50 border-b border-gray-100">
                                                <tr>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKU</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product Name</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Categories</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Images</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="import_table_body" class="divide-y divide-gray-50">
                                                <!-- Rows will be injected here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <button id="download_report" disabled class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                                        <i class="fas fa-file-excel text-emerald-600"></i> Download Sync Report
                                    </button>
                                    <a href="index.php?controller=product&action=index" id="finish_btn" class="hidden px-8 py-3 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> View All Products
                                    </a>
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
        const fileInput = document.getElementById('csv_file');
        const startBtn = document.getElementById('start_import');
        const uploadSection = document.getElementById('upload_section');
        const progressSection = document.getElementById('progress_section');
        const tableBody = document.getElementById('import_table_body');
        const downloadReportBtn = document.getElementById('download_report');
        const finishBtn = document.getElementById('finish_btn');
        const overallBadge = document.getElementById('overall_status_badge');

        const dropZone = document.getElementById('drop_zone');
        const dropPrompt = document.getElementById('drop_prompt');
        const dragOverlay = document.getElementById('drag_overlay');
        const spinnerOverlay = document.getElementById('upload_spinner_overlay');
        const fileCard = document.getElementById('file_confirmation_card');
        const previewTableBody = document.getElementById('preview_table_body');

        let currentImportToken = '';
        let csvData = [];
        let importResults = [];

        function toggleReference() {
            const ref = document.getElementById('reference_section');
            ref.classList.toggle('hidden');
        }

        function filterReferenceCategories() {
            const query = (document.getElementById('ref_search')?.value || '').trim().toLowerCase();
            const nodes = document.querySelectorAll('.ref-cat-node');
            nodes.forEach(node => {
                const catName = node.getAttribute('data-name') || '';
                const subNodes = node.querySelectorAll('.ref-sub-node');
                let hasSubMatch = false;

                subNodes.forEach(sNode => {
                    const sName = sNode.getAttribute('data-name') || '';
                    if (query === '' || sName.includes(query)) {
                        sNode.style.display = 'flex';
                        hasSubMatch = true;
                    } else {
                        sNode.style.display = 'none';
                    }
                });

                if (query === '' || catName.includes(query) || hasSubMatch) {
                    node.style.display = 'block';
                } else {
                    node.style.display = 'none';
                }
            });
        }

        function copyId(id, btn) {
            navigator.clipboard.writeText(id).then(() => {
                const toast = document.getElementById('copy_toast');
                if (toast) {
                    toast.classList.remove('hidden');
                    setTimeout(() => toast.classList.add('hidden'), 2000);
                }
            });
        }

        function formatBytes(bytes, decimals = 1) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function changeFile() {
            fileInput.click();
        }

        function resetUploadedFile() {
            if (currentImportToken) {
                // Call cleanup asynchronously
                fetch(`index.php?controller=product&action=cleanupImportTemp&import_token=${encodeURIComponent(currentImportToken)}`).catch(() => {});
            }
            fileInput.value = '';
            currentImportToken = '';
            csvData = [];
            if (fileCard) fileCard.classList.add('hidden');
            if (spinnerOverlay) spinnerOverlay.classList.add('hidden');
            if (dropPrompt) dropPrompt.classList.remove('hidden');
            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-rocket"></i> Start Professional Import';
            tableBody.innerHTML = '';
            previewTableBody.innerHTML = '';
        }

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            fileInput.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-indigo-600', 'bg-indigo-50/40', 'ring-4', 'ring-indigo-100');
                if (dropPrompt) dropPrompt.classList.add('hidden');
                if (spinnerOverlay.classList.contains('hidden') && dragOverlay) {
                    dragOverlay.classList.remove('hidden');
                }
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileInput.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-indigo-600', 'bg-indigo-50/40', 'ring-4', 'ring-indigo-100');
                if (dragOverlay) dragOverlay.classList.add('hidden');
                if (spinnerOverlay.classList.contains('hidden') && dropPrompt) {
                    dropPrompt.classList.remove('hidden');
                }
            }, false);
        });

        fileInput.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            if (file) uploadPackageFile(file);
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) uploadPackageFile(file);
        });

        async function uploadPackageFile(file) {
            if (!file) return;

            const isZip = file.name.toLowerCase().endsWith('.zip');
            const iconElem = document.getElementById('file_type_icon');
            if (iconElem) {
                iconElem.className = isZip ? 'fas fa-file-zipper' : 'fas fa-file-excel';
            }

            // Show loading spinner
            dropPrompt.classList.add('hidden');
            dragOverlay.classList.add('hidden');
            spinnerOverlay.classList.remove('hidden');
            document.getElementById('upload_spinnerText').textContent = isZip 
                ? "Extracting ZIP archive & indexing SKU image folders..." 
                : "Parsing spreadsheet products...";

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('index.php?controller=product&action=uploadImportPackage', {
                    method: 'POST',
                    body: formData
                });

                const res = await response.json();

                spinnerOverlay.classList.add('hidden');
                dropPrompt.classList.remove('hidden');

                if (res.error) {
                    alert("Upload Error: " + res.error);
                    resetUploadedFile();
                    return;
                }

                currentImportToken = res.import_token || '';
                csvData = res.products || [];

                if (csvData.length === 0) {
                    alert("No valid product rows found in the spreadsheet.");
                    resetUploadedFile();
                    return;
                }

                // Update confirmation details
                document.getElementById('file_name_display').textContent = file.name;
                document.getElementById('file_size_display').innerHTML = `<i class="fas fa-weight-hanging text-gray-400 mr-1"></i> ${formatBytes(file.size)}`;
                document.getElementById('file_sheet_display').innerHTML = `<i class="fas fa-file-lines text-indigo-600 mr-1"></i> ${res.spreadsheet_name || 'Spreadsheet'}`;
                document.getElementById('file_rows_display').innerHTML = `<i class="fas fa-box text-emerald-600 mr-1"></i> ${res.total_products} Products`;
                document.getElementById('file_images_display').innerHTML = `<i class="fas fa-images text-indigo-600 mr-1"></i> ${res.total_images} Photos Matched (${res.total_folders} Folders)`;
                document.getElementById('preview_count').textContent = Math.min(5, csvData.length);

                document.getElementById('file_status_badge').innerHTML = `<i class="fas fa-check-circle"></i> ${res.total_products} Products Ready`;
                document.getElementById('file_status_badge').className = "px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold tracking-wide uppercase flex items-center gap-1";

                fileCard.classList.remove('hidden');

                // Render Sample Preview Table
                previewTableBody.innerHTML = '';
                tableBody.innerHTML = '';

                csvData.forEach((obj, idx) => {
                    const rawCats = obj.categories || obj.category_id || obj.category || '';
                    const rawSubs = obj.sub_categories || obj.subcat_id || obj.sub_category || '';
                    const catPreview = [rawCats, rawSubs].filter(Boolean).join(' / ') || '—';
                    const imgCount = obj.images_count || (obj.temp_images ? obj.temp_images.length : 0);

                    // 1. Main Sync Table Row (for Step 2)
                    const tr = document.createElement('tr');
                    tr.id = `row-${idx}`;
                    tr.className = "hover:bg-gray-50/60 transition-colors";
                    tr.innerHTML = `
                        <td class="px-6 py-4 text-xs font-mono font-bold text-gray-800">${obj.sku || 'N/A'}</td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-700">${obj.name || 'Unnamed Product'}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase font-mono">${obj.type || 'auto'}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-[180px]" title="${catPreview}">
                            ${catPreview}
                        </td>
                        <td class="px-6 py-4 text-xs">
                            ${imgCount > 0 
                                ? `<span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-[10px] font-bold flex items-center gap-1 w-fit"><i class="fas fa-camera text-[9px]"></i> ${imgCount} Photos</span>` 
                                : `<span class="text-gray-400 text-[10px] italic">0 photos</span>`}
                        </td>
                        <td class="px-6 py-4 text-right status-cell">
                            ${getStatusBadge('pending')}
                        </td>
                    `;
                    tableBody.appendChild(tr);

                    // 2. Mini Sample Preview Table (First 5 Rows)
                    if (idx < 5) {
                        const pricePreview = [
                            obj.rental_price ? `Rent: ₹${obj.rental_price}` : '',
                            (obj.s_price || obj.sales_price || obj.sale_price) ? `Sale: ₹${obj.s_price || obj.sales_price || obj.sale_price}` : ''
                        ].filter(Boolean).join(' | ') || '—';

                        const previewTr = document.createElement('tr');
                        previewTr.className = "hover:bg-emerald-50/30 transition-colors";
                        previewTr.innerHTML = `
                            <td class="px-3 py-2 text-gray-400 font-mono">${idx + 1}</td>
                            <td class="px-3 py-2 font-mono font-bold text-gray-900">${obj.sku || 'N/A'}</td>
                            <td class="px-3 py-2 font-semibold text-gray-800 truncate max-w-[200px]" title="${obj.name || ''}">${obj.name || 'Unnamed Product'}</td>
                            <td class="px-3 py-2"><span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 rounded text-[10px] uppercase font-mono">${obj.type || 'auto'}</span></td>
                            <td class="px-3 py-2 text-gray-600 truncate max-w-[140px]" title="${catPreview}">${catPreview}</td>
                            <td class="px-3 py-2 text-emerald-700 font-medium">${pricePreview}</td>
                            <td class="px-3 py-2 text-right">
                                ${imgCount > 0 
                                    ? `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md text-[10px] font-bold"><i class="fas fa-camera text-[9px] mr-0.5"></i> ${imgCount}</span>` 
                                    : `<span class="text-gray-400 text-[10px]">0</span>`}
                            </td>
                        `;
                        previewTableBody.appendChild(previewTr);
                    }
                });

                document.getElementById('count_total').textContent = csvData.length;
                startBtn.disabled = false;
                startBtn.innerHTML = `<i class="fas fa-rocket"></i> Start Import (${csvData.length} Products, ${res.total_images} Images)`;
                overallBadge.textContent = "Ready to sync (" + csvData.length + " rows)";

            } catch (err) {
                console.error(err);
                spinnerOverlay.classList.add('hidden');
                dropPrompt.classList.remove('hidden');
                alert("Upload failed: " + err.message);
            }
        }

        function getStatusBadge(status, message = '') {
            const styles = {
                pending: 'bg-gray-100 text-gray-500 border-gray-200',
                syncing: 'bg-indigo-50 text-indigo-600 border-indigo-200 animate-pulse',
                success: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                skipped: 'bg-amber-50 text-amber-700 border-amber-200',
                updated: 'bg-blue-50 text-blue-700 border-blue-200',
                error: 'bg-rose-50 text-rose-700 border-rose-200'
            };
            const labels = {
                pending: 'Pending',
                syncing: 'Syncing',
                success: 'Created',
                skipped: 'Skipped (Exists)',
                updated: 'Updated',
                error: 'Error'
            };
            const icon = {
                pending: 'fa-clock',
                syncing: 'fa-spinner fa-spin',
                success: 'fa-check-circle',
                skipped: 'fa-forward',
                updated: 'fa-sync-alt',
                error: 'fa-exclamation-circle'
            };
            
            return `
                <div class="inline-flex flex-col items-end">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${styles[status] || styles.error}">
                        <i class="fas ${icon[status] || 'fa-info-circle'} mr-1"></i> ${labels[status] || status}
                    </span>
                    ${message ? `<div class="text-[10px] text-gray-500 mt-1 max-w-[240px] truncate" title="${message}">${message}</div>` : ''}
                </div>
            `;
        }

        startBtn.addEventListener('click', async function() {
            uploadSection.classList.add('hidden');
            progressSection.classList.remove('hidden');
            overallBadge.className = "px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold uppercase tracking-wider border border-amber-200";
            overallBadge.textContent = "Syncing in progress...";
            
            let success = 0, skipped = 0, updated = 0, error = 0;
            importResults = [['SKU', 'Name', 'Type', 'Images Attached', 'Status', 'Message']];

            for (let i = 0; i < csvData.length; i++) {
                const row = csvData[i];
                const tr = document.getElementById(`row-${i}`);
                const statusCell = tr ? tr.querySelector('.status-cell') : null;
                
                if (statusCell) statusCell.innerHTML = getStatusBadge('syncing');
                if (tr) tr.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Attach import_token to row
                const payload = {
                    ...row,
                    import_token: currentImportToken
                };

                try {
                    const response = await fetch('index.php?controller=product&action=processImportRow', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();
                    
                    const imgCount = row.images_count || (row.temp_images ? row.temp_images.length : 0);

                    if (result.status === 'success') {
                        success++;
                        if (statusCell) statusCell.innerHTML = getStatusBadge('success', result.message);
                        document.getElementById('count_success').textContent = success;
                    } else if (result.status === 'skipped') {
                        skipped++;
                        if (statusCell) statusCell.innerHTML = getStatusBadge('skipped', result.message);
                        const skippedEl = document.getElementById('count_skipped');
                        if (skippedEl) skippedEl.textContent = skipped;
                    } else if (result.status === 'updated') {
                        updated++;
                        if (statusCell) statusCell.innerHTML = getStatusBadge('updated', result.message);
                        const updatedEl = document.getElementById('count_updated');
                        if (updatedEl) updatedEl.textContent = updated;
                    } else {
                        error++;
                        if (statusCell) statusCell.innerHTML = getStatusBadge('error', result.message);
                        document.getElementById('count_error').textContent = error;
                    }
                    importResults.push([row.sku, row.name, row.type || '', imgCount, result.status, result.message || '']);
                } catch (err) {
                    error++;
                    if (statusCell) statusCell.innerHTML = getStatusBadge('error', err.message);
                    document.getElementById('count_error').textContent = error;
                    importResults.push([row.sku, row.name, row.type || '', 0, 'error', err.message]);
                }

                const progress = Math.round(((i + 1) / csvData.length) * 100);
                document.getElementById('progress_bar').style.width = progress + '%';
                document.getElementById('progress_percent').textContent = progress + '%';
                document.getElementById('progress_text').textContent = `Processing item ${i + 1} of ${csvData.length}...`;
            }

            overallBadge.className = "px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-200";
            overallBadge.textContent = "Sync Completed!";
            document.getElementById('progress_text').textContent = 'All items and images processed successfully.';
            
            downloadReportBtn.disabled = false;
            finishBtn.classList.remove('hidden');

            // Cleanup temp directory
            if (currentImportToken) {
                fetch(`index.php?controller=product&action=cleanupImportTemp&import_token=${encodeURIComponent(currentImportToken)}`).catch(() => {});
            }
        });

        downloadReportBtn.addEventListener('click', function() {
            const csvContent = importResults.map(r => r.map(c => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `import_sync_report_${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>
</html>
