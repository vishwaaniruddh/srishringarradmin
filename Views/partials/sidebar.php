<?php
if (!function_exists('isActive')) {
    function isActive($controller, $action = null) {
        $c = strtolower($_GET['controller'] ?? 'Dashboard');
        $a = strtolower($_GET['action'] ?? 'index');
        
        $targetC = strtolower($controller);
        
        if ($action === null) {
            return $c === $targetC;
        }
        
        return $c === $targetC && $a === strtolower($action);
    }
}
$isImportArchive = (basename($_SERVER['PHP_SELF']) === 'import_archive.php' || isActive('product', 'serverImport'));
?>
<style>
/* Shadcn UI Dark Sidebar Specification */
#sidebar.shadcn-sidebar {
    background-color: #090d16 !important;
    border-right: 1px solid #1e293b !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4) !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    display: flex !important;
    flex-direction: column !important;
}

#sidebar.shadcn-sidebar .shadcn-header {
    height: 58px !important;
    border-bottom: 1px solid #1e293b !important;
    background-color: #090d16 !important;
    padding: 0 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .shadcn-workspace-card {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-decoration: none !important;
}

#sidebar.shadcn-sidebar .shadcn-logo-badge {
    width: 34px !important;
    height: 34px !important;
    border-radius: 8px !important;
    background-color: #111827 !important;
    border: 1px solid #1e293b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 3px !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .shadcn-logo-img {
    max-height: 100% !important;
    max-width: 100% !important;
    object-fit: contain !important;
}

#sidebar.shadcn-sidebar .shadcn-group-label {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 12px 10px 4px 10px !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    padding: 7px 10px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    border-radius: 6px !important;
    color: #94a3b8 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
    margin-bottom: 2px !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item:hover {
    background-color: rgba(255, 255, 255, 0.06) !important;
    color: #f8fafc !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item.active {
    background-color: #1e293b !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3) !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item .nav-icon {
    width: 16px !important;
    text-align: center !important;
    font-size: 13px !important;
    color: #64748b !important;
    flex-shrink: 0 !important;
    transition: color 0.15s ease !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item:hover .nav-icon {
    color: #f8fafc !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item.active .nav-icon {
    color: #38bdf8 !important;
}

#sidebar.shadcn-sidebar .shadcn-badge {
    margin-left: auto !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
    background-color: #111827 !important;
    color: #94a3b8 !important;
    border: 1px solid #1e293b !important;
}

#sidebar.shadcn-sidebar .shadcn-nav-item.active .shadcn-badge {
    background-color: #0f172a !important;
    color: #38bdf8 !important;
    border-color: #334155 !important;
}

#sidebar.shadcn-sidebar .shadcn-footer {
    border-top: 1px solid #1e293b !important;
    background-color: #090d16 !important;
    padding: 10px 12px !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .shadcn-user-card {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    padding: 6px 8px !important;
    border-radius: 8px !important;
    background-color: #111827 !important;
    border: 1px solid #1e293b !important;
}

#sidebar.shadcn-sidebar .shadcn-user-avatar {
    width: 30px !important;
    height: 30px !important;
    border-radius: 9999px !important;
    background-color: #1e293b !important;
    color: #f8fafc !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    border: 1px solid #334155 !important;
}

#sidebar.shadcn-sidebar .shadcn-logout-btn {
    margin-left: auto !important;
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    text-decoration: none !important;
    transition: all 0.15s ease !important;
}

#sidebar.shadcn-sidebar .shadcn-logout-btn:hover {
    background-color: rgba(239, 68, 68, 0.15) !important;
    color: #f87171 !important;
}

#sidebar .custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
#sidebar .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
#sidebar .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 4px;
}
#sidebar .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #334155;
}
</style>

