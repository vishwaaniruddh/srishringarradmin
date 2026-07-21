<?php
function isActive($controller, $action = null) {
    $c = strtolower($_GET['controller'] ?? 'Dashboard');
    $a = strtolower($_GET['action'] ?? 'index');
    
    $targetC = strtolower($controller);
    
    if ($action === null) {
        return $c === $targetC;
    }
    
    return $c === $targetC && $a === strtolower($action);
}
?>
<aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-black border-r border-zinc-800 transform -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
    <div class="flex items-center justify-between h-20 px-6 bg-black border-b border-zinc-800">
        <span class="text-lg font-bold text-white tracking-tight flex items-center">
            <span class="mr-2 text-md">▲</span> Srishringarr
        </span>
        <button id="close-sidebar" class="lg:hidden text-zinc-500 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="mt-6 px-4 pb-8 space-y-6">
        <!-- Main Section -->
        <div class="space-y-1">
            <a href="index.php" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('dashboard') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-chart-pie w-5 mr-2 text-zinc-400"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- AI Section -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">AI Studio</p>
            <a href="index.php?controller=aiPlayground" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('aiplayground') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-magic w-5 mr-2 text-zinc-400"></i>
                <span>AI Playground</span>
                <span class="ml-auto text-[9px] bg-pink-500/10 text-pink-400 px-1 rounded font-bold uppercase tracking-tighter">New</span>
            </a>
            <a href="index.php?controller=aianalytics" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('aianalytics') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-chart-area w-5 mr-2 text-zinc-400"></i>
                <span>AI Analytics</span>
            </a>
            <a href="index.php?controller=aimodels" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('aimodels') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-user-astronaut w-5 mr-2 text-zinc-400"></i>
                <span>Models</span>
            </a>
        </div>

        <!-- Catalog Section -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Catalog</p>
            
            <a href="index.php?controller=product&action=index" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('product', 'index') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-box w-5 mr-2 text-zinc-400"></i>
                <span>All Products</span>
            </a>
            <a href="index.php?controller=wooproduct&action=index" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('wooproduct') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-globe w-5 mr-2 text-zinc-400"></i>
                <span>YN Web Products</span>
                <span class="ml-auto text-[9px] bg-blue-500/10 text-blue-400 px-1 rounded font-bold uppercase tracking-tighter">Remote</span>
            </a>
            <a href="index.php?controller=product&action=add" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('product', 'add') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-plus w-5 mr-2 text-zinc-400"></i>
                <span>Add Product</span>
            </a>
            <a href="index.php?controller=category&action=index" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('category') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-tags w-5 mr-2 text-zinc-400"></i>
                <span>Categories</span>
            </a>
        </div>

        <!-- Bulk Operations -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Bulk Actions</p>
            <a href="index.php?controller=product&action=import" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('product', 'import') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-file-import w-5 mr-2 text-zinc-400"></i>
                <span>Import Excel</span>
            </a>
            <a href="index.php?controller=product&action=bulkUpdate" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('product', 'bulkUpdate') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-edit w-5 mr-2 text-zinc-400"></i>
                <span>Bulk Update</span>
            </a>
            <a href="index.php?controller=product&action=bulkDelete" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('product', 'bulkDelete') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-trash-alt w-5 mr-2 text-zinc-400"></i>
                <span>Bulk Delete</span>
            </a>
        </div>

        <!-- Sales Operations -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Sales</p>
            <a href="index.php?controller=orders" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('orders') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-shopping-cart w-5 mr-2 text-zinc-400"></i>
                <span>Orders & Bookings</span>
            </a>
            <a href="#" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all text-zinc-400 hover:text-white hover:bg-zinc-900/50">
                <i class="fas fa-users w-5 mr-2 text-zinc-400"></i>
                <span>Customers</span>
            </a>
        </div>

        <!-- Marketing -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Marketing</p>
            <a href="index.php?controller=coupon" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('coupon') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-ticket-alt w-5 mr-2 text-zinc-400"></i>
                <span>Coupons</span>
            </a>
            <a href="index.php?controller=discount" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('discount') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-percentage w-5 mr-2 text-zinc-400"></i>
                <span>Discounts</span>
            </a>
        </div>

        <!-- Communications -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Comms</p>
            <a href="index.php?controller=email" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('email') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-envelope w-5 mr-2 text-zinc-400"></i>
                <span>Emails</span>
            </a>
            <a href="index.php?controller=newsletter" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('newsletter') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-paper-plane w-5 mr-2 text-zinc-400"></i>
                <span>Newsletter</span>
            </a>
        </div>

        <!-- Analytics -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Analytics</p>
            <a href="index.php?controller=report&action=sku" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('report', 'sku') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-chart-bar w-5 mr-2 text-zinc-400"></i>
                <span>SKU Master Audit</span>
            </a>
            <a href="index.php?controller=analytics&action=index" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('analytics') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-chart-line w-5 mr-2 text-zinc-400"></i>
                <span>User Activity</span>
            </a>
        </div>

        <!-- System -->
        <div class="pt-4 border-t border-zinc-800 space-y-1">
            <p class="px-3 text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">System</p>
            <a href="index.php?controller=chatbot&action=settings" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('chatbot', 'settings') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <span class="material-symbols-outlined w-5 mr-2 text-[18px]">smart_toy</span>
                <span>AI Chatbot</span>
            </a>
            <a href="index.php?controller=report&action=activityLogs" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all <?php echo isActive('report', 'activityLogs') ? 'text-white bg-zinc-900' : 'text-zinc-400 hover:text-white hover:bg-zinc-900/50'; ?>">
                <i class="fas fa-history w-5 mr-2 text-zinc-400"></i>
                <span>Activity Logs</span>
            </a>
            
            <div class="pt-2 mt-2 border-t border-zinc-800/50">
                <a href="index.php?controller=auth&action=logout" class="flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all text-zinc-500 hover:text-red-500 hover:bg-red-500/10">
                    <i class="fas fa-power-off w-5 mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>
</aside>
