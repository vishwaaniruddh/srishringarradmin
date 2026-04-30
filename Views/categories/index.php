<!DOCTYPE html>
<html lang="en">
<head>
    <title>Categories - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Category Management';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex space-x-4">
                            <button onclick="switchCategoryTab('jewel')" id="tab-jewel" class="px-6 py-2 rounded-xl text-sm font-semibold bg-primary text-white shadow-lg transition-all">Jewellery</button>
                            <button onclick="switchCategoryTab('garment')" id="tab-garment" class="px-6 py-2 rounded-xl text-sm font-semibold bg-white text-gray-500 border border-gray-100 hover:bg-gray-50 transition-all">Garments</button>
                        </div>
                        <div class="flex space-x-2">
                            <a href="index.php?controller=category&action=add&type=jewel_cat" id="add-btn" class="bg-green-500 text-white rounded-xl px-4 py-2 text-sm font-medium hover:bg-opacity-90 transition-all flex items-center">
                                <i class="fas fa-plus mr-2"></i> Add Jewel Category
                            </a>
                        </div>
                    </div>

                    <div id="jewel-section" class="space-y-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-gray-500 uppercase">Jewellery Categories & Subcategories</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50/50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">ID</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Name</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Type</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Parent</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($jewelCat as $cat): ?>
                                            <!-- Main Category -->
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-white">
                                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">#<?php echo $cat['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-bold text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-4 text-xs"><span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-semibold uppercase">Category</span></td>
                                                <td class="px-6 py-4 text-sm text-gray-400">-</td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <a href="index.php?controller=category&action=add&type=jewel_sub&parent_id=<?php echo $cat['id']; ?>" class="text-xs text-green-600 hover:underline"><i class="fas fa-plus mr-1"></i>Add Sub</a>
                                                    <a href="index.php?controller=category&action=edit&type=jewel_cat&id=<?php echo $cat['id']; ?>" class="p-1.5 text-gray-400 hover:text-primary transition-colors"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Subcategories -->
                                            <?php 
                                            $subcats = array_filter($jewelSub, function($sub) use ($cat) {
                                                return $sub['maincat_id'] == $cat['id'];
                                            });
                                            foreach ($subcats as $sub): 
                                            ?>
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/20">
                                                <td class="px-6 py-3 text-sm text-gray-400 font-mono pl-10">#<?php echo $sub['id']; ?></td>
                                                <td class="px-6 py-3 text-sm text-gray-600 pl-12 flex items-center">
                                                    <span class="text-gray-300 mr-2">—</span>
                                                    <?php echo htmlspecialchars($sub['name']); ?>
                                                </td>
                                                <td class="px-6 py-3 text-xs"><span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full font-semibold uppercase">Subcategory</span></td>
                                                <td class="px-6 py-3 text-sm text-gray-500 italic"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-3 text-right">
                                                    <a href="index.php?controller=category&action=edit&type=jewel_sub&id=<?php echo $sub['id']; ?>" class="p-1.5 text-gray-400 hover:text-primary transition-colors"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="garment-section" class="hidden space-y-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-gray-500 uppercase">Garment Categories & Subcategories</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50/50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">ID</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Name</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Type</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Parent</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($garmentCat as $cat): ?>
                                            <!-- Main Category -->
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-white">
                                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">#<?php echo $cat['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-bold text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-4 text-xs"><span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-semibold uppercase">Category</span></td>
                                                <td class="px-6 py-4 text-sm text-gray-400">-</td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <a href="index.php?controller=category&action=add&type=garment_sub&parent_id=<?php echo $cat['id']; ?>" class="text-xs text-green-600 hover:underline"><i class="fas fa-plus mr-1"></i>Add Sub</a>
                                                    <a href="index.php?controller=category&action=edit&type=garment_cat&id=<?php echo $cat['id']; ?>" class="p-1.5 text-gray-400 hover:text-primary transition-colors"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Subcategories -->
                                            <?php 
                                            $subcats = array_filter($garmentSub, function($sub) use ($cat) {
                                                return $sub['gmain_id'] == $cat['id'];
                                            });
                                            foreach ($subcats as $sub): 
                                            ?>
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/20">
                                                <td class="px-6 py-3 text-sm text-gray-400 font-mono pl-10">#<?php echo $sub['id']; ?></td>
                                                <td class="px-6 py-3 text-sm text-gray-600 pl-12 flex items-center">
                                                    <span class="text-gray-300 mr-2">—</span>
                                                    <?php echo htmlspecialchars($sub['name']); ?>
                                                </td>
                                                <td class="px-6 py-3 text-xs"><span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full font-semibold uppercase">Subcategory</span></td>
                                                <td class="px-6 py-3 text-sm text-gray-500 italic"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-3 text-right">
                                                    <a href="index.php?controller=category&action=edit&type=garment_sub&id=<?php echo $sub['id']; ?>" class="p-1.5 text-gray-400 hover:text-primary transition-colors"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        function switchCategoryTab(type) {
            document.getElementById('jewel-section').classList.toggle('hidden', type !== 'jewel');
            document.getElementById('garment-section').classList.toggle('hidden', type !== 'garment');
            
            const jewelTab = document.getElementById('tab-jewel');
            const garmentTab = document.getElementById('tab-garment');
            
            if (type === 'jewel') {
                jewelTab.classList.add('bg-primary', 'text-white', 'shadow-lg');
                jewelTab.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                garmentTab.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                garmentTab.classList.remove('bg-primary', 'text-white', 'shadow-lg');
            } else {
                garmentTab.classList.add('bg-primary', 'text-white', 'shadow-lg');
                garmentTab.classList.remove('bg-white', 'text-gray-500', 'border-gray-100');
                jewelTab.classList.add('bg-white', 'text-gray-500', 'border-gray-100');
                jewelTab.classList.remove('bg-primary', 'text-white', 'shadow-lg');
            }

            const addBtn = document.getElementById('add-btn');
            addBtn.href = `index.php?controller=category&action=add&type=${type === 'jewel' ? 'jewel_cat' : 'garment_cat'}`;
            addBtn.innerHTML = `<i class="fas fa-plus mr-2"></i> Add ${type === 'jewel' ? 'Jewel' : 'Garment'} Category`;
        }
    </script>
</body>
</html>
