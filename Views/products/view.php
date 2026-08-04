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

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-5xl mx-auto">
                    <!-- Top Minimal Header -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-zinc-900">
                        <div class="flex items-center space-x-3">
                            <a href="index.php?controller=product&action=index" class="w-8 h-8 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                                <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                            <span class="text-xs text-zinc-500 hidden sm:inline">Back to Products</span>
                        </div>
                        <div class="flex space-x-2">
                            <?php 
                            $slug = strtolower($product['name'] ?? '');
                            $slug = preg_replace('/[^\w\s-]/u', '', $slug);
                            $slug = preg_replace('/[\s_-]+/', '-', $slug);
                            $slug = trim($slug, '-');
                            $livePreviewUrl = "https://srishringarr.com/product/" . ($slug ?: 'product') . "-" . (int)$product['id'];
                            ?>
                            <a href="<?php echo $livePreviewUrl; ?>" target="_blank" style="background-color: #18181b !important; color: #d4d4d8 !important; border: 1px solid #27272a !important;" class="px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-zinc-800 transition-all flex items-center">
                                <i class="fas fa-external-link-alt mr-1.5 text-[10px]"></i> Preview
                            </a>
                            <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>&type=<?php echo $type; ?>" style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #ffffff !important;" class="px-4 py-1.5 rounded-lg text-xs font-semibold hover:opacity-90 transition-all flex items-center">
                                <i class="fas fa-edit mr-1.5 text-[10px]"></i> Edit
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Left Column: Product Image -->
                        <div class="lg:col-span-5 space-y-4">
                            <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-3 flex flex-col items-center">
                                <div class="w-full aspect-square bg-zinc-900/20 rounded-lg overflow-hidden flex items-center justify-center relative p-4 border border-zinc-900/60">
                                    <img id="main-image" src="<?php echo !empty($images) ? 'https://srishringarr.com/yn/uploads' . $images[0]['img_name'] : 'assets/default-product.jpg'; ?>" 
                                         class="max-h-[280px] w-auto object-contain transition-all duration-300" alt="Product Image">
                                    <?php if($product['featured']): ?>
                                        <div class="absolute top-3 right-3 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider">
                                            <i class="fas fa-star mr-0.5"></i> Featured
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if(count($images) > 1): ?>
                                    <div class="w-full grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-zinc-900">
                                        <?php foreach($images as $img): ?>
                                            <button onclick="changeMainImage('https://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>')" class="aspect-square rounded-lg overflow-hidden border border-zinc-900 hover:border-zinc-700 transition-all bg-zinc-900/40 p-1">
                                                <img src="https://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>" class="w-full h-full object-contain rounded-md" alt="Thumbnail">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Column: Product Info -->
                        <div class="lg:col-span-7 space-y-6">
                            <!-- Product Title & SKU -->
                            <div>
                                <h1 id="product-title" class="text-xl sm:text-2xl font-bold text-white tracking-tight leading-snug"><?php echo htmlspecialchars($product['name']); ?></h1>
                                <div class="flex items-center space-x-2 mt-2">
                                    <span class="text-xs text-zinc-500 font-mono">SKU: <?php echo $product['code']; ?></span>
                                    <span class="text-zinc-700">•</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono">
                                        <?php echo ($type === 'garments' ? 'gproduct_id: ' : 'product_id: ') . $product['id']; ?>
                                    </span>
                                    <span class="text-zinc-700">•</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-900 text-zinc-400 border border-zinc-850 capitalize"><?php echo $type; ?></span>
                                </div>
                            </div>

                            <!-- Pricing Matrix (Sleek Row) -->
                            <div class="grid grid-cols-3 gap-3 p-4 bg-zinc-950 border border-zinc-900 rounded-xl">
                                <div class="text-center">
                                    <span class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Sales Price</span>
                                    <span class="text-sm sm:text-base font-bold text-emerald-400">₹<?php echo number_format((float)($product['s_price'] ?? 0), 2); ?></span>
                                </div>
                                <div class="text-center border-l border-zinc-900">
                                    <span class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Rental Price</span>
                                    <span class="text-sm sm:text-base font-bold text-indigo-400">₹<?php echo number_format((float)($product['rental_price'] ?? 0), 2); ?></span>
                                </div>
                                <div class="text-center border-l border-zinc-900">
                                    <span class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Deposit</span>
                                    <span class="text-sm sm:text-base font-bold text-blue-400">₹<?php echo number_format((float)($product['deposit'] ?? 0), 2); ?></span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5">
                                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2.5">Product Description</h3>
                                <div id="product-description-content" class="text-sm text-zinc-300 leading-relaxed font-light">
                                    <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : '<span class="italic text-zinc-600">No description provided.</span>'; ?>
                                </div>
                            </div>

                            <!-- Specifications -->
                            <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5">
                                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Specifications</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Category</span>
                                        <span class="text-xs font-medium text-zinc-300"><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Subcategory</span>
                                        <span class="text-xs font-medium text-zinc-300"><?php echo htmlspecialchars($product['subcategory_name'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Brand</span>
                                        <span class="text-xs font-medium text-zinc-300"><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Sizes</span>
                                        <span class="text-xs font-medium text-zinc-300"><?php echo htmlspecialchars($product['size_avail'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Stock Status</span>
                                        <span class="text-xs font-bold <?php echo ($product['quantity'] ?? 0) > 0 ? 'text-zinc-300' : 'text-rose-500'; ?>">
                                            <?php echo ($product['quantity'] ?? 0) > 0 ? ($product['quantity'] . ' Units') : 'Out of Stock'; ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-zinc-900/60">
                                        <span class="text-xs text-zinc-500">Status</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-950/30 text-emerald-400 border border-emerald-900/30">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
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
