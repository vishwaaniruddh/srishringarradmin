<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($product['name'] ?? 'Product Details'); ?> - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Exact ShadCN UI Standards (Matching yn/admin & products catalog) */
        :root {
            --wp-dark: #09090b;
            --wp-blue: #2563eb;
            --wp-border: #e4e4e7;
            --wp-bg: #fafafa;
            --wp-card: #ffffff;
            --wp-text: #09090b;
            --wp-text-muted: #71717a;
            --font-stack: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            font-family: var(--font-stack) !important;
            background-color: var(--wp-bg) !important;
            color: var(--wp-text) !important;
            font-size: 13px !important;
            line-height: 1.5 !important;
        }

        .page-container {
            max-width: 1320px;
            margin: 0 auto;
        }

        /* Card Surfaces */
        .card-surface {
            background: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        /* Shadcn Buttons */
        .shadcn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 500;
            font-size: 12.5px;
            height: 32px;
            padding: 0 12px;
            border-radius: 6px;
            transition: all 0.12s ease;
            text-decoration: none !important;
            cursor: pointer;
            white-space: nowrap;
            line-height: 1;
        }
        .shadcn-btn-primary {
            background-color: #09090b !important;
            color: #ffffff !important;
            border: 1px solid #09090b !important;
        }
        .shadcn-btn-primary:hover {
            background-color: #27272a !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }
        .shadcn-btn-outline {
            background-color: #ffffff !important;
            color: #09090b !important;
            border: 1px solid #e4e4e7 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .shadcn-btn-outline:hover {
            background-color: #f4f4f5 !important;
            border-color: #d4d4d8 !important;
        }

        /* Shadcn Badges */
        .shadcn-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            line-height: 1.2;
            background: #f4f4f5;
            color: #18181b;
            border: 1px solid #e4e4e7;
        }

        /* 2-Column Responsive Layout */
        .product-detail-layout {
            display: flex;
            gap: 24px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .product-detail-sidebar {
            flex: 0 0 380px;
            width: 380px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .product-detail-main {
            flex: 1 1 520px;
            min-width: 320px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        @media (max-width: 900px) {
            .product-detail-sidebar {
                flex: 1 1 100%;
                width: 100%;
            }
        }

        /* Media Gallery (Strictly Constrained) */
        .gallery-viewport {
            width: 100%;
            height: 380px;
            background: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #f4f4f5;
            padding: 16px;
        }
        .gallery-main-img {
            max-height: 348px;
            max-width: 100%;
            object-fit: contain;
            transition: opacity 0.15s ease, transform 0.2s ease;
        }
        .gallery-viewport:hover .gallery-main-img {
            transform: scale(1.02);
        }

        .thumb-btn {
            width: 58px;
            height: 58px;
            border-radius: 6px;
            border: 1px solid #e4e4e7;
            background: #ffffff;
            padding: 3px;
            cursor: pointer;
            transition: all 0.12s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .thumb-btn:hover {
            border-color: #a1a1aa;
        }
        .thumb-btn.active {
            border-color: #09090b;
            box-shadow: 0 0 0 1.5px #09090b;
        }
        .thumb-btn img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 4px;
        }

        /* Spec Table Rows */
        .spec-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f4f4f5;
            font-size: 13px;
            gap: 12px;
        }
        .spec-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .spec-label {
            color: #71717a;
            font-weight: 450;
            flex-shrink: 0;
        }
        .spec-val {
            color: #09090b;
            font-weight: 500;
            text-align: right;
            word-break: break-word;
        }
    </style>
</head>
<body>

    <div class="flex min-h-screen">
        <!-- Shared Dark Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Shared Topbar -->
            <?php 
            $pageTitle = 'Product Details';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8 bg-gray-50/50">
                <div class="page-container">

                    <!-- Top Action Bar & Breadcrumbs -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 14px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <a href="index.php?controller=product&action=index" class="shadcn-btn shadcn-btn-outline" style="height: 32px; padding: 0 10px;" title="Back to Products Catalog">
                                <i class="fa-solid fa-arrow-left" style="font-size: 11px;"></i>
                                <span>All Products</span>
                            </a>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #71717a;">
                                <span>Catalog</span>
                                <span style="color: #d4d4d8;">/</span>
                                <span style="text-transform: capitalize;"><?php echo htmlspecialchars($type); ?></span>
                                <span style="color: #d4d4d8;">/</span>
                                <span style="color: #09090b; font-weight: 500; font-family: monospace;">SKU: <?php echo htmlspecialchars($product['code']); ?></span>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <?php 
                            $slug = strtolower($product['name'] ?? '');
                            $slug = preg_replace('/[^\w\s-]/u', '', $slug);
                            $slug = preg_replace('/[\s_-]+/', '-', $slug);
                            $slug = trim($slug, '-');
                            $livePreviewUrl = "https://srishringarr.com/product/" . ($slug ?: 'product') . "-" . (int)$product['id'];
                            ?>
                            <a href="<?php echo $livePreviewUrl; ?>" target="_blank" rel="noopener" class="shadcn-btn shadcn-btn-outline" title="Open product page on live website">
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                                <span>Storefront Preview</span>
                            </a>
                            <a href="index.php?controller=product&action=edit&id=<?php echo $product['id']; ?>&type=<?php echo $type; ?>" class="shadcn-btn shadcn-btn-primary" title="Edit this product">
                                <i class="fa-solid fa-pen-to-square" style="font-size: 11px;"></i>
                                <span>Edit Product</span>
                            </a>
                        </div>
                    </div>

                    <!-- Main 2-Column Responsive Layout -->
                    <div class="product-detail-layout">
                        
                        <!-- ==================== LEFT COLUMN: GALLERY & INVENTORY (380px) ==================== -->
                        <div class="product-detail-sidebar">
                            
                            <!-- Media Gallery Card -->
                            <div class="card-surface" style="padding: 16px;">
                                <!-- Primary Viewport -->
                                <div class="gallery-viewport">
                                    <?php 
                                    $firstImage = !empty($images) ? 'https://srishringarr.com/yn/uploads' . $images[0]['img_name'] : 'assets/default-product.jpg';
                                    ?>
                                    <img id="main-image" src="<?php echo htmlspecialchars($firstImage); ?>" 
                                         class="gallery-main-img" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">

                                    <!-- Featured Ribbon Badge -->
                                    <?php if(!empty($product['featured'])): ?>
                                        <div style="position: absolute; top: 12px; left: 12px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="fa-solid fa-star" style="font-size: 10px;"></i> Featured
                                        </div>
                                    <?php endif; ?>

                                    <!-- Image Count Pill -->
                                    <?php if(count($images) > 1): ?>
                                        <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(9, 9, 11, 0.75); backdrop-filter: blur(4px); color: #ffffff; font-size: 10.5px; font-weight: 600; padding: 2px 7px; border-radius: 4px;">
                                            <i class="fa-regular fa-images" style="margin-right: 3px;"></i> <?php echo count($images); ?> photos
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Thumbnails Row -->
                                <?php if(count($images) > 1): ?>
                                    <div style="display: flex; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f4f4f5; overflow-x: auto; padding-bottom: 2px;">
                                        <?php foreach($images as $idx => $img): 
                                            $fullUrl = 'https://srishringarr.com/yn/uploads' . $img['img_name'];
                                        ?>
                                            <button type="button" 
                                                    onclick="changeMainImage('<?php echo htmlspecialchars($fullUrl); ?>', this)" 
                                                    class="thumb-btn <?php echo $idx === 0 ? 'active' : ''; ?>"
                                                    title="View image <?php echo $idx + 1; ?>">
                                                <img src="<?php echo htmlspecialchars($fullUrl); ?>" alt="Thumbnail <?php echo $idx + 1; ?>">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Inventory Status Card -->
                            <div class="card-surface" style="padding: 16px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                    <span style="font-size: 11px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em;">
                                        Inventory &amp; Channel
                                    </span>
                                    <?php 
                                    $qty = (int)($product['quantity'] ?? 0);
                                    if ($qty > 0): ?>
                                        <span style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                                            In Stock (<?php echo $qty; ?> Units)
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span>
                                            Out of Stock
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">POS Stock Quantity</span>
                                    <span class="spec-val" style="font-weight: 700;"><?php echo $qty; ?> available</span>
                                </div>
                                <div class="spec-row">
                                    <span class="spec-label">Availability Mode</span>
                                    <span class="spec-val" style="text-transform: capitalize;">
                                        <?php 
                                        $avail = $product['availability'] ?? 'both';
                                        echo ($avail === 'both') ? 'Rent &amp; Buy' : ucfirst($avail);
                                        ?>
                                    </span>
                                </div>
                                <div class="spec-row">
                                    <span class="spec-label">Price Source</span>
                                    <span class="spec-val" style="text-transform: uppercase;">
                                        <?php echo htmlspecialchars($product['price_source'] ?? 'POS'); ?>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- ==================== RIGHT COLUMN: INFO & SPECS (Flexible) ==================== -->
                        <div class="product-detail-main">
                            
                            <!-- Product Identification Header Card -->
                            <div class="card-surface" style="padding: 20px;">
                                <h1 style="font-size: 20px; font-weight: 700; color: #09090b; letter-spacing: -0.02em; line-height: 1.35; margin: 0 0 12px 0;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h1>

                                <!-- Metadata Tags Row -->
                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <span class="shadcn-badge" style="font-family: monospace; font-weight: 600;">
                                        <i class="fa-solid fa-barcode" style="margin-right: 4px; color: #71717a;"></i>
                                        <?php echo htmlspecialchars($product['code']); ?>
                                    </span>
                                    <span class="shadcn-badge">
                                        <?php echo ($type === 'garments' ? 'G-ID #' : 'ID #') . $product['id']; ?>
                                    </span>
                                    <span class="shadcn-badge" style="text-transform: capitalize;">
                                        <i class="fa-solid fa-layer-group" style="margin-right: 4px; color: #71717a;"></i>
                                        <?php echo htmlspecialchars($type); ?>
                                    </span>
                                    <?php if(!empty($product['category_name'])): ?>
                                        <span class="shadcn-badge" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                                            <i class="fa-solid fa-tag" style="margin-right: 4px;"></i>
                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Pricing Matrix Card (3-Column ShadCN Stat Row) -->
                            <div class="card-surface" style="padding: 16px;">
                                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; text-align: center;">
                                    <!-- Sales Price -->
                                    <div style="padding: 4px 8px;">
                                        <span style="font-size: 10.5px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 4px;">
                                            Sales Price
                                        </span>
                                        <span style="font-size: 20px; font-weight: 700; color: #09090b; letter-spacing: -0.02em;">
                                            ₹<?php echo number_format((float)($product['s_price'] ?? 0), 2); ?>
                                        </span>
                                    </div>

                                    <!-- Rental Price -->
                                    <div style="padding: 4px 8px; border-left: 1px solid #f4f4f5; border-right: 1px solid #f4f4f5;">
                                        <span style="font-size: 10.5px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 4px;">
                                            Rental Price (3D)
                                        </span>
                                        <span style="font-size: 20px; font-weight: 700; color: #2563eb; letter-spacing: -0.02em;">
                                            ₹<?php echo number_format((float)($product['rental_price'] ?? 0), 2); ?>
                                        </span>
                                    </div>

                                    <!-- Security Deposit -->
                                    <div style="padding: 4px 8px;">
                                        <span style="font-size: 10.5px; font-weight: 600; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 4px;">
                                            Deposit
                                        </span>
                                        <span style="font-size: 20px; font-weight: 700; color: #52525b; letter-spacing: -0.02em;">
                                            ₹<?php echo number_format((float)($product['deposit'] ?? 0), 2); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Specifications Card -->
                            <div class="card-surface" style="padding: 18px;">
                                <h3 style="font-size: 13px; font-weight: 600; color: #09090b; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Specifications &amp; Taxonomy
                                </h3>

                                <div class="spec-row">
                                    <span class="spec-label">Primary Category</span>
                                    <span class="spec-val"><?php echo htmlspecialchars($product['category_name'] ?? 'Unassigned'); ?></span>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">Subcategory / Styles</span>
                                    <div class="spec-val">
                                        <?php 
                                        $subTags = array_filter(array_map('trim', explode(',', $product['subcategory_name'] ?? '')));
                                        if (!empty($subTags)): 
                                        ?>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end;">
                                                <?php foreach ($subTags as $tag): ?>
                                                    <span class="shadcn-badge" style="font-size: 10.5px; padding: 1px 6px;">
                                                        <?php echo htmlspecialchars($tag); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #a1a1aa;">N/A</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">Brand / Collection</span>
                                    <span class="spec-val"><?php echo htmlspecialchars($product['brand_name'] ?? 'Srishringarr'); ?></span>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">Colors</span>
                                    <div class="spec-val">
                                        <?php 
                                        $colorsList = $product['colors'] ?? [];
                                        if (!empty($colorsList)): 
                                        ?>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end;">
                                                <?php foreach ($colorsList as $c): ?>
                                                    <span class="shadcn-badge" style="font-size: 10.5px; padding: 1px 6px;">
                                                        <?php echo htmlspecialchars($c); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #a1a1aa;">Standard / As Shown</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">Size Availability</span>
                                    <span class="spec-val"><?php echo htmlspecialchars($product['size_avail'] ?? 'Free Size / Standard'); ?></span>
                                </div>

                                <div class="spec-row">
                                    <span class="spec-label">Public Storefront URL</span>
                                    <div class="spec-val" style="display: flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                        <a href="<?php echo $livePreviewUrl; ?>" target="_blank" rel="noopener" style="color: #2563eb; text-decoration: none; font-size: 12px; font-weight: 500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                            Visit Web Page <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 9px; margin-left: 2px;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Card -->
                            <div class="card-surface" style="padding: 18px;">
                                <h3 style="font-size: 13px; font-weight: 600; color: #09090b; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.04em;">
                                    Product Description
                                </h3>
                                <div style="font-size: 13px; color: #334155; line-height: 1.6;">
                                    <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : '<span style="color: #a1a1aa; font-style: italic;">No product description has been entered yet.</span>'; ?>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        function changeMainImage(url, btn) {
            const main = document.getElementById('main-image');
            if (!main) return;
            
            // Set thumbnail active state
            document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            main.style.opacity = '0';
            setTimeout(() => {
                main.src = url;
                main.style.opacity = '1';
            }, 120);
        }
    </script>
</body>
</html>
