<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Analytics & Cost Monitoring - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Slate / Zinc Theme) */
        :root {
            --wp-dark: #09090b;
            --wp-border: #e4e4e7;
            --wp-bg: #fafafa;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: #fafafa !important;
            color: #09090b !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* Metric Cards */
        .shadcn-stat-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 14px 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.15s ease;
        }
        .shadcn-stat-card:hover {
            border-color: #d4d4d8;
        }

        /* ShadCN Card Container */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .shadcn-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ShadCN Buttons */
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
            text-align: center;
            cursor: pointer;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #09090b;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.12s ease;
            text-decoration: none;
            font-family: inherit;
            white-space: nowrap;
        }
        .shadcn-btn:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        .shadcn-btn-sm {
            height: 28px;
            padding: 0 9px;
            font-size: 11.5px;
            border-radius: 5px;
        }

        .shadcn-btn-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .shadcn-btn-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        /* Neutral Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
            flex-shrink: 0;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-dot-success { background-color: #10b981; }
        .status-dot-warning { background-color: #f59e0b; }
        .status-dot-neutral { background-color: #94a3b8; }
        .status-dot-danger { background-color: #ef4444; }

        /* Search input */
        .search-input-wrap {
            position: relative;
            width: 100%;
            max-width: 280px;
        }
        .search-input-wrap i.search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1aa;
            font-size: 12px;
            pointer-events: none;
        }
        .search-input-wrap input {
            width: 100%;
            height: 32px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 10px 0 30px;
            font-size: 12px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease;
        }
        .search-input-wrap input:focus {
            border-color: #09090b;
        }

        /* ShadCN Table Standard */
        .shadcn-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .shadcn-table th {
            background: #fafafa;
            padding: 9px 16px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
            border-bottom: 1px solid #e4e4e7;
            text-align: left;
            white-space: nowrap;
        }
        .shadcn-table td {
            padding: 10px 16px;
            vertical-align: top;
            border-bottom: 1px solid #f4f4f5;
            font-size: 12.5px;
            color: #09090b;
        }
        .shadcn-table tbody tr:hover td {
            background-color: #fbfbfb;
        }

        /* Scrollbars */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <?php 
        $formatIstDate = function($dateStr, $format = 'M j, Y g:i A') {
            if (empty($dateStr)) return '';
            try {
                $dt = new DateTime($dateStr, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                return $dt->format($format);
            } catch (\Exception $e) {
                return date($format, strtotime($dateStr));
            }
        };
    ?>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'AI Analytics & Cost Monitoring';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="page-container">

                    <!-- Header Banner -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-semibold text-zinc-900 tracking-tight">AI Analytics & Usage</h1>
                                <span class="shadcn-badge font-mono text-[11px]">
                                    <span class="status-dot status-dot-success mr-1"></span>
                                    Cost & Token Telemetry
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-1">Audit AI token consumption, photoshoot vision analysis expenditure, estimated costs, and historical generation cycles.</p>
                        </div>

                        <!-- Top Action Buttons -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="index.php?controller=ai_playground" class="shadcn-btn">
                                <i class="fas fa-flask text-zinc-400 text-xs"></i>
                                <span>AI Playground</span>
                            </a>
                            <a href="index.php?controller=product&action=bulkAiWriter" class="shadcn-btn shadcn-btn-primary">
                                <i class="fas fa-wand-magic-sparkles text-xs"></i>
                                <span>Bulk AI Content Writer</span>
                            </a>
                        </div>
                    </div>

                    <!-- Metrics Overview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total API Calls</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono"><?php echo number_format($image_totals['total_generations'] ?? 0); ?></span>
                                    <span class="text-xs text-zinc-400">operations</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Images Generated</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono"><?php echo number_format($image_totals['total_images'] ?? 0); ?></span>
                                    <span class="text-xs text-zinc-400">renders</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Tokens Consumed</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono"><?php echo number_format($image_totals['total_tokens'] ?? 0); ?></span>
                                    <span class="text-xs text-zinc-400">tokens</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-microchip"></i>
                            </div>
                        </div>

                        <div class="shadcn-stat-card">
                            <div>
                                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Estimated Total Cost</span>
                                <div class="flex items-baseline gap-1 mt-1">
                                    <span class="text-xl font-semibold text-zinc-900 font-mono">₹<?php echo number_format($image_totals['total_cost'] ?? 0, 3); ?></span>
                                    <span class="text-xs text-zinc-400 font-sans">INR</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-md bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-600 text-xs">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>

                    <!-- AI Generation Cost & Activity Log Card -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div>
                                <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-list-alt text-zinc-400 text-xs"></i>
                                    <span>AI Generation Requests & Activity Log</span>
                                </h2>
                                <span class="text-xs text-zinc-400" id="activityCountLabel">Showing last <?php echo count($image_logs); ?> generation transactions</span>
                            </div>

                            <!-- Live Filter Input -->
                            <div class="search-input-wrap">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="aiLogSearch" placeholder="Filter by SKU, website, type, or prompt..." autocomplete="off">
                            </div>
                        </div>

                        <?php if (empty($image_logs)): ?>
                            <div class="py-16 text-center text-zinc-400 text-xs">
                                <i class="fas fa-magic text-3xl text-zinc-300 mb-2 block"></i>
                                No AI generation calls recorded yet.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                                <table class="shadcn-table" id="aiLogsTable">
                                    <thead class="sticky top-0 z-10">
                                        <tr>
                                            <th style="width: 120px;">Website</th>
                                            <th style="width: 160px;">Date & Time</th>
                                            <th style="width: 160px;">Product SKU</th>
                                            <th style="width: 110px;">Operation</th>
                                            <th style="width: 240px;">Prompt / Context</th>
                                            <th>Generated Output</th>
                                            <th style="width: 100px; text-align: right;">Tokens</th>
                                            <th style="width: 110px; text-align: right;">Cost (INR)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="aiLogsTableBody">
                                        <?php foreach ($image_logs as $log): ?>
                                            <?php 
                                                $site = strtolower($log['website'] ?? 'srishringarr');
                                                $isYn = ($site === 'yosshitaneha' || $site === 'yn');
                                                $siteLabel = $isYn ? 'yosshitaneha' : 'srishringarr';
                                                $editUrl = $isYn 
                                                    ? "/yn/admin/product-edit.php?id=" . urlencode($log['product_id'])
                                                    : "index.php?controller=product&action=edit&id=" . urlencode($log['product_id']) . "&type=" . urlencode($log['product_type']);

                                                $op = strtolower($log['operation_type'] ?? 'image');
                                                if ($op === 'title' || $op === 'names') {
                                                    $opLabel = 'TITLE';
                                                } elseif ($op === 'description') {
                                                    $opLabel = 'DESCRIPTION';
                                                } elseif ($op === 'bulk_content') {
                                                    $opLabel = 'BULK VISION';
                                                } else {
                                                    $opLabel = 'IMAGE (' . ($log['num_images'] ?? 1) . ')';
                                                }

                                                $outputDisplay = $log['generated_output'] ?? '';
                                                if (!empty($outputDisplay) && str_starts_with($outputDisplay, '[')) {
                                                    $decodedArr = json_decode($outputDisplay, true);
                                                    if (is_array($decodedArr)) {
                                                        $outputDisplay = implode(' | ', $decodedArr);
                                                    }
                                                } elseif (!empty($outputDisplay) && str_starts_with($outputDisplay, '{')) {
                                                    $decodedObj = json_decode($outputDisplay, true);
                                                    if (is_array($decodedObj)) {
                                                        $outputDisplay = ($decodedObj['name'] ?? '') . ' - ' . ($decodedObj['short_description'] ?? '');
                                                    }
                                                }
                                                if (empty($outputDisplay) && $op === 'image') {
                                                    $outputDisplay = ($log['num_images'] ?? 1) . " model image(s) generated";
                                                }

                                                $skuDisplay = !empty($log['product_sku']) ? $log['product_sku'] : ('ID: ' . $log['product_id']);
                                                $searchBlob = strtolower($siteLabel . ' ' . $skuDisplay . ' ' . $opLabel . ' ' . ($log['prompt_text'] ?? '') . ' ' . $outputDisplay);
                                            ?>
                                            <tr class="ai-log-row" data-search="<?php echo htmlspecialchars($searchBlob); ?>">
                                                <td>
                                                    <span class="shadcn-badge font-mono text-[10px]">
                                                        <?php echo htmlspecialchars($siteLabel); ?>
                                                    </span>
                                                </td>
                                                <td class="font-mono text-xs text-zinc-500 whitespace-nowrap">
                                                    <?php echo $formatIstDate($log['created_at']); ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo $editUrl; ?>" target="_blank" class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-zinc-900 hover:underline" title="Click to inspect product">
                                                        <span><?php echo htmlspecialchars($skuDisplay); ?></span>
                                                        <i class="fas fa-arrow-up-right-from-square text-[9px] text-zinc-400"></i>
                                                    </a>
                                                    <span class="block text-[10px] text-zinc-400 capitalize"><?php echo htmlspecialchars($log['product_type']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="shadcn-badge font-mono text-[10px]">
                                                        <?php echo htmlspecialchars($opLabel); ?>
                                                    </span>
                                                </td>
                                                <td class="text-xs text-zinc-500 max-w-xs truncate" title="<?php echo htmlspecialchars($log['prompt_text']); ?>">
                                                    <?php echo htmlspecialchars($log['prompt_text']); ?>
                                                </td>
                                                <td class="text-xs text-zinc-700 max-w-md truncate font-mono" title="<?php echo htmlspecialchars($outputDisplay); ?>">
                                                    <?php echo htmlspecialchars($outputDisplay); ?>
                                                </td>
                                                <td class="text-right font-mono text-xs text-zinc-600">
                                                    <?php echo number_format($log['total_tokens']); ?>
                                                </td>
                                                <td class="text-right font-mono text-xs font-semibold text-zinc-900">
                                                    ₹<?php echo number_format($log['cost_estimate'], 4); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Playground History Card -->
                    <div class="shadcn-card mb-6">
                        <div class="shadcn-card-header">
                            <div>
                                <h2 class="text-xs font-semibold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-flask text-zinc-400 text-xs"></i>
                                    <span>Playground Generation Sessions</span>
                                </h2>
                                <span class="text-xs text-zinc-400">Saved sessions and contextual generations</span>
                            </div>
                        </div>

                        <?php if (empty($sessions)): ?>
                            <div class="py-16 text-center text-zinc-400 text-xs">
                                <i class="fas fa-database text-3xl text-zinc-300 mb-2 block"></i>
                                No AI Playground sessions recorded yet.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto max-h-[460px] overflow-y-auto custom-scrollbar">
                                <table class="shadcn-table">
                                    <thead class="sticky top-0 z-10">
                                        <tr>
                                            <th style="width: 170px;">Date & Session</th>
                                            <th style="width: 220px;">Context</th>
                                            <th style="width: 260px;">Generated Titles</th>
                                            <th>Generated Descriptions</th>
                                            <th style="width: 200px;">Generated Media</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sessions as $session): ?>
                                            <?php
                                                $names = [];
                                                $descriptions = [];
                                                $media = [];
                                                
                                                foreach ($session['items'] as $item) {
                                                    if ($item['type'] === 'names') {
                                                        $names = array_merge($names, is_array($item['generated_data']) ? $item['generated_data'] : []);
                                                    } elseif ($item['type'] === 'description') {
                                                        $descriptions[] = $item['generated_data'];
                                                    } elseif ($item['type'] === 'image' || $item['type'] === 'video') {
                                                        $media[] = [
                                                            'type' => $item['type'],
                                                            'url' => $item['generated_data']
                                                        ];
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <!-- Date & Session -->
                                                <td>
                                                    <div class="font-semibold text-zinc-900 text-xs"><?php echo $formatIstDate($session['session_date'], 'M j, Y'); ?></div>
                                                    <div class="text-[11px] text-zinc-400 mb-1"><?php echo $formatIstDate($session['session_date'], 'g:i A'); ?></div>
                                                    <div class="text-[10px] text-zinc-400 font-mono truncate max-w-[140px]" title="<?php echo htmlspecialchars($session['session_id']); ?>">
                                                        #<?php echo htmlspecialchars($session['session_id']); ?>
                                                    </div>
                                                </td>
                                                
                                                <!-- Context -->
                                                <td class="max-w-xs">
                                                    <div class="text-xs text-zinc-700">
                                                        <span class="text-zinc-400">Context:</span> <?php echo htmlspecialchars($session['size'] ?: 'Standard'); ?>
                                                    </div>
                                                    <div class="text-[11px] text-zinc-500 mt-1 line-clamp-2" title="<?php echo htmlspecialchars($session['details']); ?>">
                                                        <?php echo htmlspecialchars($session['details'] ?: 'No details recorded'); ?>
                                                    </div>
                                                </td>
                                                
                                                <!-- Names -->
                                                <td class="max-w-xs">
                                                    <?php if (empty($names)): ?>
                                                        <span class="text-zinc-400 italic text-xs">None</span>
                                                    <?php else: ?>
                                                        <ul class="list-disc list-inside text-xs text-zinc-700 space-y-1">
                                                            <?php foreach ($names as $name): ?>
                                                                <li class="line-clamp-2" title="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <!-- Description -->
                                                <td class="max-w-md">
                                                    <?php if (empty($descriptions)): ?>
                                                        <span class="text-zinc-400 italic text-xs">None</span>
                                                    <?php else: ?>
                                                        <div class="space-y-1.5 text-xs text-zinc-700">
                                                            <?php foreach ($descriptions as $desc): ?>
                                                                <div class="p-2 rounded bg-zinc-50 border border-zinc-200/80 text-zinc-800 line-clamp-3" title="<?php echo htmlspecialchars($desc); ?>">
                                                                    <?php echo htmlspecialchars($desc); ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <!-- Media -->
                                                <td>
                                                    <?php if (empty($media)): ?>
                                                        <span class="text-zinc-400 italic text-xs">None</span>
                                                    <?php else: ?>
                                                        <div class="grid grid-cols-2 gap-1.5">
                                                            <?php foreach ($media as $m): ?>
                                                                <?php if ($m['type'] === 'image'): ?>
                                                                    <a href="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" target="_blank" class="block rounded border border-zinc-200 overflow-hidden aspect-[3/4] hover:border-zinc-900 transition-colors">
                                                                        <img src="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" class="w-full h-full object-cover">
                                                                    </a>
                                                                <?php else: ?>
                                                                    <a href="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" target="_blank" class="block rounded border border-zinc-200 overflow-hidden aspect-[9/16] relative group">
                                                                        <video src="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" class="w-full h-full object-cover" muted></video>
                                                                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                                            <i class="fas fa-play text-white text-xs"></i>
                                                                        </div>
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        // Real-time filter for AI generation activity logs
        const aiLogSearch = document.getElementById('aiLogSearch');
        if (aiLogSearch) {
            aiLogSearch.addEventListener('input', function(e) {
                const term = e.target.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.ai-log-row');
                let visible = 0;
                rows.forEach(r => {
                    const blob = r.getAttribute('data-search') || '';
                    if (!term || blob.includes(term)) {
                        r.style.display = '';
                        visible++;
                    } else {
                        r.style.display = 'none';
                    }
                });
                const countLabel = document.getElementById('activityCountLabel');
                if (countLabel) {
                    countLabel.textContent = `Showing ${visible} of ${rows.length} generation transactions`;
                }
            });
        }
    </script>
</body>
</html>
