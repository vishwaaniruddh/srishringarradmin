<!DOCTYPE html>
<html lang="en">
<head>
    <title>Orders Management - Srishringarr</title>
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
            $pageTitle = 'Orders Management';
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Order Management</h2>
                        <p class="text-gray-500 mt-1">Manage and track all heritage rentals and sales.</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium flex items-center gap-2">
                            <i class="fas fa-download"></i> Export Orders
                        </button>
                    </div>
                </div>

                <!-- Orders Table Container -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Search by Order ID or Customer..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all rounded-xl text-sm outline-none">
                        </div>
                        <div class="flex items-center gap-3">
                            <select class="bg-gray-50 border-transparent rounded-xl px-4 py-2.5 text-sm focus:ring-primary/20 outline-none">
                                <option>All Status</option>
                                <option>Pending</option>
                                <option>Active</option>
                                <option>Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Order ID</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Customer</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Bill Date</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Items</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Total Amount</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Status</th>
                                    <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-gray-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-700">#<?php echo $order['id'] + 5000; ?></span>
                                        <span class="ml-2 px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase">Web</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs mr-3">
                                                <?php echo strtoupper(substr($order['cust_name'] ?: 'C', 0, 1)); ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-800 truncate"><?php echo $order['cust_name'] ?: 'Guest Customer'; ?></p>
                                                <p class="text-[11px] text-gray-500 truncate"><?php echo $order['phone']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg">
                                            <?php echo $order['item_count']; ?> Items
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-800">₹<?php echo number_format($order['total_amount']); ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $status = strtolower($order['status'] ?: 'pending');
                                        $statusClass = 'bg-gray-100 text-gray-600';
                                        if ($status == 'paid' || $status == 'active') $statusClass = 'bg-green-50 text-green-600';
                                        if ($status == 'completed') $statusClass = 'bg-blue-50 text-blue-600';
                                        if ($status == 'cancelled') $statusClass = 'bg-red-50 text-red-600';
                                        ?>
                                        <span class="px-3 py-1 <?php echo $statusClass; ?> text-[10px] font-bold uppercase tracking-widest rounded-full">
                                            <?php echo $order['status'] ?: 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="index.php?controller=orders&action=view&id=<?php echo $order['id']; ?>" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between">
                        <p class="text-sm text-gray-500 font-medium">Showing <?php echo count($orders); ?> orders</p>
                        <div class="flex gap-2">
                            <button disabled class="p-2 border border-gray-200 rounded-lg text-gray-300 cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i></button>
                            <button class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all"><i class="fas fa-chevron-right text-xs"></i></button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
</body>
</html>