<aside id="sidebar" class="shadcn-sidebar sidebar-transition fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen">
    <!-- Shadcn Workspace Header -->
    <div class="shadcn-header">
        <a href="index.php" class="shadcn-workspace-card">
            <div class="shadcn-logo-badge">
                <img src="assets/logo.webp" alt="Logo" class="shadcn-logo-img">
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-slate-100 leading-tight tracking-tight truncate">Srishringarr</span>
                <span class="text-[10px] text-slate-400 font-medium">Store & POS Admin</span>
            </div>
        </a>
        <button id="close-sidebar" class="lg:hidden text-slate-400 hover:text-slate-200 p-1">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>
    
    <!-- Navigation Body -->
    <nav class="flex-1 overflow-y-auto px-3 py-3 custom-scrollbar">
        <!-- Overview -->
        <div class="mb-3">
            <a href="index.php" class="shadcn-nav-item <?php echo isActive('dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie nav-icon"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- AI Studio -->
        <div class="mb-3">
            <p class="shadcn-group-label">AI Studio</p>
            <a href="index.php?controller=product&action=bulkAiWriter" class="shadcn-nav-item <?php echo isActive('product', 'bulkAiWriter') ? 'active' : ''; ?>">
                <i class="fas fa-wand-magic-sparkles nav-icon"></i>
                <span>Bulk AI Writer</span>
                <span class="shadcn-badge">Gemini</span>
            </a>
            <a href="index.php?controller=aianalytics" class="shadcn-nav-item <?php echo isActive('aianalytics') ? 'active' : ''; ?>">
                <i class="fas fa-chart-area nav-icon"></i>
                <span>AI Analytics</span>
            </a>
            <a href="index.php?controller=aimodels" class="shadcn-nav-item <?php echo isActive('aimodels') ? 'active' : ''; ?>">
                <i class="fas fa-user-astronaut nav-icon"></i>
                <span>AI Models</span>
            </a>
        </div>

        <!-- Catalog -->
        <div class="mb-3">
            <p class="shadcn-group-label">Catalog</p>
            <a href="index.php?controller=product&action=index" class="shadcn-nav-item <?php echo isActive('product', 'index') ? 'active' : ''; ?>">
                <i class="fas fa-box nav-icon"></i>
                <span>All Products</span>
            </a>
            <a href="index.php?controller=wooproduct&action=index" class="shadcn-nav-item <?php echo isActive('wooproduct') ? 'active' : ''; ?>">
                <i class="fas fa-globe nav-icon"></i>
                <span>YN Web Products</span>
                <span class="shadcn-badge">Remote</span>
            </a>
            <a href="index.php?controller=sync" class="shadcn-nav-item <?php echo isActive('sync', 'index') ? 'active' : ''; ?>">
                <i class="fas fa-sync-alt nav-icon"></i>
                <span>Product Sync</span>
                <span class="shadcn-badge">Auto</span>
            </a>
            <a href="index.php?controller=category&action=index" class="shadcn-nav-item <?php echo isActive('category', 'index') ? 'active' : ''; ?>">
                <i class="fas fa-tags nav-icon"></i>
                <span>Categories</span>
            </a>
            <a href="index.php?controller=category&action=unmapped" class="shadcn-nav-item <?php echo isActive('category', 'unmapped') || isActive('product', 'unmapped') ? 'active' : ''; ?>">
                <i class="fas fa-folder-plus nav-icon"></i>
                <span>Unmapped Products</span>
                <span class="shadcn-badge">Fix</span>
            </a>
        </div>

        <!-- Bulk Operations -->
        <div class="mb-3">
            <p class="shadcn-group-label">Bulk Actions</p>
            <a href="import_archive.php" class="shadcn-nav-item <?php echo $isImportArchive ? 'active' : ''; ?>">
                <i class="fas fa-server nav-icon"></i>
                <span>Server Folder Import</span>
                <span class="shadcn-badge">Direct</span>
            </a>
            <a href="index.php?controller=product&action=import" class="shadcn-nav-item <?php echo isActive('product', 'import') ? 'active' : ''; ?>">
                <i class="fas fa-file-import nav-icon"></i>
                <span>Import Excel</span>
            </a>
            <a href="index.php?controller=product&action=bulkUpdate" class="shadcn-nav-item <?php echo isActive('product', 'bulkUpdate') ? 'active' : ''; ?>">
                <i class="fas fa-edit nav-icon"></i>
                <span>Bulk Update</span>
            </a>
            <a href="index.php?controller=product&action=bulkDelete" class="shadcn-nav-item <?php echo isActive('product', 'bulkDelete') ? 'active' : ''; ?>">
                <i class="fas fa-trash-alt nav-icon"></i>
                <span>Bulk Delete</span>
            </a>
        </div>

        <!-- Sales -->
        <div class="mb-3">
            <p class="shadcn-group-label">Sales</p>
            <a href="index.php?controller=orders" class="shadcn-nav-item <?php echo isActive('orders') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart nav-icon"></i>
                <span>Orders & Bookings</span>
            </a>
        </div>

        <!-- Marketing -->
        <div class="mb-3">
            <p class="shadcn-group-label">Marketing</p>
            <a href="index.php?controller=coupon" class="shadcn-nav-item <?php echo isActive('coupon') ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt nav-icon"></i>
                <span>Coupons</span>
            </a>
            <a href="index.php?controller=discount" class="shadcn-nav-item <?php echo isActive('discount') ? 'active' : ''; ?>">
                <i class="fas fa-percentage nav-icon"></i>
                <span>Discounts</span>
            </a>
        </div>

        <!-- Communications -->
        <div class="mb-3">
            <p class="shadcn-group-label">Communications</p>
            <a href="index.php?controller=email" class="shadcn-nav-item <?php echo isActive('email') ? 'active' : ''; ?>">
                <i class="fas fa-envelope nav-icon"></i>
                <span>Emails</span>
            </a>
            <a href="index.php?controller=newsletter" class="shadcn-nav-item <?php echo isActive('newsletter') ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane nav-icon"></i>
                <span>Newsletter</span>
            </a>
        </div>

        <!-- Analytics -->
        <div class="mb-3">
            <p class="shadcn-group-label">Analytics</p>
            <a href="index.php?controller=report&action=sku" class="shadcn-nav-item <?php echo isActive('report', 'sku') ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar nav-icon"></i>
                <span>SKU Master Audit</span>
            </a>
            <a href="index.php?controller=analytics&action=index" class="shadcn-nav-item <?php echo isActive('analytics') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line nav-icon"></i>
                <span>User Activity</span>
            </a>
        </div>

        <!-- System -->
        <div class="mb-3">
            <p class="shadcn-group-label">System</p>
            <a href="index.php?controller=chatbot&action=settings" class="shadcn-nav-item <?php echo isActive('chatbot', 'settings') ? 'active' : ''; ?>">
                <i class="fas fa-robot nav-icon"></i>
                <span>AI Chatbot</span>
            </a>
            <a href="index.php?controller=dashboard&action=systemInfo" class="shadcn-nav-item <?php echo isActive('dashboard', 'systemInfo') ? 'active' : ''; ?>">
                <i class="fas fa-microchip nav-icon"></i>
                <span>Server Limits</span>
            </a>
            <a href="index.php?controller=report&action=activityLogs" class="shadcn-nav-item <?php echo isActive('report', 'activityLogs') ? 'active' : ''; ?>">
                <i class="fas fa-history nav-icon"></i>
                <span>Activity Logs</span>
            </a>
        </div>
    </nav>

    <!-- Shadcn User Profile Footer -->
    <div class="shadcn-footer">
        <div class="shadcn-user-card">
            <?php 
            $adminUser = ucfirst($_SESSION['admin_username'] ?? 'Admin');
            $userInitial = strtoupper(substr($adminUser, 0, 1));
            ?>
            <div class="shadcn-user-avatar">
                <?php echo $userInitial; ?>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-bold text-slate-100 leading-tight truncate"><?php echo htmlspecialchars($adminUser); ?></span>
                <span class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>POS Connected</span>
                </span>
            </div>
            <a href="index.php?controller=auth&action=logout" class="shadcn-logout-btn" title="Sign Out">
                <i class="fas fa-arrow-right-from-bracket text-xs"></i>
            </a>
        </div>
    </div>
</aside>
