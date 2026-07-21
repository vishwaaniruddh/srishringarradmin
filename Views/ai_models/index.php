<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>AI Models - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        .model-card {
            background: #0a0a0a;
            border: 1px solid #1f1f1f;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }
        .model-card:hover {
            border-color: #333;
        }
        .model-img-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            background: #111;
            border-bottom: 1px solid #1f1f1f;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .model-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .model-empty {
            color: #444;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        .model-empty i {
            font-size: 2rem;
        }
        .model-info {
            padding: 1rem;
        }
        .model-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #ddd;
            margin-bottom: 0.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .model-status {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }
        .status-active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-empty { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .upload-btn {
            display: block;
            width: 100%;
            padding: 0.5rem;
            text-align: center;
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 6px;
            color: #aaa;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 0.75rem;
        }
        .upload-btn:hover {
            background: #222;
            color: #fff;
            border-color: #444;
        }
    </style>
</head>
<body class="bg-black font-sans text-gray-300">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <?php 
            $pageTitle = 'AI Reference Models';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                <div class="max-w-6xl mx-auto">
                    
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-white mb-2">AI Face Reference Models</h1>
                        <p class="text-sm text-zinc-400">
                            Upload up to 10 model faces to use as consistency references in the AI Image Studio. 
                            Use the <strong>Adjust Framing</strong> tool to perfectly center and position the face.
                        </p>
                    </div>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-400 text-sm flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <?php for($i = 1; $i <= 10; $i++): 
                            $modelPath = "assets/models/model_$i.png";
                            $fullPath = __DIR__ . '/../../' . $modelPath;
                            $exists = file_exists($fullPath);
                            $timestamp = $exists ? filemtime($fullPath) : 0;
                        ?>
                            <div class="model-card">
                                <div class="model-img-wrapper">
                                    <?php if ($exists): ?>
                                        <img src="<?php echo $modelPath; ?>?v=<?php echo $timestamp; ?>" id="img_preview_<?php echo $i; ?>" alt="Model <?php echo $i; ?>" class="model-img">
                                    <?php else: ?>
                                        <div class="model-empty" id="empty_preview_<?php echo $i; ?>">
                                            <i class="fas fa-user-astronaut"></i>
                                            <span class="text-xs font-semibold uppercase tracking-wider">Empty Slot</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="model-info">
                                    <div class="model-title">
                                        Model <?php echo $i; ?>
                                        <?php if ($exists): ?>
                                            <span class="model-status status-active">Active</span>
                                        <?php else: ?>
                                            <span class="model-status status-empty">Empty</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <input type="file" id="file_<?php echo $i; ?>" accept="image/png, image/jpeg, image/webp" class="hidden" onchange="handleFileSelect(this, <?php echo $i; ?>)">
                                    
                                    <label for="file_<?php echo $i; ?>" class="upload-btn">
                                        <i class="fas fa-upload mr-1"></i> <?php echo $exists ? 'Upload New Image' : 'Upload Image'; ?>
                                    </label>

                                    <?php if ($exists): ?>
                                        <button type="button" onclick="openFramingModal('<?php echo $modelPath; ?>?v=<?php echo $timestamp; ?>', <?php echo $i; ?>)" class="upload-btn" style="background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.3); color: #818cf8; margin-top:0.4rem;">
                                            <i class="fas fa-crop-alt mr-1"></i> Adjust Framing
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                </div>
            </main>
        </div>
    </div>

    <!-- Framing & Cropping Modal -->
    <div id="framingModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#0a0a0a] border border-zinc-800 rounded-2xl w-full max-w-lg p-6 flex flex-col gap-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
                <h3 class="text-md font-bold text-white flex items-center gap-2">
                    <i class="fas fa-crop-alt text-indigo-400"></i> Correct Image Position & Framing
                </h3>
                <button type="button" onclick="closeFramingModal()" class="text-zinc-500 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Cropper Image Container -->
            <div class="w-full bg-zinc-950 rounded-xl overflow-hidden flex items-center justify-center min-h-[300px] max-h-[400px] border border-zinc-800">
                <img id="cropperImage" src="" class="max-w-full max-h-[380px]">
            </div>

            <!-- Toolbar Controls -->
            <div class="flex items-center justify-center gap-2 bg-zinc-900/60 p-2 rounded-lg border border-zinc-800">
                <button type="button" onclick="cropperObj && cropperObj.zoom(0.1)" title="Zoom In" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold rounded transition">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button type="button" onclick="cropperObj && cropperObj.zoom(-0.1)" title="Zoom Out" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold rounded transition">
                    <i class="fas fa-search-minus"></i>
                </button>
                <button type="button" onclick="cropperObj && cropperObj.rotate(-90)" title="Rotate Left" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold rounded transition">
                    <i class="fas fa-undo"></i>
                </button>
                <button type="button" onclick="cropperObj && cropperObj.rotate(90)" title="Rotate Right" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold rounded transition">
                    <i class="fas fa-redo"></i>
                </button>
                <button type="button" onclick="cropperObj && cropperObj.reset()" title="Reset Position" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-semibold rounded transition">
                    Reset
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-zinc-800 pt-3">
                <button type="button" onclick="closeFramingModal()" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button type="button" id="saveFrameBtn" onclick="saveFramedImage()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-lg shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save & Apply Framing
                </button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let currentModelId = null;
        let cropperObj = null;

        function openFramingModal(imgSrc, modelId) {
            currentModelId = modelId;
            const modal = document.getElementById('framingModal');
            const cropperImg = document.getElementById('cropperImage');
            
            modal.classList.remove('hidden');
            cropperImg.src = imgSrc;

            if (cropperObj) {
                cropperObj.destroy();
                cropperObj = null;
            }

            cropperImg.onload = function() {
                cropperObj = new Cropper(cropperImg, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            };
        }

        function closeFramingModal() {
            document.getElementById('framingModal').classList.add('hidden');
            if (cropperObj) {
                cropperObj.destroy();
                cropperObj = null;
            }
        }

        function handleFileSelect(input, modelId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    openFramingModal(e.target.result, modelId);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function saveFramedImage() {
            if (!cropperObj || !currentModelId) return;

            const btn = document.getElementById('saveFrameBtn');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving Frame...';

            try {
                // Get 600x600 cropped canvas (high quality for AI reference)
                const canvas = cropperObj.getCroppedCanvas({
                    width: 600,
                    height: 600,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                const b64Image = canvas.toDataURL('image/png');

                const response = await fetch('index.php?controller=aimodels&action=upload', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        model_id: currentModelId,
                        cropped_image: b64Image
                    })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = 'index.php?controller=aimodels&success=Model framing adjusted and saved successfully';
                } else {
                    alert('Error: ' + (data.error || 'Failed to save framing'));
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred while saving.');
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }
    </script>
</body>
</html>
