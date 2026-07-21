<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Products - Srishringarr</title>
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
            $pageTitle = 'All Products';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-gray-50/50">
                <div class="max-w-[1400px] mx-auto">

                    <!-- Stats Cards -->
                    <div class="stats-grid" id="stats-grid">
                        <div class="stat-card stat-card--total">
                            <div class="stat-icon"><i class="fas fa-box"></i></div>
                            <div class="stat-value" id="stat-total">—</div>
                            <div class="stat-label">Total Products</div>
                        </div>
                        <div class="stat-card stat-card--stock">
                            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-value" id="stat-stock">—</div>
                            <div class="stat-label">In Stock</div>
                        </div>
                        <div class="stat-card stat-card--oos">
                            <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="stat-value" id="stat-oos">—</div>
                            <div class="stat-label">Out of Stock</div>
                        </div>
                        <div class="stat-card stat-card--featured">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-value" id="stat-featured">—</div>
                            <div class="stat-label">Featured</div>
                        </div>
                    </div>

                    <!-- Filter Bar -->
                    <div class="filter-bar">
                        <!-- Row 1: Search & Filters -->
                        <div class="filter-row" style="margin-bottom: 0.75rem;">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products by name, code, or SKU...">
                            </div>
                            <select id="categoryFilter">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $parent => $data): ?>
                                    <optgroup label="<?php echo htmlspecialchars($parent); ?> (<?php echo $data['count']; ?>)">
                                        <?php foreach ($data['children'] as $value => $childData): ?>
                                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $category == $value ? 'selected' : ''; ?>>
                                                <?php echo $childData['name']; ?> (<?php echo $childData['count']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <select id="featuredFilter">
                                <option value="">All Featured</option>
                                <option value="1">Featured Only</option>
                                <option value="0">Non-Featured</option>
                            </select>
                            <select id="sortFilter">
                                <option value="id_desc">Newest First</option>
                                <option value="id_asc">Oldest First</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                <option value="code_asc">Code (A-Z)</option>
                                <option value="code_desc">Code (Z-A)</option>
                                <option value="rent_price_asc">Rent ↑</option>
                                <option value="rent_price_desc">Rent ↓</option>
                                <option value="sales_price_asc">Sale ↑</option>
                                <option value="sales_price_desc">Sale ↓</option>
                            </select>
                        </div>
                        <!-- Row 2: Action Buttons -->
                        <div class="filter-row" style="justify-content: space-between;">
                            <div class="filter-actions">
                                <button type="button" id="availableToggle" onclick="toggleAvailableOnly()" class="filter-btn">
                                    <i class="fas fa-check-circle"></i> Available Only
                                </button>
                                <a href="index.php?controller=product&action=import" class="filter-btn">
                                    <i class="fas fa-file-import"></i> Import
                                </a>
                                <a href="javascript:void(0)" onclick="exportProducts()" class="filter-btn">
                                    <i class="fas fa-file-export"></i> Export
                                </a>
                                <a href="index.php?controller=product&action=descriptionCorrector" class="filter-btn">
                                    <i class="fas fa-magic"></i> Format Descriptions
                                </a>
                            </div>
                            <a href="index.php?controller=product&action=add" class="filter-btn filter-btn--primary">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="product-table-wrap">
                        <div class="table-responsive">
                            <table class="w-full text-left">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">#</th>
                                        <th style="min-width: 320px;">Product</th>
                                        <th>Code</th>
                                        <th>Category</th>
                                        <th>Inventory</th>
                                        <th>Pricing</th>
                                        <th style="text-align: center;">Source</th>
                                        <th style="text-align: center;">Availability</th>
                                        <th>Bookings</th>
                                        <th style="text-align: center;">Featured</th>
                                        <th style="text-align: right; width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="products-body">
                                    <!-- Skeleton Loading -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="pagination-container"></div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    let currentPage = 1;
    let availableOnly = false;

    // Generate skeleton loading rows
    function renderSkeletons(count = 8) {
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
                <tr>
                    <td><div class="skeleton skeleton-text skeleton-text--tiny" style="height: 10px;">&nbsp;</div></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="skeleton skeleton-img"></div>
                            <div>
                                <div class="skeleton skeleton-text skeleton-text--wide" style="margin-bottom: 8px;">&nbsp;</div>
                                <div class="skeleton skeleton-text skeleton-text--short">&nbsp;</div>
                            </div>
                        </div>
                    </td>
                    <td><div class="skeleton skeleton-text skeleton-text--short">&nbsp;</div></td>
                    <td><div class="skeleton skeleton-text skeleton-text--medium">&nbsp;</div></td>
                    <td><div class="skeleton skeleton-text skeleton-text--short">&nbsp;</div></td>
                    <td><div class="skeleton skeleton-text skeleton-text--medium">&nbsp;</div></td>
                    <td style="text-align:center;"><div class="skeleton skeleton-text skeleton-text--tiny" style="margin: 0 auto;">&nbsp;</div></td>
                    <td style="text-align:center;"><div class="skeleton skeleton-text skeleton-text--short" style="margin: 0 auto;">&nbsp;</div></td>
                    <td><div class="skeleton skeleton-text skeleton-text--tiny">&nbsp;</div></td>
                    <td style="text-align:center;"><div class="skeleton skeleton-text skeleton-text--tiny" style="margin: 0 auto;">&nbsp;</div></td>
                    <td style="text-align:right;"><div class="skeleton skeleton-text skeleton-text--short" style="margin-left: auto;">&nbsp;</div></td>
                </tr>
            `;
        }
        return html;
    }

    function toggleAvailableOnly() {
        availableOnly = !availableOnly;
        const btn = document.getElementById('availableToggle');
        if (availableOnly) {
            btn.classList.add('filter-btn--active');
        } else {
            btn.classList.remove('filter-btn--active');
        }
        loadProducts(1);
    }

    function exportProducts() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const sortVal = document.getElementById('sortFilter').value;
        let sortBy = 'id';
        let sortOrder = 'desc';
        if (sortVal) {
            const parts = sortVal.split('_');
            sortOrder = parts.pop();
            sortBy = parts.join('_');
        }
        window.location.href = `index.php?controller=product&action=export&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&sort_by=${sortBy}&sort_order=${sortOrder}&available_only=${availableOnly ? 1 : 0}`;
    }

    // Animate a counter from 0 to target
    function animateCounter(el, target) {
        if (isNaN(target)) { el.textContent = target; return; }
        const duration = 600;
        const start = performance.now();
        const from = 0;
        function tick(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = Math.round(from + (target - from) * eased).toLocaleString('en-IN');
            if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    async function loadProducts(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const featured = document.getElementById('featuredFilter').value;
        const sortVal = document.getElementById('sortFilter').value;
        
        let sortBy = 'id';
        let sortOrder = 'desc';
        if (sortVal) {
            const parts = sortVal.split('_');
            sortOrder = parts.pop();
            sortBy = parts.join('_');
        }
        
        const tbody = document.getElementById('products-body');
        const pagination = document.getElementById('pagination-container');

        // Show skeleton loading
        tbody.innerHTML = renderSkeletons(8);

        try {
            const response = await fetch(`index.php?controller=api&action=products&page=${page}&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&featured=${featured}&sort_by=${sortBy}&sort_order=${sortOrder}&available_only=${availableOnly ? 1 : 0}`);
            const data = await response.json();

            // Update stats
            if (data.stats) {
                animateCounter(document.getElementById('stat-total'), data.stats.total || 0);
                animateCounter(document.getElementById('stat-stock'), data.stats.in_stock || 0);
                animateCounter(document.getElementById('stat-oos'), data.stats.out_of_stock || 0);
                animateCounter(document.getElementById('stat-featured'), data.stats.featured || 0);
            } else if (data.totalRecords !== undefined) {
                animateCounter(document.getElementById('stat-total'), data.totalRecords);
            }

            if (!data.products || data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 3rem 1rem !important;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-box-open" style="font-size: 2.5rem; color: #222;"></i>
                                <p style="color: #555; font-size: 0.85rem;">No products found matching your criteria.</p>
                                <a href="index.php?controller=product&action=add" class="filter-btn filter-btn--primary" style="margin-top: 0.5rem;">
                                    <i class="fas fa-plus"></i> Add Product
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
                pagination.innerHTML = '';
                return;
            }

            let html = '';
            const serialStart = (data.currentPage - 1) * 20 + 1;

            data.products.forEach((p, index) => {
                // Bookings
                const bookingCount = p.details.bookings ? p.details.bookings.length : 0;
                const bookingHtml = bookingCount > 0 
                    ? `<span class="badge badge--red booking-pulse"><i class="fas fa-calendar-check" style="font-size:0.6rem;"></i> ${bookingCount} Booked</span>`
                    : `<span style="color: #333;">—</span>`;

                // Product name
                const rawName = (p.name || '').trim();
                const cleanName = (rawName && rawName.toLowerCase() !== 'jewellery' && rawName.toLowerCase() !== 'garments' && rawName.toLowerCase() !== 'garment_product') ? rawName : '';
                const displayName = cleanName ? cleanName.toLowerCase().replace(/\b\w/g, l => l.toUpperCase()) : 'Unnamed Product (' + p.code + ')';
                const truncatedName = displayName.length > 45 ? displayName.substring(0, 45) + '...' : displayName;

                // Inventory
                const qtyVal = parseFloat(p.details.quantity || 0);
                const qtyHtml = qtyVal > 0 
                    ? `<span class="badge badge--green"><i class="fas fa-cube" style="font-size:0.55rem;"></i> ${qtyVal % 1 === 0 ? qtyVal.toFixed(0) : qtyVal.toFixed(2)} in stock</span>`
                    : `<span class="badge badge--red"><i class="fas fa-times-circle" style="font-size:0.55rem;"></i> Out of Stock</span>`;

                // Pricing
                const rentPrice = parseFloat(p.details.rent_price || 0);
                const salePrice = parseFloat(p.details.sale_price || 0);

                // Source toggle
                const isManual = p.details.price_source === 'manual';
                const sourceLabel = isManual ? 'Manual' : 'POS';
                const sourceLabelColor = isManual ? 'color: #f59e0b;' : 'color: #555;';

                // Availability
                const avail = p.details.availability || 'both';

                // Featured
                const isFeatured = p.featured == 1;

                // Type badge
                const typeLabel = (p.details.product_type_label || p.type || '').toLowerCase();
                const typeBadgeClass = typeLabel === 'jewellery' ? 'badge--blue' : 'badge--zinc';

                html += `
                    <tr class="product-row" style="animation-delay: ${0.02 * (index + 1)}s;">
                        <td style="color: #333; font-variant-numeric: tabular-nums; font-size: 0.75rem; font-weight: 500;">${serialStart + index}</td>
                        <td style="min-width: 320px;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" style="flex-shrink: 0;">
                                    <img src="${p.details.image_path}" alt="" class="product-img-lg" onerror="this.src='assets/default-product.jpg'">
                                </a>
                                <div style="min-width: 0;">
                                    <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" 
                                       style="display: block; font-size: 0.82rem; font-weight: 600; color: #e5e5e5; text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 260px; transition: color 0.15s;"
                                       title="${displayName}"
                                       onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#e5e5e5'">
                                        ${truncatedName}
                                    </a>
                                    <span class="badge ${typeBadgeClass}" style="margin-top: 4px; font-size: 0.6rem; padding: 0.1rem 0.4rem;">${typeLabel}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="code-badge">${p.code}</span></td>
                        <td style="color: #888; font-size: 0.78rem; font-weight: 500;">${(p.details.category_name || 'N/A').toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                        <td>${qtyHtml}</td>
                        <td>
                            <div class="price-primary">₹${rentPrice.toLocaleString('en-IN', {minimumFractionDigits: 0})}</div>
                            <div class="price-secondary">Sale: ₹${salePrice.toLocaleString('en-IN', {minimumFractionDigits: 0})}</div>
                        </td>
                        <td style="text-align: center;">
                            <label class="toggle-switch">
                                <input type="checkbox" ${isManual ? 'checked' : ''} onchange="togglePriceSourceRow(${p.id}, '${p.type}', this.checked ? 'manual' : 'pos')">
                                <span class="toggle-slider"></span>
                            </label>
                            <div style="font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3px; ${sourceLabelColor}">${sourceLabel}</div>
                        </td>
                        <td style="text-align: center;">
                            <select onchange="toggleAvailabilityRow(${p.id}, '${p.type}', this.value)" class="avail-select">
                                <option value="both" ${avail === 'both' ? 'selected' : ''}>Both</option>
                                <option value="rent" ${avail === 'rent' ? 'selected' : ''}>Rent</option>
                                <option value="sell" ${avail === 'sell' ? 'selected' : ''}>Sell</option>
                            </select>
                        </td>
                        <td>${bookingHtml}</td>
                        <td style="text-align: center;">
                            <button onclick="toggleFeatured(${p.id}, '${p.type}', ${isFeatured ? 0 : 1})" class="featured-star ${isFeatured ? 'featured-star--active' : 'featured-star--inactive'}">
                                <i class="${isFeatured ? 'fas' : 'far'} fa-star"></i>
                            </button>
                        </td>
                        <td style="text-align: right;">
                            <div class="quick-actions" style="justify-content: flex-end;">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" title="View" class="quick-action-btn">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?controller=product&action=edit&id=${p.id}&type=${p.type}" title="Edit" class="quick-action-btn">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="index.php?controller=product&action=delete&id=${p.id}&type=${p.type}" 
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                   class="quick-action-btn quick-action-btn--danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
            renderPagination(data);

        } catch (error) {
            console.error('Error fetching products:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" style="text-align: center; padding: 3rem 1rem !important;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #ef4444;"></i>
                            <p style="color: #888; font-size: 0.85rem;">Error loading products. Please try again.</p>
                            <button onclick="loadProducts(currentPage)" class="filter-btn" style="margin-top: 0.25rem;">
                                <i class="fas fa-redo"></i> Retry
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        if (data.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        const startRange = (data.currentPage - 1) * 20 + 1;
        const endRange = Math.min(data.currentPage * 20, data.totalRecords);

        let html = `<div class="pagination-bar">
            <div class="page-info">
                Showing <span>${startRange}</span> to <span>${endRange}</span> of <span>${data.totalRecords}</span> products
            </div>
            <div class="page-buttons">`;

        if (data.currentPage > 1) {
            html += `<button onclick="loadProducts(1)" class="page-btn page-btn--nav">First</button>`;
            html += `<button onclick="loadProducts(${data.currentPage - 1})" class="page-btn page-btn--nav"><i class="fas fa-chevron-left" style="font-size:0.6rem;"></i></button>`;
        }

        const range = 2;
        const start = Math.max(1, data.currentPage - range);
        const end = Math.min(data.totalPages, data.currentPage + range);

        if (start > 1) {
            html += `<button onclick="loadProducts(1)" class="page-btn">1</button>`;
            if (start > 2) html += `<span style="color:#333; padding: 0 0.25rem;">…</span>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<button onclick="loadProducts(${i})" class="page-btn ${i === data.currentPage ? 'page-btn--active' : ''}">${i}</button>`;
        }

        if (end < data.totalPages) {
            if (end < data.totalPages - 1) html += `<span style="color:#333; padding: 0 0.25rem;">…</span>`;
            html += `<button onclick="loadProducts(${data.totalPages})" class="page-btn">${data.totalPages}</button>`;
        }

        if (data.currentPage < data.totalPages) {
            html += `<button onclick="loadProducts(${data.currentPage + 1})" class="page-btn page-btn--nav"><i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></button>`;
            html += `<button onclick="loadProducts(${data.totalPages})" class="page-btn page-btn--nav">Last</button>`;
        }

        html += `</div></div>`;
        container.innerHTML = html;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts(1);
        
        // Filter change listeners
        document.getElementById('categoryFilter').addEventListener('change', () => loadProducts(1));
        document.getElementById('featuredFilter').addEventListener('change', () => loadProducts(1));
        document.getElementById('sortFilter').addEventListener('change', () => loadProducts(1));
        
        // Debounced search
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProducts(1), 500);
        });

        // Enter key on search
        document.getElementById('searchInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                loadProducts(1);
            }
        });
    });

    async function toggleFeatured(id, type, status) {
        try {
            const response = await fetch('index.php?controller=api&action=toggleFeatured', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type, status })
            });
            const data = await response.json();
            if (data.success) {
                loadProducts(currentPage);
            } else {
                alert(data.error || 'Failed to update featured status');
            }
        } catch (error) {
            console.error('Error toggling featured status:', error);
            alert('Something went wrong. Please try again.');
        }
    }

    async function togglePriceSourceRow(id, type, priceSource) {
        try {
            const response = await fetch('index.php?controller=api&action=togglePriceSource', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type, price_source: priceSource })
            });
            const data = await response.json();
            if (data.success) {
                loadProducts(currentPage);
            } else {
                alert(data.error || 'Failed to update price source');
            }
        } catch (error) {
            console.error('Error toggling price source:', error);
            alert('Something went wrong. Please try again.');
        }
    }

    async function toggleAvailabilityRow(id, type, availability) {
        try {
            const response = await fetch('index.php?controller=api&action=toggleAvailability', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type, availability })
            });
            const data = await response.json();
            if (data.success) {
                loadProducts(currentPage);
            } else {
                alert(data.error || 'Failed to update availability status');
            }
        } catch (error) {
            console.error('Error toggling availability:', error);
            alert('Something went wrong. Please try again.');
        }
    }
    </script>
</body>
</html>
