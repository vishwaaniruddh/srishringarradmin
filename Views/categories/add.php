<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Category - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Add Category';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50">
                            <h2 class="text-lg font-bold text-gray-800">
                                <?php 
                                if ($type === 'jewel_cat') echo 'New Jewellery Category';
                                elseif ($type === 'jewel_sub') echo 'New Jewellery Subcategory';
                                elseif ($type === 'garment_cat') echo 'New Garment Category';
                                else echo 'New Garment Subcategory';
                                ?>
                            </h2>
                        </div>

                        <form action="index.php?controller=category&action=store" method="POST" class="p-8 space-y-6">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">

                            <?php if (strpos($type, 'sub') !== false): ?>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Parent Category</label>
                                <select name="parent_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                                    <option value="">Select Parent</option>
                                    <?php foreach ($parents as $p): ?>
                                        <option value="<?php echo $p['id']; ?>" <?php echo (isset($_GET['parent_id']) && $_GET['parent_id'] == $p['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Name</label>
                                <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                                <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-primary focus:border-primary"></textarea>
                            </div>

                            <div class="pt-6 flex justify-end space-x-4">
                                <a href="index.php?controller=category&action=index" class="px-8 py-3 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-all">Cancel</a>
                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all">
                                    Save Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
