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
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div class="flex space-x-2 bg-white p-1 rounded-2xl shadow-sm border border-gray-100 w-fit">
                            <button onclick="switchCategoryTab('jewel')" id="tab-jewel" class="px-6 py-2 rounded-xl text-sm font-semibold bg-primary text-white shadow-md transition-all">Jewellery</button>
                            <button onclick="switchCategoryTab('garment')" id="tab-garment" class="px-6 py-2 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-all">Garments</button>
                        </div>
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="catSearch" placeholder="Search categories..." class="w-full bg-white border border-gray-200 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:ring-primary focus:border-primary shadow-sm transition-all">
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="index.php?controller=category&action=add&type=jewel_cat" id="add-btn" class="bg-green-500 text-white rounded-xl px-6 py-2.5 text-sm font-semibold hover:bg-opacity-90 transition-all flex items-center shadow-md">
                                <i class="fas fa-plus mr-2"></i> Add Jewel Category
                            </a>
                        </div>
                    </div>

                    <div id="jewel-section" class="space-y-8">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jewellery Hierarchy</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse" id="jewelTable">
                                    <thead class="bg-gray-50/50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">ID</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Name</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Type</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-center">Products</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($jewelCat as $cat): ?>
                                            <!-- Main Category -->
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-white cat-row" data-name="<?php echo strtolower(htmlspecialchars($cat['name'])); ?>">
                                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">#<?php echo $cat['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-bold text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-4 text-xs"><span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-semibold uppercase">Category</span></td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-bold"><?php echo $cat['product_count']; ?></span>
                                                </td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <a href="index.php?controller=category&action=add&type=jewel_sub&parent_id=<?php echo $cat['id']; ?>" class="text-xs text-green-600 hover:underline font-medium"><i class="fas fa-plus mr-1"></i>Add Sub</a>
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
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/20 cat-row" data-name="<?php echo strtolower(htmlspecialchars($sub['name'])); ?>">
                                                <td class="px-6 py-3 text-sm text-gray-400 font-mono pl-10">#<?php echo $sub['id']; ?></td>
                                                <td class="px-6 py-3 text-sm text-gray-600 pl-12 flex items-center">
                                                    <span class="text-gray-300 mr-2">└</span>
                                                    <?php echo htmlspecialchars($sub['name']); ?>
                                                </td>
                                                <td class="px-6 py-3 text-xs"><span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full font-semibold uppercase">Sub</span></td>
                                                <td class="px-6 py-3 text-center">
                                                    <span class="text-gray-400 text-xs font-bold"><?php echo $sub['product_count']; ?></span>
                                                </td>
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
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Garment Hierarchy</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse" id="garmentTable">
                                    <thead class="bg-gray-50/50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">ID</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Name</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400">Type</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-center">Products</th>
                                            <th class="px-6 py-4 text-xs font-bold uppercase text-gray-400 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php foreach ($garmentCat as $cat): ?>
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-white cat-row" data-name="<?php echo strtolower(htmlspecialchars($cat['name'])); ?>">
                                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">#<?php echo $cat['id']; ?></td>
                                                <td class="px-6 py-4 text-sm font-bold text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></td>
                                                <td class="px-6 py-4 text-xs"><span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-semibold uppercase">Category</span></td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-xs font-bold"><?php echo $cat['product_count']; ?></span>
                                                </td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <a href="index.php?controller=category&action=add&type=garment_sub&parent_id=<?php echo $cat['id']; ?>" class="text-xs text-green-600 hover:underline font-medium"><i class="fas fa-plus mr-1"></i>Add Sub</a>
                                                    <a href="index.php?controller=category&action=edit&type=garment_cat&id=<?php echo $cat['id']; ?>" class="p-1.5 text-gray-400 hover:text-primary transition-colors"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                            
                                            <?php 
                                            $subcats = array_filter($garmentSub, function($sub) use ($cat) {
                                                return $sub['gmain_id'] == $cat['id'];
                                            });
                                            foreach ($subcats as $sub): 
                                            ?>
                                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/20 cat-row" data-name="<?php echo strtolower(htmlspecialchars($sub['name'])); ?>">
                                                <td class="px-6 py-3 text-sm text-gray-400 font-mono pl-10">#<?php echo $sub['id']; ?></td>
                                                <td class="px-6 py-3 text-sm text-gray-600 pl-12 flex items-center">
                                                    <span class="text-gray-300 mr-2">└</span>
                                                    <?php echo htmlspecialchars($sub['name']); ?>
                                                </td>
                                                <td class="px-6 py-3 text-xs"><span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full font-semibold uppercase">Sub</span></td>
                                                <td class="px-6 py-3 text-center">
                                                    <span class="text-gray-400 text-xs font-bold"><?php echo $sub['product_count']; ?></span>
                                                </td>
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
        // Real-time Search Filter
        document.getElementById('catSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.cat-row');
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function switchCategoryTab(type) {
            document.getElementById('jewel-section').classList.toggle('hidden', type !== 'jewel');
            document.getElementById('garment-section').classList.toggle('hidden', type !== 'garment');
            
            const jewelTab = document.getElementById('tab-jewel');
            const garmentTab = document.getElementById('tab-garment');
            
            if (type === 'jewel') {
                jewelTab.classList.add('bg-primary', 'text-white', 'shadow-md');
                jewelTab.classList.remove('text-gray-500', 'hover:bg-gray-50');
                garmentTab.classList.remove('bg-primary', 'text-white', 'shadow-md');
                garmentTab.classList.add('text-gray-500', 'hover:bg-gray-50');
            } else {
                garmentTab.classList.add('bg-primary', 'text-white', 'shadow-md');
                garmentTab.classList.remove('text-gray-500', 'hover:bg-gray-50');
                jewelTab.classList.remove('bg-primary', 'text-white', 'shadow-md');
                jewelTab.classList.add('text-gray-500', 'hover:bg-gray-50');
            }

            const addBtn = document.getElementById('add-btn');
            addBtn.href = `index.php?controller=category&action=add&type=${type === 'jewel' ? 'jewel_cat' : 'garment_cat'}`;
            addBtn.innerHTML = `<i class="fas fa-plus mr-2"></i> Add ${type === 'jewel' ? 'Jewel' : 'Garment'} Category`;
        }
    </script>
</body>
</html>
