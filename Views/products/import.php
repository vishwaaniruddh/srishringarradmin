<!DOCTYPE html>
<html lang="en">
<head>
    <title>Import Products - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .progress-bar { transition: width 0.3s ease; }
        .log-container { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Import Products via CSV';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <!-- Step 1: Upload -->
                            <div id="upload_section">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-800">1. Upload CSV File</h3>
                                    <a href="index.php?controller=product&action=export" class="text-sm text-primary hover:underline">
                                        <i class="fas fa-download mr-1"></i> Download Template (Export current)
                                    </a>
                                </div>
                                
                                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center hover:border-primary transition-all group relative">
                                    <input type="file" id="csv_file" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <i class="fas fa-file-csv text-5xl text-gray-300 group-hover:text-primary transition-all mb-4"></i>
                                    <p class="text-gray-500 mb-2">Click to browse or drag and drop your CSV file here</p>
                                    <p class="text-xs text-gray-400">Required columns: sku, name, description, type, category_id, subcat_id, s_price, rental_price, deposit, images</p>
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button id="start_import" disabled class="px-8 py-3 bg-primary text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        Start Import
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Progress -->
                            <div id="progress_section" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-6">2. Import Progress</h3>
                                
                                <div class="grid grid-cols-4 gap-4 mb-8">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total</div>
                                        <div id="count_total" class="text-2xl font-bold text-gray-800">0</div>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-xl border border-green-100 text-center">
                                        <div class="text-xs font-bold text-green-500 uppercase tracking-wider mb-1">Success</div>
                                        <div id="count_success" class="text-2xl font-bold text-green-600">0</div>
                                    </div>
                                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-center">
                                        <div class="text-xs font-bold text-yellow-500 uppercase tracking-wider mb-1">Skipped</div>
                                        <div id="count_skipped" class="text-2xl font-bold text-yellow-600">0</div>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-xl border border-red-100 text-center">
                                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Errors</div>
                                        <div id="count_error" class="text-2xl font-bold text-red-600">0</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span id="progress_text" class="text-sm font-medium text-gray-600">Processing...</span>
                                        <span id="progress_percent" class="text-sm font-bold text-primary">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div id="progress_bar" class="progress-bar bg-primary h-full w-0"></div>
                                    </div>
                                </div>

                                <div class="log-container bg-gray-900 rounded-xl p-4 font-mono text-xs text-gray-300 space-y-1 mb-6" id="import_log">
                                    <div>[System] Ready to start...</div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <button id="download_report" disabled class="px-6 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-black transition-all disabled:opacity-50">
                                        <i class="fas fa-file-excel mr-2"></i> Download Import Report
                                    </button>
                                    <a href="index.php?controller=product&action=index" id="finish_btn" class="hidden px-8 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 transition-all">
                                        Finish
                                    </a>
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
        const fileInput = document.getElementById('csv_file');
        const startBtn = document.getElementById('start_import');
        const uploadSection = document.getElementById('upload_section');
        const progressSection = document.getElementById('progress_section');
        const log = document.getElementById('import_log');
        const downloadReportBtn = document.getElementById('download_report');
        const finishBtn = document.getElementById('finish_btn');

        let csvData = [];
        let importResults = [];

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const text = e.target.result;
                    parseCSV(text);
                    startBtn.disabled = false;
                };
                reader.readAsText(file);
            }
        });

        function parseCSV(text) {
            const lines = text.split(/\r?\n/);
            const headers = lines[0].toLowerCase().split(',').map(h => h.trim());
            
            csvData = [];
            for (let i = 1; i < lines.length; i++) {
                if (!lines[i].trim()) continue;
                
                // Simple CSV parser that handles quotes
                const row = parseCSVRow(lines[i]);
                const obj = {};
                headers.forEach((header, index) => {
                    obj[header] = row[index] ? row[index].trim() : '';
                });
                csvData.push(obj);
            }
            document.getElementById('count_total').textContent = csvData.length;
        }

        function parseCSVRow(text) {
            const result = [];
            let current = '';
            let inQuotes = false;
            for (let i = 0; i < text.length; i++) {
                const char = text[i];
                if (char === '"') {
                    inQuotes = !inQuotes;
                } else if (char === ',' && !inQuotes) {
                    result.push(current);
                    current = '';
                } else {
                    current += char;
                }
            }
            result.push(current);
            return result;
        }

        startBtn.addEventListener('click', async function() {
            uploadSection.classList.add('hidden');
            progressSection.classList.remove('hidden');
            
            let success = 0, skipped = 0, error = 0;
            importResults = [['SKU', 'Name', 'Status', 'Message']];

            for (let i = 0; i < csvData.length; i++) {
                const row = csvData[i];
                const formData = new FormData();
                for (let key in row) formData.append(key, row[key]);

                try {
                    const response = await fetch('index.php?controller=product&action=processImportRow', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.status === 'success') success++;
                    else if (result.status === 'skipped') skipped++;
                    else error++;

                    updateProgress(i + 1, csvData.length, success, skipped, error);
                    addLog(`[${row.sku || 'Row ' + (i+1)}] ${result.message}`, result.status);
                    importResults.push([row.sku, row.name, result.status, result.message]);

                } catch (e) {
                    error++;
                    updateProgress(i + 1, csvData.length, success, skipped, error);
                    addLog(`[${row.sku || 'Row ' + (i+1)}] Connection Error: ${e.message}`, 'error');
                    importResults.push([row.sku, row.name, 'error', e.message]);
                }
            }

            document.getElementById('progress_text').textContent = 'Import Complete!';
            downloadReportBtn.disabled = false;
            finishBtn.classList.remove('hidden');
        });

        function updateProgress(current, total, success, skipped, error) {
            const percent = Math.round((current / total) * 100);
            document.getElementById('progress_bar').style.width = percent + '%';
            document.getElementById('progress_percent').textContent = percent + '%';
            document.getElementById('count_success').textContent = success;
            document.getElementById('count_skipped').textContent = skipped;
            document.getElementById('count_error').textContent = error;
            document.getElementById('progress_text').textContent = `Processing row ${current} of ${total}...`;
        }

        function addLog(message, status) {
            const div = document.createElement('div');
            let color = 'text-gray-300';
            if (status === 'success') color = 'text-green-400';
            else if (status === 'skipped') color = 'text-yellow-400';
            else if (status === 'error') color = 'text-red-400';
            
            div.className = color;
            div.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            log.appendChild(div);
            log.scrollTop = log.scrollHeight;
        }

        downloadReportBtn.addEventListener('click', function() {
            const csvContent = importResults.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `import_report_${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>
</html>
