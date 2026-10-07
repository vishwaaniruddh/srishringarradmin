<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Product - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* ==========================================================================
           ShadCN UI Standard - Add Product Page
           ========================================================================== */
        body {
            background-color: #f8fafc !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            color: #0f172a !important;
        }

        /* Top Sticky Bar */
        .product-sticky-bar {
            position: sticky;
            top: 0;
            z-index: 35;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
            will-change: transform, opacity;
        }
        .product-sticky-bar.bar--hidden {
            transform: translateY(-135%);
            opacity: 0;
            pointer-events: none;
        }
        .btn-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.65rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-back-link:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .type-badge-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.2rem 0.65rem;
            border-radius: 6px;
        }
        .quick-anchor-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.55rem;
            font-size: 0.7rem;
            font-weight: 500;
            color: #64748b;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s;
        }
        .quick-anchor-pill:hover {
            color: #0f172a;
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-top-submit {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            background: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid #0f172a !important;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            white-space: nowrap;
        }
        .btn-top-submit:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
            transform: translateY(-1px);
        }

        /* Card Surfaces */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.15s ease;
        }
        .shadcn-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Section Headers */
        .section-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-hdr-left {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .section-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }
        .section-sub {
            font-size: 0.72rem;
            color: #64748b;
            margin: 0.15rem 0 0 0;
        }

        /* Form Labels & Controls */
        .field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }
        .field-hint {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .field-input {
            width: 100%;
            height: 38px;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 0.45rem 0.75rem !important;
            font-size: 0.8125rem !important;
            color: #0f172a !important;
            outline: none !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            box-sizing: border-box;
        }
        .field-input:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.08) !important;
        }
        .field-input--textarea {
            height: auto !important;
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }
        .field-input:disabled {
            background: #f8fafc !important;
            color: #64748b !important;
            cursor: not-allowed;
            border-color: #e2e8f0 !important;
        }

        /* Currency Input Wrapper */
        .price-wrap {
            position: relative;
        }
        .price-wrap .price-sym {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 600;
            pointer-events: none;
        }
        .price-wrap .field-input {
            padding-left: 1.75rem !important;
        }

        /* Type Tab Switcher */
        .type-tab-btn {
            flex: 1;
            padding: 0.55rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: all 0.15s;
        }
        .type-tab-btn:hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .type-tab-btn--active {
            background: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Info Card */
        .info-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0.75rem 1rem !important;
        }

        /* Color Chips & Tags */
        .color-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .color-chip:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
        }
        .color-chip--active {
            background: #f1f5f9 !important;
            border-color: #0f172a !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        .color-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }
        .color-tag-selected {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .color-tag-selected .color-remove-btn {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 0.75rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.15s;
        }
        .color-tag-selected .color-remove-btn:hover {
            color: #ef4444;
        }

        /* Fixed Bottom Action Bar */
        .edit-footer {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 40 !important;
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(12px) !important;
            border-top: 1px solid #e2e8f0 !important;
            padding: 0.75rem 1.5rem !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 1rem !important;
            box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.05) !important;
            border-radius: 0 !important;
            flex-wrap: wrap;
        }
        @media (min-width: 1024px) {
            .edit-footer {
                left: 256px !important;
                padding: 0.75rem 2rem !important;
            }
        }
        .btn-cancel {
            padding: 0.45rem 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .btn-cancel:hover {
            color: #0f172a;
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-submit {
            padding: 0.45rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            background: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid #0f172a !important;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-submit:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Add New Product';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-3 sm:p-5" style="scroll-behavior: smooth; padding-bottom: 6.5rem;">
                <div class="max-w-[1400px] mx-auto">
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Sticky Top Action & Quick Navigation Bar -->
                    <div class="product-sticky-bar">
                        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                            <a href="index.php?controller=product&action=index" class="btn-back-link" title="Back to All Products">
                                <i class="fas fa-arrow-left"></i>
                                <span class="hidden sm:inline">Products</span>
                            </a>
                            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-semibold text-slate-800">New Product</span>
                                <span class="type-badge-pill" id="stickyTypePill">Jewellery</span>
                            </div>

                            <!-- Quick Section Jump Navigation -->
                            <nav class="hidden md:flex items-center gap-1 ml-2">
                                <a href="#section_verification" class="quick-anchor-pill"><i class="fas fa-barcode text-slate-400"></i> Verification</a>
                                <a href="#section_basic" class="quick-anchor-pill"><i class="fas fa-info-circle text-slate-400"></i> Basic</a>
                                <a href="#section_pricing" class="quick-anchor-pill"><i class="fas fa-tag text-slate-400"></i> Pricing</a>
                                <a href="#section_images" class="quick-anchor-pill"><i class="fas fa-images text-slate-400"></i> Media</a>
                                <a href="#section_categories" class="quick-anchor-pill"><i class="fas fa-sitemap text-slate-400"></i> Categories</a>
                                <a href="#section_colors" class="quick-anchor-pill"><i class="fas fa-palette text-slate-400"></i> Colors</a>
                            </nav>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="submit" form="productAddForm" class="btn-top-submit" id="btnSubmitProductTop">
                                <i class="fas fa-save mr-1" id="submitBtnIconTop"></i> <span id="submitBtnTextTop">Save Product</span>
                                <kbd class="hidden xl:inline-flex ml-1.5 text-[10px] bg-slate-800 text-slate-200 px-1 py-0.5 rounded font-mono font-bold">Ctrl+S</kbd>
                            </button>
                        </div>
                    </div>

                    <form action="index.php?controller=product&action=store" method="POST" enctype="multipart/form-data" id="productAddForm">
                        <input type="hidden" name="type" id="product_type" value="jewellery">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            
                            <!-- Left / Main Column (7 cols on lg, 8 cols on xl) -->
                            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                                <!-- Card 1: POS Verification & Price Source -->
                                <div class="shadcn-card" id="section_verification">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-barcode"></i>
                                            </div>
                                            <div>
                                                <h3 class="section-title">POS Verification & Pricing Mode</h3>
                                                <p class="section-sub">Verify product SKU against POS system or toggle manual web pricing</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price Source Toggle -->
                                    <div class="info-card mb-4">
                                        <div class="flex items-center justify-between gap-4 flex-wrap">
                                            <div>
                                                <span class="text-xs font-semibold text-slate-800 block">Price Calculation Source</span>
                                                <span class="text-[11px] text-slate-500" id="price_source_description">Prices are auto-calculated from POS inventory system.</span>
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <span class="text-xs font-semibold text-slate-800" id="label_pos">POS</span>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="price_source" value="manual" class="sr-only peer" id="price_source_toggle" onchange="togglePriceSource()">
                                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                                                </label>
                                                <span class="text-xs font-semibold text-slate-400" id="label_manual">Manual</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                        <div class="sm:col-span-8">
                                            <label class="field-label">SKU / Product Code <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="text" name="code" id="sku_input" required placeholder="e.g. HP99, NECK102..." class="field-input">
                                                <div id="sku_loader" class="hidden absolute right-3 top-2.5 text-slate-600 animate-spin">
                                                    <i class="fas fa-spinner"></i>
                                                </div>
                                            </div>
                                            <p id="sku_message" class="mt-1.5 text-xs"></p>
                                        </div>
                                        <div class="sm:col-span-4">
                                            <button type="button" onclick="verifySKU()" id="verify_sku_btn" class="w-full h-[38px] bg-white border border-slate-200 text-slate-800 rounded-md text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                                <i class="fas fa-check-circle text-xs text-slate-600"></i> Verify SKU
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="form_content" class="opacity-50 pointer-events-none transition-all duration-300 space-y-6">
                                    
                                    <!-- Card 2: Basic Information -->
                                    <div class="shadcn-card" id="section_basic">
                                        <div class="section-hdr">
                                            <div class="section-hdr-left">
                                                <div class="section-icon">
                                                    <i class="fas fa-info"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">Basic Information</h3>
                                                    <p class="section-sub">Product title, marketing description, size and details</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="field-label">Product Name / Title <span class="text-red-500">*</span></label>
                                                <input type="text" name="name" required placeholder="e.g. Simple Gold Kundan Choker Necklace with White Stones" class="field-input">
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <label class="field-label mb-0">Product Description</label>
                                                    <span class="text-[11px] text-slate-400">Plain text or structured bullet points</span>
                                                </div>
                                                <textarea name="description" rows="6" placeholder="Enter comprehensive product description, styling details, materials, and occasion suitability..." class="field-input field-input--textarea"></textarea>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="field-label">Size / Dimensions</label>
                                                    <input type="text" name="size_avail" placeholder="e.g. Free Size, Adjustable, 2.4, 2.6..." class="field-input">
                                                </div>
                                                <div>
                                                    <label class="field-label">Brand Name</label>
                                                    <input type="text" name="brand_name" value="Sri Shringarr" placeholder="Sri Shringarr" class="field-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 3: Pricing & Availability -->
                                    <div class="shadcn-card" id="section_pricing">
                                        <div class="section-hdr">
                                            <div class="section-hdr-left">
                                                <div class="section-icon">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">Pricing & Availability</h3>
                                                    <p class="section-sub">Configure sales price, rental fees, and availability nature</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Availability Nature -->
                                        <div class="info-card mb-4 flex items-center justify-between flex-wrap gap-3">
                                            <div>
                                                <span class="text-xs font-semibold text-slate-800 block">Product Nature</span>
                                                <span class="text-[11px] text-slate-500">Determine whether this item is rentable, for sale, or both.</span>
                                            </div>
                                            <div class="w-full sm:w-48">
                                                <select name="availability" class="field-input" style="height: 34px !important; font-size: 0.75rem !important;">
                                                    <option value="both" selected>Rent & Sell (Both)</option>
                                                    <option value="rent">Rent Only</option>
                                                    <option value="sell">Sell Only</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Pricing Inputs Grid -->
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="pricing_fields">
                                            <div>
                                                <label class="field-label">Sales Price</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="s_price" step="0.01" placeholder="0.00" class="field-input">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="field-label">Rental Price</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="rental_price" step="0.01" placeholder="0.00" class="field-input">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="field-label">Security Deposit</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="deposit" step="0.01" placeholder="0.00" class="field-input">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="pos_price_note" class="info-card mt-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <p class="text-[11px] text-slate-600 m-0 flex items-center gap-1.5">
                                                <i class="fas fa-info-circle text-slate-500"></i>
                                                <span>In <strong>POS mode</strong>, prices are auto-derived from the POS system upon checkout and storefront display.</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Card 4: Product Images & Media -->
                                    <div class="shadcn-card" id="section_images">
                                        <div class="section-hdr">
                                            <div class="section-hdr-left">
                                                <div class="section-icon">
                                                    <i class="fas fa-images"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">Product Media & Gallery</h3>
                                                    <p class="section-sub">Upload high-resolution images (PNG, JPG, WEBP)</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span id="img_counter_badge" class="hidden text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                    0 Images
                                                </span>
                                                <button type="button" id="btn_clear_images" onclick="clearAllUploadedImages()" class="hidden text-xs text-red-600 hover:text-red-700 font-medium">
                                                    Clear All
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Upload Dropzone -->
                                        <div id="image_dropzone" class="border-2 border-dashed border-slate-200 hover:border-slate-400 rounded-xl p-6 sm:p-8 text-center transition-all bg-slate-50/50 cursor-pointer">
                                            <input type="file" name="images[]" id="img_upload" multiple accept="image/*" class="hidden">
                                            <label for="img_upload" class="cursor-pointer block">
                                                <div class="w-12 h-12 mx-auto mb-2.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center">
                                                    <i class="fas fa-cloud-upload-alt text-lg"></i>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-800">Click to browse or drag & drop product photos</p>
                                                <p class="text-[11px] text-slate-500 mt-1">PNG, JPG, WEBP formats supported. First image serves as primary cover.</p>
                                            </label>
                                        </div>

                                        <!-- Image Previews Container -->
                                        <div id="img_preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-4"></div>
                                    </div>

                                    <!-- Card 5: AI Copywriter & Assistant (OpenAI / Gemini) -->
                                    <div class="shadcn-card" id="section_ai_tools">
                                        <div class="section-hdr">
                                            <div class="section-hdr-left">
                                                <div class="section-icon">
                                                    <i class="fas fa-magic text-slate-700"></i>
                                                </div>
                                                <div>
                                                    <h3 class="section-title">AI Copywriter & Vision Assistant</h3>
                                                    <p class="section-sub">Generate attractive titles and luxury descriptions from uploaded image</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between gap-4 mb-3 pb-3 border-b border-slate-100 flex-wrap">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold text-slate-700">AI Engine:</span>
                                                <select id="aiEngineSelectAdd" class="field-input" style="height: 32px !important; width: auto; font-size: 0.75rem !important;">
                                                    <option value="gemini" selected>Google Gemini (Gemini Flash)</option>
                                                    <option value="openai">OpenAI (ChatGPT-4o Mini)</option>
                                                </select>
                                            </div>
                                            <span class="text-[11px] text-slate-400">Uses first uploaded image as visual reference</span>
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap">
                                            <button type="button" onclick="aiGenerateAddDetails('name')" id="aiBtnAddName" class="btn-cancel text-xs">
                                                <i class="fas fa-heading"></i> Suggest Title
                                            </button>
                                            <button type="button" onclick="aiGenerateAddDetails('desc')" id="aiBtnAddDesc" class="btn-cancel text-xs">
                                                <i class="fas fa-align-left"></i> Generate Description
                                            </button>
                                        </div>

                                        <div id="aiAddLoading" class="hidden mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600 flex items-center justify-center gap-2">
                                            <i class="fas fa-spinner fa-spin text-slate-800"></i>
                                            <span>AI is analyzing uploaded photo...</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Right / Sidebar Column (5 cols on lg, 4 cols on xl) -->
                            <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                                <!-- Card A: Product Type & Status -->
                                <div class="shadcn-card">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-sliders-h"></i>
                                            </div>
                                            <div>
                                                <h3 class="section-title">Product Classification</h3>
                                                <p class="section-sub">Choose catalog type and promotional status</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="field-label">Category Type</label>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="switchTab('jewellery')" id="tab-jewellery" class="type-tab-btn type-tab-btn--active">
                                                    <i class="fas fa-gem"></i> Jewellery
                                                </button>
                                                <button type="button" onclick="switchTab('garments')" id="tab-garments" class="type-tab-btn">
                                                    <i class="fas fa-tshirt"></i> Garments
                                                </button>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-slate-100">
                                            <label class="relative inline-flex items-center cursor-pointer group">
                                                <input type="checkbox" name="featured" value="1" class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                                                <span class="ml-2.5 text-xs font-semibold text-slate-800">Featured Product</span>
                                            </label>
                                            <p class="text-[11px] text-slate-500 mt-1 ml-11">Display in Featured Showcase on home & landing pages.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card B: Categories & Subcategories Tree -->
                                <div class="shadcn-card" id="section_categories">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-sitemap"></i>
                                            </div>
                                            <div>
                                                <h3 class="section-title">Categories Tree</h3>
                                                <p class="section-sub">Multi-select taxonomies for navigation</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jewellery Category Tree -->
                                    <div id="jewellery_cats">
                                        <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                            <span id="wpCategoryCounterJewel" class="text-[11px] font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                                0 Selected
                                            </span>
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <button type="button" onclick="wpToggleAllCategories('jewellery_cats', true)" class="text-slate-600 hover:text-slate-900 text-xs font-medium underline">Select All</button>
                                                <span class="text-slate-300">•</span>
                                                <button type="button" onclick="wpToggleAllCategories('jewellery_cats', false)" class="text-slate-600 hover:text-slate-900 text-xs font-medium underline">Clear</button>
                                            </div>
                                        </div>

                                        <div class="relative mb-2.5">
                                            <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                            <input type="text" oninput="wpFilterCategoryTree('jewellery_cats', this.value)" placeholder="Filter categories..." class="field-input pl-8" style="height: 32px !important; padding-left: 2rem !important; font-size: 0.75rem !important;">
                                        </div>

                                        <div class="wpCategoryTree max-h-72 overflow-y-auto bg-slate-50 border border-slate-200 rounded-lg p-2 flex flex-col gap-1.5">
                                            <?php if (!empty($allJewelCategoriesTree)): ?>
                                                <?php foreach ($allJewelCategoriesTree as $catIndex => $mainCat): ?>
                                                    <?php $subList = $mainCat['subcategories'] ?? []; ?>
                                                    <div class="wp-cat-node bg-white border border-slate-200 rounded-md p-2 transition-all" data-cat-name="<?php echo htmlspecialchars(strtolower($mainCat['name'])); ?>">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer select-none">
                                                                <input type="checkbox" name="categories[]" value="<?php echo $mainCat['id']; ?>" onchange="wpUpdateCatCounter()" class="wp-cat-check w-3.5 h-3.5 accent-slate-900 cursor-pointer">
                                                                <span class="wp-cat-title"><?php echo htmlspecialchars($mainCat['name']); ?></span>
                                                            </label>
                                                            <?php if (!empty($subList)): ?>
                                                                <button type="button" onclick="wpToggleSubTree('jewel_sub_tree_<?php echo $catIndex; ?>', this)" class="bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-semibold px-1.5 py-0.5 rounded cursor-pointer flex items-center gap-1">
                                                                    <span><?php echo count($subList); ?></span>
                                                                    <i class="fas fa-chevron-right text-[8px]"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>

                                                        <?php if (!empty($subList)): ?>
                                                            <div id="jewel_sub_tree_<?php echo $catIndex; ?>" class="wp-sub-tree hidden mt-2 pt-2 border-t border-slate-100 pl-4 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                                <?php foreach ($subList as $sub): ?>
                                                                    <label class="wp-sub-node flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer p-1 rounded hover:bg-slate-50" data-sub-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>">
                                                                        <input type="checkbox" name="sub_categories[]" value="<?php echo $sub['id']; ?>" onchange="wpUpdateCatCounter()" class="wp-sub-check w-3.5 h-3.5 accent-slate-900 cursor-pointer">
                                                                        <span><?php echo htmlspecialchars($sub['name']); ?></span>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Garments Category Tree -->
                                    <div id="garment_cats" class="hidden">
                                        <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                                            <span id="wpCategoryCounterGarment" class="text-[11px] font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                                0 Selected
                                            </span>
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <button type="button" onclick="wpToggleAllCategories('garment_cats', true)" class="text-slate-600 hover:text-slate-900 text-xs font-medium underline">Select All</button>
                                                <span class="text-slate-300">•</span>
                                                <button type="button" onclick="wpToggleAllCategories('garment_cats', false)" class="text-slate-600 hover:text-slate-900 text-xs font-medium underline">Clear</button>
                                            </div>
                                        </div>

                                        <div class="relative mb-2.5">
                                            <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                            <input type="text" oninput="wpFilterCategoryTree('garment_cats', this.value)" placeholder="Filter apparel types..." class="field-input pl-8" style="height: 32px !important; padding-left: 2rem !important; font-size: 0.75rem !important;">
                                        </div>

                                        <div class="wpCategoryTree max-h-72 overflow-y-auto bg-slate-50 border border-slate-200 rounded-lg p-2 flex flex-col gap-1.5">
                                            <?php if (!empty($allGarmentCategoriesTree)): ?>
                                                <?php foreach ($allGarmentCategoriesTree as $catIndex => $mainCat): ?>
                                                    <?php $subList = $mainCat['subcategories'] ?? []; ?>
                                                    <div class="wp-cat-node bg-white border border-slate-200 rounded-md p-2 transition-all" data-cat-name="<?php echo htmlspecialchars(strtolower($mainCat['name'])); ?>">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer select-none">
                                                                <input type="checkbox" name="categories[]" value="<?php echo $mainCat['id']; ?>" onchange="wpUpdateCatCounter()" class="wp-cat-check w-3.5 h-3.5 accent-slate-900 cursor-pointer">
                                                                <span class="wp-cat-title"><?php echo htmlspecialchars($mainCat['name']); ?></span>
                                                            </label>
                                                            <?php if (!empty($subList)): ?>
                                                                <button type="button" onclick="wpToggleSubTree('garment_sub_tree_<?php echo $catIndex; ?>', this)" class="bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-semibold px-1.5 py-0.5 rounded cursor-pointer flex items-center gap-1">
                                                                    <span><?php echo count($subList); ?></span>
                                                                    <i class="fas fa-chevron-right text-[8px]"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>

                                                        <?php if (!empty($subList)): ?>
                                                            <div id="garment_sub_tree_<?php echo $catIndex; ?>" class="wp-sub-tree hidden mt-2 pt-2 border-t border-slate-100 pl-4 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                                <?php foreach ($subList as $sub): ?>
                                                                    <label class="wp-sub-node flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer p-1 rounded hover:bg-slate-50" data-sub-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>">
                                                                        <input type="checkbox" name="sub_categories[]" value="<?php echo $sub['id']; ?>" onchange="wpUpdateCatCounter()" class="wp-sub-check w-3.5 h-3.5 accent-slate-900 cursor-pointer">
                                                                        <span><?php echo htmlspecialchars($sub['name']); ?></span>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card C: Product Colors -->
                                <div class="shadcn-card" id="section_colors">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-palette"></i>
                                            </div>
                                            <div>
                                                <h3 class="section-title">Product Colors</h3>
                                                <p class="section-sub">Tag all available colors for filtering</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span id="selectedColorsCounter" class="text-[11px] font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                                0 Selected
                                            </span>
                                            <button type="button" onclick="clearAllColors()" class="text-xs text-slate-400 hover:text-slate-600 font-medium underline">Clear</button>
                                        </div>
                                    </div>

                                    <!-- Selected Colors Display -->
                                    <div id="selectedColorsContainer" class="flex flex-wrap gap-1.5 min-h-[38px] p-2 bg-slate-50 border border-slate-200 rounded-lg mb-2.5 items-center"></div>

                                    <!-- Color Search Bar -->
                                    <div class="relative mb-3">
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                                <input type="text" id="colorSearchInput" placeholder="Search color or type custom name..." class="field-input pl-8" style="height: 32px !important; padding-left: 2rem !important; font-size: 0.75rem !important;" onfocus="showColorDropdown()" oninput="filterColorDropdown()" onkeydown="handleColorInputKey(event)">
                                            </div>
                                            <button type="button" onclick="addCustomColorFromInput()" class="btn-cancel text-xs" style="height: 32px;">
                                                <i class="fas fa-plus text-[10px]"></i> Add
                                            </button>
                                        </div>

                                        <div id="colorDropdownList" class="hidden absolute top-[calc(100%+4px)] left-0 right-0 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-lg p-1.5 z-50 shadow-lg"></div>
                                    </div>

                                    <!-- Quick Pick Chips -->
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Popular Colors:</span>
                                        <div id="quickPickColors" class="flex flex-wrap gap-1"></div>
                                    </div>

                                    <div id="hiddenColorInputs"></div>
                                </div>

                            </div>
                        </div>

                        <!-- Fixed Bottom Footer Action Bar -->
                        <div class="edit-footer">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-500">Creating new product</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs text-slate-500"><kbd class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded text-[10px] border border-slate-200 font-mono font-semibold">Ctrl+S</kbd> to save</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="index.php?controller=product&action=index" class="btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn-submit" id="btnSubmitProduct">
                                    <i class="fas fa-save mr-1" id="submitBtnIcon"></i> <span id="submitBtnText">Save Product</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
    <script>
        // Tab Switcher between Jewellery and Garments
        function switchTab(type) {
            const tabs = {
                jewellery: { btn: 'tab-jewellery', cats: 'jewellery_cats' },
                garments: { btn: 'tab-garments', cats: 'garment_cats' }
            };

            document.getElementById('product_type').value = type;
            const stickyPill = document.getElementById('stickyTypePill');
            if (stickyPill) {
                stickyPill.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            }
            
            ['jewellery', 'garments'].forEach(t => {
                const btn = document.getElementById(tabs[t].btn);
                const cats = document.getElementById(tabs[t].cats);
                if (btn) btn.classList.remove('type-tab-btn--active');
                if (cats) cats.classList.add('hidden');
            });

            const activeBtn = document.getElementById(tabs[type].btn);
            const activeCats = document.getElementById(tabs[type].cats);
            if (activeBtn) activeBtn.classList.add('type-tab-btn--active');
            if (activeCats) activeCats.classList.remove('hidden');
        }

        // SKU Verification with POS System
        async function verifySKU() {
            const sku = document.getElementById('sku_input').value.trim();
            const message = document.getElementById('sku_message');
            const loader = document.getElementById('sku_loader');
            const content = document.getElementById('form_content');
            const priceSource = document.getElementById('price_source_toggle').checked ? 'manual' : 'pos';

            if (!sku) {
                alert('Please enter a product SKU/code first.');
                return;
            }
            loader.classList.remove('hidden');
            message.textContent = '';

            try {
                const response = await fetch(`index.php?controller=product&action=checkSku&sku=${encodeURIComponent(sku)}&price_source=${priceSource}`);
                const data = await response.json();

                if (data.allowed) {
                    message.className = 'mt-1.5 text-xs text-emerald-600 font-semibold';
                    message.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${data.message}`;
                    content.classList.remove('opacity-50', 'pointer-events-none');
                } else {
                    message.className = 'mt-1.5 text-xs text-rose-600 font-semibold';
                    message.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
                    content.classList.add('opacity-50', 'pointer-events-none');
                }
            } catch (error) {
                message.className = 'mt-1.5 text-xs text-rose-600 font-semibold';
                message.textContent = 'Error verifying SKU with server.';
            } finally {
                loader.classList.add('hidden');
            }
        }

        // Toggle Price Source between POS and Manual
        function togglePriceSource() {
            const toggle = document.getElementById('price_source_toggle');
            const isManual = toggle.checked;
            const desc = document.getElementById('price_source_description');
            const labelPos = document.getElementById('label_pos');
            const labelManual = document.getElementById('label_manual');
            const posNote = document.getElementById('pos_price_note');
            const content = document.getElementById('form_content');
            const message = document.getElementById('sku_message');

            if (isManual) {
                desc.textContent = 'Prices are set manually. POS validation is optional.';
                labelPos.className = 'text-xs font-semibold text-slate-400';
                labelManual.className = 'text-xs font-semibold text-slate-900';
                if (posNote) posNote.classList.add('hidden');
                // Unlock form in manual mode
                content.classList.remove('opacity-50', 'pointer-events-none');
                message.className = 'mt-1.5 text-xs text-amber-600 font-medium';
                message.innerHTML = '<i class="fas fa-info-circle mr-1"></i> Manual pricing mode — proceed with form entry.';
            } else {
                desc.textContent = 'Prices are auto-calculated from POS inventory system.';
                labelPos.className = 'text-xs font-semibold text-slate-900';
                labelManual.className = 'text-xs font-semibold text-slate-400';
                if (posNote) posNote.classList.remove('hidden');
                // Re-lock in POS mode until verified
                content.classList.add('opacity-50', 'pointer-events-none');
                message.className = 'mt-1.5 text-xs';
                message.textContent = '';
            }
        }

        // Category tree counters & styling
        function wpUpdateCatCounter() {
            ['jewellery_cats', 'garment_cats'].forEach(containerId => {
                const container = document.getElementById(containerId);
                if (!container) return;
                
                let mainChecked = 0;
                let subChecked = 0;

                container.querySelectorAll('.wp-cat-node').forEach(node => {
                    const mainCb = node.querySelector('.wp-cat-check');
                    if (mainCb && mainCb.checked) {
                        mainChecked++;
                        node.style.background = '#fefce8';
                        node.style.borderColor = '#fde047';
                    } else {
                        node.style.background = '#ffffff';
                        node.style.borderColor = '#e2e8f0';
                    }

                    node.querySelectorAll('.wp-sub-node').forEach(subNode => {
                        const subCb = subNode.querySelector('.wp-sub-check');
                        if (subCb && subCb.checked) {
                            subChecked++;
                            subNode.style.background = '#fef9c3';
                            subNode.style.color = '#713f12';
                            subNode.style.fontWeight = '700';
                        } else {
                            subNode.style.background = 'transparent';
                            subNode.style.color = '#475569';
                            subNode.style.fontWeight = '400';
                        }
                    });
                });

                const counterId = containerId === 'jewellery_cats' ? 'wpCategoryCounterJewel' : 'wpCategoryCounterGarment';
                const counter = document.getElementById(counterId);
                if (counter) {
                    const total = mainChecked + subChecked;
                    counter.textContent = `${total} Selected`;
                }
            });
        }

        function wpToggleSubTree(treeId, btn) {
            const tree = document.getElementById(treeId);
            if (!tree) return;
            const icon = btn.querySelector('i');
            if (tree.classList.contains('hidden')) {
                tree.classList.remove('hidden');
                if (icon) icon.className = 'fas fa-chevron-down text-[8px]';
            } else {
                tree.classList.add('hidden');
                if (icon) icon.className = 'fas fa-chevron-right text-[8px]';
            }
        }

        function wpToggleAllCategories(containerId, check) {
            const container = document.getElementById(containerId);
            if (container) {
                container.querySelectorAll('.wp-cat-check, .wp-sub-check').forEach(cb => {
                    cb.checked = check;
                });
                wpUpdateCatCounter();
            }
        }

        function wpFilterCategoryTree(containerId, query) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const q = (query || '').trim().toLowerCase();

            container.querySelectorAll('.wp-cat-node').forEach(node => {
                const catName = node.getAttribute('data-cat-name') || '';
                let hasMatchingSub = false;
                node.querySelectorAll('.wp-sub-node').forEach(subNode => {
                    const subName = subNode.getAttribute('data-sub-name') || '';
                    if (!q || subName.includes(q) || catName.includes(q)) {
                        subNode.style.display = 'flex';
                        hasMatchingSub = true;
                    } else {
                        subNode.style.display = 'none';
                    }
                });

                if (!q || catName.includes(q) || hasMatchingSub) {
                    node.style.display = 'block';
                } else {
                    node.style.display = 'none';
                }
            });
        }

        // Multi-image upload, dropzone & preview
        let uploadedFiles = [];
        const imgUploadInput = document.getElementById('img_upload');
        const imgDropzone = document.getElementById('image_dropzone');
        const imgPreview = document.getElementById('img_preview');
        const imgCounterBadge = document.getElementById('img_counter_badge');
        const btnClearImages = document.getElementById('btn_clear_images');

        function syncInputFiles() {
            if (!imgUploadInput) return;
            try {
                const dt = new DataTransfer();
                uploadedFiles.forEach(file => dt.items.add(file));
                imgUploadInput.files = dt.files;
            } catch (e) {
                console.warn('DataTransfer not supported:', e);
            }
        }

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function renderImagePreviews() {
            if (!imgPreview) return;
            imgPreview.innerHTML = '';
            const count = uploadedFiles.length;

            if (imgCounterBadge) {
                if (count > 0) {
                    imgCounterBadge.textContent = `${count} ${count === 1 ? 'Image' : 'Images'}`;
                    imgCounterBadge.classList.remove('hidden');
                } else {
                    imgCounterBadge.classList.add('hidden');
                }
            }

            if (btnClearImages) {
                if (count > 0) {
                    btnClearImages.classList.remove('hidden');
                } else {
                    btnClearImages.classList.add('hidden');
                }
            }

            uploadedFiles.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'relative aspect-[3/4] rounded-lg overflow-hidden border border-slate-200 bg-slate-900 group shadow-sm transition-all hover:shadow-md hover:border-slate-400';
                const objectUrl = URL.createObjectURL(file);

                card.innerHTML = `
                    <img src="${objectUrl}" alt="${escapeHtml(file.name)}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-black/60 text-white backdrop-blur-sm border border-white/10">
                                #${index + 1}
                            </span>
                            <button type="button" onclick="removeUploadedImage(${index})" class="w-5 h-5 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition-transform hover:scale-110 shadow-lg cursor-pointer" title="Remove image">
                                <i class="fas fa-times text-[9px]"></i>
                            </button>
                        </div>
                        <div class="text-[10px] text-white">
                            <p class="truncate font-medium">${escapeHtml(file.name)}</p>
                            <p class="text-zinc-400 text-[9px]">${formatBytes(file.size)}</p>
                        </div>
                    </div>
                    ${index === 0 ? `
                        <div class="absolute top-1.5 left-1.5 z-10">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-900 text-white shadow-md flex items-center gap-1">
                                <i class="fas fa-star text-[7px] text-amber-400"></i> Cover
                            </span>
                        </div>
                    ` : ''}
                `;
                imgPreview.appendChild(card);
            });

            syncInputFiles();
        }

        function handleFilesAdded(newFileList) {
            if (!newFileList || newFileList.length === 0) return;
            const validFiles = Array.from(newFileList).filter(file => file.type.startsWith('image/'));
            if (validFiles.length === 0) {
                alert('Please select valid image files (PNG, JPG, WEBP).');
                return;
            }
            uploadedFiles = [...uploadedFiles, ...validFiles];
            renderImagePreviews();
        }

        function removeUploadedImage(index) {
            if (index >= 0 && index < uploadedFiles.length) {
                uploadedFiles.splice(index, 1);
                renderImagePreviews();
            }
        }

        function clearAllUploadedImages() {
            uploadedFiles = [];
            renderImagePreviews();
        }

        if (imgUploadInput) {
            imgUploadInput.addEventListener('change', function(e) {
                handleFilesAdded(this.files);
            });
        }

        if (imgDropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                imgDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    imgDropzone.classList.add('border-slate-800', 'bg-slate-100');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                imgDropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    imgDropzone.classList.remove('border-slate-800', 'bg-slate-100');
                }, false);
            });

            imgDropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    handleFilesAdded(dt.files);
                }
            }, false);
        }

        // Color Multi-Select Logic
        const allAvailableColors = <?php echo json_encode($availableColors ?? []); ?>;
        let selectedColors = [];

        const colorHexMap = {
            'antique gold': '#d97706', 'azure blue': '#0284c7', 'baby pink': '#f472b6',
            'beige': '#d4c5a9', 'black': '#0f172a', 'blue': '#2563eb', 'bottle green': '#064e3b',
            'brown': '#78350f', 'coral': '#fb7185', 'cream': '#fef3c7', 'dark gold': '#b45309',
            'emerald green': '#059669', 'fuchsia pink': '#db2777', 'gold': '#eab308',
            'golden': '#eab308', 'green': '#22c55e', 'kundan': '#fef08a', 'maroon': '#881337',
            'multicolor': 'linear-gradient(135deg, #ef4444, #eab308, #22c55e, #3b82f6)',
            'navy blue': '#1e3a8a', 'off white': '#f5f5f4', 'pink': '#ec4899', 'red': '#dc2626',
            'rose gold': '#f43f5e', 'ruby': '#e11d48', 'silver': '#94a3b8', 'white': '#ffffff',
            'yellow': '#eab308'
        };

        const popularQuickPickColors = [
            'Gold', 'Silver', 'Rose Gold', 'Antique Gold', 'Red', 'Maroon', 
            'Ruby', 'Green', 'Emerald Green', 'Pink', 'White', 'Kundan', 'Yellow', 'Blue', 'Black', 'Multicolor'
        ];

        function getColorSwatch(colorName) {
            const key = String(colorName || '').trim().toLowerCase();
            return colorHexMap[key] || '#94a3b8';
        }

        function renderSelectedColors() {
            const container = document.getElementById('selectedColorsContainer');
            const counter = document.getElementById('selectedColorsCounter');
            const hiddenInputs = document.getElementById('hiddenColorInputs');
            if (!container || !counter || !hiddenInputs) return;

            counter.textContent = `${selectedColors.length} Selected`;

            if (selectedColors.length === 0) {
                container.innerHTML = `<span style="font-size: 0.72rem; color: #94a3b8; font-style: italic; padding: 0.2rem 0.4rem;">
                    No colors selected. Pick from popular colors below.
                </span>`;
            } else {
                container.innerHTML = selectedColors.map((color) => {
                    const swatch = getColorSwatch(color);
                    const isGrad = swatch.includes('gradient');
                    const bgStyle = isGrad ? `background: ${swatch};` : `background-color: ${swatch};`;
                    return `
                        <div class="color-tag-selected">
                            <span class="color-dot" style="${bgStyle}"></span>
                            <span>${escapeHtml(color)}</span>
                            <button type="button" class="color-remove-btn" onclick="removeColor('${escapeJsStr(color)}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }).join('');
            }

            let inputsHtml = selectedColors.map(c => `<input type="hidden" name="colors[]" value="${escapeHtml(c)}">`).join('');
            inputsHtml += `<input type="hidden" name="brand_color" value='${escapeHtml(JSON.stringify(selectedColors))}'>`;
            hiddenInputs.innerHTML = inputsHtml;

            renderQuickPickPills();
        }

        function renderQuickPickPills() {
            const container = document.getElementById('quickPickColors');
            if (!container) return;

            container.innerHTML = popularQuickPickColors.map(color => {
                const isSelected = selectedColors.some(c => c.toLowerCase() === color.toLowerCase());
                const swatch = getColorSwatch(color);
                const isGrad = swatch.includes('gradient');
                const bgStyle = isGrad ? `background: ${swatch};` : `background-color: ${swatch};`;
                return `
                    <button type="button" onclick="toggleColor('${escapeJsStr(color)}')" class="color-chip ${isSelected ? 'color-chip--active' : ''}">
                        <span class="color-dot" style="${bgStyle}"></span>
                        <span>${escapeHtml(color)}</span>
                        ${isSelected ? '<i class="fas fa-check" style="font-size: 0.6rem; color: #0f172a; margin-left: 2px;"></i>' : ''}
                    </button>
                `;
            }).join('');
        }

        function toggleColor(color) {
            const trimmed = color.trim();
            if (!trimmed) return;
            const index = selectedColors.findIndex(c => c.toLowerCase() === trimmed.toLowerCase());
            if (index > -1) {
                selectedColors.splice(index, 1);
            } else {
                selectedColors.push(trimmed);
            }
            renderSelectedColors();
        }

        function removeColor(color) {
            selectedColors = selectedColors.filter(c => c.toLowerCase() !== color.trim().toLowerCase());
            renderSelectedColors();
        }

        function clearAllColors() {
            selectedColors = [];
            renderSelectedColors();
        }

        function showColorDropdown() {
            filterColorDropdown();
            const dropdown = document.getElementById('colorDropdownList');
            if (dropdown) dropdown.classList.remove('hidden');
        }

        function filterColorDropdown() {
            const input = document.getElementById('colorSearchInput');
            const dropdown = document.getElementById('colorDropdownList');
            if (!input || !dropdown) return;

            const q = input.value.trim().toLowerCase();
            const combinedColors = Array.from(new Set([...allAvailableColors, ...popularQuickPickColors]));
            combinedColors.sort((a, b) => a.localeCompare(b));
            const filtered = combinedColors.filter(c => !q || c.toLowerCase().includes(q));

            let html = '';
            if (filtered.length > 0) {
                html = filtered.map(c => {
                    const isSelected = selectedColors.some(sc => sc.toLowerCase() === c.toLowerCase());
                    const swatch = getColorSwatch(c);
                    const isGrad = swatch.includes('gradient');
                    const bgStyle = isGrad ? `background: ${swatch};` : `background-color: ${swatch};`;
                    return `
                        <div onclick="toggleColor('${escapeJsStr(c)}'); event.stopPropagation();" 
                             style="display: flex; align-items: center; justify-content: space-between; padding: 0.4rem 0.6rem; border-radius: 6px; cursor: pointer; transition: background 0.15s; ${isSelected ? 'background: #f1f5f9; color: #0f172a; font-weight: 600;' : 'color: #334155;'}"
                             onmouseover="this.style.background='#f8fafc'" 
                             onmouseout="this.style.background='${isSelected ? '#f1f5f9' : 'transparent'}'">
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.74rem;">
                                <span class="color-dot" style="${bgStyle}"></span>
                                <span>${escapeHtml(c)}</span>
                            </div>
                            ${isSelected ? '<i class="fas fa-check" style="font-size: 0.65rem; color: #0f172a;"></i>' : '<i class="fas fa-plus" style="font-size: 0.6rem; color: #94a3b8;"></i>'}
                        </div>
                    `;
                }).join('');
            }

            if (q && !combinedColors.some(c => c.toLowerCase() === q)) {
                html += `
                    <div onclick="addCustomColorFromInput(); event.stopPropagation();" 
                         style="display: flex; align-items: center; gap: 0.5rem; padding: 0.45rem 0.65rem; border-radius: 6px; cursor: pointer; background: #eff6ff; color: #1d4ed8; font-size: 0.74rem; font-weight: 600; margin-top: 0.25rem;">
                        <i class="fas fa-plus-circle" style="color: #3b82f6;"></i>
                        <span>Add custom color: "<strong>${escapeHtml(input.value.trim())}</strong>"</span>
                    </div>
                `;
            }

            if (!html) {
                html = '<div style="padding: 0.6rem; text-align: center; color: #94a3b8; font-size: 0.75rem;">No matching colors found.</div>';
            }

            dropdown.innerHTML = html;
            dropdown.classList.remove('hidden');
        }

        function handleColorInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomColorFromInput();
            } else if (e.key === 'Escape') {
                const dropdown = document.getElementById('colorDropdownList');
                if (dropdown) dropdown.classList.add('hidden');
            }
        }

        function addCustomColorFromInput() {
            const input = document.getElementById('colorSearchInput');
            if (!input) return;
            const val = input.value.trim();
            if (val) {
                const formatted = val.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
                if (!selectedColors.some(c => c.toLowerCase() === formatted.toLowerCase())) {
                    selectedColors.push(formatted);
                    renderSelectedColors();
                }
                input.value = '';
                const dropdown = document.getElementById('colorDropdownList');
                if (dropdown) dropdown.classList.add('hidden');
            }
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function escapeJsStr(str) {
            return String(str || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }

        document.addEventListener('click', (e) => {
            const searchWrap = document.getElementById('colorSearchInput')?.parentElement?.parentElement;
            if (searchWrap && !searchWrap.contains(e.target)) {
                const dropdown = document.getElementById('colorDropdownList');
                if (dropdown) dropdown.classList.add('hidden');
            }
        });

        // AI Copywriter from uploaded image (Supports OpenAI and Gemini)
        async function aiGenerateAddDetails(targetType) {
            if (!uploadedFiles || uploadedFiles.length === 0) {
                alert('Please upload or drag at least one product photo into the dropzone first.');
                return;
            }

            const loader = document.getElementById('aiAddLoading');
            const provider = document.getElementById('aiEngineSelectAdd')?.value || 'gemini';
            const productType = document.getElementById('product_type')?.value || 'jewellery';
            const file = uploadedFiles[0];

            loader.classList.remove('hidden');

            try {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = async () => {
                    const base64Data = reader.result;
                    // Send to AI endpoint
                    const response = await fetch('index.php?controller=product&action=aiAnalyzeUpload', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            image: base64Data,
                            target: targetType,
                            provider: provider,
                            type: productType
                        })
                    });
                    const res = await response.json();
                    loader.classList.add('hidden');

                    if (res.success) {
                        if (targetType === 'name' && res.result) {
                            const nameInput = document.querySelector('input[name="name"]');
                            if (nameInput) {
                                nameInput.value = res.result;
                                nameInput.focus();
                            }
                        } else if (targetType === 'desc' && res.result) {
                            const descInput = document.querySelector('textarea[name="description"]');
                            if (descInput) {
                                descInput.value = res.result;
                                descInput.focus();
                            }
                        }
                    } else {
                        alert(res.error || 'AI generation failed.');
                    }
                };
            } catch (err) {
                loader.classList.add('hidden');
                alert('Failed to analyze image: ' + err);
            }
        }

        // Smart Hide on Scroll Down, Show on Scroll Up for product-sticky-bar
        (function() {
            const stickyBar = document.querySelector('.product-sticky-bar');
            const mainContainer = document.querySelector('main.overflow-y-auto') || document.querySelector('main');
            if (!stickyBar) return;

            let lastScrollY = 0;
            let ticking = false;

            function updateStickyBarVisibility(currentY) {
                if (currentY <= 50) {
                    stickyBar.classList.remove('bar--hidden');
                } else if (currentY > lastScrollY + 8) {
                    stickyBar.classList.add('bar--hidden');
                } else if (currentY < lastScrollY - 8) {
                    stickyBar.classList.remove('bar--hidden');
                }
                lastScrollY = Math.max(0, currentY);
            }

            function handleScroll(e) {
                const target = e.target;
                const scrollY = (target && target.scrollTop !== undefined && target !== document)
                    ? target.scrollTop
                    : (window.pageYOffset || document.documentElement.scrollTop || 0);

                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        updateStickyBarVisibility(scrollY);
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            if (mainContainer) {
                mainContainer.addEventListener('scroll', handleScroll, { passive: true });
            }
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();

        // Keyboard Shortcut: Ctrl+S to save
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                const form = document.getElementById('productAddForm');
                if (form) form.requestSubmit();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            wpUpdateCatCounter();
            renderSelectedColors();
        });
    </script>
</body>
</html>
