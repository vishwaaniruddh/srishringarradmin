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
<aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0">
    <div class="flex items-center justify-between h-20 px-6 bg-white border-b">
        <span class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">
            Srishringarr
        </span>
        <button id="close-sidebar" class="lg:hidden text-gray-500 hover:text-primary">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="mt-6 px-4 space-y-2">
        <a href="index.php" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo isActive('dashboard') ? 'text-white bg-gradient-to-r from-primary to-secondary shadow-lg' : 'text-gray-500 hover:bg-gray-100 hover:text-primary'; ?>">
            <i class="fas fa-grid-2 mr-3"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="space-y-1">
            <button class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all group submenu-toggle <?php echo (isActive('product') || isActive('category')) ? 'text-primary bg-gray-50 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-primary'; ?>">
                <div class="flex items-center">
                    <i class="fas fa-box mr-3 group-hover:scale-110 transition-transform"></i>
                    <span>Products</span>
                </div>
                <i class="fas fa-chevron-right text-xs transition-transform duration-200 chevron <?php echo (isActive('product') || isActive('category')) ? 'rotate-90' : ''; ?>"></i>
            </button>
            <div class="submenu <?php echo (isActive('product') || isActive('category')) ? '' : 'hidden'; ?> overflow-hidden transition-all duration-300 pl-12 pr-4 space-y-1">
                <a href="index.php?controller=product&action=index" class="block py-2 text-sm <?php echo isActive('product', 'index') ? 'text-primary font-semibold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">All Products</a>
                <a href="index.php?controller=wooproduct&action=index" class="block py-2 text-sm <?php echo isActive('wooproduct') ? 'text-primary font-semibold' : 'text-gray-500 hover:text-primary'; ?> transition-colors font-medium">Yn Products <span class="ml-1 text-[10px] bg-blue-100 text-blue-600 px-1 rounded uppercase tracking-tighter">Remote</span></a>
                <a href="index.php?controller=product&action=add" class="block py-2 text-sm <?php echo isActive('product', 'add') ? 'text-primary font-semibold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">Add New</a>
                <a href="index.php?controller=category&action=index" class="block py-2 text-sm <?php echo isActive('category') ? 'text-primary font-semibold' : 'text-gray-500 hover:text-primary'; ?> transition-colors">Categories</a>
            </div>
        </div>
        
        <a href="#" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-100 hover:text-primary rounded-xl transition-all group">
            <i class="fas fa-shopping-cart mr-3 group-hover:scale-110 transition-transform"></i>
            <span>Orders</span>
        </a>
        
        <a href="#" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-100 hover:text-primary rounded-xl transition-all group">
            <i class="fas fa-users mr-3 group-hover:scale-110 transition-transform"></i>
            <span>Customers</span>
        </a>

        <div class="pt-4 mt-4 border-t border-gray-100">
            <a href="#" class="flex items-center px-4 py-3 text-gray-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group">
                <i class="fas fa-power-off mr-3"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>
