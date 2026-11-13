<header class="h-16 bg-white border-b border-zinc-200 flex items-center justify-between px-6 z-10 flex-shrink-0" style="height: 58px !important; background: #ffffff !important; border-bottom: 1px solid #e4e4e7 !important;">
    <div class="flex items-center gap-3">
        <button id="open-sidebar" type="button" class="lg:hidden flex items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition-colors shadow-xs" style="width: 34px; height: 34px; min-width: 34px; padding: 0; cursor: pointer; margin-right: 4px;" aria-label="Toggle navigation menu">
            <i class="fa-solid fa-bars" style="font-size: 15px;"></i>
        </button>
        <h1 style="font-size: 14px; font-weight: 600; color: #09090b !important; letter-spacing: -0.01em; margin: 0;"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
    </div>
    
    <div class="flex items-center gap-3">
        <!-- Quick product search box -->
        <form method="GET" action="index.php" class="relative hidden sm:block">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="index">
            <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #a1a1aa; font-size: 11.5px; pointer-events: none;"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search products..." style="background: #f4f4f5 !important; border: 1px solid #e4e4e7 !important; color: #09090b !important; font-size: 12.5px; border-radius: 6px; padding: 0 12px 0 30px !important; height: 32px; width: 220px; outline: none; font-family: inherit; transition: all 0.12s ease;">
        </form>

        <!-- Clear Server & Image Cache Button -->
        <button type="button" id="btn-clear-cache" onclick="triggerClearCache(this)" class="shadcn-btn shadcn-btn-outline" style="height: 32px; font-size: 12px; padding: 0 10px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border-radius: 6px; border: 1px solid #e2e8f0; background: #ffffff; color: #334155;" title="Clear server image cache and store cache">
            <i class="fa-solid fa-arrows-rotate" style="font-size: 11px; color: #64748b;"></i>
            <span class="hidden md:inline" style="font-weight: 500;">Clear Cache</span>
        </button>
        <script>
        function triggerClearCache(btn) {
            if (!confirm('Clear server image cache and store cache now?')) return;
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size: 11px;"></i> <span style="font-size:11px;">Clearing...</span>';
            fetch('index.php?controller=api&action=clearCache', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    alert(data.message || 'Server cache cleared successfully!');
                })
                .catch(err => {
                    alert('Cache clear requested.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                });
        }
        </script>

        <div class="flex items-center gap-2">
            <span style="font-size: 12.5px; color: #71717a; font-weight: 400;">
                <?php echo htmlspecialchars(ucfirst($_SESSION['admin_username'] ?? 'Admin')); ?>
            </span>
            <?php $initials = strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 2)); ?>
            <div style="width: 30px; height: 30px; border-radius: 6px; background: #09090b; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 11px; font-weight: 600; border: 1px solid #27272a;"><?php echo $initials; ?></div>
        </div>
    </div>
</header>
