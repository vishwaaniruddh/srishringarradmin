<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>Unmapped Category Fixer - Srishringarr Studio</title>
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
                        <i class="fas fa-link text-indigo-400"></i> Product Category Mapper & Fixer
                    </h1>
                    <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded uppercase tracking-wider border border-amber-500/20">
                        products &rarr; product_categories
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="loadUnmappedProducts()" class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 rounded-lg text-xs font-semibold border border-white/10 transition-all flex items-center gap-1.5 cursor-pointer">
                        <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh Data
                    </button>
                    <button type="button" id="btnFixAll" onclick="runBulkFix()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-bolt" id="fixAllIcon"></i>
                        <span id="fixAllText">Fix All Missing Records</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col overflow-y-auto custom-scrollbar gap-6">
                
                <!-- Overview Banner -->
                <div class="bg-gradient-to-r from-[#0e0e17] via-[#0a0a10] to-[#0d0a0e] border border-white/10 rounded-2xl p-6 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shrink-0">
                    <div class="space-y-1.5 max-w-2xl">
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-400"></i> Fetch & Insert Unmapped Product Categories
                        </h2>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            Products that exist in the <code class="bg-black/60 px-1.5 py-0.5 rounded text-amber-300 font-mono">products</code> table but are missing from the <code class="bg-black/60 px-1.5 py-0.5 rounded text-emerald-300 font-mono">product_categories</code> table will not display on store category pages. Use this tool to scan and insert missing relationship records in bulk or individually.
                        </p>
                    </div>

                    <!-- Target Database Selector Switcher -->
                    <div class="flex items-center bg-black/60 p-1.5 rounded-xl border border-white/10 shrink-0">
                        <button type="button" id="tabChild" onclick="switchTarget('child')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-indigo-600 text-white shadow-md">
                            <i class="fas fa-store"></i> Child Store (YN)
                            <span id="badgeChildCount" class="px-2 py-0.5 bg-black/40 text-indigo-200 text-[10px] rounded-full font-mono"><?php echo number_format($childUnmappedCount); ?></span>
                        </button>
                        <button type="button" id="tabParent" onclick="switchTarget('parent')" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-zinc-400 hover:text-white">
                            <i class="fas fa-database"></i> Parent Store (SS)
                            <span id="badgeParentCount" class="px-2 py-0.5 bg-zinc-800 text-zinc-300 text-[10px] rounded-full font-mono"><?php echo number_format($parentUnmappedCount); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 shrink-0">
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Child Store Unmapped</div>
                        <div id="statChildCount" class="text-3xl font-bold text-amber-400 font-mono"><?php echo number_format($childUnmappedCount); ?></div>
                        <div class="text-[11px] text-zinc-500 mt-1">In child <code class="text-zinc-400">products</code> without <code class="text-zinc-400">product_categories</code></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Parent Store Unmapped</div>
                        <div id="statParentCount" class="text-3xl font-bold text-indigo-400 font-mono"><?php echo number_format($parentUnmappedCount); ?></div>
                        <div class="text-[11px] text-zinc-500 mt-1">In parent <code class="text-zinc-400">product</code> without <code class="text-zinc-400">product_categories</code></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Active Target Database</div>
                        <div id="statActiveTarget" class="text-xl font-bold text-emerald-400 mt-1 uppercase tracking-wider">Child Store (Yosshitaneha)</div>
                        <div class="text-[11px] text-zinc-500 mt-1">Target table: <code id="statTargetTable" class="text-zinc-400 font-mono">product_categories</code></div>
                    </div>
                </div>

                <!-- Live Activity Console (Hidden by default, shown during fixes) -->
                <div id="liveActivityAlert" class="hidden p-5 rounded-2xl border border-indigo-500/30 bg-[#0d0d15] shadow-2xl flex flex-col gap-3 shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-spinner fa-spin text-indigo-400 text-lg"></i>
                            <div>
                                <h3 id="activityTitle" class="text-sm font-bold text-white">Processing Category Insertion</h3>
                                <p id="activityMsg" class="text-xs text-zinc-400 mt-0.5">Inserting category relationship records...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unmapped Products Table Card -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col overflow-hidden shadow-2xl shrink-0">
                    
                    <!-- Table Search & Controls Header -->
                    <div class="px-6 py-4 border-b border-white/5 bg-[#111] flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-80">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                                <input type="text" id="inputSearch" onkeyup="handleSearchKey(event)" placeholder="Search by SKU, Name or Category ID..." class="w-full bg-black border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500 transition-colors">
                            </div>
                            <button type="button" onclick="loadUnmappedProducts(1)" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-all">Search</button>
                        </div>
                        <div class="text-xs text-zinc-400 font-mono">
                            Showing <span id="txtShowingCount" class="font-bold text-white">0</span> of <span id="txtTotalCount" class="font-bold text-white">0</span> unmapped items
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto overflow-y-auto max-h-[500px] custom-scrollbar">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-[#111] sticky top-0 z-10 border-b border-white/5 text-xs text-zinc-400 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Product ID</th>
                                    <th class="px-6 py-3">SKU / Code</th>
                                    <th class="px-6 py-3">Product Name</th>
                                    <th class="px-6 py-3">Category ID</th>
                                    <th class="px-6 py-3">Category Name</th>
                                    <th class="px-6 py-3">Status</th>
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

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        let currentTarget = 'child';
        let currentPage = 1;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', () => {
            loadUnmappedProducts(1);
        });

        function switchTarget(target) {
            currentTarget = target;
            const tabChild = document.getElementById('tabChild');
            const tabParent = document.getElementById('tabParent');
            const statActiveTarget = document.getElementById('statActiveTarget');
            const statTargetTable = document.getElementById('statTargetTable');
            const fixAllText = document.getElementById('fixAllText');

            if (target === 'child') {
                tabChild.className = "px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-indigo-600 text-white shadow-md cursor-pointer";
                tabParent.className = "px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-zinc-400 hover:text-white cursor-pointer";
                statActiveTarget.textContent = "Child Store (Yosshitaneha)";
                statActiveTarget.className = "text-xl font-bold text-emerald-400 mt-1 uppercase tracking-wider";
                statTargetTable.textContent = "products -> product_categories";
                fixAllText.textContent = "Fix All Child Category Records";
            } else {
                tabParent.className = "px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 bg-indigo-600 text-white shadow-md cursor-pointer";
                tabChild.className = "px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 text-zinc-400 hover:text-white cursor-pointer";
                statActiveTarget.textContent = "Parent Store (Srishringarr)";
                statActiveTarget.className = "text-xl font-bold text-indigo-400 mt-1 uppercase tracking-wider";
                statTargetTable.textContent = "product -> product_categories";
                fixAllText.textContent = "Fix All Parent Category Records";
            }

            loadUnmappedProducts(1);
        }

        function handleSearchKey(event) {
            if (event.key === 'Enter') {
                loadUnmappedProducts(1);
            }
        }

        function loadUnmappedProducts(page = 1) {
            currentPage = page;
            const search = document.getElementById('inputSearch').value.trim();
            const tableBody = document.getElementById('tableBody');
            const refreshIcon = document.getElementById('refreshIcon');

            refreshIcon.classList.add('fa-spin');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                        <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i> Loading unmapped products...
                    </td>
                </tr>`;

            fetch(`index.php?controller=sync&action=getUnmappedProducts&target=${currentTarget}&search=${encodeURIComponent(search)}&page=${page}`)
                .then(res => res.json())
                .then(data => {
                    refreshIcon.classList.remove('fa-spin');

                    if (!data.success || !data.items) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-rose-400">Failed to fetch unmapped products: ${data.message || 'Unknown error'}</td></tr>`;
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
                                    <p class="text-xs text-zinc-500 mt-1">No unmapped category records found for ${currentTarget === 'child' ? 'Child Store' : 'Parent Store'}.</p>
                                </td>
                            </tr>`;
                        return;
                    }

                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-3 font-mono text-xs text-zinc-300 font-bold">#${item.id}</td>
                                <td class="px-6 py-3 font-mono text-xs text-indigo-400 font-bold">${escapeHtml(item.sku)}</td>
                                <td class="px-6 py-3 text-xs text-white max-w-xs truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</td>
                                <td class="px-6 py-3 font-mono text-xs text-amber-400 font-bold">${item.category_id}</td>
                                <td class="px-6 py-3 text-xs text-zinc-300">${escapeHtml(item.category_name)}</td>
                                <td class="px-6 py-3 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase border border-amber-500/20">
                                        Missing Category Record
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-right">
                                    <button type="button" onclick="fixSingleProduct(${item.id}, '${item.type || 'jewellery'}', this)" class="px-3 py-1 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded border border-emerald-500/30 text-xs font-bold transition-all cursor-pointer">
                                        <i class="fas fa-plus-circle mr-1"></i> Insert Record
                                    </button>
                                </td>
                            </tr>`;
                    });
                    tableBody.innerHTML = html;
                })
                .catch(err => {
                    refreshIcon.classList.remove('fa-spin');
                    tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-8 text-center text-rose-400">Network error: ${err}</td></tr>`;
                });
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                loadUnmappedProducts(newPage);
            }
        }

        function fixSingleProduct(id, type, btn) {
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Inserting...`;

            const formData = new FormData();
            formData.append('target', currentTarget);
            formData.append('id', id);
            formData.append('type', type);

            fetch('index.php?controller=sync&action=fixUnmappedProducts', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.className = "px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded border border-emerald-500/30 text-xs font-bold";
                    btn.innerHTML = `<i class="fas fa-check"></i> Inserted`;
                    setTimeout(() => loadUnmappedProducts(currentPage), 800);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origText;
                alert('Network error: ' + err);
            });
        }

        function runBulkFix() {
            const targetName = currentTarget === 'child' ? 'Child Store (Yosshitaneha)' : 'Parent Store (Srishringarr)';
            if (!confirm(`Are you sure you want to insert all missing category relationship records into ${targetName}?`)) return;

            const btn = document.getElementById('btnFixAll');
            const icon = document.getElementById('fixAllIcon');
            const liveAlert = document.getElementById('liveActivityAlert');
            const actTitle = document.getElementById('activityTitle');
            const actMsg = document.getElementById('activityMsg');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.className = 'fas fa-spinner fa-spin';
            liveAlert.classList.remove('hidden');

            actTitle.textContent = `Inserting Category Records for ${targetName}...`;
            actMsg.textContent = "Executing database transaction to insert missing category relationships...";

            const formData = new FormData();
            formData.append('target', currentTarget);

            fetch('index.php?controller=sync&action=fixUnmappedProducts', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.className = 'fas fa-bolt';

                if (data.success) {
                    actTitle.textContent = "🎉 Category Insertion Completed!";
                    actMsg.textContent = data.message;
                    setTimeout(() => {
                        liveAlert.classList.add('hidden');
                        loadUnmappedProducts(1);
                    }, 2500);
                } else {
                    actTitle.textContent = "❌ Insertion Failed";
                    actMsg.textContent = data.message;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.className = 'fas fa-bolt';
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
