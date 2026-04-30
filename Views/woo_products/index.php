<!DOCTYPE html>
<html lang="en">
<head>
    <title>Yn Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <?php 
            $pageTitle = 'Yn Products (WooCommerce Remote)';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="w-full mx-auto">
                    <!-- Filters & Search -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div class="flex-1 max-w-2xl flex items-center space-x-4">
                            <form action="index.php" method="GET" class="flex-1 relative group">
                                <input type="hidden" name="controller" value="wooproduct">
                                <input type="hidden" name="action" value="index">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-primary transition-colors">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search remote products by name or SKU..." class="w-full bg-white border border-gray-200 rounded-2xl py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm transition-all outline-none">
                            </form>
                            <button onclick="toggleExportModal()" class="inline-flex items-center px-6 py-3.5 bg-primary text-white rounded-2xl text-sm font-bold shadow-lg hover:opacity-90 transition-all whitespace-nowrap">
                                <i class="fas fa-file-export mr-2"></i> Export Options
                            </button>
                        </div>
                        <div class="flex items-center space-x-2 bg-green-50 text-green-700 px-4 py-2 rounded-xl border border-green-100 text-sm font-medium">
                            <span class="relative flex h-2 w-2 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Live Connection Active
                        </div>
                    </div>

                    <!-- Product Table -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 border-b border-gray-100">
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider">Product Info</th>
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider">SKU</th>
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider">Category</th>
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider">Inventory</th>
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider">Price</th>
                                        <th class="px-8 py-5 text-xs font-bold uppercase text-gray-400 tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="6" class="px-8 py-20 text-center text-gray-400">
                                                <i class="fas fa-box-open text-4xl mb-4 opacity-20 block"></i>
                                                No remote products found.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $p): ?>
                                            <tr class="hover:bg-gray-50/50 transition-all duration-200">
                                                <td class="px-8 py-5">
                                                    <div class="flex items-center">
                                                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-primary/10 transition-colors overflow-hidden">
                                                            <?php if (!empty($p['image_url'])): ?>
                                                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="" class="w-full h-full object-cover">
                                                            <?php else: ?>
                                                                <i class="fas fa-image text-gray-300"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-900 leading-tight mb-1"><?php echo htmlspecialchars($p['name']); ?></div>
                                                            <div class="text-[10px] text-gray-400 font-mono">ID: #<?php echo $p['ID']; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold font-mono">
                                                        <?php echo htmlspecialchars($p['sku'] ?: 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <div class="flex flex-wrap gap-1">
                                                        <?php 
                                                        $cats = explode(',', $p['categories'] ?? '');
                                                        foreach ($cats as $cat): if (empty($cat)) continue;
                                                        ?>
                                                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md text-[10px] font-bold"><?php echo htmlspecialchars($cat); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <?php 
                                                    $stock = (int)$p['stock'];
                                                    $stockColor = $stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50';
                                                    ?>
                                                    <div class="flex items-center">
                                                        <span class="<?php echo $stockColor; ?> px-3 py-1 rounded-full text-xs font-bold">
                                                            Qty: <?php echo $stock; ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-5 text-sm font-bold text-gray-900">
                                                    ₹<?php echo number_format((float)$p['price'], 2); ?>
                                                </td>
                                                <td class="px-8 py-5 text-right">
                                                    <a href="https://yosshitaneha.com/product/<?php echo $p['slug']; ?>/" target="_blank" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-primary/10 hover:text-primary transition-all shadow-sm" title="View on Storefront">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <span class="text-xs font-medium text-gray-500">
                                    Showing <span class="text-gray-900 font-bold"><?php echo count($products); ?></span> of <span class="text-gray-900 font-bold"><?php echo $totalCount; ?></span> products
                                </span>
                                
                                <div class="flex items-center space-x-1">
                                    <!-- Previous -->
                                    <?php if ($currentPage > 1): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-all">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php 
                                    $range = 2;
                                    $showFirst = $currentPage > $range + 1;
                                    $showLast = $currentPage < $totalPages - $range;
                                    
                                    if ($showFirst): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=1&search=<?php echo urlencode($search); ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-500 hover:border-primary hover:text-primary transition-all">1</a>
                                        <?php if ($currentPage > $range + 2): ?>
                                            <span class="px-2 text-gray-400">...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $currentPage - $range); $i <= min($totalPages, $currentPage + $range); $i++): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all <?php echo $i === $currentPage ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-110 z-10' : 'bg-white text-gray-500 border border-gray-200 hover:border-primary hover:text-primary'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($showLast): ?>
                                        <?php if ($currentPage < $totalPages - $range - 1): ?>
                                            <span class="px-2 text-gray-400">...</span>
                                        <?php endif; ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-500 hover:border-primary hover:text-primary transition-all"><?php echo $totalPages; ?></a>
                                    <?php endif; ?>

                                    <!-- Next -->
                                    <?php if ($currentPage < $totalPages): ?>
                                        <a href="index.php?controller=wooproduct&action=index&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>" 
                                           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-all">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    
    <!-- Export Modal -->
    <div id="exportModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
            <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Export Options</h3>
                    <p class="text-xs text-gray-400 mt-1">Choose how you want to export your remote products</p>
                </div>
                <button onclick="toggleExportModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-8 space-y-6">
                <!-- Option 1: All -->
                <div class="group">
                    <a href="index.php?controller=wooproduct&action=export" class="flex items-center p-6 bg-gray-50 rounded-2xl border border-gray-100 hover:border-primary hover:bg-primary/5 transition-all group">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mr-4 text-primary group-hover:scale-110 transition-transform">
                            <i class="fas fa-boxes text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-bold text-gray-900">Export All Products</div>
                            <div class="text-[10px] text-gray-400">Download your entire remote inventory (Max 5,000)</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary transition-colors"></i>
                    </a>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-gray-100"></span></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-gray-400 font-bold tracking-widest">Or Select Specific</span></div>
                </div>

                <!-- Option 2: Selective -->
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-excel mr-2 text-primary"></i> Upload SKU List (Excel/CSV)
                    </h4>
                    <form action="index.php?controller=wooproduct&action=export" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="relative group">
                            <input type="file" name="sku_file" id="sku_file" accept=".xlsx,.xls,.csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center group-hover:border-primary transition-all bg-white">
                                <i class="fas fa-file-excel text-2xl text-gray-300 mb-2 group-hover:text-primary transition-colors"></i>
                                <div id="file_name" class="text-[10px] text-gray-500 font-medium">Click to upload Excel with SKUs</div>
                                <div class="text-[9px] text-gray-400 mt-1">(First column should contain SKU values)</div>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all flex items-center justify-center">
                            <i class="fas fa-filter mr-2"></i> Export Matching Products
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.toggle('hidden');
        }

        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Click to upload Excel with SKUs';
            document.getElementById('file_name').innerText = fileName;
            document.getElementById('file_name').classList.add('text-primary');
        }

        // Close on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('exportModal');
            if (event.target == modal) {
                toggleExportModal();
            }
        }
    </script>
</body>
</html>
