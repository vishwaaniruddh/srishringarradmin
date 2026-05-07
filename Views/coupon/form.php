<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $pageTitle; ?> - Srishringarr</title>
    <?php include 'Views/partials/head.php'; ?>
    <style>
        .tab-btn.active {
            color: #ec4899;
            border-bottom: 2px solid #ec4899;
            background-color: #fdf2f8;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'Views/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = $pageTitle;
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <form action="index.php?controller=coupon&action=save" method="POST" class="max-w-4xl mx-auto">
                    <input type="hidden" name="id" value="<?php echo $coupon['id'] ?? ''; ?>">
                    
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800"><?php echo $coupon ? 'Edit' : 'Add New'; ?> Coupon</h2>
                            <p class="text-gray-500 mt-1">Define discount rules and usage restrictions.</p>
                        </div>
                        <div class="space-x-3">
                            <a href="index.php?controller=coupon" class="px-4 py-2 text-gray-600 font-medium hover:text-gray-800 transition-all">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all font-semibold shadow-lg shadow-primary/20">
                                <?php echo $coupon ? 'Update' : 'Publish'; ?> Coupon
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Settings -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Code & Description -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Coupon Code</label>
                                    <div class="flex gap-2">
                                        <input type="text" id="coupon_code" name="code" value="<?php echo $coupon['code'] ?? ''; ?>" placeholder="e.g. SUMMER25" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-bold text-lg uppercase" required>
                                        <button type="button" onclick="generateRandomCode()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all font-medium">
                                            Generate
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Codes are case-insensitive. Customers enter this code at checkout.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description (Optional)</label>
                                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"><?php echo $coupon['description'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Coupon Data Tabs -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="flex border-b border-gray-100 bg-gray-50/50">
                                    <button type="button" onclick="showTab('general')" class="tab-btn active px-6 py-4 text-sm font-semibold text-gray-500 transition-all flex items-center" id="tab-btn-general">
                                        <i class="fas fa-cog mr-2"></i> General
                                    </button>
                                    <button type="button" onclick="showTab('restriction')" class="tab-btn px-6 py-4 text-sm font-semibold text-gray-500 transition-all flex items-center" id="tab-btn-restriction">
                                        <i class="fas fa-ban mr-2"></i> Usage Restriction
                                    </button>
                                    <button type="button" onclick="showTab('limits')" class="tab-btn px-6 py-4 text-sm font-semibold text-gray-500 transition-all flex items-center" id="tab-btn-limits">
                                        <i class="fas fa-redo mr-2"></i> Usage Limits
                                    </button>
                                </div>

                                <div class="p-6">
                                    <!-- General Tab -->
                                    <div id="tab-general" class="tab-content space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
                                                <select name="discount_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all">
                                                    <option value="percent" <?php echo ($coupon['discount_type'] ?? '') === 'percent' ? 'selected' : ''; ?>>Percentage discount</option>
                                                    <option value="fixed_cart" <?php echo ($coupon['discount_type'] ?? '') === 'fixed_cart' ? 'selected' : ''; ?>>Fixed cart discount</option>
                                                    <option value="fixed_product" <?php echo ($coupon['discount_type'] ?? '') === 'fixed_product' ? 'selected' : ''; ?>>Fixed product discount</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Coupon Amount</label>
                                                <div class="relative">
                                                    <input type="number" step="0.01" name="coupon_amount" value="<?php echo $coupon['coupon_amount'] ?? '0'; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all pl-10" required>
                                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                                        <i class="fas fa-tag"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Coupon Expiry Date</label>
                                            <input type="date" name="expiry_date" value="<?php echo $coupon['expiry_date'] ?? ''; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all">
                                        </div>
                                    </div>

                                    <!-- Restriction Tab -->
                                    <div id="tab-restriction" class="tab-content hidden space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Spend</label>
                                                <input type="number" step="0.01" name="minimum_amount" value="<?php echo $coupon['minimum_amount'] ?? ''; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all" placeholder="No minimum">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Spend</label>
                                                <input type="number" step="0.01" name="maximum_amount" value="<?php echo $coupon['maximum_amount'] ?? ''; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all" placeholder="No maximum">
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <label class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all">
                                                <input type="checkbox" name="individual_use" value="1" <?php echo ($coupon['individual_use'] ?? 0) ? 'checked' : ''; ?> class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                                                <div>
                                                    <span class="block text-sm font-semibold text-gray-700">Individual use only</span>
                                                    <span class="block text-xs text-gray-400">Check this box if the coupon cannot be used in conjunction with other coupons.</span>
                                                </div>
                                            </label>

                                            <label class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-all">
                                                <input type="checkbox" name="exclude_sale_items" value="1" <?php echo ($coupon['exclude_sale_items'] ?? 0) ? 'checked' : ''; ?> class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                                                <div>
                                                    <span class="block text-sm font-semibold text-gray-700">Exclude sale items</span>
                                                    <span class="block text-xs text-gray-400">Check this box if the coupon should not apply to items on sale.</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Limits Tab -->
                                    <div id="tab-limits" class="tab-content hidden space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Usage limit per coupon</label>
                                                <input type="number" name="usage_limit" value="<?php echo $coupon['usage_limit'] ?? ''; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all" placeholder="Unlimited usage">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Usage limit per user</label>
                                                <input type="number" name="usage_limit_per_user" value="<?php echo $coupon['usage_limit_per_user'] ?? ''; ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none transition-all" placeholder="Unlimited usage">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Settings -->
                        <div class="space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Status</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" name="status" value="active" <?php echo ($coupon['status'] ?? 'active') === 'active' ? 'checked' : ''; ?> class="text-primary focus:ring-primary">
                                        <span class="text-sm text-gray-700 font-medium">Active</span>
                                    </label>
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" name="status" value="disabled" <?php echo ($coupon['status'] ?? '') === 'disabled' ? 'checked' : ''; ?> class="text-primary focus:ring-primary">
                                        <span class="text-sm text-gray-700 font-medium">Disabled</span>
                                    </label>
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" name="status" value="expired" <?php echo ($coupon['status'] ?? '') === 'expired' ? 'checked' : ''; ?> class="text-primary focus:ring-primary">
                                        <span class="text-sm text-gray-700 font-medium">Expired</span>
                                    </label>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-primary/10 to-secondary/10 p-6 rounded-2xl border border-primary/10">
                                <h3 class="text-sm font-bold text-primary mb-2">Pro Tip 💡</h3>
                                <p class="text-xs text-gray-600 leading-relaxed">
                                    Using percentage discounts (e.g. 10%) usually performs better for lower priced items, while fixed discounts work best for high-value items.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
    <script>
        function showTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            // Show target content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab-btn-' + tabId).classList.add('active');
        }

        function generateRandomCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('coupon_code').value = result;
        }
    </script>
</body>
</html>
