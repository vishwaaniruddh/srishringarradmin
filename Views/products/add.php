<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Add New Product';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-4xl mx-auto">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Tabs -->
                        <div class="flex border-b border-gray-100">
                            <button onclick="switchTab('jewellery')" id="tab-jewellery" class="flex-1 py-4 text-sm font-semibold border-b-2 border-primary text-primary transition-all">
                                <i class="fas fa-gem mr-2"></i> Jewellery
                            </button>
                            <button onclick="switchTab('garments')" id="tab-garments" class="flex-1 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all">
                                <i class="fas fa-tshirt mr-2"></i> Garments / Apparel
                            </button>
                        </div>

                        <form action="index.php?controller=product&action=store" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                            <input type="hidden" name="type" id="product_type" value="jewellery">

                            <!-- SKU Section -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                    POS Verification
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">SKU / Product Code</label>
                                        <div class="relative">
                                            <input type="text" name="code" id="sku_input" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary transition-all">
                                            <div id="sku_loader" class="hidden absolute right-3 top-3 text-primary animate-spin">
                                                <i class="fas fa-spinner"></i>
                                            </div>
                                        </div>
                                        <p id="sku_message" class="mt-2 text-xs"></p>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="verifySKU()" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                                            Verify SKU
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="form_content" class="opacity-50 pointer-events-none transition-all duration-300">
                                <!-- Basic Info -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                                        Basic Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Product Name</label>
                                            <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                                            <textarea name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary"></textarea>
                                        </div>
                                        
                                        <div id="jewellery_cats" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                                                <select name="category" id="jewel_cat" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($jewelCategories as $cat): ?>
                                                        <option value="<?php echo $cat['subcat_id']; ?>"><?php echo htmlspecialchars($cat['categories_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subcategory</label>
                                                <select name="sub_category" id="jewel_subcat" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Subcategory</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div id="garment_cats" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Garment Type</label>
                                                <select name="category" id="garment_cat" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Type</option>
                                                    <?php foreach ($garments as $g): ?>
                                                        <option value="<?php echo $g['garment_id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subcategory</label>
                                                <select name="sub_category" id="garment_subcat" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                                    <option value="">Select Subcategory</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                                        Pricing Information
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sales Price</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="s_price" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rental Price</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="rental_price" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deposit</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">₹</span>
                                                <input type="number" name="deposit" step="0.01" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 p-3 text-sm focus:ring-primary focus:border-primary">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 text-sm">4</span>
                                        Product Images
                                    </h3>
                                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-primary transition-all group">
                                        <input type="file" name="images[]" id="img_upload" multiple accept="image/*" class="hidden">
                                        <label for="img_upload" class="cursor-pointer">
                                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 group-hover:text-primary transition-all mb-4"></i>
                                            <p class="text-sm text-gray-500">Click to browse or drag and drop images here</p>
                                        </label>
                                        <div id="img_preview" class="grid grid-cols-4 md:grid-cols-6 gap-4 mt-6"></div>
                                    </div>
                                </div>

                                <div class="pt-8 flex justify-end space-x-4">
                                    <a href="index.php?controller=product&action=index" class="px-8 py-3 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-all">Cancel</a>
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all">
                                        Save Product
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
        function switchTab(type) {
            const tabs = {
                jewellery: { btn: 'tab-jewellery', cats: 'jewellery_cats' },
                garments: { btn: 'tab-garments', cats: 'garment_cats' }
            };

            document.getElementById('product_type').value = type;
            
            ['jewellery', 'garments'].forEach(t => {
                const btn = document.getElementById(tabs[t].btn);
                const cats = document.getElementById(tabs[t].cats);
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-gray-400');
                cats.classList.add('hidden');
                cats.querySelectorAll('select, input').forEach(i => i.required = false);
            });

            const active = document.getElementById(tabs[type].btn);
            const activeCats = document.getElementById(tabs[type].cats);
            active.classList.remove('border-transparent', 'text-gray-400');
            active.classList.add('border-primary', 'text-primary');
            activeCats.classList.remove('hidden');
            activeCats.querySelectorAll('select, input').forEach(i => i.required = true);
        }

        async function verifySKU() {
            const sku = document.getElementById('sku_input').value;
            const message = document.getElementById('sku_message');
            const loader = document.getElementById('sku_loader');
            const content = document.getElementById('form_content');

            if (!sku) return;
            loader.classList.remove('hidden');
            message.textContent = '';

            try {
                const response = await fetch(`index.php?controller=product&action=checkSku&sku=${encodeURIComponent(sku)}`);
                const data = await response.json();

                if (data.allowed) {
                    message.className = 'mt-2 text-xs text-green-500 font-medium';
                    message.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${data.message}`;
                    content.classList.remove('opacity-50', 'pointer-events-none');
                } else {
                    message.className = 'mt-2 text-xs text-red-500 font-medium';
                    message.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
                    content.classList.add('opacity-50', 'pointer-events-none');
                }
            } catch (error) {
                message.textContent = 'Error verifying SKU.';
            } finally {
                loader.classList.add('hidden');
            }
        }

        document.getElementById('jewel_cat').addEventListener('change', function() {
            fetchSubcategories('jewellery', this.value, 'jewel_subcat');
        });

        document.getElementById('garment_cat').addEventListener('change', function() {
            fetchSubcategories('garments', this.value, 'garment_subcat');
        });

        async function fetchSubcategories(type, parentId, targetId) {
            const subDropdown = document.getElementById(targetId);
            if (!parentId) {
                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
                return;
            }

            const response = await fetch(`index.php?controller=product&action=getSubcategories&type=${type}&parent_id=${parentId}`);
            const data = await response.json();

            subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
            data.forEach(sub => {
                const opt = document.createElement('option');
                opt.value = sub.subcat_id;
                opt.textContent = sub.name;
                subDropdown.appendChild(opt);
            });
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
    </script>
</body>
</html>
