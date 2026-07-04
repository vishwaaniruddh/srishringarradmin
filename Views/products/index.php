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
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-7xl mx-auto">
                    <!-- Filters & Actions -->
                    <div class="mb-6 bg-black p-4 rounded-xl border border-zinc-800">
                        <form method="GET" action="index.php" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                            <input type="hidden" name="controller" value="product">
                            <input type="hidden" name="action" value="index">
                            
                            <div class="lg:col-span-3">
                                <select name="category" id="categoryFilter" class="w-full bg-black border border-zinc-800 text-white text-xs rounded-lg focus:ring-1 focus:ring-zinc-700 focus:border-zinc-700 block py-2 px-3">
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
                            </div>

                            <div class="lg:col-span-2">
                                <select name="featured" id="featuredFilter" class="w-full bg-black border border-zinc-800 text-white text-xs rounded-lg focus:ring-1 focus:ring-zinc-700 focus:border-zinc-700 block py-2 px-3">
                                    <option value="">All Featured</option>
                                    <option value="1">Featured Only</option>
                                    <option value="0">Non-Featured</option>
                                </select>
                            </div>
                            
                            <div class="lg:col-span-2">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 text-xs">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or code..." class="w-full bg-black border border-zinc-800 text-white text-xs rounded-lg focus:ring-1 focus:ring-zinc-700 focus:border-zinc-700 block pl-8 py-2">
                                </div>
                            </div>
                            
                            <div class="lg:col-span-5 flex flex-wrap gap-2 justify-start lg:justify-end">
                                <button type="button" onclick="loadProducts(1)" class="bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-4 py-2 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-search mr-1.5 text-zinc-400"></i> Find
                                </button>
                                <a href="index.php?controller=product&action=import" class="bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-4 py-2 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-file-import mr-1.5 text-zinc-400"></i> Import
                                </a>
                                <a href="javascript:void(0)" onclick="exportProducts()" class="bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-4 py-2 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-file-export mr-1.5 text-zinc-400"></i> Export
                                </a>
                                <a href="index.php?controller=product&action=add" class="bg-white border border-white text-black hover:bg-black hover:text-white hover:border-zinc-800 rounded-lg px-4 py-2 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-plus mr-1.5"></i> Add Product
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-black rounded-xl border border-zinc-800 overflow-hidden">
                        <div class="table-responsive">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-zinc-950 border-b border-zinc-800">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-16">#</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider min-w-[320px]">Product</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Inventory</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Pricing</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider">Bookings</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider text-center">Featured</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="products-body" class="divide-y divide-zinc-800/40">
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-spinner fa-spin text-3xl text-primary mb-4"></i>
                                                <p>Loading products...</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Container -->
                        <div id="pagination-container"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    let currentPage = 1;

    function exportProducts() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        window.location.href = `index.php?controller=product&action=export&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`;
    }

    async function loadProducts(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const featured = document.getElementById('featuredFilter').value;
        const tbody = document.getElementById('products-body');
        const pagination = document.getElementById('pagination-container');

        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-primary mb-4"></i>
                        <p>Loading products...</p>
                    </div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(`index.php?controller=api&action=products&page=${page}&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&featured=${featured}`);
            const data = await response.json();

            if (!data.products || data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box-open text-3xl mb-4 opacity-20"></i>
                                <p>No products found matching your criteria.</p>
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
                const bookingText = p.details.bookings.length > 0 
                    ? `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20">${p.details.bookings.length} Booked</span>`
                    : `<span class="text-xs text-zinc-500">–</span>`;

                const rawName = (p.name || '').trim();
                const cleanName = (rawName && rawName.toLowerCase() !== 'jewellery' && rawName.toLowerCase() !== 'garments' && rawName.toLowerCase() !== 'garment_product') ? rawName : '';
                const displayName = cleanName ? cleanName.toLowerCase().replace(/\b\w/g, l => l.toUpperCase()) : 'Unnamed Product (' + p.code + ')';
                const truncatedName = displayName.length > 40 ? displayName.substring(0, 40) + '...' : displayName;

                const qtyVal = parseFloat(p.details.quantity || 0);
                const qtyText = qtyVal > 0 
                    ? `<span class="text-xs text-zinc-300 font-medium">${qtyVal % 1 === 0 ? qtyVal.toFixed(0) : qtyVal.toFixed(2)} in stock</span>`
                    : `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-500 border border-red-500/20">Out of Stock</span>`;

                const rentPrice = parseFloat(p.details.rent_price || 0);
                const salePrice = parseFloat(p.details.sale_price || 0);

                html += `
                    <tr class="hover:bg-zinc-900/30 transition-colors border-b border-zinc-900">
                        <td class="px-6 py-4 text-xs text-zinc-500 font-medium">${serialStart + index}</td>
                        <td class="px-6 py-4 min-w-[320px]">
                            <div class="flex items-center">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" class="block flex-shrink-0">
                                    <img src="${p.details.image_path}" alt="" class="product-img mr-4 border border-zinc-800 hover:opacity-85 transition-opacity" onerror="this.src='assets/default-product.jpg'">
                                </a>
                                <div>
                                    <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" 
                                       class="text-sm font-semibold text-white hover:text-zinc-300 transition-colors block" 
                                       title="${displayName}">
                                        ${truncatedName}
                                    </a>
                                    <div class="text-[10px] text-zinc-500 font-medium capitalize mt-0.5">${p.details.product_type_label}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs text-zinc-300 bg-zinc-900 border border-zinc-800 px-2 py-0.5 rounded">${p.code}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-400 font-medium">${(p.details.category_name || 'N/A').toLowerCase().replace(/\b\w/g, l => l.toUpperCase())}</td>
                        <td class="px-6 py-4">${qtyText}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${p.details.price_source === 'manual' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-blue-500/10 text-blue-500 border border-blue-500/20'}">${p.details.price_source === 'manual' ? 'Manual' : 'POS'}</span>
                            </div>
                            <div class="text-xs font-bold text-white mt-1">Rent: ₹${rentPrice.toLocaleString('en-IN', {minimumFractionDigits: 0})}</div>
                            <div class="text-[10px] text-zinc-500 mt-0.5">Sale: ₹${salePrice.toLocaleString('en-IN', {minimumFractionDigits: 0})}</div>
                        </td>
                        <td class="px-6 py-4">${bookingText}</td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="toggleFeatured(${p.id}, '${p.type}', ${p.featured == 1 ? 0 : 1})" class="transition-all transform hover:scale-110 text-zinc-500">
                                <i class="${p.featured == 1 ? 'fas fa-star text-yellow-500' : 'far fa-star text-zinc-700'} text-lg"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2.5">
                                <a href="index.php?controller=product&action=view_details&id=${p.id}&type=${p.type}" title="View Details" class="text-zinc-500 hover:text-white transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="index.php?controller=product&action=edit&id=${p.id}&type=${p.type}" title="Edit Product" class="text-zinc-500 hover:text-white transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="index.php?controller=product&action=delete&id=${p.id}&type=${p.type}" 
                                   title="Delete Product"
                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                   class="text-zinc-500 hover:text-red-500 transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
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
                    <td colspan="8" class="px-6 py-10 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-3xl mb-4"></i>
                        <p>Error loading products. Please try again.</p>
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

        let paginationHtml = `
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-800">${startRange}</span> to 
                    <span class="font-semibold text-gray-800">${endRange}</span> of 
                    <span class="font-semibold text-gray-800">${data.totalRecords}</span> products
                </div>
                <div class="flex items-center space-x-1">
        `;

        if (data.currentPage > 1) {
            paginationHtml += `
                <button onclick="loadProducts(1)" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">First</button>
                <button onclick="loadProducts(${data.currentPage - 1})" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Prev</button>
            `;
        }

        const range = 2;
        const start = Math.max(1, data.currentPage - range);
        const end = Math.min(data.totalPages, data.currentPage + range);

        for (let i = start; i <= end; i++) {
            paginationHtml += `
                <button onclick="loadProducts(${i})" 
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-all ${i === data.currentPage ? 'bg-primary text-white shadow-sm' : 'text-gray-500 bg-white border border-gray-200 hover:bg-gray-50'}">
                    ${i}
                </button>
            `;
        }

        if (data.currentPage < data.totalPages) {
            paginationHtml += `
                <button onclick="loadProducts(${data.currentPage + 1})" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Next</button>
                <button onclick="loadProducts(${data.totalPages})" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Last</button>
            `;
        }

        paginationHtml += `</div></div>`;
        container.innerHTML = paginationHtml;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts(1);
        
        // Add event listeners for filters
        document.getElementById('categoryFilter').addEventListener('change', () => loadProducts(1));
        
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProducts(1), 500);
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

    document.getElementById('featuredFilter').addEventListener('change', () => loadProducts(1));
    </script>
</body>
</html>
