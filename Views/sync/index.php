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
                
                <!-- Real-time Interactive Sync Console -->
                <div id="syncStatusAlert" class="hidden p-5 rounded-2xl border border-indigo-500/30 bg-gradient-to-b from-[#0d0d15] to-[#08080c] shadow-2xl flex flex-col gap-4">
                    <!-- Top Status Header & Stop Button -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400">
                                <i class="fas fa-sync-alt fa-spin text-sm" id="statusSpinner"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-white flex items-center gap-2">
                                    <span id="syncStatusTitle">Bulk Syncing Products to Yosshitaneha</span>
                                    <span id="syncPercentBadge" class="px-2 py-0.5 bg-indigo-500/20 text-indigo-300 text-[11px] font-mono font-bold rounded-full border border-indigo-500/30">0%</span>
                                </div>
                                <div id="syncStatusMsg" class="text-xs text-zinc-400 mt-0.5 font-medium">Preparing product sync queue...</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnCancelSync" onclick="cancelBulkSync()" style="display:none;" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-stop-circle"></i> Stop Sync
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-zinc-900 h-2.5 rounded-full overflow-hidden border border-white/5 relative">
                        <div id="syncProgressBar" class="bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 h-full w-0 transition-all duration-200 rounded-full shadow-[0_0_12px_rgba(99,102,241,0.6)]"></div>
                    </div>

                    <!-- Realtime Counter Badges -->
                    <div class="grid grid-cols-4 gap-3 text-center">
                        <div class="bg-zinc-900/60 border border-white/5 p-2.5 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-zinc-500 block tracking-wider">Processed</span>
                            <span id="cntProcessed" class="text-base font-bold text-white font-mono">0 / 0</span>
                        </div>
                        <div class="bg-emerald-500/10 border border-emerald-500/20 p-2.5 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">Synced</span>
                            <span id="cntSynced" class="text-base font-bold text-emerald-300 font-mono">0</span>
                        </div>
                        <div class="bg-amber-500/10 border border-amber-500/20 p-2.5 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-amber-400 block tracking-wider">Skipped</span>
                            <span id="cntSkipped" class="text-base font-bold text-amber-300 font-mono">0</span>
                        </div>
                        <div class="bg-rose-500/10 border border-rose-500/20 p-2.5 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-rose-400 block tracking-wider">Failed</span>
                            <span id="cntFailed" class="text-base font-bold text-rose-300 font-mono">0</span>
                        </div>
                    </div>

                    <!-- Realtime Live Log Terminal Window -->
                    <div class="mt-1">
                        <div class="flex items-center justify-between text-[11px] font-bold text-zinc-400 mb-1.5 uppercase tracking-wider">
                            <span><i class="fas fa-terminal mr-1.5 text-indigo-400"></i> Live Sync Terminal Activity</span>
                            <span class="text-zinc-500 font-normal">Item-by-item real-time status</span>
                        </div>
                        <div id="syncLiveConsole" class="w-full h-44 bg-black/90 border border-zinc-800 rounded-xl p-3 font-mono text-[11px] overflow-y-auto custom-scrollbar flex flex-col gap-1 text-zinc-300">
                            <div class="text-zinc-500 italic">[System initialized] Click "Sync All Products" to start live synchronization.</div>
                        </div>
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

                <!-- Category Sync Configuration Card -->
                <div class="bg-[#0a0a0a] border border-white/5 rounded-xl p-6 shadow-2xl shrink-0 flex flex-col gap-5">
                    <div class="flex items-center justify-between border-b border-white/5 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-white flex items-center gap-2">
                                <i class="fas fa-sliders-h text-indigo-400"></i> Category Sync Filter Configuration
                            </h2>
                            <p class="text-xs text-zinc-400 mt-0.5">Select which product categories from Srishringarr should auto-sync and bulk-sync to Yosshitaneha.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="selectAllCategories(true)" class="text-xs text-zinc-400 hover:text-white underline font-medium">Select All</button>
                            <span class="text-zinc-700">|</span>
                            <button type="button" onclick="selectAllCategories(false)" class="text-xs text-zinc-400 hover:text-white underline font-medium">Deselect All</button>
                            <button type="button" id="btnSaveConfig" onclick="saveCategoryConfig()" class="ml-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                                <i class="fas fa-save" id="saveIcon"></i>
                                <span>Save Category Configuration</span>
                            </button>
                        </div>
                    </div>

                    <?php
                        $syncAll = !empty($syncSettings['sync_all']);
                        $enabledCats = $syncSettings['enabled_categories'] ?? [];
                    ?>

                    <form id="formCatConfig" class="flex flex-col gap-6">
                        <div class="flex items-center gap-3 bg-zinc-900/50 p-3 rounded-lg border border-white/5">
                            <input type="checkbox" id="chkSyncAll" name="sync_all" value="1" <?php echo $syncAll ? 'checked' : ''; ?> onchange="toggleSyncAllMode()" class="w-4 h-4 accent-indigo-500 cursor-pointer">
                            <label for="chkSyncAll" class="text-xs font-semibold text-zinc-200 cursor-pointer select-none">
                                Sync All Categories (Unrestricted - Overrides category selections below)
                            </label>
                        </div>

                        <div id="catSelectionGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6 <?php echo $syncAll ? 'opacity-40 pointer-events-none' : ''; ?> transition-all">
                            <!-- Apparel Categories -->
                            <div class="bg-black/40 border border-white/5 rounded-xl p-4 flex flex-col gap-3">
                                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-2">
                                        <i class="fas fa-tshirt"></i> Apparel / Garments Categories
                                    </h3>
                                    <span class="text-[11px] text-zinc-500"><?php echo count($categories['Apparel']['children'] ?? []); ?> Categories</span>
                                </div>
                                <div class="space-y-2 max-h-72 overflow-y-auto custom-scrollbar pr-2">
                                    <?php if (!empty($categories['Apparel']['children'])): ?>
                                        <?php foreach ($categories['Apparel']['children'] as $catKey => $catData): ?>
                                            <?php $isChecked = $syncAll || in_array($catKey, $enabledCats); ?>
                                            <label class="flex items-center justify-between p-2 rounded hover:bg-white/[0.03] transition-colors cursor-pointer group">
                                                <div class="flex items-center gap-2.5">
                                                    <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox w-4 h-4 accent-indigo-500 cursor-pointer">
                                                    <span class="text-xs text-zinc-300 group-hover:text-white font-medium"><?php echo htmlspecialchars($catData['name']); ?></span>
                                                </div>
                                                <span class="text-[10px] text-zinc-500 bg-zinc-900 px-2 py-0.5 rounded-full font-mono"><?php echo (int)$catData['count']; ?> items</span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-xs text-zinc-600">No apparel categories found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Jewellery Categories -->
                            <div class="bg-black/40 border border-white/5 rounded-xl p-4 flex flex-col gap-3">
                                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                                    <h3 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                                        <i class="fas fa-gem"></i> Jewellery Categories & Subcategories
                                    </h3>
                                    <span class="text-[11px] text-zinc-500"><?php echo count($categories['Jewellery']['children'] ?? []); ?> Items</span>
                                </div>
                                <div class="space-y-1.5 max-h-72 overflow-y-auto custom-scrollbar pr-2">
                                    <?php if (!empty($categories['Jewellery']['children'])): ?>
                                        <?php foreach ($categories['Jewellery']['children'] as $catKey => $catData): ?>
                                            <?php 
                                                $isChecked = $syncAll || in_array($catKey, $enabledCats);
                                                $isSub = str_starts_with($catKey, 'jewel_child:');
                                            ?>
                                            <label class="flex items-center justify-between p-2 rounded hover:bg-white/[0.03] transition-colors cursor-pointer group <?php echo $isSub ? 'ml-4 bg-zinc-900/30' : ''; ?>">
                                                <div class="flex items-center gap-2.5">
                                                    <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($catKey); ?>" <?php echo $isChecked ? 'checked' : ''; ?> class="cat-checkbox w-4 h-4 accent-amber-500 cursor-pointer">
                                                    <span class="text-xs <?php echo $isSub ? 'text-zinc-400 font-normal' : 'text-zinc-200 font-semibold'; ?> group-hover:text-white">
                                                        <?php echo htmlspecialchars($catData['name']); ?>
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-zinc-500 bg-zinc-900 px-2 py-0.5 rounded-full font-mono"><?php echo (int)$catData['count']; ?> items</span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-xs text-zinc-600">No jewellery categories found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
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
                                                <?php elseif ($log['status'] === 'skipped'): ?>
                                                    <span class="text-zinc-400 flex items-center gap-1.5"><i class="fas fa-minus-circle text-[10px]"></i> Skipped</span>
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
        function toggleSyncAllMode() {
            const chkSyncAll = document.getElementById('chkSyncAll');
            const grid = document.getElementById('catSelectionGrid');
            if (chkSyncAll.checked) {
                grid.classList.add('opacity-40', 'pointer-events-none');
            } else {
                grid.classList.remove('opacity-40', 'pointer-events-none');
            }
        }

        function selectAllCategories(state) {
            const chkSyncAll = document.getElementById('chkSyncAll');
            if (chkSyncAll.checked) {
                chkSyncAll.checked = false;
                toggleSyncAllMode();
            }
            const checkboxes = document.querySelectorAll('.cat-checkbox');
            checkboxes.forEach(cb => cb.checked = state);
        }

        function saveCategoryConfig() {
            const btn = document.getElementById('btnSaveConfig');
            const icon = document.getElementById('saveIcon');
            const form = document.getElementById('formCatConfig');
            const formData = new FormData(form);

            btn.disabled = true;
            icon.className = 'fas fa-spinner fa-spin';

            fetch('index.php?controller=sync&action=saveSettings', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                icon.className = 'fas fa-save';

                if (data.success) {
                    alert(data.message);
                } else {
                    alert('Error saving configuration: ' + data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                icon.className = 'fas fa-save';
                alert('Network error while saving settings: ' + err);
            });
        }

        let isSyncCancelled = false;

        async function startBulkSync() {
            if (!confirm("Are you sure you want to sync products from Srishringarr to Yosshitaneha? You will see live progress for each product.")) return;

            isSyncCancelled = false;

            const btn = document.getElementById('btnBulkSync');
            const icon = document.getElementById('syncIcon');
            const alertBox = document.getElementById('syncStatusAlert');
            const statusMsg = document.getElementById('syncStatusMsg');
            const statusTitle = document.getElementById('syncStatusTitle');
            const progressBar = document.getElementById('syncProgressBar');
            const percentBadge = document.getElementById('syncPercentBadge');
            const consoleBox = document.getElementById('syncLiveConsole');
            const btnCancel = document.getElementById('btnCancelSync');

            const cntProcessed = document.getElementById('cntProcessed');
            const cntSynced = document.getElementById('cntSynced');
            const cntSkipped = document.getElementById('cntSkipped');
            const cntFailed = document.getElementById('cntFailed');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.classList.add('fa-spin');
            alertBox.classList.remove('hidden');
            btnCancel.style.display = 'inline-flex';

            statusTitle.textContent = "Bulk Syncing Products to Yosshitaneha";
            statusMsg.textContent = "Fetching product sync queue from database...";
            progressBar.style.width = '0%';
            percentBadge.textContent = '0%';
            consoleBox.innerHTML = `<div class="text-indigo-400 font-bold">[${new Date().toLocaleTimeString()}] Fetching product queue from database...</div>`;

            let queue = [];
            try {
                const res = await fetch('index.php?controller=sync&action=getSyncQueue');
                const data = await res.json();
                if (!data.success || !data.items) {
                    statusMsg.textContent = "Failed to fetch sync queue: " + (data.message || "Unknown error");
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    icon.classList.remove('fa-spin');
                    return;
                }
                queue = data.items;
            } catch (err) {
                statusMsg.textContent = "Network error fetching sync queue: " + err;
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');
                return;
            }

            const total = queue.length;
            if (total === 0) {
                statusMsg.textContent = "No products found to sync.";
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                icon.classList.remove('fa-spin');
                return;
            }

            let processed = 0;
            let synced = 0;
            let skipped = 0;
            let failed = 0;

            cntProcessed.textContent = `0 / ${total}`;
            cntSynced.textContent = '0';
            cntSkipped.textContent = '0';
            cntFailed.textContent = '0';

            consoleBox.innerHTML += `<div class="text-emerald-400 font-bold">[${new Date().toLocaleTimeString()}] Queue ready. ${total} products to process. Starting live sync...</div>`;

            for (let i = 0; i < total; i++) {
                if (isSyncCancelled) {
                    consoleBox.innerHTML += `<div class="text-rose-400 font-bold mt-2">[${new Date().toLocaleTimeString()}] 🛑 Sync process stopped by user. Processed ${processed} of ${total}.</div>`;
                    statusTitle.textContent = "Sync Cancelled by User";
                    statusMsg.textContent = `Stopped at ${processed} / ${total} products.`;
                    break;
                }

                const item = queue[i];
                const code = item.code || `ID:${item.id}`;
                const name = item.name ? (item.name.length > 40 ? item.name.substring(0, 40) + '...' : item.name) : 'Product';

                statusMsg.innerHTML = `<span class="text-indigo-300 font-bold">Syncing ${i + 1} of ${total}:</span> <span class="text-white font-mono">[${code}]</span> ${name} (${item.type})`;

                try {
                    const formData = new FormData();
                    formData.append('id', item.id);
                    formData.append('type', item.type);

                    const syncRes = await fetch('index.php?controller=sync&action=syncSingle', {
                        method: 'POST',
                        body: formData
                    });
                    const resData = await syncRes.json();

                    processed++;
                    const pct = Math.round((processed / total) * 100);
                    progressBar.style.width = pct + '%';
                    percentBadge.textContent = pct + '%';
                    cntProcessed.textContent = `${processed} / ${total}`;

                    const timeStr = new Date().toLocaleTimeString();

                    if (resData.success) {
                        if (resData.skipped) {
                            skipped++;
                            cntSkipped.textContent = skipped;
                            consoleBox.innerHTML += `<div class="text-amber-400"><span class="text-zinc-500">[${timeStr}]</span> ⏭ <span class="font-bold">[${code}]</span> ${name} &rarr; <span class="italic">Skipped (Category disabled)</span></div>`;
                        } else {
                            synced++;
                            cntSynced.textContent = synced;
                            consoleBox.innerHTML += `<div class="text-emerald-400"><span class="text-zinc-500">[${timeStr}]</span> ✓ <span class="font-bold">[${code}]</span> ${name} &rarr; <span class="font-bold">Synced</span></div>`;
                        }
                    } else {
                        failed++;
                        cntFailed.textContent = failed;
                        const err = resData.message || 'Unknown failure';
                        consoleBox.innerHTML += `<div class="text-rose-400"><span class="text-zinc-500">[${timeStr}]</span> ❌ <span class="font-bold">[${code}]</span> ${name} &rarr; <span>${err}</span></div>`;
                    }

                    consoleBox.scrollTop = consoleBox.scrollHeight;

                } catch (err) {
                    processed++;
                    failed++;
                    cntFailed.textContent = failed;
                    cntProcessed.textContent = `${processed} / ${total}`;
                    consoleBox.innerHTML += `<div class="text-rose-400"><span class="text-zinc-500">[${new Date().toLocaleTimeString()}]</span> ❌ <span class="font-bold">[${code}]</span> Network Error: ${err}</div>`;
                    consoleBox.scrollTop = consoleBox.scrollHeight;
                }
            }

            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            icon.classList.remove('fa-spin');
            btnCancel.style.display = 'none';

            if (!isSyncCancelled) {
                statusTitle.textContent = "🎉 Bulk Synchronization Completed!";
                statusMsg.textContent = `Finished processing ${total} products. Synced: ${synced}, Skipped: ${skipped}, Failed: ${failed}.`;
                consoleBox.innerHTML += `<div class="text-emerald-400 font-bold mt-2">[${new Date().toLocaleTimeString()}] 🎉 All done! Processed ${total} products.</div>`;
                consoleBox.scrollTop = consoleBox.scrollHeight;
            }
        }

        function cancelBulkSync() {
            isSyncCancelled = true;
            document.getElementById('syncStatusMsg').textContent = "Stopping sync process after current item completes...";
        }
    </script>
</body>
</html>
