<!DOCTYPE html>
<html lang="en">
<head>
    <title>Analytics Dashboard - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .trending-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .trending-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(99,102,241,0.06), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .trending-card:hover::before {
            opacity: 1;
        }
        .trending-card:hover {
            border-color: rgba(99,102,241,0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px -12px rgba(99,102,241,0.15);
        }
        .trending-card .rank-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
        }
        .trending-card .product-img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .trending-card:hover .product-img {
            transform: scale(1.05);
        }
        .trending-card .visit-site-btn {
            opacity: 0;
            transform: translateY(6px);
            transition: all 0.3s;
        }
        .trending-card:hover .visit-site-btn {
            opacity: 1;
            transform: translateY(0);
        }
        .rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #b45309, #92400e); }
        .rank-default { background: linear-gradient(135deg, #3f3f46, #27272a); }
        .timeline-line { position: relative; }
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #3f3f46, transparent);
        }
        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            position: absolute;
            left: 11px;
            top: 6px;
        }
        .session-card { transition: all 0.2s; }
        .session-card:hover { border-color: #3f3f46; }
        .event-badge {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        .badge-product_view { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.25); }
        .badge-shop_view { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .badge-category_view { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
        .badge-page_view { background: rgba(113,113,122,0.15); color: #a1a1aa; border: 1px solid rgba(113,113,122,0.25); }
        .badge-cart_add { background: rgba(236,72,153,0.15); color: #f472b6; border: 1px solid rgba(236,72,153,0.25); }
        .badge-cart_view { background: rgba(236,72,153,0.1); color: #f9a8d4; border: 1px solid rgba(236,72,153,0.2); }
        .badge-checkout_start { background: rgba(168,85,247,0.15); color: #c084fc; border: 1px solid rgba(168,85,247,0.25); }
        .badge-search { background: rgba(14,165,233,0.15); color: #38bdf8; border: 1px solid rgba(14,165,233,0.25); }
        .dot-product_view { background: #818cf8; }
        .dot-shop_view { background: #34d399; }
        .dot-category_view { background: #fbbf24; }
        .dot-page_view { background: #71717a; }
        .dot-cart_add, .dot-cart_view { background: #f472b6; }
        .dot-checkout_start { background: #c084fc; }
        .dot-search { background: #38bdf8; }
        .stat-card {
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        }
        .stat-card:hover {
            border-color: #3f3f46;
            transform: translateY(-1px);
        }
        .mini-bar {
            height: 4px;
            border-radius: 2px;
            transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
</head>
<body class="bg-zinc-950 font-sans text-zinc-300 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Analytics & Trending';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">

                    <!-- Date Filter Bar -->
                    <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-4 mb-6">
                        <form method="GET" action="index.php" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <input type="hidden" name="controller" value="analytics">
                            <input type="hidden" name="action" value="index">

                            <!-- Quick Preset Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold text-zinc-400 mr-1 flex items-center gap-1.5">
                                    <i class="fas fa-calendar-alt text-indigo-400"></i> Date Range:
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
                                       class="px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors <?php echo $isActive ? 'bg-indigo-600 border-indigo-500 text-white shadow-sm' : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-800'; ?>">
                                        <?php echo $label; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <!-- Custom Date Range -->
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="flex items-center gap-1.5 bg-zinc-900 border border-zinc-800 rounded-lg px-2 py-1">
                                    <span class="text-[10px] uppercase font-bold text-zinc-500">From</span>
                                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate ?? ''); ?>" 
                                           class="bg-transparent border-0 text-zinc-200 text-xs focus:outline-none focus:ring-0 p-0 cursor-pointer">
                                </div>
                                <div class="flex items-center gap-1.5 bg-zinc-900 border border-zinc-800 rounded-lg px-2 py-1">
                                    <span class="text-[10px] uppercase font-bold text-zinc-500">To</span>
                                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate ?? ''); ?>" 
                                           class="bg-transparent border-0 text-zinc-200 text-xs focus:outline-none focus:ring-0 p-0 cursor-pointer">
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-filter text-[10px]"></i> Apply
                                </button>
                                <?php if ($startDate || $endDate || $preset): ?>
                                    <a href="index.php?controller=analytics&action=index" class="bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-zinc-200 text-xs px-3 py-1.5 rounded-lg border border-zinc-800 transition-colors flex items-center gap-1">
                                        <i class="fas fa-times text-[10px]"></i> Reset
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if ($startDate || $endDate): ?>
                            <div class="mt-3 pt-3 border-t border-zinc-900 flex items-center gap-2 text-xs text-zinc-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Filtering: 
                                <span class="text-zinc-200 font-semibold">
                                    <?php echo $startDate ? date('d M Y', strtotime($startDate)) : 'Start'; ?> 
                                    &rarr; 
                                    <?php echo $endDate ? date('d M Y', strtotime($endDate)) : 'Today'; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Stats Overview Row -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
                        <?php
                        $statsCards = [
                            ['label' => 'Unique Visitors', 'value' => $totalSessions, 'icon' => 'fa-users', 'color' => 'indigo'],
                            ['label' => 'Total Page Views', 'value' => $totalPageViews, 'icon' => 'fa-eye', 'color' => 'emerald'],
                            ['label' => 'Product Views', 'value' => $totalProductViews, 'icon' => 'fa-gem', 'color' => 'amber'],
                            ['label' => 'Cart Adds', 'value' => $funnel['cart_adds'], 'icon' => 'fa-shopping-cart', 'color' => 'pink'],
                        ];
                        foreach ($statsCards as $sc):
                        ?>
                        <div class="stat-card bg-zinc-950 border border-zinc-900 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-7 h-7 rounded-lg bg-<?php echo $sc['color']; ?>-500/10 border border-<?php echo $sc['color']; ?>-500/20 flex items-center justify-center">
                                    <i class="fas <?php echo $sc['icon']; ?> text-<?php echo $sc['color']; ?>-400 text-[10px]"></i>
                                </span>
                                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider"><?php echo $sc['label']; ?></span>
                            </div>
                            <p class="text-xl font-bold text-white"><?php echo number_format($sc['value']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ============================================= -->
                    <!-- TRENDING PRODUCTS - Hero Section               -->
                    <!-- ============================================= -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center">
                                    <i class="fas fa-fire text-amber-400 text-sm"></i>
                                </span>
                                <div>
                                    <h2 class="text-base font-bold text-white tracking-tight">Trending Products</h2>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">Most viewed products by your customers — click to view on website</p>
                                </div>
                            </div>
                            <span class="text-[10px] uppercase font-bold text-zinc-600 tracking-wider">Top 10</span>
                        </div>

                        <?php if (empty($topProducts)): ?>
                            <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-12 text-center">
                                <i class="fas fa-chart-line text-zinc-800 text-3xl mb-3"></i>
                                <p class="text-xs text-zinc-500">No product views logged yet for the selected period.</p>
                            </div>
                        <?php else: ?>
                            <!-- Product Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                <?php foreach ($topProducts as $i => $p): 
                                    $rank = $i + 1;
                                    $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-default'));
                                    $convRate = $p['view_count'] > 0 ? round(($p['cart_adds'] / $p['view_count']) * 100, 1) : 0;
                                ?>
                                <a href="<?php echo htmlspecialchars($p['website_url']); ?>" target="_blank" rel="noopener" 
                                   class="trending-card bg-zinc-950 border border-zinc-900 rounded-xl block group" 
                                   style="animation-delay: <?php echo $i * 60; ?>ms"
                                   title="View on website: <?php echo htmlspecialchars($p['product_name']); ?>">
                                    
                                    <!-- Rank Badge -->
                                    <span class="rank-badge <?php echo $rankClass; ?> text-white text-[10px] font-extrabold w-6 h-6 rounded-lg flex items-center justify-center shadow-lg">
                                        <?php echo $rank; ?>
                                    </span>

                                    <!-- Product Image -->
                                    <div class="relative overflow-hidden rounded-t-xl bg-zinc-900">
                                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($p['product_name']); ?>" 
                                             class="product-img"
                                             loading="lazy"
                                             onerror="this.src='https://srishringarr.com/static/images/default.jpg'">
                                        
                                        <!-- Hover overlay button -->
                                        <div class="visit-site-btn absolute bottom-2 left-2 right-2">
                                            <span class="flex items-center justify-center gap-1.5 bg-indigo-600/90 backdrop-blur-sm text-white text-[10px] font-bold py-1.5 rounded-lg w-full">
                                                <i class="fas fa-external-link-alt text-[9px]"></i> View on Website
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <h4 class="text-xs font-semibold text-white leading-tight line-clamp-2 mb-1.5 group-hover:text-indigo-300 transition-colors">
                                            <?php echo htmlspecialchars($p['product_name']); ?>
                                        </h4>
                                        
                                        <?php if (!empty($p['product_sku'])): ?>
                                            <p class="text-[10px] text-zinc-600 font-mono mb-2"><?php echo htmlspecialchars($p['product_sku']); ?></p>
                                        <?php endif; ?>

                                        <!-- Mini Stats -->
                                        <div class="flex items-center gap-3 text-[10px] text-zinc-500 mb-2">
                                            <span class="flex items-center gap-1" title="Total Views">
                                                <i class="fas fa-eye text-indigo-400/60"></i>
                                                <span class="font-bold text-zinc-300"><?php echo number_format($p['view_count']); ?></span>
                                            </span>
                                            <span class="flex items-center gap-1" title="Unique Visitors">
                                                <i class="fas fa-user text-emerald-400/60"></i>
                                                <span class="font-bold text-zinc-300"><?php echo number_format($p['unique_visitors']); ?></span>
                                            </span>
                                            <span class="flex items-center gap-1" title="Added to Cart">
                                                <i class="fas fa-cart-plus text-pink-400/60"></i>
                                                <span class="font-bold text-zinc-300"><?php echo number_format($p['cart_adds']); ?></span>
                                            </span>
                                        </div>

                                        <!-- View-to-Cart Conversion Bar -->
                                        <div class="w-full bg-zinc-900 rounded-full overflow-hidden h-1 mb-1">
                                            <div class="mini-bar <?php echo $convRate > 5 ? 'bg-emerald-500' : ($convRate > 0 ? 'bg-amber-500' : 'bg-zinc-800'); ?>" 
                                                 style="width: <?php echo min($convRate, 100); ?>%"></div>
                                        </div>
                                        <div class="flex justify-between text-[9px]">
                                            <span class="text-zinc-600">View → Cart</span>
                                            <span class="font-bold <?php echo $convRate > 5 ? 'text-emerald-400' : ($convRate > 0 ? 'text-amber-400' : 'text-zinc-600'); ?>"><?php echo $convRate; ?>%</span>
                                        </div>
                                    </div>

                                    <!-- Type badge -->
                                    <div class="absolute top-3 right-3 z-10">
                                        <span class="text-[8px] font-bold uppercase px-1.5 py-0.5 rounded bg-black/50 backdrop-blur-sm text-zinc-300 border border-zinc-700/50">
                                            <?php echo htmlspecialchars($p['product_type']); ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Middle Row: Funnel + Categories + Searches -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
                        
                        <!-- Conversion Funnel -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 lg:col-span-1">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-filter text-indigo-400 mr-2 text-xs"></i>
                                Conversion Funnel
                            </h3>
                            
                            <div class="space-y-4">
                                <?php 
                                $stages = [
                                    ['name' => 'Product Views', 'count' => $funnel['product_views'], 'color' => 'bg-indigo-500', 'icon' => 'fa-eye'],
                                    ['name' => 'Cart Additions', 'count' => $funnel['cart_adds'], 'color' => 'bg-violet-500', 'icon' => 'fa-cart-plus'],
                                    ['name' => 'Checkout Started', 'count' => $funnel['checkout_starts'], 'color' => 'bg-purple-500', 'icon' => 'fa-credit-card'],
                                    ['name' => 'Orders Placed', 'count' => $funnel['purchases'], 'color' => 'bg-emerald-500', 'icon' => 'fa-check-circle']
                                ];
                                $maxCount = max(1, $funnel['product_views']);
                                foreach ($stages as $si => $stage): 
                                    $pct = round(($stage['count'] / $maxCount) * 100);
                                ?>
                                    <div>
                                        <div class="flex justify-between text-xs font-medium mb-1.5">
                                            <span class="text-zinc-400 flex items-center gap-1.5">
                                                <i class="fas <?php echo $stage['icon']; ?> text-[10px] opacity-50"></i>
                                                <?php echo $stage['name']; ?>
                                            </span>
                                            <span class="text-white font-bold"><?php echo number_format($stage['count']); ?> <span class="text-zinc-600 font-normal">(<?php echo $pct; ?>%)</span></span>
                                        </div>
                                        <div class="w-full bg-zinc-900 h-2 rounded-full overflow-hidden border border-zinc-900">
                                            <div class="<?php echo $stage['color']; ?> h-full rounded-full transition-all duration-700" style="width: <?php echo $pct; ?>%"></div>
                                        </div>
                                        <?php if ($si < count($stages) - 1): ?>
                                            <div class="flex justify-center my-1">
                                                <i class="fas fa-chevron-down text-zinc-800 text-[8px]"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Popular Categories -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-tags text-emerald-400 mr-2 text-xs"></i>
                                Trending Categories
                            </h3>
                            
                            <?php if (empty($topCategories)): ?>
                                <p class="text-xs text-zinc-550 py-8 text-center">No category views logged yet.</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php 
                                    $maxCatCount = max(1, $topCategories[0]['count']);
                                    foreach ($topCategories as $ci => $cat): 
                                        $catPct = round(($cat['count'] / $maxCatCount) * 100);
                                    ?>
                                        <a href="<?php echo htmlspecialchars($cat['website_url']); ?>" target="_blank" rel="noopener" 
                                           class="flex items-center justify-between py-2.5 px-3 rounded-lg border border-zinc-900 hover:border-zinc-700 hover:bg-zinc-900/50 transition-all group text-xs">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-5 h-5 rounded bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-[9px] font-bold flex-shrink-0">
                                                    <?php echo $ci + 1; ?>
                                                </span>
                                                <span class="text-zinc-300 font-medium truncate group-hover:text-emerald-300 transition-colors">
                                                    <?php echo htmlspecialchars($cat['label']); ?>
                                                </span>
                                                <i class="fas fa-external-link-alt text-zinc-700 text-[8px] group-hover:text-emerald-400/50 transition-colors"></i>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                <div class="w-12 bg-zinc-900 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-emerald-500/60 h-full rounded-full" style="width: <?php echo $catPct; ?>%"></div>
                                                </div>
                                                <span class="px-2 py-0.5 bg-zinc-900 border border-zinc-800 text-zinc-400 rounded-md font-bold text-[10px]">
                                                    <?php echo number_format($cat['count']); ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Top Search Intent -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-search text-amber-400 mr-2 text-xs"></i>
                                Search Intent
                            </h3>

                            <?php if (empty($topSearches)): ?>
                                <p class="text-xs text-zinc-550 py-8 text-center">No search queries logged yet.</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php 
                                    $maxSearchCount = max(1, $topSearches[0]['search_count']);
                                    foreach ($topSearches as $si => $s): 
                                        $sPct = round(($s['search_count'] / $maxSearchCount) * 100);
                                    ?>
                                        <div class="flex items-center justify-between py-2 px-3 rounded-lg border border-zinc-900 text-xs hover:border-zinc-800 transition-all">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-5 h-5 rounded bg-amber-500/10 flex items-center justify-center text-amber-400 text-[9px] font-bold flex-shrink-0">
                                                    <?php echo $si + 1; ?>
                                                </span>
                                                <span class="text-white font-medium truncate">"<?php echo htmlspecialchars($s['query']); ?>"</span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                                <div class="w-12 bg-zinc-900 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-amber-500/60 h-full rounded-full" style="width: <?php echo $sPct; ?>%"></div>
                                                </div>
                                                <span class="text-zinc-500 font-bold whitespace-nowrap"><?php echo $s['search_count']; ?>×</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- User Session Journeys -->
                    <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 mb-8">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                            <i class="fas fa-route text-violet-400 mr-2 text-xs"></i>
                            User Session Journeys
                            <span class="ml-2 text-[10px] text-zinc-500 font-normal normal-case">(Last 20 sessions)</span>
                        </h3>

                        <?php if (empty($sessions)): ?>
                            <p class="text-xs text-zinc-500 py-8 text-center">No sessions recorded yet.</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($sessions as $i => $sess): ?>
                                    <div class="session-card border border-zinc-900 rounded-lg overflow-hidden">
                                        <!-- Session Header -->
                                        <button onclick="document.getElementById('sess-<?php echo $i; ?>').classList.toggle('hidden')" 
                                                class="w-full flex items-center justify-between p-4 text-left hover:bg-zinc-900/50 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500/20 to-violet-500/20 border border-indigo-500/30 flex items-center justify-center text-[10px] font-bold text-indigo-400">
                                                    <i class="fas fa-user text-[10px]"></i>
                                                </span>
                                                <div>
                                                    <p class="text-xs font-semibold text-white">
                                                        Session #<?php echo substr($sess['session_id'], 0, 8); ?>…
                                                    </p>
                                                    <p class="text-[10px] text-zinc-500 mt-0.5">
                                                        <?php echo date('d M Y, h:i A', strtotime($sess['first_seen'])); ?>
                                                        → <?php echo date('h:i A', strtotime($sess['last_seen'])); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <span class="px-2 py-0.5 bg-zinc-900 border border-zinc-800 rounded text-[10px] font-bold text-zinc-400">
                                                    <?php echo $sess['total_events']; ?> events
                                                </span>
                                                <i class="fas fa-chevron-down text-zinc-600 text-[10px] transition-transform"></i>
                                            </div>
                                        </button>

                                        <!-- Session Timeline -->
                                        <div id="sess-<?php echo $i; ?>" class="<?php echo $i === 0 ? '' : 'hidden'; ?> px-4 pb-4">
                                            <div class="timeline-line pl-10 space-y-0">
                                                <?php foreach ($sess['events'] as $ev): 
                                                    $type = $ev['event_type'];
                                                    $path = $ev['page_path'];
                                                    $time = date('h:i:s A', strtotime($ev['created_at']));
                                                    
                                                    // Build human-readable label
                                                    $label = $path;
                                                    if ($type === 'product_view' && $ev['target_id']) {
                                                        $slug = basename($path);
                                                        $slug = preg_replace('/-\d+$/', '', $slug);
                                                        $label = ucwords(str_replace('-', ' ', $slug));
                                                        $label = "Viewed product: $label (ID: {$ev['target_id']})";
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
                                                                $label = 'Browsed shop';
                                                            }
                                                        } else {
                                                            $label = 'Browsed shop';
                                                        }
                                                    } elseif ($type === 'cart_add') {
                                                        $label = 'Added to cart';
                                                    } elseif ($type === 'cart_view') {
                                                        $label = 'Viewed cart';
                                                    } elseif ($type === 'checkout_start') {
                                                        $label = 'Started checkout';
                                                    } elseif ($type === 'page_view') {
                                                        $cleanPath = trim($path, '/');
                                                        $label = 'Visited: /' . ($cleanPath ?: 'home');
                                                    }
                                                ?>
                                                    <div class="relative py-2">
                                                        <span class="timeline-dot dot-<?php echo $type; ?>"></span>
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <span class="event-badge badge-<?php echo $type; ?>"><?php echo str_replace('_', ' ', $type); ?></span>
                                                                <span class="text-xs text-zinc-300 truncate"><?php echo htmlspecialchars($label); ?></span>
                                                            </div>
                                                            <span class="text-[10px] text-zinc-600 whitespace-nowrap flex-shrink-0"><?php echo $time; ?></span>
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

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
