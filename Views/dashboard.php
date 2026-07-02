<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Srishringarr</title>
    <?php include 'partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Dashboard';
            include 'partials/topbar.php'; 
            ?>

            <!-- Dashboard Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <!-- Dashboard Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white tracking-tight">Overview</h2>
                    <button id="refresh-stats" class="px-4 py-2 bg-zinc-900 border border-zinc-800 text-white rounded-lg hover:bg-zinc-800 transition-all font-medium text-xs cursor-pointer">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh Data
                    </button>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <!-- Total Orders -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-shopping-bag text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">+12%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Total Orders</h3>
                        <p id="total-orders" class="text-2xl font-bold text-gray-800 mt-1">---</p>
                    </div>

                    <!-- Monthly Revenue -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                                <i class="fas fa-indian-rupee-sign text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">+8.4%</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Monthly Revenue</h3>
                        <p id="monthly-revenue" class="text-2xl font-bold text-gray-800 mt-1">---</p>
                    </div>

                    <!-- Active Products -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                                <i class="fas fa-box text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Active Products</h3>
                        <p id="active-products" class="text-2xl font-bold text-gray-800 mt-1">---</p>
                    </div>

                    <!-- Active Rentals -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                                <i class="fas fa-calendar-check text-xl"></i>
                            </div>
                            <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Live</span>
                        </div>
                        <h3 class="text-gray-500 text-sm font-medium">Active Rentals</h3>
                        <p id="active-rentals" class="text-2xl font-bold text-gray-800 mt-1">---</p>
                    </div>
                </div>

                <!-- Secondary Metrics / Stock & Categories Breakdown -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                    <!-- Jewellery -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <h3 class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Jewellery Items</h3>
                        <p id="jewellery-count" class="text-2xl font-bold text-white mt-2">---</p>
                    </div>

                    <!-- Garments -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <h3 class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Garments Items</h3>
                        <p id="garments-count" class="text-2xl font-bold text-white mt-2">---</p>
                    </div>

                    <!-- Out of Stock -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Out of Stock</h3>
                            <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                        </div>
                        <p id="out-of-stock" class="text-2xl font-bold text-red-500 mt-2">---</p>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Low Stock Alerts</h3>
                            <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                        </div>
                        <p id="low-stock" class="text-2xl font-bold text-orange-500 mt-2">---</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Recent Bookings Table -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400">Recent Bookings (POS)</h3>
                            <a href="index.php?controller=orders" class="text-xs text-white hover:underline">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-zinc-800">
                                        <th class="px-4 py-2 text-zinc-500 font-semibold uppercase tracking-wider">Bill ID</th>
                                        <th class="px-4 py-2 text-zinc-500 font-semibold uppercase tracking-wider">SKUs</th>
                                        <th class="px-4 py-2 text-zinc-500 font-semibold uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-2 text-zinc-500 font-semibold uppercase tracking-wider">Rent</th>
                                        <th class="px-4 py-2 text-zinc-500 font-semibold uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-bookings-body" class="divide-y divide-zinc-900">
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-zinc-500">Loading bookings...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="bg-black p-6 rounded-xl border border-zinc-800">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-6">Recent Activities</h3>
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-zinc-950 border border-zinc-800 flex items-center justify-center mr-4">
                                    <i class="fas fa-plus text-zinc-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-white">New product added</p>
                                    <p class="text-[10px] text-zinc-500">2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-zinc-950 border border-zinc-800 flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-zinc-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-white">Order #1234 completed</p>
                                    <p class="text-[10px] text-zinc-500">5 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'partials/scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const refreshBtn = document.getElementById('refresh-stats');

            const fetchStats = async () => {
                try {
                    if (refreshBtn) refreshBtn.classList.add('animate-spin');
                    
                    const response = await fetch('index.php?controller=api&action=stats');
                    const data = await response.json();

                    document.getElementById('total-orders').textContent = new Intl.NumberFormat().format(data.total_orders);
                    document.getElementById('monthly-revenue').textContent = '₹' + new Intl.NumberFormat().format(data.monthly_revenue);
                    document.getElementById('active-products').textContent = new Intl.NumberFormat().format(data.active_products);
                    document.getElementById('active-rentals').textContent = new Intl.NumberFormat().format(data.active_rentals);
                    
                    document.getElementById('jewellery-count').textContent = new Intl.NumberFormat().format(data.jewellery_count);
                    document.getElementById('garments-count').textContent = new Intl.NumberFormat().format(data.garments_count);
                    document.getElementById('out-of-stock').textContent = new Intl.NumberFormat().format(data.out_of_stock);
                    document.getElementById('low-stock').textContent = new Intl.NumberFormat().format(data.low_stock);

                    // Render Recent Bookings Table
                    const bookingsTbody = document.getElementById('recent-bookings-body');
                    if (bookingsTbody) {
                        bookingsTbody.innerHTML = '';
                        if (data.recent_bookings && data.recent_bookings.length > 0) {
                            data.recent_bookings.forEach(b => {
                                let statusClass = 'bg-zinc-800 text-white';
                                if (b.booking_status === 'Returned') statusClass = 'bg-green-100/10 text-green-500 border border-green-500/20';
                                else if (b.booking_status === 'Picked') statusClass = 'bg-blue-100/10 text-blue-500 border border-blue-500/20';
                                else if (b.booking_status === 'Booked' || b.booking_status === 'Confirmed') statusClass = 'bg-orange-100/10 text-orange-500 border border-orange-500/20';

                                bookingsTbody.innerHTML += `
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-white">#${b.bill_id}</td>
                                        <td class="px-4 py-3 text-xs text-zinc-400 max-w-[150px] truncate" title="${b.items || ''}">${b.items || 'N/A'}</td>
                                        <td class="px-4 py-3 text-xs">
                                            <div class="text-zinc-300">Pick: ${b.pick_date}</div>
                                            <div class="text-zinc-500">Return: ${b.delivery_date}</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs font-semibold text-white">
                                            ₹${parseFloat(b.rent_amount).toLocaleString('en-IN')}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold ${statusClass}">${b.booking_status}</span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            bookingsTbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-zinc-500 text-xs">No recent bookings found.</td></tr>';
                        }
                    }
                    
                } catch (error) {
                    console.error('Error fetching stats:', error);
                } finally {
                    if (refreshBtn) refreshBtn.classList.remove('animate-spin');
                }
            };

            fetchStats();
            if (refreshBtn) refreshBtn.addEventListener('click', fetchStats);
        });
    </script>
</body>
</html>
