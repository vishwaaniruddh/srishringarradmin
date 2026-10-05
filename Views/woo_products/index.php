<!DOCTYPE html>
<html lang="en">
<head>
    <title>YN Storefront Products - Srishringarr Admin</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .table-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .data-table th {
            background: #f8fafc;
            padding: 10px 14px;
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .data-table td {
            padding: 11px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .data-table tr:hover td {
            background-color: #f8fafc;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        .badge-neutral {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .badge-success {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-warning {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-danger {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 4px;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans text-zinc-900">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <?php 
            $pageTitle = 'YN Storefront Products';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <!-- Page Header & Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h2 class="text-xl font-semibold text-zinc-900 tracking-tight">YN Web Storefront Products</h2>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Core PHP Storefront
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500">Live products catalog stored in child database (<code>yosshitaneha.com</code>)</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleExportModal()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-md bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 text-xs font-medium transition-colors shadow-xs">
                                <i class="fa-solid fa-file-export text-xs text-zinc-500"></i>
                                <span>Export Options</span>
                            </button>
                            <a href="index.php?controller=sync&action=index" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-md bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-medium transition-colors shadow-xs">
                                <i class="fa-solid fa-rotate text-xs"></i>
                                <span>Store Sync Hub</span>
                            </a>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-zinc-500">Total Products</p>
                                    <h3 class="text-xl font-semibold text-zinc-900 mt-1"><?php echo number_format($stats['total'] ?? 0); ?></h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-zinc-500">Published</p>
                                    <h3 class="text-xl font-semibold text-zinc-900 mt-1"><?php echo number_format($stats['published'] ?? 0); ?></h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-zinc-500">Draft / Inactive</p>
                                    <h3 class="text-xl font-semibold text-zinc-900 mt-1"><?php echo number_format($stats['draft'] ?? 0); ?></h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-zinc-500">Out of Stock</p>
                                    <h3 class="text-xl font-semibold text-zinc-900 mt-1"><?php echo number_format($stats['out_of_stock'] ?? 0); ?></h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-xs">
                        <form action="index.php" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5">
                            <input type="hidden" name="controller" value="wooproduct">
                            <input type="hidden" name="action" value="index">

                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by product name or SKU..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-zinc-50 border border-zinc-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-zinc-900 transition-all">
                            </div>

                            <div class="flex items-center gap-2">
                                <select name="status" class="py-1.5 px-3 text-xs bg-zinc-50 border border-zinc-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-zinc-900 text-zinc-700">
                                    <option value="">All Statuses</option>
                                    <option value="published" <?php echo ($status === 'published') ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo ($status === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                </select>

                                <?php if (!empty($categories)): ?>
                                    <select name="category_id" class="py-1.5 px-3 text-xs bg-zinc-50 border border-zinc-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-zinc-900 text-zinc-700 max-w-[180px]">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($categoryId == (int)$cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>

                                <button type="submit" class="px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-md text-xs font-medium transition-colors">
                                    Filter
                                </button>

                                <?php if (!empty($search) || !empty($status) || $categoryId > 0): ?>
                                    <a href="index.php?controller=wooproduct&action=index" class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-md text-xs font-medium transition-colors">
                                        Reset
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Products Table Container -->
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">Img</th>
                                        <th>Product Information</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th style="text-align: right; width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-12 text-zinc-400">
                                                <i class="fa-solid fa-box-open text-3xl mb-2 text-zinc-300 block"></i>
                                                <span class="text-xs">No storefront products found matching criteria.</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $p): ?>
                                            <tr>
                                                <!-- Thumbnail Image -->
                                                <td>
                                                    <div class="w-10 h-10 rounded-md bg-zinc-100 border border-zinc-200 overflow-hidden flex items-center justify-center flex-shrink-0">
                                                        <?php if (!empty($p['image_url'])): ?>
                                                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                            <i class="fa-regular fa-image text-zinc-400 text-xs hidden"></i>
                                                        <?php else: ?>
                                                            <i class="fa-regular fa-image text-zinc-400 text-xs"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>

                                                <!-- Product Info -->
                                                <td>
                                                    <div class="font-medium text-zinc-900 leading-snug line-clamp-1 max-w-md">
                                                        <?php echo htmlspecialchars($p['name']); ?>
                                                    </div>
                                                    <div class="text-[11px] text-zinc-400 font-mono mt-0.5">
                                                        ID: #<?php echo $p['id']; ?> &bull; slug: <?php echo htmlspecialchars(mb_strimwidth($p['slug'], 0, 35, '...')); ?>
                                                    </div>
                                                </td>

                                                <!-- SKU -->
                                                <td>
                                                    <span class="badge-neutral font-mono">
                                                        <?php echo htmlspecialchars($p['sku'] ?: 'N/A'); ?>
                                                    </span>
                                                </td>

                                                <!-- Categories -->
                                                <td>
                                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                                        <?php 
                                                        $cats = explode(',', (string)($p['categories'] ?? ''));
                                                        foreach ($cats as $cat): 
                                                            $cat = trim($cat);
                                                            if (empty($cat)) continue;
                                                        ?>
                                                            <span class="badge-neutral text-[10px]"><?php echo htmlspecialchars($cat); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>

                                                <!-- Stock -->
                                                <td>
                                                    <?php $stock = (int)($p['stock'] ?? 0); ?>
                                                    <?php if ($stock > 0): ?>
                                                        <span class="badge-success">Qty: <?php echo $stock; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge-danger">Out of Stock</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Price -->
                                                <td>
                                                    <div class="font-medium text-zinc-900">
                                                        ₹<?php echo number_format((float)($p['price'] ?? 0), 2); ?>
                                                    </div>
                                                    <?php if (!empty($p['sale_price']) && (float)$p['sale_price'] > 0 && (float)$p['sale_price'] < (float)$p['price']): ?>
                                                        <div class="text-[11px] text-emerald-600 font-medium">
                                                            Sale: ₹<?php echo number_format((float)$p['sale_price'], 2); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Status -->
                                                <td>
                                                    <?php if (($p['status'] ?? '') === 'published'): ?>
                                                        <span class="badge-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge-warning">Draft</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Actions -->
                                                <td style="text-align: right;">
                                                    <div class="inline-flex items-center gap-1">
                                                        <a href="https://yosshitaneha.com/product/<?php echo urlencode($p['slug']); ?>/" target="_blank" rel="noopener noreferrer" class="w-7 h-7 rounded inline-flex items-center justify-center text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition-colors" title="View live on storefront">
                                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        <div class="p-3 bg-zinc-50/70 border-t border-zinc-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-500">
                            <div>
                                Showing <span class="font-semibold text-zinc-900"><?php echo count($products); ?></span> of <span class="font-semibold text-zinc-900"><?php echo number_format($totalCount); ?></span> products
                            </div>

                            <?php if ($totalPages > 1): ?>
                                <div class="flex items-center gap-1">
                                    <!-- First / Prev -->
                                    <?php if ($currentPage > 1): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=1&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&category_id=<?php echo $categoryId; ?>" class="px-2 py-1 bg-white border border-zinc-200 rounded text-zinc-600 hover:bg-zinc-100" title="First Page">
                                            <i class="fa-solid fa-angles-left text-[10px]"></i>
                                        </a>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&category_id=<?php echo $categoryId; ?>" class="px-2.5 py-1 bg-white border border-zinc-200 rounded text-zinc-600 hover:bg-zinc-100">
                                            Prev
                                        </a>
                                    <?php endif; ?>

                                    <!-- Page Number Badges -->
                                    <span class="px-3 py-1 font-semibold text-zinc-800 bg-white border border-zinc-200 rounded">
                                        Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                                    </span>

                                    <!-- Next / Last -->
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&category_id=<?php echo $categoryId; ?>" class="px-2.5 py-1 bg-white border border-zinc-200 rounded text-zinc-600 hover:bg-zinc-100">
                                            Next
                                        </a>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&category_id=<?php echo $categoryId; ?>" class="px-2 py-1 bg-white border border-zinc-200 rounded text-zinc-600 hover:bg-zinc-100" title="Last Page">
                                            <i class="fa-solid fa-angles-right text-[10px]"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- Export Modal (ShadCN style) -->
    <div id="exportModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="backdrop-filter: blur(2px);">
        <div class="bg-white rounded-xl shadow-lg border border-zinc-200 w-full max-w-md overflow-hidden">
            <div class="p-5 border-b border-zinc-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900">Export Storefront Products</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Download product data from child database into Excel (.xlsx)</p>
                </div>
                <button type="button" onclick="toggleExportModal()" class="w-7 h-7 rounded inline-flex items-center justify-center text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            
            <div class="p-5 space-y-4">
                <!-- Option 1: Current / All -->
                <div class="p-3.5 border border-zinc-200 rounded-lg hover:border-zinc-300 transition-colors">
                    <h4 class="text-xs font-semibold text-zinc-900 mb-1">Export Current Selection</h4>
                    <p class="text-[11px] text-zinc-500 mb-3">Download products matching your current search and filters (up to 10,000 items).</p>
                    <a href="index.php?controller=wooproduct&action=export<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status) ? '&status=' . urlencode($status) : ''; ?><?php echo $categoryId > 0 ? '&category_id=' . $categoryId : ''; ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded text-xs font-medium transition-colors">
                        <i class="fa-solid fa-download text-xs"></i>
                        <span>Download Excel (.xlsx)</span>
                    </a>
                </div>

                <!-- Option 2: Upload SKU file -->
                <div class="p-3.5 border border-zinc-200 rounded-lg hover:border-zinc-300 transition-colors">
                    <h4 class="text-xs font-semibold text-zinc-900 mb-1">Export by SKU List</h4>
                    <p class="text-[11px] text-zinc-500 mb-3">Upload an Excel or CSV file containing SKU codes in the first column.</p>
                    <form action="index.php?controller=wooproduct&action=export" method="POST" enctype="multipart/form-data" class="space-y-2">
                        <input type="file" name="sku_file" accept=".xlsx, .xls, .csv" required class="block w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded text-xs font-medium transition-colors">
                            <i class="fa-solid fa-file-arrow-up text-xs text-zinc-500"></i>
                            <span>Upload &amp; Export</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-3.5 bg-zinc-50 border-t border-zinc-200 flex justify-end">
                <button type="button" onclick="toggleExportModal()" class="px-3 py-1.5 bg-white border border-zinc-200 rounded text-xs font-medium text-zinc-700 hover:bg-zinc-100">
                    Close
                </button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
        function toggleExportModal() {
            const modal = document.getElementById('exportModal');
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('exportModal');
                if (modal && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
