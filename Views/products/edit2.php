<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Edit Product: ' . htmlspecialchars($product['code']);
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-8 pb-24 sm:pb-8 relative">
                <div class="max-w-4xl mx-auto">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm">
                            <i class="fas fa-check-circle mr-2"></i> Product updated successfully!
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Tabs (Disabled in Edit) -->
                        <div class="flex border-b border-gray-100 bg-gray-50/50">
                            <button type="button" class="flex-1 py-4 text-sm font-semibold border-b-2 <?php echo $type === 'jewellery' ? 'border-primary text-primary' : 'border-transparent text-gray-400'; ?> cursor-default">
                                <i class="fas fa-gem mr-2"></i> Jewellery
                            </button>
                            <button type="button" class="flex-1 py-4 text-sm font-semibold border-b-2 <?php echo $type === 'garments' ? 'border-primary text-primary' : 'border-transparent text-gray-400'; ?> cursor-default">
                                <i class="fas fa-tshirt mr-2"></i> Garments / Apparel
                            </button>
                        </div>

                        <form action="index.php?controller=product&action=update" method="POST" enctype="multipart/form-data" class="p-4 sm:p-8 space-y-6 sm:space-y-8">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="type" id="product_type" value="<?php echo $type; ?>">
                            <input type="hidden" name="code" value="<?php echo htmlspecialchars($product['code']); ?>">

                            <!-- SKU Section (Read-only in Edit) -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                    Product Code
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">SKU / Product Code</label>
                                        <input type="text" value="<?php echo htmlspecialchars($product['code']); ?>" disabled class="w-full bg-gray-100 border border-gray-200 rounded-xl p-3 text-sm text-gray-500 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            <div id="form_content" class="transition-all duration-300">
                                <!-- Basic Info -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                                        Basic Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Product Name</label>
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                                            <textarea name="description" id="product_desc_textarea" rows="8" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                            <p class="text-[10px] text-zinc-500 mt-1 flex items-center space-x-1">
                                                <i class="fas fa-info-circle text-[9px] text-indigo-400"></i>
                                                <span>Having formatting issues (bullets or double question marks)? Clean them using the <a href="index.php?controller=product&action=descriptionCorrector" class="text-indigo-400 hover:underline hover:text-indigo-300 font-semibold transition-all">Description Corrector Tool</a>.</span>
                                            </p>
                                        </div>

                                        <!-- AI Copywriter Helper Card (In-Place Form Updates) -->
                                        <div class="md:col-span-2 bg-zinc-950/20 border border-zinc-800 rounded-xl p-5 mt-2">
                                            <div class="flex items-center space-x-2 mb-1.5">
                                                <i class="fas fa-magic text-indigo-400 text-xs animate-pulse"></i>
                                                <h4 class="text-xs font-bold text-zinc-300 uppercase tracking-wider">AI Copywriter (Gemini)</h4>
                                            </div>
                                            <p class="text-[11px] text-zinc-550 mb-4">Analyze the product's image using Gemini to draft names or descriptions.</p>

                                            <div class="space-y-4">
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <button type="button" onclick="aiGenerateNames()" id="aiNamesBtn" class="flex-1 flex items-center justify-center space-x-2 py-2.5 px-4 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-300 transition-all cursor-pointer">
                                                        <i class="fas fa-heading text-[10px]"></i>
                                                        <span>Suggest Names</span>
                                                    </button>
                                                    <div class="flex-1 flex items-center space-x-2">
                                                        <input type="number" id="aiDescMaxWords" value="100" min="10" max="500" class="w-16 py-2.5 bg-zinc-900/80 border border-zinc-800 rounded-lg px-2 text-center text-xs text-zinc-300 focus:border-indigo-500 transition-all" title="Max Words" placeholder="Words">
                                                        <button type="button" onclick="aiGenerateDescription()" id="aiDescBtn" class="flex-1 flex items-center justify-center space-x-2 py-2.5 px-4 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-300 transition-all cursor-pointer">
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
                                                <div id="aiNamesResult" class="hidden space-y-2 pt-3 border-t border-zinc-900">
                                                    <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Suggested Names (Click to Apply)</h5>
                                                    <div id="aiNamesList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2"></div>
                                                </div>

                                                <!-- Results: Description Generator -->
                                                <div id="aiDescResult" class="hidden space-y-3 pt-3 border-t border-zinc-900">
                                                    <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Suggested Description</h5>
                                                    <textarea id="aiDescTextarea" rows="8" class="w-full bg-zinc-900/40 border border-zinc-800 rounded-lg p-3 text-xs text-zinc-300 focus:border-zinc-750 transition-all leading-relaxed font-light"></textarea>
                                                    <button type="button" onclick="applyAiDescription()" id="applyDescBtn" style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #ffffff !important;" class="w-full py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition-all cursor-pointer">
                                                        Apply to Description Field
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- AI Image Studio Helper Card -->
                                        <div class="md:col-span-2 bg-zinc-950/20 border border-zinc-800 rounded-xl p-5 mt-4">
                                            <div class="flex items-center space-x-2 mb-1.5">
                                                <i class="fas fa-camera text-pink-400 text-xs animate-pulse"></i>
                                                <h4 class="text-xs font-bold text-zinc-300 uppercase tracking-wider">AI Image Studio (Gemini)</h4>
                                            </div>
                                            <p class="text-[11px] text-zinc-550 mb-4">Generate an AI fashion model wearing this exact product.</p>

                                            <div class="space-y-4">
                                                <div class="flex flex-col sm:flex-row gap-3">
                                                    <div class="flex-1">
                                                        <?php
                                                        $catName = htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product'));
                                                        $defaultPrompt = "A photorealistic beautiful Indian fashion model wearing this exact $catName. The model should have open flowing hair (khule baal). The background should have elegant props (piche props) like a palace or traditional setting that compliments the jewelry perfectly. Do not change the $catName details. Show the full upper body.";
                                                        ?>
                                                        <input type="text" id="aiImagePrompt" value="<?= $defaultPrompt ?>" class="w-full bg-zinc-900/40 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-300 focus:border-pink-500 transition-all" placeholder="Enter prompt...">
                                                    </div>
                                                    <button type="button" onclick="aiGenerateModelImage()" id="aiImageBtn" class="sm:w-auto flex items-center justify-center space-x-2 py-2.5 px-6 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-300 transition-all cursor-pointer">
                                                        <i class="fas fa-image text-[10px]"></i>
                                                        <span>Generate Model Image</span>
                                                    </button>
                                                </div>

                                                <!-- Loading indicator -->
                                                <div id="aiImageLoading" class="hidden flex flex-col items-center justify-center space-y-3 py-6 bg-zinc-900/40 rounded-lg text-xs font-semibold text-zinc-400 border border-zinc-900">
                                                    <div class="w-5 h-5 rounded-full border-2 border-pink-400 border-t-transparent animate-spin"></div>
                                                    <span>AI is generating image. This may take 10-15 seconds...</span>
                                                </div>

                                                <!-- Results: Image Generator -->
                                                <div id="aiImageResult" class="hidden space-y-3 pt-3 border-t border-zinc-900">
                                                    <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Generated Image</h5>
                                                    <div class="flex justify-center bg-black/20 rounded-lg p-4 border border-zinc-800">
                                                        <img id="aiGeneratedImg" src="" alt="Generated Model" class="max-w-full h-auto max-h-96 rounded shadow-lg">
                                                    </div>
                                                    <div class="flex flex-col sm:flex-row gap-3 mt-3">
                                                        <button type="button" onclick="saveAiGeneratedImage()" id="aiSaveImgBtn" style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #ffffff !important;" class="flex-1 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition-all cursor-pointer">
                                                            <i class="fas fa-save mr-1"></i> Use This Image
                                                        </button>
                                                        <button type="button" onclick="resetAiImage()" id="aiResetImgBtn" class="flex-1 py-2 bg-zinc-900 border border-zinc-700 rounded-lg text-xs font-semibold hover:bg-zinc-800 transition-all cursor-pointer text-white">
                                                            <i class="fas fa-redo mr-1"></i> Try Something Else
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <?php if ($type === 'jewellery'): ?>
                                        <div id="jewellery_cats" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                                                <select name="category" id="jewel_cat" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($jewelCategories as $cat): ?>
                                                        <option value="<?php echo $cat['subcat_id']; ?>" <?php echo $product['category'] == $cat['subcat_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['categories_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subcategory</label>
                                                <select name="sub_category" id="jewel_subcat" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Subcategory</option>
                                                    <!-- Will be populated by JS on load if needed, but let's pre-populate for edit -->
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Size</label>
                                                <input type="text" name="size_avail" value="<?php echo htmlspecialchars($product['size_avail'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" placeholder="e.g. 5, 6, 7, 8 (or custom size)">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Brand</label>
                                                <input type="text" name="brand_name" value="<?php echo htmlspecialchars($product['brand_name'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" placeholder="e.g. Brand Name">
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div id="garment_cats" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Garment Type</label>
                                                <select name="category" id="garment_cat" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Type</option>
                                                    <?php foreach ($garments as $g): ?>
                                                        <option value="<?php echo $g['garment_id']; ?>" <?php echo $product['category'] == $g['garment_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($g['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subcategory</label>
                                                <select name="sub_category" id="garment_subcat" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Subcategory</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Size</label>
                                                <input type="text" name="size_avail" value="<?php echo htmlspecialchars($product['size_avail'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" placeholder="e.g. S, M, L, XL, 38, 40, 42">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Brand</label>
                                                <input type="text" name="brand_name" value="<?php echo htmlspecialchars($product['brand_name'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary" placeholder="e.g. Brand Name">
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="md:col-span-2 pt-4">
                                            <label class="relative inline-flex items-center cursor-pointer group">
                                                <input type="checkbox" name="featured" value="1" <?php echo ($product['featured'] ?? 0) == 1 ? 'checked' : ''; ?> class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                                <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors">Featured Product</span>
                                            </label>
                                            <p class="text-xs text-gray-400 mt-1 ml-14">Featured products are displayed in the Exclusive Collection on the homepage.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                                        Pricing Information
                                    </h3>

                                    <!-- Price Source Toggle -->
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Price Source</label>
                                                <p class="text-xs text-gray-400" id="price_source_description">
                                                    <?php echo ($product['price_source'] ?? 'pos') === 'manual' 
                                                        ? 'Prices are set manually from the fields below.' 
                                                        : 'Prices are auto-calculated from POS system data.'; ?>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs font-semibold <?php echo ($product['price_source'] ?? 'pos') === 'pos' ? 'text-primary' : 'text-gray-400'; ?>" id="label_pos">POS</span>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="price_source" value="manual" 
                                                           <?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'checked' : ''; ?> 
                                                           class="sr-only peer" id="price_source_toggle" onchange="togglePriceSource()">
                                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                                </label>
                                                <span class="text-xs font-semibold <?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'text-amber-600' : 'text-gray-400'; ?>" id="label_manual">Manual</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Availability -->
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Availability / Nature</label>
                                                <p class="text-xs text-gray-400">Control if this product is available for Rent, Sell, or Both.</p>
                                            </div>
                                            <div class="w-full sm:w-48">
                                                <select name="availability" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="both" <?php echo ($product['availability'] ?? 'both') === 'both' ? 'selected' : ''; ?>>Rent & Sell (Both)</option>
                                                    <option value="rent" <?php echo ($product['availability'] ?? 'both') === 'rent' ? 'selected' : ''; ?>>Rent Only</option>
                                                    <option value="sell" <?php echo ($product['availability'] ?? 'both') === 'sell' ? 'selected' : ''; ?>>Sell Only</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="pricing_fields">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sales Price</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="s_price" step="0.01" value="<?php echo $product['s_price']; ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary" style="padding-left: 2.25rem !important;">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rental Price</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="rental_price" step="0.01" value="<?php echo $product['rental_price']; ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary" style="padding-left: 2.25rem !important;">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deposit</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="deposit" step="0.01" value="<?php echo $product['deposit']; ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary" style="padding-left: 2.25rem !important;">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pos_price_note" class="<?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'hidden' : ''; ?> bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i> These values are stored but <strong>overridden</strong> by POS-calculated prices on the frontend. Switch to "Manual" to use these values directly.
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">4</span>
                                            Product Images
                                        </h3>
                                    </div>
                                    
                                    <!-- Existing Images -->
                                    <div class="grid grid-cols-4 md:grid-cols-6 gap-4 mb-6">
                                        <?php foreach ($images as $index => $img): ?>
                                            <div class="aspect-square relative group">
                                                <img src="../yn/uploads<?php echo $img['img_name']; ?>" class="w-full h-full object-contain rounded-xl shadow-sm border border-gray-100">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                                                    <span class="text-white text-xs font-semibold">
                                                        <?php echo ($index === 0) ? 'Main Image' : 'Existing'; ?>
                                                    </span>
                                                </div>
                                                
                                                <?php if ($index === 0): ?>
                                                    <div class="absolute top-2 left-2 bg-yellow-400 text-white w-7 h-7 rounded-full flex items-center justify-center shadow-md" title="Main Image">
                                                        <i class="fas fa-star text-xs"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <button type="button" onclick="setMainImage(this, <?php echo $img['id']; ?>)" class="absolute top-2 left-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-md scale-0 group-hover:scale-100 transition-all focus:outline-none z-10 cursor-pointer" title="Set as Main Image">
                                                        <i class="far fa-star text-xs"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <button type="button" onclick="deleteProductImage(this, <?php echo $img['id']; ?>)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-md transition-all scale-0 group-hover:scale-100 focus:outline-none z-10 cursor-pointer" title="Delete Image">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-primary transition-all group">
                                        <input type="file" name="images[]" id="img_upload" multiple accept="image/*" class="hidden">
                                        <label for="img_upload" class="cursor-pointer">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 group-hover:text-primary transition-all mb-4"></i>
                                            <p class="text-sm text-gray-500">Click to add more images</p>
                                        </label>
                                        <div id="img_preview" class="grid grid-cols-4 md:grid-cols-6 gap-4 mt-6"></div>
                                    </div>
                                </div>

                                <div class="sticky bottom-0 sm:static pt-4 pb-4 sm:pt-8 sm:pb-0 flex flex-col-reverse sm:flex-row justify-end sm:space-x-4 bg-white sm:bg-transparent border-t border-gray-100 sm:border-none px-4 sm:px-0 -mx-4 sm:mx-0 z-40 gap-3 sm:gap-0 mt-8 sm:mt-0 shadow-[0_-15px_15px_-10px_rgba(0,0,0,0.05)] sm:shadow-none">
                                    <a href="index.php?controller=product&action=index" class="w-full sm:w-auto px-8 py-3.5 sm:py-3 border border-gray-300 sm:border-zinc-800 rounded-xl text-sm font-semibold text-gray-700 sm:text-zinc-400 hover:text-gray-900 sm:hover:text-white hover:bg-gray-50 sm:hover:bg-zinc-900 transition-all flex items-center justify-center">
                                        <i class="fas fa-times mr-2 text-lg sm:text-base"></i> Cancel
                                    </a>
                                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 sm:py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all flex items-center justify-center">
                                        <i class="fas fa-save mr-2 text-lg sm:text-base"></i> Update Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        const currentType = '<?php echo $type; ?>';
        const currentSubId = '<?php echo $product['sub_category'] ?? ''; ?>';
        const currentCatId = '<?php echo $product['category'] ?? ''; ?>';

        // Initialize subcategories on load
        window.addEventListener('DOMContentLoaded', () => {
            if (currentCatId) {
                const targetId = currentType === 'jewellery' ? 'jewel_subcat' : 'garment_subcat';
                fetchSubcategories(currentType, currentCatId, targetId, currentSubId);
            }
        });

        if (document.getElementById('jewel_cat')) {
            document.getElementById('jewel_cat').addEventListener('change', function() {
                fetchSubcategories('jewellery', this.value, 'jewel_subcat');
            });
        }

        if (document.getElementById('garment_cat')) {
            document.getElementById('garment_cat').addEventListener('change', function() {
                fetchSubcategories('garments', this.value, 'garment_subcat');
            });
        }

        async function fetchSubcategories(type, parentId, targetId, selectedId = null) {
            const subDropdown = document.getElementById(targetId);
            if (!parentId) {
                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
                return;
            }

            try {
                const response = await fetch(`index.php?controller=product&action=getSubcategories&type=${type}&parent_id=${parentId}`);
                const data = await response.json();

                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.subcat_id;
                    opt.textContent = sub.name;
                    if (selectedId && sub.subcat_id == selectedId) {
                        opt.selected = true;
                    }
                    subDropdown.appendChild(opt);
                });
            } catch (error) {
                console.error('Error fetching subcategories:', error);
            }
        }

        document.getElementById('img_upload').addEventListener('change', function(e) {
            const preview = document.getElementById('img_preview');
            preview.innerHTML = '';
            [...this.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'aspect-square relative group';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl shadow-sm">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        async function deleteProductImage(btn, imgId) {
            if (!confirm('Are you sure you want to delete this image?')) return;
            
            try {
                const response = await fetch('index.php?controller=product&action=deleteImage', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: imgId })
                });
                
                const data = await response.json();
                if (data.success) {
                    const imgCard = btn.closest('.relative');
                    imgCard.remove();
                } else {
                    alert(data.error || 'Failed to delete the image');
                }
            } catch (error) {
                console.error('Error deleting image:', error);
                alert('An error occurred while deleting the image.');
            }
        }

        function readAsBase64(fileOrBlob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
                reader.readAsDataURL(fileOrBlob);
            });
        }

        async function suggestProductDetails() {
            const btn = document.getElementById('btn_ai_suggest');
            const originalText = btn.innerHTML;
            
            const imgUpload = document.getElementById('img_upload');
            let imageBase64 = null;
            
            try {
                if (imgUpload && imgUpload.files && imgUpload.files.length > 0) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Reading image...';
                    imageBase64 = await readAsBase64(imgUpload.files[0]);
                } else {
                    const existingImgs = document.querySelectorAll('.aspect-square.relative img');
                    if (existingImgs && existingImgs.length > 0) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Loading image...';
                        const firstImg = existingImgs[0];
                        const response = await fetch(firstImg.src);
                        if (!response.ok) throw new Error('Failed to fetch existing image file.');
                        const blob = await response.blob();
                        imageBase64 = await readAsBase64(blob);
                    }
                }
                
                if (!imageBase64) {
                    alert('Please select or upload at least one product image first.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return;
                }
                
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> AI is analyzing...';
                
                const fab = document.getElementById('chatbot-fab');
                const chatWindow = document.getElementById('chatbot-window');
                if (chatWindow && !chatWindow.classList.contains('visible') && fab) {
                    fab.click();
                }
                
                const type = document.getElementById('product_type')?.value || 'garments';
                const prompt = `Please analyze this uploaded product image for a ${type} product. Suggest a premium, attractive, and SEO-optimized Product Name and a detailed Product Description (mentioning the design style, fabric/materials, and collection appeal if appropriate). Formulate the suggestions clearly as:
Product Name: [Your suggested name]
Product Description: [Your suggested description]`;
                
                const response = await fetch('index.php?controller=chatbot&action=chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: prompt,
                        image: imageBase64,
                        history: []
                    })
                });
                
                const data = await response.json();
                if (data.success && data.reply) {
                    if (typeof addMessage === 'function') {
                        addMessage(data.reply, 'bot');
                        conversationHistory.push({ role: 'model', text: data.reply });
                    }
                    
                    const reply = data.reply;
                    const nameMatch = reply.match(/(?:Product Name|Suggested Name):\s*(.*?)(?:\n|$)/i);
                    const descMatch = reply.match(/(?:Product Description|Suggested Description|Description):\s*([\s\S]*)/i);
                    
                    let updatedCount = 0;
                    
                    if (nameMatch && nameMatch[1]) {
                        const suggestedName = nameMatch[1].replace(/^[\["']|[\]"']$/g, '').trim();
                        const nameInput = document.querySelector('input[name="name"]');
                        if (nameInput) {
                            nameInput.value = suggestedName;
                            nameInput.classList.add('ring-2', 'ring-green-400');
                            setTimeout(() => nameInput.classList.remove('ring-2', 'ring-green-400'), 3000);
                            updatedCount++;
                        }
                    }
                    
                    if (descMatch && descMatch[1]) {
                        const suggestedDesc = descMatch[1].replace(/^[\["']|[\]"']$/g, '').trim();
                        const descTextarea = document.querySelector('textarea[name="description"]');
                        if (descTextarea) {
                            descTextarea.value = suggestedDesc;
                            descTextarea.classList.add('ring-2', 'ring-green-400');
                            setTimeout(() => descTextarea.classList.remove('ring-2', 'ring-green-400'), 3000);
                            updatedCount++;
                        }
                    }
                    
                    if (updatedCount > 0) {
                        alert('AI suggestions successfully applied to the form fields!');
                    }
                } else {
                    alert(data.error || 'Failed to get suggestions from AI.');
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred during AI analysis: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
        function togglePriceSource() {
            const toggle = document.getElementById('price_source_toggle');
            const isManual = toggle.checked;
            const desc = document.getElementById('price_source_description');
            const labelPos = document.getElementById('label_pos');
            const labelManual = document.getElementById('label_manual');
            const posNote = document.getElementById('pos_price_note');
            const pricingFields = document.getElementById('pricing_fields');

            if (isManual) {
                desc.textContent = 'Prices are set manually from the fields below.';
                labelPos.className = 'text-xs font-semibold text-gray-400';
                labelManual.className = 'text-xs font-semibold text-amber-600';
                posNote.classList.add('hidden');
                pricingFields.classList.add('ring-2', 'ring-amber-400', 'rounded-xl', 'p-3');
            } else {
                desc.textContent = 'Prices are auto-calculated from POS system data.';
                labelPos.className = 'text-xs font-semibold text-primary';
                labelManual.className = 'text-xs font-semibold text-gray-400';
                posNote.classList.remove('hidden');
                pricingFields.classList.remove('ring-2', 'ring-amber-400', 'rounded-xl', 'p-3');
            }
        }

        async function setMainImage(btn, imageId) {
            if (!confirm('Make this the main product image?')) return;

            try {
                const response = await fetch('index.php?controller=product&action=setMainImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        image_id: imageId,
                        product_id: <?php echo $product['id']; ?>,
                        type: '<?php echo $type; ?>'
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Main image updated successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (err) {
                console.error(err);
                alert('Network request failed');
            }
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
                        <button type="button" onclick="applyProductName('${name.replace(/'/g, "\\'")}')" class="w-full text-left py-2 px-3 hover:bg-zinc-800 border border-zinc-900 hover:border-zinc-700 rounded-lg text-xs font-medium text-zinc-300 hover:text-white bg-zinc-950/40 transition-all flex justify-between items-center group cursor-pointer">
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

        function applyProductName(newName) {
            const nameInput = document.querySelector('input[name="name"]');
            if (nameInput) {
                nameInput.value = newName;
                nameInput.focus();
                nameInput.style.transition = 'all 0.3s ease';
                nameInput.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.4)';
                setTimeout(() => {
                    nameInput.style.boxShadow = '';
                }, 1000);
            }
        }

        function applyAiDescription() {
            const val = document.getElementById('aiDescTextarea').value.trim();
            const descInput = document.getElementById('product_desc_textarea');
            if (descInput && val) {
                descInput.value = val;
                descInput.focus();
                descInput.style.transition = 'all 0.3s ease';
                descInput.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.4)';
                setTimeout(() => {
                    descInput.style.boxShadow = '';
                }, 1000);
            }
        }

        async function aiGenerateModelImage() {
            const btn = document.getElementById('aiImageBtn');
            const loader = document.getElementById('aiImageLoading');
            const resultBox = document.getElementById('aiImageResult');
            const imgEl = document.getElementById('aiGeneratedImg');
            const prompt = document.getElementById('aiImagePrompt').value.trim();

            btn.disabled = true;
            loader.classList.remove('hidden');
            resultBox.classList.add('hidden');

            try {
                const response = await fetch(`index.php?controller=product&action=aiGenerateModelImage&id=${productId}&type=${productType}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: prompt })
                });
                const data = await response.json();
                
                if (data.success && data.image_base64) {
                    const imgSrc = `data:image/jpeg;base64,${data.image_base64}`;
                    imgEl.src = imgSrc;
                    window.currentGeneratedAiImage = data.image_base64;
                    resultBox.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate image'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }

        function resetAiImage() {
            document.getElementById('aiImageResult').classList.add('hidden');
            window.currentGeneratedAiImage = null;
            document.getElementById('aiImagePrompt').focus();
        }

        async function saveAiGeneratedImage() {
            if (!window.currentGeneratedAiImage) return;

            const btn = document.getElementById('aiSaveImgBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
            btn.disabled = true;

            try {
                const response = await fetch(`index.php?controller=product&action=saveAiImage&id=${productId}&type=${productType}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image_base64: window.currentGeneratedAiImage })
                });
                const data = await response.json();
                
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-1 text-green-600"></i> Saved!';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (data.error || 'Failed to save image'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred while saving.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
