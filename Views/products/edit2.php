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

        /* Edit Page Compact Overrides */
        .edit-wrap { max-width: 100%; margin: 0 auto; }

        /* Highlighting for Selected Categories & Subcategories */
        .wp-cat-node.wp-cat-selected {
            background: rgba(234, 179, 8, 0.14) !important;
            border-color: #facc15 !important;
            box-shadow: 0 0 14px rgba(250, 204, 21, 0.2) !important;
        }
        .wp-cat-node.wp-cat-selected .wp-cat-title {
            color: #fef08a !important;
        }
        .wp-cat-node.wp-cat-selected .wp-cat-icon {
            color: #facc15 !important;
        }

        .wp-sub-node.wp-sub-selected {
            background: rgba(234, 179, 8, 0.22) !important;
            border-color: #facc15 !important;
            color: #fef08a !important;
            font-weight: 700 !important;
            box-shadow: 0 0 8px rgba(250, 204, 21, 0.25) !important;
        }
        .wp-sub-node.wp-sub-selected .wp-sub-icon {
            color: #facc15 !important;
        }
        .edit-card {
            background: #0a0a0a !important;
            border: 1px solid var(--border-dark, #1f1f1f) !important;
            border-radius: 10px !important;
            overflow: hidden;
            box-shadow: none !important;
        }
        .edit-tabs {
            display: flex;
            border-bottom: 1px solid #1f1f1f;
            background: #050505;
        }
        .edit-tab {
            flex: 1;
            padding: 0.65rem 0;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            cursor: default;
            border-bottom: 2px solid transparent;
            color: #444;
            transition: all 0.15s;
            background: transparent !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
        }
        .edit-tab--active { color: #6e8efb; border-bottom-color: #6e8efb; }
        .edit-form { padding: 1.25rem !important; }
        .edit-section { margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #1a1a1a; }
        .edit-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .section-hdr {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .section-num {
            width: 22px; height: 22px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 700;
            background: rgba(110,142,251,0.1);
            color: #6e8efb;
            flex-shrink: 0;
        }
        .section-title { font-size: 0.8rem; font-weight: 600; color: #ddd; }
        .field-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.3rem;
        }
        .field-input {
            width: 100%;
            background: #000 !important;
            border: 1px solid #1f1f1f !important;
            border-radius: 6px !important;
            padding: 0.45rem 0.6rem !important;
            font-size: 0.78rem !important;
            color: #ccc !important;
            transition: border-color 0.15s;
        }
        .field-input:focus { border-color: #333 !important; outline: none !important; }
        .field-input:disabled, .field-input[readonly] { color: #555 !important; cursor: not-allowed; background: #050505 !important; }
        .field-input--textarea { min-height: 100px; resize: vertical; line-height: 1.5; }
        .field-grid { display: grid; gap: 0.6rem; }
        .field-grid--2 { grid-template-columns: repeat(2, 1fr); }
        .field-grid--3 { grid-template-columns: repeat(3, 1fr); }
        @media (max-width: 640px) { .field-grid--2, .field-grid--3 { grid-template-columns: 1fr; } }
        .field-span-2 { grid-column: span 2; }
        @media (max-width: 640px) { .field-span-2 { grid-column: span 1; } }

        /* AI Cards */
        .ai-card {
            background: #050505 !important;
            border: 1px solid #1a1a1a !important;
            border-radius: 8px !important;
            padding: 0.85rem !important;
            margin-top: 0.6rem;
            box-shadow: none !important;
        }
        .ai-card-hdr {
            display: flex; align-items: center; gap: 0.4rem;
            margin-bottom: 0.35rem;
        }
        .ai-card-hdr i { font-size: 0.65rem; }
        .ai-card-hdr h4 { font-size: 0.65rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 0.05em; }
        .ai-card-desc { font-size: 0.68rem; color: #555; margin-bottom: 0.6rem; }
        .ai-btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.4rem 0.7rem;
            font-size: 0.7rem; font-weight: 500;
            background: #111; border: 1px solid #1f1f1f; border-radius: 6px;
            color: #999; cursor: pointer; transition: all 0.15s;
            white-space: nowrap;
        }
        .ai-btn:hover { background: #1a1a1a; color: #fff; border-color: #333; }
        .ai-btn i { font-size: 0.6rem; }
        .ai-input {
            background: #000 !important; border: 1px solid #1a1a1a !important;
            border-radius: 6px !important; padding: 0.4rem 0.6rem !important;
            font-size: 0.72rem !important; color: #aaa !important;
        }
        .ai-input:focus { border-color: #333 !important; }

        /* Price source card */
        .info-card {
            background: #050505 !important;
            border: 1px solid #1a1a1a !important;
            border-radius: 8px !important;
            padding: 0.65rem 0.85rem !important;
            margin-bottom: 0.6rem;
            box-shadow: none !important;
        }

        /* Images grid */
        .img-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.5rem; }
        @media (max-width: 640px) { .img-grid { grid-template-columns: repeat(3, 1fr); } }
        .img-card-item {
            display: flex; flex-direction: column;
            background: #0a0a0a; border: 1px solid #1f1f1f;
            border-radius: 8px; overflow: hidden; transition: all 0.2s;
        }
        .img-card-item:hover { border-color: #333; }
        .img-thumb {
            aspect-ratio: 1; position: relative; width: 100%;
            background: #111;
        }
        .img-thumb img { width: 100%; height: 100%; object-fit: contain; }
        .img-thumb .img-overlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.5);
            opacity: 0; transition: opacity 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        .img-thumb:hover .img-overlay { opacity: 1; }
        .img-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.35rem 0.5rem; background: #0c0c0c;
            border-top: 1px solid #1a1a1a;
        }
        .img-order-label {
            font-size: 0.65rem; font-weight: 700; color: #888;
            text-transform: uppercase; letter-spacing: 0.04em;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .img-order-input {
            width: 46px; height: 24px; background: #000;
            border: 1px solid #2a2a2a; border-radius: 4px;
            color: #f472b6; font-weight: 700; font-size: 0.8rem;
            text-align: center; outline: none; transition: all 0.2s;
            -moz-appearance: textfield;
        }
        .img-order-input::-webkit-outer-spin-button,
        .img-order-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .img-order-input:focus {
            border-color: #ec4899; box-shadow: 0 0 0 2px rgba(236,72,153,0.2);
        }
        .img-badge {
            position: absolute; top: 4px; left: 4px;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.55rem; z-index: 5;
        }
        .img-del-btn {
            position: absolute; top: -4px; right: -4px;
            width: 20px; height: 20px; border-radius: 50%;
            background: #ef4444; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.55rem; cursor: pointer;
            opacity: 0; transition: all 0.2s; z-index: 10;
            border: none;
        }
        .img-thumb:hover .img-del-btn { opacity: 1; }
        .img-set-main {
            position: absolute; top: 4px; left: 4px;
            width: 20px; height: 20px; border-radius: 50%;
            background: #6366f1; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.55rem; cursor: pointer;
            opacity: 0; transition: all 0.2s; z-index: 10;
            border: none;
        }
        .img-thumb:hover .img-set-main { opacity: 1; }

        /* Upload zone */
        .upload-zone {
            border: 1px dashed #222 !important;
            border-radius: 8px !important;
            padding: 1.5rem !important;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
            background: transparent !important;
            box-shadow: none !important;
        }
        .upload-zone:hover { border-color: #444 !important; }

        /* Toggle */
        .toggle-row {
            display: flex; align-items: center; gap: 0.5rem;
        }
        .toggle-label { font-size: 0.75rem; font-weight: 500; }

        /* Footer actions */
        .edit-footer {
            display: flex; justify-content: flex-end; gap: 0.5rem;
            padding-top: 1rem; margin-top: 0.5rem;
            border-top: 1px solid #1a1a1a;
        }
        .btn-cancel {
            padding: 0.45rem 1.25rem;
            font-size: 0.75rem; font-weight: 500;
            background: transparent; border: 1px solid #1f1f1f;
            border-radius: 6px; color: #666; cursor: pointer;
            transition: all 0.15s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.3rem;
        }
        .btn-cancel:hover { color: #fff; border-color: #333; }
        .btn-submit {
            padding: 0.45rem 1.5rem;
            font-size: 0.75rem; font-weight: 600;
            background: #fff !important; color: #000 !important;
            border: 1px solid #fff !important; border-radius: 6px;
            cursor: pointer; transition: all 0.15s;
            display: inline-flex; align-items: center; gap: 0.3rem;
        }
        .btn-submit:hover { background: #e5e5e5 !important; border-color: #e5e5e5 !important; }

        /* Success/Error alerts */
        .alert { padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.75rem; margin-bottom: 0.75rem; }
        .alert--success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; }
        .alert--error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; }

        /* Price input with symbol */
        .price-wrap { position: relative; }
        .price-wrap .price-sym {
            position: absolute; left: 0.5rem; top: 50%; transform: translateY(-50%);
            font-size: 0.75rem; color: #444; pointer-events: none;
        }
        .price-wrap .field-input { padding-left: 1.5rem !important; }

        /* Featured toggle override */
        .featured-toggle {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.5rem 0;
        }
        .featured-toggle .toggle-text { font-size: 0.78rem; font-weight: 500; color: #aaa; }
        .featured-toggle .toggle-hint { font-size: 0.65rem; color: #444; margin-top: 0.15rem; }

        /* Sticky mobile footer */
        @media (max-width: 640px) {
            .edit-footer {
                position: sticky; bottom: 0;
                background: #0a0a0a; padding: 0.75rem 1rem;
                margin: 0 -1.25rem -1.25rem -1.25rem;
                border-top: 1px solid #1f1f1f;
                z-index: 50;
            }
            .btn-cancel, .btn-submit { flex: 1; justify-content: center; padding: 0.6rem; }
        }

        /* Desc helper text */
        .desc-hint { font-size: 0.62rem; color: #444; margin-top: 0.2rem; }
        .desc-hint a { color: #6366f1; text-decoration: none; }
        .desc-hint a:hover { text-decoration: underline; }
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

            <main class="flex-1 overflow-y-auto p-2 lg:p-3">
                <div class="edit-wrap">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert--success"><i class="fas fa-check-circle mr-1"></i> Product updated successfully!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert--error"><i class="fas fa-exclamation-circle mr-1"></i> <?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    <div class="mb-3 flex items-center justify-between p-3 bg-zinc-900 border border-zinc-800 rounded-xl">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white"><i class="fas fa-sync-alt text-teal-400 mr-1.5"></i> Yosshitaneha Store Sync</span>
                            <span class="text-[11px] text-zinc-400">(Child Buy Store)</span>
                        </div>
                        <button type="button" id="btnSyncSingle" onclick="syncSingleProduct(<?php echo $product['id']; ?>, '<?php echo $type; ?>')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1.5">
                            <i class="fas fa-paper-plane" id="syncSingleIcon"></i>
                            <span>Sync to Child Store</span>
                        </button>
                    </div>

                    <div class="edit-card">
                        <!-- Tabs -->
                        <div class="edit-tabs">
                            <button type="button" class="edit-tab <?php echo $type === 'jewellery' ? 'edit-tab--active' : ''; ?>">
                                <i class="fas fa-gem mr-1"></i> Jewellery
                            </button>
                            <button type="button" class="edit-tab <?php echo $type === 'garments' ? 'edit-tab--active' : ''; ?>">
                                <i class="fas fa-tshirt mr-1"></i> Garments / Apparel
                            </button>
                        </div>

                        <form action="index.php?controller=product&action=update" method="POST" enctype="multipart/form-data" class="edit-form" onsubmit="return validateProductForm(this)">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="type" id="product_type" value="<?php echo $type; ?>">
                            <input type="hidden" name="code" value="<?php echo htmlspecialchars($product['code']); ?>">

                            <!-- Section 1: Product Code -->
                            <div class="edit-section">
                                <div class="section-hdr">
                                    <div class="section-num">1</div>
                                    <div class="section-title">Product Code</div>
                                </div>
                                <div class="field-grid field-grid--2">
                                    <div>
                                        <label class="field-label">SKU / Product Code</label>
                                        <input type="text" value="<?php echo htmlspecialchars($product['code']); ?>" disabled class="field-input">
                                    </div>
                                    <div>
                                        <label class="field-label">Database ID (<?php echo $type === 'garments' ? 'gproduct_id' : 'product_id'; ?>)</label>
                                        <input type="text" value="<?php echo $product['id']; ?>" disabled class="field-input" style="color: #60a5fa; font-weight: 700;">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Basic Information -->
                            <div class="edit-section" id="form_content">
                                <div class="section-hdr">
                                    <div class="section-num">2</div>
                                    <div class="section-title">Basic Information</div>
                                </div>
                                <div class="field-grid field-grid--2">
                                    <div class="field-span-2">
                                        <label class="field-label">Product Name</label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="field-input">
                                    </div>
                                    <div class="field-span-2">
                                        <label class="field-label">Description</label>
                                        <textarea name="description" id="product_desc_textarea" rows="5" class="field-input field-input--textarea"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                        <p class="desc-hint">
                                            <i class="fas fa-info-circle"></i>
                                            Formatting issues? Use the <a href="index.php?controller=product&action=descriptionCorrector">Description Corrector</a>.
                                        </p>
                                    </div>

                                    <!-- AI Copywriter -->
                                    <div class="field-span-2">
                                        <div class="ai-card">
                                            <div class="ai-card-hdr">
                                                <i class="fas fa-magic" style="color: #818cf8;"></i>
                                                <h4>AI Copywriter (Gemini)</h4>
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
                                            <div id="aiLoading" class="hidden" style="display:none; align-items:center; justify-content:center; gap:0.5rem; padding:0.6rem; background:#111; border-radius:6px; margin-top:0.6rem; font-size:0.72rem; color:#666;">
                                                <div style="width:14px; height:14px; border:2px solid #818cf8; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
                                                <span>AI is analyzing image...</span>
                                            </div>
                                            <!-- Name results -->
                                            <div id="aiNamesResult" class="hidden" style="margin-top:0.6rem; padding-top:0.6rem; border-top:1px solid #1a1a1a;">
                                                <h5 style="font-size:0.62rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Suggested Names (Click to Apply)</h5>
                                                <div id="aiNamesList" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:0.35rem;"></div>
                                            </div>
                                            <!-- Desc results -->
                                            <div id="aiDescResult" class="hidden" style="margin-top:0.6rem; padding-top:0.6rem; border-top:1px solid #1a1a1a;">
                                                <h5 style="font-size:0.62rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Suggested Description</h5>
                                                <textarea id="aiDescTextarea" rows="5" class="field-input field-input--textarea" style="margin-bottom:0.4rem;"></textarea>
                                                <button type="button" onclick="applyAiDescription()" id="applyDescBtn" class="btn-submit" style="width:100%;">
                                                    Apply to Description Field
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Advanced AI Image Studio -->
                                    <div class="field-span-2">
                                        <div class="ai-card">
                                            <div class="ai-card-hdr">
                                                <i class="fas fa-camera" style="color: #f472b6;"></i>
                                                <h4>AI Image Studio (Gemini)</h4>
                                            </div>
                                            <p class="ai-card-desc">Generate an AI fashion model wearing this exact product.</p>
                                            
                                            <div style="display:flex; flex-direction:column; gap:0.8rem; margin-top:0.8rem;">
                                                
                                                <!-- Face Reference Models -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Model Face (Optional)</label>
                                                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                                        <label class="model-picker-label relative group">
                                                            <input type="radio" name="ai_model_face" value="" checked class="hidden peer">
                                                            <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-500/30 border border-zinc-800 rounded-xl overflow-hidden cursor-pointer transition-all opacity-70 peer-checked:opacity-100 bg-zinc-900 flex items-center justify-center" style="width:64px; height:64px;">
                                                                <span style="font-size:0.65rem; color:#aaa; font-weight:700;">NONE</span>
                                                            </div>
                                                        </label>
                                                        <?php for($i=1; $i<=10; $i++): ?>
                                                        <label class="model-picker-label relative group">
                                                            <input type="radio" name="ai_model_face" value="model_<?= $i ?>.png" class="hidden peer">
                                                            <div class="peer-checked:border-pink-500 peer-checked:ring-2 peer-checked:ring-pink-500/30 border border-zinc-800 rounded-xl overflow-hidden cursor-pointer transition-all opacity-70 peer-checked:opacity-100 hover:opacity-100 bg-zinc-900" style="width:64px; height:64px;" title="Model <?= $i ?>">
                                                                <img src="assets/models/model_<?= $i ?>.png" alt="Model <?= $i ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.parentElement.parentElement.style.display='none'">
                                                            </div>

                                                            <!-- Hover Zoom Popover -->
                                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:flex flex-col items-center z-50 pointer-events-none">
                                                                <div class="bg-[#0a0a0a] p-1.5 rounded-xl border border-pink-500/40 shadow-2xl shadow-pink-500/20 w-44 h-44 overflow-hidden">
                                                                    <img src="assets/models/model_<?= $i ?>.png" alt="Model <?= $i ?>" class="w-full h-full object-cover rounded-lg">
                                                                </div>
                                                                <div class="text-[10px] font-bold text-pink-400 bg-black/90 px-2.5 py-0.5 rounded-full border border-pink-500/30 -mt-2 uppercase tracking-wider shadow-lg">
                                                                    Model <?= $i ?>
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>

                                                <!-- Background Presets -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Background / Props</label>
                                                    <div style="display:flex; gap:0.4rem; flex-wrap:wrap;" id="bg_preset_container">
                                                        <?php
                                                        $bgPresets = [
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
                                                        foreach($bgPresets as $label => $promptPart):
                                                        ?>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_bg_preset" value="<?= htmlspecialchars($promptPart) ?>" <?= $first ? 'checked' : '' ?> class="hidden peer">
                                                            <div class="peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 border border-zinc-800 bg-zinc-900 text-zinc-400 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all hover:bg-zinc-800">
                                                                <?= $label ?>
                                                            </div>
                                                        </label>
                                                        <?php $first=false; endforeach; ?>
                                                    </div>
                                                    <input type="text" id="ai_bg_custom" class="ai-input mt-2 w-full" value="elegant royal palace with marble pillars and chandeliers" placeholder="Describe the background and props...">
                                                </div>

                                                <!-- Shot & Hair Controls -->
                                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                                    <div>
                                                        <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Shot Type</label>
                                                        <div style="display:flex; flex-direction:column; gap:0.3rem;">
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_shot_type" value="close-up portrait shot focusing on the face and the jewelry"> Close-up Portrait</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_shot_type" value="half body shot from waist up, showing the model's torso and face"> Half Body</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_shot_type" value="full body head-to-toe shot showing the complete outfit/jewelry look" checked> Full Body</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_shot_type" value="shot from behind showing the back design and details of the product"> Back View</label>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Hair Style</label>
                                                        <div style="display:flex; flex-direction:column; gap:0.3rem;">
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="open flowing hair with soft waves (khule baal)"> Open Flowing</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="neatly tied bun with gajra flowers"> Tied / Bun</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="traditional long braided hair (gajra choti)"> Traditional Braid (Choti)</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="elegant half-up half-down hairstyle"> Half Up, Half Down</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="glamorous side-swept waves"> Side Swept Waves</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="sleek straight hair with center part"> Sleek Straight</label>
                                                            <label class="flex items-center gap-2 text-xs text-zinc-300 cursor-pointer"><input type="radio" name="ai_hair_style" value="" checked> As per product</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Final Prompt Textarea -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Final Prompt (Edit if needed)</label>
                                                    <textarea id="ai_final_prompt" rows="4" class="ai-input w-full" style="resize:vertical;">A photorealistic beautiful Indian fashion model wearing this exact <?php echo htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product')); ?>. The background should have elegant royal palace with marble pillars and chandeliers. Shot type: full body head-to-toe shot showing the complete outfit/jewelry look. Do not change the <?php echo htmlspecialchars($product['category_name'] ?? ($product['subcategory_name'] ?? 'product')); ?> details. Aspect ratio: 2:3 vertical fashion portrait format.</textarea>
                                                </div>

                                                <!-- Quantity Selector -->
                                                <div>
                                                    <label style="font-size:0.65rem; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem; display:block;">Number of Images (Variations)</label>
                                                    <div style="display:flex; gap:0.5rem;">
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="1" checked class="hidden peer">
                                                            <div class="peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 border border-zinc-800 bg-zinc-900 text-zinc-400 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">1 Image</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="2" class="hidden peer">
                                                            <div class="peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 border border-zinc-800 bg-zinc-900 text-zinc-400 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">2 Images</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="3" class="hidden peer">
                                                            <div class="peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 border border-zinc-800 bg-zinc-900 text-zinc-400 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">3 Images</div>
                                                        </label>
                                                        <label class="bg-picker-label">
                                                            <input type="radio" name="ai_num_images" value="4" class="hidden peer">
                                                            <div class="peer-checked:bg-pink-600 peer-checked:text-white peer-checked:border-pink-600 border border-zinc-800 bg-zinc-900 text-zinc-400 px-4 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-all">4 Images</div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Generate Button -->
                                                <button type="button" onclick="aiGenerateAdvancedImage()" id="aiImageBtn" class="btn-submit" style="justify-content:center; padding:0.6rem; margin-top:0.5rem; background:#f472b6 !important; border-color:#f472b6 !important; color:#000 !important;">
                                                    <i class="fas fa-magic"></i> Generate Model Image
                                                </button>
                                            </div>

                                            <!-- Loading -->
                                            <div id="aiImageLoading" class="hidden" style="display:none; flex-direction:column; align-items:center; gap:0.5rem; padding:1rem; background:#111; border-radius:6px; margin-top:0.6rem; font-size:0.72rem; color:#666;">
                                                <div style="width:18px; height:18px; border:2px solid #f472b6; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
                                                <span>AI is generating image. This may take 15-20 seconds...</span>
                                            </div>
                                            <!-- Result -->
                                            <div id="aiImageResult" class="hidden" style="margin-top:0.8rem; padding-top:0.8rem; border-top:1px solid #1a1a1a;">
                                                <h5 style="font-size:0.62rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Generated Images</h5>
                                                
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

                                    <!-- AI Video Studio - HIDDEN per user request -->
                                    <div class="field-span-2" style="display: none;">
                                        <div class="ai-card">
                                            <div class="ai-card-hdr">
                                                <i class="fas fa-video" style="color: #2dd4bf;"></i>
                                                <h4>AI Video Studio (Veo 3.1)</h4>
                                            </div>
                                            <p class="ai-card-desc">Generate an 8-second cinematic AI video of this product.</p>
                                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                                <input type="text" id="aiVideoPrompt" value="A beautiful, cinematic showcase of this fashion product. The product is the central focus." class="ai-input" style="flex:1;" placeholder="Enter video prompt...">
                                                <button type="button" onclick="aiGenerateVideo()" id="aiVideoBtn" class="ai-btn">
                                                    <i class="fas fa-film"></i> <span id="aiVideoBtnText">Generate Video</span>
                                                </button>
                                            </div>
                                            <div id="aiVideoLoading" class="hidden" style="display:none; flex-direction:column; align-items:center; gap:0.5rem; padding:1rem; background:#111; border-radius:6px; margin-top:0.6rem; font-size:0.72rem; color:#666;">
                                                <div style="width:18px; height:18px; border:2px solid #2dd4bf; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
                                                <span id="aiVideoLoadingText">Starting video generation...</span>
                                            </div>
                                            <div id="aiVideoResult" class="hidden" style="margin-top:0.6rem; padding-top:0.6rem; border-top:1px solid #1a1a1a;">
                                                <h5 style="font-size:0.62rem; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.4rem;">Generated Video</h5>
                                                <div style="display:flex; justify-content:center; background:#000; border-radius:6px; padding:0.5rem; border:1px solid #1a1a1a;">
                                                    <div id="aiVideoContainer" style="width:100%; max-width:300px; border-radius:6px; overflow:hidden; border:1px solid rgba(255,255,255,0.05); background:#000;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category & Subcategory: WordPress-Style Multi-Select Checkbox Component -->
                                    <div class="field-span-2">
                                        <div style="background: #09090b; border: 1px solid #27272a; border-radius: 12px; padding: 1.25rem; margin-bottom: 0.5rem;">
                                            <!-- Header -->
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.85rem; flex-wrap: wrap;">
                                                <div>
                                                    <label style="font-size: 0.85rem; font-weight: 700; color: #f4f4f5; display: flex; align-items: center; gap: 0.4rem;">
                                                        <i class="fas fa-sitemap" style="color: #818cf8;"></i> Categories & Subcategories (Multi-Select)
                                                    </label>
                                                    <span style="font-size: 0.7rem; color: #a1a1aa; display: block; margin-top: 2px;">
                                                        Select all main categories and subcategories this product belongs to.
                                                    </span>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                    <span id="wpCategoryCounter" style="background: rgba(129, 140, 248, 0.15); color: #a5b4fc; font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; border: 1px solid rgba(129, 140, 248, 0.3);">
                                                        0 Selected
                                                    </span>
                                                    <button type="button" onclick="wpToggleAllCategories(true)" style="background: transparent; border: none; color: #a1a1aa; font-size: 0.7rem; font-weight: 600; cursor: pointer; text-decoration: underline;">Select All</button>
                                                    <span style="color: #3f3f46;">•</span>
                                                    <button type="button" onclick="wpToggleAllCategories(false)" style="background: transparent; border: none; color: #a1a1aa; font-size: 0.7rem; font-weight: 600; cursor: pointer; text-decoration: underline;">Clear</button>
                                                </div>
                                            </div>

                                            <!-- Search Bar -->
                                            <div style="position: relative; margin-bottom: 0.85rem;">
                                                <i class="fas fa-search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; color: #71717a;"></i>
                                                <input type="text" id="wpCategorySearch" oninput="wpFilterCategoryTree()" placeholder="Quick filter categories..." style="width: 100%; background: #18181b; border: 1px solid #27272a; border-radius: 8px; padding: 0.45rem 0.75rem 0.45rem 2.2rem; font-size: 0.78rem; color: #fff; outline: none; transition: border-color 0.15s;" onfocus="this.style.borderColor='#818cf8'" onblur="this.style.borderColor='#27272a'">
                                            </div>

                                            <!-- Category Tree Box -->
                                            <div id="wpCategoryTree" style="max-height: 360px; overflow-y: auto; background: #121215; border: 1px solid #27272a; border-radius: 8px; padding: 0.6rem; display: flex; flex-direction: column; gap: 0.4rem;">
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
                                                        <div class="wp-cat-node <?php echo $isMainChecked ? 'wp-cat-selected' : ''; ?>" data-cat-name="<?php echo htmlspecialchars(strtolower($mainCat['name'])); ?>" style="background: <?php echo $isMainChecked ? 'rgba(234, 179, 8, 0.14)' : 'rgba(24, 24, 27, 0.6)'; ?>; border: 1px solid <?php echo $isMainChecked ? '#facc15' : 'rgba(39, 39, 42, 0.8)'; ?>; border-radius: 8px; padding: 0.5rem 0.75rem; transition: all 0.15s ease;">
                                                            <!-- Main Category Row -->
                                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                                                <label style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; font-weight: 700; color: <?php echo $isMainChecked ? '#fef08a' : '#f4f4f5'; ?>; cursor: pointer; user-select: none;">
                                                                    <input type="checkbox" name="categories[]" value="<?php echo $mainCat['id']; ?>" <?php echo $isMainChecked ? 'checked' : ''; ?> onchange="wpUpdateCatCounter()" class="wp-cat-check" style="width: 15px; height: 15px; accent-color: #facc15; cursor: pointer;">
                                                                    <i class="fas <?php echo !empty($subList) ? 'fa-folder' : 'fa-tag'; ?> wp-cat-icon" style="font-size: 0.8rem; color: <?php echo $isMainChecked ? '#facc15' : '#818cf8'; ?>;"></i>
                                                                    <span class="wp-cat-title"><?php echo htmlspecialchars($mainCat['name']); ?></span>
                                                                </label>
                                                                <?php if (!empty($subList)): ?>
                                                                    <button type="button" onclick="wpToggleSubTree('sub_tree_<?php echo $catIndex; ?>', this)" style="background: rgba(39, 39, 42, 0.6); border: 1px solid rgba(63, 63, 70, 0.5); color: #a1a1aa; font-size: 0.68rem; font-weight: 600; padding: 0.15rem 0.55rem; border-radius: 9999px; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                                                        <span><?php echo count($subList); ?> subcategories</span>
                                                                        <i class="fas <?php echo $isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'; ?>" style="font-size: 0.6rem;"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- Nested Subcategories -->
                                                            <?php if (!empty($subList)): ?>
                                                                <div id="sub_tree_<?php echo $catIndex; ?>" class="wp-sub-tree" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed rgba(39, 39, 42, 0.8); padding-left: 1.6rem; display: <?php echo $isExpanded ? 'grid' : 'none'; ?>; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.4rem;">
                                                                    <?php foreach ($subList as $sub): ?>
                                                                        <?php $isSubChecked = in_array((int)$sub['id'], $assignedSub); ?>
                                                                        <label class="wp-sub-node <?php echo $isSubChecked ? 'wp-sub-selected' : ''; ?>" data-sub-name="<?php echo htmlspecialchars(strtolower($sub['name'])); ?>" style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.76rem; font-weight: <?php echo $isSubChecked ? '700' : '500'; ?>; color: <?php echo $isSubChecked ? '#fef08a' : '#d4d4d8'; ?>; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 6px; border: 1px solid <?php echo $isSubChecked ? '#facc15' : 'transparent'; ?>; background: <?php echo $isSubChecked ? 'rgba(234, 179, 8, 0.22)' : 'transparent'; ?>; transition: all 0.15s ease;">
                                                                            <input type="checkbox" name="sub_categories[]" value="<?php echo $sub['id']; ?>" <?php echo $isSubChecked ? 'checked' : ''; ?> onchange="wpUpdateCatCounter()" class="wp-sub-check" style="width: 14px; height: 14px; accent-color: #facc15; cursor: pointer;">
                                                                            <i class="fas fa-tag wp-sub-icon" style="font-size: 0.65rem; color: <?php echo $isSubChecked ? '#facc15' : '#818cf8'; ?>;"></i>
                                                                            <span><?php echo htmlspecialchars($sub['name']); ?></span>
                                                                        </label>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div style="padding: 1.5rem; text-align: center; color: #71717a; font-size: 0.8rem;">
                                                        No categories found for this product type.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Size & Brand Fields -->
                                    <div class="field-span-2">
                                        <div class="field-grid field-grid--2">
                                            <div>
                                                <label class="field-label">Size</label>
                                                <input type="text" name="size_avail" value="<?php echo htmlspecialchars($product['size_avail'] ?? ''); ?>" class="field-input" placeholder="e.g. 5, 6, 7, 8">
                                            </div>
                                            <div>
                                                <label class="field-label">Brand</label>
                                                <input type="text" name="brand_name" value="<?php echo htmlspecialchars($product['brand_name'] ?? ''); ?>" class="field-input" placeholder="e.g. Brand Name">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Featured Toggle -->
                                    <div class="field-span-2">
                                        <div class="featured-toggle">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="featured" value="1" <?php echo ($product['featured'] ?? 0) == 1 ? 'checked' : ''; ?> class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                            </label>
                                            <div>
                                                <div class="toggle-text">Featured Product</div>
                                                <div class="toggle-hint">Displayed in homepage Exclusive Collection.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Pricing -->
                            <div class="edit-section">
                                <div class="section-hdr">
                                    <div class="section-num">3</div>
                                    <div class="section-title">Pricing</div>
                                </div>

                                <!-- Price Source -->
                                <div class="info-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                                    <div>
                                        <label class="field-label" style="margin-bottom:0.1rem;">Price Source</label>
                                        <p id="price_source_description" style="font-size:0.68rem; color:#555; margin:0;">
                                            <?php echo ($product['price_source'] ?? 'pos') === 'manual' 
                                                ? 'Prices are set manually from the fields below.' 
                                                : 'Prices are auto-calculated from POS system data.'; ?>
                                        </p>
                                    </div>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <span class="<?php echo ($product['price_source'] ?? 'pos') === 'pos' ? 'text-primary' : ''; ?>" style="font-size:0.72rem; font-weight:600; color:<?php echo ($product['price_source'] ?? 'pos') === 'pos' ? '#6e8efb' : '#444'; ?>;" id="label_pos">POS</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="price_source" value="manual" 
                                                   <?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'checked' : ''; ?> 
                                                   class="sr-only peer" id="price_source_toggle" onchange="togglePriceSource()">
                                            <div class="w-9 h-5 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                        </label>
                                        <span style="font-size:0.72rem; font-weight:600; color:<?php echo ($product['price_source'] ?? 'pos') === 'manual' ? '#f59e0b' : '#444'; ?>;" id="label_manual">Manual</span>
                                    </div>
                                </div>

                                <!-- Availability -->
                                <div class="info-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                                    <div>
                                        <label class="field-label" style="margin-bottom:0.1rem;">Availability / Nature</label>
                                        <p style="font-size:0.68rem; color:#555; margin:0;">Control if this product is available for Rent, Sell, or Both.</p>
                                    </div>
                                    <select name="availability" class="field-input" style="width:auto; min-width:130px;">
                                        <option value="both" <?php echo ($product['availability'] ?? 'both') === 'both' ? 'selected' : ''; ?>>Rent & Sell (Both)</option>
                                        <option value="rent" <?php echo ($product['availability'] ?? 'both') === 'rent' ? 'selected' : ''; ?>>Rent Only</option>
                                        <option value="sell" <?php echo ($product['availability'] ?? 'both') === 'sell' ? 'selected' : ''; ?>>Sell Only</option>
                                    </select>
                                </div>

                                <!-- Price Fields -->
                                <div class="field-grid field-grid--3" id="pricing_fields" style="margin-top:0.6rem;">
                                    <div>
                                        <label class="field-label">Sales Price</label>
                                        <div class="price-wrap">
                                            <span class="price-sym">₹</span>
                                            <input type="number" name="s_price" step="0.01" value="<?php echo $product['s_price']; ?>" class="field-input">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="field-label">Rental Price</label>
                                        <div class="price-wrap">
                                            <span class="price-sym">₹</span>
                                            <input type="number" name="rental_price" step="0.01" value="<?php echo $product['rental_price']; ?>" class="field-input">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="field-label">Deposit</label>
                                        <div class="price-wrap">
                                            <span class="price-sym">₹</span>
                                            <input type="number" name="deposit" step="0.01" value="<?php echo $product['deposit']; ?>" class="field-input">
                                        </div>
                                    </div>
                                </div>
                                <div id="pos_price_note" class="<?php echo ($product['price_source'] ?? 'pos') === 'manual' ? 'hidden' : ''; ?>" style="margin-top:0.5rem; padding:0.45rem 0.7rem; background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.12); border-radius:6px; font-size:0.68rem; color:#3b82f6;">
                                    <i class="fas fa-info-circle mr-1"></i> These values are stored but <strong>overridden</strong> by POS-calculated prices on the frontend. Switch to "Manual" to use these values directly.
                                </div>
                            </div>

                            <!-- Section 4: Images -->
                            <div class="edit-section" style="border-bottom:none; padding-bottom:0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <div class="section-hdr" style="margin-bottom:0;">
                                        <div class="section-num">4</div>
                                        <div class="section-title">Product Images</div>
                                    </div>
                                    <div style="display: flex; gap: 0.4rem; align-items: center;">
                                        <div id="bulkSelectActions" style="display:none; gap:0.4rem; align-items:center;">
                                            <button type="button" onclick="selectAllImages(true)" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 600; padding: 0.35rem 0.7rem; border-radius: 6px; background: #18181b; border: 1px solid #27272a; color: #e4e4e7; cursor: pointer; transition: all 0.15s;">
                                                <i class="fas fa-check-square" style="color:#38bdf8;"></i> Select All
                                            </button>
                                            <button type="button" onclick="selectAllImages(false)" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 600; padding: 0.35rem 0.7rem; border-radius: 6px; background: #18181b; border: 1px solid #27272a; color: #a1a1aa; cursor: pointer; transition: all 0.15s;">
                                                <i class="far fa-square"></i> Deselect
                                            </button>
                                            <button type="button" id="deleteSelectedBtn" onclick="deleteSelectedImages()" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 600; padding: 0.35rem 0.75rem; border-radius: 6px; background: #dc2626; border: 1px solid #ef4444; color: #ffffff; opacity: 0.5; cursor: not-allowed; transition: all 0.2s;" disabled>
                                                <i class="fas fa-trash-alt"></i> Delete Selected (<span id="selectedCount">0</span>)
                                            </button>
                                            <button type="button" onclick="toggleBulkDeleteMode(false)" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 600; padding: 0.35rem 0.65rem; border-radius: 6px; background: transparent; border: 1px solid #27272a; color: #71717a; cursor: pointer; transition: all 0.15s;">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </div>
                                        <button type="button" id="enableBulkDeleteBtn" onclick="toggleBulkDeleteMode(true)" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.72rem; font-weight: 600; padding: 0.4rem 0.85rem; border-radius: 8px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; cursor: pointer; transition: all 0.2s ease;">
                                            <i class="fas fa-check-double"></i> Select Multiple to Delete
                                        </button>
                                    </div>
                                </div>

                                <!-- Existing Images -->
                                <div id="existing_img_grid" class="img-grid" style="margin-bottom:0.6rem;">
                                    <?php foreach ($images as $index => $img): ?>
                                        <div class="img-card-item" data-image-id="<?php echo $img['id']; ?>" onclick="handleCardClick(event, this)">
                                            <div class="img-thumb">
                                                <label class="img-select-wrapper" style="display:none; position:absolute; top:8px; left:8px; z-index:10; cursor:pointer;">
                                                    <input type="checkbox" class="img-select-checkbox" value="<?php echo $img['id']; ?>" onchange="updateSelectedCount()" style="width:18px; height:18px; accent-color:#ef4444; cursor:pointer;">
                                                </label>
                                                <img src="/ss/yn/uploads<?php echo $img['img_name']; ?>" onerror="this.onerror=null; this.src='http://srishringarr.com/yn/uploads<?php echo $img['img_name']; ?>';" alt="">
                                                <div class="img-overlay">
                                                    <span style="color:#fff; font-size:0.62rem; font-weight:600;">
                                                        <?php echo ($index === 0) ? 'Main' : 'Image'; ?>
                                                    </span>
                                                </div>
                                                <?php if ($index === 0): ?>
                                                    <div class="img-badge" style="background:#f59e0b; color:#fff;" title="Main Image">
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
                                                <span class="img-order-label"><i class="fas fa-sort-numeric-down text-pink-400"></i> Order</span>
                                                <input type="number" min="0" name="image_weights[<?php echo $img['id']; ?>]" value="<?php echo (isset($img['rank']) && (int)$img['rank'] > 0) ? (int)$img['rank'] : $index; ?>" class="img-order-input" onchange="updateImageWeight(<?php echo $img['id']; ?>, this)">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Upload Zone -->
                                <div class="upload-zone">
                                    <input type="file" name="images[]" id="img_upload" multiple accept="image/*" class="hidden">
                                    <label for="img_upload" style="cursor:pointer; display:block;">
                                        <i class="fas fa-cloud-upload-alt" style="font-size:1.5rem; color:#333; margin-bottom:0.4rem; display:block;"></i>
                                        <span style="font-size:0.75rem; color:#555;">Click to add more images</span>
                                    </label>
                                    <div id="img_preview" class="img-grid" style="margin-top:0.5rem;"></div>
                                </div>
                            </div>

                            <!-- Footer Actions -->
                            <div class="edit-footer">
                                <a href="index.php?controller=product&action=index" class="btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save"></i> Update Product
                                </button>
                            </div>
                        </form>
                    </div>
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

        window.addEventListener('DOMContentLoaded', () => {
            if (currentCatId) {
                const targetId = currentType === 'jewellery' ? 'jewel_subcat' : 'garment_subcat';
                fetchSubcategories(currentType, currentCatId, targetId, currentSubId);
            }
        });

        if (document.getElementById('jewel_cat')) {
            document.getElementById('jewel_cat').addEventListener('change', function() {
                fetchSubcategories('jewellery', this.value, 'jewel_subcat');
            });
        }
        if (document.getElementById('garment_cat')) {
            document.getElementById('garment_cat').addEventListener('change', function() {
                fetchSubcategories('garments', this.value, 'garment_subcat');
            });
        }

        async function fetchSubcategories(type, parentId, targetId, selectedId = null) {
            const subDropdown = document.getElementById(targetId);
            if (!parentId) {
                subDropdown.innerHTML = '<option value="">Select Subcategory</option>';
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
            } catch (error) { console.error('Error fetching subcategories:', error); }
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
            btn.disabled = true;
            showEl('aiLoading');
            hideEl('aiNamesResult');
            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestNames&id=${productId}&type=${productType}`);
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
            btn.disabled = true;
            showEl('aiLoading');
            hideEl('aiDescResult');
            try {
                const response = await fetch(`index.php?controller=product&action=aiSuggestDescription&id=${productId}&type=${productType}&max_words=${maxWords}`);
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
            
            // Gather inputs
            const faceInput = document.querySelector('input[name="ai_model_face"]:checked').value;
            const customBg = document.getElementById('ai_bg_custom').value.trim();
            const shotType = document.querySelector('input[name="ai_shot_type"]:checked').value;
            const hairStyle = document.querySelector('input[name="ai_hair_style"]:checked').value;

            // Assemble background part
            let bgPrompt = customBg;
            if (!bgPrompt) bgPrompt = 'clean studio background';

            // Assemble final prompt
            let promptParts = [
                `A photorealistic beautiful Indian fashion model wearing this exact ${catName}.`,
                `The background should have ${bgPrompt}.`,
                `Shot type: ${shotType}.`,
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

        // Attach listeners to all inputs to live-update the prompt textarea
        document.querySelectorAll('input[name="ai_model_face"], input[name="ai_bg_preset"], input[name="ai_shot_type"], input[name="ai_hair_style"]').forEach(input => {
            input.addEventListener('change', updateFinalPrompt);
        });
        document.getElementById('ai_bg_custom').addEventListener('input', updateFinalPrompt);

        document.querySelectorAll('input[name="ai_bg_preset"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const customInput = document.getElementById('ai_bg_custom');
                customInput.value = e.target.value;
                updateFinalPrompt(); // update the big box too
                
                // Add a subtle highlight effect to show it updated
                customInput.style.transition = 'all 0.3s ease';
                customInput.style.boxShadow = '0 0 0 2px rgba(244,114,182,0.4)';
                setTimeout(() => { customInput.style.boxShadow = ''; }, 600);
            });
        });

        async function aiGenerateAdvancedImage() {
            const btn = document.getElementById('aiImageBtn');
            const faceInput = document.querySelector('input[name="ai_model_face"]:checked').value;
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
                            <div style="display:flex; flex-direction:column; gap:0.5rem; background:#0a0a0a; border:1px solid #1a1a1a; padding:0.5rem; border-radius:8px;">
                                <img src="data:image/jpeg;base64,${b64}" style="width:100%; aspect-ratio:2/3; object-fit:cover; border-radius:4px;">
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
                    node.style.background = 'rgba(234, 179, 8, 0.14)';
                    node.style.borderColor = '#facc15';
                    if (titleEl) titleEl.style.color = '#fef08a';
                    if (iconEl) iconEl.style.color = '#facc15';
                } else {
                    node.classList.remove('wp-cat-selected');
                    node.style.background = 'rgba(24, 24, 27, 0.6)';
                    node.style.borderColor = 'rgba(39, 39, 42, 0.8)';
                    if (titleEl) titleEl.style.color = '#f4f4f5';
                    if (iconEl) iconEl.style.color = '#818cf8';
                }

                node.querySelectorAll('.wp-sub-node').forEach(subNode => {
                    const subCb = subNode.querySelector('.wp-sub-check');
                    const subIconEl = subNode.querySelector('.wp-sub-icon');
                    if (subCb && subCb.checked) {
                        subNode.classList.add('wp-sub-selected');
                        subNode.style.color = '#fef08a';
                        subNode.style.fontWeight = '700';
                        subNode.style.borderColor = '#facc15';
                        subNode.style.background = 'rgba(234, 179, 8, 0.22)';
                        if (subIconEl) subIconEl.style.color = '#facc15';
                    } else {
                        subNode.classList.remove('wp-sub-selected');
                        subNode.style.color = '#d4d4d8';
                        subNode.style.fontWeight = '500';
                        subNode.style.borderColor = 'transparent';
                        subNode.style.background = 'transparent';
                        if (subIconEl) subIconEl.style.color = '#818cf8';
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
                    counter.style.background = 'rgba(234, 179, 8, 0.2)';
                    counter.style.color = '#fde047';
                    counter.style.borderColor = '#facc15';
                    counter.style.boxShadow = '0 0 10px rgba(250, 204, 21, 0.3)';
                } else {
                    counter.style.background = 'rgba(129, 140, 248, 0.15)';
                    counter.style.color = '#a5b4fc';
                    counter.style.borderColor = 'rgba(129, 140, 248, 0.3)';
                    counter.style.boxShadow = 'none';
                }
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
        });

        function syncSingleProduct(id, type) {
            const btn = document.getElementById('btnSyncSingle');
            const icon = document.getElementById('syncSingleIcon');
            if (btn) btn.disabled = true;
            if (icon) icon.className = 'fas fa-spinner fa-spin';

            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type);

            fetch('index.php?controller=sync&action=syncSingle', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'fas fa-paper-plane';
                if (res.success) {
                    alert("✓ " + res.message);
                } else {
                    alert("❌ " + res.message);
                }
            })
            .catch(err => {
                if (btn) btn.disabled = false;
                if (icon) icon.className = 'fas fa-paper-plane';
                alert("Sync request failed: " + err);
            });
        }
    </script>
</body>
</html>
