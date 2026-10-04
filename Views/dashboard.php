<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Srishringarr</title>
    <?php include 'partials/head.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <style>
        /* Dashboard ShadCN Light Theme Overrides */
        .dash-body {
            background: #fafafa !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .dash-main {
            background: #fafafa !important;
        }

        /* Card surfaces — white with subtle border */
        .card-surface {
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }
        .card-surface:hover {
            border-color: #d4d4d8 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Glow hover effect — light version */
        .glow-hover {
            transition: all 0.15s ease !important;
        }
        .glow-hover:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Custom scrollbar — light */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d4d4d8;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1aa;
        }

        /* Input light style */
        .input-dark {
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            color: #09090b !important;
            outline: none !important;
        }
        .input-dark:focus {
            border-color: #09090b !important;
            box-shadow: 0 0 0 1px #09090b !important;
        }
        .input-dark::placeholder {
            color: #a1a1aa !important;
        }

        /* Zebra table rows — light */
        .table-row-zebra:nth-child(even) {
            background: #fafafa;
        }

        /* Override for dashboard-specific elements */
        .dash-main .bg-white,
        .dash-main .rounded-2xl {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Animate pulse for live indicator */
        @keyframes dash-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .dash-pulse {
            animation: dash-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Loading skeleton — light */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f4f4f5 25%, #e4e4e7 50%, #f4f4f5 75%);
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
            <!-- Top Header (Shared Component) -->
            <?php 
            $pageTitle = 'Dashboard';
            include __DIR__ . '/partials/topbar.php'; 
            ?>

            <!-- Dashboard Content -->
            <main class="dash-main dash-content-wrapper flex-1 p-6">
                <!-- Dashboard Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 gap-4">
                    <div>
                        <h2 style="font-size: 20px; font-weight: 600; color: #09090b; letter-spacing: -0.02em;">Operational Dashboard</h2>
                        <p style="color: #71717a; margin-top: 4px; font-size: 13px;">Real-time overview of revenue streams, inventory status, and pending actions.</p>
                    </div>
                    <!-- Right Actions: Refresh Data & Date Range Picker -->
                    <div class="flex items-center gap-2">
                        <button id="refresh-stats" type="button" class="shadcn-btn" style="height: 32px; font-size: 12px; padding: 0 10px; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 6px; cursor: pointer; color: #09090b; font-weight: 500; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                            <i class="fa-solid fa-arrows-rotate" style="font-size: 11px;"></i>
                            <span>Refresh Data</span>
                        </button>
                        <div class="flex items-center gap-1 rounded-md p-1" style="background: #ffffff; border: 1px solid #e4e4e7;">
                            <button class="px-3 py-1 rounded-md text-[11px] font-semibold tracking-tight shadow-sm" style="background: #09090b !important; color: #ffffff !important;">Today</button>
                            <button class="px-3 py-1 rounded-md text-[11px] font-medium tracking-tight transition-colors" style="color: #71717a;" onmouseover="this.style.background='#f4f4f5';this.style.color='#09090b'" onmouseout="this.style.background='transparent';this.style.color='#71717a'">7D</button>
                            <button class="px-3 py-1 rounded-md text-[11px] font-medium tracking-tight transition-colors" style="color: #71717a;" onmouseover="this.style.background='#f4f4f5';this.style.color='#09090b'" onmouseout="this.style.background='transparent';this.style.color='#71717a'">30D</button>
                            <div class="h-4 w-px mx-1" style="background: #e4e4e7;"></div>
                            <button class="flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium transition-colors" style="color: #71717a;" onmouseover="this.style.color='#09090b'" onmouseout="this.style.color='#71717a'">
                                <i class="fa-regular fa-calendar" style="font-size: 12px;"></i>
                                <span>Custom</span>
                            </button>
                        </div>
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
                    <div class="lg:col-span-8 card-surface flex flex-col" style="height: 400px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px;">
                        <div class="p-4 flex justify-between items-center rounded-t-lg flex-shrink-0" style="border-bottom: 1px solid #e4e4e7; background: #ffffff;">
                            <h3 class="text-sm font-semibold flex items-center gap-2" style="color: #09090b;">
                                <span class="material-symbols-outlined text-[18px]" style="color: #09090b;">receipt_long</span>
                                Recent Bookings (Action Required)
                            </h3>
                            <div class="flex gap-2 items-center">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[14px]" style="color: #71717a;">filter_list</span>
                                    <select class="pl-7 pr-6 py-1 text-[11px] rounded-md appearance-none" style="background: #ffffff; border: 1px solid #e4e4e7; color: #09090b;">
                                        <option>All Statuses</option>
                                        <option>Pending Pickup</option>
                                        <option>Overdue Return</option>
                                    </select>
                                </div>
                                <a class="text-xs hover:underline font-semibold" style="color: #2563eb;" href="index.php?controller=orders">View All</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead class="sticky top-0 z-10" style="background: #f9fafb; border-bottom: 1px solid #e4e4e7;">
                                    <tr>
                                        <th class="py-2.5 px-4 text-[10px] font-semibold uppercase tracking-wider" style="color: #71717a; border-bottom: 1px solid #e4e4e7; background: #f9fafb;">BILL/CUST</th>
                                        <th class="py-2.5 px-4 text-[10px] font-semibold uppercase tracking-wider" style="color: #71717a; border-bottom: 1px solid #e4e4e7; background: #f9fafb;">ITEM DETAILS</th>
                                        <th class="py-2.5 px-4 text-[10px] font-semibold uppercase tracking-wider" style="color: #71717a; border-bottom: 1px solid #e4e4e7; background: #f9fafb;">RENTAL DATES</th>
                                        <th class="py-2.5 px-4 text-[10px] font-semibold uppercase tracking-wider text-right" style="color: #71717a; border-bottom: 1px solid #e4e4e7; background: #f9fafb;">AMOUNT / SEC DEP</th>
                                        <th class="py-2.5 px-4 text-[10px] font-semibold uppercase tracking-wider text-right" style="color: #71717a; border-bottom: 1px solid #e4e4e7; background: #f9fafb;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-bookings-body" class="text-sm">
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-xs" style="color: #71717a;">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-[24px]" style="color: #a1a1aa;">hourglass_empty</span>
                                            Loading bookings...
                                        </div>
                                    </td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Value & Availability -->
                    <div class="lg:col-span-4 card-surface p-4 flex flex-col" style="height: 400px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px;">
                        <div class="flex justify-between items-center mb-4 flex-shrink-0">
                            <h3 class="text-sm font-semibold flex items-center gap-2" style="color: #09090b;">
                                <span class="material-symbols-outlined text-[18px]" style="color: #d97706;">inventory_2</span>
                                Stock Value & Availability
                            </h3>
                        </div>
                        <!-- Summary Card -->
                        <div class="mb-4 p-3.5 rounded-lg flex justify-between items-center flex-shrink-0" style="background: #f4f4f5; border: 1px solid #e4e4e7;">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a;">ESTIMATED STOCK VALUE</p>
                                <p class="text-[20px] font-bold mt-1" style="color: #09090b; letter-spacing: -0.02em;">₹1.42 Cr</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: #71717a;">TOTAL UNITS</p>
                                <p class="font-mono text-base font-semibold mt-1" id="metric-total-units" style="color: #09090b;">---</p>
                            </div>
                        </div>
                        <!-- Category Breakdowns -->
                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3.5">
                            <!-- Bridal Lehengas -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-xs font-semibold flex items-center gap-1.5" style="color: #09090b;">
                                        <span class="material-symbols-outlined text-[14px]" style="color: #d97706;">checkroom</span> Bridal Lehengas
                                    </span>
                                    <span class="font-mono text-[11px]" style="color: #71717a;">85 / 120 Avail</span>
                                </div>
                                <div class="w-full rounded-full h-1.5 mb-1 overflow-hidden flex" style="background: #e4e4e7;">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 70%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 20%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 10%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider" style="color: #71717a;">
                                    <span>VALUE: ₹45.2L</span>
                                    <span class="flex gap-2">
                                        <span class="text-green-600">■ IN</span>
                                        <span class="text-amber-600">■ OUT</span>
                                        <span class="text-red-600">■ MAINT</span>
                                    </span>
                                </div>
                            </div>
                            <!-- Heavy Kundan Sets -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-xs font-semibold flex items-center gap-1.5" style="color: #09090b;">
                                        <span class="material-symbols-outlined text-[14px]" style="color: #d97706;">diamond</span> Heavy Kundan Sets
                                    </span>
                                    <span class="font-mono text-[11px]" style="color: #71717a;">42 / 60 Avail</span>
                                </div>
                                <div class="w-full rounded-full h-1.5 mb-1 overflow-hidden flex" style="background: #e4e4e7;">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 70%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 25%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 5%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider" style="color: #71717a;">
                                    <span>VALUE: ₹32.8L</span>
                                </div>
                            </div>
                            <!-- AD/CZ Jewellery -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-xs font-semibold flex items-center gap-1.5" style="color: #09090b;">
                                        <span class="material-symbols-outlined text-[14px]" style="color: #059669;">diamond</span> AD/CZ Jewellery
                                    </span>
                                    <span class="font-mono text-[11px]" style="color: #71717a;">312 / 450 Avail</span>
                                </div>
                                <div class="w-full rounded-full h-1.5 mb-1 overflow-hidden flex" style="background: #e4e4e7;">
                                    <div class="bg-green-500 h-1.5 rounded-l-full" style="width: 69%"></div>
                                    <div class="bg-amber-500 h-1.5" style="width: 30%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 1%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider" style="color: #71717a;">
                                    <span>VALUE: ₹21.5L</span>
                                </div>
                            </div>
                            <!-- Indo-Western Gowns -->
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-xs font-semibold flex items-center gap-1.5" style="color: #09090b;">
                                        <span class="material-symbols-outlined text-[14px]" style="color: #71717a;">checkroom</span> Indo-Western Gowns
                                    </span>
                                    <span class="font-mono text-[11px]" style="color: #71717a;">18 / 45 Avail</span>
                                </div>
                                <div class="w-full rounded-full h-1.5 mb-1 overflow-hidden flex" style="background: #e4e4e7;">
                                    <div class="bg-amber-500 h-1.5 rounded-l-full" style="width: 40%"></div>
                                    <div class="bg-amber-500/60 h-1.5" style="width: 50%"></div>
                                    <div class="bg-red-500 h-1.5 rounded-r-full" style="width: 10%"></div>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold uppercase tracking-wider" style="color: #71717a;">
                                    <span>VALUE: ₹18.0L <span class="text-amber-600 lowercase ml-1">(high demand)</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section (Charts & Widgets) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
                    <!-- Revenue Performance Chart -->
                    <div class="lg:col-span-8 card-surface p-4 flex flex-col relative overflow-hidden" style="height: 320px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px;">
                        <div class="flex justify-between items-center z-10 relative mb-4 flex-shrink-0">
                            <h3 class="text-sm font-semibold flex items-center gap-2" style="color: #09090b;">
                                <span class="material-symbols-outlined text-[18px]" style="color: #d97706;">bar_chart</span>
                                Revenue Performance (30 Days)
                            </h3>
                            <div class="flex rounded-md p-0.5" style="background: #f4f4f5; border: 1px solid #e4e4e7;">
                                <button class="px-2.5 py-1 text-xs font-semibold rounded shadow-sm" style="background: #ffffff; color: #09090b; border: 1px solid #e4e4e7;">Rental Trends</button>
                                <button class="px-2.5 py-1 text-xs font-medium rounded transition-colors" style="color: #71717a;" onmouseover="this.style.color='#09090b'" onmouseout="this.style.color='#71717a'">Sales Performance</button>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="flex gap-4 z-10 relative text-xs mb-2 pl-6 flex-shrink-0">
                            <span class="flex items-center gap-1.5" style="color: #71717a;"><span class="inline-block w-2 h-2 rounded-full" style="background: #f97316;"></span> Bookings (Qty)</span>
                            <span class="flex items-center gap-1.5" style="color: #71717a;"><span class="inline-block w-2 h-2 rounded-full" style="background: #cbd5e1;"></span> Returns (Qty)</span>
                            <span class="flex items-center gap-1.5 ml-4" style="color: #71717a;"><span class="inline-block w-4 h-[2px]" style="background: #eab308;"></span> Revenue Trend (₹)</span>
                        </div>
                        <!-- Bar Chart -->
                        <div class="flex-1 relative flex items-end justify-between px-2 pb-6 mt-2 ml-6" style="border-left: 1px solid #e4e4e7; border-bottom: 1px solid #e4e4e7;">
                            <!-- Y Axis Labels -->
                            <div class="absolute -left-8 top-0 bottom-6 flex flex-col justify-between text-[9px] font-mono py-0" style="color: #a1a1aa;">
                                <span>50k</span>
                                <span>25k</span>
                                <span>0</span>
                            </div>
                            <!-- Bars -->
                            <div class="w-[8%] h-[30%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[60%] rounded-t-sm" style="background: #f97316;"></div>
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 p-1 rounded text-[9px] font-mono whitespace-nowrap z-20" style="background: #09090b; color: #ffffff; border: 1px solid #27272a;">B: 12 | R: 8 | ₹15k</div>
                            </div>
                            <div class="w-[8%] h-[45%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[80%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <div class="w-[8%] h-[20%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[40%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <div class="w-[8%] h-[60%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[90%] rounded-t-sm" style="background: #f97316; box-shadow: 0 0 10px rgba(244,125,49,0.3)"></div>
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 p-1 rounded text-[9px] font-mono whitespace-nowrap z-20" style="background: #09090b; color: #ffffff; border: 1px solid #27272a;">B: 24 | R: 18 | ₹32k</div>
                            </div>
                            <div class="w-[8%] h-[75%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[70%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <div class="w-[8%] h-[50%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[50%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <div class="w-[8%] h-[85%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[60%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <div class="w-[8%] h-[40%] rounded-t-sm relative group cursor-pointer" style="background: #f4f4f5;">
                                <div class="absolute bottom-0 w-full h-[85%] rounded-t-sm" style="background: #f97316;"></div>
                            </div>
                            <!-- SVG Trend Line -->
                            <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none">
                                <path d="M 10 70 Q 50 60, 100 80 T 200 40 T 300 30 T 400 50 T 500 20 T 600 35 T 700 15 T 780 40" fill="none" stroke="#eab308" stroke-width="2"></path>
                                <circle cx="10" cy="70" fill="#eab308" r="3"></circle>
                                <circle cx="200" cy="40" fill="#eab308" r="3"></circle>
                                <circle cx="400" cy="50" fill="#eab308" r="3"></circle>
                                <circle cx="500" cy="20" fill="#eab308" r="3"></circle>
                                <circle cx="700" cy="15" fill="#eab308" r="3"></circle>
                            </svg>
                            <!-- X Axis Labels -->
                            <div class="absolute bottom-0 left-0 right-0 flex justify-between text-[9px] font-mono px-2 translate-y-full pt-1" style="color: #a1a1aa;">
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
                        <div class="card-surface p-4 flex-1 flex flex-col" style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px;">
                            <h3 class="text-sm font-semibold mb-3 flex items-center gap-2 flex-shrink-0" style="color: #09090b;">
                                <span class="material-symbols-outlined text-[18px]" style="color: #71717a;">group</span>
                                Today's Store Activity
                            </h3>
                            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-1">
                                <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #f4f4f5;">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold" style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;">RK</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold" style="color: #09090b;">Rahul K.</span>
                                            <span class="text-[9px]" style="color: #71717a;">Processed 4 Bookings</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs text-green-600 font-semibold">+₹12.5k</span>
                                </div>
                                <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #f4f4f5;">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold" style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;">SM</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold" style="color: #09090b;">Sneha M.</span>
                                            <span class="text-[9px]" style="color: #71717a;">Handled 2 Returns</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs" style="color: #a1a1aa;">--</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold" style="background: #09090b; color: #ffffff;">AD</div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold" style="color: #09090b;">Admin</span>
                                            <span class="text-[9px]" style="color: #71717a;">Added 15 New SKUs</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs" style="color: #a1a1aa;">--</span>
                                </div>
                            </div>
                        </div>
                        <!-- Quick Actions -->
                        <div class="flex gap-2 flex-shrink-0">
                            <button onclick="window.location.href='index.php?controller=orders'" class="flex-1 font-semibold py-2 rounded-md flex items-center justify-center gap-1.5 transition-colors text-xs shadow-sm" style="background: #09090b; color: #ffffff;">
                                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                New Bill
                            </button>
                            <button class="flex-1 font-semibold py-2 rounded-md flex items-center justify-center gap-1.5 transition-colors text-xs shadow-sm" style="background: #ffffff; border: 1px solid #e4e4e7; color: #09090b;" onmouseover="this.style.background='#f4f4f5'" onmouseout="this.style.background='#ffffff'">
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
                                    <tr class="table-row-zebra transition-colors ${rowBorder}" style="cursor:pointer; border-bottom: 1px solid #f4f4f5;">
                                        <td class="py-2.5 px-4">
                                            <div class="flex flex-col">
                                                <span class="font-mono font-bold text-xs" style="color: #09090b;">#${b.bill_id}</span>
                                                <span class="text-[11px] truncate w-28" style="color: #71717a;">${b.customer_name || ''}</span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded flex items-center justify-center flex-shrink-0" style="background: #f4f4f5; border: 1px solid #e4e4e7;">
                                                    <span class="material-symbols-outlined text-[14px]" style="color: #71717a;">${icon}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-medium" style="color: #09090b;">${(b.items || 'N/A').substring(0, 24)}</span>
                                                    <span class="text-[10px]" style="color: #71717a;">${b.product_type || ''}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4">
                                            <div class="flex flex-col text-[11px] font-mono" style="color: #71717a;">
                                                <span class="flex items-center gap-1 ${pickIsToday ? 'font-bold' : ''}" style="${pickIsToday ? 'color: #2563eb;' : ''}">
                                                    <span class="material-symbols-outlined text-[12px]" style="${pickIsToday ? 'color: #2563eb;' : 'color: #94a3b8;'}">flight_takeoff</span> ${pickDate}${pickIsToday ? ' (Today)' : ''}
                                                </span>
                                                <span class="flex items-center gap-1 ${returnIsToday ? 'font-bold' : ''}" style="${returnIsToday ? 'color: #d97706;' : ''}">
                                                    <span class="material-symbols-outlined text-[12px]" style="${returnIsToday ? 'color: #d97706;' : 'color: #94a3b8;'}">flight_land</span> ${returnDate}${returnIsToday ? ' (Today)' : ''}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4 text-right">
                                            <div class="flex flex-col">
                                                <span class="font-mono text-xs font-semibold" style="color: #09090b;">₹${amount}</span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4 text-right">
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
                        const icon = refreshBtn.querySelector('.fa-arrows-rotate, .material-symbols-outlined');
                        if (icon) icon.style.animation = '';
                    }
                }
            };

            // Add spin keyframes dynamically
            const styleSheet = document.createElement('style');
            styleSheet.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
            document.head.appendChild(styleSheet);

            fetchStats();
            if (refreshBtn) {
                refreshBtn.addEventListener('click', () => {
                    const icon = refreshBtn.querySelector('.fa-arrows-rotate, .material-symbols-outlined');
                    if (icon) icon.style.animation = 'spin 0.8s linear infinite';
                    fetchStats();
                });
            }
        });
    </script>
</body>
</html>
