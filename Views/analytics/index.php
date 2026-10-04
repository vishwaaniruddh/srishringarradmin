<!DOCTYPE html>
<html lang="en">
<head>
    <title>Analytics & Traffic Monitor - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Matching yn/admin & products catalog) */
        :root {
            --wp-dark: #09090b;
            --wp-blue: #2563eb;
            --wp-border: #e4e4e7;
            --wp-border-muted: #f4f4f5;
            --wp-bg: #fafafa;
            --wp-card: #ffffff;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: var(--wp-bg) !important;
            color: var(--wp-text) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Card Surface */
        .card-surface {
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        /* Shadcn Buttons */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 500;
            font-size: 12.5px;
            height: 32px;
            padding: 0 12px;
            border-radius: 6px;
            transition: all 0.12s ease;
            text-decoration: none !important;
            cursor: pointer;
            white-space: nowrap;
            line-height: 1;
        }
        .shadcn-btn-primary {
            background-color: #09090b !important;
            color: #ffffff !important;
            border: 1px solid #09090b !important;
        }
        .shadcn-btn-primary:hover {
            background-color: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }
        .shadcn-btn-outline {
            background-color: #ffffff !important;
            color: #09090b !important;
            border: 1px solid #e4e4e7 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .shadcn-btn-outline:hover {
            background-color: #f4f4f5 !important;
            border-color: #d4d4d8 !important;
        }

        /* Shadcn Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 0.02em;
            line-height: 1.2;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        /* KPI Stat Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.15s ease;
        }
        .stat-card:hover {
            border-color: #d4d4d8;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }
        .stat-icon-wrap {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #09090b;
            font-size: 13px;
            flex-shrink: 0;
        }

        /* Trending Product Cards */
        .trending-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            text-decoration: none !important;
            color: inherit !important;
        }
        .trending-card:hover {
            border-color: #09090b;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }
        .trending-card .rank-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        .rank-1 { background: #09090b; }
        .rank-2 { background: #3f3f46; }
        .rank-3 { background: #71717a; }
        .rank-default { background: #a1a1aa; }

        .trending-card .product-img-wrap {
            width: 100%;
            aspect-ratio: 1;
            background: #f4f4f5;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #f4f4f5;
        }
        .trending-card .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .trending-card:hover .product-img {
            transform: scale(1.04);
        }
        .trending-card .visit-site-btn {
            position: absolute;
            bottom: 8px;
            left: 8px;
            right: 8px;
            opacity: 0;
            transform: translateY(6px);
            transition: all 0.2s ease;
            z-index: 5;
        }
        .trending-card:hover .visit-site-btn {
            opacity: 1;
            transform: translateY(0);
        }

        /* Timeline and Session Cards */
        .session-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            transition: all 0.15s ease;
            overflow: hidden;
        }
        .session-card:hover {
            border-color: #d4d4d8;
        }
        .timeline-line {
            position: relative;
        }
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: #e4e4e7;
        }
        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            position: absolute;
            left: 11px;
            top: 7px;
            background: #71717a;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 1px #e4e4e7;
        }
        .dot-product_view { background: #2563eb !important; }
        .dot-shop_view { background: #16a34a !important; }
        .dot-category_view { background: #d97706 !important; }
        .dot-page_view { background: #71717a !important; }
        .dot-cart_add, .dot-cart_view { background: #db2777 !important; }
        .dot-checkout_start { background: #9333ea !important; }
        .dot-search { background: #0284c7 !important; }

        /* Event Badges */
        .event-badge {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
            line-height: 1.3;
        }
        .badge-product_view { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-shop_view { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-category_view { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-page_view { background: #f4f4f5; color: #52525b; border: 1px solid #e4e4e7; }
        .badge-cart_add, .badge-cart_view { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
        .badge-checkout_start { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-search { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }

        /* Progress Bar */
        .mini-bar {
            height: 4px;
            border-radius: 2px;
            transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body>

    <div class="flex min-h-screen">
        <!-- Shared Dark Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Shared Topbar -->
            <?php 
            $pageTitle = 'Analytics & Reports';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-gray-50/50">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; gap: 14px; flex-wrap: wrap;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <h1 style="font-size: 20px; font-weight: 600; color: #09090b; letter-spacing: -0.02em; margin: 0; line-height: 1.2;">
                                    Traffic &amp; Visitor Analytics
                                </h1>
                                <span class="shadcn-badge">
                                    <i class="fa-solid fa-chart-line" style="margin-right: 5px; color: #2563eb;"></i> LIVE MONITOR
                                </span>
                            </div>
                            <p style="font-size: 13px; color: #71717a; margin: 4px 0 0 0;">
                                Real-time storefront visitor activity, trending products, user journeys, and funnel conversions.
                            </p>
                        </div>
                    </div>

                    <!-- Date Filter Bar (ShadCN Card Surface) -->
                    <div class="card-surface" style="padding: 14px 16px; margin-bottom: 24px;">
                        <form method="GET" action="index.php" style="display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap;">
                            <input type="hidden" name="controller" value="analytics">
                            <input type="hidden" name="action" value="index">

                            <!-- Preset Tabs -->
                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span style="font-size: 12px; font-weight: 600; color: #71717a; margin-right: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fa-regular fa-calendar" style="font-size: 11px;"></i> Period:
                                </span>
                                <?php 
                                $presets = [
                                    'all' => 'All Time',
                                    'today' => 'Today',
                                    '7days' => 'Last 7 Days',
                                    '30days' => 'Last 30 Days',
                                    'this_month' => 'This Month'
                                ];
                                $activePreset = $preset ?: ($startDate || $endDate ? 'custom' : 'all');
                                foreach ($presets as $key => $label): 
                                    $isActive = ($activePreset === $key);
                                ?>
                                    <a href="index.php?controller=analytics&action=index&preset=<?php echo $key; ?>" 
                                       class="shadcn-btn <?php echo $isActive ? 'shadcn-btn-primary' : 'shadcn-btn-outline'; ?>"
                                       style="height: 30px; font-size: 12px; padding: 0 11px;">
                                        <?php echo $label; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <!-- Custom Date Range Form -->
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 6px; padding: 4px 8px; height: 32px;">
                                    <span style="font-size: 10.5px; font-weight: 600; color: #71717a; text-transform: uppercase;">From</span>
                                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" 
                                           style="border: none; background: transparent; font-size: 12px; color: #09090b; outline: none; font-family: inherit; cursor: pointer;">
                                </div>
                                <div style="display: flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 6px; padding: 4px 8px; height: 32px;">
                                    <span style="font-size: 10.5px; font-weight: 600; color: #71717a; text-transform: uppercase;">To</span>
                                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" 
                                           style="border: none; background: transparent; font-size: 12px; color: #09090b; outline: none; font-family: inherit; cursor: pointer;">
                                </div>
                                <button type="submit" class="shadcn-btn shadcn-btn-primary" style="height: 32px;">
                                    <i class="fa-solid fa-filter" style="font-size: 10px;"></i> Apply
                                </button>
                                <?php if ($startDate || $endDate || $preset): ?>
                                    <a href="index.php?controller=analytics&action=index" class="shadcn-btn shadcn-btn-outline" style="height: 32px; color: #71717a;" title="Clear date filter">
                                        <i class="fa-solid fa-xmark" style="font-size: 11px;"></i> Reset
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if ($startDate || $endDate): ?>
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f4f4f5; display: flex; align-items: center; gap: 6px; font-size: 12px; color: #71717a;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
                                Active Filter: 
                                <strong style="color: #09090b;">
                                    <?php echo $startDate ? date('d M Y', strtotime($startDate)) : 'Beginning'; ?> 
                                    &rarr; 
                                    <?php echo $endDate ? date('d M Y', strtotime($endDate)) : 'Today'; ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Stats Overview Row -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 24px;">
                        <?php
                        $statsCards = [
                            ['label' => 'Unique Visitors', 'value' => $totalSessions, 'icon' => 'fa-solid fa-users', 'sub' => 'Distinct IP addresses'],
                            ['label' => 'Total Page Views', 'value' => $totalPageViews, 'icon' => 'fa-solid fa-eye', 'sub' => 'Total pages served'],
                            ['label' => 'Product Views', 'value' => $totalProductViews, 'icon' => 'fa-solid fa-gem', 'sub' => 'Catalog engagement'],
                            ['label' => 'Cart Additions', 'value' => $funnel['cart_adds'], 'icon' => 'fa-solid fa-cart-shopping', 'sub' => 'High purchase intent'],
                        ];
                        foreach ($statsCards as $sc):
                        ?>
                        <div class="stat-card">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-size: 11px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em;">
                                    <?php echo $sc['label']; ?>
                                </span>
                                <div class="stat-icon-wrap">
                                    <i class="<?php echo $sc['icon']; ?>"></i>
                                </div>
                            </div>
                            <div style="font-size: 24px; font-weight: 700; color: #09090b; letter-spacing: -0.02em; line-height: 1.1; margin-bottom: 4px;">
                                <?php echo number_format($sc['value']); ?>
                            </div>
                            <div style="font-size: 11.5px; color: #a1a1aa;">
                                <?php echo $sc['sub']; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ============================================= -->
                    <!-- TRENDING PRODUCTS - Hero Catalog Showcase      -->
                    <!-- ============================================= -->
                    <div style="margin-bottom: 28px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 13px;">
                                    <i class="fa-solid fa-fire"></i>
                                </div>
                                <div>
                                    <h2 style="font-size: 15px; font-weight: 600; color: #09090b; letter-spacing: -0.01em; margin: 0; line-height: 1.2;">
                                        Trending Products
                                    </h2>
                                    <p style="font-size: 12px; color: #71717a; margin: 2px 0 0 0;">
                                        Top products with the highest customer views and engagement.
                                    </p>
                                </div>
                            </div>
                            <span class="shadcn-badge">
                                TOP 10 ITEMS
                            </span>
                        </div>

                        <?php if (empty($topProducts)): ?>
                            <div class="card-surface" style="padding: 48px; text-align: center;">
                                <i class="fa-solid fa-chart-line" style="font-size: 28px; color: #d4d4d8; margin-bottom: 10px;"></i>
                                <p style="font-size: 13px; color: #71717a; margin: 0;">No product views logged yet for the selected time range.</p>
                            </div>
                        <?php else: ?>
                            <!-- Product Grid -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 14px;">
                                <?php foreach ($topProducts as $i => $p): 
                                    $rank = $i + 1;
                                    $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-default'));
                                    $convRate = $p['view_count'] > 0 ? round(($p['cart_adds'] / $p['view_count']) * 100, 1) : 0;
                                ?>
                                <a href="<?php echo htmlspecialchars($p['website_url']); ?>" target="_blank" rel="noopener" 
                                   class="trending-card" 
                                   title="View on website: <?php echo htmlspecialchars($p['product_name']); ?>">
                                    
                                    <!-- Rank Badge -->
                                    <span class="rank-badge <?php echo $rankClass; ?>">
                                        <?php echo $rank; ?>
                                    </span>

                                    <!-- Product Image -->
                                    <div class="product-img-wrap">
                                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($p['product_name']); ?>" 
                                             class="product-img"
                                             loading="lazy"
                                             onerror="this.src='https://srishringarr.com/static/images/default.jpg'">
                                        
                                        <!-- Hover Visit Site Button -->
                                        <div class="visit-site-btn">
                                            <span style="display: flex; align-items: center; justify-content: center; gap: 6px; background: rgba(9, 9, 11, 0.9); backdrop-filter: blur(4px); color: #ffffff; font-size: 11px; font-weight: 600; padding: 6px 10px; border-radius: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i> View on Storefront
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div style="padding: 12px; display: flex; flex-direction: column; flex: 1;">
                                        <h4 style="font-size: 12.5px; font-weight: 600; color: #09090b; line-height: 1.35; margin: 0 0 4px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 34px;">
                                            <?php echo htmlspecialchars($p['product_name']); ?>
                                        </h4>
                                        
                                        <?php if (!empty($p['product_sku'])): ?>
                                            <p style="font-size: 11px; font-family: monospace; color: #71717a; margin: 0 0 10px 0;">
                                                <?php echo htmlspecialchars($p['product_sku']); ?>
                                            </p>
                                        <?php else: ?>
                                            <div style="height: 15px; margin-bottom: 10px;"></div>
                                        <?php endif; ?>

                                        <!-- Mini Stats Row -->
                                        <div style="display: flex; align-items: center; gap: 10px; font-size: 11px; color: #71717a; margin-bottom: 8px; margin-top: auto;">
                                            <span style="display: inline-flex; align-items: center; gap: 4px;" title="Total Page Views">
                                                <i class="fa-solid fa-eye" style="color: #2563eb; font-size: 10px;"></i>
                                                <strong style="color: #09090b;"><?php echo number_format($p['view_count']); ?></strong>
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 4px;" title="Unique Visitors">
                                                <i class="fa-solid fa-user" style="color: #16a34a; font-size: 10px;"></i>
                                                <strong style="color: #09090b;"><?php echo number_format($p['unique_visitors']); ?></strong>
                                            </span>
                                            <span style="display: inline-flex; align-items: center; gap: 4px;" title="Cart Adds">
                                                <i class="fa-solid fa-cart-shopping" style="color: #db2777; font-size: 10px;"></i>
                                                <strong style="color: #09090b;"><?php echo number_format($p['cart_adds']); ?></strong>
                                            </span>
                                        </div>

                                        <!-- View-to-Cart Conversion Progress Bar -->
                                        <div style="width: 100%; background: #f4f4f5; border-radius: 999px; overflow: hidden; height: 4px; margin-bottom: 4px;">
                                            <div class="mini-bar" style="width: <?php echo min($convRate, 100); ?>%; background: <?php echo $convRate > 5 ? '#16a34a' : ($convRate > 0 ? '#d97706' : '#d4d4d8'); ?>;"></div>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 10px; color: #71717a;">
                                            <span>Cart Conv. Rate</span>
                                            <strong style="color: <?php echo $convRate > 5 ? '#16a34a' : ($convRate > 0 ? '#d97706' : '#71717a'); ?>;"><?php echo $convRate; ?>%</strong>
                                        </div>
                                    </div>

                                    <!-- Product Category / Type Pill -->
                                    <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                        <span style="font-size: 9px; font-weight: 600; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(4px); color: #09090b; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                            <?php echo htmlspecialchars($p['product_type']); ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Middle Row: Funnel + Trending Categories + Search Intent -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; margin-bottom: 28px;">
                        
                        <!-- 1. Conversion Funnel -->
                        <div class="card-surface" style="padding: 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                                <h3 style="font-size: 13.5px; font-weight: 600; color: #09090b; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-filter" style="color: #2563eb; font-size: 12px;"></i> Conversion Funnel
                                </h3>
                                <span class="shadcn-badge">TRAFFIC PIPELINE</span>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <?php 
                                $stages = [
                                    ['name' => 'Product Views', 'count' => $funnel['product_views'], 'color' => '#2563eb', 'icon' => 'fa-solid fa-eye'],
                                    ['name' => 'Cart Additions', 'count' => $funnel['cart_adds'], 'color' => '#8b5cf6', 'icon' => 'fa-solid fa-cart-shopping'],
                                    ['name' => 'Checkout Started', 'count' => $funnel['checkout_starts'], 'color' => '#d946ef', 'icon' => 'fa-solid fa-credit-card'],
                                    ['name' => 'Orders Placed', 'count' => $funnel['purchases'], 'color' => '#16a34a', 'icon' => 'fa-solid fa-circle-check']
                                ];
                                $maxCount = max(1, $funnel['product_views']);
                                foreach ($stages as $si => $stage): 
                                    $pct = round(($stage['count'] / $maxCount) * 100);
                                ?>
                                    <div>
                                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                                            <span style="color: #52525b; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                                                <i class="<?php echo $stage['icon']; ?>" style="font-size: 10px; color: <?php echo $stage['color']; ?>;"></i>
                                                <?php echo $stage['name']; ?>
                                            </span>
                                            <span style="font-weight: 600; color: #09090b;">
                                                <?php echo number_format($stage['count']); ?> 
                                                <span style="font-weight: 400; color: #a1a1aa; font-size: 11px;">(<?php echo $pct; ?>%)</span>
                                            </span>
                                        </div>
                                        <div style="width: 100%; background: #f4f4f5; height: 6px; border-radius: 999px; overflow: hidden;">
                                            <div style="height: 100%; border-radius: 999px; background: <?php echo $stage['color']; ?>; width: <?php echo $pct; ?>%; transition: width 0.6s ease;"></div>
                                        </div>
                                        <?php if ($si < count($stages) - 1): ?>
                                            <div style="display: flex; justify-content: center; margin: 4px 0 0 0;">
                                                <i class="fa-solid fa-chevron-down" style="font-size: 8px; color: #d4d4d8;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 2. Trending Categories -->
                        <div class="card-surface" style="padding: 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                                <h3 style="font-size: 13.5px; font-weight: 600; color: #09090b; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-tags" style="color: #16a34a; font-size: 12px;"></i> Trending Categories
                                </h3>
                                <span class="shadcn-badge">MOST VISITED</span>
                            </div>
                            
                            <?php if (empty($topCategories)): ?>
                                <p style="font-size: 12px; color: #71717a; text-align: center; padding: 32px 0;">No category views recorded yet.</p>
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <?php 
                                    $maxCatCount = max(1, $topCategories[0]['count']);
                                    foreach ($topCategories as $ci => $cat): 
                                        $catPct = round(($cat['count'] / $maxCatCount) * 100);
                                    ?>
                                        <a href="<?php echo htmlspecialchars($cat['website_url']); ?>" target="_blank" rel="noopener" 
                                           style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; border-radius: 6px; border: 1px solid #e4e4e7; background: #ffffff; text-decoration: none; transition: all 0.12s ease;"
                                           onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'" 
                                           onmouseout="this.style.background='#ffffff';this.style.borderColor='#e4e4e7'">
                                            <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                                <span style="width: 20px; height: 20px; border-radius: 4px; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 10px; font-weight: 700; flex-shrink: 0;">
                                                    <?php echo $ci + 1; ?>
                                                </span>
                                                <span style="font-size: 12.5px; font-weight: 500; color: #09090b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo htmlspecialchars($cat['label']); ?>
                                                </span>
                                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 8px; color: #a1a1aa; flex-shrink: 0;"></i>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 8px;">
                                                <div style="width: 48px; background: #f4f4f5; height: 4px; border-radius: 999px; overflow: hidden;">
                                                    <div style="background: #16a34a; height: 100%; border-radius: 999px; width: <?php echo $catPct; ?>%;"></div>
                                                </div>
                                                <span class="shadcn-badge" style="font-weight: 600;">
                                                    <?php echo number_format($cat['count']); ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 3. Top Search Intent -->
                        <div class="card-surface" style="padding: 18px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                                <h3 style="font-size: 13.5px; font-weight: 600; color: #09090b; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-magnifying-glass" style="color: #d97706; font-size: 12px;"></i> Search Intent
                                </h3>
                                <span class="shadcn-badge">USER QUERIES</span>
                            </div>

                            <?php if (empty($topSearches)): ?>
                                <p style="font-size: 12px; color: #71717a; text-align: center; padding: 32px 0;">No search queries logged yet.</p>
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <?php 
                                    $maxSearchCount = max(1, $topSearches[0]['search_count']);
                                    foreach ($topSearches as $si => $s): 
                                        $sPct = round(($s['search_count'] / $maxSearchCount) * 100);
                                    ?>
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; border-radius: 6px; border: 1px solid #e4e4e7; background: #ffffff;">
                                            <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                                <span style="width: 20px; height: 20px; border-radius: 4px; background: #fffbeb; border: 1px solid #fde68a; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 10px; font-weight: 700; flex-shrink: 0;">
                                                    <?php echo $si + 1; ?>
                                                </span>
                                                <span style="font-size: 12.5px; font-weight: 500; color: #09090b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    &ldquo;<?php echo htmlspecialchars($s['query']); ?>&rdquo;
                                                </span>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 8px;">
                                                <div style="width: 48px; background: #f4f4f5; height: 4px; border-radius: 999px; overflow: hidden;">
                                                    <div style="background: #d97706; height: 100%; border-radius: 999px; width: <?php echo $sPct; ?>%;"></div>
                                                </div>
                                                <span class="shadcn-badge" style="font-weight: 600;">
                                                    <?php echo $s['search_count']; ?>&times;
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ============================================= -->
                    <!-- USER SESSION JOURNEYS (Accordion Timeline)     -->
                    <!-- ============================================= -->
                    <div class="card-surface" style="padding: 18px; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                            <h3 style="font-size: 14px; font-weight: 600; color: #09090b; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-route" style="color: #8b5cf6; font-size: 13px;"></i> User Session Journeys
                                <span style="font-size: 11px; color: #71717a; font-weight: 400;">(Recent 20 live sessions)</span>
                            </h3>
                            <span class="shadcn-badge">CLICK SESSION TO EXPAND</span>
                        </div>

                        <?php if (empty($sessions)): ?>
                            <div style="text-align: center; padding: 40px; color: #71717a; font-size: 13px;">
                                <i class="fa-solid fa-users-slash" style="font-size: 24px; color: #d4d4d8; margin-bottom: 8px;"></i>
                                <p style="margin: 0;">No visitor sessions recorded yet.</p>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach ($sessions as $i => $sess): ?>
                                    <div class="session-card">
                                        <!-- Session Header Button -->
                                        <button type="button" 
                                                onclick="toggleSession('sess-<?php echo $i; ?>', this)" 
                                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; text-align: left; background: transparent; border: none; cursor: pointer; transition: background 0.12s ease;"
                                                onmouseover="this.style.background='#f8fafc'" 
                                                onmouseout="this.style.background='transparent'">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 32px; height: 32px; border-radius: 6px; background: #f4f4f5; border: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #09090b;">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>
                                                <div>
                                                    <p style="font-size: 13px; font-weight: 600; color: #09090b; margin: 0; line-height: 1.2;">
                                                        Session #<?php echo substr($sess['session_id'], 0, 8); ?>&hellip;
                                                    </p>
                                                    <p style="font-size: 11px; color: #71717a; margin: 2px 0 0 0;">
                                                        <?php echo date('d M Y, h:i A', strtotime($sess['first_seen'])); ?>
                                                        &rarr; <?php echo date('h:i A', strtotime($sess['last_seen'])); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span class="shadcn-badge" style="font-weight: 600;">
                                                    <?php echo $sess['total_events']; ?> EVENTS
                                                </span>
                                                <i class="fa-solid fa-chevron-down chevron-icon" style="font-size: 10px; color: #71717a; transition: transform 0.2s ease; <?php echo $i === 0 ? 'transform: rotate(180deg);' : ''; ?>"></i>
                                            </div>
                                        </button>

                                        <!-- Session Timeline Details -->
                                        <div id="sess-<?php echo $i; ?>" style="<?php echo $i === 0 ? 'display: block;' : 'display: none;'; ?> padding: 4px 16px 16px 16px; border-top: 1px solid #f4f4f5;">
                                            <div class="timeline-line" style="padding-left: 36px; padding-top: 8px;">
                                                <?php foreach ($sess['events'] as $ev): 
                                                    $type = $ev['event_type'];
                                                    $path = $ev['page_path'];
                                                    $time = date('h:i:s A', strtotime($ev['created_at']));
                                                    
                                                    // Human label
                                                    $label = $path;
                                                    if ($type === 'product_view' && $ev['target_id']) {
                                                        $slug = basename($path);
                                                        $slug = preg_replace('/-\d+$/', '', $slug);
                                                        $label = ucwords(str_replace('-', ' ', $slug));
                                                        $label = "Viewed: $label (ID #{$ev['target_id']})";
                                                    } elseif ($type === 'category_view') {
                                                        $parts = array_filter(explode('/', trim($path, '/')));
                                                        $label = 'Browsed: ' . ucwords(implode(' → ', array_map(function($p) { return str_replace('-', ' ', $p); }, $parts)));
                                                    } elseif ($type === 'shop_view') {
                                                        $parsed = parse_url($path);
                                                        if (isset($parsed['query'])) {
                                                            parse_str($parsed['query'], $qp);
                                                            if (!empty($qp['q'])) {
                                                                $label = 'Searched: "' . $qp['q'] . '"';
                                                            } else {
                                                                $label = 'Browsed shop catalog';
                                                            }
                                                        } else {
                                                            $label = 'Browsed shop catalog';
                                                        }
                                                    } elseif ($type === 'cart_add') {
                                                        $label = 'Added item to cart';
                                                    } elseif ($type === 'cart_view') {
                                                        $label = 'Viewed shopping cart';
                                                    } elseif ($type === 'checkout_start') {
                                                        $label = 'Initiated checkout';
                                                    } elseif ($type === 'page_view') {
                                                        $cleanPath = trim($path, '/');
                                                        $label = 'Visited: /' . ($cleanPath ?: 'home');
                                                    }
                                                ?>
                                                    <div style="position: relative; padding: 7px 0;">
                                                        <span class="timeline-dot dot-<?php echo $type; ?>"></span>
                                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                                                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                                <span class="event-badge badge-<?php echo $type; ?>">
                                                                    <?php echo str_replace('_', ' ', $type); ?>
                                                                </span>
                                                                <?php if ($type === 'product_view' && !empty($ev['website_url'])): ?>
                                                                    <a href="<?php echo htmlspecialchars($ev['website_url']); ?>" target="_blank" rel="noopener" 
                                                                       style="font-size: 12.5px; color: #2563eb; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;"
                                                                       onmouseover="this.style.textDecoration='underline'" 
                                                                       onmouseout="this.style.textDecoration='none'">
                                                                        <?php echo htmlspecialchars($label); ?>
                                                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 8px;"></i>
                                                                    </a>
                                                                    <?php if (!empty($ev['product_sku'])): ?>
                                                                        <span style="font-size: 10px; font-family: monospace; background: #f4f4f5; border: 1px solid #e4e4e7; padding: 1px 6px; border-radius: 4px; color: #52525b;">
                                                                            <?php echo htmlspecialchars($ev['product_sku']); ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <span style="font-size: 12.5px; color: #334155;">
                                                                        <?php echo htmlspecialchars($label); ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <span style="font-size: 11px; color: #a1a1aa; white-space: nowrap;">
                                                                <?php echo $time; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
    function toggleSession(id, btn) {
        const el = document.getElementById(id);
        const icon = btn.querySelector('.chevron-icon');
        if (!el) return;
        
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            el.style.display = 'none';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }
    </script>
</body>
</html>
