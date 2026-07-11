<!DOCTYPE html>
<html lang="en">
<head>
    <title>Description Corrector - Srishringarr</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="bg-zinc-950 font-sans text-zinc-300 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <?php 
            $pageTitle = 'Description Corrector';
            include __DIR__ . '/../partials/topbar.php'; 
            ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-6xl mx-auto">
                    <!-- Header Action Card -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-4 border-b border-zinc-900">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Format Description Tool</h1>
                            <p class="text-xs text-zinc-550 mt-1">Detects and corrects leading bullets (•) and double question marks (??) to clean numbered lists.</p>
                        </div>
                        <?php if (!empty($products)): ?>
                            <button onclick="bulkCorrectAll()" id="bulkBtn" style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #ffffff !important;" class="px-5 py-2.5 rounded-lg text-xs font-semibold hover:opacity-90 transition-all flex items-center shadow-md">
                                <i class="fas fa-magic mr-1.5"></i> Bulk Correct All (<span id="total-count"><?php echo count($products); ?></span>)
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="bg-zinc-950 border border-zinc-900 rounded-xl p-12 text-center">
                            <div class="w-12 h-12 bg-zinc-900 border border-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-400">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2 class="text-lg font-bold text-white">All Clear!</h2>
                            <p class="text-xs text-zinc-500 mt-1">No products were found with poorly formatted descriptions (bullets or double question marks).</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($products as $p): ?>
                                <div id="row-<?php echo $p['type']; ?>-<?php echo $p['id']; ?>" class="bg-zinc-950 border border-zinc-900 rounded-xl p-5 sm:p-6 transition-all duration-300 hover:border-zinc-800">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 pb-3 border-b border-zinc-900/60">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-zinc-400 font-mono"><?php echo htmlspecialchars($p['code']); ?></span>
                                                <span class="text-zinc-700">•</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-semibold bg-zinc-900 text-zinc-500 border border-zinc-800 capitalize"><?php echo $p['type']; ?></span>
                                            </div>
                                            <h3 class="text-sm font-semibold text-white mt-1"><?php echo htmlspecialchars($p['name']); ?></h3>
                                        </div>
                                        <div class="flex space-x-2 shrink-0">
                                            <button onclick="saveCorrection(this, <?php echo $p['id']; ?>, '<?php echo $p['type']; ?>')" style="background-color: #16a34a !important; color: #ffffff !important; border: 1px solid #16a34a !important;" class="px-4 py-1.5 rounded-lg text-xs font-semibold hover:opacity-90 transition-all flex items-center">
                                                <i class="fas fa-check mr-1"></i> Apply
                                            </button>
                                            <button onclick="skipRow('<?php echo $p['type']; ?>', <?php echo $p['id']; ?>)" class="px-4 py-1.5 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-lg text-xs font-medium text-zinc-400 hover:text-zinc-200 transition-all">
                                                Skip
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Original Description -->
                                        <div class="p-3.5 bg-zinc-900/20 border border-zinc-900 rounded-lg">
                                            <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-2">Original Description</span>
                                            <div class="text-xs text-zinc-400 leading-relaxed whitespace-pre-wrap font-light">
                                                <?php echo htmlspecialchars($p['description']); ?>
                                            </div>
                                        </div>

                                        <!-- Corrected Description (Editable) -->
                                        <div class="p-3.5 bg-zinc-900/40 border border-zinc-900 rounded-lg flex flex-col">
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-2">Corrected Preview (Editable)</span>
                                            <textarea id="correct-<?php echo $p['type']; ?>-<?php echo $p['id']; ?>" rows="5" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-200 focus:border-zinc-700 transition-all leading-relaxed font-light mt-auto"><?php echo htmlspecialchars($p['corrected_description']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        function skipRow(type, id) {
            const row = document.getElementById(`row-${type}-${id}`);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    row.remove();
                    updateCountHeader();
                }, 300);
            }
        }

        function updateCountHeader() {
            const remaining = document.querySelectorAll('[id^="row-"]').length;
            const countSpan = document.getElementById('total-count');
            if (countSpan) {
                countSpan.innerText = remaining;
            }
            if (remaining === 0) {
                window.location.reload();
            }
        }

        async function saveCorrection(btn, id, type) {
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>';

            const textarea = document.getElementById(`correct-${type}-${id}`);
            const correctedValue = textarea ? textarea.value.trim() : '';

            if (!correctedValue) {
                alert('Description cannot be empty');
                btn.disabled = false;
                btn.innerHTML = originalText;
                return;
            }

            try {
                const response = await fetch('index.php?controller=product&action=saveProductField', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: id,
                        type: type,
                        field: 'description',
                        value: correctedValue
                    })
                });

                const data = await response.json();
                if (data.success) {
                    skipRow(type, id);
                } else {
                    alert('Error: ' + data.error);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        async function bulkCorrectAll() {
            const bulkBtn = document.getElementById('bulkBtn');
            if (!confirm('Are you sure you want to bulk apply all corrected descriptions?')) {
                return;
            }

            bulkBtn.disabled = true;
            bulkBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Correcting...';

            const rows = document.querySelectorAll('[id^="row-"]');
            for (let row of rows) {
                const idParts = row.id.split('-');
                const type = idParts[1];
                const id = parseInt(idParts[2]);

                const textarea = document.getElementById(`correct-${type}-${id}`);
                const correctedValue = textarea ? textarea.value.trim() : '';

                if (correctedValue) {
                    try {
                        const response = await fetch('index.php?controller=product&action=saveProductField', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id: id,
                                type: type,
                                field: 'description',
                                value: correctedValue
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                    } catch (err) {
                        console.error('Failed to correct product ID ' + id, err);
                    }
                }
            }

            alert('Bulk description corrections complete!');
            window.location.reload();
        }
    </script>
</body>
</html>
