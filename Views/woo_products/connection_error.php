<!DOCTYPE html>
<html lang="en">
<head>
    <title>Connection Error - YN Storefront Products</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-[#f8fafc] font-sans text-zinc-900">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="flex-1 flex flex-col min-w-0">
            <?php 
            $pageTitle = 'YN Storefront Products';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>
            <main class="flex-1 flex items-center justify-center p-8">
                <div class="max-w-md w-full bg-white rounded-xl shadow-xs p-8 text-center border border-zinc-200">
                    <div class="w-14 h-14 bg-red-50 text-red-600 rounded-lg flex items-center justify-center mx-auto mb-5 border border-red-100">
                        <i class="fa-solid fa-database text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-900 mb-1.5 tracking-tight">Child Store Database Disconnected</h2>
                    <p class="text-xs text-zinc-500 mb-6 leading-relaxed">
                        Unable to connect to the Yosshita Neha Core PHP child storefront database. Please ensure the MySQL service is running and credentials in <code>Core/ProductSyncService.php</code> match the environment.
                    </p>
                    
                    <div class="bg-zinc-50 p-4 rounded-lg text-left mb-6 border border-zinc-200">
                        <h4 class="text-[11px] font-semibold text-zinc-600 uppercase tracking-wider mb-2.5">Connection Target</h4>
                        <ul class="text-xs space-y-1.5 text-zinc-700 font-mono">
                            <li class="flex justify-between"><span>Host:</span> <span class="text-zinc-900 font-semibold">localhost</span></li>
                            <li class="flex justify-between"><span>DB (Prod):</span> <span class="text-zinc-900 font-semibold">u464193275_yosshitanehafs</span></li>
                            <li class="flex justify-between"><span>DB (Local):</span> <span class="text-zinc-900 font-semibold">yosshitaneha_db</span></li>
                        </ul>
                    </div>

                    <a href="index.php?controller=wooproduct&action=index" class="inline-flex items-center justify-center w-full bg-zinc-900 hover:bg-zinc-800 text-white py-2.5 px-4 rounded-md text-xs font-medium transition-colors">
                        <i class="fa-solid fa-arrows-rotate mr-2 text-xs"></i> Retry Connection
                    </a>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
