<!DOCTYPE html>
<html lang="en">
<head>
    <title>Email Configuration - Srishringarr</title>
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
            $pageTitle = 'Email Configuration';
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                
                <?php if (isset($_GET['success'])): ?>
                <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-600 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <i class="fas fa-check-circle"></i>
                    <p class="text-sm font-bold">Configuration saved successfully!</p>
                </div>
                <?php endif; ?>

                <form action="index.php?controller=email&action=save" method="POST" class="max-w-4xl space-y-8">
                    
                    <!-- SMTP Settings -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <i class="fas fa-server"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">SMTP Configuration</h3>
                                <p class="text-xs text-gray-500">Configure your outgoing email server</p>
                            </div>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">SMTP Host</label>
                                <input type="text" name="settings[smtp_host]" value="<?php echo $settings['smtp_host'] ?? ''; ?>" placeholder="smtp.gmail.com" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">SMTP Port</label>
                                <input type="text" name="settings[smtp_port]" value="<?php echo $settings['smtp_port'] ?? '587'; ?>" placeholder="587" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">SMTP Username</label>
                                <input type="text" name="settings[smtp_user]" value="<?php echo $settings['smtp_user'] ?? ''; ?>" placeholder="your-email@gmail.com" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">SMTP Password</label>
                                <input type="password" name="settings[smtp_pass]" value="<?php echo $settings['smtp_pass'] ?? ''; ?>" placeholder="••••••••" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Encryption</label>
                                <select name="settings[smtp_enc]" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                                    <option value="tls" <?php echo ($settings['smtp_enc'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo ($settings['smtp_enc'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="none" <?php echo ($settings['smtp_enc'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">From Name</label>
                                <input type="text" name="settings[from_name]" value="<?php echo $settings['from_name'] ?? 'Srishringarr'; ?>" placeholder="Srishringarr Boutique" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 focus:bg-white focus:ring-primary transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Email Hooks -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <i class="fas fa-hook"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Email Hooks & Triggers</h3>
                                <p class="text-xs text-gray-500">Configure which events trigger automated emails</p>
                            </div>
                        </div>
                        <div class="p-8 space-y-8">
                            <!-- New Order Hook -->
                            <div class="flex items-start justify-between gap-8 p-6 bg-gray-50 rounded-2xl">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-800">New Order Confirmation</h4>
                                    <p class="text-xs text-gray-500">Sends an email to the customer when they place a new order successfully.</p>
                                    <div class="mt-4">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email Subject</label>
                                        <input type="text" name="settings[hook_new_order_subject]" value="<?php echo $settings['hook_new_order_subject'] ?? 'Your Order #{order_id} has been received!'; ?>" class="w-full mt-1 bg-white border-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-primary">
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[hook_new_order_enabled]" value="1" class="sr-only peer" <?php echo ($settings['hook_new_order_enabled'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                    <button type="button" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Edit Template</button>
                                </div>
                            </div>

                            <!-- Rental Return Reminder Hook -->
                            <div class="flex items-start justify-between gap-8 p-6 bg-gray-50 rounded-2xl">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-800">Rental Return Reminder</h4>
                                    <p class="text-xs text-gray-500">Sends a reminder 24 hours before the rental period ends.</p>
                                    <div class="mt-4">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email Subject</label>
                                        <input type="text" name="settings[hook_rental_return_subject]" value="<?php echo $settings['hook_rental_return_subject'] ?? 'Reminder: Your rental return for #{order_id} is tomorrow'; ?>" class="w-full mt-1 bg-white border-gray-100 rounded-lg px-3 py-2 text-sm focus:ring-primary">
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-4">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[hook_rental_return_enabled]" value="1" class="sr-only peer" <?php echo ($settings['hook_rental_return_enabled'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                    <button type="button" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Edit Template</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-12 py-4 bg-primary text-white rounded-2xl font-bold uppercase tracking-widest text-sm shadow-xl shadow-primary/20 hover:bg-stone-900 transition-all">
                            Save All Configurations
                        </button>
                    </div>

                </form>

            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
</body>
</html>
