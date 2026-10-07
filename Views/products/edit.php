<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        /* Remove number input spinners */
        #aiDescMaxWords::-webkit-inner-spin-button,
        #aiDescMaxWords::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        #aiDescMaxWords {
            -moz-appearance: textfield;
        }

        /* Edit Page Wrapper */
        .edit-wrap {
            max-width: 1360px;
            margin: 0 auto;
            width: 100%;
        }

        /* Sticky Action & Navigation Top Bar */
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
        .sku-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            letter-spacing: 0.03em;
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
        .quick-anchor-pill--ai:hover {
            color: #db2777;
            background: #fdf2f8;
            border-color: #fbcfe8;
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

        /* Clean Card Surfaces */
        .shadcn-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.25rem;
        }
        .section-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .section-hdr-left {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
            flex-shrink: 0;
        }
        .section-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.2;
        }
        .section-desc {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 2px;
        }

        /* Form Controls */
        .field-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }
        .field-input {
            width: 100%;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.82rem !important;
            color: #0f172a !important;
            transition: all 0.15s ease;
            outline: none;
        }
        .field-input:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        }
        .field-input:disabled, .field-input[readonly] {
            color: #64748b !important;
            cursor: not-allowed;
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        .field-input--textarea {
            min-height: 110px;
            resize: vertical;
            line-height: 1.5;
        }

        /* Info Card */
        .info-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0.75rem 1rem !important;
            box-shadow: none !important;
        }

        /* Price wrap */
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

        /* Media & Gallery */
        .img-card-item {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.15s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            position: relative;
        }
        .img-card-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }
        .img-thumb {
            aspect-ratio: 1;
            position: relative;
            width: 100%;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .img-thumb .img-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            opacity: 0;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-thumb:hover .img-overlay {
            opacity: 1;
        }
        .img-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.35rem 0.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        .img-order-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .img-order-input {
            width: 42px;
            height: 24px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            color: #0f172a;
            font-weight: 600;
            font-size: 0.78rem;
            text-align: center;
            outline: none;
            transition: all 0.15s;
            -moz-appearance: textfield;
        }
        .img-order-input::-webkit-outer-spin-button,
        .img-order-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .img-order-input:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.1);
        }
        .img-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            z-index: 5;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .img-del-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ef4444;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            cursor: pointer;
            opacity: 0;
            transition: all 0.15s;
            z-index: 10;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .img-thumb:hover .img-del-btn {
            opacity: 1;
        }
        .img-set-main {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            cursor: pointer;
            opacity: 0;
            transition: all 0.15s;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .img-thumb:hover .img-set-main {
            opacity: 1;
        }

        /* Upload Zone */
        .upload-zone {
            border: 2px dashed #cbd5e1 !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s;
            background: #f8fafc !important;
            box-shadow: none !important;
        }
        .upload-zone:hover {
            border-color: #94a3b8 !important;
            background: #f1f5f9 !important;
        }

        /* Category Tree */
        .wp-cat-node {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            transition: all 0.15s ease;
        }
        .wp-cat-node.wp-cat-selected {
            background: #fefce8 !important;
            border-color: #fde047 !important;
        }
        .wp-cat-node.wp-cat-selected .wp-cat-title {
            color: #713f12 !important;
        }
        .wp-cat-node.wp-cat-selected .wp-cat-icon {
            color: #ca8a04 !important;
        }
        .wp-sub-node {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }
        .wp-sub-node.wp-sub-selected {
            background: #fef9c3 !important;
            border-color: #fde047 !important;
            color: #713f12 !important;
            font-weight: 600 !important;
        }
        .wp-sub-node.wp-sub-selected .wp-sub-icon {
            color: #ca8a04 !important;
        }

        /* Color multi-select */
        .color-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.72rem;
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
        }
        .color-chip--active {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .color-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            border: 1px solid rgba(0, 0, 0, 0.12);
        }
        .color-tag-selected {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            transition: all 0.15s ease;
        }
        .color-tag-selected .color-remove-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.75rem;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            margin-left: 2px;
            transition: color 0.15s;
        }
        .color-tag-selected .color-remove-btn:hover {
            color: #ef4444;
        }

        /* AI Accordion & Tools */
        .ai-accordion-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-top: 0.75rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .ai-accordion-box:hover {
            border-color: #cbd5e1;
        }
        .ai-accordion-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.85rem 1.25rem;
            cursor: pointer;
            background: #fdf2f8;
            user-select: none;
            transition: background 0.15s;
            border-bottom: 1px solid #fce7f3;
        }
        .ai-accordion-hdr:hover {
            background: #fce7f3;
        }
        .ai-accordion-trigger-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            font-size: 0.68rem;
            font-weight: 600;
            background: #ffffff;
            border: 1px solid #fbcfe8;
            border-radius: 6px;
            color: #be185d;
            transition: all 0.15s;
        }
        .ai-accordion-body {
            padding: 1.25rem;
            background: #fafafa;
        }
        .ai-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 1rem !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }
        .ai-card-hdr {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.35rem;
        }
        .ai-card-hdr h4 {
            font-size: 0.75rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .ai-card-desc {
            font-size: 0.72rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }
        .ai-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.75rem;
            font-size: 0.72rem;
            font-weight: 500;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .ai-btn:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .ai-input {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 0.4rem 0.6rem !important;
            font-size: 0.75rem !important;
            color: #0f172a !important;
            outline: none;
        }
        .ai-input:focus {
            border-color: #0f172a !important;
        }

        /* Fixed Bottom Footer Action Bar */
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
                left: 256px !important; /* Offset for desktop sidebar */
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
            font-size: 0.78rem;
            font-weight: 600;
            background: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid #0f172a !important;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .btn-submit:hover {
            background: #1e293b !important;
            border-color: #1e293b !important;
            transform: translateY(-1px);
        }

        /* Success & Error Alerts */
        .alert {
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            font-size: 0.78rem;
            margin-bottom: 1rem;
        }
        .alert--success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .alert--error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        @media (max-width: 768px) {
            main {
                padding-bottom: 110px !important;
            }
            .edit-footer {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0.65rem 1rem !important;
                padding-right: 1rem !important;
                background: rgba(255, 255, 255, 0.98) !important;
                border-top: 1px solid #e2e8f0 !important;
                border-radius: 0 !important;
                box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08) !important;
                z-index: 9998 !important;
                flex-direction: column !important;
                gap: 0.45rem !important;
            }
            .edit-footer-status {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .edit-footer-actions {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
            .btn-cancel {
                flex: 1 !important;
                height: 40px !important;
                justify-content: center !important;
            }
            .btn-submit {
                flex: 2 !important;
                height: 40px !important;
                justify-content: center !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <?php 
            $pageTitle = 'Edit Product: ' . htmlspecialchars($product['code']);
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-2 lg:p-3" style="scroll-behavior: smooth; padding-bottom: 6.5rem;">
                <div class="edit-wrap" style="padding-bottom: 1.5rem;">
                    <?php if (isset($_GET['success'])): ?>
                        <?php if (isset($_GET['sync_status']) && $_GET['sync_status'] === 'synced'): ?>
                            <div class="alert alert--success flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-emerald-400"></i>
                                    <span><strong>Product updated and synced to Child Store (Yosshitaneha) successfully!</strong></span>
                                </div>
                                <span class="text-[11px] text-emerald-300 bg-emerald-950/70 border border-emerald-800/60 px-2.5 py-0.5 rounded-full font-medium">Child Store Synced</span>
                            </div>
                        <?php elseif (isset($_GET['sync_status']) && $_GET['sync_status'] === 'error'): ?>
                            <div class="alert" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #f59e0b; display: flex; align-items: center; justify-content: space-between;">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Product updated, but sync to child store issue: <?php echo htmlspecialchars($_GET['sync_msg'] ?? 'Sync failed'); ?></span>
                                </div>
                            </div>
                        <?php elseif (isset($_GET['sync_status']) && $_GET['sync_status'] === 'skipped'): ?>
                            <div class="alert alert--success">
                                <i class="fas fa-check-circle mr-1.5"></i> Product updated successfully! <span class="text-zinc-400 text-xs ml-1">(Child store sync skipped: <?php echo htmlspecialchars($_GET['sync_msg'] ?? ''); ?>)</span>
                            </div>
                        <?php elseif (isset($_GET['sync_status']) && $_GET['sync_status'] === 'not_applicable'): ?>
                            <div class="alert alert--success">
                                <i class="fas fa-check-circle mr-1.5"></i> Product updated successfully! <span class="text-zinc-400 text-xs ml-1">(Child store sync not applicable for this category)</span>
                            </div>
                        <?php else: ?>
                            <div class="alert alert--success"><i class="fas fa-check-circle mr-1"></i> Product updated successfully!</div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert--error"><i class="fas fa-exclamation-circle mr-1"></i> <?php echo htmlspecialchars($_GET['error']); ?></div>
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
                                <span class="sku-pill"><?php echo htmlspecialchars($product['code']); ?></span>
                                <span class="text-[11px] font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded"><?php echo ucfirst($type); ?></span>
                                <?php if (!empty($product['s_price'])): ?>
                                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">₹<?php echo number_format((float)$product['s_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Quick Section Jump Navigation -->
                            <nav class="hidden md:flex items-center gap-1 ml-2">
                                <a href="#section_basic" class="quick-anchor-pill"><i class="fas fa-info-circle text-slate-400"></i> Info</a>
                                <a href="#section_pricing" class="quick-anchor-pill"><i class="fas fa-tag text-slate-400"></i> Pricing</a>
                                <a href="#section_images" class="quick-anchor-pill"><i class="fas fa-images text-slate-400"></i> Gallery</a>
                                <a href="#section_categories" class="quick-anchor-pill"><i class="fas fa-sitemap text-slate-400"></i> Categories</a>
                                <a href="#section_colors" class="quick-anchor-pill"><i class="fas fa-palette text-slate-400"></i> Colors</a>
                                <a href="#section_ai_tools" onclick="expandAiToolsAccordion()" class="quick-anchor-pill quick-anchor-pill--ai"><i class="fas fa-magic text-pink-500"></i> AI Studio</a>
                            </nav>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div id="syncStatusIndicatorTop" class="hidden sm:flex items-center">
                                <?php if (!empty($isSyncApplicable)): ?>
                                    <span class="text-[11px] text-teal-700 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-md flex items-center gap-1.5" title="Category is configured for Yosshitaneha child store sync">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                        <span>Sync: <strong>Applicable</strong></span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <button type="submit" form="productEditForm" class="btn-top-submit" id="btnSubmitProductTop">
                                <?php if (!empty($isSyncApplicable)): ?>
                                    <i class="fas fa-sync-alt mr-1 text-teal-400" id="submitBtnIconTop"></i> <span id="submitBtnTextTop">Sync & Update</span>
                                <?php else: ?>
                                    <i class="fas fa-save mr-1" id="submitBtnIconTop"></i> <span id="submitBtnTextTop">Update Product</span>
                                <?php endif; ?>
                                <kbd class="hidden xl:inline-flex ml-1.5 text-[10px] bg-slate-800 text-slate-200 px-1 py-0.5 rounded font-mono font-bold">Ctrl+S</kbd>
                            </button>
                        </div>
                    </div>

                    <form action="index.php?controller=product&action=update" method="POST" enctype="multipart/form-data" onsubmit="return validateProductForm(this)" id="productEditForm">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="type" id="product_type" value="<?php echo $type; ?>">
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($product['code']); ?>">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            
                            <!-- Left / Main Column (7 cols on lg, 8 cols on xl) -->
                            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                                <!-- Card 1: General Information -->
                                <div class="shadcn-card" id="section_basic">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-info"></i>
                                            </div>
                                            <div>
                                                <div class="section-title">General Information</div>
                                                <div class="section-desc">Product title, description, and specifications</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="field-label">Product Name <span class="text-rose-500">*</span></label>
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="field-input">
                                        </div>

                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label class="field-label" style="margin-bottom:0;">Description</label>
                                                <a href="index.php?controller=product&action=descriptionCorrector" target="_blank" class="text-[11px] font-medium text-slate-500 hover:text-slate-800 flex items-center gap-1 transition-colors">
                                                    <i class="fas fa-magic text-[10px]"></i> Description Corrector
                                                </a>
                                            </div>
                                            <textarea name="description" id="product_desc_textarea" rows="6" class="field-input field-input--textarea"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                            <div>
                                                <label class="field-label">Size / Dimensions</label>
                                                <input type="text" name="size_avail" value="<?php echo htmlspecialchars($product['size_avail'] ?? ''); ?>" class="field-input" placeholder="e.g. Adjustable, 2.4, 2.6, Free Size">
                                            </div>
                                            <div>
                                                <label class="field-label">Brand</label>
                                                <input type="text" name="brand_name" value="<?php echo htmlspecialchars($product['brand_name'] ?? ''); ?>" class="field-input" placeholder="e.g. Sri Shringarr">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Pricing & Availability -->
                                <div class="shadcn-card" id="section_pricing">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                            <div>
                                                <div class="section-title">Pricing & Inventory Availability</div>
                                                <div class="section-desc">Manage sales price, rental fees, and POS auto-calculation</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <!-- Price Source -->
                                            <div class="info-card">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <label class="field-label" style="margin-bottom:0;">Price Source</label>
                                                    <div class="flex items-center gap-2">
                                                        <span class="<?php echo ($product['price_source'] ?? 'pos') === 'pos' ? 'text-slate-900 font-bold' : 'text-slate-400 font-medium'; ?> text-xs" id="label_pos">POS</span>
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" name="price_source" value="manual" 
                                                                   <?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'checked' : ''; ?> 
                                                                   class="sr-only peer" id="price_source_toggle" onchange="togglePriceSource()">
                                                            <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-slate-900"></div>
                                                        </label>
                                                        <span class="<?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'text-amber-700 font-bold' : 'text-slate-400 font-medium'; ?> text-xs" id="label_manual">Manual</span>
                                                    </div>
                                                </div>
                                                <p id="price_source_description" class="text-[11px] text-slate-500 m-0">
                                                    <?php echo ($product['price_source'] ?? 'pos') === 'manual' 
                                                        ? 'Prices are set manually from the fields below.' 
                                                        : 'Prices are auto-calculated from POS system data.'; ?>
                                                </p>
                                            </div>

                                            <!-- Availability -->
                                            <div class="info-card">
                                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                                    <label class="field-label" style="margin-bottom:0;">Availability / Nature</label>
                                                </div>
                                                <select name="availability" class="field-input h-9 text-xs font-medium">
                                                    <option value="both" <?php echo ($product['availability'] ?? 'both') === 'both' ? 'selected' : ''; ?>>Rent & Sell (Both)</option>
                                                    <option value="rent" <?php echo ($product['availability'] ?? 'both') === 'rent' ? 'selected' : ''; ?>>Rent Only</option>
                                                    <option value="sell" <?php echo ($product['availability'] ?? 'both') === 'sell' ? 'selected' : ''; ?>>Sell Only</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Price Fields -->
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="pricing_fields">
                                            <div>
                                                <label class="field-label">Sales Price</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="s_price" step="0.01" value="<?php echo $product['s_price']; ?>" class="field-input font-semibold">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="field-label">Rental Price</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="rental_price" step="0.01" value="<?php echo $product['rental_price']; ?>" class="field-input font-semibold">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="field-label">Deposit</label>
                                                <div class="price-wrap">
                                                    <span class="price-sym">₹</span>
                                                    <input type="number" name="deposit" step="0.01" value="<?php echo $product['deposit']; ?>" class="field-input font-semibold">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="pos_price_note" class="<?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'hidden' : ''; ?> p-2.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 flex items-start gap-2">
                                            <i class="fas fa-info-circle mt-0.5 text-blue-600"></i>
                                            <span>These values are stored as fallbacks and overridden by POS real-time formulas on storefront. Toggle to <strong>Manual</strong> to override.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3: Media & Gallery -->
                                <div class="shadcn-card" id="section_images">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-images"></i>
                                            </div>
                                            <div>
                                                <div class="section-title">Product Media & Gallery</div>
                                                <div class="section-desc">Manage product images, display order, and upload new media</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div id="bulkSelectActions" style="display:none;" class="flex items-center gap-2">
                                                <button type="button" onclick="selectAllImages(true)" class="btn-cancel text-xs py-1 px-2.5">
                                                    <i class="fas fa-check-square text-sky-600"></i> All
                                                </button>
                                                <button type="button" onclick="selectAllImages(false)" class="btn-cancel text-xs py-1 px-2.5">
                                                    <i class="far fa-square"></i> Clear
                                                </button>
                                                <button type="button" id="deleteSelectedBtn" onclick="deleteSelectedImages()" class="text-xs font-semibold py-1 px-3 rounded-md bg-rose-600 text-white hover:bg-rose-700 transition-colors opacity-50 cursor-not-allowed" disabled>
                                                    <i class="fas fa-trash-alt"></i> Delete (<span id="selectedCount">0</span>)
                                                </button>
                                                <button type="button" onclick="toggleBulkDeleteMode(false)" class="btn-cancel text-xs py-1 px-2">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <button type="button" id="enableBulkDeleteBtn" onclick="toggleBulkDeleteMode(true)" class="inline-flex items-center gap-1.5 text-xs font-medium py-1.5 px-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                                                <i class="fas fa-check-double text-slate-500"></i> Manage / Bulk Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Existing Images Grid -->
                                    <div id="existing_img_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 mb-4">
                                        <?php foreach ($images as $index => $img): ?>
                                            <div class="img-card-item" data-image-id="<?php echo $img['id']; ?>" onclick="handleCardClick(event, this)">
                                                <div class="img-thumb">
                                                    <label class="img-select-wrapper" style="display:none; position:absolute; top:8px; left:8px; z-index:10; cursor:pointer;">
                                                        <input type="checkbox" class="img-select-checkbox" value="<?php echo $img['id']; ?>" onchange="updateSelectedCount()" style="width:18px; height:18px; accent-color:#ef4444; cursor:pointer;">
                                                    </label>
                                                    <img src="/ss/yn/uploads<?php echo $img['img_name']; ?>" onerror="this.onerror=null; this.src='http://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>';" alt="">
                                                    <div class="img-overlay">
                                                        <span style="color:#fff; font-size:0.62rem; font-weight:600;">
                                                            <?php echo ($index === 0) ? 'Main Image' : 'Image'; ?>
                                                        </span>
                                                    </div>
                                                    <?php if ($index === 0): ?>
                                                        <div class="img-badge bg-amber-500 text-white" title="Main Image">
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <button type="button" onclick="setMainImage(this, <?php echo $img['id']; ?>)" class="img-set-main" title="Set as Main Image">
                                                            <i class="far fa-star"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" onclick="deleteProductImage(this, <?php echo $img['id']; ?>)" class="img-del-btn" title="Delete Image">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                                <div class="img-card-footer">
                                                    <span class="img-order-label"><i class="fas fa-sort-numeric-down text-slate-400"></i> Order</span>
                                                    <input type="number" min="0" name="image_weights[<?php echo $img['id']; ?>]" value="<?php echo (isset($img['rank']) && (int)$img['rank'] > 0) ? (int)$img['rank'] : $index; ?>" class="img-order-input" onchange="updateImageWeight(<?php echo $img['id']; ?>, this)">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Upload Zone -->
                                    <div class="upload-zone">
                                        <input type="file" name="images[]" id="img_upload" multiple accept="image/*" class="hidden">
                                        <label for="img_upload" style="cursor:pointer; display:block;">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-2 block"></i>
                                            <span class="text-xs font-semibold text-slate-800 block">Click to upload or drag images here</span>
                                            <span class="text-[11px] text-slate-400 block mt-1">PNG, JPG, WEBP formats supported</span>
                                        </label>
                                        <div id="img_preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 mt-3"></div>
                                    </div>
                                </div>

                                <!-- Card 4: Collapsible AI Creative Studio & Copywriter -->
                                <div class="ai-accordion-box" id="section_ai_tools">
                                    <div class="ai-accordion-hdr" onclick="toggleAiToolsAccordion()">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(236, 72, 153, 0.12); border: 1px solid rgba(236, 72, 153, 0.25); display: flex; align-items: center; justify-content: center; color: #db2777; flex-shrink: 0;">
                                                <i class="fas fa-magic" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                    <span style="font-size: 0.85rem; font-weight: 700; color: #0f172a;">AI Creative Studio & Copywriter</span>
                                                    <span style="font-size: 0.65rem; font-weight: 600; background: rgba(236, 72, 153, 0.12); border: 1px solid rgba(236, 72, 153, 0.25); color: #be185d; padding: 0.1rem 0.45rem; border-radius: 9999px;">Gemini Powered</span>
                                                </div>
                                                <span style="font-size: 0.7rem; color: #64748b; display: block; margin-top: 1px;">Generate model photos, studio mockups, smart copywriter & descriptions (Click to expand)</span>
                                            </div>
                                        </div>
                                        <div class="ai-accordion-trigger-badge">
                                            <span id="aiAccordionStateText">Show AI Studio</span>
                                            <i class="fas fa-chevron-down" id="aiAccordionChevron" style="font-size: 0.65rem; margin-left: 3px;"></i>
                                        </div>
                                    </div>

                                    <div class="ai-accordion-body hidden" id="aiAccordionBody" style="display: none;">
                                        <div class="ai-card" style="margin-top: 0;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.6rem; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 0.5rem;">
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #0f172a;">AI Engine:</span>
                                                    <select id="aiEngineSelect" class="ai-input" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                                        <option value="gemini" selected>Google Gemini (Gemini Flash)</option>
                                                        <option value="openai">OpenAI (ChatGPT-4o Mini)</option>
                                                    </select>
                                                </div>
                                                <span class="text-[11px] text-slate-500">Select model engine for names & descriptions</span>
                                            </div>
                                            <div class="ai-card-hdr">
                                                <i class="fas fa-magic" style="color: #6366f1;"></i>
                                                <h4>AI Copywriter & Vision</h4>
                                            </div>
                                            <p class="ai-card-desc">Analyze product image to draft names or descriptions.</p>
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                                <button type="button" onclick="aiGenerateNames()" id="aiNamesBtn" class="ai-btn">
                                                    <i class="fas fa-heading"></i> Suggest Names
                                                </button>
                                                <input type="number" id="aiDescMaxWords" value="100" min="10" max="500" class="ai-input" style="width: 85px; text-align: center; -webkit-appearance: none; -moz-appearance: textfield;" title="Max Words" placeholder="Words">
                                                <button type="button" onclick="aiGenerateDescription()" id="aiDescBtn" class="ai-btn">
                                                    <i class="fas fa-align-left"></i> Gen Description
                                                </button>
                                            </div>
                                            <!-- Loading -->
                                            <div id="aiLoading" class="hidden" style="display:none; align-items:center; justify-content:center; gap:0.5rem; padding:0.6rem; background:#f1f5f9; border-radius:6px; margin-top:0.6rem; font-size:0.72rem; color:#475569;">
                                                <div style="width:14px; height:14px; border:2px solid #6366f1; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
                                                <span>AI is analyzing image...</span>
                                            </div>
                                            <!-- Name results -->
                                            <div id="aiNamesResult" class="hidden" style="margin-top:0.6rem; padding-top:0.6rem; border-top:1px solid #e2e8f0;">
                                                <h5 style="font-size:0.65rem; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Suggested Names (Click to Apply)</h5>
                                                <div id="aiNamesList" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:0.35rem;"></div>
                                            </div>
                                            <!-- Desc results -->
                                            <div id="aiDescResult" class="hidden" style="margin-top:0.6rem; padding-top:0.6rem; border-top:1px solid #e2e8f0;">
                                                <h5 style="font-size:0.65rem; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Suggested Description</h5>
                                                <textarea id="aiDescTextarea" rows="5" class="field-input field-input--textarea" style="margin-bottom:0.4rem;"></textarea>
                                                <button type="button" onclick="applyAiDescription()" id="applyDescBtn" class="btn-submit" style="width:100%;">
                                                    Apply to Description Field
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Advanced AI Image Studio -->
                                        <div class="ai-card" style="margin-top: 1rem;">
                                            <div class="ai-card-hdr">
                                                <i class="fas fa-camera" style="color: #db2777;"></i>
                                                <h4>AI Image Studio (Gemini)</h4>
                                            </div>
                                            <p class="ai-card-desc">Generate AI fashion model images or professional studio product photos for this item.</p>
                                            
                                            <div style="display:flex; flex-direction:column; gap:0.8rem; margin-top:0.8rem;">
                                                
                                                <!-- Generation Mode Selector (On Model vs Studio Photo) -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Generation Mode</label>
                                                    <div style="display:flex; gap:0.5rem;">
                                                        <label class="bg-picker-label flex-1 cursor-pointer">
                                                            <input type="radio" name="ai_gen_mode" value="model" checked class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all hover:bg-slate-50">
                                                                <i class="fas fa-female"></i> On Model
                                                            </div>
                                                        </label>
                                                        <label class="bg-picker-label flex-1 cursor-pointer">
                                                            <input type="radio" name="ai_gen_mode" value="studio" class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all hover:bg-slate-50">
                                                                <i class="fas fa-box-open"></i> Studio Photo (No Model)
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Face Reference Models (Model Mode Only) -->
                                                <div id="ai_model_face_container">
                                                    <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Model Face (Optional)</label>
                                                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                                        <label class="model-picker-label relative group">
                                                            <input type="radio" name="ai_model_face" value="" checked class="hidden peer">
                                                            <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-500/30 border border-slate-200 rounded-xl overflow-hidden cursor-pointer transition-all opacity-80 peer-checked:opacity-100 bg-slate-50 flex items-center justify-center" style="width:64px; height:64px;">
                                                                <span style="font-size:0.65rem; color:#64748b; font-weight:700;">NONE</span>
                                                            </div>
                                                        </label>
                                                        <?php for($i=1; $i<=10; $i++): ?>
                                                        <label class="model-picker-label relative group">
                                                            <input type="radio" name="ai_model_face" value="model_<?= $i ?>.png" class="hidden peer">
                                                            <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-500/30 border border-slate-200 rounded-xl overflow-hidden cursor-pointer transition-all opacity-80 peer-checked:opacity-100 hover:opacity-100 bg-slate-50" style="width:64px; height:64px;" title="Model <?= $i ?>">
                                                                <img src="assets/models/model_<?= $i ?>.png" alt="Model <?= $i ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.parentElement.parentElement.style.display='none'">
                                                            </div>

                                                            <!-- Hover Zoom Popover -->
                                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:flex flex-col items-center z-50 pointer-events-none">
                                                                <div class="bg-white p-1.5 rounded-xl border border-pink-400 shadow-xl w-44 h-44 overflow-hidden">
                                                                    <img src="assets/models/model_<?= $i ?>.png" alt="Model <?= $i ?>" class="w-full h-full object-cover rounded-lg">
                                                                </div>
                                                                <div class="text-[10px] font-bold text-pink-600 bg-pink-50 px-2.5 py-0.5 rounded-full border border-pink-200 -mt-2 uppercase tracking-wider shadow-sm">
                                                                    Model <?= $i ?>
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>

                                                <!-- Background / Surface Presets -->
                                                <div>
                                                    <label id="ai_bg_label" style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Background / Props</label>
                                                    
                                                    <!-- Model Background Presets -->
                                                    <div style="display:flex; gap:0.4rem; flex-wrap:wrap;" id="bg_preset_container_model">
                                                        <?php
                                                        $bgPresetsModel = [
                                                            'Palace' => 'elegant royal palace with marble pillars and chandeliers',
                                                            'Beach' => 'golden hour beach with soft waves and sunset sky',
                                                            'Studio' => 'clean professional photography studio with soft gradient backdrop',
                                                            'Mountains' => 'majestic Himalayan mountains with misty peaks',
                                                            'Lake' => 'serene lake with reflections and lush greenery',
                                                            'Garden' => 'blooming flower garden with roses and jasmine',
                                                            'Haveli' => 'traditional Rajasthani haveli with jharokha windows',
                                                            'City Night' => 'modern city skyline at night with bokeh lights'
                                                        ];
                                                        $first = true;
                                                        foreach($bgPresetsModel as $label => $promptPart):
                                                        ?>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_bg_preset" value="<?= htmlspecialchars($promptPart) ?>" <?= $first ? 'checked' : '' ?> class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all hover:bg-slate-50">
                                                                <?= $label ?>
                                                            </div>
                                                        </label>
                                                        <?php $first=false; endforeach; ?>
                                                    </div>

                                                    <!-- Studio Surface Presets (Studio Mode Only) -->
                                                    <div style="display:none; gap:0.4rem; flex-wrap:wrap;" id="bg_preset_container_studio">
                                                        <?php
                                                        $bgPresetsStudio = [
                                                            'Dark Marble' => 'luxury black marble surface with soft warm directional studio spotlighting',
                                                            'Velvet Cushion' => 'royal velvet jewelry display cushion with subtle warm accent lighting',
                                                            'Wooden Stand' => 'minimalist rustic wooden jewelry stand with soft natural shadows',
                                                            'Reflective Glass' => 'reflective dark mirror glass surface with sharp luxury reflections',
                                                            'Champagne Silk' => 'draped champagne silk fabric backdrop with soft diffuse studio lighting',
                                                            'White Studio' => 'pristine white minimalist studio display backdrop'
                                                        ];
                                                        foreach($bgPresetsStudio as $label => $promptPart):
                                                        ?>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_bg_preset_studio" value="<?= htmlspecialchars($promptPart) ?>" class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all hover:bg-slate-50">
                                                                <?= $label ?>
                                                            </div>
                                                        </label>
                                                        <?php endforeach; ?>
                                                    </div>

                                                    <input type="text" id="ai_bg_custom" class="ai-input mt-2 w-full" value="elegant royal palace with marble pillars and chandeliers" placeholder="Describe the background, display surface, or props...">
                                                </div>

                                                <!-- Shot & Hair Controls -->
                                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                                    <div>
                                                        <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Shot Type</label>
                                                        
                                                        <!-- Model Shot Types -->
                                                        <div id="ai_shot_type_container_model" style="display:flex; flex-direction:column; gap:0.3rem;">
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type" value="close-up portrait shot focusing on the face and the jewelry"> Close-up Portrait</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type" value="half body shot from waist up, showing the model's torso and face"> Half Body</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type" value="full body head-to-toe shot showing the complete outfit/jewelry look" checked> Full Body</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type" value="shot from behind showing the back design and details of the product"> Back View</label>
                                                        </div>

                                                        <!-- Studio Shot Types (Studio Mode Only) -->
                                                        <div id="ai_shot_type_container_studio" style="display:none; flex-direction:column; gap:0.3rem;">
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type_studio" value="ultra-sharp macro close-up product photography highlighting fine details, gemstone sparkle, and metal texture" checked> Macro Close-Up</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type_studio" value="professional flat lay top-down product photography layout"> Flat Lay (Top-Down)</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type_studio" value="elegant 3/4 perspective product display shot showing side and front angles"> 3/4 Display Angle</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_shot_type_studio" value="dramatic floating product display with soft realistic shadow underneath"> Floating Display</label>
                                                        </div>
                                                    </div>

                                                    <div id="ai_hair_style_container">
                                                        <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Hair Style</label>
                                                        <div style="display:flex; flex-direction:column; gap:0.3rem;">
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="open flowing hair with soft waves (khule baal)"> Open Flowing</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="neatly tied bun with gajra flowers"> Tied / Bun</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="traditional long braided hair (gajra choti)"> Traditional Braid (Choti)</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="elegant half-up half-down hairstyle"> Half Up, Half Down</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="glamorous side-swept waves"> Side Swept Waves</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="sleek straight hair with center part"> Sleek Straight</label>
                                                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer"><input type="radio" name="ai_hair_style" value="" checked> As per product</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Final Prompt Textarea -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Final Prompt (Edit if needed)</label>
                                                    <textarea id="ai_final_prompt" rows="4" class="ai-input w-full" style="resize:vertical;">A photorealistic beautiful Indian fashion model wearing this exact <?php echo htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product')); ?>. The background should have elegant royal palace with marble pillars and chandeliers. Shot type: full body head-to-toe shot showing the complete outfit/jewelry look. Do not change the <?php echo htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product')); ?> details. Aspect ratio: 2:3 vertical fashion portrait format.</textarea>
                                                </div>

                                                <!-- Quantity Selector -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Number of Images (Variations)</label>
                                                    <div style="display:flex; gap:0.5rem;">
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="1" checked class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">1 Image</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="2" class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">2 Images</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="3" class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">3 Images</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="4" class="hidden peer">
                                                            <div class="peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 border border-slate-200 bg-white text-slate-700 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">4 Images</div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Generate Button -->
                                                <button type="button" onclick="aiGenerateAdvancedImage()" id="aiImageBtn" class="btn-submit" style="justify-content:center; padding:0.6rem; margin-top:0.5rem; background:#0f172a !important; border-color:#0f172a !important; color:#ffffff !important;">
                                                    <i class="fas fa-magic"></i> Generate Model Image
                                                </button>
                                            </div>

                                            <!-- Loading -->
                                            <div id="aiImageLoading" class="hidden" style="display:none; flex-direction:column; align-items:center; gap:0.5rem; padding:1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; margin-top:0.6rem; font-size:0.72rem; color:#475569;">
                                                <div style="width:18px; height:18px; border:2px solid #0f172a; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
                                                <span>AI is generating image. This may take 15-20 seconds...</span>
                                            </div>
                                            <!-- Result -->
                                            <div id="aiImageResult" class="hidden" style="margin-top:0.8rem; padding-top:0.8rem; border-top:1px solid #e2e8f0;">
                                                <h5 style="font-size:0.65rem; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Generated Images</h5>
                                                
                                                <!-- Grid for up to 4 images -->
                                                <div id="aiImageGrid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1rem;">
                                                    <!-- Injected by JS -->
                                                </div>
                                                
                                                <div style="display:flex; justify-content:center;">
                                                    <button type="button" onclick="resetAiImage()" id="aiResetImgBtn" class="btn-cancel" style="padding:0.5rem 2rem;">
                                                        <i class="fas fa-redo"></i> Clear Results & Try Again
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Right / Sidebar Column (5 cols on lg, 4 cols on xl) -->
                            <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                                <!-- Card A: Product Organization & Identifiers -->
                                <div class="shadcn-card" id="section_code">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-fingerprint"></i>
                                            </div>
                                            <div>
                                                <div class="section-title">Identifiers & Status</div>
                                                <div class="section-desc">Product metadata and taxonomy</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3.5">
                                        <div>
                                            <label class="field-label">SKU / Product Code</label>
                                            <input type="text" value="<?php echo htmlspecialchars($product['code']); ?>" disabled class="field-input font-mono font-semibold">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="field-label">Database ID</label>
                                                <input type="text" value="<?php echo $product['id']; ?>" disabled class="field-input font-mono">
                                            </div>
                                            <div>
                                                <label class="field-label">Product Type</label>
                                                <input type="text" value="<?php echo ucfirst($type); ?>" disabled class="field-input font-medium capitalize">
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-slate-100">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs font-semibold text-slate-800">Featured Product</div>
                                                    <div class="text-[11px] text-slate-500">Highlight in Exclusive Collections</div>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="featured" value="1" <?php echo ($product['featured'] ?? 0) == 1 ? 'checked' : ''; ?> class="sr-only peer">
                                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card B: Categories & Subcategories (WordPress-Style Multi-Select Checkbox Component) -->
                                <div class="shadcn-card" id="section_categories">
                                    <div class="section-hdr">
                                        <div class="section-hdr-left">
                                            <div class="section-icon">
                                                <i class="fas fa-sitemap"></i>
                                            </div>
                                            <div>
                                                <div class="section-title">Categories & Taxonomies</div>
                                                <div class="section-desc">Select main & subcategories</div>
                                            </div>
                                        </div>
                                        <span id="wpCategoryCounter" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                            0 Selected
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        <!-- Quick Action Controls -->
                                        <div class="flex items-center justify-between gap-1 text-[11px] text-slate-500 border-b border-slate-100 pb-2 flex-wrap">
                                            <button type="button" onclick="wpToggleAllCategories(true)" class="hover:text-slate-900 font-medium">Select All</button>
                                            <span>•</span>
                                            <button type="button" onclick="wpToggleAllCategories(false)" class="hover:text-slate-900 font-medium">Clear</button>
                                            <span>•</span>
                                            <button type="button" onclick="wpToggleAllTrees(true)" class="hover:text-slate-900 font-medium">Expand All</button>
                                            <span>•</span>
                                            <button type="button" onclick="wpToggleAllTrees(false)" class="hover:text-slate-900 font-medium">Collapse All</button>
                                        </div>

                                        <!-- Search Bar -->
                                        <div class="relative">
                                            <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                            <input type="text" id="wpCategorySearch" oninput="wpFilterCategoryTree()" placeholder="Filter categories..." class="w-full pl-8 pr-3 py-1.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-lg outline-none focus:border-slate-900 transition-colors">
                                        </div>

                                        <!-- Category Tree Box -->
                                        <div id="wpCategoryTree" style="max-height: 380px; overflow-y: auto;" class="bg-slate-50/70 border border-slate-200 rounded-lg p-2 space-y-2">
                                            <?php 
                                            $assignedMain = $assignedCategories['main_categories'] ?? [];
                                            $assignedSub = $assignedCategories['subcategories'] ?? [];
                                            ?>
                                            <?php if (!empty($allCategoriesTree)): ?>
                                                <?php foreach ($allCategoriesTree as $catIndex => $mainCat): ?>
                                                    <?php 
                                                    $isMainChecked = in_array((int)$mainCat['id'], $assignedMain);
                                                    $subList = $mainCat['subcategories'] ?? [];
                                                    $hasCheckedSub = false;
                                                    foreach ($subList as $s) {
                                                        if (in_array((int)$s['id'], $assignedSub)) {
                                                            $hasCheckedSub = true;
                                                            break;
                                                        }
                                                    }
                                                    $isExpanded = $isMainChecked || $hasCheckedSub;
                                                    ?>
                                                    <div class="wp-cat-node <?php echo $isMainChecked ? 'wp-cat-selected' : ''; ?>" data-cat-name="<?php echo htmlspecialchars(strtolower($mainCat['name'])); ?>">
                                                        <!-- Main Category Row -->
                                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                                            <label style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; font-weight: 600; color: <?php echo $isMainChecked ? '#713f12' : '#0f172a'; ?>; cursor: pointer; user-select: none;">
                                                                <input type="checkbox" name="categories[]" value="<?php echo $mainCat['id']; ?>" <?php echo $isMainChecked ? 'checked' : ''; ?> onchange="wpUpdateCatCounter()" class="wp-cat-check" style="width: 15px; height: 15px; accent-color: #0f172a; cursor: pointer;">
                                                                <i class="fas <?php echo !empty($subList) ? 'fa-folder' : 'fa-tag'; ?> wp-cat-icon" style="font-size: 0.75rem; color: <?php echo $isMainChecked ? '#ca8a04' : '#64748b'; ?>;"></i>
                                                                <span class="wp-cat-title"><?php echo htmlspecialchars($mainCat['name']); ?></span>
                                                            </label>
                                                            <?php if (!empty($subList)): ?>
                                                                <button type="button" onclick="wpToggleSubTree('sub_tree_<?php echo $catIndex; ?>', this)" style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-size: 0.68rem; font-weight: 500; padding: 0.15rem 0.55rem; border-radius: 9999px; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                                                    <span><?php echo count($subList); ?> sub</span>
                                                                    <i class="fas <?php echo $isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'; ?>" style="font-size: 0.6rem;"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Nested Subcategories -->
                                                        <?php if (!empty($subList)): ?>
                                                            <div id="sub_tree_<?php echo $catIndex; ?>" class="wp-sub-tree" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e2e8f0; padding-left: 1.4rem; display: <?php echo $isExpanded ? 'grid' : 'none'; ?>; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.35rem;">
                                                                <?php foreach ($subList as $sub): ?>
                                                                    <?php $isSubChecked = in_array((int)$sub['id'], $assignedSub); ?>
                                                                    <label class="wp-sub-node <?php echo $isSubChecked ? 'wp-sub-selected' : ''; ?>" data-sub-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>">
                                                                        <input type="checkbox" name="sub_categories[]" value="<?php echo $sub['id']; ?>" <?php echo $isSubChecked ? 'checked' : ''; ?> onchange="wpUpdateCatCounter()" class="wp-sub-check" style="width: 14px; height: 14px; accent-color: #0f172a; cursor: pointer;">
                                                                        <i class="fas fa-tag wp-sub-icon" style="font-size: 0.65rem; color: <?php echo $isSubChecked ? '#ca8a04' : '#94a3b8'; ?>;"></i>
                                                                        <span><?php echo htmlspecialchars($sub['name']); ?></span>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div style="padding: 1.5rem; text-align: center; color: #64748b; font-size: 0.78rem;">
                                                    No categories found for this product type.
                                                </div>
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
                                                <div class="section-title">Product Colors</div>
                                                <div class="section-desc">Select available color swatches</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" id="btnAiDetectColors" onclick="aiDetectColors()" class="inline-flex items-center gap-1 text-[11px] font-medium py-1 px-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                                                <i class="fas fa-magic text-[10px] text-pink-500" id="aiDetectColorsIcon"></i> AI Detect
                                            </button>
                                            <span id="selectedColorsCounter" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                                0 Selected
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <!-- Selected Colors Display Area -->
                                        <div id="selectedColorsContainer" class="flex flex-wrap gap-1.5 min-h-[42px] p-2 bg-slate-50 border border-slate-200 rounded-lg items-center">
                                            <!-- Dynamically rendered selected color badges -->
                                        </div>

                                        <!-- Search & Custom Add Bar -->
                                        <div class="relative">
                                            <div class="flex gap-1.5">
                                                <div class="relative flex-1">
                                                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                                                    <input type="text" id="colorSearchInput" placeholder="Search color or type name..." 
                                                           class="w-full pl-8 pr-3 py-1.5 text-xs text-slate-900 bg-white border border-slate-200 rounded-lg outline-none focus:border-slate-900 transition-colors"
                                                           onfocus="showColorDropdown()" 
                                                           oninput="filterColorDropdown()" 
                                                           onkeydown="handleColorInputKey(event)">
                                                </div>
                                                <button type="button" onclick="addCustomColorFromInput()" class="px-2.5 py-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg transition-colors">
                                                    Add
                                                </button>
                                            </div>

                                            <!-- Dropdown of matching colors -->
                                            <div id="colorDropdownList" style="display: none;" class="absolute top-[calc(100%+4px)] left-0 right-0 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-lg p-1.5 z-40 shadow-lg">
                                                <!-- Dynamically populated options -->
                                            </div>
                                        </div>

                                        <!-- Quick Pick Popular Colors -->
                                        <div>
                                            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                                Popular Colors (1-Click):
                                            </div>
                                            <div id="quickPickColors" class="flex flex-wrap gap-1.5">
                                                <!-- Curated quick pick buttons -->
                                            </div>
                                        </div>

                                        <!-- Hidden Inputs Container -->
                                        <div id="hiddenColorInputs"></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Sticky Footer Actions Bar -->
                        <div class="edit-footer">
                            <div class="edit-footer-status flex items-center gap-3 flex-wrap">
                                <div class="sync-indicator flex items-center gap-2" id="syncStatusIndicator">
                                    <?php if (!empty($isSyncApplicable)): ?>
                                        <span class="text-[11px] text-teal-700 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-md flex items-center gap-1.5" title="Category is configured for Yosshitaneha child store sync">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                            <span>Child Store Sync: <strong>Applicable</strong></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                                    <span>SKU: <strong class="text-slate-800"><?php echo htmlspecialchars($product['code']); ?></strong></span>
                                    <span class="text-slate-300">•</span>
                                    <span><kbd class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded text-[10px] border border-slate-200 font-mono font-semibold">Ctrl+S</kbd> to save</span>
                                </div>
                            </div>
                            <div class="edit-footer-actions flex items-center gap-2">
                                <a href="index.php?controller=product&action=index" class="btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn-submit" id="btnSubmitProduct">
                                    <?php if (!empty($isSyncApplicable)): ?>
                                        <i class="fas fa-sync-alt mr-1 text-teal-400" id="submitBtnIcon"></i> <span id="submitBtnText">Sync & Update</span>
                                    <?php else: ?>
                                        <i class="fas fa-save mr-1" id="submitBtnIcon"></i> <span id="submitBtnText">Update Product</span>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </form>          </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
    <script>
        const currentType = '<?php echo $type; ?>';
        const currentSubId = '<?php echo $product['sub_category'] ?? ''; ?>';
        const currentCatId = '<?php echo $product['category'] ?? ''; ?>';
        window.syncSettings = <?php echo json_encode(\Core\ProductSyncService::getSyncSettings()); ?>;
        window.isSyncApplicableInitial = <?php echo !empty($isSyncApplicable) ? 'true' : 'false'; ?>;

        window.addEventListener('DOMContentLoaded', () => {
            if (currentCatId) {
                const targetId = currentType === 'jewellery' ? 'jewel_subcat' : 'garment_subcat';
                fetchSubcategories(currentType, currentCatId, targetId, currentSubId);
            }
            checkSyncApplicability();
        });

        if (document.getElementById('jewel_cat')) {
            document.getElementById('jewel_cat').addEventListener('change', function() {
                fetchSubcategories('jewellery', this.value, 'jewel_subcat');
                checkSyncApplicability();
            });
        }
        if (document.getElementById('jewel_subcat')) {
            document.getElementById('jewel_subcat').addEventListener('change', function() {
                checkSyncApplicability();
            });
        }
        if (document.getElementById('garment_cat')) {
            document.getElementById('garment_cat').addEventListener('change', function() {
                fetchSubcategories('garments', this.value, 'garment_subcat');
                checkSyncApplicability();
            });
        }
        if (document.getElementById('garment_subcat')) {
            document.getElementById('garment_subcat').addEventListener('change', function() {
                checkSyncApplicability();
            });
        }

        async function fetchSubcategories(type, parentId, targetId, selectedId = null) {
            const subDropdown = document.getElementById(targetId);
            if (!parentId) {
                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
                checkSyncApplicability();
                return;
            }
            try {
                const response = await fetch(`index.php?controller=product&action=getSubcategories&type=${type}&parent_id=${parentId}`);
                const data = await response.json();
                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.subcat_id;
                    opt.textContent = sub.name;
                    if (selectedId && sub.subcat_id == selectedId) opt.selected = true;
                    subDropdown.appendChild(opt);
                });
                checkSyncApplicability();
            } catch (error) { 
                console.error('Error fetching subcategories:', error); 
                checkSyncApplicability();
            }
        }

        document.getElementById('img_upload').addEventListener('change', function(e) {
            const preview = document.getElementById('img_preview');
            preview.innerHTML = '';
            [...this.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'img-thumb';
                    div.innerHTML = `<img src="${e.target.result}" alt="">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        let isBulkDeleteActive = false;

        function toggleBulkDeleteMode(enable) {
            if (enable === undefined) enable = !isBulkDeleteActive;
            isBulkDeleteActive = enable;

            const actions = document.getElementById('bulkSelectActions');
            const enableBtn = document.getElementById('enableBulkDeleteBtn');
            const checkboxes = document.querySelectorAll('.img-select-wrapper');
            const cards = document.querySelectorAll('.img-card-item');

            if (enable) {
                if (actions) actions.style.display = 'flex';
                if (enableBtn) enableBtn.style.display = 'none';
                checkboxes.forEach(cb => cb.style.display = 'block');
                cards.forEach(card => card.style.cursor = 'pointer');
            } else {
                if (actions) actions.style.display = 'none';
                if (enableBtn) enableBtn.style.display = 'inline-flex';
                checkboxes.forEach(cb => cb.style.display = 'none');
                cards.forEach(card => card.style.cursor = '');
                selectAllImages(false);
            }
            updateSelectedCount();
        }

        function handleCardClick(event, cardElem) {
            if (!isBulkDeleteActive) return;
            if (event.target.closest('.img-del-btn') || event.target.closest('.img-set-main') || event.target.closest('.img-order-input')) {
                return;
            }
            const cb = cardElem.querySelector('.img-select-checkbox');
            if (cb && event.target !== cb) {
                cb.checked = !cb.checked;
                updateSelectedCount();
            }
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.img-select-checkbox:checked');
            const count = checked.length;
            const countSpan = document.getElementById('selectedCount');
            const btn = document.getElementById('deleteSelectedBtn');

            if (countSpan) countSpan.textContent = count;

            if (btn) {
                if (count > 0) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                } else {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.style.cursor = 'not-allowed';
                }
            }

            document.querySelectorAll('.img-card-item').forEach(card => {
                const cb = card.querySelector('.img-select-checkbox');
                if (cb && cb.checked) {
                    card.style.outline = '2px solid #ef4444';
                    card.style.borderRadius = '8px';
                } else {
                    card.style.outline = '';
                }
            });
        }

        function selectAllImages(check) {
            const checkboxes = document.querySelectorAll('.img-select-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = check;
            });
            updateSelectedCount();
        }

        async function deleteSelectedImages() {
            const checked = document.querySelectorAll('.img-select-checkbox:checked');
            const ids = Array.from(checked).map(cb => parseInt(cb.value, 10)).filter(id => id > 0);

            if (ids.length === 0) return;

            if (!confirm(`Are you sure you want to delete ${ids.length} selected image(s)?`)) return;

            const btn = document.getElementById('deleteSelectedBtn');
            const origText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            }

            try {
                const response = await fetch('index.php?controller=product&action=deleteImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: ids })
                });
                const data = await response.json();
                if (data.success) {
                    checked.forEach(cb => {
                        const cardItem = cb.closest('.img-card-item');
                        if (cardItem) cardItem.remove();
                    });
                    reindexImageWeightsUI();
                    toggleBulkDeleteMode(false);
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete selected images'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred while deleting images.');
            } finally {
                if (btn) btn.innerHTML = origText;
                updateSelectedCount();
            }
        }

        function reindexImageWeightsUI() {
            const cards = document.querySelectorAll('#existing_img_grid .img-card-item');
            cards.forEach((card, index) => {
                const input = card.querySelector('.img-order-input');
                if (input) {
                    input.value = index;
                    input.style.borderColor = '';
                    input.style.color = '';
                }
            });
        }

        async function deleteProductImage(btn, imgId) {
            if (!confirm('Are you sure you want to delete this image?')) return;
            try {
                const response = await fetch('index.php?controller=product&action=deleteImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: imgId })
                });
                const data = await response.json();
                if (data.success) { 
                    const cardItem = btn.closest('.img-card-item');
                    if (cardItem) cardItem.remove();
                    else btn.closest('.img-thumb')?.parentElement?.remove();
                    reindexImageWeightsUI();
                }
                else { alert(data.error || 'Failed to delete the image'); }
            } catch (error) { console.error('Error deleting image:', error); alert('An error occurred while deleting the image.'); }
        }

        function readAsBase64(fileOrBlob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
                reader.readAsDataURL(fileOrBlob);
            });
        }

        function togglePriceSource() {
            const toggle = document.getElementById('price_source_toggle');
            const isManual = toggle.checked;
            const desc = document.getElementById('price_source_description');
            const labelPos = document.getElementById('label_pos');
            const labelManual = document.getElementById('label_manual');
            const posNote = document.getElementById('pos_price_note');
            const pricingFields = document.getElementById('pricing_fields');

            if (isManual) {
                desc.textContent = 'Prices are set manually from the fields below.';
                labelPos.style.color = '#444';
                labelManual.style.color = '#f59e0b';
                posNote.classList.add('hidden');
                pricingFields.style.outline = '2px solid rgba(245,158,11,0.3)';
                pricingFields.style.borderRadius = '8px';
                pricingFields.style.padding = '0.5rem';
            } else {
                desc.textContent = 'Prices are auto-calculated from POS system data.';
                labelPos.style.color = '#6e8efb';
                labelManual.style.color = '#444';
                posNote.classList.remove('hidden');
                pricingFields.style.outline = '';
                pricingFields.style.padding = '';
            }
        }

        async function setMainImage(btn, imageId) {
            if (!confirm('Make this the main product image?')) return;
            try {
                const response = await fetch('index.php?controller=product&action=setMainImage', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image_id: imageId, product_id: <?php echo $product['id']; ?>, type: '<?php echo $type; ?>' })
                });
                const data = await response.json();
                if (data.success) { alert('Main image updated!'); window.location.reload(); }
                else { alert('Error: ' + data.error); }
            } catch (err) { console.error(err); alert('Network request failed'); }
        }

        const productId = <?php echo $product['id']; ?>;
        const productType = '<?php echo $type; ?>';

        // AI loading helper: show/hide using display property
        function showEl(id) { const el = document.getElementById(id); el.style.display = 'flex'; el.classList.remove('hidden'); }
        function hideEl(id) { const el = document.getElementById(id); el.style.display = 'none'; el.classList.add('hidden'); }

        async function aiGenerateNames() {
            const btn = document.getElementById('aiNamesBtn');
            const provider = document.getElementById('aiEngineSelect')?.value || 'gemini';
            btn.disabled = true;
            showEl('aiLoading');
            hideEl('aiNamesResult');
            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestNames&id=${productId}&type=${productType}&ai_provider=${provider}`);
                const data = await response.json();
                if (data.success && data.names) {
                    document.getElementById('aiNamesList').innerHTML = data.names.map(name => `
                        <button type="button" onclick="applyProductName('${name.replace(/'/g, "\\'")}')" class="ai-btn" style="width:100%; justify-content:space-between; text-align:left;">
                            <span style="white-space:normal; line-height:1.3;">${name}</span>
                            <i class="fas fa-chevron-right" style="font-size:0.55rem; color:#444; flex-shrink:0;"></i>
                        </button>
                    `).join('');
                    document.getElementById('aiNamesResult').classList.remove('hidden');
                    document.getElementById('aiNamesResult').style.display = 'block';
                } else { alert('Error: ' + (data.error || 'Failed to generate names')); }
            } catch (err) { console.error(err); alert('A network error occurred.'); }
            finally { btn.disabled = false; hideEl('aiLoading'); }
        }

        async function aiGenerateDescription() {
            const btn = document.getElementById('aiDescBtn');
            const maxWords = document.getElementById('aiDescMaxWords')?.value || 100;
            const provider = document.getElementById('aiEngineSelect')?.value || 'gemini';
            btn.disabled = true;
            showEl('aiLoading');
            hideEl('aiDescResult');
            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestDescription&id=${productId}&type=${productType}&max_words=${maxWords}&ai_provider=${provider}`);
                const data = await response.json();
                if (data.success && data.description) {
                    document.getElementById('aiDescTextarea').value = data.description;
                    document.getElementById('aiDescResult').classList.remove('hidden');
                    document.getElementById('aiDescResult').style.display = 'block';
                } else { alert('Error: ' + (data.error || 'Failed to generate description')); }
            } catch (err) { console.error(err); alert('A network error occurred.'); }
            finally { btn.disabled = false; hideEl('aiLoading'); }
        }

        function applyProductName(newName) {
            const nameInput = document.querySelector('input[name="name"]');
            if (nameInput) {
                nameInput.value = newName;
                nameInput.focus();
                nameInput.style.transition = 'all 0.3s ease';
                nameInput.style.boxShadow = '0 0 0 2px rgba(16,185,129,0.4)';
                setTimeout(() => { nameInput.style.boxShadow = ''; }, 1000);
            }
        }

        function applyAiDescription() {
            const val = document.getElementById('aiDescTextarea').value.trim();
            const descInput = document.getElementById('product_desc_textarea');
            if (descInput && val) {
                descInput.value = val;
                descInput.focus();
                descInput.style.transition = 'all 0.3s ease';
                descInput.style.boxShadow = '0 0 0 2px rgba(16,185,129,0.4)';
                setTimeout(() => { descInput.style.boxShadow = ''; }, 1000);
            }
        }

        function updateFinalPrompt() {
            const catName = '<?php echo htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product')); ?>';
            const mode = document.querySelector('input[name="ai_gen_mode"]:checked')?.value || 'model';
            const customBg = document.getElementById('ai_bg_custom').value.trim();

            if (mode === 'studio') {
                const shotTypeStudio = document.querySelector('input[name="ai_shot_type_studio"]:checked')?.value || 'ultra-sharp macro close-up product photography highlighting fine details, gemstone sparkle, and metal texture';
                let bgPrompt = customBg || 'luxury black marble surface with soft warm directional studio spotlighting';

                let promptParts = [
                    `Ultra-high resolution professional studio product photography of this exact ${catName} placed on ${bgPrompt}.`,
                    `Shot type: ${shotTypeStudio}.`,
                    `No human model, no hands, product-only studio display photoshoot with soft diffuse studio lighting, 8k resolution, razor-sharp focus on details, gemstone sparkle and fine metal craftsmanship.`,
                    `Do not change the ${catName} design or features.`,
                    `Aspect ratio: 2:3 vertical format.`
                ];
                document.getElementById('ai_final_prompt').value = promptParts.join(' ');
            } else {
                const faceInput = document.querySelector('input[name="ai_model_face"]:checked')?.value || '';
                const shotTypeModel = document.querySelector('input[name="ai_shot_type"]:checked')?.value || 'full body head-to-toe shot showing the complete outfit/jewelry look';
                const hairStyle = document.querySelector('input[name="ai_hair_style"]:checked')?.value || '';
                let bgPrompt = customBg || 'elegant royal palace with marble pillars and chandeliers';

                let promptParts = [
                    `A photorealistic beautiful Indian fashion model wearing this exact ${catName}.`,
                    `The background should have ${bgPrompt}.`,
                    `Shot type: ${shotTypeModel}.`,
                    `Do not change the ${catName} details.`,
                    `Aspect ratio: 2:3 vertical fashion portrait format.`
                ];
                
                if (hairStyle) {
                    promptParts.push(`The model should have ${hairStyle}.`);
                }
                if (faceInput) {
                    promptParts.push(`The model's face must match the reference photo exactly.`);
                }

                document.getElementById('ai_final_prompt').value = promptParts.join(' ');
            }
        }

        // Toggle UI Mode (On Model vs Studio Photo)
        document.querySelectorAll('input[name="ai_gen_mode"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const mode = e.target.value;
                const faceContainer = document.getElementById('ai_model_face_container');
                const hairContainer = document.getElementById('ai_hair_style_container');
                const bgLabel = document.getElementById('ai_bg_label');
                const bgModel = document.getElementById('bg_preset_container_model');
                const bgStudio = document.getElementById('bg_preset_container_studio');
                const shotModel = document.getElementById('ai_shot_type_container_model');
                const shotStudio = document.getElementById('ai_shot_type_container_studio');
                const customBg = document.getElementById('ai_bg_custom');
                const btn = document.getElementById('aiImageBtn');

                if (mode === 'studio') {
                    if (faceContainer) faceContainer.style.display = 'none';
                    if (hairContainer) hairContainer.style.display = 'none';
                    if (bgModel) bgModel.style.display = 'none';
                    if (bgStudio) bgStudio.style.display = 'flex';
                    if (shotModel) shotModel.style.display = 'none';
                    if (shotStudio) shotStudio.style.display = 'flex';
                    if (bgLabel) bgLabel.innerText = 'Display Surface / Backdrop';
                    
                    const studioVal = document.querySelector('input[name="ai_bg_preset_studio"]:checked')?.value || 'luxury black marble surface with soft warm directional studio spotlighting';
                    customBg.value = studioVal;

                    btn.style.setProperty('background', '#a855f7', 'important');
                    btn.style.setProperty('border-color', '#a855f7', 'important');
                    btn.style.setProperty('color', '#fff', 'important');
                    btn.innerHTML = '<i class="fas fa-camera"></i> Generate Studio Product Photo';
                } else {
                    if (faceContainer) faceContainer.style.display = 'block';
                    if (hairContainer) hairContainer.style.display = 'block';
                    if (bgModel) bgModel.style.display = 'flex';
                    if (bgStudio) bgStudio.style.display = 'none';
                    if (shotModel) shotModel.style.display = 'flex';
                    if (shotStudio) shotStudio.style.display = 'none';
                    if (bgLabel) bgLabel.innerText = 'Background / Props';

                    const modelVal = document.querySelector('input[name="ai_bg_preset"]:checked')?.value || 'elegant royal palace with marble pillars and chandeliers';
                    customBg.value = modelVal;

                    btn.style.setProperty('background', '#f472b6', 'important');
                    btn.style.setProperty('border-color', '#f472b6', 'important');
                    btn.style.setProperty('color', '#000', 'important');
                    btn.innerHTML = '<i class="fas fa-magic"></i> Generate Model Image';
                }
                updateFinalPrompt();
            });
        });

        // Attach listeners to all inputs to live-update the prompt textarea
        document.querySelectorAll('input[name="ai_model_face"], input[name="ai_bg_preset"], input[name="ai_bg_preset_studio"], input[name="ai_shot_type"], input[name="ai_shot_type_studio"], input[name="ai_hair_style"]').forEach(input => {
            input.addEventListener('change', updateFinalPrompt);
        });
        document.getElementById('ai_bg_custom').addEventListener('input', updateFinalPrompt);

        document.querySelectorAll('input[name="ai_bg_preset"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const customInput = document.getElementById('ai_bg_custom');
                customInput.value = e.target.value;
                updateFinalPrompt();
                
                customInput.style.transition = 'all 0.3s ease';
                customInput.style.boxShadow = '0 0 0 2px rgba(244,114,182,0.4)';
                setTimeout(() => { customInput.style.boxShadow = ''; }, 600);
            });
        });

        document.querySelectorAll('input[name="ai_bg_preset_studio"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const customInput = document.getElementById('ai_bg_custom');
                customInput.value = e.target.value;
                updateFinalPrompt();
                
                customInput.style.transition = 'all 0.3s ease';
                customInput.style.boxShadow = '0 0 0 2px rgba(168,85,247,0.4)';
                setTimeout(() => { customInput.style.boxShadow = ''; }, 600);
            });
        });

        async function aiGenerateAdvancedImage() {
            const btn = document.getElementById('aiImageBtn');
            const mode = document.querySelector('input[name="ai_gen_mode"]:checked')?.value || 'model';
            const faceInput = (mode === 'studio') ? '' : (document.querySelector('input[name="ai_model_face"]:checked')?.value || '');
            const finalPrompt = document.getElementById('ai_final_prompt').value.trim();
            const numImages = document.querySelector('input[name="ai_num_images"]:checked').value;

            btn.disabled = true;
            showEl('aiImageLoading');
            hideEl('aiImageResult');
            document.getElementById('aiImageGrid').innerHTML = ''; // Clear old results
            
            try {
                const response = await fetch(`index.php?controller=product&action=aiGenerateModelImage&id=${productId}&type=${productType}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        prompt: finalPrompt,
                        face_reference: faceInput,
                        num_images: numImages
                    })
                });
                const data = await response.json();
                
                if (data.success && data.images_base64 && data.images_base64.length > 0) {
                    const grid = document.getElementById('aiImageGrid');
                    data.images_base64.forEach((b64, index) => {
                        grid.innerHTML += `
                            <div style="display:flex; flex-direction:column; gap:0.5rem; background:#ffffff; border:1px solid #e2e8f0; padding:0.5rem; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                                <img src="data:image/jpeg;base64,${b64}" style="width:100%; aspect-ratio:2/3; object-fit:cover; border-radius:6px;">
                                <button type="button" onclick="saveAiGeneratedImage(this, '${b64}')" class="btn-submit" style="width:100%; justify-content:center; padding:0.4rem; font-size:0.7rem;">
                                    <i class="fas fa-save"></i> Save Image ${index + 1}
                                </button>
                            </div>
                        `;
                    });
                    
                    document.getElementById('aiImageResult').classList.remove('hidden');
                    document.getElementById('aiImageResult').style.display = 'block';
                } else { 
                    alert('Error: ' + (data.error || 'Failed to generate images')); 
                }
            } catch (err) { 
                console.error(err); 
                alert('A network error occurred.'); 
            } finally { 
                btn.disabled = false; 
                hideEl('aiImageLoading'); 
            }
        }

        function resetAiImage() {
            hideEl('aiImageResult');
            document.getElementById('aiImageGrid').innerHTML = '';
            document.getElementById('ai_final_prompt').focus();
        }

        async function saveAiGeneratedImage(btn, base64Str) {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;
            try {
                const response = await fetch(`index.php?controller=product&action=saveAiImage&id=${productId}&type=${productType}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image_base64: base64Str })
                });
                const data = await response.json();
                if (data.success) { 
                    btn.innerHTML = '<i class="fas fa-check" style="color:#10b981;"></i> Saved!'; 
                    btn.style.background = 'rgba(16,185,129,0.1)';
                    btn.style.borderColor = 'rgba(16,185,129,0.3)';
                    btn.style.color = '#10b981';
                    
                    // Dynamically append the saved image to the gallery without reloading the page!
                    const imgGrid = document.getElementById('existing_img_grid');
                    if (imgGrid && data.path) {
                        const localSrc = '/ss/yn/uploads' + data.path;
                        const cloudSrc = 'http://srishringarr.com/yn/uploads' + data.path;
                        
                        const newThumb = document.createElement('div');
                        newThumb.className = 'img-card-item';
                        newThumb.innerHTML = `
                            <div class="img-thumb">
                                <img src="${localSrc}" onerror="this.onerror=null; this.src='${cloudSrc}';" alt="">
                                <div class="img-overlay">
                                    <span style="color:#fff; font-size:0.62rem; font-weight:600;">Image</span>
                                </div>
                                <button type="button" onclick="setMainImage(this, ${data.id})" class="img-set-main" title="Set as Main Image">
                                    <i class="far fa-star"></i>
                                </button>
                                <button type="button" onclick="deleteProductImage(this, ${data.id})" class="img-del-btn" title="Delete Image">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <div class="img-card-footer">
                                <span class="img-order-label"><i class="fas fa-sort-numeric-down text-pink-400"></i> Order</span>
                                <input type="number" min="0" name="image_weights[${data.id}]" value="${imgGrid.querySelectorAll('.img-card-item').length + 1}" class="img-order-input" onchange="updateImageWeight(${data.id}, this)">
                            </div>
                        `;
                        imgGrid.appendChild(newThumb);
                        reindexImageWeightsUI();
                    }
                } else { 
                    alert('Error: ' + (data.error || 'Failed to save image')); 
                    btn.innerHTML = orig; 
                    btn.disabled = false; 
                }
            } catch (err) { 
                console.error(err); 
                alert('A network error occurred.'); 
                btn.innerHTML = orig; 
                btn.disabled = false; 
            }
        }

        async function aiGenerateVideo() {
            const btn = document.getElementById('aiVideoBtn');
            const loaderText = document.getElementById('aiVideoLoadingText');
            const prompt = document.getElementById('aiVideoPrompt').value.trim();
            btn.disabled = true;
            showEl('aiVideoLoading');
            hideEl('aiVideoResult');
            loaderText.innerText = "Starting video generation...";
            try {
                const response = await fetch(`index.php?controller=product&action=aiGenerateVideoStart&id=${productId}&type=${productType}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: prompt })
                });
                const data = await response.json();
                if (data.success && data.operation_name) {
                    loaderText.innerText = "Generating... Please wait (est. 1-2 mins)";
                    pollProductVideoStatus(data.operation_name);
                } else { alert('Error: ' + (data.error || 'Failed to start video generation')); btn.disabled = false; hideEl('aiVideoLoading'); }
            } catch (err) { console.error(err); alert('A network error occurred.'); btn.disabled = false; hideEl('aiVideoLoading'); }
        }

        function pollProductVideoStatus(operationName) {
            const pollInterval = setInterval(async () => {
                try {
                    const response = await fetch('index.php?controller=product&action=aiGenerateVideoStatus', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ operation_name: operationName, product_id: productId })
                    });
                    const data = await response.json();
                    if (data.error) {
                        clearInterval(pollInterval);
                        alert('Error: ' + data.error);
                        document.getElementById('aiVideoBtn').disabled = false;
                        hideEl('aiVideoLoading');
                        return;
                    }
                    if (data.success && data.done) {
                        clearInterval(pollInterval);
                        const vidContainer = document.getElementById('aiVideoContainer');
                        vidContainer.innerHTML = `
                            <video src="${data.video_url}" controls autoplay loop style="width:100%; aspect-ratio:9/16; object-fit:cover;"></video>
                            <div style="padding:0.5rem; background:#000; display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,0.05);">
                                <span style="font-size:0.65rem; color:#555;">Veo 3.1</span>
                                <a href="${data.video_url}" download="ai_generated_video.mp4" style="font-size:0.65rem; color:#2dd4bf; font-weight:700; text-decoration:none;"><i class="fas fa-download mr-1"></i>Download</a>
                            </div>`;
                        document.getElementById('aiVideoResult').classList.remove('hidden');
                        document.getElementById('aiVideoResult').style.display = 'block';
                        hideEl('aiVideoLoading');
                        document.getElementById('aiVideoBtn').disabled = false;
                    }
                } catch (err) {
                    console.error(err);
                    clearInterval(pollInterval);
                    alert('Polling error occurred.');
                    document.getElementById('aiVideoBtn').disabled = false;
                    hideEl('aiVideoLoading');
                }
            }, 10000);
        }
        function validateProductForm(form) {
            const weightInputs = document.querySelectorAll('.img-order-input');
            if (weightInputs.length > 0) {
                const weights = [];
                let hasZero = false;
                let hasDuplicate = false;

                weightInputs.forEach(input => {
                    input.style.borderColor = '';
                    const val = parseInt(input.value, 10);
                    if (isNaN(val)) return;

                    if (val === 0) hasZero = true;

                    if (weights.includes(val)) {
                        hasDuplicate = true;
                    } else {
                        weights.push(val);
                    }
                });

                if (hasDuplicate || !hasZero) {
                    reindexImageWeightsUI();
                }
            }

            const buttons = [
                { btn: document.getElementById('btnSubmitProduct'), icon: document.getElementById('submitBtnIcon'), text: document.getElementById('submitBtnText') },
                { btn: document.getElementById('btnSubmitProductTop'), icon: document.getElementById('submitBtnIconTop'), text: document.getElementById('submitBtnTextTop') }
            ];

            buttons.forEach(b => {
                if (b.btn) {
                    if (b.icon) b.icon.className = 'fas fa-spinner fa-spin mr-1';
                    if (b.text) {
                        b.text.textContent = b.text.textContent.includes('Sync') ? 'Syncing & Updating...' : 'Updating...';
                    }
                    setTimeout(() => { b.btn.disabled = true; }, 50);
                }
            });

            return true;
        }

        async function updateImageWeight(imageId, inputElem) {
            const val = parseInt(inputElem.value, 10);
            const weightInputs = document.querySelectorAll('.img-order-input');
            
            // Check for duplicates
            let duplicateCount = 0;
            weightInputs.forEach(inp => {
                inp.style.borderColor = '';
                if (parseInt(inp.value, 10) === val) {
                    duplicateCount++;
                }
            });

            if (duplicateCount > 1) {
                inputElem.style.borderColor = '#ef4444';
                alert(`Validation Error: Order Weight ${val} is already assigned to another image! Each image must have a unique order weight.`);
                return;
            }

            inputElem.style.borderColor = '#3b82f6';
            try {
                const response = await fetch('index.php?controller=product&action=updateImageWeight', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image_id: imageId, weight: val })
                });
                const data = await response.json();
                if (data.success) {
                    inputElem.style.borderColor = '#10b981';
                    inputElem.style.color = '#10b981';
                    setTimeout(() => {
                        inputElem.style.borderColor = '';
                        inputElem.style.color = '';
                    }, 1000);
                } else {
                    alert('Error updating image weight: ' + (data.error || 'Unknown error'));
                    inputElem.style.borderColor = '#ef4444';
                }
            } catch (err) {
                console.error(err);
                inputElem.style.borderColor = '#ef4444';
            }
        }
        // --- WordPress-Style Multi-Category Checkbox Helpers ---
        function wpUpdateCatCounter() {
            document.querySelectorAll('.wp-cat-node').forEach(node => {
                const mainCb = node.querySelector('.wp-cat-check');
                const titleEl = node.querySelector('.wp-cat-title');
                const iconEl = node.querySelector('.wp-cat-icon');
                if (mainCb && mainCb.checked) {
                    node.classList.add('wp-cat-selected');
                    node.style.background = '#fefce8';
                    node.style.borderColor = '#fde047';
                    if (titleEl) titleEl.style.color = '#713f12';
                    if (iconEl) iconEl.style.color = '#ca8a04';
                } else {
                    node.classList.remove('wp-cat-selected');
                    node.style.background = '#ffffff';
                    node.style.borderColor = '#e2e8f0';
                    if (titleEl) titleEl.style.color = '#0f172a';
                    if (iconEl) iconEl.style.color = '#64748b';
                }

                node.querySelectorAll('.wp-sub-node').forEach(subNode => {
                    const subCb = subNode.querySelector('.wp-sub-check');
                    const subIconEl = subNode.querySelector('.wp-sub-icon');
                    if (subCb && subCb.checked) {
                        subNode.classList.add('wp-sub-selected');
                        subNode.style.color = '#713f12';
                        subNode.style.fontWeight = '600';
                        subNode.style.borderColor = '#fde047';
                        subNode.style.background = '#fef9c3';
                        if (subIconEl) subIconEl.style.color = '#ca8a04';
                    } else {
                        subNode.classList.remove('wp-sub-selected');
                        subNode.style.color = '#475569';
                        subNode.style.fontWeight = '500';
                        subNode.style.borderColor = 'transparent';
                        subNode.style.background = 'transparent';
                        if (subIconEl) subIconEl.style.color = '#94a3b8';
                    }
                });
            });

            const mainChecked = document.querySelectorAll('.wp-cat-check:checked').length;
            const subChecked = document.querySelectorAll('.wp-sub-check:checked').length;
            const total = mainChecked + subChecked;
            const counter = document.getElementById('wpCategoryCounter');
            if (counter) {
                counter.textContent = `${total} Selected (${mainChecked} Main, ${subChecked} Sub)`;
                if (total > 0) {
                    counter.style.background = '#fef9c3';
                    counter.style.color = '#713f12';
                    counter.style.borderColor = '#fde047';
                    counter.style.boxShadow = 'none';
                } else {
                    counter.style.background = '#f1f5f9';
                    counter.style.color = '#475569';
                    counter.style.borderColor = '#e2e8f0';
                    counter.style.boxShadow = 'none';
                }
            }

            if (typeof checkSyncApplicability === 'function') {
                checkSyncApplicability();
            }
        }

        function wpToggleSubTree(treeId, btn) {
            const tree = document.getElementById(treeId);
            if (!tree) return;
            const icon = btn.querySelector('i');
            if (tree.style.display === 'none') {
                tree.style.display = 'grid';
                if (icon) icon.className = 'fas fa-chevron-down';
            } else {
                tree.style.display = 'none';
                if (icon) icon.className = 'fas fa-chevron-right';
            }
        }

        function wpToggleAllCategories(check) {
            document.querySelectorAll('.wp-cat-check, .wp-sub-check').forEach(cb => {
                cb.checked = check;
            });
            wpUpdateCatCounter();
        }

        function wpToggleAllTrees(expand) {
            document.querySelectorAll('.wp-sub-tree').forEach(tree => {
                tree.style.display = expand ? 'grid' : 'none';
            });
            document.querySelectorAll('.wp-cat-node button i').forEach(icon => {
                icon.className = expand ? 'fas fa-chevron-down' : 'fas fa-chevron-right';
            });
        }

        function toggleAiToolsAccordion() {
            const body = document.getElementById('aiAccordionBody');
            const chevron = document.getElementById('aiAccordionChevron');
            const text = document.getElementById('aiAccordionStateText');
            if (!body) return;
            const isHidden = body.style.display === 'none' || body.classList.contains('hidden');
            if (isHidden) {
                body.classList.remove('hidden');
                body.style.display = 'block';
                if (chevron) chevron.className = 'fas fa-chevron-up text-xs';
                if (text) text.textContent = 'Collapse AI Tools';
            } else {
                body.classList.add('hidden');
                body.style.display = 'none';
                if (chevron) chevron.className = 'fas fa-chevron-down text-xs';
                if (text) text.textContent = 'Show AI Studio';
            }
        }

        function expandAiToolsAccordion() {
            const body = document.getElementById('aiAccordionBody');
            const chevron = document.getElementById('aiAccordionChevron');
            const text = document.getElementById('aiAccordionStateText');
            if (body) {
                body.classList.remove('hidden');
                body.style.display = 'block';
                if (chevron) chevron.className = 'fas fa-chevron-up text-xs';
                if (text) text.textContent = 'Collapse AI Tools';
            }
        }

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                const form = document.getElementById('productEditForm');
                if (form) {
                    form.requestSubmit();
                }
            }
        });

        function wpFilterCategoryTree() {
            const query = (document.getElementById('wpCategorySearch')?.value || '').trim().toLowerCase();
            document.querySelectorAll('.wp-cat-node').forEach(node => {
                const catName = node.getAttribute('data-cat-name') || '';
                let hasMatchingSub = false;
                node.querySelectorAll('.wp-sub-node').forEach(subNode => {
                    const subName = subNode.getAttribute('data-sub-name') || '';
                    if (!query || subName.includes(query) || catName.includes(query)) {
                        subNode.style.display = 'flex';
                        hasMatchingSub = true;
                    } else {
                        subNode.style.display = 'none';
                    }
                });

                if (!query || catName.includes(query) || hasMatchingSub) {
                    node.style.display = 'block';
                } else {
                    node.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            wpUpdateCatCounter();
            renderSelectedColors();

            // Auto-detect colors from product image asynchronously on page load if none are set
            if (selectedColors.length === 0) {
                aiDetectColors(true);
            }
        });

        // --- Product Color Multi-Select Logic ---
        const allAvailableColors = <?php echo json_encode($availableColors ?? []); ?>;
        let selectedColors = <?php echo json_encode($product['colors'] ?? []); ?>;
        
        // Color mapping for swatches
        const colorHexMap = {
            'antique gold': '#d97706',
            'azure blue': '#0284c7',
            'baby pink': '#f472b6',
            'beige': '#d4c5a9',
            'black': '#18181b',
            'blue': '#2563eb',
            'bottle green': '#064e3b',
            'brown': '#78350f',
            'coral': '#fb7185',
            'cream': '#fef3c7',
            'dark gold': '#b45309',
            'dark green': '#14532d',
            'emerald green': '#059669',
            'fuchsia pink': '#db2777',
            'gold': '#eab308',
            'golden': '#eab308',
            'green': '#22c55e',
            'green kundan': '#15803d',
            'grey': '#71717a',
            'indigo': '#4f46e5',
            'kundan': '#fef08a',
            'light gold': '#fde047',
            'lime green': '#84cc16',
            'magenta': '#c026d3',
            'maroon': '#881337',
            'mauve': '#a855f7',
            'mint green': '#6ee7b7',
            'multicolor': 'linear-gradient(135deg, #ef4444, #eab308, #22c55e, #3b82f6)',
            'mustard': '#ca8a04',
            'navy blue': '#1e3a8a',
            'off white': '#f5f5f4',
            'olive green': '#65a30d',
            'orange': '#ea580c',
            'peach': '#fdba74',
            'peacock blue': '#0284c7',
            'pearl': '#f8fafc',
            'pink': '#ec4899',
            'purple': '#9333ea',
            'red': '#dc2626',
            'rhodolite': '#9f1239',
            'rose gold': '#f43f5e',
            'royal blue': '#1d4ed8',
            'ruby': '#e11d48',
            'rust': '#c2410c',
            'sea green': '#0d9488',
            'silver': '#94a3b8',
            'sky blue': '#38bdf8',
            'teal': '#0f766e',
            'turquoise': '#06b6d4',
            'vilandi': '#fde047',
            'white': '#ffffff',
            'white kundan': '#fafafa',
            'white pearl': '#f1f5f9',
            'wine': '#4c0519',
            'yellow': '#eab308'
        };

        const popularQuickPickColors = [
            'Gold', 'Silver', 'Rose Gold', 'Antique Gold', 'Red', 'Maroon', 
            'Ruby', 'Green', 'Emerald Green', 'Pink', 'Baby Pink', 'White', 
            'Off White', 'Kundan', 'Yellow', 'Blue', 'Black', 'Multicolor'
        ];

        function getColorSwatch(colorName) {
            const key = String(colorName || '').trim().toLowerCase();
            return colorHexMap[key] || '#ec4899';
        }

        function renderSelectedColors() {
            const container = document.getElementById('selectedColorsContainer');
            const counter = document.getElementById('selectedColorsCounter');
            const hiddenInputs = document.getElementById('hiddenColorInputs');
            if (!container || !counter || !hiddenInputs) return;

            // Update Counter
            counter.textContent = `${selectedColors.length} Selected`;
            if (selectedColors.length > 0) {
                counter.style.background = '#f1f5f9';
                counter.style.color = '#0f172a';
                counter.style.borderColor = '#cbd5e1';
                counter.style.boxShadow = 'none';
            } else {
                counter.style.background = '#f8fafc';
                counter.style.color = '#64748b';
                counter.style.borderColor = '#e2e8f0';
                counter.style.boxShadow = 'none';
            }

            // Render Tags
            if (selectedColors.length === 0) {
                container.innerHTML = `<span style="font-size: 0.75rem; color: #94a3b8; font-style: italic; padding: 0.2rem 0.4rem;">
                    No colors selected. Pick from popular colors below or search to add.
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
                            <button type="button" class="color-remove-btn" onclick="removeColor('${escapeJsStr(color)}')" title="Remove color">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }).join('');
            }

            // Render Hidden Inputs (both array and JSON string)
            let inputsHtml = selectedColors.map(c => `<input type="hidden" name="colors[]" value="${escapeHtml(c)}">`).join('');
            inputsHtml += `<input type="hidden" name="brand_color" value='${escapeHtml(JSON.stringify(selectedColors))}'>`;
            hiddenInputs.innerHTML = inputsHtml;

            // Update Quick Pick active state
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
            if (dropdown) dropdown.style.display = 'block';
        }

        function filterColorDropdown() {
            const input = document.getElementById('colorSearchInput');
            const dropdown = document.getElementById('colorDropdownList');
            if (!input || !dropdown) return;

            const q = input.value.trim().toLowerCase();
            
            // Combine allAvailableColors with quick pick and existing
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
                             style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0.65rem; border-radius: 6px; cursor: pointer; transition: background 0.15s; ${isSelected ? 'background: #f1f5f9; color: #0f172a; font-weight: 600;' : 'color: #334155;'}"
                             onmouseover="this.style.background='#f8fafc'" 
                             onmouseout="this.style.background='${isSelected ? '#f1f5f9' : 'transparent'}'">
                            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.74rem; font-weight: 500;">
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
            dropdown.style.display = 'block';
        }

        function handleColorInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomColorFromInput();
            } else if (e.key === 'Escape') {
                const dropdown = document.getElementById('colorDropdownList');
                if (dropdown) dropdown.style.display = 'none';
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
                if (dropdown) dropdown.style.display = 'none';
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
            return String(str || '')
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'");
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const searchWrap = document.getElementById('colorSearchInput')?.parentElement?.parentElement;
            if (searchWrap && !searchWrap.contains(e.target)) {
                const dropdown = document.getElementById('colorDropdownList');
                if (dropdown) dropdown.style.display = 'none';
            }
        });

        function aiDetectColors(isAuto = false) {
            const btn = document.getElementById('btnAiDetectColors');
            const icon = document.getElementById('aiDetectColorsIcon');
            const container = document.getElementById('selectedColorsContainer');

            if (btn) btn.disabled = true;
            if (icon) icon.className = 'fas fa-spinner fa-spin';

            if (isAuto && container && selectedColors.length === 0) {
                container.innerHTML = `<span style="font-size: 0.72rem; color: #0284c7; font-style: italic; display: flex; align-items: center; gap: 0.45rem; padding: 0.2rem 0.4rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 0.7rem;"></i> AI analyzing product image to detect colors...
                </span>`;
            }

            const productId = <?php echo (int)($product['id'] ?? 0); ?>;
            const productType = '<?php echo addslashes($type ?? 'jewellery'); ?>';

            fetch(`index.php?controller=product&action=aiSuggestColors&id=${productId}&type=${productType}`)
                .then(r => r.json())
                .then(res => {
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-magic';
                    if (res.success && Array.isArray(res.colors) && res.colors.length > 0) {
                        res.colors.forEach(c => {
                            const trimmed = c.trim();
                            if (!selectedColors.some(sc => sc.toLowerCase() === trimmed.toLowerCase())) {
                                selectedColors.push(trimmed);
                            }
                        });
                        renderSelectedColors();
                    } else {
                        if (!isAuto) {
                            alert(res.error || "No colors detected by AI for this product image.");
                        } else {
                            renderSelectedColors();
                        }
                    }
                })
                .catch(err => {
                    if (btn) btn.disabled = false;
                    if (icon) icon.className = 'fas fa-magic';
                    if (!isAuto) {
                        alert("AI Color Detection failed: " + err);
                    } else {
                        renderSelectedColors();
                    }
                });
        }

        function checkSyncApplicability() {
            if (!window.syncSettings) return;
            const syncAll = !!window.syncSettings.sync_all;
            const enabled = window.syncSettings.enabled_categories || [];

            if (syncAll || enabled.length === 0) {
                updateSyncUi(true);
                return;
            }

            let isApplicable = false;
            if (currentType === 'garments') {
                const catSelect = document.getElementById('garment_cat');
                const subSelect = document.getElementById('garment_subcat');
                const catId = catSelect ? catSelect.value : '';
                const subId = subSelect ? subSelect.value : '';
                if (catId && enabled.includes('garment:' + catId)) isApplicable = true;
                if (subId && enabled.includes('garment:' + subId)) isApplicable = true;
            } else {
                const catSelect = document.getElementById('jewel_cat');
                const subSelect = document.getElementById('jewel_subcat');
                const catId = catSelect ? catSelect.value : '';
                const subId = subSelect ? subSelect.value : '';
                if (catId && enabled.includes('jewel_parent:' + catId)) isApplicable = true;
                if (subId && (enabled.includes('jewel_child:' + subId) || enabled.includes('jewel_parent:' + subId))) isApplicable = true;

                document.querySelectorAll('.wp-cat-check:checked').forEach(cb => {
                    if (enabled.includes('jewel_parent:' + cb.value)) isApplicable = true;
                });
                document.querySelectorAll('.wp-sub-check:checked').forEach(cb => {
                    if (enabled.includes('jewel_child:' + cb.value) || enabled.includes('jewel_parent:' + cb.value)) isApplicable = true;
                });
            }

            updateSyncUi(isApplicable);
        }

        function updateSyncUi(isApplicable) {
            const btnText = document.getElementById('submitBtnText');
            const btnIcon = document.getElementById('submitBtnIcon');
            const indicator = document.getElementById('syncStatusIndicator');

            const btnTextTop = document.getElementById('submitBtnTextTop');
            const btnIconTop = document.getElementById('submitBtnIconTop');
            const indicatorTop = document.getElementById('syncStatusIndicatorTop');

            const label = isApplicable ? 'Sync & Update' : 'Update Product';
            const iconClass = isApplicable ? 'fas fa-sync-alt mr-1 text-emerald-600' : 'fas fa-save mr-1';

            if (btnText && btnIcon) {
                btnText.textContent = label;
                btnIcon.className = iconClass;
            }
            if (btnTextTop && btnIconTop) {
                btnTextTop.textContent = label;
                btnIconTop.className = iconClass;
            }

            const html = isApplicable ? `
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 500; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.25rem 0.6rem; border-radius: 9999px;" title="Category is configured for Yosshitaneha child store sync">
                    <span style="width: 6px; height: 6px; border-radius: 9999px; background: #10b981;"></span>
                    <span>Child Store Sync: <strong>Applicable</strong></span>
                </span>
            ` : '';

            if (indicator) indicator.innerHTML = html;
            if (indicatorTop) indicatorTop.innerHTML = html;
        }

        // Smart Hide on Scroll Down, Show on Scroll Up for product-sticky-bar
        (function() {
            const stickyBar = document.querySelector('.product-sticky-bar');
            const mainContainer = document.querySelector('main.overflow-y-auto') || document.querySelector('main');
            if (!stickyBar) return;

            let lastScrollY = 0;
            let ticking = false;

            function updateStickyBarVisibility(currentY) {
                // If within 50px of the top, always stay visible
                if (currentY <= 50) {
                    stickyBar.classList.remove('bar--hidden');
                } else if (currentY > lastScrollY + 8) {
                    // Scrolling down (below) -> hide
                    stickyBar.classList.add('bar--hidden');
                } else if (currentY < lastScrollY - 8) {
                    // Scrolling up -> show
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
    </script>
</body>
</html>
