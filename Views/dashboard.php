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
                <!-- Welcome Banner -->
                <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Welcome Back, Admin! 👋</h2>
                        <p class="text-gray-500 mt-1">Here's what's happening with your store today.</p>
                    </div>
                    <div class="hidden md:block">
                        <button id="refresh-stats" class="px-4 py-2 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-all font-medium">
                            <i class="fas fa-sync-alt mr-2"></i> Refresh Data
                        </button>
                    </div>
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

                <!-- Placeholder for Charts or Recent Orders -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">Recent Activities</h3>
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-plus text-gray-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">New product added</p>
                                    <p class="text-xs text-gray-500">2 hours ago</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-gray-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Order #1234 completed</p>
                                    <p class="text-xs text-gray-500">5 hours ago</p>
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
