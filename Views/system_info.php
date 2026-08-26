<?php
// Calculate disk free and total space
$uploadsDir = __DIR__ . '/../../yn/uploads/';
$tempDir = $uploadsDir . 'temp_imports/';

$uploadsWritable = is_dir($uploadsDir) && is_writable($uploadsDir);
$tempWritable = is_dir($tempDir) ? is_writable($tempDir) : (is_dir($uploadsDir) && is_writable($uploadsDir));

$freeSpace = @disk_free_space($uploadsDir);
$totalSpace = @disk_total_space($uploadsDir);

function formatSize($bytes) {
    if (!$bytes || $bytes < 0) return 'N/A';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Convert ini size like 128M to bytes
function iniToBytes($val) {
    $val = trim($val);
    if (empty($val)) return 0;
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024 * 1024 * 1024; break;
        case 'm': $val *= 1024 * 1024; break;
        case 'k': $val *= 1024; break;
    }
    return $val;
}

$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
$memLimit = ini_get('memory_limit');
$maxExec = ini_get('max_execution_time');
$maxInputTime = ini_get('max_input_time');
$maxFiles = ini_get('max_file_uploads');
$zipEnabled = class_exists('\ZipArchive');
$gdEnabled = extension_loaded('gd');
$mysqliEnabled = extension_loaded('mysqli');

$uploadMaxBytes = iniToBytes($uploadMax);
$postMaxBytes = iniToBytes($postMax);

