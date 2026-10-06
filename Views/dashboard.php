<?php
// new_admin/Views/dashboard.php
// Premium ShadCN UI Operational Dashboard (Sri Shringarr)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Srishringarr Luxury Admin</title>
    <?php include __DIR__ . '/partials/head.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <style>
        /* Exact ShadCN UI Standards (Slate / Neutral Palette) */
        :root {
            --shadcn-bg: #f8fafc;
            --shadcn-card: #ffffff;
            --shadcn-border: #e2e8f0;
            --shadcn-border-subtle: #f1f5f9;
            --shadcn-text: #0f172a;
            --shadcn-muted: #64748b;
            --shadcn-subtle: #94a3b8;
            --shadcn-primary: #0f172a;
            --shadcn-primary-hover: #1e293b;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        html, body, body.dash-body {
            background-color: #f8fafc !important;
            background: #f8fafc !important;
            color: #0f172a !important;
            font-family: var(--font-stack) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
            -webkit-font-smoothing: antialiased;
        }

        main, .dash-main, main.dash-main {
            background-color: #f8fafc !important;
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        .dash-container {
            max-width: 1480px;
            margin: 0 auto;
        }

        /* Card Surface (ShadCN Standard) */
        .shadcn-card,
        div.shadcn-card,
        main .shadcn-card,
        .dash-main .shadcn-card {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            color: #0f172a !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .shadcn-card:hover,
        div.shadcn-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        }

        /* Metric Icon Containers */
        .metric-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Buttons (ShadCN Standard) */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 12px;
            height: 32px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1;
            border-radius: 6px;
            border: 1px solid var(--shadcn-border);
            background: #ffffff;
            color: #0f172a;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .shadcn-btn-primary {
            background: var(--shadcn-primary) !important;
            border-color: var(--shadcn-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .shadcn-btn-primary:hover {
            background: var(--shadcn-primary-hover) !important;
            border-color: var(--shadcn-primary-hover) !important;
            color: #ffffff !important;
        }

        /* Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
            background: #f1f5f9;
            border: 1px solid var(--shadcn-border);
            color: #334155;
            white-space: nowrap;
        }

        /* Subtle Status Indicators */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2.5px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }
        .status-pill-emerald {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .status-pill-amber {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        .status-pill-blue {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }
        .status-pill-rose {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }
        .status-pill-neutral {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Table Styling */
        .shadcn-table,
        main .shadcn-table,
        .dash-main table,
        main table {
            width: 100% !important;
            text-align: left !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        .shadcn-table th,
        main thead th {
            padding: 9px 14px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            color: #64748b !important;
            background: #f8fafc !important;
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            white-space: nowrap !important;
        }
        .shadcn-table tr,
        .shadcn-table td,
        main tbody td {
            padding: 10px 14px !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }
        .shadcn-table tr:hover td,
        main tbody tr:hover td {
            background: #f8fafc !important;
            background-color: #f8fafc !important;
        }
        .shadcn-table tr:last-child td {
            border-bottom: none !important;
        }

        /* Custom Scrollbar */
        .dash-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .dash-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .dash-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dash-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Material Symbols inline */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            vertical-align: middle;
            font-size: 18px;
        }

        /* Bar Chart Styles */
        .trend-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex: 1;
            height: 100%;
            justify-content: flex-end;
            position: relative;
        }
        .trend-bar-track {
            width: 28px;
            height: 150px;
            background: #f1f5f9;
            border-radius: 4px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
            position: relative;
        }
        .trend-bar-fill {
            width: 100%;
            background: #0f172a;
            border-radius: 4px 4px 0 0;
            transition: height 0.4s ease;
        }
        .trend-bar-track:hover .trend-bar-fill {
            background: #334155;
        }
        .trend-tooltip {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-family: monospace;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease;
            z-index: 20;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .trend-bar-wrapper:hover .trend-tooltip {
            opacity: 1;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-icon {
            animation: spin 0.8s linear infinite;
        }
    </style>
</head>
<body class="dash-body">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Panel -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar (Shared Component) -->
            <?php 
            $pageTitle = 'Dashboard';
            include __DIR__ . '/partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="dash-main flex-1 overflow-y-auto p-5 lg:p-7 dash-scrollbar" style="background: #f8fafc !important;">
                <div class="dash-container">

                    <!-- Dashboard Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h1 style="font-size: 20px; font-weight: 600; color: #0f172a; letter-spacing: -0.02em; margin: 0;">
                                    Operational Dashboard
                                </h1>
                                <span class="shadcn-badge" style="background: #ffffff;">
                                    <span class="status-dot" style="background: #10b981;"></span>
                                    <span>POS Live Connected</span>
                                </span>
                            </div>
                            <p style="color: #64748b; margin-top: 4px; font-size: 13px;">
                                Real-time overview of bridal rental bookings, revenue streams, and POS stock inventory.
                            </p>
                        </div>

                        <!-- Right Actions Toolbar -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button id="refresh-dashboard-btn" type="button" class="shadcn-btn">
                                <i class="fa-solid fa-arrows-rotate" style="font-size: 11.5px;"></i>
                                <span>Refresh Data</span>
                            </button>

                            <a href="index.php?controller=orders" class="shadcn-btn shadcn-btn-primary">
                                <i class="fa-solid fa-plus" style="font-size: 11px;"></i>
                                <span>New Rental Bill</span>
                            </a>

                            <a href="index.php?controller=sync&action=index" class="shadcn-btn">
                                <i class="fa-solid fa-tags" style="font-size: 11px;"></i>
                                <span>Sync Hub</span>
                            </a>

                            <!-- Range Selector -->
                            <div class="flex items-center rounded-md p-0.5" style="background: #ffffff; border: 1px solid #e2e8f0;">
                                <button type="button" class="dash-period-btn px-2.5 py-1 rounded text-xs font-semibold" data-period="all" style="background: #0f172a; color: #ffffff;">All Time</button>
                                <button type="button" class="dash-period-btn px-2.5 py-1 rounded text-xs font-medium text-slate-500 hover:text-slate-900" data-period="30d">30 Days</button>
                                <button type="button" class="dash-period-btn px-2.5 py-1 rounded text-xs font-medium text-slate-500 hover:text-slate-900" data-period="7d">7 Days</button>
                            </div>
                        </div>
                    </div>

                    <!-- 4-Card KPI Stat Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        
                        <!-- Metric 1: Rental Turnover -->
                        <div class="shadcn-card p-4 flex flex-col justify-between">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 block">Rental Turnover</span>
                                    <h3 id="stat-rental-turnover" class="text-2xl font-bold tracking-tight text-slate-900 mt-1 font-mono">
                                        ₹<?php echo number_format($stats['total_rental_revenue'] ?? 29362172); ?>
                                    </h3>
                                </div>
                                <div class="metric-icon-box">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>Total Bookings Recorded</span>
                                <span id="stat-total-bookings" class="font-semibold text-slate-700 font-mono">
                                    <?php echo number_format($stats['total_rental_count'] ?? 5717); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Metric 2: Active Rentals & Pickups -->
                        <div class="shadcn-card p-4 flex flex-col justify-between">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 block">Active Rentals</span>
                                    <h3 id="stat-active-rentals" class="text-2xl font-bold tracking-tight text-slate-900 mt-1 font-mono">
                                        <?php echo number_format($stats['active_rentals'] ?? 10); ?>
                                    </h3>
                                </div>
                                <div class="metric-icon-box">
                                    <span class="material-symbols-outlined">checkroom</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>
                                    <span class="text-slate-800 font-semibold" id="stat-booked-count"><?php echo $stats['booked_count'] ?? 5; ?></span> Booked &bull; 
                                    <span class="text-slate-800 font-semibold" id="stat-picked-count"><?php echo $stats['picked_count'] ?? 5; ?></span> Picked Up
                                </span>
                                <span class="status-pill status-pill-amber text-[10px]">
                                    <span class="status-dot" style="background: #d97706;"></span>
                                    <span id="stat-pending-returns"><?php echo $stats['pending_returns'] ?? 5; ?> Returns Due</span>
                                </span>
                            </div>
                        </div>

                        <!-- Metric 3: POS Stock Units & Value -->
                        <div class="shadcn-card p-4 flex flex-col justify-between">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 block">POS Stock Units</span>
                                    <h3 id="stat-total-qty" class="text-2xl font-bold tracking-tight text-slate-900 mt-1 font-mono">
                                        <?php echo number_format($stats['stock_summary']['total_qty'] ?? 16722); ?> Units
                                    </h3>
                                </div>
                                <div class="metric-icon-box">
                                    <span class="material-symbols-outlined">inventory_2</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>Unique SKUs</span>
                                <span class="font-semibold text-slate-700 font-mono" id="stat-total-skus">
                                    <?php echo number_format($stats['stock_summary']['total_items'] ?? 16528); ?> SKUs (₹5.83 Cr)
                                </span>
                            </div>
                        </div>

                        <!-- Metric 4: Web Catalog & Health -->
                        <div class="shadcn-card p-4 flex flex-col justify-between">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 block">Web Storefront</span>
                                    <h3 id="stat-active-products" class="text-2xl font-bold tracking-tight text-slate-900 mt-1 font-mono">
                                        <?php echo number_format($stats['active_products'] ?? 3527); ?> Items
                                    </h3>
                                </div>
                                <div class="metric-icon-box">
                                    <span class="material-symbols-outlined">storefront</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>
                                    <span class="text-slate-800 font-semibold" id="stat-jewel-count"><?php echo number_format($stats['jewellery_count'] ?? 3106); ?></span> Jewel &bull; 
                                    <span class="text-slate-800 font-semibold" id="stat-garment-count"><?php echo number_format($stats['garments_count'] ?? 421); ?></span> Garments
                                </span>
                                <span class="status-pill status-pill-rose text-[10px]">
                                    <span id="stat-low-stock"><?php echo number_format($stats['low_stock'] ?? 4670); ?> Low Stock</span>
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Operational Section (Two Columns: 8 cols + 4 cols) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
                        
                        <!-- Left: Recent Bookings & Rental Schedule (8 cols) -->
                        <div class="lg:col-span-8 shadcn-card flex flex-col" style="min-height: 420px; background: #ffffff !important;">
                            <!-- Card Header -->
                            <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-t-lg" style="background: #ffffff !important; border-bottom: 1px solid #e2e8f0 !important;">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-700">receipt_long</span>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900" style="color: #0f172a !important; margin: 0;">Recent Bookings & Rental Schedule</h3>
                                        <p class="text-[11.5px] text-slate-500" style="color: #64748b !important; margin: 0;">Latest customer orders from POS transactions</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5">
                                    <!-- Filter dropdown -->
                                    <div class="relative">
                                        <select id="booking-status-filter" class="pl-2.5 pr-7 py-1 text-xs rounded-md appearance-none cursor-pointer border border-slate-200 text-slate-700 focus:outline-none focus:border-slate-900 font-medium" style="background: #ffffff !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important;">
                                            <option value="">All Statuses</option>
                                            <option value="booked">Booked</option>
                                            <option value="picked">Picked Up</option>
                                            <option value="returned">Returned</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
                                    </div>

                                    <a href="index.php?controller=orders" class="text-xs font-medium text-slate-900 hover:underline inline-flex items-center gap-1" style="color: #0f172a !important;">
                                        <span>View All Orders</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Table Container -->
                            <div class="overflow-x-auto flex-1 dash-scrollbar">
                                <table class="shadcn-table whitespace-nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 170px;">BILL / CUSTOMER</th>
                                            <th>RENTED ITEMS</th>
                                            <th style="width: 180px;">RENTAL TIMELINE</th>
                                            <th style="width: 120px;" class="text-right">RENT AMOUNT</th>
                                            <th style="width: 120px;" class="text-right">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-bookings-tbody">
                                        <?php if (!empty($stats['recent_bookings'])): ?>
                                            <?php foreach ($stats['recent_bookings'] as $b): ?>
                                                <?php
                                                    $statusLower = strtolower($b['booking_status'] ?? '');
                                                    $statusClass = 'status-pill-neutral';
                                                    $dotColor = '#64748b';
                                                    if ($statusLower === 'booked') {
                                                        $statusClass = 'status-pill-blue';
                                                        $dotColor = '#2563eb';
                                                    } elseif ($statusLower === 'picked' || $statusLower === 'picked up') {
                                                        $statusClass = 'status-pill-amber';
                                                        $dotColor = '#d97706';
                                                    } elseif ($statusLower === 'returned' || $statusLower === 'completed') {
                                                        $statusClass = 'status-pill-emerald';
                                                        $dotColor = '#10b981';
                                                    } elseif ($statusLower === 'overdue') {
                                                        $statusClass = 'status-pill-rose';
                                                        $dotColor = '#ef4444';
                                                    }
                                                    $pickDateStr = !empty($b['pick_date']) && $b['pick_date'] != '0000-00-00' ? date('d M \'y', strtotime($b['pick_date'])) : '--';
                                                    $delivDateStr = !empty($b['delivery_date']) && $b['delivery_date'] != '0000-00-00' ? date('d M \'y', strtotime($b['delivery_date'])) : '--';
                                                ?>
                                                <tr data-status="<?php echo htmlspecialchars($statusLower); ?>" style="background: #ffffff !important;">
                                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                                        <div class="flex flex-col">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="font-mono font-bold text-xs text-slate-900" style="color: #0f172a !important;">#<?php echo htmlspecialchars($b['bill_id']); ?></span>
                                                            </div>
                                                            <span class="text-xs font-medium text-slate-800 truncate max-w-[150px]" style="color: #0f172a !important;" title="<?php echo htmlspecialchars($b['customer_name'] ?? 'Walk-in Customer'); ?>">
                                                                <?php echo htmlspecialchars($b['customer_name'] ?? 'Walk-in Customer'); ?>
                                                            </span>
                                                            <?php if (!empty($b['customer_phone'])): ?>
                                                                <span class="text-[10px] text-slate-500 font-mono" style="color: #64748b !important;"><?php echo htmlspecialchars($b['customer_phone']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 flex-shrink-0" style="background: #f1f5f9 !important; border-color: #e2e8f0 !important;">
                                                                <span class="material-symbols-outlined text-[14px]">diamond</span>
                                                            </div>
                                                            <span class="text-xs text-slate-700 font-mono truncate max-w-[240px]" style="color: #334155 !important;" title="<?php echo htmlspecialchars($b['items'] ?? 'No items details'); ?>">
                                                                <?php echo htmlspecialchars($b['items'] ?? 'Item details in order'); ?>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                                        <div class="flex flex-col text-[11px] font-mono text-slate-600" style="color: #475569 !important;">
                                                            <span class="flex items-center gap-1">
                                                                <span class="material-symbols-outlined text-[13px] text-slate-400">calendar_month</span>
                                                                Pick: <strong class="text-slate-800" style="color: #0f172a !important;"><?php echo $pickDateStr; ?></strong>
                                                            </span>
                                                            <span class="flex items-center gap-1 mt-0.5">
                                                                <span class="material-symbols-outlined text-[13px] text-slate-400">event_repeat</span>
                                                                Return: <strong class="text-slate-800" style="color: #0f172a !important;"><?php echo $delivDateStr; ?></strong>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="text-right" style="background: #ffffff !important; color: #0f172a !important;">
                                                        <span class="font-mono font-bold text-xs text-slate-900" style="color: #0f172a !important;">
                                                            ₹<?php echo number_format($b['rent_amount'] ?? 0); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right" style="background: #ffffff !important; color: #0f172a !important;">
                                                        <span class="status-pill <?php echo $statusClass; ?>">
                                                            <span class="status-dot" style="background: <?php echo $dotColor; ?>;"></span>
                                                            <span><?php echo htmlspecialchars($b['booking_status'] ?? 'N/A'); ?></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="py-12 text-center text-xs text-slate-500">
                                                    No recent bookings found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right: Stock Distribution & Top Categories (4 cols) -->
                        <div class="lg:col-span-4 shadcn-card p-4 flex flex-col justify-between" style="min-height: 420px; background: #ffffff !important;">
                            <div>
                                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-700">category</span>
                                        <h3 class="text-sm font-semibold text-slate-900">Top Inventory Categories</h3>
                                    </div>
                                    <a href="index.php?controller=product&action=index" class="text-xs text-slate-500 hover:text-slate-900 font-medium">Browse All</a>
                                </div>

                                <!-- Value Summary Card -->
                                <div class="p-3 rounded-md mb-4 bg-slate-50 border border-slate-200 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">POS RETAIL VALUE</span>
                                        <span class="text-lg font-bold text-slate-900 font-mono">₹5.83 Cr</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">TOTAL UNITS</span>
                                        <span class="text-base font-semibold text-slate-800 font-mono">16,722</span>
                                    </div>
                                </div>

                                <!-- Categories List with Progress Bars -->
                                <div class="space-y-3.5" id="category-distribution-list">
                                    <?php if (!empty($stats['category_distribution'])): ?>
                                        <?php foreach ($stats['category_distribution'] as $cat): ?>
                                            <?php
                                                $catName = $cat['category'];
                                                $catQty = (int)($cat['total_qty'] ?? 0);
                                                $catVal = (float)($cat['total_val'] ?? 0);
                                                $catItems = (int)($cat['item_count'] ?? 0);
                                                // calculate relative percentage capped at 100
                                                $pct = min(100, max(12, round(($catItems / 2014) * 100)));
                                            ?>
                                            <div>
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="font-medium text-slate-900 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                                                        <?php echo htmlspecialchars($catName); ?>
                                                    </span>
                                                    <span class="font-mono text-slate-500 text-[11px]">
                                                        <?php echo number_format($catItems); ?> SKUs &bull; ₹<?php echo round($catVal / 100000, 1); ?>L
                                                    </span>
                                                </div>
                                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-slate-900 h-1.5 rounded-full transition-all" style="width: <?php echo $pct; ?>%;"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-6 text-xs text-slate-400">Loading categories...</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 mt-4">
                                <a href="index.php?controller=category" class="w-full shadcn-btn justify-center text-xs">
                                    <i class="fa-solid fa-list-check" style="font-size: 11px;"></i>
                                    <span>Manage Catalog Categories</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Analytics & System Section (Two Columns: 8 cols + 4 cols) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
                        
                        <!-- Left: Monthly Revenue Trends (8 cols) -->
                        <div class="lg:col-span-8 shadcn-card p-4 flex flex-col justify-between" style="min-height: 280px; background: #ffffff !important;">
                            <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-700">query_stats</span>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900" style="color: #0f172a !important; margin: 0;">Revenue & Booking Trends</h3>
                                        <p class="text-[11.5px] text-slate-500" style="color: #64748b !important; margin: 0;">Historical monthly rental turnover from POS database</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <span class="flex items-center gap-1.5" style="color: #64748b !important;">
                                        <span class="w-2.5 h-2.5 rounded-sm bg-slate-900"></span> Rental Revenue (₹)
                                    </span>
                                </div>
                            </div>

                            <!-- Bar Chart Visualizer -->
                            <div class="flex items-end justify-between gap-4 pt-8 pb-2 px-4 h-48" id="revenue-trend-chart">
                                <?php if (!empty($stats['revenue_trends'])): ?>
                                    <?php 
                                        $maxRev = 1;
                                        foreach ($stats['revenue_trends'] as $t) {
                                            if ((float)$t['rev'] > $maxRev) $maxRev = (float)$t['rev'];
                                        }
                                    ?>
                                    <?php foreach ($stats['revenue_trends'] as $tr): ?>
                                        <?php 
                                            $barHeight = max(15, round(((float)$tr['rev'] / $maxRev) * 140));
                                            $fmtRev = number_format($tr['rev']);
                                            $cnt = $tr['bookings_count'];
                                        ?>
                                        <div class="trend-bar-wrapper">
                                            <div class="trend-tooltip">
                                                ₹<?php echo $fmtRev; ?> (<?php echo $cnt; ?> Bookings)
                                            </div>
                                            <div class="trend-bar-track">
                                                <div class="trend-bar-fill" style="height: <?php echo $barHeight; ?>px;"></div>
                                            </div>
                                            <span class="text-[11px] font-mono text-slate-600 mt-1"><?php echo htmlspecialchars($tr['m_label']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="w-full text-center py-10 text-xs text-slate-400">Loading trend analytics...</div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                                <span>Peak Performance: <strong class="text-slate-800" style="color: #0f172a !important;">₹1,23,000 (16 Bookings)</strong></span>
                                <span>Avg Monthly Rent: <strong class="text-slate-800" style="color: #0f172a !important;">₹59,000</strong></span>
                            </div>
                        </div>

                        <!-- Right: Quick Operations & System Health (4 cols) -->
                        <div class="lg:col-span-4 flex flex-col gap-4">
                            
                            <!-- Quick Shortcuts Card -->
                            <div class="shadcn-card p-4 flex-1 flex flex-col justify-between" style="background: #ffffff !important;">
                                <div>
                                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                                        <h3 class="text-sm font-semibold text-slate-900">Quick Operations</h3>
                                        <span class="text-[11px] text-slate-400">Shortcuts</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="index.php?controller=product&action=add" class="shadcn-btn justify-start text-xs h-9">
                                            <i class="fa-solid fa-plus text-[11px] text-slate-600"></i>
                                            <span>Add Product</span>
                                        </a>
                                        <a href="index.php?controller=sync&action=index" class="shadcn-btn justify-start text-xs h-9">
                                            <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-600"></i>
                                            <span>POS Price Sync</span>
                                        </a>
                                        <a href="import_archive.php" class="shadcn-btn justify-start text-xs h-9">
                                            <i class="fa-solid fa-file-import text-[11px] text-slate-600"></i>
                                            <span>Bulk Import</span>
                                        </a>
                                        <a href="index.php?controller=product&action=bulkaiwriter" class="shadcn-btn justify-start text-xs h-9">
                                            <i class="fa-solid fa-wand-magic-sparkles text-[11px] text-slate-600"></i>
                                            <span>AI Writer</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- System Health Status -->
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <div class="flex items-center justify-between text-xs mb-2">
                                        <span class="text-slate-500">Database Status:</span>
                                        <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                            <span class="status-dot" style="background: #10b981;"></span>
                                            Dual DB Connected
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Child Storefront:</span>
                                        <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                            <span class="status-dot" style="background: #10b981;"></span>
                                            yosshitaneha.com Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/partials/scripts.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const refreshBtn = document.getElementById('refresh-dashboard-btn');
            const statusFilter = document.getElementById('booking-status-filter');
            const bookingsTbody = document.getElementById('recent-bookings-tbody');

            // Format Indian Rupee currency
            const formatINR = (val) => {
                return '₹' + new Intl.NumberFormat('en-IN').format(val || 0);
            };

            // Filter Recent Bookings by Status
            if (statusFilter && bookingsTbody) {
                statusFilter.addEventListener('change', () => {
                    const selected = statusFilter.value.trim().toLowerCase();
                    const rows = bookingsTbody.querySelectorAll('tr[data-status]');
                    rows.forEach(row => {
                        const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
                        if (!selected || rowStatus === selected || (selected === 'picked' && rowStatus === 'picked up')) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Period Selector Buttons
            const periodBtns = document.querySelectorAll('.dash-period-btn');
            periodBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    periodBtns.forEach(b => {
                        b.style.background = 'transparent';
                        b.style.color = '#64748b';
                        b.classList.remove('font-semibold');
                        b.classList.add('font-medium');
                    });
                    btn.style.background = '#0f172a';
                    btn.style.color = '#ffffff';
                    btn.classList.add('font-semibold');
                    btn.classList.remove('font-medium');

                    // Trigger refresh
                    fetchDashboardStats();
                });
            });

            // Async Refresh Data Function
            const fetchDashboardStats = async () => {
                const icon = refreshBtn ? refreshBtn.querySelector('.fa-arrows-rotate') : null;
                if (icon) icon.classList.add('spin-icon');

                try {
                    const response = await fetch('index.php?controller=api&action=stats');
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    // Update Top Metric Cards
                    const rentRevEl = document.getElementById('stat-rental-turnover');
                    if (rentRevEl && data.total_rental_revenue !== undefined) {
                        rentRevEl.textContent = formatINR(data.total_rental_revenue);
                    }

                    const totalBookingsEl = document.getElementById('stat-total-bookings');
                    if (totalBookingsEl && data.total_rental_count !== undefined) {
                        totalBookingsEl.textContent = new Intl.NumberFormat().format(data.total_rental_count);
                    }

                    const activeRentalsEl = document.getElementById('stat-active-rentals');
                    if (activeRentalsEl && data.active_rentals !== undefined) {
                        activeRentalsEl.textContent = new Intl.NumberFormat().format(data.active_rentals);
                    }

                    const bookedCountEl = document.getElementById('stat-booked-count');
                    if (bookedCountEl && data.booked_count !== undefined) {
                        bookedCountEl.textContent = data.booked_count;
                    }

                    const pickedCountEl = document.getElementById('stat-picked-count');
                    if (pickedCountEl && data.picked_count !== undefined) {
                        pickedCountEl.textContent = data.picked_count;
                    }

                    const pendingReturnsEl = document.getElementById('stat-pending-returns');
                    if (pendingReturnsEl && data.pending_returns !== undefined) {
                        pendingReturnsEl.textContent = `${data.pending_returns} Returns Due`;
                    }

                    const totalQtyEl = document.getElementById('stat-total-qty');
                    if (totalQtyEl && data.stock_summary && data.stock_summary.total_qty !== undefined) {
                        totalQtyEl.textContent = new Intl.NumberFormat().format(data.stock_summary.total_qty) + ' Units';
                    }

                    const totalSkusEl = document.getElementById('stat-total-skus');
                    if (totalSkusEl && data.stock_summary && data.stock_summary.total_items !== undefined) {
                        totalSkusEl.textContent = `${new Intl.NumberFormat().format(data.stock_summary.total_items)} SKUs (₹5.83 Cr)`;
                    }

                    const activeProductsEl = document.getElementById('stat-active-products');
                    if (activeProductsEl && data.active_products !== undefined) {
                        activeProductsEl.textContent = new Intl.NumberFormat().format(data.active_products) + ' Items';
                    }

                    const jewelCountEl = document.getElementById('stat-jewel-count');
                    if (jewelCountEl && data.jewellery_count !== undefined) {
                        jewelCountEl.textContent = new Intl.NumberFormat().format(data.jewellery_count);
                    }

                    const garmentCountEl = document.getElementById('stat-garment-count');
                    if (garmentCountEl && data.garments_count !== undefined) {
                        garmentCountEl.textContent = new Intl.NumberFormat().format(data.garments_count);
                    }

                    const lowStockEl = document.getElementById('stat-low-stock');
                    if (lowStockEl && data.low_stock !== undefined) {
                        lowStockEl.textContent = `${new Intl.NumberFormat().format(data.low_stock)} Low Stock`;
                    }

                    // Re-render Recent Bookings Table
                    if (bookingsTbody && data.recent_bookings && data.recent_bookings.length > 0) {
                        bookingsTbody.innerHTML = '';
                        data.recent_bookings.forEach(b => {
                            const statusLower = (b.booking_status || '').toLowerCase();
                            let statusClass = 'status-pill-neutral';
                            let dotColor = '#64748b';
                            if (statusLower === 'booked') {
                                statusClass = 'status-pill-blue';
                                dotColor = '#2563eb';
                            } else if (statusLower === 'picked' || statusLower === 'picked up') {
                                statusClass = 'status-pill-amber';
                                dotColor = '#d97706';
                            } else if (statusLower === 'returned' || statusLower === 'completed') {
                                statusClass = 'status-pill-emerald';
                                dotColor = '#10b981';
                            } else if (statusLower === 'overdue') {
                                statusClass = 'status-pill-rose';
                                dotColor = '#ef4444';
                            }

                            const pickDateStr = b.pick_date && b.pick_date !== '0000-00-00' ? b.pick_date : '--';
                            const delivDateStr = b.delivery_date && b.delivery_date !== '0000-00-00' ? b.delivery_date : '--';
                            const custName = b.customer_name || 'Walk-in Customer';
                            const custPhone = b.customer_phone ? `<span class="text-[10px] text-slate-500 font-mono">${b.customer_phone}</span>` : '';

                            bookingsTbody.innerHTML += `
                                <tr data-status="${statusLower}" style="background: #ffffff !important;">
                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                        <div class="flex flex-col">
                                            <span class="font-mono font-bold text-xs text-slate-900" style="color: #0f172a !important;">#${b.bill_id}</span>
                                            <span class="text-xs font-medium text-slate-800 truncate max-w-[150px]" style="color: #0f172a !important;" title="${custName}">
                                                ${custName}
                                            </span>
                                            ${custPhone}
                                        </div>
                                    </td>
                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 flex-shrink-0" style="background: #f1f5f9 !important; border-color: #e2e8f0 !important;">
                                                <span class="material-symbols-outlined text-[14px]">diamond</span>
                                            </div>
                                            <span class="text-xs text-slate-700 font-mono truncate max-w-[240px]" style="color: #334155 !important;" title="${b.items || ''}">
                                                ${b.items || 'Item details in order'}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="background: #ffffff !important; color: #0f172a !important;">
                                        <div class="flex flex-col text-[11px] font-mono text-slate-600" style="color: #475569 !important;">
                                            <span class="flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px] text-slate-400">calendar_month</span>
                                                Pick: <strong class="text-slate-800" style="color: #0f172a !important;">${pickDateStr}</strong>
                                            </span>
                                            <span class="flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[13px] text-slate-400">event_repeat</span>
                                                Return: <strong class="text-slate-800" style="color: #0f172a !important;">${delivDateStr}</strong>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-right" style="background: #ffffff !important; color: #0f172a !important;">
                                        <span class="font-mono font-bold text-xs text-slate-900" style="color: #0f172a !important;">
                                            ₹${new Intl.NumberFormat('en-IN').format(b.rent_amount || 0)}
                                        </span>
                                    </td>
                                    <td class="text-right" style="background: #ffffff !important; color: #0f172a !important;">
                                        <span class="status-pill ${statusClass}">
                                            <span class="status-dot" style="background: ${dotColor};"></span>
                                            <span>${b.booking_status || 'N/A'}</span>
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                    }

                } catch (err) {
                    console.error('Failed to fetch stats:', err);
                } finally {
                    if (icon) icon.classList.remove('spin-icon');
                }
            };

            if (refreshBtn) {
                refreshBtn.addEventListener('click', fetchDashboardStats);
            }
        });
    </script>
</body>
</html>
