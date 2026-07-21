<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>AI Analytics - Srishringarr Studio</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #050505; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-black text-white font-sans antialiased overflow-hidden selection:bg-indigo-500/30">
    <div class="flex h-screen w-full">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#050505]">
            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between px-6 border-b border-white/5 bg-[#0a0a0a] shrink-0">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-bold text-white tracking-wide">AI Analytics & Usage</h1>
                    <span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold rounded uppercase tracking-wider border border-indigo-500/20">Cost Dashboard</span>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col overflow-y-auto custom-scrollbar gap-6">
                
                <!-- Metrics Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 shrink-0">
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Total API Calls</div>
                        <div class="text-3xl font-bold text-white"><?php echo number_format($image_totals['total_generations'] ?? 0); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Images Generated</div>
                        <div class="text-3xl font-bold text-pink-400"><?php echo number_format($image_totals['total_images'] ?? 0); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Tokens Used (Gemini)</div>
                        <div class="text-3xl font-bold text-emerald-400"><?php echo number_format($image_totals['total_tokens'] ?? 0); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Est. Total Cost</div>
                        <div class="text-3xl font-bold text-amber-400">$<?php echo number_format($image_totals['total_cost'] ?? 0, 4); ?></div>
                    </div>
                </div>

                <!-- Image Generation Logs -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col overflow-hidden shadow-2xl shrink-0" style="max-height: 400px;">
                    <div class="px-6 py-4 border-b border-white/5 bg-[#111]">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Image Generation Cost Log</h2>
                    </div>
                    <?php if (empty($image_logs)): ?>
                        <div class="flex-1 flex flex-col items-center justify-center py-10">
                            <i class="fas fa-image text-3xl text-zinc-700 mb-3"></i>
                            <h2 class="text-md font-medium text-zinc-400">No images generated yet</h2>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-[#111] sticky top-0 z-10 border-b border-white/5">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Date</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Product</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Images</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Prompt</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Tokens</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <?php foreach ($image_logs as $log): ?>
                                        <tr class="hover:bg-white/[0.02] transition-colors">
                                            <td class="px-6 py-3 text-xs text-zinc-300"><?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?></td>
                                            <td class="px-6 py-3 text-xs text-zinc-300">ID: <?php echo htmlspecialchars($log['product_id']); ?> (<?php echo htmlspecialchars($log['product_type']); ?>)</td>
                                            <td class="px-6 py-3 text-xs font-bold text-pink-400"><?php echo htmlspecialchars($log['num_images']); ?></td>
                                            <td class="px-6 py-3 text-xs text-zinc-500 max-w-xs truncate" title="<?php echo htmlspecialchars($log['prompt_text']); ?>"><?php echo htmlspecialchars($log['prompt_text']); ?></td>
                                            <td class="px-6 py-3 text-xs text-emerald-400"><?php echo number_format($log['total_tokens']); ?></td>
                                            <td class="px-6 py-3 text-xs font-mono text-amber-400">$<?php echo number_format($log['cost_estimate'], 5); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Playground History -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col overflow-hidden shadow-2xl shrink-0" style="max-height: 400px;">
                    <div class="px-6 py-4 border-b border-white/5 bg-[#111]">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Playground History</h2>
                    </div>
                    
                    <?php if (empty($sessions)): ?>
                        <div class="flex-1 flex flex-col items-center justify-center py-20">
                            <i class="fas fa-database text-4xl text-zinc-700 mb-4"></i>
                            <h2 class="text-lg font-medium text-zinc-400">No AI Generations Yet</h2>
                            <p class="text-sm text-zinc-500 mt-2">Go to the AI Playground to generate some product content!</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-[#111] sticky top-0 z-10 border-b border-white/5">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold text-zinc-400">Date & Session</th>
                                        <th class="px-6 py-4 font-semibold text-zinc-400">Context</th>
                                        <th class="px-6 py-4 font-semibold text-zinc-400">Generated Names</th>
                                        <th class="px-6 py-4 font-semibold text-zinc-400">Generated Description</th>
                                        <th class="px-6 py-4 font-semibold text-zinc-400">Generated Images</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
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
                                        <tr class="hover:bg-white/[0.02] transition-colors group">
                                            
                                            <!-- Date & Session -->
                                            <td class="px-6 py-4 align-top w-48">
                                                <div class="font-medium text-white mb-1"><?php echo date('M j, Y', strtotime($session['session_date'])); ?></div>
                                                <div class="text-xs text-zinc-500 mb-2"><?php echo date('g:i A', strtotime($session['session_date'])); ?></div>
                                                <div class="text-[10px] text-zinc-600 font-mono truncate max-w-[120px]" title="<?php echo htmlspecialchars($session['session_id']); ?>">
                                                    <?php echo htmlspecialchars($session['session_id']); ?>
                                                </div>
                                            </td>
                                            
                                            <!-- Context -->
                                            <td class="px-6 py-4 align-top max-w-xs whitespace-normal">
                                                <div class="text-xs text-zinc-300">
                                                    <span class="text-zinc-500">Size:</span> <?php echo htmlspecialchars($session['size'] ?: 'N/A'); ?>
                                                </div>
                                                <div class="text-xs text-zinc-400 mt-1 line-clamp-3" title="<?php echo htmlspecialchars($session['details']); ?>">
                                                    <?php echo htmlspecialchars($session['details'] ?: 'No details'); ?>
                                                </div>
                                            </td>
                                            
                                            <!-- Names -->
                                            <td class="px-6 py-4 align-top max-w-sm whitespace-normal">
                                                <?php if (empty($names)): ?>
                                                    <span class="text-zinc-600 italic text-xs">None</span>
                                                <?php else: ?>
                                                    <ul class="list-disc list-inside text-xs text-zinc-300 space-y-1">
                                                        <?php foreach ($names as $name): ?>
                                                            <li class="line-clamp-2" title="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Description -->
                                            <td class="px-6 py-4 align-top max-w-sm whitespace-normal">
                                                <?php if (empty($descriptions)): ?>
                                                    <span class="text-zinc-600 italic text-xs">None</span>
                                                <?php else: ?>
                                                    <div class="text-xs text-zinc-300 space-y-2">
                                                        <?php foreach ($descriptions as $desc): ?>
                                                            <div class="line-clamp-4 bg-black/40 p-2 rounded border border-white/5" title="<?php echo htmlspecialchars($desc); ?>">
                                                                <?php echo htmlspecialchars($desc); ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Images / Videos -->
                                            <td class="px-6 py-4 align-top w-64 whitespace-normal">
                                                <?php if (empty($media)): ?>
                                                    <span class="text-zinc-600 italic text-xs">None</span>
                                                <?php else: ?>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <?php foreach ($media as $m): ?>
                                                            <?php if ($m['type'] === 'image'): ?>
                                                                <a href="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" target="_blank" class="block rounded overflow-hidden aspect-[3/4] border border-white/10 hover:border-indigo-500 transition-colors">
                                                                    <img src="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" class="w-full h-full object-cover">
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" target="_blank" class="block rounded overflow-hidden aspect-[9/16] border border-white/10 hover:border-teal-500 transition-colors relative group">
                                                                    <video src="/ss/yn/<?php echo htmlspecialchars($m['url']); ?>" class="w-full h-full object-cover" muted></video>
                                                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                                        <i class="fas fa-play text-white text-xl"></i>
                                                                    </div>
                                                                    <div class="absolute top-1 left-1 bg-black/60 px-1.5 py-0.5 rounded text-[8px] text-teal-400 font-bold border border-teal-500/30">
                                                                        VIDEO
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
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
