<!DOCTYPE html>
<html lang="en">
<head>
    <title>Analytics Dashboard - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
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
        .session-card {
            transition: all 0.2s;
        }
        .session-card:hover {
            border-color: #3f3f46;
        }
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
    </style>
</head>
<body class="bg-zinc-950 font-sans text-zinc-300 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'User Analytics';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-6xl mx-auto">
                    <!-- Top stats cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                        <!-- Card 1: Unique Sessions -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 hover:border-zinc-800 transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Unique Sessions</p>
                                    <h3 class="text-2xl font-semibold text-white mt-1.5"><?php echo number_format($totalSessions); ?></h3>
                                </div>
                                <span class="p-2 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 text-xs">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 2: Page Views -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 hover:border-zinc-800 transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Total Page Views</p>
                                    <h3 class="text-2xl font-semibold text-white mt-1.5"><?php echo number_format($totalPageViews); ?></h3>
                                </div>
                                <span class="p-2 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-400 text-xs">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 3: Product Detail Views -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 hover:border-zinc-800 transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Product Detail Views</p>
                                    <h3 class="text-2xl font-semibold text-white mt-1.5"><?php echo number_format($totalProductViews); ?></h3>
                                </div>
                                <span class="p-2 bg-amber-500/10 border border-amber-500/20 rounded-lg text-amber-400 text-xs">
                                    <i class="fas fa-gem"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Conversion Funnel Card -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 lg:col-span-2">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-filter text-indigo-400 mr-2 text-xs"></i>
                                Checkout Conversion Funnel
                            </h3>
                            
                            <div class="space-y-4">
                                <?php 
                                $stages = [
                                    ['name' => '1. Product Views', 'count' => $funnel['product_views'], 'color' => 'bg-indigo-500'],
                                    ['name' => '2. Cart Additions', 'count' => $funnel['cart_adds'], 'color' => 'bg-violet-500'],
                                    ['name' => '3. Checkout Started', 'count' => $funnel['checkout_starts'], 'color' => 'bg-purple-500'],
                                    ['name' => '4. Orders Placed', 'count' => $funnel['purchases'], 'color' => 'bg-emerald-500']
                                ];
                                $maxCount = max(1, $funnel['product_views']);
                                foreach ($stages as $stage): 
                                    $pct = round(($stage['count'] / $maxCount) * 100);
                                ?>
                                    <div>
                                        <div class="flex justify-between text-xs font-medium mb-1">
                                            <span class="text-zinc-400"><?php echo $stage['name']; ?></span>
                                            <span class="text-white"><?php echo number_format($stage['count']); ?> (<?php echo $pct; ?>%)</span>
                                        </div>
                                        <div class="w-full bg-zinc-900 h-2 rounded-full overflow-hidden border border-zinc-900">
                                            <div class="<?php echo $stage['color']; ?> h-full" style="width: <?php echo $pct; ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Top Categories List -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-tags text-emerald-400 mr-2 text-xs"></i>
                                Popular Categories
                            </h3>
                            
                            <?php if (empty($topCategories)): ?>
                                <p class="text-xs text-zinc-550 py-8 text-center">No category filter events logged yet.</p>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php foreach ($topCategories as $cat): ?>
                                        <div class="flex justify-between items-center py-2 border-b border-zinc-900 last:border-0 text-xs">
                                            <span class="text-zinc-300 font-medium"><?php echo htmlspecialchars($cat['label']); ?></span>
                                            <span class="px-2 py-0.5 bg-zinc-900 border border-zinc-800 text-zinc-400 rounded-md font-semibold text-[10px]"><?php echo $cat['count']; ?> views</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Top Searches Table -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 overflow-hidden">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-search text-amber-400 mr-2 text-xs animate-pulse"></i>
                                Top Customer Search Intent
                            </h3>

                            <?php if (empty($topSearches)): ?>
                                <p class="text-xs text-zinc-550 py-8 text-center">No customer search queries logged yet.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b border-zinc-900 text-zinc-500">
                                                <th class="pb-3 font-semibold uppercase tracking-wider">Search Keyword</th>
                                                <th class="pb-3 font-semibold uppercase tracking-wider text-right">Searched</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900">
                                            <?php foreach ($topSearches as $s): ?>
                                                <tr>
                                                    <td class="py-3 font-medium text-white"><?php echo htmlspecialchars($s['query']); ?></td>
                                                    <td class="py-3 text-right text-zinc-400 font-semibold"><?php echo $s['search_count']; ?> times</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Top Products Table -->
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 overflow-hidden">
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-5 flex items-center">
                                <i class="fas fa-trophy text-yellow-400 mr-2 text-xs"></i>
                                Most Popular Products
                            </h3>

                            <?php if (empty($topProducts)): ?>
                                <p class="text-xs text-zinc-550 py-8 text-center">No product views logged yet.</p>
                            <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b border-zinc-900 text-zinc-500">
                                                <th class="pb-3 font-semibold uppercase tracking-wider">Product Name / ID</th>
                                                <th class="pb-3 font-semibold uppercase tracking-wider text-center">Type</th>
                                                <th class="pb-3 font-semibold uppercase tracking-wider text-right">Views</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900">
                                            <?php foreach ($topProducts as $p): ?>
                                                <tr>
                                                    <td class="py-3">
                                                        <a href="index.php?controller=product&action=view_details&id=<?php echo $p['product_id']; ?>&type=<?php echo $p['product_type']; ?>" class="text-white hover:text-indigo-400 font-medium transition-colors">
                                                            <?php echo htmlspecialchars($p['product_name']); ?>
                                                        </a>
                                                        <span class="block text-[10px] text-zinc-500 mt-0.5">ID: <?php echo $p['product_id']; ?></span>
                                                    </td>
                                                    <td class="py-3 text-center capitalize">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-zinc-900 text-zinc-500 border border-zinc-800">
                                                            <?php echo htmlspecialchars($p['product_type']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="py-3 text-right text-zinc-400 font-semibold"><?php echo htmlspecialchars($p['view_count'] ?? 0); ?> views</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- User Session Activity Timeline -->
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

                                        <!-- Session Timeline (collapsible) -->
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
