<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>Product Sync - Srishringarr Studio</title>
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
                    <h1 class="text-lg font-bold text-white tracking-wide">Product Sync Manager</h1>
                    <span class="px-2 py-1 bg-teal-500/10 text-teal-400 text-[10px] font-bold rounded uppercase tracking-wider border border-teal-500/20">Parent &rarr; Child (Buy Store)</span>
                </div>
                <div>
                    <button id="btnBulkSync" onclick="startBulkSync()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                        <i class="fas fa-sync-alt" id="syncIcon"></i>
                        <span>Sync All Products to Yosshitaneha</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 p-6 flex flex-col overflow-y-auto custom-scrollbar gap-6">
                
                <!-- Status Banner -->
                <div id="syncStatusAlert" class="hidden p-4 rounded-xl border border-indigo-500/30 bg-indigo-500/10 text-indigo-300 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-spinner fa-spin text-indigo-400 text-lg" id="statusSpinner"></i>
                        <span id="syncStatusMsg">Synchronizing products... Please wait.</span>
                    </div>
                </div>

                <!-- Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 shrink-0">
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Total Sync Operations</div>
                        <div class="text-3xl font-bold text-white"><?php echo number_format($stats['total_synced']); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Successful Syncs</div>
                        <div class="text-3xl font-bold text-emerald-400"><?php echo number_format($stats['success_count']); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Failed Syncs</div>
                        <div class="text-3xl font-bold text-rose-400"><?php echo number_format($stats['failed_count']); ?></div>
                    </div>
                    <div class="bg-[#0a0a0a] border border-white/5 p-5 rounded-xl shadow-lg">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Last Sync Activity</div>
                        <div class="text-xl font-bold text-amber-400 mt-1"><?php echo htmlspecialchars($stats['last_sync']); ?></div>
                    </div>
                </div>

                <!-- Sync Audit Logs Table -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl flex flex-col overflow-hidden shadow-2xl shrink-0" style="max-height: 550px;">
                    <div class="px-6 py-4 border-b border-white/5 bg-[#111] flex items-center justify-between">
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider">Product Sync Audit Log</h2>
                        <span class="text-xs text-zinc-500">Showing last 100 entries</span>
                    </div>

                    <?php if (empty($logs)): ?>
                        <div class="flex-1 flex flex-col items-center justify-center py-16">
                            <i class="fas fa-exchange-alt text-4xl text-zinc-700 mb-3"></i>
                            <h2 class="text-md font-medium text-zinc-400">No sync logs found</h2>
                            <p class="text-xs text-zinc-600 mt-1">Product synchronization activity will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto overflow-y-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead class="bg-[#111] sticky top-0 z-10 border-b border-white/5">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Date & Time</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">SKU</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Type</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Trigger</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Status</th>
                                        <th class="px-6 py-3 font-semibold text-zinc-400 text-xs">Log Message</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="hover:bg-white/[0.02] transition-colors">
                                            <td class="px-6 py-3 text-xs text-zinc-400"><?php echo date('M j, Y g:i A', strtotime($log['synced_at'])); ?></td>
                                            <td class="px-6 py-3 text-xs font-mono font-bold text-indigo-400"><?php echo htmlspecialchars($log['sku']); ?></td>
                                            <td class="px-6 py-3 text-xs text-zinc-300 capitalize"><?php echo htmlspecialchars($log['product_type']); ?></td>
                                            <td class="px-6 py-3 text-xs">
                                                <?php if ($log['sync_mode'] === 'auto'): ?>
                                                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase border border-emerald-500/20">Auto</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase border border-amber-500/20">Manual</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-3 text-xs font-bold">
                                                <?php if ($log['status'] === 'success'): ?>
                                                    <span class="text-emerald-400 flex items-center gap-1.5"><i class="fas fa-check-circle text-[10px]"></i> Success</span>
                                                <?php else: ?>
                                                    <span class="text-rose-400 flex items-center gap-1.5"><i class="fas fa-times-circle text-[10px]"></i> Failed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-zinc-400 max-w-md truncate" title="<?php echo htmlspecialchars($log['message']); ?>"><?php echo htmlspecialchars($log['message']); ?></td>
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
    <script>
        function startBulkSync() {
            if (!confirm("Are you sure you want to sync all products from Srishringarr to Yosshitaneha? This may take up to a minute.")) return;

            const btn = document.getElementById('btnBulkSync');
            const icon = document.getElementById('syncIcon');
            const alert = document.getElementById('syncStatusAlert');
            const msg = document.getElementById('syncStatusMsg');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.classList.add('fa-spin');
            alert.classList.remove('hidden');
            msg.innerText = "Synchronizing products to Yosshitaneha Child DB... Please wait.";

            fetch('index.php?controller=sync&action=syncBulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');

                if (data.success) {
                    msg.innerText = `Bulk Sync Complete! Total: ${data.total}, Success: ${data.success_count}, Failed: ${data.failed_count}`;
                    setTimeout(() => { window.location.reload(); }, 2000);
                } else {
                    msg.innerText = `Sync Failed: ${data.message}`;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');
                msg.innerText = "An error occurred during bulk sync: " + err;
            });
        }
    </script>
</body>
</html>
