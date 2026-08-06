<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>Unmapped Products - Srishringarr Studio</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #050505; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-black text-white font-sans antialiased overflow-hidden selection:bg-indigo-500/30">
    <div class="flex h-screen w-full">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#050505]">
            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between px-6 border-b border-white/5 bg-[#0a0a0a] shrink-0">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="fas fa-tags text-indigo-400"></i> Unmapped Products Category Manager
                    </h1>
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded uppercase tracking-wider border border-amber-500/20">
                        product & garment_product &rarr; product_categories
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="loadUnmappedProducts(1)" class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 rounded-lg text-xs font-semibold border border-white/10 transition-all flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh Data
                    </button>
                    <button type="button" id="btnAutoFixAll" onclick="runAutoFixAll()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-magic" id="fixIcon"></i>
                        <span>Auto-Fix All Unmapped Products</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col overflow-y-auto custom-scrollbar gap-6">
                
                <!-- Overview Info Card -->
                <div class="bg-gradient-to-r from-[#0e0e17] via-[#0a0a10] to-[#0d0a0e] border border-white/10 rounded-2xl p-6 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shrink-0">
                    <div class="space-y-1.5 max-w-2xl">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-folder-plus text-indigo-400"></i> Ensure Every Product Has A Category Mapping
                        </h2>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Every product stored in <code class="bg-black/60 px-1.5 py-0.5 rounded text-indigo-300 font-mono">product</code> and <code class="bg-black/60 px-1.5 py-0.5 rounded text-indigo-300 font-mono">garment_product</code> tables must have corresponding relationship entries in the <code class="bg-black/60 px-1.5 py-0.5 rounded text-emerald-300 font-mono">product_categories</code> table to appear accurately on store catalog pages and search listings.
                        </p>
                    </div>

                    <!-- Metrics Badges -->
                    <div class="flex items-center gap-4 bg-black/60 p-4 rounded-xl border border-white/10 shrink-0">
                        <div class="text-center px-3 border-r border-white/10">
                            <div class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider">Unmapped Jewellery</div>
                            <div id="statJewelCount" class="text-2xl font-bold text-amber-400 font-mono"><?php echo number_format($jewelUnmappedCount); ?></div>
                        </div>
                        <div class="text-center px-3 border-r border-white/10">
                            <div class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider">Unmapped Garments</div>
                            <div id="statGarmentCount" class="text-2xl font-bold text-purple-400 font-mono"><?php echo number_format($garmentUnmappedCount); ?></div>
                        </div>
                        <div class="text-center px-3">
                            <div class="text-[10px] uppercase font-bold text-zinc-500 tracking-wider">Total Missing</div>
                            <div id="statTotalCount" class="text-2xl font-bold text-white font-mono"><?php echo number_format($totalUnmappedCount); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Status Banner / Live Output -->
                <div id="liveActivityAlert" class="hidden p-5 rounded-2xl border border-emerald-500/30 bg-[#0d1510] shadow-2xl flex flex-col gap-3 shrink-0">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-spinner fa-spin text-emerald-400 text-lg" id="activitySpinner"></i>
                        <div>
                            <h3 id="activityTitle" class="text-sm font-bold text-white">Auto-Assigning Categories...</h3>
                            <p id="activityMsg" class="text-xs text-zinc-300 mt-0.5">Inserting category relationships for unmapped products...</p>
                        </div>
                    </div>
                </div>

                <!-- Unmapped Products Table Card -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col overflow-hidden shadow-2xl shrink-0">
                    
                    <!-- Search & Filter Controls Header -->
                    <div class="px-6 py-4 border-b border-white/5 bg-[#111] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <!-- Type Filter -->
                            <select id="selectTypeFilter" onchange="loadUnmappedProducts(1)" class="bg-black border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500 cursor-pointer font-medium">
                                <option value="all">All Product Types</option>
                                <option value="jewellery">Jewellery Only</option>
                                <option value="garments">Garments Only</option>
                            </select>

                            <!-- Search Input -->
                            <div class="relative flex-1 sm:w-80">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                                <input type="text" id="inputSearch" onkeyup="handleSearchKey(event)" placeholder="Search SKU code or product name..." class="w-full bg-black border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition-colors">
                            </div>
                            <button type="button" onclick="loadUnmappedProducts(1)" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-all">Search</button>
                        </div>
                        <div class="text-xs text-zinc-400 font-mono">
                            Showing <span id="txtShowingCount" class="font-bold text-white">0</span> of <span id="txtTotalCount" class="font-bold text-white">0</span> unmapped products
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto overflow-y-auto max-h-[550px] custom-scrollbar">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-[#111] sticky top-0 z-10 border-b border-white/5 text-xs text-zinc-400 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Product ID</th>
                                    <th class="px-6 py-3">SKU Code</th>
                                    <th class="px-6 py-3">Product Name</th>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Assign Main Category</th>
                                    <th class="px-6 py-3">Assign Subcategory</th>
                                    <th class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody" class="divide-y divide-white/5">
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                        <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i> Loading unmapped products...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="px-6 py-3 border-t border-white/5 bg-[#111] flex items-center justify-between text-xs text-zinc-400">
                        <div>Page <span id="txtCurrentPage" class="font-bold text-white">1</span> of <span id="txtTotalPages" class="font-bold text-white">1</span></div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnPrevPage" onclick="changePage(-1)" class="px-3 py-1 bg-zinc-900 hover:bg-zinc-800 text-white rounded border border-white/10 text-xs disabled:opacity-30 disabled:cursor-not-allowed">Previous</button>
                            <button type="button" id="btnNextPage" onclick="changePage(1)" class="px-3 py-1 bg-zinc-900 hover:bg-zinc-800 text-white rounded border border-white/10 text-xs disabled:opacity-30 disabled:cursor-not-allowed">Next</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Category Options Data (Passed from PHP) -->
    <script>
        const JEWEL_CATEGORIES = <?php echo json_encode($jewelCategories ?? []); ?>;
        const GARMENT_CATEGORIES = <?php echo json_encode($garmentCategories ?? []); ?>;

        let currentPage = 1;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', () => {
            loadUnmappedProducts(1);
        });

        function handleSearchKey(event) {
            if (event.key === 'Enter') {
                loadUnmappedProducts(1);
            }
        }

        function loadUnmappedProducts(page = 1) {
            currentPage = page;
            const search = document.getElementById('inputSearch').value.trim();
            const typeFilter = document.getElementById('selectTypeFilter').value;
            const tableBody = document.getElementById('tableBody');
            const refreshIcon = document.getElementById('refreshIcon');

            refreshIcon.classList.add('fa-spin');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                        <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i> Loading unmapped products...
                    </td>
                </tr>`;

            fetch(`index.php?controller=category&action=getUnmappedProducts&type=${typeFilter}&search=${encodeURIComponent(search)}&page=${page}`)
                .then(res => res.json())
                .then(data => {
                    refreshIcon.classList.remove('fa-spin');

                    if (!data.success || !data.items) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-rose-400">Failed to fetch products: ${data.message || 'Unknown error'}</td></tr>`;
                        return;
                    }

                    totalPages = data.total_pages || 1;
                    document.getElementById('txtShowingCount').textContent = data.items.length;
                    document.getElementById('txtTotalCount').textContent = data.total;
                    document.getElementById('txtCurrentPage').textContent = data.page;
                    document.getElementById('txtTotalPages').textContent = totalPages;

                    document.getElementById('btnPrevPage').disabled = (data.page <= 1);
                    document.getElementById('btnNextPage').disabled = (data.page >= totalPages);

                    if (data.items.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">All Products Mapped!</h3>
                                    <p class="text-xs text-zinc-500 mt-1">Every product in product & garment_product tables has a valid product_categories entry.</p>
                                </td>
                            </tr>`;
                        return;
                    }

                    let html = '';
                    data.items.forEach(item => {
                        const isJewel = (item.type === 'jewellery');
                        const catOptions = isJewel ? JEWEL_CATEGORIES : GARMENT_CATEGORIES;

                        let catSelectHtml = `<select id="catSelect_${item.type}_${item.id}" onchange="loadSubcategories('${item.type}', ${item.id})" class="bg-black border border-white/10 rounded px-2.5 py-1 text-xs text-white focus:outline-none focus:border-indigo-500 font-medium">`;
                        catSelectHtml += `<option value="0">-- Select Category --</option>`;

                        catOptions.forEach(c => {
                            const cId = isJewel ? c.subcat_id : c.garment_id;
                            const cName = isJewel ? c.categories_name : c.name;
                            const selected = (parseInt(cId) === parseInt(item.category_id)) ? 'selected' : '';
                            catSelectHtml += `<option value="${cId}" ${selected}>${escapeHtml(cName)}</option>`;
                        });
                        catSelectHtml += `</select>`;

                        let subSelectHtml = `<select id="subSelect_${item.type}_${item.id}" class="bg-black border border-white/10 rounded px-2.5 py-1 text-xs text-white focus:outline-none focus:border-indigo-500 font-medium">`;
                        subSelectHtml += `<option value="0">-- Select Subcategory --</option>`;
                        subSelectHtml += `</select>`;

                        const typeBadge = isJewel ? 
                            `<span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase border border-amber-500/20">Jewellery</span>` : 
                            `<span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 text-[10px] font-bold uppercase border border-purple-500/20">Garments</span>`;

                        html += `
                            <tr class="hover:bg-white/[0.02] transition-colors" id="row_${item.type}_${item.id}">
                                <td class="px-6 py-3 font-mono text-xs text-zinc-300 font-bold">#${item.id}</td>
                                <td class="px-6 py-3 font-mono text-xs text-indigo-400 font-bold">${escapeHtml(item.code)}</td>
                                <td class="px-6 py-3 text-xs text-white max-w-xs truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</td>
                                <td class="px-6 py-3 text-xs">${typeBadge}</td>
                                <td class="px-6 py-3 text-xs">${catSelectHtml}</td>
                                <td class="px-6 py-3 text-xs">${subSelectHtml}</td>
                                <td class="px-6 py-3 text-xs text-right">
                                    <button type="button" onclick="saveProductMapping('${item.type}', ${item.id}, this)" class="px-3 py-1 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded border border-emerald-500/30 text-xs font-bold transition-all cursor-pointer">
                                        <i class="fas fa-save mr-1"></i> Save Mapping
                                    </button>
                                </td>
                            </tr>`;
                    });
                    tableBody.innerHTML = html;

                    // Trigger initial subcategory loads for items with pre-selected category_id
                    data.items.forEach(item => {
                        if (item.category_id > 0) {
                            loadSubcategories(item.type, item.id, item.subcategory_id);
                        }
                    });
                })
                .catch(err => {
                    refreshIcon.classList.remove('fa-spin');
                    tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-rose-400">Network error: ${err}</td></tr>`;
                });
        }

        function loadSubcategories(type, id, preSelectedSub = 0) {
            const catSelect = document.getElementById(`catSelect_${type}_${id}`);
            const subSelect = document.getElementById(`subSelect_${type}_${id}`);

            if (!catSelect || !subSelect) return;
            const catId = catSelect.value;

            subSelect.innerHTML = `<option value="0">-- Loading... --</option>`;

            if (parseInt(catId) <= 0) {
                subSelect.innerHTML = `<option value="0">-- Select Subcategory --</option>`;
                return;
            }

            fetch(`index.php?controller=category&action=getSubcategories&type=${type}&cat_id=${catId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.subcategories) {
                        subSelect.innerHTML = `<option value="0">-- None --</option>`;
                        return;
                    }

                    let subHtml = `<option value="0">-- Select Subcategory --</option>`;
                    data.subcategories.forEach(s => {
                        const sId = (type === 'jewellery') ? s.subcat_id : s.sub_id;
                        const sName = (type === 'jewellery') ? s.name : s.sub_name;
                        const sel = (parseInt(sId) === parseInt(preSelectedSub)) ? 'selected' : '';
                        subHtml += `<option value="${sId}" ${sel}>${escapeHtml(sName)}</option>`;
                    });
                    subSelect.innerHTML = subHtml;
                })
                .catch(err => {
                    subSelect.innerHTML = `<option value="0">-- Error loading --</option>`;
                });
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                loadUnmappedProducts(newPage);
            }
        }

        function saveProductMapping(type, id, btn) {
            const catSelect = document.getElementById(`catSelect_${type}_${id}`);
            const subSelect = document.getElementById(`subSelect_${type}_${id}`);

            const catId = catSelect ? catSelect.value : 0;
            const subId = subSelect ? subSelect.value : 0;

            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Saving...`;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type);
            formData.append('category_id', catId);
            formData.append('subcategory_id', subId);

            fetch('index.php?controller=category&action=saveProductCategoryMapping', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.className = "px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded border border-emerald-500/30 text-xs font-bold";
                    btn.innerHTML = `<i class="fas fa-check"></i> Mapped`;
                    setTimeout(() => loadUnmappedProducts(currentPage), 800);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    alert('Error saving mapping: ' + data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert('Network error: ' + err);
            });
        }

        function runAutoFixAll() {
            if (!confirm("Are you sure you want to automatically assign and insert category mapping records for all unmapped products?")) return;

            const btn = document.getElementById('btnAutoFixAll');
            const icon = document.getElementById('fixIcon');
            const liveAlert = document.getElementById('liveActivityAlert');
            const actTitle = document.getElementById('activityTitle');
            const actMsg = document.getElementById('activityMsg');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.className = 'fas fa-spinner fa-spin';
            liveAlert.classList.remove('hidden');

            actTitle.textContent = "Auto-Fixing Unmapped Products...";
            actMsg.textContent = "Analyzing SKU codes, setting primary category columns, and populating product_categories...";

            fetch('index.php?controller=category&action=autoFixAllUnmapped', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.className = 'fas fa-magic';

                if (data.success) {
                    actTitle.textContent = "🎉 Auto-Fix Completed Successfully!";
                    actMsg.textContent = data.message;
                    setTimeout(() => {
                        liveAlert.classList.add('hidden');
                        loadUnmappedProducts(1);
                    }, 3000);
                } else {
                    actTitle.textContent = "❌ Auto-Fix Error";
                    actMsg.textContent = data.message;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.className = 'fas fa-magic';
                actTitle.textContent = "❌ Network Error";
                actMsg.textContent = err;
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;")
                       .replace(/</g, "&lt;")
                       .replace(/>/g, "&gt;")
                       .replace(/"/g, "&quot;")
                       .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>
