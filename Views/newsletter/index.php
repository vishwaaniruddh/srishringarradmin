<!DOCTYPE html>
<html lang="en">
<head>
    <title>Newsletter - Srishringarr</title>
    <?php include 'Views/partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'Views/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Newsletter';
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Newsletter Subscribers</h2>
                            <p class="text-gray-500 mt-1">Manage your store's email marketing audience.</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="subscriber_search" placeholder="Search emails..." class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none text-sm w-64">
                            </div>
                            <button onclick="window.location.href='../newsloffers.php'" class="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-medium flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i> Compose Newsletter
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Total Subscribers</h3>
                                <p class="text-2xl font-bold text-gray-800"><?php echo $totalSubscribers; ?></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Active Status</h3>
                                <p class="text-2xl font-bold text-gray-800">Verified</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Growth</h3>
                                <p class="text-2xl font-bold text-gray-800">+12%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Subscribers List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800">Subscriber List</h3>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Latest 100 Subscribers</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider font-bold">
                                        <th class="px-6 py-4 w-16">#</th>
                                        <th class="px-6 py-4">Email Address</th>
                                        <th class="px-6 py-4">Subscription Date</th>
                                        <th class="px-6 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="subscribers_table_body" class="divide-y divide-gray-50">
                                    <?php if (empty($subscribers)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                                <i class="fas fa-info-circle text-3xl mb-3 block"></i>
                                                No subscribers found.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $i = 1; foreach ($subscribers as $sub): ?>
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-6 py-4 font-medium text-gray-400">
                                                    <?php echo $i++; ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 font-bold text-xs">
                                                            <?php echo strtoupper(substr($sub['email'], 0, 1)); ?>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-700"><?php echo $sub['email']; ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    <i class="far fa-calendar-alt mr-2"></i>
                                                    <?php echo date('M d, Y', strtotime($sub['created_at'] ?? 'now')); ?>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $sub['id']; ?>)" class="text-red-400 hover:text-red-600 transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this subscriber from the list?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6e8efb',
                cancelButtonColor: '#f87171',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?controller=newsletter&delete_id=${id}`;
                }
            })
        }

        document.getElementById('subscriber_search').addEventListener('input', function(e) {
            const term = e.target.value.trim();
            if (term.length < 2 && term.length > 0) return;

            fetch(`index.php?controller=newsletter&action=search&q=${encodeURIComponent(term)}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('subscribers_table_body');
                    tbody.innerHTML = '';
                    
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">No matching subscribers found.</td></tr>';
                        return;
                    }

                    data.forEach((sub, index) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50/50 transition-colors';
                        tr.innerHTML = `
                            <td class="px-6 py-4 font-medium text-gray-400">${index + 1}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-full flex items-center justify-center mr-3 font-bold text-xs">
                                        ${sub.email.charAt(0).toUpperCase()}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">${sub.email}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <i class="far fa-calendar-alt mr-2"></i>
                                Recently
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="javascript:void(0)" onclick="confirmDelete(${sub.id})" class="text-red-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        });
    </script>
</body>
</html>
