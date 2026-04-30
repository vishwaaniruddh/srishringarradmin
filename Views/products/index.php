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
                    <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <form method="GET" action="index.php" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                            <input type="hidden" name="controller" value="product">
                            <input type="hidden" name="action" value="index">
                            
                            <div class="lg:col-span-3">
                                <select name="category" id="categoryFilter" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block p-3">
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
                            
                            <div class="lg:col-span-4">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or code..." class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block pl-10 p-3">
                                </div>
                            </div>
                            
                            <div class="lg:col-span-5 flex flex-wrap gap-2 justify-start lg:justify-end">
                                <button type="button" onclick="loadProducts(1)" class="bg-primary text-white rounded-xl px-4 py-3 text-sm font-bold hover:opacity-90 shadow-lg shadow-primary/20 transition-all flex items-center justify-center">
                                    <i class="fas fa-search mr-2"></i> Find
                                </button>
                                <a href="index.php?controller=product&action=import" class="bg-blue-600 text-white rounded-xl px-4 py-3 text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all flex items-center justify-center">
                                    <i class="fas fa-file-import mr-2"></i> Import
                                </a>
                                <a href="index.php?controller=product&action=export" class="bg-gray-800 text-white rounded-xl px-4 py-3 text-sm font-bold hover:bg-gray-900 shadow-lg shadow-gray-200 transition-all flex items-center justify-center">
                                    <i class="fas fa-file-export mr-2"></i> Export
                                </a>
                                <a href="index.php?controller=product&action=add" class="bg-green-500 text-white rounded-xl px-4 py-3 text-sm font-bold hover:bg-green-600 shadow-lg shadow-green-100 transition-all flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="table-responsive">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">#</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Inventory</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pricing</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bookings</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="products-body" class="divide-y divide-gray-100">
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

    async function loadProducts(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
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
            const response = await fetch(`index.php?controller=api&action=products&page=${page}&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`);
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
                    ? `<span class="text-xs font-semibold text-red-500">${p.details.bookings.length} Active Bookings</span>`
                    : `<span class="text-xs text-gray-400">No Bookings</span>`;

                html += `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-400 font-medium">${serialStart + index}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="${p.details.image_path}" alt="" class="product-img mr-4 shadow-sm border border-gray-100" onerror="this.src='assets/default-product.jpg'">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">${p.name.toLowerCase().replace(/\\b\\w/g, l => l.toUpperCase())}</div>
                                    <div class="text-xs text-gray-400 capitalize">${p.details.product_type_label}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800">${p.code}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">${p.details.category_name.toLowerCase().replace(/\\b\\w/g, l => l.toUpperCase())}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium ${p.details.quantity > 0 ? 'text-gray-800' : 'text-red-500'}">Qty: ${p.details.quantity}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-400">Sale: <span class="text-sm font-semibold text-gray-800">₹${parseFloat(p.details.sale_price).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span></div>
                            <div class="text-xs text-gray-400">Rent: <span class="text-sm font-semibold text-primary">₹${parseFloat(p.details.rent_price).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span></div>
                        </td>
                        <td class="px-6 py-4">${bookingText}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="index.php?controller=product&action=edit&id=${p.id}&type=${p.type}" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?controller=product&action=delete&id=${p.id}&type=${p.type}" 
                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                   class="p-2 text-gray-400 hover:text-red-500 transition-colors">
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
    </script>
</body>
</html>
