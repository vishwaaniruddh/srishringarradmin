<!DOCTYPE html>
<html lang="en">
<head>
    <title>Unmapped Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Slate / Zinc Theme) */
        :root {
            --wp-dark: #09090b;
            --wp-border: #e4e4e7;
            --wp-bg: #fafafa;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: #fafafa !important;
            color: #09090b !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Metric Cards */
        .shadcn-stat-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.15s ease;
        }
        .shadcn-stat-card:hover {
            border-color: #d4d4d8;
        }

        /* ShadCN Card Container */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .shadcn-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ShadCN Buttons */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 12px;
            height: 32px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            cursor: pointer;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #09090b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            font-family: inherit;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        .shadcn-btn-sm {
            height: 28px;
            padding: 0 9px;
            font-size: 11.5px;
            border-radius: 5px;
        }

        .shadcn-btn-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .shadcn-btn-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        /* Neutral Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
            flex-shrink: 0;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        /* Form Controls */
        .field-input, .field-select {
            height: 32px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
            font-family: inherit;
        }
        .field-input:focus, .field-select:focus {
            border-color: #09090b;
        }

        /* Table dropdowns */
        .table-select {
            height: 28px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 5px;
            padding: 0 8px;
            font-size: 11.5px;
            color: #09090b;
            outline: none;
            max-width: 170px;
            font-family: inherit;
        }
        .table-select:focus {
            border-color: #09090b;
        }

        /* Search input */
        .search-input-wrap {
            position: relative;
            width: 100%;
            max-width: 320px;
        }
        .search-input-wrap i.search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            font-size: 12px;
            pointer-events: none;
        }
        .search-input-wrap input {
            width: 100%;
            height: 32px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px 0 30px;
            font-size: 12.5px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
            font-family: inherit;
        }
        .search-input-wrap input:focus {
            border-color: #09090b;
        }

        /* ShadCN Table Standard */
        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .shadcn-table th {
            background: #fafafa;
            padding: 9px 16px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
            border-bottom: 1px solid #e4e4e7;
            text-align: left;
            white-space: nowrap;
        }
        .shadcn-table td {
            padding: 9px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f4f4f5;
            font-size: 12.5px;
            color: #09090b;
        }
        .shadcn-table tbody tr:hover td {
            background-color: #fafafa;
        }

        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }

        /* Floating Toast */
        #toast-box {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
        .toast-msg {
            background: #09090b;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 500;
            padding: 9px 15px;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: auto;
            animation: toastIn 0.15s ease forwards;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Unmapped Products Category Manager';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Breadcrumb Navigation -->
                    <div class="mb-4">
                        <a href="index.php?controller=category&action=index" class="text-xs text-zinc-500 hover:text-zinc-900 inline-flex items-center gap-1.5 transition-colors">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>Back to Category Hierarchy</span>
                        </a>
                    </div>

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">Unmapped Products Manager</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    product &rarr; product_categories
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Audit and assign taxonomy mapping records for products lacking active category relationships.</p>
                        </div>

                        <!-- Top Action Buttons -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="loadUnmappedProducts(1)" class="shadcn-btn">
                                <i class="fas fa-sync-alt text-[10px]" id="refreshIcon"></i>
                                <span>Refresh Data</span>
                            </button>
                            <button type="button" id="btnAutoFixAll" onclick="runAutoFixAll()" class="shadcn-btn shadcn-btn-primary">
                                <i class="fas fa-magic text-[10px]" id="fixIcon"></i>
                                <span>Auto-Fix All Unmapped</span>
                            </button>
                        </div>
                    </div>

                    <!-- Metrics Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Unmapped Jewellery</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono" id="statJewelCount"><?php echo number_format($jewelUnmappedCount); ?></span>
                                    <span class="text-xs text-zinc-400">items</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-gem"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Unmapped Garments</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono" id="statGarmentCount"><?php echo number_format($garmentUnmappedCount); ?></span>
                                    <span class="text-xs text-zinc-400">items</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-tshirt"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Missing Mappings</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono" id="statTotalCount"><?php echo number_format($totalUnmappedCount); ?></span>
                                    <span class="text-xs text-zinc-400">pending resolution</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Status Banner / Live Output -->
                    <div id="liveActivityAlert" class="shadcn-card hidden mb-6" style="border-color: #09090b;">
                        <div class="p-4 flex items-center gap-3">
                            <div class="w-7 h-7 rounded bg-zinc-900 text-white flex items-center justify-center text-xs">
                                <i class="fas fa-spinner fa-spin" id="activitySpinner"></i>
                            </div>
                            <div>
                                <h3 id="activityTitle" class="text-xs font-semibold text-zinc-900">Auto-Assigning Categories...</h3>
                                <p id="activityMsg" class="text-[11px] text-zinc-500 mt-0.5">Inserting category relationships for unmapped products...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Unmapped Products Table Card -->
                    <div class="shadcn-card mb-6">
                        <!-- Search & Filter Controls Header -->
                        <div class="shadcn-card-header">
                            <div class="flex items-center gap-2.5 flex-wrap flex-1">
                                <!-- Type Filter -->
                                <select id="selectTypeFilter" onchange="loadUnmappedProducts(1)" class="field-select">
                                    <option value="all">All Product Types</option>
                                    <option value="jewellery">Jewellery Only</option>
                                    <option value="garments">Garments Only</option>
                                </select>

                                <!-- Search Input -->
                                <div class="search-input-wrap">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" id="inputSearch" onkeyup="handleSearchKey(event)" placeholder="Search SKU code or product name..." autocomplete="off">
                                </div>

                                <button type="button" onclick="loadUnmappedProducts(1)" class="shadcn-btn shadcn-btn-primary">
                                    Search
                                </button>
                            </div>

                            <div class="text-xs text-zinc-500 font-mono">
                                Showing <span id="txtShowingCount" class="font-semibold text-zinc-900">0</span> of <span id="txtTotalCount" class="font-semibold text-zinc-900">0</span> unmapped products
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="overflow-x-auto max-h-[550px] overflow-y-auto custom-scrollbar">
                            <table class="shadcn-table">
                                <thead class="sticky top-0 z-10">
                                    <tr>
                                        <th style="width: 90px;">Product ID</th>
                                        <th style="width: 140px;">SKU Code</th>
                                        <th>Product Name</th>
                                        <th style="width: 110px;">Type</th>
                                        <th style="width: 190px;">Assign Main Category</th>
                                        <th style="width: 190px;">Assign Subcategory</th>
                                        <th style="width: 120px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-zinc-400 text-xs">
                                            <i class="fas fa-spinner fa-spin text-xl mb-2 block text-zinc-300"></i> Loading unmapped products...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="px-4 py-3 border-t border-zinc-100 bg-zinc-50/50 flex items-center justify-between text-xs text-zinc-500">
                            <div>
                                Page <span id="txtCurrentPage" class="font-semibold text-zinc-900">1</span> of <span id="txtTotalPages" class="font-semibold text-zinc-900">1</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="btnPrevPage" onclick="changePage(-1)" class="shadcn-btn shadcn-btn-sm" disabled>Previous</button>
                                <button type="button" id="btnNextPage" onclick="changePage(1)" class="shadcn-btn shadcn-btn-sm" disabled>Next</button>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-box"></div>

    <!-- Category Options Data (Passed from PHP) -->
    <script>
        const JEWEL_CATEGORIES = <?php echo json_encode($jewelCategories ?? []); ?>;
        const GARMENT_CATEGORIES = <?php echo json_encode($garmentCategories ?? []); ?>;

        let currentPage = 1;
        let totalPages = 1;

        // Toast notification helper
        function showToast(text, isError = false) {
            const toastBox = document.getElementById('toast-box');
            const msg = document.createElement('div');
            msg.className = 'toast-msg';
            msg.innerHTML = `
                <i class="fas ${isError ? 'fa-exclamation-circle text-rose-400' : 'fa-check-circle text-emerald-400'}"></i>
                <span>${text}</span>
            `;
            toastBox.appendChild(msg);
            setTimeout(() => {
                msg.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                msg.style.opacity = '0';
                msg.style.transform = 'translateY(6px)';
                setTimeout(() => msg.remove(), 250);
            }, 3500);
        }

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
                    <td colspan="7" class="py-12 text-center text-zinc-400 text-xs">
                        <i class="fas fa-spinner fa-spin text-xl mb-2 block text-zinc-300"></i> Loading unmapped products...
                    </td>
                </tr>`;

            fetch(`index.php?controller=category&action=getUnmappedProducts&type=${typeFilter}&search=${encodeURIComponent(search)}&page=${page}`)
                .then(res => res.json())
                .then(data => {
                    refreshIcon.classList.remove('fa-spin');

                    if (!data.success || !data.items) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-zinc-500 text-xs">Failed to fetch products: ${data.message || 'Unknown error'}</td></tr>`;
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
                                <td colspan="7" class="py-16 text-center">
                                    <div class="w-10 h-10 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-800 flex items-center justify-center mx-auto mb-2 text-base">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <h3 class="text-sm font-semibold text-zinc-900">All Products Mapped</h3>
                                    <p class="text-xs text-zinc-500 mt-0.5">Every product has a valid category relationship configured.</p>
                                </td>
                            </tr>`;
                        return;
                    }

                    let html = '';
                    data.items.forEach(item => {
                        const isJewel = (item.type === 'jewellery');
                        const catOptions = isJewel ? JEWEL_CATEGORIES : GARMENT_CATEGORIES;

                        let catSelectHtml = `<select id="catSelect_${item.type}_${item.id}" onchange="loadSubcategories('${item.type}', ${item.id})" class="table-select">`;
                        catSelectHtml += `<option value="0">-- Select Category --</option>`;

                        catOptions.forEach(c => {
                            const cId = isJewel ? c.subcat_id : c.garment_id;
                            const cName = isJewel ? c.categories_name : c.name;
                            const selected = (parseInt(cId) === parseInt(item.category_id)) ? 'selected' : '';
                            catSelectHtml += `<option value="${cId}" ${selected}>${escapeHtml(cName)}</option>`;
                        });
                        catSelectHtml += `</select>`;

                        let subSelectHtml = `<select id="subSelect_${item.type}_${item.id}" class="table-select">`;
                        subSelectHtml += `<option value="0">-- Select Subcategory --</option>`;
                        subSelectHtml += `</select>`;

                        const typeBadge = isJewel ? 
                            `<span class="shadcn-badge font-mono text-[10px]">Jewellery</span>` : 
                            `<span class="shadcn-badge font-mono text-[10px]">Garments</span>`;

                        html += `
                            <tr id="row_${item.type}_${item.id}">
                                <td class="font-mono text-xs text-zinc-400">#${item.id}</td>
                                <td>
                                    <span class="font-mono font-semibold text-zinc-900 text-xs">${escapeHtml(item.code)}</span>
                                </td>
                                <td class="text-xs text-zinc-900 max-w-xs truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</td>
                                <td>${typeBadge}</td>
                                <td>${catSelectHtml}</td>
                                <td>${subSelectHtml}</td>
                                <td class="text-right">
                                    <button type="button" onclick="saveProductMapping('${item.type}', ${item.id}, this)" class="shadcn-btn shadcn-btn-sm" title="Save category mapping">
                                        <i class="fas fa-check text-[10px]"></i>
                                        <span>Save</span>
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
                    tableBody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-zinc-500 text-xs">Network error: ${err}</td></tr>`;
                });
        }

        function loadSubcategories(type, id, preSelectedSub = 0) {
            const catSelect = document.getElementById(`catSelect_${type}_${id}`);
            const subSelect = document.getElementById(`subSelect_${type}_${id}`);

            if (!catSelect || !subSelect) return;
            const catId = catSelect.value;

            subSelect.innerHTML = `<option value="0">Loading...</option>`;

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
            btn.innerHTML = `<i class="fas fa-spinner fa-spin text-[10px]"></i>`;

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
                    btn.innerHTML = `<i class="fas fa-check text-[10px]"></i> Mapped`;
                    showToast(`Product #${id} mapped successfully`);
                    setTimeout(() => loadUnmappedProducts(currentPage), 600);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    showToast('Error saving mapping: ' + data.message, true);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                showToast('Network error: ' + err, true);
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
            icon.className = 'fas fa-spinner fa-spin text-[10px]';
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
                icon.className = 'fas fa-magic text-[10px]';

                if (data.success) {
                    actTitle.textContent = "Auto-Fix Completed Successfully";
                    actMsg.textContent = data.message;
                    showToast("Auto-fix completed successfully!");
                    setTimeout(() => {
                        liveAlert.classList.add('hidden');
                        loadUnmappedProducts(1);
                    }, 2500);
                } else {
                    actTitle.textContent = "Auto-Fix Failed";
                    actMsg.textContent = data.message;
                    showToast(data.message, true);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.className = 'fas fa-magic text-[10px]';
                actTitle.textContent = "Network Error";
                actMsg.textContent = err;
                showToast(err, true);
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
