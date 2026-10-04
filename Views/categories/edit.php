<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Category - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        :root {
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        body {
            font-family: var(--font-stack) !important;
            background-color: #fafafa !important;
            color: #09090b !important;
            font-size: 13px !important;
        }
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
            margin-bottom: 6px;
        }
        .field-input {
            width: 100%;
            height: 36px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 13px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
            font-family: inherit;
        }
        .field-input:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }
        .field-textarea {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 13px;
            color: #09090b;
            outline: none;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
            font-family: inherit;
            resize: vertical;
        }
        .field-textarea:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 1px #09090b;
        }
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 14px;
            height: 36px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            color: #09090b;
            transition: all 0.12s ease;
            text-decoration: none;
            font-family: inherit;
        }
        .shadcn-btn:hover {
            background: #f4f4f5;
        }
        .shadcn-btn-primary {
            background: #09090b !important;
            border-color: #09090b !important;
            color: #ffffff !important;
        }
        .shadcn-btn-primary:hover {
            background: #27272a !important;
            border-color: #27272a !important;
        }
    </style>
</head>
<body class="bg-zinc-50 font-sans text-zinc-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Edit Category';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-zinc-50/50">
                <div class="max-w-2xl mx-auto">
                    <!-- Navigation Breadcrumb -->
                    <div class="mb-4">
                        <a href="index.php?controller=category&action=index" class="text-xs text-zinc-500 hover:text-zinc-900 inline-flex items-center gap-1.5 transition-colors">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>Back to Category Hierarchy</span>
                        </a>
                    </div>

                    <div class="shadcn-card">
                        <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-zinc-900">
                                    Edit <?php 
                                    if ($type === 'jewel_cat') echo 'Jewellery Category';
                                    elseif ($type === 'jewel_sub') echo 'Jewellery Subcategory';
                                    elseif ($type === 'garment_cat') echo 'Garment Category';
                                    else echo 'Garment Subcategory';
                                    ?>
                                </h2>
                                <p class="text-xs text-zinc-500 mt-0.5">Modify category name, description, image, or parent mapping.</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-zinc-100 text-zinc-600 border border-zinc-200">
                                #<?php echo htmlspecialchars($id); ?>
                            </span>
                        </div>

                        <form action="index.php?controller=category&action=update" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                            <?php if (strpos($type, 'sub') !== false): ?>
                            <div>
                                <label class="field-label">Parent Category <span class="text-red-500">*</span></label>
                                <select name="parent_id" required class="field-input">
                                    <option value="">Select Parent Category</option>
                                    <?php foreach ($parents as $p): ?>
                                        <option value="<?php echo $p['id']; ?>" <?php echo $p['id'] == ($category['parent_id'] ?? '') ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($p['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="field-label">Category Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required class="field-input">
                                </div>
                                <div>
                                    <label class="field-label">Category Banner / Image</label>
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($category['image'])): ?>
                                            <div class="w-9 h-9 rounded border border-zinc-200 overflow-hidden flex-shrink-0 bg-zinc-50">
                                                <img src="../../uploads/categories/<?php echo htmlspecialchars($category['image']); ?>" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="category_image" class="block w-full text-xs text-zinc-500 file:mr-3 file:py-1 file:px-2.5 file:rounded file:border file:border-zinc-200 file:text-xs file:font-medium file:bg-zinc-50 file:text-zinc-700 hover:file:bg-zinc-100 cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="field-label">Description</label>
                                <textarea name="description" rows="4" placeholder="Optional category overview or SEO description..." class="field-textarea"><?php echo htmlspecialchars($category['desc'] ?? ''); ?></textarea>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2.5">
                                <a href="index.php?controller=category&action=index" class="shadcn-btn">
                                    Cancel
                                </a>
                                <button type="submit" class="shadcn-btn shadcn-btn-primary">
                                    <i class="fas fa-check text-xs"></i>
                                    <span>Update Category</span>
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
