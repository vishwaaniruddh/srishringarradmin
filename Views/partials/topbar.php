<header class="h-20 bg-black border-b border-zinc-800 flex items-center justify-between px-8 z-10">
    <div class="flex items-center">
        <button id="open-sidebar" class="lg:hidden mr-4 text-zinc-400 hover:text-white">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="text-lg font-semibold text-white"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
    </div>
    
    <div class="flex items-center space-x-4">
        <!-- Quick product search box -->
        <form method="GET" action="index.php" class="relative hidden sm:block">
            <input type="hidden" name="controller" value="product">
            <input type="hidden" name="action" value="index">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 text-xs pointer-events-none">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Quick SKU/Name search..." class="bg-zinc-900 border border-zinc-800 text-white text-xs rounded-xl pl-8 pr-4 py-2 w-48 lg:w-64 focus:ring-1 focus:ring-zinc-700 focus:border-zinc-700 focus:outline-none transition-all" style="padding-left: 2.25rem !important;">
        </form>

        <div class="relative">
            <button class="flex items-center text-zinc-400 hover:text-white transition-colors">
                <span class="mr-2 text-sm font-medium"><?php echo htmlspecialchars(ucfirst($_SESSION['admin_username'] ?? 'Admin')); ?></span>
                <?php $initials = strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 2)); ?>
                <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white text-xs font-bold border border-zinc-700"><?php echo $initials; ?></div>
            </button>
        </div>
    </div>
</header>
