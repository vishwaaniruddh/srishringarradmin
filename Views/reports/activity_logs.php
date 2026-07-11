<!DOCTYPE html>
<html lang="en">
<head>
    <title>System Activity Logs - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'System Activity Logs';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-7xl mx-auto">
                    
                    <!-- Search & Filter Bar -->
                    <div class="mb-6 bg-black p-4 rounded-xl border border-zinc-800">
                        <form method="GET" action="index.php" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                            <input type="hidden" name="controller" value="report">
                            <input type="hidden" name="action" value="activityLogs">
                            
                            <div class="lg:col-span-8">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 text-xs">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by username, controller, action, IP or params..." class="w-full bg-black border border-zinc-800 text-white text-xs rounded-lg focus:ring-1 focus:ring-zinc-700 focus:border-zinc-700 block pl-8 py-2.5" style="padding-left: 2.25rem !important;">
                                </div>
                            </div>
                            
                            <div class="lg:col-span-4 flex gap-2 justify-end">
                                <button type="submit" class="w-full lg:w-auto bg-white border border-white text-black hover:bg-black hover:text-white hover:border-zinc-800 rounded-lg px-6 py-2.5 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-search mr-1.5"></i> Search
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="index.php?controller=report&action=activityLogs" class="w-full lg:w-auto bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-4 py-2.5 text-xs font-semibold transition-all flex items-center justify-center cursor-pointer">
                                        Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Logs Table -->
                    <div class="bg-black rounded-xl border border-zinc-800 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-zinc-950 border-b border-zinc-800">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-20">ID</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-44">Time</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-36">Admin</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-48">Action</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-24 text-center">Method</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider w-36">IP Address</th>
                                        <th class="px-6 py-4 text-xs font-semibold text-zinc-500 uppercase tracking-wider text-right">Inspection</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/40">
                                    <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-zinc-500">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-history text-3xl mb-4 opacity-20"></i>
                                                    <p>No activity logs found matching the filter criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr class="hover:bg-zinc-900/30 transition-colors border-b border-zinc-900">
                                                <td class="px-6 py-4 text-xs text-zinc-500 font-mono"><?php echo $log['id']; ?></td>
                                                <td class="px-6 py-4 text-xs text-zinc-300 font-medium">
                                                    <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                                                </td>
                                                <td class="px-6 py-4 text-xs">
                                                    <div class="flex flex-col">
                                                        <span class="text-white font-semibold"><?php echo htmlspecialchars($log['admin_username'] ?? 'Guest'); ?></span>
                                                        <?php if ($log['admin_id']): ?>
                                                            <span class="text-[10px] text-zinc-500">ID: <?php echo $log['admin_id']; ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-xs">
                                                    <div class="flex flex-col">
                                                        <span class="text-zinc-200 font-semibold font-mono"><?php echo htmlspecialchars($log['controller']); ?></span>
                                                        <span class="text-[10px] text-zinc-500 font-mono">→ <?php echo htmlspecialchars($log['action']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border 
                                                        <?php 
                                                        $method = strtoupper($log['request_method']);
                                                        if ($method === 'POST') {
                                                            echo 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
                                                        } elseif ($method === 'GET') {
                                                            echo 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                                                        } else {
                                                            echo 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20';
                                                        }
                                                        ?>">
                                                        <?php echo $method; ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-xs text-zinc-400 font-mono">
                                                    <?php echo htmlspecialchars($log['ip_address']); ?>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <button 
                                                        onclick="inspectPayload(<?php echo htmlspecialchars(json_encode($log)); ?>)" 
                                                        class="bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all inline-flex items-center cursor-pointer">
                                                        <i class="fas fa-info-circle mr-1"></i> Inspect
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Section -->
                        <?php if ($totalPages > 1): ?>
                            <?php 
                            $startRange = ($page - 1) * 20 + 1;
                            $endRange = min($page * 20, $total);
                            ?>
                            <div class="px-6 py-4 bg-zinc-950 border-t border-zinc-900 flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="text-xs text-zinc-500">
                                    Showing <span class="font-semibold text-zinc-300"><?php echo $startRange; ?></span> to 
                                    <span class="font-semibold text-zinc-300"><?php echo $endRange; ?></span> of 
                                    <span class="font-semibold text-zinc-300"><?php echo $total; ?></span> entries
                                </div>
                                <div class="flex items-center space-x-1">
                                    <?php if ($page > 1): ?>
                                        <a href="index.php?controller=report&action=activityLogs&page=1&search=<?php echo urlencode($search); ?>" class="px-2.5 py-1.5 text-[11px] font-medium text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg hover:text-white hover:bg-zinc-800 transition-all">First</a>
                                        <a href="index.php?controller=report&action=activityLogs&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="px-2.5 py-1.5 text-[11px] font-medium text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg hover:text-white hover:bg-zinc-800 transition-all">Prev</a>
                                    <?php endif; ?>

                                    <?php 
                                    $range = 2;
                                    $start = max(1, $page - $range);
                                    $end = min($totalPages, $page + $range);
                                    for ($i = $start; $i <= $end; $i++): 
                                    ?>
                                        <a href="index.php?controller=report&action=activityLogs&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                                           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?php echo $i === $page ? 'bg-white text-black shadow-sm' : 'text-zinc-400 bg-zinc-900 border border-zinc-800 hover:text-white hover:bg-zinc-800'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <a href="index.php?controller=report&action=activityLogs&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="px-2.5 py-1.5 text-[11px] font-medium text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg hover:text-white hover:bg-zinc-800 transition-all">Next</a>
                                        <a href="index.php?controller=report&action=activityLogs&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>" class="px-2.5 py-1.5 text-[11px] font-medium text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-lg hover:text-white hover:bg-zinc-800 transition-all">Last</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Inspector Modal -->
    <div id="inspectorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-zinc-950 rounded-xl border border-zinc-800 max-w-3xl w-full max-h-[85vh] flex flex-col shadow-2xl">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-zinc-800 bg-zinc-950">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Request Payload Inspector</h3>
                    <p class="text-xs text-zinc-500 mt-1 font-mono" id="modal-route-label"></p>
                </div>
                <button onclick="closeInspector()" class="text-zinc-500 hover:text-white transition-colors cursor-pointer">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-6 bg-zinc-950">
                <!-- Metadata Info -->
                <div class="grid grid-cols-2 gap-4 text-xs bg-zinc-900/40 p-4 rounded-lg border border-zinc-850">
                    <div>
                        <span class="text-zinc-500 block mb-0.5">Admin User:</span>
                        <span class="text-white font-semibold" id="modal-user-label"></span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block mb-0.5">IP Address / Time:</span>
                        <span class="text-white font-semibold font-mono" id="modal-meta-label"></span>
                    </div>
                </div>

                <!-- URI -->
                <div>
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-2">Request URI</span>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-3 text-xs text-zinc-300 font-mono break-all" id="modal-uri-box"></div>
                </div>

                <!-- GET Parameters -->
                <div>
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-2">GET Parameters</span>
                    <pre class="bg-zinc-900 border border-zinc-800 rounded-lg p-4 text-xs text-emerald-400 font-mono overflow-x-auto" id="modal-get-box"></pre>
                </div>

                <!-- POST Parameters -->
                <div>
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-2">POST / JSON Parameters (Sanitized)</span>
                    <pre class="bg-zinc-900 border border-zinc-800 rounded-lg p-4 text-xs text-amber-400 font-mono overflow-x-auto" id="modal-post-box"></pre>
                </div>

                <!-- User Agent -->
                <div>
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-2">User Agent</span>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-3 text-xs text-zinc-400 font-mono break-all" id="modal-ua-box"></div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-zinc-800 bg-zinc-950 flex justify-end">
                <button onclick="closeInspector()" class="bg-zinc-900 border border-zinc-800 text-white hover:bg-zinc-800 rounded-lg px-6 py-2.5 text-xs font-semibold transition-all cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>

    <script>
    function inspectPayload(log) {
        // Build Labels
        document.getElementById('modal-route-label').textContent = `${log.request_method} - ${log.controller} / ${log.action}`;
        document.getElementById('modal-user-label').textContent = `${log.admin_username || 'Guest'} ${log.admin_id ? '(ID: ' + log.admin_id + ')' : ''}`;
        document.getElementById('modal-meta-label').textContent = `${log.ip_address} @ ${log.created_at}`;
        document.getElementById('modal-uri-box').textContent = log.request_uri;

        // Parse Parameters
        let getParams = {};
        try {
            getParams = JSON.parse(log.get_params || '{}');
        } catch (e) {
            getParams = { error: 'Failed to parse parameters' };
        }
        
        let postParams = {};
        try {
            postParams = JSON.parse(log.post_params || '{}');
        } catch (e) {
            postParams = { error: 'Failed to parse parameters' };
        }

        // Format and render boxes
        document.getElementById('modal-get-box').textContent = Object.keys(getParams).length > 0 
            ? JSON.stringify(getParams, null, 4) 
            : '{}';
            
        document.getElementById('modal-post-box').textContent = Object.keys(postParams).length > 0 
            ? JSON.stringify(postParams, null, 4) 
            : '{}';

        document.getElementById('modal-ua-box').textContent = log.user_agent || 'Unknown';

        // Display Modal
        const modal = document.getElementById('inspectorModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeInspector() {
        const modal = document.getElementById('inspectorModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modal on click outside content window
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('inspectorModal');
        if (e.target === modal) {
            closeInspector();
        }
    });

    // Close modal on ESC key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeInspector();
        }
    });
    </script>
</body>
</html>
