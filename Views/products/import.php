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
            $pageTitle = 'Import Products via Excel';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="w-full mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <!-- Step 1: Upload -->
                            <div id="upload_section">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-800">1. Upload Excel File</h3>
                                    <a href="index.php?controller=product&action=downloadTemplate" class="text-sm text-primary hover:underline font-semibold">
                                        <i class="fas fa-file-excel mr-1"></i> Download Excel Template
                                    </a>
                                </div>
                                
                                <div class="border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center hover:border-primary transition-all group relative bg-gray-50/30">
                                    <input type="file" id="csv_file" accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <i class="fas fa-file-excel text-5xl text-gray-300 group-hover:text-primary transition-all mb-4"></i>
                                    <p class="text-gray-500 mb-2 font-medium">Click to browse or drag and drop your Excel file here</p>
                                    <p class="text-[10px] text-gray-400">Professional Excel (.xlsx) recommended. Multiline descriptions and special characters are handled automatically.</p>
                                </div>

                                <div class="mt-8 flex justify-between items-center">
                                    <button type="button" onclick="toggleReference()" class="text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-primary transition-all">
                                        <i class="fas fa-info-circle mr-1"></i> View Category IDs Reference
                                    </button>
                                    <button id="start_import" disabled class="px-10 py-3.5 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-rocket mr-2"></i> Start Professional Import
                                    </button>
                                </div>

                                <div id="reference_section" class="hidden mt-6 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Category & Subcategory ID Reference</h4>
                                        <span class="text-[10px] text-gray-400">Use these IDs in your CSV template</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <!-- Jewellery Section -->
                                        <div>
                                            <h5 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                <i class="fas fa-gem mr-2 text-primary"></i> Jewellery
                                            </h5>
                                            <div class="space-y-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                                                <?php foreach ($categories['Jewellery']['children'] as $val => $data): ?>
                                                    <?php 
                                                    list($type, $id) = explode(':', $val);
                                                    $isParent = ($type === 'jewel_parent');
                                                    ?>
                                                    <div class="flex items-center justify-between py-1 <?php echo $isParent ? 'border-b border-gray-200 mt-2 pb-1' : 'pl-6 border-l border-gray-200 ml-2'; ?>">
                                                        <span class="text-xs <?php echo $isParent ? 'font-bold text-gray-700' : 'text-gray-500'; ?>">
                                                            <?php echo htmlspecialchars($data['name']); ?>
                                                        </span>
                                                        <div class="flex space-x-2">
                                                            <span class="text-[10px] bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-400 font-mono">
                                                                <?php echo $isParent ? 'cat_id:' : 'sub_id:'; ?> <b class="text-primary"><?php echo $id; ?></b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <!-- Apparel Section -->
                                        <div>
                                            <h5 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                                <i class="fas fa-tshirt mr-2 text-primary"></i> Apparel
                                            </h5>
                                            <div class="space-y-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                                                <?php foreach ($categories['Apparel']['children'] as $val => $data): ?>
                                                    <?php 
                                                    list($type, $id) = explode(':', $val);
                                                    ?>
                                                    <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                                        <span class="text-xs font-bold text-gray-700">
                                                            <?php echo htmlspecialchars($data['name']); ?>
                                                        </span>
                                                        <span class="text-[10px] bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-400 font-mono">
                                                            cat_id: <b class="text-primary"><?php echo $id; ?></b>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                        <p class="text-[10px] text-blue-700 leading-relaxed">
                                            <strong><i class="fas fa-lightbulb mr-1"></i> Pro Tip:</strong> 
                                            For <b>Jewellery</b>, always provide the <code>category_id</code>. If you are adding to a specific subcategory, also provide the <code>subcat_id</code>. For <b>Apparel</b>, you only need to provide the <code>category_id</code>.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Progress & Table -->
                            <div id="progress_section" class="hidden">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-800">2. Professional Review & Sync</h3>
                                    <div class="flex items-center space-x-2">
                                        <span id="overall_status_badge" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-wider border border-blue-100">Initializing...</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-4 gap-4 mb-8">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total</div>
                                        <div id="count_total" class="text-2xl font-bold text-gray-800">0</div>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-xl border border-green-100 text-center">
                                        <div class="text-xs font-bold text-green-500 uppercase tracking-wider mb-1">Success</div>
                                        <div id="count_success" class="text-2xl font-bold text-green-600">0</div>
                                    </div>
                                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center">
                                        <div class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Updated</div>
                                        <div id="count_updated" class="text-2xl font-bold text-blue-600">0</div>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-xl border border-red-100 text-center">
                                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Errors</div>
                                        <div id="count_error" class="text-2xl font-bold text-red-600">0</div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span id="progress_text" class="text-sm font-medium text-gray-600 font-mono">Ready to sync...</span>
                                        <span id="progress_percent" class="text-sm font-bold text-primary">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                                        <div id="progress_bar" class="progress-bar bg-primary h-full w-0 transition-all duration-300"></div>
                                    </div>
                                </div>

                                <!-- Professional Data Table -->
                                <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm bg-white mb-8">
                                    <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="sticky top-0 z-20 bg-gray-50 border-b border-gray-100">
                                                <tr>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">SKU</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Product Name</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type</th>
                                                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="import_table_body" class="divide-y divide-gray-50">
                                                <!-- Rows will be injected here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">
                                    <button id="download_report" disabled class="px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all shadow-sm">
                                        <i class="fas fa-file-excel mr-2 text-green-500"></i> Download Sync Report
                                    </button>
                                    <a href="index.php?controller=product&action=index" id="finish_btn" class="hidden px-8 py-3 bg-green-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-200 hover:opacity-90 transition-all">
                                        <i class="fas fa-check-circle mr-2"></i> Sync Completed
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        const fileInput = document.getElementById('csv_file');
        const startBtn = document.getElementById('start_import');
        const uploadSection = document.getElementById('upload_section');
        const progressSection = document.getElementById('progress_section');
        const tableBody = document.getElementById('import_table_body');
        const downloadReportBtn = document.getElementById('download_report');
        const finishBtn = document.getElementById('finish_btn');
        const overallBadge = document.getElementById('overall_status_badge');

        function toggleReference() {
            const ref = document.getElementById('reference_section');
            ref.classList.toggle('hidden');
        }

        let csvData = [];
        let importResults = [];

        const dropZone = fileInput.parentElement;

        // Drag and drop handlers
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileInput.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            fileInput.addEventListener(eventName, () => {
                dropZone.classList.add('border-primary', 'bg-primary/5');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileInput.addEventListener(eventName, () => {
                dropZone.classList.remove('border-primary', 'bg-primary/5');
            }, false);
        });

        fileInput.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            if (file) handleFile(file);
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) handleFile(file);
        });

        function handleFile(file) {
            console.log("Starting to handle file:", file.name);
            if (typeof XLSX === 'undefined') {
                alert("Excel library (SheetJS) not loaded. Please refresh the page.");
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    
                    let allRows = [];
                    let foundHeaders = null;
                    
                    for (const sheetName of workbook.SheetNames) {
                        const worksheet = workbook.Sheets[sheetName];
                        const json = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: "" });
                        if (json.length > 1) {
                            foundHeaders = json[0].map(h => String(h).toLowerCase().trim());
                            allRows = json.slice(1);
                            break;
                        }
                    }
                    
                    if (allRows.length === 0) {
                        alert("No data rows found in this Excel file.");
                        return;
                    }

                    csvData = [];
                    tableBody.innerHTML = ''; 

                    allRows.forEach((row, i) => {
                        if (!row || row.length === 0) return;
                        
                        const obj = {};
                        foundHeaders.forEach((header, index) => {
                            obj[header] = row[index] !== undefined ? String(row[index]).trim() : '';
                        });
                        
                        const skuValue = obj.sku || obj.sku_code || obj['product code'] || obj['code'];
                        if (skuValue || obj.name) {
                            if (skuValue && !obj.sku) obj.sku = skuValue;
                            csvData.push(obj);

                            const tr = document.createElement('tr');
                            tr.id = `row-${csvData.length - 1}`;
                            tr.className = "hover:bg-gray-50/50 transition-colors";
                            tr.innerHTML = `
                                <td class="px-6 py-4 text-xs font-mono font-bold text-gray-700">${obj.sku || 'N/A'}</td>
                                <td class="px-6 py-4 text-xs font-medium text-gray-600">${obj.name || 'Unnamed Product'}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-[9px] font-bold uppercase">${obj.type || 'jewellery'}</span>
                                </td>
                                <td class="px-6 py-4 text-right status-cell">
                                    ${getStatusBadge('pending')}
                                </td>
                            `;
                            tableBody.appendChild(tr);
                        }
                    });
                    
                    if (csvData.length > 0) {
                        document.getElementById('count_total').textContent = csvData.length;
                        startBtn.disabled = false;
                        overallBadge.textContent = "Ready to start";
                    }
                } catch (err) {
                    console.error(err);
                    alert("Error reading Excel file: " + err.message);
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function getStatusBadge(status, message = '') {
            const styles = {
                pending: 'bg-gray-100 text-gray-400 border-gray-200',
                syncing: 'bg-blue-50 text-blue-500 border-blue-100 animate-pulse',
                success: 'bg-green-50 text-green-600 border-green-100',
                updated: 'bg-indigo-50 text-indigo-600 border-indigo-100',
                error: 'bg-red-50 text-red-600 border-red-100'
            };
            const labels = {
                pending: 'Pending',
                syncing: 'Syncing',
                success: 'Success',
                updated: 'Updated',
                error: 'Error'
            };
            const icon = {
                pending: 'fa-clock',
                syncing: 'fa-spinner fa-spin',
                success: 'fa-check-circle',
                updated: 'fa-sync-alt',
                error: 'fa-exclamation-circle'
            };
            
            return `
                <div class="inline-flex flex-col items-end">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${styles[status]}">
                        <i class="fas ${icon[status]} mr-1"></i> ${labels[status]}
                    </span>
                    ${message ? `<div class="text-[9px] text-red-400 mt-1 max-w-[200px] truncate" title="${message}">${message}</div>` : ''}
                </div>
            `;
        }

        startBtn.addEventListener('click', async function() {
            uploadSection.classList.add('hidden');
            progressSection.classList.remove('hidden');
            overallBadge.className = "px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[10px] font-bold uppercase tracking-wider border border-yellow-100";
            overallBadge.textContent = "Syncing in progress...";
            
            let success = 0, updated = 0, error = 0;
            importResults = [['SKU', 'Name', 'Status', 'Message']];

            for (let i = 0; i < csvData.length; i++) {
                const row = csvData[i];
                const tr = document.getElementById(`row-${i}`);
                const statusCell = tr.querySelector('.status-cell');
                
                statusCell.innerHTML = getStatusBadge('syncing');
                tr.scrollIntoView({ behavior: 'smooth', block: 'center' });

                try {
                    const response = await fetch('index.php?controller=product&action=processImportRow', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(row)
                    });
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        success++;
                        statusCell.innerHTML = getStatusBadge('success');
                        document.getElementById('count_success').textContent = success;
                    } else if (result.status === 'updated') {
                        updated++;
                        statusCell.innerHTML = getStatusBadge('updated');
                        document.getElementById('count_updated').textContent = updated;
                    } else {
                        error++;
                        statusCell.innerHTML = getStatusBadge('error', result.message);
                        document.getElementById('count_error').textContent = error;
                    }
                    importResults.push([row.sku, row.name, result.status, result.message]);
                } catch (err) {
                    error++;
                    statusCell.innerHTML = getStatusBadge('error', err.message);
                    document.getElementById('count_error').textContent = error;
                    importResults.push([row.sku, row.name, 'error', err.message]);
                }

                const progress = Math.round(((i + 1) / csvData.length) * 100);
                document.getElementById('progress_bar').style.width = progress + '%';
                document.getElementById('progress_percent').textContent = progress + '%';
                document.getElementById('progress_text').textContent = `Processing item ${i + 1} of ${csvData.length}...`;
            }

            overallBadge.className = "px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-bold uppercase tracking-wider border border-green-100";
            overallBadge.textContent = "Sync Completed!";
            document.getElementById('progress_text').textContent = 'All items processed successfully.';
            
            downloadReportBtn.disabled = false;
            finishBtn.classList.remove('hidden');
        });

        downloadReportBtn.addEventListener('click', function() {
            const csvContent = importResults.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `sync_report_${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>
</html>
