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

        function startBulkSync() {
            if (!confirm("Are you sure you want to sync products from Srishringarr to Yosshitaneha based on your Category Configuration? This may take up to a minute.")) return;

            const btn = document.getElementById('btnBulkSync');
            const icon = document.getElementById('syncIcon');
            const alertBox = document.getElementById('syncStatusAlert');
            const msg = document.getElementById('syncStatusMsg');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            icon.classList.add('fa-spin');
            alertBox.classList.remove('hidden');
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
                    msg.innerText = `Bulk Sync Complete! Total Processed: ${data.total}, Synced: ${data.success_count}, Skipped: ${data.skipped_count || 0}, Failed: ${data.failed_count}`;
                    setTimeout(() => { window.location.reload(); }, 2500);
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
