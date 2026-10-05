<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Matching yn/admin/products.php) */
        :root {
            --wp-dark: #09090b;
            --wp-blue: #2563eb;
            --wp-border: #e4e4e7;
            --wp-bg: #fafafa;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: var(--wp-bg) !important;
            color: var(--wp-text) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Dashboard Header Banner */
        .dashboard-header-banner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            gap: 14px;
            flex-wrap: wrap;
        }

        .dashboard-header-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .dashboard-greeting {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dashboard-greeting h1 {
            font-size: 20px;
            font-weight: 600;
            color: #09090b;
            letter-spacing: -0.02em;
            margin: 0;
            line-height: 1.2;
        }

        .dashboard-subtitle {
            font-size: 12.5px;
            color: #71717a;
            margin: 0;
            line-height: 1.4;
            font-weight: 400;
        }

        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            line-height: 1.2;
            flex-shrink: 0;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        .dashboard-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        /* ShadCN Button Standards */
        .button, .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 11px;
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

        .button:hover, .shadcn-btn:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        .button-primary, .shadcn-btn-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        .button-primary:hover, .shadcn-btn-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        .shadcn-btn-outline {
            background: #ffffff;
            border-color: #e4e4e7;
            color: #09090b;
        }
        .shadcn-btn-outline:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
        }

        .shadcn-btn-ghost {
            background: transparent;
            border-color: transparent;
            color: #71717a;
            box-shadow: none;
        }
        .shadcn-btn-ghost:hover {
            background: #f4f4f5;
            color: #09090b;
        }

        /* Export Dropdown Popover */
        .dropdown-menu-wrap {
            position: relative;
            display: inline-block;
        }

        .dropdown-popover {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            list-style: none;
            margin: 0;
            padding: 5px;
            z-index: 100;
            min-width: 220px;
        }
        .dropdown-popover.open {
            display: block;
            animation: popoverFadeIn 0.12s ease;
        }
        @keyframes popoverFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 500;
            color: #09090b;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.12s ease;
        }
        .dropdown-item:hover {
            background: #f4f4f5;
            color: #09090b;
        }

        /* ShadCN Card Container */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .shadcn-card-padded {
            padding: 12px 16px;
        }

        .shadcn-card-header {
            padding: 10px 16px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
        }

        /* Filter Controls */
        .form-control-shadcn {
            height: 34px;
            font-size: 12.5px;
            font-family: inherit;
            color: #09090b;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px;
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
        }
        .form-control-shadcn:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }

        .search-clear-btn {
            display: none;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 2px 4px;
            line-height: 1;
        }
        .search-clear-btn:hover {
            color: #09090b;
        }

        /* ShadCN Table Standard */
        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: none;
            box-shadow: none;
        }

        .shadcn-table th {
            background: #fafafa;
            padding: 9px 14px;
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
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f4f4f5;
            font-size: 13px;
            color: #09090b;
            font-weight: 400;
        }

        .shadcn-table tbody tr:hover td {
            background-color: #fafafa;
        }

        .shadcn-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-name-link {
            font-weight: 500;
            color: #09090b;
            text-decoration: none;
            font-size: 13px;
            line-height: 1.4;
            display: block;
            max-width: 360px;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .product-name-link:hover {
            color: #2563eb;
        }

        /* Skeletons */
        .skeleton {
            background: linear-gradient(90deg, #f4f4f5 25%, #e4e4e7 50%, #f4f4f5 75%);
            background-size: 200% 100%;
            animation: skeletonLoading 1.5s infinite;
            border-radius: 4px;
        }
        @keyframes skeletonLoading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Floating Toast Box */
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
            padding: 8px 14px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
            <!-- Top Header -->
            <?php 
            $pageTitle = 'All Products';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-gray-50/50">
                <div class="page-container">

                    <!-- Header Banner (Exact yn/admin layout) -->
                    <div class="dashboard-header-banner">
                        <div class="dashboard-header-info">
                            <div class="dashboard-greeting">
                                <h1>Products Catalog</h1>
                                <span class="shadcn-badge">
                                    <i class="fa-solid fa-boxes-stacked" style="margin-right: 5px;"></i> <span id="header-count">—</span>&nbsp;ITEMS
                                </span>
                            </div>
                            <p class="dashboard-subtitle">
                                Manage your store inventory, pricing, SKUs, and category bindings.
                            </p>
                        </div>
                        <div class="dashboard-actions">
                            <a href="index.php?controller=product&action=add" class="shadcn-btn shadcn-btn-primary">
                                <i class="fa-solid fa-plus"></i> Add Product
                            </a>
                            <a href="index.php?controller=sync&action=index" class="shadcn-btn shadcn-btn-outline">
                                <i class="fa-solid fa-tags"></i> POS Price Sync
                            </a>
                            <a href="import_archive.php" class="shadcn-btn shadcn-btn-outline">
                                <i class="fa-solid fa-folder-tree"></i> Archive Import
                            </a>
                            <a href="index.php?controller=product&action=import" class="shadcn-btn shadcn-btn-outline">
                                <i class="fa-solid fa-file-csv"></i> CSV Import
                            </a>

                            <!-- Export Dropdown -->
                            <div class="dropdown-menu-wrap" id="export-dropdown-wrap">
                                <button type="button" onclick="toggleExportMenu()" class="shadcn-btn shadcn-btn-outline">
                                    <i class="fa-solid fa-download"></i> Export <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 2px;"></i>
                                </button>
                                <ul id="export-menu" class="dropdown-popover">
                                    <li>
                                        <a href="javascript:void(0)" onclick="exportProducts('excel')" class="dropdown-item">
                                            <i class="fa-solid fa-file-excel" style="color: #16a34a; width: 16px;"></i> Filtered Excel (.xlsx)
                                        </a>
                                    </li>
                                    <li style="border-top: 1px solid #f4f4f5;">
                                        <a href="javascript:void(0)" onclick="exportProducts('csv')" class="dropdown-item">
                                            <i class="fa-solid fa-file-csv" style="color: #0284c7; width: 16px;"></i> Filtered CSV (.csv)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Trash Navigation Bar (Exact yn/admin format) -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                        <!-- Left: Status Tabs -->
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                            <button type="button" id="tab-all" onclick="setQuickTab('all', this)" class="shadcn-btn shadcn-btn-primary" style="height: 30px; font-size: 12px; padding: 0 12px; font-weight: 500;">
                                All (<span id="tab-count-all">—</span>)
                            </button>
                            <button type="button" id="tab-featured" onclick="setQuickTab('featured', this)" class="shadcn-btn shadcn-btn-outline" style="height: 30px; font-size: 12px; padding: 0 12px; font-weight: 500;">
                                <i class="fa-solid fa-star" style="font-size: 10px; margin-right: 4px; color: #f59e0b;"></i> Featured (<span id="tab-count-featured">—</span>)
                            </button>
                            <button type="button" id="tab-instock" onclick="setQuickTab('instock', this)" class="shadcn-btn shadcn-btn-outline" style="height: 30px; font-size: 12px; padding: 0 12px; font-weight: 500;">
                                In Stock (<span id="tab-count-instock">—</span>)
                            </button>
                        </div>

                        <!-- Right: Trash Counter -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a href="index.php?controller=product&action=bulkDelete" class="shadcn-btn shadcn-btn-outline" style="height: 30px; font-size: 12px; padding: 0 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #ef4444; border-color: #fca5a5; background: #fef2f2;" title="Bulk Delete Products">
                                <i class="fa-solid fa-trash-can" style="font-size: 11px;"></i>
                                <span>Trash</span>
                                <span id="tab-count-trash" style="background: #ef4444; color: #ffffff; font-size: 10.5px; padding: 1px 6px; border-radius: 999px; font-weight: 600;">
                                    0
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Filters Card -->
                    <div class="shadcn-card" style="margin-bottom: 16px;">
                        <div class="shadcn-card-padded">
                            <form id="filterForm" onsubmit="event.preventDefault(); loadProducts(1);" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin: 0;">
                                <!-- Search Box -->
                                <div style="position: relative; flex: 1; min-width: 220px;">
                                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #a1a1aa; font-size: 12px; pointer-events: none;"></i>
                                    <input type="text" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, SKU, description..." class="form-control-shadcn" style="padding-left: 32px !important; width: 100%;">
                                    <button type="button" id="searchClearBtn" class="search-clear-btn" onclick="clearSearch()">&times;</button>
                                </div>

                                <!-- Categories Dropdown -->
                                <div style="min-width: 160px;">
                                    <select id="categoryFilter" class="form-control-shadcn" style="width: 100%;" onchange="loadProducts(1)">
                                        <option value="">All Categories</option>
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $parent => $data): ?>
                                                <optgroup label="<?php echo htmlspecialchars($parent); ?> (<?php echo $data['count']; ?>)">
                                                    <?php foreach ($data['children'] as $value => $childData): ?>
                                                        <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $category == $value ? 'selected' : ''; ?>>
                                                            <?php echo $childData['name']; ?> (<?php echo $childData['count']; ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Stock Status Dropdown -->
                                <div style="min-width: 140px;">
                                    <select id="stockFilter" class="form-control-shadcn" style="width: 100%;" onchange="onStockFilterChange()">
                                        <option value="">All Stock Status</option>
                                        <option value="instock">In Stock (&gt; 0)</option>
                                        <option value="lowstock">Low Stock (1–5)</option>
                                        <option value="outofstock">Out of Stock (0)</option>
                                    </select>
                                </div>

                                <!-- Featured / Items Dropdown -->
                                <div style="min-width: 125px;">
                                    <select id="featuredFilter" class="form-control-shadcn" style="width: 100%;" onchange="loadProducts(1)">
                                        <option value="">All Items</option>
                                        <option value="1">Starred Only</option>
                                        <option value="0">Unstarred Only</option>
                                    </select>
                                </div>

                                <!-- Store Presence Dropdown (Parent / Child) -->
                                <div style="min-width: 175px;">
                                    <select id="storePresenceFilter" class="form-control-shadcn" style="width: 100%;" onchange="loadProducts(1)">
                                        <option value="">All Store Presence</option>
                                        <option value="in_child">Both Stores (Parent &amp; Child)</option>
                                        <option value="parent_only">Parent Only (Not in Child)</option>
                                    </select>
                                </div>

                                <button type="submit" class="shadcn-btn shadcn-btn-primary" style="height: 34px; font-size: 12.5px; padding: 0 14px;">
                                    <i class="fa-solid fa-filter"></i> Filter
                                </button>
                                
                                <button type="button" id="resetFiltersBtn" onclick="resetAllFilters()" class="shadcn-btn shadcn-btn-ghost" style="display: none; height: 34px; font-size: 12.5px;" title="Clear Filters">
                                    <i class="fa-solid fa-xmark"></i> Reset
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Multi-Category Notice Container -->
                    <div id="duplicateNoticeContainer"></div>

                    <!-- Products Table Card -->
                    <div class="shadcn-card">
                        <div class="shadcn-card-header">
                            <div style="font-size: 12.5px; color: #52525b;">
                                <span id="table-record-range">Loading products...</span>
                            </div>
                            <div id="table-page-range" style="font-size: 11.5px; color: #71717a;">
                                Page 1
                            </div>
                        </div>

                        <div style="overflow-x: auto;">
                            <table class="shadcn-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">IMAGE</th>
                                        <th style="max-width: 320px; width: 30%;">PRODUCT TITLE &amp; SKU</th>
                                        <th style="white-space: nowrap; width: 130px;">CATEGORY</th>
                                        <th style="white-space: nowrap; width: 110px;">PRICE</th>
                                        <th style="white-space: nowrap; width: 110px;">INVENTORY</th>
                                        <th style="white-space: nowrap; width: 140px;">STORE PRESENCE</th>
                                        <th style="width: 50px; text-align: center;"><i class="fa-solid fa-star" title="Featured" style="font-size: 11px;"></i></th>
                                        <th style="width: 100px; white-space: nowrap;">ADDED ON</th>
                                        <th style="width: 75px; text-align: center; white-space: nowrap;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="products-body">
                                    <!-- AJAX loaded rows -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div id="pagination-container" style="border-top: 1px solid #f4f4f5; padding: 12px 16px;"></div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Floating Toast Message Box -->
    <div id="toast-box"></div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    let currentPage = 1;
    let availableOnly = false;
    let quickTab = 'all';

    // Toast Notification
    function showToast(text, icon = 'check') {
        const box = document.getElementById('toast-box');
        const msg = document.createElement('div');
        msg.className = 'toast-msg';
        msg.innerHTML = `<i class="fa-solid fa-${icon}"></i> <span>${text}</span>`;
        box.appendChild(msg);
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(6px)';
            msg.style.transition = 'all 0.2s ease';
            setTimeout(() => msg.remove(), 250);
        }, 2500);
    }

    // Copy SKU to clipboard
    function copySku(sku) {
        if (!sku) return;
        navigator.clipboard.writeText(sku).then(() => {
            showToast(`SKU "${sku}" copied to clipboard`, 'copy');
        }).catch(() => {
            showToast(`Failed to copy SKU`, 'circle-exclamation');
        });
    }

    // Export dropdown toggle
    function toggleExportMenu() {
        const menu = document.getElementById('export-menu');
        if (menu) menu.classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('export-dropdown-wrap');
        if (wrap && !wrap.contains(e.target)) {
            const menu = document.getElementById('export-menu');
            if (menu) menu.classList.remove('open');
        }
    });

    // Skeletons generator
    function renderSkeletons(count = 8) {
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
                <tr>
                    <td><div class="skeleton" style="width: 36px; height: 46px; border-radius: 4px;"></div></td>
                    <td>
                        <div class="skeleton" style="width: 240px; height: 14px; margin-bottom: 6px;"></div>
                        <div class="skeleton" style="width: 90px; height: 11px;"></div>
                    </td>
                    <td><div class="skeleton" style="width: 100px; height: 20px; border-radius: 4px;"></div></td>
                    <td><div class="skeleton" style="width: 70px; height: 15px;"></div></td>
                    <td><div class="skeleton" style="width: 80px; height: 20px; border-radius: 4px;"></div></td>
                    <td><div class="skeleton" style="width: 90px; height: 20px; border-radius: 4px;"></div></td>
                    <td style="text-align: center;"><div class="skeleton" style="width: 16px; height: 16px; border-radius: 50%; margin: 0 auto;"></div></td>
                    <td><div class="skeleton" style="width: 75px; height: 13px;"></div></td>
                    <td style="text-align: center;"><div class="skeleton" style="width: 40px; height: 18px; margin: 0 auto;"></div></td>
                </tr>
            `;
        }
        return html;
    }

    // Quick Tab Selector
    function setQuickTab(tab, el) {
        quickTab = tab;
        document.querySelectorAll('#tab-all, #tab-featured, #tab-instock').forEach(b => {
            b.className = 'shadcn-btn shadcn-btn-outline';
        });
        if (el) {
            el.className = 'shadcn-btn shadcn-btn-primary';
        }

        if (tab === 'all') {
            availableOnly = false;
            document.getElementById('featuredFilter').value = '';
            document.getElementById('stockFilter').value = '';
            document.getElementById('storePresenceFilter').value = '';
        } else if (tab === 'featured') {
            availableOnly = false;
            document.getElementById('featuredFilter').value = '1';
        } else if (tab === 'instock') {
            availableOnly = false;
            document.getElementById('stockFilter').value = 'instock';
        }
        checkFilterActive();
        loadProducts(1);
    }

    function onStockFilterChange() {
        const val = document.getElementById('stockFilter').value;
        if (val === 'instock') {
            availableOnly = true;
        } else {
            availableOnly = false;
        }
        loadProducts(1);
    }

    // Export handler
    function exportProducts(format) {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const storePresence = document.getElementById('storePresenceFilter') ? document.getElementById('storePresenceFilter').value : '';
        const menu = document.getElementById('export-menu');
        if (menu) menu.classList.remove('open');
        window.location.href = `index.php?controller=product&action=export&format=${format}&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&store_presence=${encodeURIComponent(storePresence)}&available_only=${availableOnly ? 1 : 0}`;
    }

    async function loadProducts(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const featured = document.getElementById('featuredFilter').value;
        const stock = document.getElementById('stockFilter').value;
        const storePresence = document.getElementById('storePresenceFilter') ? document.getElementById('storePresenceFilter').value : '';

        checkFilterActive();

        const tbody = document.getElementById('products-body');
        const pagination = document.getElementById('pagination-container');
        tbody.innerHTML = renderSkeletons(8);

        try {
            const isAvail = (availableOnly || stock === 'instock') ? 1 : 0;
            const response = await fetch(`index.php?controller=api&action=products&page=${page}&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&featured=${featured}&available_only=${isAvail}&store_presence=${encodeURIComponent(storePresence)}`);
            const data = await response.json();

            // Total and Stats Update
            const total = data.totalRecords !== undefined ? data.totalRecords : (data.stats ? data.stats.total : 0);
            const inStockCount = data.stats ? data.stats.in_stock : 0;
            const oosCount = data.stats ? data.stats.out_of_stock : 0;
            const featuredCount = data.stats ? data.stats.featured : 0;

            document.getElementById('header-count').textContent = Number(total).toLocaleString('en-IN');
            document.getElementById('tab-count-all').textContent = Number(total).toLocaleString('en-IN');
            document.getElementById('tab-count-featured').textContent = Number(featuredCount).toLocaleString('en-IN');
            document.getElementById('tab-count-instock').textContent = Number(inStockCount).toLocaleString('en-IN');
            document.getElementById('tab-count-trash').textContent = Number(oosCount).toLocaleString('en-IN');

            // Range display (Showing 1–20 of X products)
            const rangeEl = document.getElementById('table-record-range');
            const pageRangeEl = document.getElementById('table-page-range');
            if (total === 0) {
                rangeEl.textContent = 'No products found';
                if (pageRangeEl) pageRangeEl.textContent = 'Page 0 of 0';
            } else {
                const startRange = (data.currentPage - 1) * 20 + 1;
                const endRange = Math.min(data.currentPage * 20, total);
                rangeEl.innerHTML = `Showing <strong>${startRange}–${endRange}</strong> of <strong>${Number(total).toLocaleString('en-IN')}</strong> products`;
                if (pageRangeEl) pageRangeEl.textContent = `Page ${data.currentPage} of ${data.totalPages || 1}`;
            }

            if (!data.products || data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align: center; color: #71717a; padding: 40px;">
                            <i class="fa-solid fa-boxes-stacked" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                            No products found matching filters.
                        </td>
                    </tr>
                `;
                pagination.innerHTML = '';
                const noticeContainer = document.getElementById('duplicateNoticeContainer');
                if (noticeContainer) noticeContainer.innerHTML = '';
                return;
            }

            // SKU Map for duplicate check
            const skuMap = {};
            data.products.forEach(p => {
                const key = (p.code || '').trim().toUpperCase() + '_' + (p.type || 'jewellery').toLowerCase();
                if (!skuMap[key]) {
                    skuMap[key] = { count: 0 };
                }
                skuMap[key].count++;
            });

            let html = '';
            data.products.forEach((p) => {
                // Name
                const rawName = (p.name || '').trim();
                const cleanName = (rawName && rawName.toLowerCase() !== 'jewellery' && rawName.toLowerCase() !== 'garments' && rawName.toLowerCase() !== 'garment_product') ? rawName : '';
                const displayName = cleanName ? cleanName.toLowerCase().replace(/\b\w/g, l => l.toUpperCase()) : 'Unnamed Product (' + p.code + ')';

                // Qty & Inventory
                const qtyVal = parseFloat(p.details ? p.details.quantity : 0) || 0;
                let inventoryBadge = '';
                if (qtyVal <= 0) {
                    inventoryBadge = `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;"><i class="fa-solid fa-circle-xmark" style="font-size: 10px;"></i> Out of stock</span>`;
                } else if (qtyVal <= 5) {
                    inventoryBadge = `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><i class="fa-solid fa-triangle-exclamation" style="font-size: 10px;"></i> Low: ${qtyVal}</span>`;
                } else {
                    inventoryBadge = `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><i class="fa-solid fa-circle-check" style="font-size: 10px;"></i> In Stock (${qtyVal})</span>`;
                }

                // Store presence badge (Parent / Child sync status)
                let presenceHtml = '';
                if (p.in_child) {
                    const childSlug = p.child_product && p.child_product.slug ? p.child_product.slug : '';
                    presenceHtml = `
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="display: inline-flex; align-items: center; gap: 5px; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;" title="Live on Parent POS & YN Web Storefront">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e;"></span>
                                Both Stores
                            </span>
                            ${childSlug ? `<a href="https://yosshitaneha.com/product/${encodeURIComponent(childSlug)}/" target="_blank" rel="noopener noreferrer" style="color: #a1a1aa; font-size: 10.5px; padding: 2px;" title="View on Child Storefront"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>` : ''}
                        </div>
                    `;
                } else {
                    presenceHtml = `
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <span style="display: inline-flex; align-items: center; gap: 5px; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 500; background: #f4f4f5; color: #52525b; border: 1px solid #e4e4e7;" title="Present in Parent Store only">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #94a3b8;"></span>
                                Parent Only
                            </span>
                            <a href="index.php?controller=sync&action=index" style="font-size: 10.5px; color: #71717a; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" onmouseover="this.style.color='#09090b'" onmouseout="this.style.color='#71717a'" title="Check Sync Settings / Eligible Categories">
                                ${p.sync_eligible ? '<i class="fa-solid fa-rotate" style="font-size: 9px; color: #d97706;"></i> <span style="color: #b45309; font-weight: 500;">Sync Eligible</span>' : '<i class="fa-solid fa-sliders" style="font-size: 9px;"></i> Check Sync'}
                            </a>
                        </div>
                    `;
                }

                // Pricing
                const rentPrice = parseFloat(p.details ? p.details.rent_price : 0) || 0;
                const salePrice = parseFloat(p.details ? p.details.sale_price : 0) || 0;

                // Featured
                const isFeatured = (p.featured == 1);

                // Category
                const subcatName = (p.details && p.details.subcategory_name ? p.details.subcategory_name : (p.details ? p.details.category_name : '')).trim();
                const displayCat = subcatName ? subcatName.split(', ')[0] : 'Designer Jewellery';

                // Image
                const imgPath = (p.details && p.details.image_path) ? p.details.image_path : 'assets/default-product.jpg';

                // Sku duplicate info
                const key = (p.code || '').trim().toUpperCase() + '_' + (p.type || 'jewellery').toLowerCase();
                const skuInfo = skuMap[key];
                const isDup = skuInfo && skuInfo.count > 1;

                // Date
                let dateFormatted = '—';
                if (p.created_at) {
                    try {
                        const d = new Date(p.created_at);
                        if (!isNaN(d.getTime())) {
                            dateFormatted = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        }
                    } catch(e) {}
                }

                html += `
                    <tr>
                        <td>
                            <div style="width: 36px; height: 46px; border-radius: 4px; overflow: hidden; position: relative; background: #f4f4f5; border: 1px solid #e4e4e7;">
                                <img src="${imgPath}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.src='assets/default-product.jpg'">
                            </div>
                        </td>
                        <td style="max-width: 320px; width: 30%;">
                            <div style="max-width: 320px;">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" class="product-name-link" title="${displayName}">
                                    ${displayName}
                                </a>
                                <div style="margin-top: 3px;">
                                    <span style="font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; color: #71717a; text-transform: uppercase; cursor: pointer;" onclick="copySku('${p.code}')" title="Click to copy SKU">
                                        ${p.code || 'NO-SKU'}
                                    </span>
                                    ${isDup ? `<span style="margin-left: 6px; font-size: 10.5px; color: #d97706; font-weight: 600;">(${skuInfo.count} listings)</span>` : ''}
                                </div>
                            </div>
                        </td>
                        <td style="white-space: nowrap; width: 130px;">
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11.5px; font-weight: 450; background: #f4f4f5; border: 1px solid #e4e4e7; color: #52525b; white-space: nowrap;">
                                ${displayCat}
                            </span>
                        </td>
                        <td style="white-space: nowrap; width: 110px;">
                            <div style="font-weight: 600; font-size: 13px; color: #09090b; white-space: nowrap;">
                                ₹${rentPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </div>
                            ${salePrice > 0 ? `<div style="font-size: 11px; color: #71717a; margin-top: 1px; white-space: nowrap;">Sale: ₹${salePrice.toLocaleString('en-IN')}</div>` : ''}
                        </td>
                        <td style="white-space: nowrap; width: 110px;">
                            ${inventoryBadge}
                        </td>
                        <td style="white-space: nowrap; width: 140px;">
                            ${presenceHtml}
                        </td>
                        <td style="text-align: center;">
                            <button type="button" onclick="toggleFeaturedRow(${p.id}, '${p.type}', ${isFeatured ? 0 : 1})" style="background: none; border: none; cursor: pointer; padding: 4px; font-size: 13px; color: ${isFeatured ? '#f59e0b' : '#d4d4d8'}; transition: color 0.15s ease;" title="${isFeatured ? 'Starred' : 'Not Starred'}">
                                <i class="fa-${isFeatured ? 'solid' : 'regular'} fa-star"></i>
                            </button>
                        </td>
                        <td style="white-space: nowrap; font-size: 12px; color: #71717a;">
                            ${dateFormatted}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" title="Edit / View" style="color: #71717a; font-size: 13px; padding: 4px; text-decoration: none; transition: color 0.12s;">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="deleteProductRow(${p.id}, '${p.type}')" title="Delete" style="color: #ef4444; font-size: 13px; padding: 4px; text-decoration: none; transition: color 0.12s;">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            renderPagination(data.totalPages, data.currentPage);

        } catch (error) {
            console.error('Error loading products:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; color: #ef4444; padding: 30px;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Failed to load products. Please check server logs or refresh.
                    </td>
                </tr>
            `;
        }
    }

    // Toggle Featured AJAX
    async function toggleFeaturedRow(id, type, newStatus) {
        try {
            const res = await fetch('index.php?controller=api&action=toggleFeatured', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type, status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                showToast(newStatus ? 'Product marked as featured' : 'Product removed from featured');
                loadProducts(currentPage);
            } else {
                showToast(data.error || 'Failed to update featured', 'circle-exclamation');
            }
        } catch (e) {
            showToast('Error toggling featured status', 'circle-exclamation');
        }
    }

    // Delete Product Row
    async function deleteProductRow(id, type) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        window.location.href = `index.php?controller=product&action=delete&id=${id}&type=${type}`;
    }

    // Pagination Renderer
    function renderPagination(totalPages, page) {
        const container = document.getElementById('pagination-container');
        if (!container) return;
        if (!totalPages || totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">';
        html += `<div style="font-size: 12px; color: #71717a;">Page ${page} of ${totalPages}</div>`;
        html += '<div style="display: flex; align-items: center; gap: 4px;">';

        // Prev Button
        html += `
            <button onclick="loadProducts(${page - 1})" 
                    ${page <= 1 ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''} 
                    class="shadcn-btn shadcn-btn-outline" style="height: 30px; padding: 0 10px; font-size: 12px;">
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i> Previous
            </button>
        `;

        // Number Buttons
        const start = Math.max(1, page - 2);
        const end = Math.min(totalPages, page + 2);

        if (start > 1) {
            html += `<button onclick="loadProducts(1)" class="shadcn-btn shadcn-btn-outline" style="height: 30px; min-width: 30px; padding: 0 8px; font-size: 12px;">1</button>`;
            if (start > 2) html += `<span style="color: #a1a1aa; padding: 0 4px;">…</span>`;
        }

        for (let i = start; i <= end; i++) {
            const isActive = i === page;
            html += `
                <button onclick="loadProducts(${i})" 
                        class="shadcn-btn ${isActive ? 'shadcn-btn-primary' : 'shadcn-btn-outline'}" 
                        style="height: 30px; min-width: 30px; padding: 0 8px; font-size: 12px;">
                    ${i}
                </button>
            `;
        }

        if (end < totalPages) {
            if (end < totalPages - 1) html += `<span style="color: #a1a1aa; padding: 0 4px;">…</span>`;
            html += `<button onclick="loadProducts(${totalPages})" class="shadcn-btn shadcn-btn-outline" style="height: 30px; min-width: 30px; padding: 0 8px; font-size: 12px;">${totalPages}</button>`;
        }

        // Next Button
        html += `
            <button onclick="loadProducts(${page + 1})" 
                    ${page >= totalPages ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''} 
                    class="shadcn-btn shadcn-btn-outline" style="height: 30px; padding: 0 10px; font-size: 12px;">
                Next <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
            </button>
        `;

        html += '</div></div>';
        container.innerHTML = html;
    }

    // Reset filters
    function resetAllFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('featuredFilter').value = '';
        document.getElementById('stockFilter').value = '';
        if (document.getElementById('storePresenceFilter')) document.getElementById('storePresenceFilter').value = '';
        availableOnly = false;
        quickTab = 'all';
        document.querySelectorAll('#tab-all, #tab-featured, #tab-instock').forEach(b => {
            b.className = 'shadcn-btn shadcn-btn-outline';
        });
        document.getElementById('tab-all').className = 'shadcn-btn shadcn-btn-primary';
        checkFilterActive();
        loadProducts(1);
    }

    // Clear search
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        checkFilterActive();
        loadProducts(1);
    }

    // Check filter active
    function checkFilterActive() {
        const search = document.getElementById('searchInput').value.trim();
        const category = document.getElementById('categoryFilter').value;
        const featured = document.getElementById('featuredFilter').value;
        const stock = document.getElementById('stockFilter').value;
        const storePresence = document.getElementById('storePresenceFilter') ? document.getElementById('storePresenceFilter').value : '';

        const clearBtn = document.getElementById('searchClearBtn');
        if (clearBtn) clearBtn.style.display = search.length > 0 ? 'block' : 'none';

        const resetBtn = document.getElementById('resetFiltersBtn');
        const hasFilters = search.length > 0 || category !== '' || featured !== '' || stock !== '' || storePresence !== '' || quickTab !== 'all';
        if (resetBtn) resetBtn.style.display = hasFilters ? 'inline-flex' : 'none';
    }

    // Search input debouncer
    let searchDebounce = null;
    document.getElementById('searchInput').addEventListener('input', function() {
        checkFilterActive();
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            loadProducts(1);
        }, 300);
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts(1);
    });
    </script>
</body>
</html>
