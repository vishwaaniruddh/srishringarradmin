<?php
// new_admin/Views/partials/sidebar.php
// Unified ShadCN Dark Sidebar Architecture (Matching yn/admin/includes/sidebar-secondary.php)

$currentController = strtolower($_GET['controller'] ?? 'dashboard');
$currentAction = strtolower($_GET['action'] ?? 'index');
$currentScript = basename($_SERVER['PHP_SELF']);

// Active checkers
$isDashboard = ($currentController === 'dashboard' && ($currentScript === 'index.php' || $currentScript === ''));
$isAnalytics = ($currentController === 'analytics' || $currentController === 'reports');

$isProductsActive = in_array($currentController, ['product', 'sync', 'category']) || $currentScript === 'import_archive.php';
$isWooActive = ($currentController === 'wooproduct');

$isOrdersActive = ($currentController === 'orders');
$isDiscountsActive = in_array($currentController, ['discount', 'coupon']);

$isAiActive = in_array($currentController, ['aianalytics', 'aimodels']) || 
              ($currentController === 'product' && in_array($currentAction, ['bulkaiwriter', 'desccorrector']));

$isMarketingActive = ($currentController === 'newsletter');
$isSystemActive = false;

$admin_name = $_SESSION['admin_username'] ?? 'Admin';
$admin_initials = strtoupper(substr($admin_name, 0, 2));
?>
<style>
/* ==========================================================================
   ShadCN UI Dark Sidebar Specification (Exact match with yn/admin)
   ========================================================================== */
#sidebar.shadcn-sidebar {
    background-color: #09090b !important;
    border-right: 1px solid #1f1f23 !important;
    box-shadow: none !important;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    display: flex !important;
    flex-direction: column !important;
    width: 256px !important;
    flex-shrink: 0 !important;
    user-select: none;
}

#sidebar.shadcn-sidebar .shadcn-header {
    height: 58px !important;
    border-bottom: 1px solid #1f1f23 !important;
    background-color: #09090b !important;
    padding: 0 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .workspace-brand {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    text-decoration: none !important;
    min-width: 0;
}

#sidebar.shadcn-sidebar .workspace-icon {
    width: 28px !important;
    height: 28px !important;
    border-radius: 6px !important;
    background-color: rgba(217, 119, 6, 0.15) !important;
    border: 1px solid rgba(245, 158, 11, 0.3) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #fbbf24 !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .workspace-meta {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0;
}

#sidebar.shadcn-sidebar .workspace-title {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #f4f4f5 !important;
    line-height: 1.2 !important;
    letter-spacing: -0.01em !important;
}

#sidebar.shadcn-sidebar .workspace-tag {
    font-size: 10px !important;
    font-weight: 500 !important;
    color: #71717a !important;
    line-height: 1.2 !important;
}

#sidebar.shadcn-sidebar .sidebar-scroll-container {
    flex: 1 1 auto !important;
    overflow-y: auto !important;
    padding: 10px 8px !important;
}

#sidebar.shadcn-sidebar .adminmenu {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Section Header Labels */
#sidebar.shadcn-sidebar .menu-section-label {
    font-size: 10.5px !important;
    font-weight: 600 !important;
    color: #71717a !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    padding: 14px 10px 4px 10px !important;
    margin: 0 !important;
    list-style: none !important;
}

#sidebar.shadcn-sidebar .menu-section-label:first-child {
    padding-top: 4px !important;
}

/* Top-level Menu Items */
#sidebar.shadcn-sidebar .menu-item {
    list-style: none !important;
    margin-bottom: 2px !important;
}

#sidebar.shadcn-sidebar .menu-item > a {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 6px 10px !important;
    font-size: 13px !important;
    font-weight: 450 !important;
    line-height: 1.4 !important;
    border-radius: 6px !important;
    color: #a1a1aa !important;
    text-decoration: none !important;
    transition: all 0.12s ease !important;
    border: 1px solid transparent !important;
    cursor: pointer;
}

#sidebar.shadcn-sidebar .menu-item > a i.item-icon {
    width: 16px !important;
    font-size: 13px !important;
    text-align: center !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #71717a !important;
    flex-shrink: 0 !important;
    transition: color 0.12s ease !important;
}

#sidebar.shadcn-sidebar .menu-item > a span.item-text {
    flex: 1 1 auto !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Hover state */
