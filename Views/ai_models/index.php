<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>AI Models - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
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
                            Upload up to 5 model faces to use as consistency references in the AI Image Studio. 
                            For best results, upload high-resolution, front-facing portraits with a 1:1 aspect ratio.
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
                        <?php for($i = 1; $i <= 5; $i++): 
                            $modelPath = "assets/models/model_$i.png";
                            $fullPath = __DIR__ . '/../../' . $modelPath;
                            $exists = file_exists($fullPath);
                            $timestamp = $exists ? filemtime($fullPath) : 0;
                        ?>
                            <div class="model-card">
                                <div class="model-img-wrapper">
                                    <?php if ($exists): ?>
                                        <img src="<?php echo $modelPath; ?>?v=<?php echo $timestamp; ?>" alt="Model <?php echo $i; ?>" class="model-img">
                                    <?php else: ?>
                                        <div class="model-empty">
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
                                    
                                    <form action="index.php?controller=aimodels&action=upload" method="POST" enctype="multipart/form-data" class="mt-2">
                                        <input type="hidden" name="model_id" value="<?php echo $i; ?>">
                                        <input type="file" name="model_image" id="file_<?php echo $i; ?>" accept="image/png, image/jpeg, image/webp" class="hidden" onchange="this.form.submit()">
                                        <label for="file_<?php echo $i; ?>" class="upload-btn">
                                            <i class="fas fa-upload mr-1"></i> <?php echo $exists ? 'Replace Image' : 'Upload Image'; ?>
                                        </label>
                                    </form>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        // Auto-submit forms when file is selected is handled via onchange="this.form.submit()"
    </script>
</body>
</html>
