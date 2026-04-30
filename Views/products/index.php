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
                        <form method="GET" action="index.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="hidden" name="controller" value="product">
                            <input type="hidden" name="action" value="index">
                            
                            <div class="md:col-span-1">
                                <select name="category" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block p-3">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $parent => $data): ?>
                                        <option value="<?php echo htmlspecialchars($parent); ?>" <?php echo $category == $parent ? 'selected' : ''; ?>>
                                            <?php echo ucwords(strtolower($parent)); ?> (<?php echo $data['count']; ?>)
                                        </option>
                                        <?php foreach ($data['children'] as $child => $count): ?>
                                            <option value="<?php echo htmlspecialchars($parent . '>' . $child); ?>" <?php echo $category == ($parent . '>' . $child) ? 'selected' : ''; ?>>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<?php echo ucwords(strtolower($child)); ?> (<?php echo $count; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or code..." class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-primary focus:border-primary block pl-10 p-3">
                                </div>
                            </div>
                            
                            <div class="md:col-span-1 flex space-x-2">
                                <button type="submit" class="bg-primary text-white rounded-xl px-4 py-3 text-sm font-medium hover:bg-opacity-90 transition-all flex-shrink-0">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="index.php?controller=product&action=import" class="bg-blue-500 text-white rounded-xl px-4 py-3 text-sm font-medium hover:bg-opacity-90 transition-all flex items-center justify-center flex-1">
                                    <i class="fas fa-file-import mr-2"></i> Import
                                </a>
                                <a href="index.php?controller=product&action=export" class="bg-gray-700 text-white rounded-xl px-4 py-3 text-sm font-medium hover:bg-opacity-90 transition-all flex items-center justify-center flex-1">
                                    <i class="fas fa-file-export mr-2"></i> Export
                                </a>
                                <a href="index.php?controller=product&action=add" class="bg-green-500 text-white rounded-xl px-4 py-3 text-sm font-medium hover:bg-opacity-90 transition-all flex items-center justify-center flex-1">
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
                                <tbody class="divide-y divide-gray-100">
                                    <?php 
                                    $serialStart = ($currentPage - 1) * 20 + 1;
                                    foreach ($products as $index => $p): 
                                    ?>
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-400 font-medium">
                                            <?php echo $serialStart + $index; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="<?php echo htmlspecialchars($p['details']['image_path']); ?>" alt="" class="product-img mr-4 shadow-sm border border-gray-100">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars(ucwords(strtolower($p['name']))); ?></div>
                                                    <div class="text-xs text-gray-400 capitalize"><?php echo $p['details']['product_type_label']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-800">
                                                <?php echo htmlspecialchars($p['code']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <?php echo htmlspecialchars(ucwords(strtolower($p['details']['category_name']))); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium <?php echo $p['details']['quantity'] > 0 ? 'text-gray-800' : 'text-red-500'; ?>">
                                                Qty: <?php echo $p['details']['quantity']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-400">Sale: <span class="text-sm font-semibold text-gray-800">₹<?php echo number_format($p['details']['sale_price'], 2); ?></span></div>
                                            <div class="text-xs text-gray-400">Rent: <span class="text-sm font-semibold text-primary">₹<?php echo number_format($p['details']['rent_price'], 2); ?></span></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (count($p['details']['bookings']) > 0): ?>
                                                <span class="text-xs font-semibold text-red-500">
                                                    <?php echo count($p['details']['bookings']); ?> Active Bookings
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400">No Bookings</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="index.php?controller=product&action=edit&id=<?php echo $p['id']; ?>&type=<?php echo $p['type']; ?>" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?controller=product&action=delete&id=<?php echo $p['id']; ?>&type=<?php echo $p['type']; ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                                   class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-500">
                                Showing <span class="font-semibold text-gray-800"><?php echo ($currentPage - 1) * 20 + 1; ?></span> to 
                                <span class="font-semibold text-gray-800"><?php echo min($currentPage * 20, $totalRecords); ?></span> of 
                                <span class="font-semibold text-gray-800"><?php echo $totalRecords; ?></span> products
                            </div>
                            
                            <div class="flex items-center space-x-1">
                                <?php if ($currentPage > 1): ?>
                                    <a href="index.php?controller=product&action=index&page=1&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">First</a>
                                    <a href="index.php?controller=product&action=index&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Prev</a>
                                <?php endif; ?>

                                <?php
                                $range = 2;
                                $start = max(1, $currentPage - $range);
                                $end = min($totalPages, $currentPage + $range);
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <a href="index.php?controller=product&action=index&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" 
                                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all <?php echo $i == $currentPage ? 'bg-primary text-white shadow-sm' : 'text-gray-500 bg-white border border-gray-200 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="index.php?controller=product&action=index&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Next</a>
                                    <a href="index.php?controller=product&action=index&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="px-3 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all">Last</a>
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
</body>
</html>
