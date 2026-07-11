<!DOCTYPE html>
<html lang="en">
<head>
    <title>Analytics Dashboard - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
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

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                                <th class="pb-3 font-semibold uppercase tracking-wider text-center">Result Count</th>
                                                <th class="pb-3 font-semibold uppercase tracking-wider text-right">Searched</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-900">
                                            <?php foreach ($topSearches as $s): ?>
                                                <tr>
                                                    <td class="py-3 font-medium text-white"><?php echo htmlspecialchars($s['query']); ?></td>
                                                    <td class="py-3 text-center">
                                                        <?php if ($s['results_count'] == 0): ?>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 border border-red-500/20 text-red-400">
                                                                0 Results (Needs Inventory)
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-zinc-400 font-semibold"><?php echo $s['results_count']; ?> items</span>
                                                        <?php endif; ?>
                                                    </td>
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
                                                    <td class="py-3 text-right text-zinc-400 font-semibold"><?php echo $p['count']; ?> views</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
