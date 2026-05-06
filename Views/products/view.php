<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Product - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Product Details';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Header Actions -->
                    <div class="flex justify-between items-center mb-8">
                        <div class="flex items-center space-x-4">
                            <a href="index.php?controller=product&action=index" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 hover:text-primary transition-all">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></h1>
                                <p class="text-sm text-gray-400">SKU: <span class="font-semibold text-gray-600"><?php echo $product['code']; ?></span> • <?php echo ucfirst($type); ?></p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="../../../product.php?id=<?php echo $product['id']; ?>&type=<?php echo $type; ?>" target="_blank" class="px-6 py-3 bg-white text-gray-700 rounded-xl text-sm font-semibold border border-gray-200 hover:bg-gray-50 transition-all flex items-center">
                                <i class="fas fa-external-link-alt mr-2"></i> Preview on Website
                            </a>
                            <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>&type=<?php echo $type; ?>" class="px-6 py-3 bg-primary text-white rounded-xl text-sm font-semibold shadow-lg shadow-primary/20 hover:opacity-90 transition-all flex items-center">
                                <i class="fas fa-edit mr-2"></i> Edit Product
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Images -->
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="p-2">
                                    <div class="aspect-[3/4] rounded-xl overflow-hidden bg-gray-100 relative group">
                                        <img id="main-image" src="<?php echo !empty($images) ? 'https://srishringarr.com/yn/uploads' . $images[0]['img_name'] : 'assets/default-product.jpg'; ?>" 
                                             class="w-full h-full object-cover transition-transform duration-500" alt="Product Image">
                                        <?php if($product['featured']): ?>
                                            <div class="absolute top-4 right-4 bg-yellow-400 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-md">
                                                <i class="fas fa-star mr-1"></i> Featured
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if(count($images) > 1): ?>
                                    <div class="p-4 grid grid-cols-4 gap-2 border-t border-gray-50">
                                        <?php foreach($images as $img): ?>
                                            <button onclick="changeMainImage('https://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>')" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition-all">
                                                <img src="https://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>" class="w-full h-full object-cover" alt="Thumbnail">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Inventory Status</h3>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-600">Stock Quantity</span>
                                    </div>
                                    <span class="text-lg font-bold <?php echo ($product['quantity'] ?? 0) > 0 ? 'text-gray-800' : 'text-red-500'; ?>">
                                        <?php echo $product['quantity'] ?? 0; ?> Units
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Details -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Pricing Card -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-6">Pricing Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="p-6 bg-green-50 rounded-2xl border border-green-100">
                                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">Sales Price</p>
                                        <p class="text-2xl font-bold text-green-700">₹<?php echo number_format($product['s_price'], 2); ?></p>
                                    </div>
                                    <div class="p-6 bg-primary/5 rounded-2xl border border-primary/10">
                                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Rental Price</p>
                                        <p class="text-2xl font-bold text-primary">₹<?php echo number_format($product['rental_price'], 2); ?></p>
                                    </div>
                                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">Refundable Deposit</p>
                                        <p class="text-2xl font-bold text-blue-700">₹<?php echo number_format($product['deposit'], 2); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4">Product Description</h3>
                                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                                    <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : '<span class="italic text-gray-400">No description provided.</span>'; ?>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-6">Specifications</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                        <span class="text-sm text-gray-400">Category</span>
                                        <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                        <span class="text-sm text-gray-400">Subcategory</span>
                                        <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($product['subcategory_name'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                        <span class="text-sm text-gray-400">Product Type</span>
                                        <span class="text-sm font-semibold text-gray-700 capitalize"><?php echo $type; ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                        <span class="text-sm text-gray-400">Status</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        function changeMainImage(url) {
            const main = document.getElementById('main-image');
            main.style.opacity = '0';
            setTimeout(() => {
                main.src = url;
                main.style.opacity = '1';
            }, 200);
        }
    </script>
</body>
</html>