// Effective maximum single upload is min(upload_max_filesize, post_max_size)
$effectiveMaxBytes = min($uploadMaxBytes, $postMaxBytes);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Server & Upload Limits Diagnostic - Srishringarr</title>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <?php include __DIR__ . '/partials/topbar.php'; ?>

            <main class="p-6 max-w-7xl mx-auto w-full space-y-6">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-md text-xs font-bold uppercase tracking-wider border border-indigo-100">Diagnostics</span>
                            <h1 class="text-2xl font-bold text-gray-900">Server & Upload Limits</h1>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Live configuration analysis for ZIP packages and large media uploads.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="index.php?controller=product&action=import" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-file-import"></i> Go to Product Import
                        </a>
                    </div>
                </div>

                <!-- Primary Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Effective Max Upload Card -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">
                            <span>Max Upload Size</span>
                            <i class="fas fa-cloud-upload-alt text-indigo-500 text-base"></i>
                        </div>
                        <div class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($uploadMax); ?></div>
                        <p class="text-xs text-gray-500 mt-1 font-mono">
                            Effective Limit: <strong class="text-indigo-600"><?php echo formatSize($effectiveMaxBytes); ?></strong>
                        </p>
                    </div>

                    <!-- POST Max Size -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">
                            <span>POST Max Size</span>
                            <i class="fas fa-paper-plane text-blue-500 text-base"></i>
                        </div>
                        <div class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($postMax); ?></div>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php if ($postMaxBytes < $uploadMaxBytes): ?>
                                <span class="text-amber-600 font-semibold"><i class="fas fa-exclamation-triangle"></i> Smaller than upload_max!</span>
                            <?php else: ?>
                                <span class="text-emerald-600 font-semibold"><i class="fas fa-check"></i> Sufficient for payload</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Memory Limit -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">
                            <span>PHP Memory Limit</span>
                            <i class="fas fa-memory text-emerald-500 text-base"></i>
                        </div>
                        <div class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($memLimit); ?></div>
                        <p class="text-xs text-gray-500 mt-1">Used by ZipArchive & image parsing</p>
                    </div>

                    <!-- Max Execution Time -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between text-gray-500 text-xs font-semibold uppercase tracking-wider mb-2">
                            <span>Max Execution Time</span>
                            <i class="fas fa-stopwatch text-amber-500 text-base"></i>
                        </div>
                        <div class="text-3xl font-extrabold text-gray-900"><?php echo htmlspecialchars($maxExec); ?>s</div>
                        <p class="text-xs text-gray-500 mt-1">Time allowed for ZIP extraction</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Detailed System Config Table -->
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-sliders-h text-indigo-600"></i> PHP Directives & Extensions
                        </h2>
                        
                        <div class="divide-y divide-gray-100 text-xs">
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">PHP Version</span>
                                <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded font-mono"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">ZipArchive Extension</span>
                                <?php if ($zipEnabled): ?>
                                    <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-bold"><i class="fas fa-check-circle"></i> Enabled</span>
                                <?php else: ?>
                                    <span class="text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded font-bold"><i class="fas fa-times-circle"></i> Missing (Required for ZIPs)</span>
                                <?php endif; ?>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">GD Graphics Extension</span>
                                <?php if ($gdEnabled): ?>
                                    <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-bold"><i class="fas fa-check-circle"></i> Enabled</span>
                                <?php else: ?>
                                    <span class="text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded font-bold"><i class="fas fa-exclamation-triangle"></i> Disabled</span>
                                <?php endif; ?>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">max_file_uploads</span>
                                <span class="font-bold text-gray-900 font-mono"><?php echo htmlspecialchars($maxFiles); ?> files / request</span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">max_input_time</span>
                                <span class="font-bold text-gray-900 font-mono"><?php echo htmlspecialchars($maxInputTime); ?>s</span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="font-medium text-gray-600">Server Software</span>
                                <span class="text-gray-700 font-mono truncate max-w-[260px]" title="<?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?>"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?></span>
                            </div>
                        </div>

                        <!-- Directory Permissions -->
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider pt-2 border-t border-gray-100 flex items-center gap-1.5">
                            <i class="fas fa-folder-open text-amber-500"></i> Storage Directories & Permissions
                        </h3>
                        <div class="space-y-2 text-xs">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/80 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-800">yn/uploads/</div>
                                    <div class="text-[11px] text-gray-500 font-mono truncate max-w-[280px]"><?php echo htmlspecialchars(realpath($uploadsDir) ?: $uploadsDir); ?></div>
                                </div>
                                <div>
                                    <?php if ($uploadsWritable): ?>
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]"><i class="fas fa-check"></i> Writable</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-rose-100 text-rose-800 rounded font-bold text-[10px]"><i class="fas fa-times"></i> Not Writable</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($freeSpace): ?>
                                <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 flex items-center justify-between text-xs">
                                    <span class="text-gray-600">Available Server Disk Space:</span>
                                    <span class="font-bold text-indigo-700"><?php echo formatSize($freeSpace); ?> free</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Live File Upload Speed & Limit Tester -->
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-vial text-purple-600"></i> Live Upload Limit Tester
                                </h2>
                                <span class="text-[10px] bg-purple-50 text-purple-700 px-2 py-0.5 rounded font-bold uppercase tracking-wider border border-purple-200">Interactive</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">
                                Select any test ZIP or file to test if your server successfully receives, inspects, and measures transfer performance without saving it permanently.
                            </p>

                            <!-- Drop Zone -->
                            <div id="test_drop_zone" class="border-2 border-dashed border-gray-300 hover:border-indigo-500 rounded-2xl p-6 text-center cursor-pointer transition-all bg-gray-50/60 hover:bg-indigo-50/20">
                                <input type="file" id="test_file_input" class="hidden">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fas fa-file-archive"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-800">Click to choose a file or drag & drop here</p>
                                <p class="text-[11px] text-gray-500 mt-1">Test with a 10MB, 50MB, 100MB, or 200MB+ ZIP</p>
                            </div>

                            <!-- Progress Bar -->
                            <div id="upload_progress_container" class="mt-4 hidden space-y-1.5">
                                <div class="flex justify-between text-xs">
                                    <span id="upload_progress_status" class="font-medium text-gray-700">Uploading to server...</span>
                                    <span id="upload_progress_pct" class="font-bold text-indigo-600">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div id="upload_progress_bar" class="bg-indigo-600 h-full w-0 transition-all duration-150"></div>
                                </div>
                            </div>

                            <!-- Result Box -->
                            <div id="test_result_box" class="mt-4 hidden p-4 rounded-xl text-xs space-y-2"></div>
                        </div>

                        <!-- Recommendations Card -->
                        <div class="mt-4 p-4 bg-amber-50/70 border border-amber-200/80 rounded-xl text-xs space-y-1 text-amber-900">
                            <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                <i class="fas fa-lightbulb text-amber-600"></i> Recommended Server Settings for 500+ Product ZIPs:
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-amber-800/90 pl-1">
                                <li><code>upload_max_filesize = 512M</code></li>
                                <li><code>post_max_size = 512M</code></li>
                                <li><code>memory_limit = 512M</code></li>
                                <li><code>max_execution_time = 300</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/partials/scripts.php'; ?>

    <script>
        const dropZone = document.getElementById('test_drop_zone');
        const fileInput = document.getElementById('test_file_input');
        const progressContainer = document.getElementById('upload_progress_container');
        const progressBar = document.getElementById('upload_progress_bar');
        const progressPct = document.getElementById('upload_progress_pct');
        const progressStatus = document.getElementById('upload_progress_status');
        const resultBox = document.getElementById('test_result_box');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50/30');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50/30');
            if (e.dataTransfer.files.length) {
                runUploadTest(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                runUploadTest(fileInput.files[0]);
            }
        });

        function runUploadTest(file) {
            progressContainer.classList.remove('hidden');
            resultBox.classList.add('hidden');
            resultBox.className = 'mt-4 p-4 rounded-xl text-xs space-y-2';

            const startTime = Date.now();
            const formData = new FormData();
            formData.append('test_file', file);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php?controller=dashboard&action=testUpload', true);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = pct + '%';
                    progressPct.textContent = pct + '%';
                    const elapsedSec = ((Date.now() - startTime) / 1000).toFixed(1);
                    const mbDone = (e.loaded / (1024 * 1024)).toFixed(2);
                    const mbTotal = (e.total / (1024 * 1024)).toFixed(2);
                    progressStatus.textContent = `Uploading: ${mbDone} MB / ${mbTotal} MB (${elapsedSec}s)...`;
                }
            };

            xhr.onload = () => {
                const totalSeconds = ((Date.now() - startTime) / 1000).toFixed(2);
                const speedMbps = ((file.size / (1024 * 1024)) / totalSeconds).toFixed(2);

                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        resultBox.classList.remove('hidden');
                        resultBox.classList.add('bg-emerald-50', 'border', 'border-emerald-200', 'text-emerald-900');
                        resultBox.innerHTML = `
                            <div class="font-bold text-emerald-800 flex items-center gap-1.5 text-sm">
                                <i class="fas fa-check-circle text-emerald-600"></i> Upload Test Passed Successfully!
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-2 font-mono text-[11px]">
                                <div><b>File:</b> ${res.filename}</div>
                                <div><b>Size:</b> ${res.size_formatted}</div>
                                <div><b>Upload Speed:</b> ~${speedMbps} MB/s</div>
                                <div><b>Total Time:</b> ${totalSeconds}s</div>
                                <div><b>Server Time:</b> ${res.server_processing_seconds}s</div>
                                <div><b>PHP Memory:</b> ${res.memory_used}</div>
                                ${res.is_zip ? `<div><b>ZIP Files:</b> ${res.zip_files_count} files</div>` : ''}
                            </div>
                        `;
                    } else {
                        resultBox.classList.remove('hidden');
                        resultBox.classList.add('bg-rose-50', 'border', 'border-rose-200', 'text-rose-900');
                        resultBox.innerHTML = `
                            <div class="font-bold text-rose-800 flex items-center gap-1.5 text-sm">
                                <i class="fas fa-times-circle text-rose-600"></i> Upload Failed / Blocked by Server
                            </div>
                            <p class="mt-1">${res.message || 'Server rejected file.'}</p>
                        `;
                    }
                } catch (e) {
                    resultBox.classList.remove('hidden');
                    resultBox.classList.add('bg-rose-50', 'border', 'border-rose-200', 'text-rose-900');
                    resultBox.innerHTML = `
                        <div class="font-bold text-rose-800 flex items-center gap-1.5 text-sm">
                            <i class="fas fa-times-circle text-rose-600"></i> Server Returned HTTP ${xhr.status}
                        </div>
                        <p class="mt-1">The server or Cloudflare likely dropped the connection because the file exceeded <code>post_max_size</code> or <code>upload_max_filesize</code>.</p>
                    `;
                }
            };

            xhr.onerror = () => {
                resultBox.classList.remove('hidden');
                resultBox.classList.add('bg-rose-50', 'border', 'border-rose-200', 'text-rose-900');
                resultBox.innerHTML = `
                    <div class="font-bold text-rose-800 flex items-center gap-1.5 text-sm">
                        <i class="fas fa-exclamation-triangle text-rose-600"></i> Network / Connection Error
                    </div>
                    <p class="mt-1">The request was terminated before reaching the server script. Check server max upload configuration.</p>
                `;
            };

            xhr.send(formData);
        }
    </script>
</body>
</html>
