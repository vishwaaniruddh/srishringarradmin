<!DOCTYPE html>
<html lang="en">
<head>
    <title>Coupons - Srishringarr</title>
    <?php include 'Views/partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'Views/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Coupons';
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>

                <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Coupon Management</h2>
                        <p class="text-gray-500 mt-1">Manage your promotional codes and discount rules.</p>
                    </div>
                    <a href="index.php?controller=coupon&action=add" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all font-medium inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create Coupon
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <?php if (empty($coupons)): ?>
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-ticket-alt text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">No Coupons Yet</h3>
                            <p class="text-gray-500 mt-2 max-w-sm mx-auto">Start by creating your first coupon to offer discounts to your customers.</p>
                            <a href="index.php?controller=coupon&action=add" class="mt-6 inline-block text-primary font-semibold hover:underline text-sm">Create your first coupon &rarr;</a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Coupon Code</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Discount Type</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Usage</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($coupons as $c): ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-800"><?php echo htmlspecialchars($c['code']); ?></div>
                                                <?php if ($c['description']): ?>
                                                    <div class="text-xs text-gray-400 mt-1 truncate max-w-[200px]"><?php echo htmlspecialchars($c['description']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php 
                                                $types = [
                                                    'percent' => 'Percentage discount',
                                                    'fixed_cart' => 'Fixed cart discount',
                                                    'fixed_product' => 'Fixed product discount'
                                                ];
                                                echo $types[$c['discount_type']] ?? $c['discount_type'];
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                                <?php echo ($c['discount_type'] === 'percent') ? $c['coupon_amount'] . '%' : '₹' . $c['coupon_amount']; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <?php echo $c['expiry_date'] ? date('M d, Y', strtotime($c['expiry_date'])) : 'Never'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <?php echo $c['usage_count']; ?> / <?php echo $c['usage_limit'] ?? '∞'; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-[10px] font-bold uppercase rounded-full <?php 
                                                    echo $c['status'] === 'active' ? 'bg-green-100 text-green-600' : 
                                                        ($c['status'] === 'expired' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'); 
                                                ?>">
                                                    <?php echo $c['status']; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-3">
                                                <a href="index.php?controller=coupon&action=edit&id=<?php echo $c['id']; ?>" class="text-gray-400 hover:text-primary transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?controller=coupon&action=delete&id=<?php echo $c['id']; ?>" class="text-gray-400 hover:text-red-50 transition-colors" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
</body>
</html>
