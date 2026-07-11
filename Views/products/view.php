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
                                     <div class="aspect-[2/3] rounded-xl overflow-hidden bg-gray-100 relative group">
                                         <!-- Blurred Background -->
                                         <img src="<?php echo !empty($images) ? 'https://srishringarr.com/yn/uploads' . $images[0]['img_name'] : 'assets/default-product.jpg'; ?>" 
                                              class="absolute inset-0 w-full h-full object-cover filter blur-md opacity-30 scale-105 pointer-events-none" alt="">
                                         <!-- Main Contain Image -->
                                         <img id="main-image" src="<?php echo !empty($images) ? 'https://srishringarr.com/yn/uploads' . $images[0]['img_name'] : 'assets/default-product.jpg'; ?>" 
                                              class="relative w-full h-full object-contain z-10 transition-transform duration-500" alt="Product Image">
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

                    <!-- AI Assistant Card (Full Width) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-8">
                        <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider mb-4 flex items-center">
                            <i class="fas fa-magic text-indigo-600 mr-2 animate-pulse"></i> AI Copywriter (Gemini)
                        </h3>
                        <p class="text-xs text-gray-400 mb-6">Analyze this product's photo using Google Gemini AI to generate names or descriptions.</p>

                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <button onclick="aiGenerateNames()" id="aiNamesBtn" class="flex-1 flex items-center justify-center space-x-2 py-3.5 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-all border border-indigo-100/50 cursor-pointer">
                                    <i class="fas fa-heading"></i>
                                    <span>Suggest Names</span>
                                </button>
                                <button onclick="aiGenerateDescription()" id="aiDescBtn" class="flex-1 flex items-center justify-center space-x-2 py-3.5 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-all border border-indigo-100/50 cursor-pointer">
                                    <i class="fas fa-align-left"></i>
                                    <span>Gen Description</span>
                                </button>
                            </div>

                            <!-- Loading indicator -->
                            <div id="aiLoading" class="hidden flex items-center justify-center space-x-2 py-3.5 bg-gray-50 rounded-xl text-xs font-semibold text-gray-600">
                                <div class="w-4 h-4 rounded-full border-2 border-indigo-600 border-t-transparent animate-spin"></div>
                                <span>AI is analyzing image...</span>
                            </div>

                            <!-- Results: Name Suggestions -->
                            <div id="aiNamesResult" class="hidden space-y-3 pt-4 border-t border-gray-100">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Suggested Names (Click to Apply)</h4>
                                <div id="aiNamesList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3"></div>
                            </div>

                            <!-- Results: Description Generator -->
                            <div id="aiDescResult" class="hidden space-y-3 pt-4 border-t border-gray-100">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Suggested Description</h4>
                                <textarea id="aiDescTextarea" rows="12" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs focus:ring-indigo-600 focus:border-indigo-600 focus:bg-white transition-all leading-relaxed"></textarea>
                                <button onclick="applyAiDescription()" id="applyDescBtn" class="w-full py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all shadow-md cursor-pointer">
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
                        <button onclick="applyProductName('${name.replace(/'/g, "\\'")}')" class="w-full text-left py-2 px-3 hover:bg-indigo-50 border border-gray-100 rounded-lg text-xs font-medium text-gray-700 transition-all flex justify-between items-center group cursor-pointer">
                            <span>${name}</span>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
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

            btn.disabled = true;
            loader.classList.remove('hidden');
            resultBox.classList.add('hidden');

            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestDescription&id=${productId}&type=${productType}`);
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
                    document.querySelector('h1.text-2xl').innerText = newName;
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
                    const descContainer = document.querySelector('.prose');
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
