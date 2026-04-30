<header class="h-20 bg-white border-b flex items-center justify-between px-8 z-10">
    <div class="flex items-center">
        <button id="open-sidebar" class="lg:hidden mr-4 text-gray-500 hover:text-primary">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="text-xl font-semibold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="relative">
            <button class="flex items-center text-gray-600 hover:text-primary transition-colors">
                <span class="mr-2 text-sm font-medium">Admin User</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=6e8efb&color=fff" class="w-10 h-10 rounded-full border-2 border-primary/20" alt="Avatar">
            </button>
        </div>
    </div>
</header>
