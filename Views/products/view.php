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
                            <a href="../../../product.php?id=<?php echo $product['id']; ?>&type=<?php echo $type; ?>" target="_blank" style="background-color: #18181b !important; color: #d4d4d8 !important; border: 1px solid #27272a !important;" class="px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-zinc-800 transition-all flex items-center">
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

                    <!-- AI Assistant Card (Full Width) -->
                    <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 sm:p-6 mt-8">
                        <div class="flex items-center space-x-2 mb-1.5">
                            <i class="fas fa-magic text-indigo-400 text-xs animate-pulse"></i>
                            <h3 class="text-xs font-bold text-zinc-300 uppercase tracking-wider">AI Copywriter (Gemini)</h3>
                        </div>
                        <p class="text-[11px] text-zinc-500 mb-5">Analyze this product's photo using Google Gemini AI to generate descriptive names or a new description.</p>

                        <div class="space-y-6">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button onclick="aiGenerateNames()" id="aiNamesBtn" class="flex-1 flex items-center justify-center space-x-2 py-2.5 px-4 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-300 transition-all cursor-pointer">
                                    <i class="fas fa-heading text-[10px]"></i>
                                    <span>Suggest Names</span>
                                </button>
                                <div class="flex-1 flex items-center space-x-2">
                                    <input type="number" id="aiDescMaxWords" value="100" min="10" max="500" class="w-16 py-2.5 bg-zinc-900/80 border border-zinc-800 rounded-lg px-2 text-center text-xs text-zinc-300 focus:border-indigo-500 transition-all" title="Max Words" placeholder="Words">
                                    <button onclick="aiGenerateDescription()" id="aiDescBtn" class="flex-1 flex items-center justify-center space-x-2 py-2.5 px-4 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-300 transition-all cursor-pointer">
                                        <i class="fas fa-align-left text-[10px]"></i>
                                        <span>Gen Description</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Loading indicator -->
                            <div id="aiLoading" class="hidden flex items-center justify-center space-x-2 py-3 bg-zinc-900/40 rounded-lg text-xs font-semibold text-zinc-400 border border-zinc-900">
                                <div class="w-3.5 h-3.5 rounded-full border-2 border-indigo-400 border-t-transparent animate-spin"></div>
                                <span>AI is analyzing image...</span>
                            </div>

                            <!-- Results: Name Suggestions -->
                            <div id="aiNamesResult" class="hidden space-y-2 pt-4 border-t border-zinc-900">
                                <h4 class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Suggested Names (Click to Apply)</h4>
                                <div id="aiNamesList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3"></div>
                            </div>

                            <!-- Results: Description Generator -->
                            <div id="aiDescResult" class="hidden space-y-3 pt-4 border-t border-zinc-900">
                                <h4 class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Suggested Description</h4>
                                <textarea id="aiDescTextarea" rows="12" class="w-full bg-zinc-900/40 border border-zinc-800 rounded-lg p-3 text-xs text-zinc-300 focus:border-zinc-750 transition-all leading-relaxed font-light"></textarea>
                                <button onclick="applyAiDescription()" id="applyDescBtn" class="w-full py-2.5 bg-white hover:bg-zinc-200 text-black rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                    Apply & Save Description
                                </button>
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

        const productId = <?php echo $product['id']; ?>;
        const productType = '<?php echo $type; ?>';

        async function aiGenerateNames() {
            const btn = document.getElementById('aiNamesBtn');
            const loader = document.getElementById('aiLoading');
            const resultBox = document.getElementById('aiNamesResult');
            const namesList = document.getElementById('aiNamesList');

            btn.disabled = true;
            loader.classList.remove('hidden');
            resultBox.classList.add('hidden');

            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestNames&id=${productId}&type=${productType}`);
                const data = await response.json();
                
                if (data.success && data.names) {
                    namesList.innerHTML = data.names.map(name => `
                        <button onclick="applyProductName('${name.replace(/'/g, "\\'")}')" class="w-full text-left py-2 px-3 hover:bg-zinc-800 border border-zinc-900 hover:border-zinc-700 rounded-lg text-xs font-medium text-zinc-300 hover:text-white bg-zinc-950/40 transition-all flex justify-between items-center group cursor-pointer">
                            <span>${name}</span>
                            <i class="fas fa-chevron-right text-zinc-600 group-hover:text-white group-hover:translate-x-0.5 transition-all"></i>
                        </button>
                    `).join('');
                    resultBox.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate names'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }

        async function aiGenerateDescription() {
            const btn = document.getElementById('aiDescBtn');
            const loader = document.getElementById('aiLoading');
            const resultBox = document.getElementById('aiDescResult');
            const textarea = document.getElementById('aiDescTextarea');
            const maxWords = document.getElementById('aiDescMaxWords')?.value || 100;

            btn.disabled = true;
            loader.classList.remove('hidden');
            resultBox.classList.add('hidden');

            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestDescription&id=${productId}&type=${productType}&max_words=${maxWords}`);
                const data = await response.json();
                
                if (data.success && data.description) {
                    textarea.value = data.description;
                    resultBox.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate description'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }

        async function applyProductName(newName) {
            if (!confirm(`Are you sure you want to change the product name to:\n"${newName}"?`)) {
                return;
            }

            try {
                const response = await fetch('index.php?controller=product&action=saveProductField', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: productId,
                        type: productType,
                        field: 'name',
                        value: newName
                    })
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('product-title').innerText = newName;
                    alert('Product name updated successfully!');
                    document.getElementById('aiNamesResult').classList.add('hidden');
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Failed to update name.');
            }
        }

        async function applyAiDescription() {
            const value = document.getElementById('aiDescTextarea').value.trim();
            if (!value) {
                alert('Description cannot be empty');
                return;
            }

            try {
                const response = await fetch('index.php?controller=product&action=saveProductField', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: productId,
                        type: productType,
                        field: 'description',
                        value: value
                    })
                });
                const data = await response.json();
                if (data.success) {
                    const descContainer = document.getElementById('product-description-content');
                    if (descContainer) {
                        descContainer.innerHTML = value.replace(/\n/g, '<br>');
                    }
                    alert('Product description updated successfully!');
                    document.getElementById('aiDescResult').classList.add('hidden');
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Failed to update description.');
            }
        }
    </script>
</body>
</html>