#sidebar.shadcn-sidebar .menu-item > a:hover {
    background-color: #18181b !important;
    color: #ffffff !important;
}

#sidebar.shadcn-sidebar .menu-item > a:hover i.item-icon {
    color: #f4f4f5 !important;
}

/* Active Top-Level item ONLY (e.g. Dashboard) */
#sidebar.shadcn-sidebar .menu-item.active:not(.has-submenu) > a {
    background-color: #27272a !important;
    color: #ffffff !important;
    font-weight: 500 !important;
}

#sidebar.shadcn-sidebar .menu-item.active:not(.has-submenu) > a i.item-icon {
    color: #ffffff !important;
}

/* Parent with Submenu open / active */
#sidebar.shadcn-sidebar .menu-item.has-submenu.open > a,
#sidebar.shadcn-sidebar .menu-item.has-submenu.active-parent > a {
    color: #ffffff !important;
    font-weight: 500 !important;
}

#sidebar.shadcn-sidebar .menu-item.has-submenu.open > a i.item-icon,
#sidebar.shadcn-sidebar .menu-item.has-submenu.active-parent > a i.item-icon {
    color: #ffffff !important;
}

/* Chevron arrow indicators */
#sidebar.shadcn-sidebar .submenu-arrow {
    margin-left: auto !important;
    font-size: 9px !important;
    color: #52525b !important;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), color 0.15s ease !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .menu-item > a:hover .submenu-arrow {
    color: #a1a1aa !important;
}

#sidebar.shadcn-sidebar .menu-item.open > a .submenu-arrow {
    transform: rotate(90deg) !important;
    color: #a1a1aa !important;
}

/* Submenu container (ShadCN collapsible tree) */
#sidebar.shadcn-sidebar .submenu {
    list-style: none !important;
    padding: 0 !important;
    margin: 2px 0 2px 18px !important;
    border-left: 1px solid #27272a !important;
    padding-left: 6px !important;
    max-height: 0 !important;
    overflow: hidden !important;
    transition: max-height 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 1px !important;
    background-color: transparent !important;
}

#sidebar.shadcn-sidebar .menu-item.open .submenu {
    max-height: 600px !important;
}

#sidebar.shadcn-sidebar .submenu li {
    list-style: none !important;
    margin: 0 !important;
}

#sidebar.shadcn-sidebar .submenu li a {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 5px 8px !important;
    color: #8b8b94 !important;
    font-size: 12.5px !important;
    font-weight: 400 !important;
    border-radius: 5px !important;
    transition: all 0.12s ease !important;
    text-decoration: none !important;
    line-height: 1.4 !important;
}

#sidebar.shadcn-sidebar .submenu li a i {
    width: 14px !important;
    font-size: 11px !important;
    color: #52525b !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .submenu li a:hover {
    color: #ffffff !important;
    background-color: #18181b !important;
}

#sidebar.shadcn-sidebar .submenu li a:hover i {
    color: #e4e4e7 !important;
}

#sidebar.shadcn-sidebar .submenu li.active a {
    color: #ffffff !important;
    font-weight: 500 !important;
    background-color: #27272a !important;
}

#sidebar.shadcn-sidebar .submenu li.active a i {
    color: #ffffff !important;
}

/* Completely suppress any badge in sidebar */
#sidebar.shadcn-sidebar .shadcn-badge {
    display: none !important;
}

/* User Footer Card */
#sidebar.shadcn-sidebar .sidebar-user-footer {
    height: 52px !important;
    padding: 0 12px !important;
    border-top: 1px solid #1f1f23 !important;
    background: #09090b !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-shrink: 0 !important;
}

#sidebar.shadcn-sidebar .user-profile-badge {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
}

#sidebar.shadcn-sidebar .user-avatar-pill {
    width: 26px !important;
    height: 26px !important;
    border-radius: 4px !important;
    background-color: #18181b !important;
    color: #fafafa !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    border: 1px solid #27272a !important;
}

#sidebar.shadcn-sidebar .user-meta {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
}

#sidebar.shadcn-sidebar .user-name {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #f4f4f5 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#sidebar.shadcn-sidebar .user-role {
    font-size: 10px !important;
    color: #71717a !important;
    line-height: 1.2 !important;
}

#sidebar.shadcn-sidebar .sidebar-logout-btn {
    width: 26px !important;
    height: 26px !important;
    border-radius: 4px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #71717a !important;
    text-decoration: none !important;
    transition: all 0.12s ease !important;
}

