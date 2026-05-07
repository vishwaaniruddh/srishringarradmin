<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order #<?php echo $order['id'] + 5000; ?> Details - Srishringarr</title>
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
            $pageTitle = "Order Details #".($order['id'] + 5000);
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <!-- Back Link -->
                <div class="mb-8">
                    <a href="index.php?controller=orders" class="text-sm font-bold text-gray-400 hover:text-primary transition-all flex items-center gap-2 group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        Back to Orders
                    </a>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Left: Order Info & Items -->
                    <div class="xl:col-span-2 space-y-8">
                        <!-- Order Status & Summary -->
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-2xl">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h2 class="text-2xl font-bold text-gray-800">Order #<?php echo $order['id'] + 5000; ?></h2>
                                        <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-widest rounded-full">
                                            <?php echo $order['status'] ?: 'Paid'; ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">Placed on <?php echo date('d M, Y \a\t h:i A', strtotime($order['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Print Invoice</button>
                                <button class="px-4 py-2 bg-primary text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:shadow-lg shadow-primary/20 transition-all">Ship Order</button>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                                <h3 class="font-bold text-gray-800">Order Items (<?php echo count($details); ?>)</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50/20">
                                            <th class="px-6 py-4 text-xs uppercase font-bold text-gray-400">Piece Details</th>
                                            <th class="px-6 py-4 text-xs uppercase font-bold text-gray-400">Booking Info</th>
                                            <th class="px-6 py-4 text-xs uppercase font-bold text-gray-400 text-center">Qty</th>
                                            <th class="px-6 py-4 text-xs uppercase font-bold text-gray-400 text-right">Price</th>
                                            <th class="px-6 py-4 text-xs uppercase font-bold text-gray-400 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($details as $item): ?>
                                        <tr class="hover:bg-gray-50/30 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-100">
                                                        <?php 
                                                        $imgPath = !empty($item['img_name']) ? "https://srishringarr.com/yn/uploads" . $item['img_name'] : 'https://srishringarr.com/static/images/default.jpg';
                                                        ?>
                                                        <img src="<?php echo $imgPath; ?>" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x150?text=Piece'">
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800"><?php echo $item['product_name'] ?: 'Heritage Piece'; ?></p>
                                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">SKU: <?php echo $item['sku']; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <?php if ($item['booking_type'] === 'rent'): ?>
                                                        <span class="text-[11px] font-bold text-gray-700"><?php echo date('d M', strtotime($item['start_date'])); ?> - <?php echo date('d M, Y', strtotime($item['end_date'])); ?></span>
                                                        <span class="text-[10px] text-[#800020] font-bold uppercase tracking-tighter italic">Rental (<?php echo $item['days']; ?> Days)</span>
                                                    <?php else: ?>
                                                        <span class="text-[10px] text-green-600 font-bold uppercase tracking-tighter">Direct Purchase</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-sm font-medium text-gray-600"><?php echo $item['qty']; ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="text-sm font-bold text-gray-800">₹<?php echo number_format($item['price']); ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="text-sm font-bold text-primary">₹<?php echo number_format($item['total']); ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-8 bg-gray-50/30">
                                <div class="flex justify-end">
                                    <div class="w-full max-w-xs space-y-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Order Subtotal</span>
                                            <span class="text-gray-800 font-bold">₹<?php echo number_format($order['total_amount']); ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Processing Fees</span>
                                            <span class="text-gray-800 font-bold">₹0</span>
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                                            <span class="text-gray-800 font-bold">Total Amount Paid</span>
                                            <span class="text-xl font-bold text-primary">₹<?php echo number_format($order['total_amount']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Customer & Payment Info -->
                    <div class="space-y-8">
                        <!-- Customer Profile -->
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Customer Details</h3>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-lg">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800"><?php echo $order['cust_name'] ?: 'Guest'; ?></p>
                                    <p class="text-xs text-gray-500">ID: #<?php echo $order['user_id']; ?></p>
                                </div>
                            </div>
                            <div class="space-y-4 pt-6 border-t border-gray-50">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-phone text-gray-300 w-5"></i>
                                    <span class="text-sm text-gray-600"><?php echo $order['phone'] ?: 'N/A'; ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-envelope text-gray-300 w-5"></i>
                                    <span class="text-sm text-gray-600 truncate"><?php echo $order['email']; ?></span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-map-marker-alt text-gray-300 w-5 mt-1"></i>
                                    <span class="text-sm text-gray-600"><?php echo $order['address']; ?>, <?php echo $order['city']; ?>, <?php echo $order['state']; ?> - <?php echo $order['pincode']; ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Payment Info</h3>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-gray-500 uppercase">Method</span>
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase">Razorpay</span>
                            </div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-xs font-bold text-gray-500 uppercase">Status</span>
                                <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded uppercase">Paid</span>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl mb-3">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Razorpay Order ID</p>
                                <p class="text-[11px] font-mono text-gray-600 break-all"><?php echo $order['razorpay_order_id']; ?></p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Payment ID</p>
                                <p class="text-[11px] font-mono text-gray-600 break-all"><?php echo $order['razorpay_payment_id']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
</body>
</html>
