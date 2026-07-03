<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Srishringarr</title>
    <?php include 'partials/head.php'; ?>
    <!-- Google Fonts for premium typography -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Work+Sans:wght@400;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <style>
        /* Dashboard Premium Theme Overrides */
        .dash-body {
            background: #0a0a0a !important;
            font-family: 'Work Sans', sans-serif !important;
        }
        .dash-main {
            background: #0a0a0a !important;
        }

        /* Card surfaces */
        .card-surface {
            background: #1a1a1a !important;
            border: 1px solid #2e2e2e !important;
            border-radius: 12px !important;
        }
        .card-surface:hover {
            border-color: #3a3a3a !important;
        }

        /* Glow hover effect */
        .glow-hover {
            transition: all 0.3s ease !important;
        }
        .glow-hover:hover {
            box-shadow: inset 0 0 20px rgba(244,125,49,0.05) !important;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #2e2e2e;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #444;
        }

        /* Input dark style */
        .input-dark {
            background: #0f0f0f !important;
            border: 1px solid #2e2e2e !important;
            color: #e5e2e1 !important;
            outline: none !important;
        }
        .input-dark:focus {
            border-color: #f47d31 !important;
            box-shadow: 0 0 0 2px rgba(244,125,49,0.1) !important;
        }

        /* Zebra table rows */
        .table-row-zebra:nth-child(even) {
            background: #151515;
        }

        /* Override vercel.css for dashboard-specific elements */
        .dash-main .bg-white,
        .dash-main .rounded-2xl {
            background-color: transparent !important;
            border: none !important;
        }

        /* Animate pulse for live indicator */
        @keyframes dash-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .dash-pulse {
            animation: dash-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Loading skeleton */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #1a1a1a 25%, #252525 50%, #1a1a1a 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }

        /* Fix overflow for the main content to work with existing sidebar */
        .dash-content-wrapper {
            overflow-y: auto !important;
            height: 100vh !important;
        }

        /* Material icons inline fix */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
</head>
<body class="dash-body">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar (unchanged) -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header - Premium -->
            <header class="h-16 bg-black border-b border-zinc-800 flex items-center justify-between px-6 z-10 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="open-sidebar" class="lg:hidden mr-2 text-zinc-400 hover:text-white">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <!-- Search Bar -->
                    <div class="relative hidden md:block">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-[18px]">search</span>
                        <input class="input-dark pl-10 pr-4 py-2 rounded-lg text-sm w-56 font-['Work_Sans']" placeholder="Search orders, SKUs..." type="text">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="refresh-stats" class="flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-2 rounded-lg transition-colors text-xs font-semibold border border-zinc-800">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        <span class="hidden xl:inline">Refresh Data</span>
                    </button>
                    <div class="flex items-center gap-1 text-zinc-500">
                        <button class="p-2 hover:bg-zinc-900 rounded-lg transition-colors relative" title="Notifications">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        </button>
                        <button class="p-2 hover:bg-zinc-900 rounded-lg transition-colors" title="Settings">
                            <span class="material-symbols-outlined text-[20px]">settings</span>
                        </button>
                        <button class="p-2 hover:bg-zinc-900 rounded-lg transition-colors" title="Messages">
                            <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3 pl-3 border-l border-zinc-800 cursor-pointer hover:bg-zinc-900 p-2 rounded-lg transition-colors">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-semibold text-white font-['Manrope']"><?php echo htmlspecialchars(ucfirst($_SESSION['admin_username'] ?? 'Admin')); ?></p>
                            <p class="text-[9px] font-bold text-orange-500 uppercase tracking-wider">SUPER ADMIN</p>
                        </div>
                        <?php $initials = strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 2)); ?>
                        <div class="w-9 h-9 rounded-full bg-orange-600 flex items-center justify-center text-white font-bold text-sm font-['Manrope']"><?php echo $initials; ?></div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="dash-main dash-content-wrapper flex-1 p-6">
                <!-- Dashboard Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 gap-4">
                    <div>
                        <h2 class="text-[26px] font-bold text-white font-['Manrope'] tracking-tight">Operational Dashboard</h2>
                        <p class="text-zinc-500 mt-1 text-sm font-['Work_Sans']">Real-time overview of revenue streams, inventory status, and pending actions.</p>
                    </div>
                    <!-- Date Range Picker -->
                    <div class="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-lg p-1">
                        <button class="px-4 py-2 rounded-md text-[10px] font-bold uppercase tracking-wider text-white bg-zinc-800">Today</button>
                        <button class="px-4 py-2 rounded-md text-[10px] font-bold uppercase tracking-wider text-zinc-500 hover:text-white hover:bg-zinc-800 transition-colors">7D</button>
                        <button class="px-4 py-2 rounded-md text-[10px] font-bold uppercase tracking-wider text-zinc-500 hover:text-white hover:bg-zinc-800 transition-colors">30D</button>
                        <div class="h-4 w-px bg-zinc-700 mx-1"></div>
                        <button class="flex items-center gap-2 px-3 py-2 rounded-md text-zinc-500 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            <span class="text-[11px] font-mono">Custom</span>
                        </button>
                    </div>
                </div>

                <!-- Metric Cards Row (5 Cols) -->
                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
                    <!-- Rental Revenue -->
                    <div class="card-surface glow-hover p-4 relative overflow-hidden" style="border-left: 2px solid #f47d31 !important;">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">checkroom</span> RENTAL REVENUE
                                </p>
                                <h3 id="metric-rental-revenue" class="text-[22px] font-bold text-white font-['Manrope'] tracking-tight mt-1">₹31,200</h3>
                            </div>
                            <div class="bg-green-500/10 text-green-500 px-1.5 py-0.5 rounded text-[10px] font-mono flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[12px]">trending_up</span>+8.2%
                            </div>
                        </div>
                        <div class="mt-2 text-[10px] text-zinc-500 flex justify-between items-center border-t border-zinc-800/50 pt-2">
                            <span>vs Last Period</span>
                            <span class="font-mono text-zinc-300">₹28,835</span>
                        </div>
                    </div>

                    <!-- Sales Revenue -->
                    <div class="card-surface glow-hover p-4 relative overflow-hidden" style="border-left: 2px solid #e9c349 !important;">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">shopping_bag</span> SALES REVENUE
                                </p>
                                <h3 id="metric-sales-revenue" class="text-[22px] font-bold text-white font-['Manrope'] tracking-tight mt-1">₹11,300</h3>
                            </div>
                            <div class="bg-green-500/10 text-green-500 px-1.5 py-0.5 rounded text-[10px] font-mono flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[12px]">trending_up</span>+4.5%
                            </div>
                        </div>
                        <div class="mt-2 text-[10px] text-zinc-500 flex justify-between items-center border-t border-zinc-800/50 pt-2">
                            <span>vs Last Period</span>
                            <span class="font-mono text-zinc-300">₹10,813</span>
                        </div>
                    </div>

                    <!-- Security Deposits -->
                    <div class="card-surface glow-hover p-4 relative overflow-hidden" style="border-left: 2px solid #3adfab !important;">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">lock</span> SEC. DEPOSITS HELD
                                </p>
                                <h3 class="text-[22px] font-bold text-white font-['Manrope'] tracking-tight mt-1">₹48,500</h3>
                            </div>
                            <div class="bg-zinc-800 p-1 rounded-md text-emerald-400 mt-1">
                                <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
                            </div>
                        </div>
                        <div class="mt-2 text-[10px] text-zinc-500 flex justify-between items-center border-t border-zinc-800/50 pt-2">
                            <span>Refunds Due (3D)</span>
                            <span class="font-mono text-amber-500">₹12,000</span>
                        </div>
                    </div>

                    <!-- Active Rentals -->
                    <div class="card-surface glow-hover p-4" style="border-left: 2px solid #ffb68f !important;">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">local_shipping</span> ACTIVE RENTALS
                                </p>
                                <h3 id="metric-active-rentals" class="text-[22px] font-bold text-white font-['Manrope'] tracking-tight mt-1">---</h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 border-t border-zinc-800/50 pt-2">
                            <div class="flex-1">
                                <div class="flex justify-between text-[10px] text-zinc-500 mb-1 font-mono">
                                    <span>Returns Pending</span>
                                    <span class="text-red-500 font-bold" id="metric-returns-pending">5</span>
                                </div>
                                <div class="h-1 w-full bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full" style="width: 20%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Health -->
                    <div class="card-surface glow-hover p-4 relative overflow-hidden" style="border-left: 2px solid #ef4444 !important;">
                        <div class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-red-500 dash-pulse"></div>
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">inventory</span> INVENTORY HEALTH
                                </p>
                                <h3 class="text-[22px] font-bold text-red-500 font-['Manrope'] tracking-tight mt-1">92%</h3>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-col gap-1 border-t border-zinc-800/50 pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-zinc-500">Out of Stock</span>
                                <span class="text-[10px] text-red-500 font-bold font-mono" id="metric-out-of-stock">---</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-zinc-500">Low Stock</span>
                                <span class="text-[10px] text-amber-500 font-bold font-mono" id="metric-low-stock">---</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Operational Section (2 Columns) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
                    <!-- Recent Bookings Table (Wide) -->
                    <div class="lg:col-span-8 card-surface flex flex-col" style="height: 400px;">
                        <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-zinc-900/50 rounded-t-xl flex-shrink-0">
                            <h3 class="text-base font-semibold text-white font-['Manrope'] flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-orange-500">receipt_long</span>
                                Recent Bookings (Action Required)
                            </h3>
                            <div class="flex gap-2 items-center">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-zinc-500 text-[14px]">filter_list</span>
                                    <select class="input-dark pl-7 pr-6 py-1 text-[11px] rounded-md appearance-none bg-zinc-800 border-zinc-700 text-zinc-300">
                                        <option>All Statuses</option>
                                        <option>Pending Pickup</option>
                                        <option>Overdue Return</option>
                                    </select>
                                </div>
                                <a class="text-xs text-orange-500 hover:underline font-semibold" href="index.php?controller=orders">View All</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead class="sticky top-0 bg-zinc-900/80 z-10">
                                    <tr class="border-b border-zinc-800">
                                        <th class="py-2 px-4 text-[10px] text-zinc-600 font-bold uppercase tracking-wider font-['Work_Sans']">BILL/CUST</th>
                                        <th class="py-2 px-4 text-[10px] text-zinc-600 font-bold uppercase tracking-wider font-['Work_Sans']">ITEM DETAILS</th>
                                        <th class="py-2 px-4 text-[10px] text-zinc-600 font-bold uppercase tracking-wider font-['Work_Sans']">RENTAL DATES</th>
                                        <th class="py-2 px-4 text-[10px] text-zinc-600 font-bold uppercase tracking-wider font-['Work_Sans'] text-right">AMOUNT / SEC DEP</th>
                                        <th class="py-2 px-4 text-[10px] text-zinc-600 font-bold uppercase tracking-wider font-['Work_Sans'] text-right">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-bookings-body" class="text-sm">
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-600 text-xs">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-[24px] text-zinc-700">hourglass_empty</span>
                                            Loading bookings...
                                        </div>
                                    </td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Value & Availability -->
                    <div class="lg:col-span-4 card-surface p-4 flex flex-col" style="height: 400px;">
                        <div class="flex justify-between items-center mb-4 flex-shrink-0">
                            <h3 class="text-base font-semibold text-white font-['Manrope'] flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-yellow-500">inventory_2</span>
                                Stock Value & Availability
                            </h3>
                        </div>
                        <!-- Summary Card -->
                        <div class="mb-4 bg-zinc-800/80 p-3 rounded-lg flex justify-between items-center border border-zinc-700 flex-shrink-0">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">ESTIMATED STOCK VALUE</p>
                                <p class="text-[20px] font-bold text-white font-['Manrope'] mt-1">₹1.42 Cr</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">TOTAL UNITS</p>
                                <p class="font-mono text-base text-zinc-200 mt-1" id="metric-total-units">---</p>
                            </div>
                        </div>
                        <!-- Category Breakdowns -->
                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3">
                            <!-- Bridal Lehengas -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm text-white font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-orange-500">checkroom</span> Bridal Lehengas
                                    </span>
                                    <span class="font-mono text-xs text-zinc-400">85 / 120 Avail</span>
                                </div>
                                <div class="w-full bg-zinc-800 rounded-full h-1.5 mb-1 overflow-hidden flex">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 70%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 20%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 10%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider text-zinc-500">
                                    <span>VALUE: ₹45.2L</span>
                                    <span class="flex gap-2">
                                        <span class="text-green-500">■ IN</span>
                                        <span class="text-amber-500">■ OUT</span>
                                        <span class="text-red-500">■ MAINT</span>
                                    </span>
                                </div>
                            </div>
                            <!-- Heavy Kundan Sets -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm text-white font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-yellow-500">diamond</span> Heavy Kundan Sets
                                    </span>
                                    <span class="font-mono text-xs text-zinc-400">42 / 60 Avail</span>
                                </div>
                                <div class="w-full bg-zinc-800 rounded-full h-1.5 mb-1 overflow-hidden flex">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 70%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 25%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 5%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider text-zinc-500">
                                    <span>VALUE: ₹32.8L</span>
                                </div>
                            </div>
                            <!-- AD/CZ Jewellery -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm text-white font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-emerald-400">diamond</span> AD/CZ Jewellery
                                    </span>
                                    <span class="font-mono text-xs text-zinc-400">312 / 450 Avail</span>
                                </div>
                                <div class="w-full bg-zinc-800 rounded-full h-1.5 mb-1 overflow-hidden flex">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 69%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 30%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 1%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider text-zinc-500">
                                    <span>VALUE: ₹21.5L</span>
                                </div>
                            </div>
                            <!-- Indo-Western Gowns -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm text-white font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-zinc-400">checkroom</span> Indo-Western Gowns
                                    </span>
                                    <span class="font-mono text-xs text-zinc-400">18 / 45 Avail</span>
                                </div>
                                <div class="w-full bg-zinc-800 rounded-full h-1.5 mb-1 overflow-hidden flex">
                                    <div class="bg-amber-500 h-1.5 rounded-l-full" style="width: 40%"></div>
                                    <div class="bg-amber-500/60 h-1.5" style="width: 50%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 10%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider text-zinc-500">
                                    <span>VALUE: ₹18.0L <span class="text-amber-500 lowercase ml-1">(high demand)</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section (Charts & Widgets) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
                    <!-- Revenue Performance Chart -->
                    <div class="lg:col-span-8 card-surface p-4 flex flex-col relative overflow-hidden" style="height: 320px;">
                        <div class="flex justify-between items-center z-10 relative mb-4 flex-shrink-0">
                            <h3 class="text-base font-semibold text-white font-['Manrope'] flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-orange-500">bar_chart</span>
                                Revenue Performance (30 Days)
                            </h3>
                            <div class="flex bg-zinc-800 rounded-lg p-0.5 border border-zinc-700">
                                <button class="px-3 py-1 text-xs font-semibold rounded-md bg-zinc-900 text-white shadow-sm border border-zinc-700">Rental Trends</button>
                                <button class="px-3 py-1 text-xs font-semibold rounded-md text-zinc-500 hover:text-white transition-colors">Sales Performance</button>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="flex gap-4 z-10 relative text-xs mb-2 pl-6 flex-shrink-0">
                            <span class="flex items-center gap-1 text-zinc-500"><span class="inline-block w-2 h-2 rounded-full bg-orange-500"></span> Bookings (Qty)</span>
                            <span class="flex items-center gap-1 text-zinc-500"><span class="inline-block w-2 h-2 rounded-full bg-zinc-600"></span> Returns (Qty)</span>
                            <span class="flex items-center gap-1 text-zinc-500 ml-4"><span class="inline-block w-4 h-[2px] bg-yellow-500"></span> Revenue Trend (₹)</span>
                        </div>
                        <!-- Bar Chart -->
                        <div class="flex-1 relative flex items-end justify-between px-2 pb-6 border-l border-b border-zinc-800/50 mt-2 ml-6">
                            <!-- Y Axis Labels -->
                            <div class="absolute -left-8 top-0 bottom-6 flex flex-col justify-between text-[9px] font-mono text-zinc-600 py-0">
                                <span>50k</span>
                                <span>25k</span>
                                <span>0</span>
                            </div>
                            <!-- Bars -->
                            <div class="w-[8%] h-[30%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[60%] bg-orange-500/80 rounded-t-sm"></div>
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-black p-1 rounded border border-zinc-700 text-[9px] font-mono text-white whitespace-nowrap z-20">B: 12 | R: 8 | ₹15k</div>
                            </div>
                            <div class="w-[8%] h-[45%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[80%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <div class="w-[8%] h-[20%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[40%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <div class="w-[8%] h-[60%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[90%] bg-orange-500 rounded-t-sm" style="box-shadow: 0 0 10px rgba(244,125,49,0.5)"></div>
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-black p-1 rounded border border-zinc-700 text-[9px] font-mono text-white whitespace-nowrap z-20">B: 24 | R: 18 | ₹32k</div>
                            </div>
                            <div class="w-[8%] h-[75%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[70%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <div class="w-[8%] h-[50%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[50%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <div class="w-[8%] h-[85%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[60%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <div class="w-[8%] h-[40%] bg-zinc-800/30 rounded-t-sm relative group cursor-pointer">
                                <div class="absolute bottom-0 w-full h-[85%] bg-orange-500/80 rounded-t-sm"></div>
                            </div>
                            <!-- SVG Trend Line -->
                            <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none">
                                <path d="M 10 70 Q 50 60, 100 80 T 200 40 T 300 30 T 400 50 T 500 20 T 600 35 T 700 15 T 780 40" fill="none" stroke="#e9c349" stroke-width="2"></path>
                                <circle cx="10" cy="70" fill="#e9c349" r="3"></circle>
                                <circle cx="200" cy="40" fill="#e9c349" r="3"></circle>
                                <circle cx="400" cy="50" fill="#e9c349" r="3"></circle>
                                <circle cx="500" cy="20" fill="#e9c349" r="3"></circle>
                                <circle cx="700" cy="15" fill="#e9c349" r="3"></circle>
                            </svg>
                            <!-- X Axis Labels -->
                            <div class="absolute bottom-0 left-0 right-0 flex justify-between text-[9px] font-mono text-zinc-600 px-2 translate-y-full pt-1">
                                <span>Jun 1</span>
                                <span>Jun 8</span>
                                <span>Jun 15</span>
                                <span>Jun 22</span>
                                <span>Jun 30</span>
                            </div>
                        </div>
                    </div>

                    <!-- Store Activity & Quick Actions -->
                    <div class="lg:col-span-4 flex flex-col gap-4" style="height: 320px;">
                        <!-- Staff Activity Widget -->
                        <div class="card-surface p-4 flex-1 flex flex-col">
                            <h3 class="text-base font-semibold text-white font-['Manrope'] mb-3 flex items-center gap-2 flex-shrink-0">
                                <span class="material-symbols-outlined text-[18px] text-zinc-400">group</span>
                                Today's Store Activity
                            </h3>
                            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-1">
                                <div class="flex items-center justify-between border-b border-zinc-800/50 pb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-300">RK</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold text-white">Rahul K.</span>
                                            <span class="text-[9px] text-zinc-500">Processed 4 Bookings</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs text-green-500">+₹12.5k</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-zinc-800/50 pb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-300">SM</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold text-white">Sneha M.</span>
                                            <span class="text-[9px] text-zinc-500">Handled 2 Returns</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs text-zinc-600">--</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-orange-600 flex items-center justify-center text-[10px] font-bold text-white">AD</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold text-white">Admin</span>
                                            <span class="text-[9px] text-zinc-500">Added 15 New SKUs</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs text-zinc-600">--</span>
                                </div>
                            </div>
                        </div>
                        <!-- Quick Actions -->
                        <div class="flex gap-2 flex-shrink-0">
                            <button onclick="window.location.href='index.php?controller=orders'" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 rounded-lg flex items-center justify-center gap-1 transition-colors text-sm">
                                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                New Bill
                            </button>
                            <button class="flex-1 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-semibold py-2.5 rounded-lg flex items-center justify-center gap-1 transition-colors text-sm">
                                <span class="material-symbols-outlined text-[16px]">keyboard</span>
                                POS
                            </button>
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

            // Status helper
            const getStatusBadge = (status) => {
                const s = (status || '').toLowerCase();
                if (s === 'returned' || s === 'completed') {
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-green-500/10 text-green-500 border border-green-500/20">COMPLETED</span>`;
                } else if (s === 'picked' || s === 'picked up') {
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-500 border border-amber-500/20">PICKED UP</span>`;
                } else if (s === 'booked' || s === 'confirmed') {
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20">BOOKED</span>`;
                } else if (s === 'overdue') {
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-red-500/10 text-red-500 border border-red-500/20">OVERDUE</span>`;
                } else if (s === 'cancelled') {
                    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-zinc-700 text-zinc-400 border border-zinc-600">CANCELLED</span>`;
                }
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-zinc-800 text-zinc-300">${status || 'N/A'}</span>`;
            };

            // Item icon based on type
            const getItemIcon = (items) => {
                const str = (items || '').toLowerCase();
                if (str.includes('lehenga') || str.includes('gown') || str.includes('saree') || str.includes('suit')) {
                    return 'checkroom';
                } else if (str.includes('necklace') || str.includes('kundan') || str.includes('earring') || str.includes('jewel') || str.includes('set')) {
                    return 'diamond';
                }
                return 'shopping_bag';
            };

            // Format date nicely
            const formatDate = (dateStr) => {
                if (!dateStr) return '--';
                try {
                    const d = new Date(dateStr);
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    return `${String(d.getDate()).padStart(2, '0')} ${months[d.getMonth()]} '${String(d.getFullYear()).slice(2)}`;
                } catch(e) {
                    return dateStr;
                }
            };

            // Check if date is today
            const isToday = (dateStr) => {
                if (!dateStr) return false;
                const today = new Date();
                const d = new Date(dateStr);
                return d.toDateString() === today.toDateString();
            };

            const fetchStats = async () => {
                try {
                    if (refreshBtn) {
                        const icon = refreshBtn.querySelector('.material-symbols-outlined');
                        if (icon) icon.style.animation = 'spin 1s linear infinite';
                    }

                    const response = await fetch('index.php?controller=api&action=stats');
                    const data = await response.json();

                    // Update metric cards
                    const rev = document.getElementById('metric-rental-revenue');
                    if (rev) rev.textContent = '₹' + new Intl.NumberFormat('en-IN').format(data.monthly_revenue || 0);

                    const rentals = document.getElementById('metric-active-rentals');
                    if (rentals) rentals.textContent = new Intl.NumberFormat().format(data.active_rentals || 0);

                    const oos = document.getElementById('metric-out-of-stock');
                    if (oos) oos.textContent = new Intl.NumberFormat().format(data.out_of_stock || 0);

                    const ls = document.getElementById('metric-low-stock');
                    if (ls) ls.textContent = new Intl.NumberFormat().format(data.low_stock || 0);

                    const tu = document.getElementById('metric-total-units');
                    if (tu) tu.textContent = new Intl.NumberFormat().format(data.active_products || 0);

                    // Render Recent Bookings Table (Premium Layout)
                    const bookingsTbody = document.getElementById('recent-bookings-body');
                    if (bookingsTbody) {
                        bookingsTbody.innerHTML = '';
                        if (data.recent_bookings && data.recent_bookings.length > 0) {
                            data.recent_bookings.forEach((b, idx) => {
                                const icon = getItemIcon(b.items);
                                const pickDate = formatDate(b.pick_date);
                                const returnDate = formatDate(b.delivery_date);
                                const pickIsToday = isToday(b.pick_date);
                                const returnIsToday = isToday(b.delivery_date);
                                const amount = parseFloat(b.rent_amount || 0).toLocaleString('en-IN');

                                // Determine row border accent for special statuses
                                const status = (b.booking_status || '').toLowerCase();
                                let rowBorder = '';
                                if (status === 'overdue') rowBorder = 'border-l-2 border-l-red-500';
                                else if (status === 'picked' || status === 'picked up') rowBorder = 'border-l-2 border-l-amber-500';

                                bookingsTbody.innerHTML += `
                                    <tr class="table-row-zebra border-b border-zinc-800/30 hover:bg-zinc-800/50 transition-colors ${rowBorder}" style="cursor:pointer;">
                                        <td class="py-3 px-4">
                                            <div class="flex flex-col">
                                                <span class="font-mono text-white font-bold text-sm">#${b.bill_id}</span>
                                                <span class="text-[11px] text-zinc-500 truncate w-24">${b.customer_name || ''}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 bg-zinc-800 rounded flex items-center justify-center border border-zinc-700">
                                                    <span class="material-symbols-outlined text-[16px] text-zinc-400">${icon}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-mono text-zinc-200 text-xs">${(b.items || 'N/A').substring(0, 18)}</span>
                                                    <span class="text-[11px] text-zinc-500">${b.product_type || ''}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex flex-col text-[11px] font-mono text-zinc-400">
                                                <span class="flex items-center gap-1 ${pickIsToday ? 'text-blue-400 font-bold' : ''}">
                                                    <span class="material-symbols-outlined text-[12px] ${pickIsToday ? 'text-blue-400' : 'text-blue-500'}">flight_takeoff</span> ${pickDate}${pickIsToday ? ' (Today)' : ''}
                                                </span>
                                                <span class="flex items-center gap-1 ${returnIsToday ? 'text-amber-500 font-bold' : ''}">
                                                    <span class="material-symbols-outlined text-[12px] ${returnIsToday ? 'text-amber-500' : 'text-orange-500'}">flight_land</span> ${returnDate}${returnIsToday ? ' (Today)' : ''}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex flex-col">
                                                <span class="font-mono text-zinc-200 text-xs">₹${amount}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex flex-col items-end gap-1">
                                                ${getStatusBadge(b.booking_status)}
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            bookingsTbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-zinc-600 text-xs">No recent bookings found.</td></tr>';
                        }
                    }

                } catch (error) {
                    console.error('Error fetching stats:', error);
                } finally {
                    if (refreshBtn) {
                        const icon = refreshBtn.querySelector('.material-symbols-outlined');
                        if (icon) icon.style.animation = '';
                    }
                }
            };

            // Add spin keyframes dynamically
            const styleSheet = document.createElement('style');
            styleSheet.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
            document.head.appendChild(styleSheet);

            fetchStats();
            if (refreshBtn) refreshBtn.addEventListener('click', fetchStats);
        });
    </script>
</body>
</html>