#sidebar.shadcn-sidebar .sidebar-logout-btn:hover {
    background-color: rgba(239, 68, 68, 0.12) !important;
    color: #ef4444 !important;
}

/* Thin Scrollbar */
#sidebar.shadcn-sidebar .sidebar-scroll-container::-webkit-scrollbar {
    width: 4px;
}
#sidebar.shadcn-sidebar .sidebar-scroll-container::-webkit-scrollbar-track {
    background: transparent;
}
#sidebar.shadcn-sidebar .sidebar-scroll-container::-webkit-scrollbar-thumb {
    background: #27272a;
    border-radius: 4px;
}
#sidebar.shadcn-sidebar .sidebar-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #3f3f46;
}
</style>

<aside id="sidebar" class="shadcn-sidebar fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen">
    <!-- Workspace Brand Header -->
    <div class="shadcn-header">
        <a href="index.php" class="workspace-brand">
            <div class="workspace-icon">
                <i class="fa-solid fa-gem"></i>
            </div>
            <div class="workspace-meta">
                <span class="workspace-title">Srishringarr</span>
                <span class="workspace-tag">Store &bull; POS Admin</span>
            </div>
        </a>
        <button id="close-sidebar" class="lg:hidden text-zinc-400 hover:text-zinc-200 p-1" aria-label="Close Sidebar">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    
    <!-- Scrollable Navigation Area -->
    <div class="sidebar-scroll-container">
        <ul class="adminmenu">
            
            <!-- ==================== PLATFORM ==================== -->
            <li class="menu-section-label">Platform</li>
            
            <li class="menu-item <?php echo $isDashboard ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="fa-solid fa-chart-pie item-icon"></i>
                    <span class="item-text">Dashboard</span>
                </a>
            </li>

            <li class="menu-item <?php echo $isAnalytics ? 'active' : ''; ?>">
                <a href="index.php?controller=analytics">
                    <i class="fa-solid fa-chart-line item-icon"></i>
                    <span class="item-text">Analytics &amp; Reports</span>
                </a>
            </li>

            <!-- ==================== CATALOG & INVENTORY ==================== -->
            <li class="menu-section-label">Catalog &amp; Inventory</li>

            <!-- Products Submenu (Collapsible) -->
            <li class="menu-item has-submenu <?php echo $isProductsActive ? 'open active-parent' : ''; ?>">
                <a href="javascript:void(0);" class="submenu-toggle">
                    <i class="fa-solid fa-boxes-stacked item-icon"></i>
                    <span class="item-text">Products</span>
                    <i class="fa-solid fa-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li class="<?php echo ($currentController === 'product' && in_array($currentAction, ['index', 'edit', 'edit2', 'edit3', 'view'])) ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=index">
                            <i class="fa-solid fa-box"></i> All Products
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'product' && $currentAction === 'add') ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=add">
                            <i class="fa-solid fa-plus"></i> Add New Product
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'category' && $currentAction === 'index') ? 'active' : ''; ?>">
                        <a href="index.php?controller=category&action=index">
                            <i class="fa-solid fa-tags"></i> Categories
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'sync' && $currentAction === 'index') ? 'active' : ''; ?>">
                        <a href="index.php?controller=sync&action=index">
                            <i class="fa-solid fa-arrow-rotate-right"></i> POS Price Sync
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'category' && $currentAction === 'unmapped') ? 'active' : ''; ?>">
                        <a href="index.php?controller=category&action=unmapped">
                            <i class="fa-solid fa-folder-plus"></i> Unmapped Products
                        </a>
                    </li>
                    <li class="<?php echo ($currentScript === 'import_archive.php') ? 'active' : ''; ?>">
                        <a href="import_archive.php">
                            <i class="fa-solid fa-folder-tree"></i> Bulk Import &amp; Archive
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'product' && $currentAction === 'import') ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=import">
                            <i class="fa-solid fa-file-excel"></i> Import Excel
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'product' && $currentAction === 'bulkupdate') ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=bulkUpdate">
                            <i class="fa-solid fa-pen-to-square"></i> Bulk Update
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'product' && $currentAction === 'bulkdelete') ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=bulkDelete">
                            <i class="fa-solid fa-trash-can"></i> Bulk Delete
                        </a>
                    </li>
                </ul>
            </li>

            <!-- YN Web Storefront Products -->
            <li class="menu-item <?php echo $isWooActive ? 'active' : ''; ?>">
                <a href="index.php?controller=wooproduct&action=index">
                    <i class="fa-solid fa-globe item-icon"></i>
                    <span class="item-text">YN Web Products</span>
                </a>
            </li>

            <!-- ==================== SALES & BOOKINGS ==================== -->
            <li class="menu-section-label">Sales &amp; Bookings</li>

            <li class="menu-item <?php echo $isOrdersActive ? 'active' : ''; ?>">
                <a href="index.php?controller=orders">
                    <i class="fa-solid fa-receipt item-icon"></i>
                    <span class="item-text">Orders &amp; Bookings</span>
                </a>
            </li>

            <!-- Discounts & Coupons Submenu (Collapsible) -->
            <li class="menu-item has-submenu <?php echo $isDiscountsActive ? 'open active-parent' : ''; ?>">
                <a href="javascript:void(0);" class="submenu-toggle">
                    <i class="fa-solid fa-tags item-icon"></i>
                    <span class="item-text">Store Discounts</span>
                    <i class="fa-solid fa-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li class="<?php echo ($currentController === 'discount') ? 'active' : ''; ?>">
                        <a href="index.php?controller=discount&action=index">
                            <i class="fa-solid fa-percent"></i> Discounts
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'coupon') ? 'active' : ''; ?>">
                        <a href="index.php?controller=coupon&action=index">
                            <i class="fa-solid fa-ticket"></i> Coupons
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ==================== AI STUDIO & TOOLS ==================== -->
            <li class="menu-section-label">AI Studio &amp; Tools</li>

            <!-- AI Studio Submenu (Collapsible) -->
            <li class="menu-item has-submenu <?php echo $isAiActive ? 'open active-parent' : ''; ?>">
                <a href="javascript:void(0);" class="submenu-toggle">
                    <i class="fa-solid fa-wand-magic-sparkles item-icon"></i>
                    <span class="item-text">AI Content Studio</span>
                    <i class="fa-solid fa-chevron-right submenu-arrow"></i>
                </a>
                <ul class="submenu">
                    <li class="<?php echo ($currentController === 'product' && $currentAction === 'bulkaiwriter') ? 'active' : ''; ?>">
                        <a href="index.php?controller=product&action=bulkAiWriter">
                            <i class="fa-solid fa-bolt-lightning"></i> Bulk AI Writer
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'aianalytics') ? 'active' : ''; ?>">
                        <a href="index.php?controller=aianalytics">
                            <i class="fa-solid fa-chart-area"></i> AI Analytics
                        </a>
                    </li>
                    <li class="<?php echo ($currentController === 'aimodels') ? 'active' : ''; ?>">
                        <a href="index.php?controller=aimodels">
                            <i class="fa-solid fa-user-astronaut"></i> AI Models
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ==================== MARKETING & COMMS ==================== -->
            <li class="menu-section-label">Marketing &amp; Comms</li>

            <li class="menu-item <?php echo ($currentController === 'newsletter') ? 'active' : ''; ?>">
                <a href="index.php?controller=newsletter">
                    <i class="fa-solid fa-envelope-open-text item-icon"></i>
                    <span class="item-text">Newsletters</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar User Footer -->
    <div class="sidebar-user-footer">
        <div class="user-profile-badge">
            <div class="user-avatar-pill">
                <?php echo htmlspecialchars($admin_initials); ?>
            </div>
            <div class="user-meta">
                <span class="user-name"><?php echo htmlspecialchars(ucfirst($admin_name)); ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <a href="index.php?controller=auth&action=logout" class="sidebar-logout-btn" title="Log Out">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
    </div>
</aside>

<script>
// Self-contained Submenu & Sidebar Mobile Toggle (Guaranteed to work across all views)
(function() {
    function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        // Submenu accordion toggling
        const submenuToggles = sidebar.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            // Remove previous event listener if any
            toggle.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.closest('.has-submenu');
                if (parent) {
                    parent.classList.toggle('open');
                }
            };
        });

        // Mobile sidebar toggle
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        if (openBtn) {
            openBtn.onclick = function(e) {
                e.preventDefault();
                sidebar.classList.toggle('-translate-x-full');
            };
        }
        if (closeBtn) {
            closeBtn.onclick = function(e) {
                e.preventDefault();
                sidebar.classList.add('-translate-x-full');
            };
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();
</script>
