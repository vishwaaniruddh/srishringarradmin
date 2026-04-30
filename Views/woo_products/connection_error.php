<!DOCTYPE html>
<html lang="en">
<head>
    <title>Connection Error - Yn Products</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="flex-1 flex flex-col min-w-0">
            <?php 
            $pageTitle = 'Yn Products (Remote)';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>
            <main class="flex-1 flex items-center justify-center p-8">
                <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 text-center border border-gray-100">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-database text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Remote Database Disconnected</h2>
                    <p class="text-gray-500 mb-8 leading-relaxed">We couldn't connect to your remote WordPress WooCommerce server. Please check your credentials in <code>admin/config.php</code>.</p>
                    
                    <div class="bg-gray-50 p-6 rounded-2xl text-left mb-8 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Required Credentials</h4>
                        <ul class="text-xs space-y-2 text-gray-600 font-mono">
                            <li class="flex justify-between"><span>Host:</span> <span class="text-red-500">REMOTE_HOST</span></li>
                            <li class="flex justify-between"><span>User:</span> <span class="text-red-500">REMOTE_USER</span></li>
                            <li class="flex justify-between"><span>DB Name:</span> <span class="text-red-500">REMOTE_DB</span></li>
                        </ul>
                    </div>

                    <a href="index.php?controller=wooproduct&action=index" class="inline-flex items-center justify-center w-full bg-primary text-white py-3 rounded-xl font-bold shadow-lg hover:opacity-90 transition-all">
                        <i class="fas fa-sync-alt mr-2"></i> Retry Connection
                    </a>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
