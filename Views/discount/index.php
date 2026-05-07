<!DOCTYPE html>
<html lang="en">
<head>
    <title>Discounts - Srishringarr</title>
    <?php include 'Views/partials/head.php'; ?>
    <!-- Select2 -->
    <style>
        .custom-select-container {
            position: relative;
            width: 100%;
        }
        .custom-select-input {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            min-height: 42px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: text;
        }
        .custom-select-input:focus-within {
            border-color: #6e8efb;
            box-shadow: 0 0 0 3px rgba(110, 142, 251, 0.1);
        }
        .selected-chip {
            display: flex;
            items: center;
            background-color: #6e8efb;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .remove-chip {
            margin-left: 6px;
            cursor: pointer;
            opacity: 0.8;
        }
        .remove-chip:hover {
            opacity: 1;
        }
        .search-input {
            flex: 1;
            min-width: 120px;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.875rem;
            padding: 2px;
        }
        .dropdown-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 50;
            background: white;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: none;
        }
        .dropdown-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.2s;
        }
        .dropdown-item:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'Views/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <?php 
            $pageTitle = 'Discounts';
            include 'Views/partials/topbar.php'; 
            ?>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                <div class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Discount Architect</h2>
                            <p class="text-gray-500 mt-1">Master your store's pricing with precision.</p>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="switchTab('settings')" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all font-medium flex items-center">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </button>
                            <button onclick="switchTab('rules')" class="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-medium flex items-center">
                                <i class="fas fa-list mr-2"></i> View Rules
                            </button>
                        </div>
                    </div>

                    <!-- Status Messages -->
                    <?php if (isset($_GET['status'])): ?>
                        <?php if ($_GET['status'] === 'saved'): ?>
                            <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl flex items-center">
                                <i class="fas fa-check-circle mr-3"></i> Rule saved successfully!
                            </div>
                        <?php elseif ($_GET['status'] === 'settings_saved'): ?>
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 text-blue-700 rounded-xl flex items-center">
                                <i class="fas fa-info-circle mr-3"></i> Settings updated successfully!
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Tabs Content -->
                    <div id="tab-rules" class="tab-content">
                        <!-- Add Rule Form -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-plus-circle text-primary mr-2"></i> Add New Discount Rule
                                </h3>
                            </div>
                            <form id="da_rule_form" action="index.php?controller=discount" method="POST" class="p-6">
                                <input type="hidden" name="action" id="da_action" value="add_rule">
                                <input type="hidden" name="rule_id" id="da_rule_id" value="">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Scope</label>
                                        <select id="da_scope" name="scope" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                            <optgroup label="Global Rules">
                                                <option value="global">All Products</option>
                                            </optgroup>
                                            <optgroup label="Specific Targets">
                                                <option value="product">Product Wise</option>
                                                <option value="category">Category Wise</option>
                                            </optgroup>
                                            <optgroup label="Price Conditions">
                                                <option value="price_gt">Price Greater Than (>)</option>
                                                <option value="price_lt">Price Less Than (<)</option>
                                                <option value="price_between">In Between 2 Prices (Min/Max)</option>
                                            </optgroup>
                                            <optgroup label="Compound Rules">
                                                <option value="cat_price_gt">Category + Price Greater Than</option>
                                                <option value="cat_price_lt">Category + Price Less Than</option>
                                                <option value="cat_price_between">Category + In Between 2 Prices</option>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div id="targets_container">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Targets</label>
                                        <div class="custom-select-container" id="target-select-wrapper">
                                            <div class="custom-select-input" id="da_targets_ui">
                                                <input type="text" class="search-input" placeholder="Search..." id="da_search">
                                            </div>
                                            <div class="dropdown-results" id="da_results"></div>
                                            <!-- Hidden input to store values for form submission -->
                                            <input type="hidden" name="targets_json" id="da_targets_input">
                                        </div>
                                    </div>

                                    <div id="threshold_container" class="hidden">
                                        <label id="threshold_label" class="block text-sm font-semibold text-gray-700 mb-2">Price Threshold</label>
                                        <input type="number" step="0.01" name="threshold" id="da_threshold" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                    </div>

                                    <div id="threshold_max_container" class="hidden">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Max Price Threshold</label>
                                        <input type="number" step="0.01" name="threshold_max" id="da_threshold_max" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Type</label>
                                        <select name="type" id="da_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="flat">Flat Amount</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Value</label>
                                        <input type="number" step="0.01" name="value" id="da_value" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Rule Weight (Priority)</label>
                                        <input type="number" name="weight" id="da_weight" value="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none">
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between items-center">
                                    <div id="edit_notice" class="hidden text-sm text-orange-600 font-medium bg-orange-50 px-4 py-2 rounded-lg border border-orange-100">
                                        <i class="fas fa-edit mr-2"></i> You are currently editing a rule.
                                        <button type="button" onclick="resetForm()" class="ml-4 underline hover:no-underline">Cancel</button>
                                    </div>
                                    <div class="flex-1"></div>
                                    <button type="submit" id="submit_btn" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:opacity-90 transition-all font-bold shadow-lg shadow-primary/20">
                                        <i class="fas fa-save mr-2"></i> Add Rule to Architect
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Active Rules List -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-50">
                                <h3 class="text-lg font-bold text-gray-800">Active Discount Rules</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider font-bold">
                                            <th class="px-6 py-4 w-16">#</th>
                                            <th class="px-6 py-4">Scope</th>
                                            <th class="px-6 py-4">Target / Conditions</th>
                                            <th class="px-6 py-4">Type</th>
                                            <th class="px-6 py-4">Value</th>
                                            <th class="px-6 py-4">Weight</th>
                                            <th class="px-6 py-4 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php if (empty($rules)): ?>
                                            <tr>
                                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                                    <i class="fas fa-info-circle text-3xl mb-3 block"></i>
                                                    No discount rules found.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $i = 1; foreach ($rules as $rule): ?>
                                                <tr class="hover:bg-gray-50/50 transition-colors">
                                                    <td class="px-6 py-4 font-medium text-gray-400">
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                                            <?php echo ucwords(str_replace('_', ' ', $rule['scope'])); ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-600">
                                                        <?php 
                                                            if ($rule['scope'] === 'global') {
                                                                echo "All Products";
                                                            } else {
                                                                echo $rule['target_display'];
                                                                if ($rule['threshold']) echo " | Min: " . $rule['threshold'];
                                                                if ($rule['threshold_max']) echo " | Max: " . $rule['threshold_max'];
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span class="text-sm font-medium <?php echo $rule['type'] === 'percentage' ? 'text-purple-600' : 'text-orange-600'; ?>">
                                                            <?php echo ucfirst($rule['type']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 font-bold text-gray-800">
                                                        <?php echo $rule['value']; ?><?php echo $rule['type'] === 'percentage' ? '%' : ''; ?>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span class="font-mono text-primary font-bold">#<?php echo $rule['weight']; ?></span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <div class="flex justify-end gap-3">
                                                            <a href="javascript:void(0)" 
                                                               onclick='editRule(<?php echo json_encode($rule); ?>)' 
                                                               class="text-blue-400 hover:text-blue-600 transition-colors">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $rule['id']; ?>)" class="text-red-400 hover:text-red-600 transition-colors">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="tab-settings" class="tab-content hidden">
                        <!-- Global Settings Form -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-sliders-h text-primary mr-2"></i> Global Label Settings
                                </h3>
                            </div>
                            <form action="index.php?controller=discount" method="POST" class="p-6">
                                <input type="hidden" name="action" value="update_settings">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Discount Label Position</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <?php 
                                            $positions = [
                                                'top_left' => 'Top Left',
                                                'top_right' => 'Top Right',
                                                'bottom_left' => 'Bottom Left',
                                                'bottom_right' => 'Bottom Right'
                                            ];
                                            $current_pos = $settings['da_badge_position'] ?? 'top_left';
                                            foreach ($positions as $val => $label): 
                                            ?>
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="da_badge_position" value="<?php echo $val; ?>" <?php echo $current_pos === $val ? 'checked' : ''; ?> class="hidden peer">
                                                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-center text-sm font-medium text-gray-600 peer-checked:bg-primary/10 peer-checked:border-primary peer-checked:text-primary transition-all">
                                                        <?php echo $label; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Visibility Options</label>
                                        <div class="space-y-3 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                            <?php 
                                            $options = [
                                                'da_show_on_shop' => 'Shop Page',
                                                'da_show_on_archive' => 'Category/Archive Pages',
                                                'da_show_on_single' => 'Single Product Page',
                                                'da_show_on_related' => 'Related Products',
                                                'da_show_on_search' => 'Search Results',
                                                'da_show_on_cart' => 'Cart Page'
                                            ];
                                            foreach ($options as $key => $label): 
                                            ?>
                                                <label class="flex items-center group cursor-pointer">
                                                    <div class="relative flex items-center">
                                                        <input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo ($settings[$key] ?? '1') === '1' ? 'checked' : ''; ?> class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-gray-300 bg-white checked:bg-primary checked:border-primary transition-all">
                                                        <i class="fas fa-check absolute text-white text-[10px] left-1 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                                                    </div>
                                                    <span class="ml-3 text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors"><?php echo $label; ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-primary text-white rounded-xl hover:opacity-90 transition-all font-bold shadow-lg shadow-primary/20">
                                        <i class="fas fa-save mr-2"></i> Save Global Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Views/partials/scripts.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scopeSelect = document.getElementById('da_scope');
            const searchInput = document.getElementById('da_search');
            const resultsDropdown = document.getElementById('da_results');
            const targetsInput = document.getElementById('da_targets_input');
            const targetsUI = document.getElementById('da_targets_ui');

            let selectedItems = [];
            let searchTimeout = null;

            // Initialize tabs
            window.switchTab = function(tabId) {
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                document.getElementById(`tab-${tabId}`).classList.remove('hidden');
            };

            // Scope Change Handler
            scopeSelect.addEventListener('change', function() {
                handleScopeChange(this.value);
            });

            function handleScopeChange(scope) {
                const targetsCont = document.getElementById('targets_container');
                const threshCont = document.getElementById('threshold_container');
                const threshMaxCont = document.getElementById('threshold_max_container');
                const threshLabel = document.getElementById('threshold_label');

                // Hide all first
                targetsCont.classList.add('hidden');
                threshCont.classList.add('hidden');
                threshMaxCont.classList.add('hidden');

                if (scope === 'global') return;

                if (scope === 'product' || scope === 'category') {
                    targetsCont.classList.remove('hidden');
                } else if (scope.includes('price')) {
                    threshCont.classList.remove('hidden');
                    if (scope.includes('cat_')) {
                        targetsCont.classList.remove('hidden');
                    }
                    if (scope.includes('between')) {
                        threshMaxCont.classList.remove('hidden');
                        threshLabel.textContent = 'Min Price Threshold';
                    } else {
                        threshLabel.textContent = 'Price Threshold';
                    }
                }
                
                // Clear selected items if scope changes significantly
                // selectedItems = [];
                // renderSelectedItems();
            }

            // Search Logic
            searchInput.addEventListener('input', function() {
                const term = this.value.trim();
                const type = scopeSelect.value.includes('category') || scopeSelect.value.includes('cat_') ? 'category' : 'product';

                clearTimeout(searchTimeout);
                if (term.length < 2) {
                    resultsDropdown.style.display = 'none';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`index.php?controller=discount&action=search&q=${encodeURIComponent(term)}&type=${type}`)
                        .then(res => res.json())
                        .then(data => {
                            renderResults(data.results);
                        });
                }, 300);
            });

            function renderResults(results) {
                resultsDropdown.innerHTML = '';
                if (results.length === 0) {
                    resultsDropdown.innerHTML = '<div class="p-3 text-sm text-gray-500">No results found</div>';
                } else {
                    results.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'dropdown-item';
                        div.textContent = item.text;
                        div.onclick = () => addItem(item);
                        resultsDropdown.appendChild(div);
                    });
                }
                resultsDropdown.style.display = 'block';
            }

            function addItem(item) {
                if (!selectedItems.find(i => i.id === item.id)) {
                    selectedItems.push(item);
                    renderSelectedItems();
                }
                searchInput.value = '';
                resultsDropdown.style.display = 'none';
            }

            function removeItem(id) {
                selectedItems = selectedItems.filter(i => i.id !== id);
                renderSelectedItems();
            }

            function renderSelectedItems() {
                // Clear all except search input
                const input = targetsUI.querySelector('.search-input');
                targetsUI.innerHTML = '';
                
                selectedItems.forEach(item => {
                    const chip = document.createElement('div');
                    chip.className = 'selected-chip';
                    chip.innerHTML = `
                        ${item.text}
                        <span class="remove-chip" onclick="event.stopPropagation(); window.removeDaItem('${item.id}')">&times;</span>
                    `;
                    targetsUI.appendChild(chip);
                });

                targetsUI.appendChild(input);
                targetsInput.value = selectedItems.map(i => i.id).join(',');
                input.focus();
            }

            window.removeDaItem = removeItem;

            window.editRule = function(rule) {
                console.log('Editing rule:', rule);
                
                // Set form state
                document.getElementById('da_action').value = 'update_rule';
                document.getElementById('da_rule_id').value = rule.id;
                document.getElementById('submit_btn').innerHTML = '<i class="fas fa-sync mr-2"></i> Update Rule';
                document.getElementById('edit_notice').classList.remove('hidden');
                
                // Populate fields
                scopeSelect.value = rule.scope;
                handleScopeChange(rule.scope);
                
                document.getElementById('da_type').value = rule.type;
                document.getElementById('da_value').value = rule.value;
                document.getElementById('da_weight').value = rule.weight;
                document.getElementById('da_threshold').value = rule.threshold || '';
                document.getElementById('da_threshold_max').value = rule.threshold_max || '';
                
                // Populate targets
                selectedItems = rule.target_objects || [];
                renderSelectedItems();
                
                // Scroll to form
                document.getElementById('da_rule_form').scrollIntoView({ behavior: 'smooth' });
            };

            window.resetForm = function() {
                document.getElementById('da_rule_form').reset();
                document.getElementById('da_action').value = 'add_rule';
                document.getElementById('da_rule_id').value = '';
                document.getElementById('submit_btn').innerHTML = '<i class="fas fa-save mr-2"></i> Add Rule to Architect';
                document.getElementById('edit_notice').classList.add('hidden');
                
                selectedItems = [];
                renderSelectedItems();
                handleScopeChange('global');
            };

            // Click outside to close dropdown
            document.addEventListener('click', function(e) {
                if (!document.getElementById('target-select-wrapper').contains(e.target)) {
                    resultsDropdown.style.display = 'none';
                }
            });

            // Initial call
            handleScopeChange(scopeSelect.value);
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6e8efb',
                cancelButtonColor: '#f87171',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `index.php?controller=discount&delete_id=${id}`;
                }
            })
        }
    </script>
</body>
</html>
