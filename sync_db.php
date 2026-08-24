<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set execution time and memory limits for large data operations
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

// Connection details for local and server DB
$localConfig = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db'   => 'u464193275_srishringarr'
];

$serverConfig = [
    'host' => '193.203.184.203',
    'user' => 'u464193275_sarmicropos',
    'pass' => 'Mypos1234',
    'db'   => 'u464193275_srishringarr'
];

function getDbConnection($config) {
    try {
        $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        if ($conn->connect_error) {
            return null;
        }
        $conn->set_charset("utf8mb4");
        // Boost packet size on local
        if ($config['host'] === 'localhost') {
            try { $conn->query("SET GLOBAL max_allowed_packet = 67108864"); } catch (Exception $e) {}
        }
        return $conn;
    } catch (Exception $e) {
        return null;
    }
}

$localConn = getDbConnection($localConfig);
$serverConn = getDbConnection($serverConfig);

// Helper function to sync table data with adaptive payload-safe batching
function syncTableDataSafe($table, $localConn, $serverConn, $batchSize = 500) {
    if (!$localConn || !$serverConn) {
        return ['success' => false, 'error' => 'Database connection failed.'];
    }

    $startTime = microtime(true);

    try {
        if (!$localConn->ping()) {
            global $localConfig;
            $localConn = getDbConnection($localConfig);
        }
        if (!$serverConn->ping()) {
            global $serverConfig;
            $serverConn = getDbConnection($serverConfig);
        }

        $localConn->query("SET FOREIGN_KEY_CHECKS = 0");
        $localConn->query("TRUNCATE TABLE `$table`");

        // Fetch column definitions
        $colRes = $serverConn->query("SHOW COLUMNS FROM `$table`");
        if (!$colRes) {
            $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
            return ['success' => false, 'error' => "Failed to get server columns: " . $serverConn->error];
        }

        $columns = [];
        while ($colRow = $colRes->fetch_assoc()) {
            $columns[] = "`" . $colRow['Field'] . "`";
        }
        $colList = implode(', ', $columns);

        if (empty($columns)) {
            $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
            return ['success' => true, 'rows' => 0, 'time' => round(microtime(true) - $startTime, 2)];
        }

        // Get server row count
        $cntRes = $serverConn->query("SELECT COUNT(*) FROM `$table`");
        $totalRows = (int)($cntRes ? $cntRes->fetch_row()[0] : 0);

        if ($totalRows === 0) {
            $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
            return [
                'success'   => true,
                'rows'      => 0,
                'prodRows'  => 0,
                'localRows' => 0,
                'time'      => round(microtime(true) - $startTime, 2)
            ];
        }

        $offset = 0;
        $syncedRows = 0;

        while ($offset < $totalRows) {
            $res = $serverConn->query("SELECT * FROM `$table` LIMIT $batchSize OFFSET $offset");
            if (!$res) {
                $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
                return ['success' => false, 'error' => "Fetch error at offset $offset: " . $serverConn->error];
            }

            $valueRows = [];
            $currentPayloadBytes = 0;

            while ($row = $res->fetch_assoc()) {
                $escaped = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $escaped[] = "NULL";
                    } else {
                        $escaped[] = "'" . $localConn->real_escape_string($val) . "'";
                    }
                }
                $rowStr = "(" . implode(', ', $escaped) . ")";
                $valueRows[] = $rowStr;
                $currentPayloadBytes += strlen($rowStr);

                // Flush if batch approaches 400KB to safely avoid max_allowed_packet limits
                if ($currentPayloadBytes >= 400000) {
                    $insertSql = "INSERT INTO `$table` ($colList) VALUES " . implode(', ', $valueRows);
                    if (!$localConn->query($insertSql)) {
                        $err = $localConn->error;
                        $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
                        return ['success' => false, 'error' => "Insert error: " . $err];
                    }
                    $syncedRows += count($valueRows);
                    $valueRows = [];
                    $currentPayloadBytes = 0;
                }
            }
            $res->free();

            if (!empty($valueRows)) {
                $insertSql = "INSERT INTO `$table` ($colList) VALUES " . implode(', ', $valueRows);
                if (!$localConn->query($insertSql)) {
                    $err = $localConn->error;
                    $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
                    return ['success' => false, 'error' => "Insert error: " . $err];
                }
                $syncedRows += count($valueRows);
            }

            $offset += $batchSize;
        }

        $localConn->query("SET FOREIGN_KEY_CHECKS = 1");

        // Verify local count
        $locRes = $localConn->query("SELECT COUNT(*) FROM `$table`");
        $locCount = (int)($locRes ? $locRes->fetch_row()[0] : $syncedRows);

        return [
            'success'   => true,
            'rows'      => $syncedRows,
            'prodRows'  => $totalRows,
            'localRows' => $locCount,
            'time'      => round(microtime(true) - $startTime, 2)
        ];
    } catch (Exception $e) {
        if ($localConn) $localConn->query("SET FOREIGN_KEY_CHECKS = 1");
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Helper to sync single column
function syncColumnDefinition($table, $col, $localConn, $serverConn) {
    $res = $localConn->query("SHOW CREATE TABLE `$table`");
    if ($res && $row = $res->fetch_assoc()) {
        $createSQL = $row['Create Table'];
        $colEsc = preg_quote($col, '/');
        $pattern = "/^\s*`$colEsc`\s+(.*?),?$/m";

        if (preg_match($pattern, $createSQL, $matches)) {
            $fullDef = trim($matches[0]);
            if (substr($fullDef, -1) === ',') {
                $fullDef = substr($fullDef, 0, -1);
            }
            $alterSQL = "ALTER TABLE `$table` ADD $fullDef";
            return $serverConn->query($alterSQL) ? true : $serverConn->error;
        } else {
            return "Definition not found in local table";
        }
    }
    return "Local table not found";
}

function getTablesList($conn) {
    if (!$conn) return [];
    $tables = [];
    $res = $conn->query("SHOW TABLES");
    if ($res) {
        while ($row = $res->fetch_array()) {
            $tables[] = $row[0];
        }
    }
    return $tables;
}

function getRowCount($conn, $table) {
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) FROM `$table`");
    if ($res) {
        $row = $res->fetch_row();
        return intval($row[0]);
    }
    return 0;
}

function compareColumns($table, $localConn, $serverConn) {
    $localCols = getColumns($localConn, $table);
    $serverCols = getColumns($serverConn, $table);

    $diff = [
        'onlyLocal' => [],
        'onlyServer' => [],
        'typeMismatch' => [],
        'collationMismatch' => []
    ];

    foreach ($localCols as $col => $details) {
        if (!isset($serverCols[$col])) {
            $diff['onlyLocal'][$col] = $details;
        } else {
            if ($details['Type'] !== $serverCols[$col]['Type']) {
                $diff['typeMismatch'][$col] = [
                    'Local' => $details['Type'],
                    'Server' => $serverCols[$col]['Type']
                ];
            }
            if ($details['Collation'] !== $serverCols[$col]['Collation']) {
                $diff['collationMismatch'][$col] = [
                    'Local' => $details['Collation'],
                    'Server' => $serverCols[$col]['Collation']
                ];
            }
        }
    }

    foreach ($serverCols as $col => $details) {
        if (!isset($localCols[$col])) {
            $diff['onlyServer'][$col] = $details;
        }
    }

    return $diff;
}

function getColumns($conn, $table) {
    if (!$conn) return [];
    $columns = [];
    $result = $conn->query("SHOW FULL COLUMNS FROM `$table`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[$row['Field']] = [
                'Type' => $row['Type'],
                'Collation' => $row['Collation'] ?? 'NULL'
            ];
        }
    }
    return $columns;
}

function compareIndexes($table, $localConn, $serverConn) {
    $localIdx = getIndexes($localConn, $table);
    $serverIdx = getIndexes($serverConn, $table);

    $diff = [
        'onlyLocal' => [],
        'onlyServer' => [],
        'mismatch' => [],
    ];

    foreach ($localIdx as $name => $info) {
        if (!isset($serverIdx[$name])) {
            $diff['onlyLocal'][$name] = $info;
        } else {
            $localCols = implode(',', $info['columns']);
            $serverCols = implode(',', $serverIdx[$name]['columns']);
            if ($localCols !== $serverCols || $info['unique'] !== $serverIdx[$name]['unique']) {
                $diff['mismatch'][$name] = [
                    'local' => ($info['unique'] ? 'UNIQUE ' : '') . "($localCols)",
                    'server' => ($serverIdx[$name]['unique'] ? 'UNIQUE ' : '') . "($serverCols)",
                ];
            }
        }
    }

    foreach ($serverIdx as $name => $info) {
        if (!isset($localIdx[$name])) {
            $diff['onlyServer'][$name] = $info;
        }
    }

    return $diff;
}

function getIndexes($conn, $table) {
    if (!$conn) return [];
    $indexes = [];
    $result = $conn->query("SHOW INDEX FROM `$table`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $key = $row['Key_name'];
            if (!isset($indexes[$key])) {
                $indexes[$key] = [
                    'unique' => $row['Non_unique'] == 0,
                    'columns' => [],
                ];
            }
            $indexes[$key]['columns'][$row['Seq_in_index']] = $row['Column_name'];
        }
    }
    foreach ($indexes as &$idx) {
        ksort($idx['columns']);
        $idx['columns'] = array_values($idx['columns']);
    }
    return $indexes;
}

// =========================================================================
// AJAX API ROUTING
// =========================================================================
if (isset($_REQUEST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_REQUEST['ajax_action'];

    if ($action === 'sync_single_table') {
        $table = $_REQUEST['table'] ?? '';
        if (empty($table)) {
            echo json_encode(['success' => false, 'error' => 'Table name required']);
            exit;
        }
        $res = syncTableDataSafe($table, $localConn, $serverConn);
        $res['table'] = $table;
        echo json_encode($res);
        exit;
    }

    if ($action === 'get_table_counts') {
        $table = $_REQUEST['table'] ?? '';
        if (empty($table)) {
            echo json_encode(['success' => false, 'error' => 'Table name required']);
            exit;
        }
        $prodRows = getRowCount($serverConn, $table);
        $localRows = getRowCount($localConn, $table);
        echo json_encode([
            'success' => true,
            'table' => $table,
            'prodRows' => $prodRows,
            'localRows' => $localRows
        ]);
        exit;
    }

    if ($action === 'sync_table_schema') {
        $table = $_REQUEST['table'] ?? '';
        $res = $localConn->query("SHOW CREATE TABLE `$table`");
        if ($res && $row = $res->fetch_assoc()) {
            $createSQL = $row['Create Table'];
            if ($serverConn->query($createSQL)) {
                echo json_encode(['success' => true, 'message' => "Table `$table` created on server."]);
            } else {
                echo json_encode(['success' => false, 'error' => $serverConn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not read local table structure.']);
        }
        exit;
    }

    if ($action === 'sync_column') {
        $table = $_REQUEST['table'] ?? '';
        $col = $_REQUEST['column'] ?? '';
        $res = syncColumnDefinition($table, $col, $localConn, $serverConn);
        if ($res === true) {
            echo json_encode(['success' => true, 'message' => "Column `$col` synced to server."]);
        } else {
            echo json_encode(['success' => false, 'error' => $res]);
        }
        exit;
    }

    if ($action === 'sync_index') {
        $table = $_REQUEST['table'] ?? '';
        $idxKey = $_REQUEST['index_key'] ?? '';
        $res = $localConn->query("SHOW INDEX FROM `$table` WHERE Key_name = '" . $localConn->real_escape_string($idxKey) . "'");
        $cols = [];
        $isUnique = false;
        while ($r = $res->fetch_assoc()) {
            $cols[$r['Seq_in_index']] = '`' . $r['Column_name'] . '`';
            if ($r['Non_unique'] == 0 && $r['Key_name'] !== 'PRIMARY')
                $isUnique = true;
        }
        ksort($cols);
        $colList = implode(', ', $cols);
        if ($idxKey === 'PRIMARY') {
            $sql = "ALTER TABLE `$table` ADD PRIMARY KEY ($colList)";
        } elseif ($isUnique) {
            $sql = "ALTER TABLE `$table` ADD UNIQUE KEY `$idxKey` ($colList)";
        } else {
            $sql = "ALTER TABLE `$table` ADD INDEX `$idxKey` ($colList)";
        }
        if ($serverConn->query($sql)) {
            echo json_encode(['success' => true, 'message' => "Index `$idxKey` created on server."]);
        } else {
            echo json_encode(['success' => false, 'error' => $serverConn->error]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

// Fetch general data for page rendering
$localTables = getTablesList($localConn);
$serverTables = getTablesList($serverConn);

$matchingTables = array_values(array_intersect($localTables, $serverTables));
$uniqueToLocal = array_values(array_diff($localTables, $serverTables));
$uniqueToServer = array_values(array_diff($serverTables, $localTables));

// Gather statistics for initial table data
$tablesData = [];
$totalProdRows = 0;
$totalLocalRows = 0;
$outOfSyncCount = 0;

foreach ($matchingTables as $t) {
    $pCount = getRowCount($serverConn, $t);
    $lCount = getRowCount($localConn, $t);
    $totalProdRows += $pCount;
    $totalLocalRows += $lCount;
    $diff = ($pCount !== $lCount);
    if ($diff) $outOfSyncCount++;

    $tablesData[] = [
        'name' => $t,
        'prodRows' => $pCount,
        'localRows' => $lCount,
        'diff' => $diff
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Sync & Schema Master — Sri Shringaar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        obsidian: '#09090b',
                        gold: '#d4af37',
                        primary: '#6366f1',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(24, 24, 27, 0.6); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(63, 63, 70, 0.8); border-radius: 4px; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen antialiased">

    <!-- Top Navigation Header -->
    <header class="border-b border-zinc-800/80 bg-zinc-900/60 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 hover:scale-105 transition-transform">
                    <i class="fas fa-database text-lg"></i>
                </a>
                <div>
                    <h1 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Database Sync Master</span>
                        <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">v2.0 Live Sync</span>
                    </h1>
                    <p class="text-xs text-zinc-400">Sequential batch sync & schema validator</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-semibold px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="window.location.reload()" class="text-xs font-semibold px-3 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 transition-all flex items-center gap-1.5">
                    <i class="fas fa-rotate"></i> Refresh State
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Connection Status Banner -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <!-- Production Server -->
            <div class="p-4 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-200">Production Remote Server</span>
                            <span class="w-2 h-2 rounded-full <?= $serverConn ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' ?>"></span>
                        </div>
                        <p class="text-xs font-mono text-zinc-400 mt-0.5"><?= htmlspecialchars($serverConfig['host']) ?> &bull; <?= htmlspecialchars($serverConfig['db']) ?></p>
                    </div>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium <?= $serverConn ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400' ?>">
                    <?= $serverConn ? 'Online' : 'Disconnected' ?>
                </span>
            </div>

            <!-- Local Server -->
            <div class="p-4 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-200">Local Database (Target)</span>
                            <span class="w-2 h-2 rounded-full <?= $localConn ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' ?>"></span>
                        </div>
                        <p class="text-xs font-mono text-zinc-400 mt-0.5"><?= htmlspecialchars($localConfig['host']) ?> &bull; <?= htmlspecialchars($localConfig['db']) ?></p>
                    </div>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium <?= $localConn ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400' ?>">
                    <?= $localConn ? 'Online' : 'Disconnected' ?>
                </span>
            </div>
        </div>

        <!-- Quick Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="p-4 rounded-2xl bg-zinc-900/50 border border-zinc-800">
                <p class="text-xs font-medium text-zinc-400">Matching Tables</p>
                <p class="text-2xl font-bold text-white mt-1"><?= count($matchingTables) ?></p>
                <p class="text-[11px] text-zinc-500 mt-1">Ready for data synchronization</p>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-900/50 border border-zinc-800">
                <p class="text-xs font-medium text-zinc-400">Out-of-Sync Tables</p>
                <p class="text-2xl font-bold text-amber-400 mt-1" id="stat_out_of_sync"><?= $outOfSyncCount ?></p>
                <p class="text-[11px] text-zinc-500 mt-1">Row count discrepancy</p>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-900/50 border border-zinc-800">
                <p class="text-xs font-medium text-zinc-400">Production Total Rows</p>
                <p class="text-2xl font-bold text-indigo-400 mt-1 font-mono"><?= number_format($totalProdRows) ?></p>
                <p class="text-[11px] text-zinc-500 mt-1">Live data volume</p>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-900/50 border border-zinc-800">
                <p class="text-xs font-medium text-zinc-400">Local Total Rows</p>
                <p class="text-2xl font-bold text-emerald-400 mt-1 font-mono" id="stat_local_total_rows"><?= number_format($totalLocalRows) ?></p>
                <p class="text-[11px] text-zinc-500 mt-1">Local cached volume</p>
            </div>
        </div>

        <!-- Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-zinc-800 pb-3 mb-6 flex-wrap">
            <button onclick="switchTab('data_sync')" id="tab_btn_data_sync" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                <i class="fas fa-arrows-rotate"></i>
                <span>Data Sync (Prod &rarr; Local)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white"><?= count($matchingTables) ?></span>
            </button>
            <button onclick="switchTab('schema_diff')" id="tab_btn_schema_diff" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-table-columns"></i>
                <span>Schema Differences</span>
                <?php if (count($uniqueToLocal) + count($uniqueToServer) > 0): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/20 text-amber-300"><?= count($uniqueToLocal) + count($uniqueToServer) ?></span>
                <?php endif; ?>
            </button>
            <button onclick="switchTab('index_diff')" id="tab_btn_index_diff" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-key"></i>
                <span>Index Validation</span>
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 1: DATA SYNC SECTION -->
        <!-- ================================================================= -->
        <div id="tab_content_data_sync" class="tab-content space-y-6">

            <!-- Control Bar -->
            <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-72">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-500"></i>
                        <input type="text" id="table_search_input" oninput="filterTablesList()" placeholder="Filter tables (e.g. products, datarecords)..." class="w-full bg-zinc-950 border border-zinc-800 rounded-xl pl-9 pr-3 py-2 text-xs text-zinc-200 outline-none focus:border-indigo-500">
                    </div>
                    <select id="table_filter_select" onchange="filterTablesList()" class="bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs text-zinc-300 outline-none">
                        <option value="all">Show All Tables (<?= count($matchingTables) ?>)</option>
                        <option value="diff">Only Out-of-Sync (<?= $outOfSyncCount ?>)</option>
                        <option value="synced">In Sync Only</option>
                    </select>
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button onclick="syncSelectedTables()" id="btn_sync_selected" class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold transition-all flex items-center gap-2">
                        <i class="fas fa-check-double"></i> Sync Selected (<span id="selected_count_label">0</span>)
                    </button>
                    <button onclick="startSyncAllSequence()" id="btn_sync_all" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-bold shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2">
                        <i class="fas fa-cloud-arrow-down"></i> Sync All Tables Data to Local (One-by-One)
                    </button>
                </div>
            </div>

            <!-- Tables Grid / List -->
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead class="bg-zinc-900 border-b border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold">
                            <tr>
                                <th class="p-4 w-12 text-center">
                                    <input type="checkbox" id="select_all_checkbox" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-indigo-600 focus:ring-0 cursor-pointer">
                                </th>
                                <th class="p-4">Table Name</th>
                                <th class="p-4 text-right">Production Rows</th>
                                <th class="p-4 text-right">Local Rows</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tables_tbody" class="divide-y divide-zinc-800/60 font-mono">
                            <?php foreach ($tablesData as $idx => $t): ?>
                                <tr id="row_<?= htmlspecialchars($t['name']) ?>" class="table-row-item hover:bg-zinc-800/40 transition-colors" data-table-name="<?= htmlspecialchars(strtolower($t['name'])) ?>" data-has-diff="<?= $t['diff'] ? '1' : '0' ?>">
                                    <td class="p-4 text-center">
                                        <input type="checkbox" value="<?= htmlspecialchars($t['name']) ?>" onchange="updateSelectedCount()" class="table-cb w-4 h-4 rounded bg-zinc-950 border-zinc-700 text-indigo-600 focus:ring-0 cursor-pointer">
                                    </td>
                                    <td class="p-4 font-sans font-semibold text-zinc-100 flex items-center gap-2">
                                        <i class="fas fa-table text-zinc-500 text-xs"></i>
                                        <span><?= htmlspecialchars($t['name']) ?></span>
                                    </td>
                                    <td class="p-4 text-right font-bold text-indigo-300" id="prod_count_<?= htmlspecialchars($t['name']) ?>">
                                        <?= number_format($t['prodRows']) ?>
                                    </td>
                                    <td class="p-4 text-right font-bold <?= $t['diff'] ? 'text-amber-400' : 'text-emerald-400' ?>" id="local_count_<?= htmlspecialchars($t['name']) ?>">
                                        <?= number_format($t['localRows']) ?>
                                    </td>
                                    <td class="p-4 text-center" id="status_col_<?= htmlspecialchars($t['name']) ?>">
                                        <?php if ($t['diff']): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-sans font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <i class="fas fa-circle-exclamation text-[9px]"></i> Out of Sync
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-sans font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <i class="fas fa-circle-check text-[9px]"></i> In Sync
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button onclick="syncSingleTable('<?= htmlspecialchars($t['name']) ?>', this)" class="btn-sync-single px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-indigo-600 text-zinc-300 hover:text-white font-sans text-xs font-semibold transition-all flex items-center gap-1.5 ml-auto">
                                            <i class="fas fa-arrow-down"></i>
                                            <span>Sync Table</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 2: SCHEMA DIFFERENCES -->
        <!-- ================================================================= -->
        <div id="tab_content_schema_diff" class="tab-content hidden space-y-8">
            
            <!-- Unique to Local -->
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-zinc-100 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-emerald-400"></i> Tables Only in Local Database (Missing on Server)
                    </h3>
                    <span class="px-2.5 py-0.5 text-xs rounded-full bg-zinc-800 text-zinc-300 font-mono"><?= count($uniqueToLocal) ?></span>
                </div>
                <?php if (empty($uniqueToLocal)): ?>
                    <p class="text-xs text-zinc-500 py-4 text-center">No extra local tables. All tables exist on remote server.</p>
                <?php else: ?>
                    <div class="divide-y divide-zinc-800 font-mono text-xs">
                        <?php foreach ($uniqueToLocal as $tbl): ?>
                            <div class="py-3 flex items-center justify-between">
                                <span class="text-zinc-200"><?= htmlspecialchars($tbl) ?></span>
                                <button onclick="syncTableSchema('<?= htmlspecialchars($tbl) ?>', this)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-sans font-semibold transition-all">
                                    Create Table on Server
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Unique to Server -->
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-zinc-100 flex items-center gap-2">
                        <i class="fas fa-cloud text-purple-400"></i> Tables Only on Remote Server (Missing Locally)
                    </h3>
                    <span class="px-2.5 py-0.5 text-xs rounded-full bg-zinc-800 text-zinc-300 font-mono"><?= count($uniqueToServer) ?></span>
                </div>
                <?php if (empty($uniqueToServer)): ?>
                    <p class="text-xs text-zinc-500 py-4 text-center">No missing local tables. All remote tables exist locally.</p>
                <?php else: ?>
                    <div class="divide-y divide-zinc-800 font-mono text-xs">
                        <?php foreach ($uniqueToServer as $tbl): ?>
                            <div class="py-3 flex items-center justify-between">
                                <span class="text-zinc-200"><?= htmlspecialchars($tbl) ?></span>
                                <span class="text-zinc-500 text-xs font-sans">Exists only on production</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Column Differences in Matching Tables -->
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6">
                <h3 class="text-sm font-bold text-zinc-100 mb-4 flex items-center gap-2">
                    <i class="fas fa-columns text-amber-400"></i> Column Differences in Matching Tables
                </h3>
                <div class="divide-y divide-zinc-800">
                    <?php
                    $anyColDiff = false;
                    foreach ($matchingTables as $table):
                        $colDiff = compareColumns($table, $localConn, $serverConn);
                        if (empty($colDiff['onlyLocal']) && empty($colDiff['onlyServer']) && empty($colDiff['typeMismatch']) && empty($colDiff['collationMismatch'])) {
                            continue;
                        }
                        $anyColDiff = true;
                    ?>
                        <div class="py-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="font-mono font-bold text-sm text-indigo-400"><?= htmlspecialchars($table) ?></h4>
                            </div>
                            <?php if (!empty($colDiff['onlyLocal'])): ?>
                                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs">
                                    <p class="font-semibold text-amber-300 mb-2">Columns Only in Local:</p>
                                    <div class="space-y-1 font-mono">
                                        <?php foreach ($colDiff['onlyLocal'] as $c => $d): ?>
                                            <div class="flex items-center justify-between">
                                                <span><?= htmlspecialchars($c) ?> <span class="text-zinc-500">(<?= htmlspecialchars($d['Type']) ?>)</span></span>
                                                <button onclick="syncSingleColumn('<?= htmlspecialchars($table) ?>', '<?= htmlspecialchars($c) ?>', this)" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-500 text-white rounded text-[11px] font-sans font-semibold">
                                                    Sync Column to Server
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$anyColDiff): ?>
                        <p class="text-xs text-emerald-400 py-4 text-center font-medium">✅ All columns in matching tables are fully identical between local and server!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 3: INDEX DIFFERENCES -->
        <!-- ================================================================= -->
        <div id="tab_content_index_diff" class="tab-content hidden space-y-6">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6">
                <h3 class="text-sm font-bold text-zinc-100 mb-4 flex items-center gap-2">
                    <i class="fas fa-key text-purple-400"></i> Index Validation & Keys
                </h3>
                <div class="divide-y divide-zinc-800">
                    <?php
                    $anyIdxDiff = false;
                    foreach ($matchingTables as $table):
                        $idxDiff = compareIndexes($table, $localConn, $serverConn);
                        if (empty($idxDiff['onlyLocal']) && empty($idxDiff['onlyServer']) && empty($idxDiff['mismatch']))
                            continue;
                        $anyIdxDiff = true;
                    ?>
                        <div class="py-4 space-y-2">
                            <h4 class="font-mono font-bold text-sm text-purple-400"><?= htmlspecialchars($table) ?></h4>
                            <?php if (!empty($idxDiff['onlyLocal'])): ?>
                                <div class="p-3 rounded-xl bg-purple-500/10 border border-purple-500/20 text-xs">
                                    <p class="font-semibold text-purple-300 mb-2">Missing Indexes on Server:</p>
                                    <div class="space-y-1 font-mono">
                                        <?php foreach ($idxDiff['onlyLocal'] as $idxKey => $info): ?>
                                            <div class="flex items-center justify-between">
                                                <span><code><?= htmlspecialchars($idxKey) ?></code> (<?= $info['unique'] ? 'UNIQUE ' : '' ?>on <?= implode(', ', $info['columns']) ?>)</span>
                                                <button onclick="syncSingleIndex('<?= htmlspecialchars($table) ?>', '<?= htmlspecialchars($idxKey) ?>', this)" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded text-[11px] font-sans font-semibold">
                                                    Add Index to Server
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$anyIdxDiff): ?>
                        <p class="text-xs text-emerald-400 py-4 text-center font-medium">✅ All database indexes match perfectly between local and server!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- SEQUENTIAL LIVE SYNC MODAL / CONSOLE -->
    <!-- ========================================================================= -->
    <div id="sync_progress_modal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-6">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 animate-pulse">
                        <i class="fas fa-sync-alt fa-spin"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Live Data Synchronization</h3>
                        <p class="text-xs text-zinc-400" id="sync_current_target">Preparing sync sequence...</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="btn_pause_sync" onclick="togglePauseSync()" class="px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold transition-all">
                        <i class="fas fa-pause mr-1"></i> Pause
                    </button>
                    <button id="btn_cancel_sync" onclick="cancelSyncProcess()" class="px-3 py-1.5 rounded-xl bg-red-600/20 hover:bg-red-600/30 text-red-400 text-xs font-semibold transition-all">
                        <i class="fas fa-stop mr-1"></i> Stop
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-mono">
                    <span class="text-zinc-400" id="sync_progress_label">0 / 0 Tables (0%)</span>
                    <span class="text-indigo-400 font-bold" id="sync_time_elapsed">Elapsed: 00:00</span>
                </div>
                <div class="w-full h-3 bg-zinc-950 rounded-full overflow-hidden border border-zinc-800 p-0.5">
                    <div id="sync_progress_bar" class="h-full rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500 transition-all duration-300 w-0"></div>
                </div>
            </div>

            <!-- Live Terminal Log Box -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-[11px] text-zinc-500 uppercase tracking-wider font-semibold">
                    <span>Sync Activity Log</span>
                    <button onclick="clearSyncLogs()" class="hover:text-zinc-300">Clear Logs</button>
                </div>
                <div id="sync_terminal_box" class="h-64 bg-zinc-950 border border-zinc-800/90 rounded-2xl p-4 overflow-y-auto font-mono text-xs text-zinc-300 space-y-1.5 custom-scrollbar">
                    <div class="text-zinc-500 italic">[Waiting to start...]</div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-2 border-t border-zinc-800">
                <div class="text-xs text-zinc-400">
                    <span id="sync_status_summary">Sequential non-blocking batches</span>
                </div>
                <button id="btn_close_sync_modal" onclick="closeSyncModal()" class="hidden px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all">
                    Done / Close
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CLIENT SIDE LOGIC -->
    <!-- ========================================================================= -->
    <script>
        // State variables for batch synchronization
        let syncQueue = [];
        let syncCurrentIndex = 0;
        let syncTotalTables = 0;
        let isSyncRunning = false;
        let isSyncPaused = false;
        let syncTimerInterval = null;
        let syncStartTimestamp = 0;

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg');
                el.classList.add('text-zinc-400');
            });

            const content = document.getElementById(`tab_content_${tabId}`);
            const btn = document.getElementById(`tab_btn_${tabId}`);
            if (content) content.classList.remove('hidden');
            if (btn) {
                btn.classList.add('bg-indigo-600', 'text-white', 'shadow-lg');
                btn.classList.remove('text-zinc-400');
            }
        }

        function filterTablesList() {
            const query = (document.getElementById('table_search_input')?.value || '').trim().toLowerCase();
            const filterType = document.getElementById('table_filter_select')?.value || 'all';

            document.querySelectorAll('.table-row-item').forEach(row => {
                const name = row.getAttribute('data-table-name') || '';
                const hasDiff = row.getAttribute('data-has-diff') === '1';

                const matchesQuery = !query || name.includes(query);
                let matchesFilter = true;

                if (filterType === 'diff') matchesFilter = hasDiff;
                if (filterType === 'synced') matchesFilter = !hasDiff;

                row.style.display = (matchesQuery && matchesFilter) ? '' : 'none';
            });
        }

        function toggleSelectAll(checked) {
            document.querySelectorAll('.table-cb').forEach(cb => {
                const row = cb.closest('tr');
                if (row && row.style.display !== 'none') {
                    cb.checked = checked;
                }
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.table-cb:checked').length;
            const label = document.getElementById('selected_count_label');
            if (label) label.textContent = selected;
        }

        function logToTerminal(msg, type = 'info') {
            const box = document.getElementById('sync_terminal_box');
            if (!box) return;

            const time = new Date().toLocaleTimeString();
            let color = 'text-zinc-300';
            let icon = 'ℹ️';

            if (type === 'success') { color = 'text-emerald-400 font-semibold'; icon = '✅'; }
            if (type === 'error') { color = 'text-red-400 font-bold'; icon = '❌'; }
            if (type === 'start') { color = 'text-indigo-400 font-bold'; icon = '🚀'; }
            if (type === 'warn') { color = 'text-amber-400'; icon = '⚠️'; }

            const div = document.createElement('div');
            div.className = `leading-relaxed ${color}`;
            div.innerHTML = `<span class="text-zinc-600">[${time}]</span> ${icon} ${msg}`;
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        }

        function clearSyncLogs() {
            const box = document.getElementById('sync_terminal_box');
            if (box) box.innerHTML = '';
        }

        function updateProgressUI() {
            const pct = syncTotalTables > 0 ? Math.round((syncCurrentIndex / syncTotalTables) * 100) : 0;
            const bar = document.getElementById('sync_progress_bar');
            const label = document.getElementById('sync_progress_label');

            if (bar) bar.style.width = `${pct}%`;
            if (label) label.textContent = `${syncCurrentIndex} / ${syncTotalTables} Tables (${pct}%)`;
        }

        // =========================================================================
        // SEQUENTIAL BATCH SYNC ENGINE
        // =========================================================================
        function startSyncAllSequence() {
            const allCheckboxes = document.querySelectorAll('.table-cb');
            const tables = Array.from(allCheckboxes).map(cb => cb.value);
            if (tables.length === 0) {
                alert('No matching tables found.');
                return;
            }
            if (!confirm(`Are you sure you want to sync all ${tables.length} tables from Production to Local? This will update local data table-by-table.`)) {
                return;
            }
            startBatchProcess(tables);
        }

        function syncSelectedTables() {
            const selected = Array.from(document.querySelectorAll('.table-cb:checked')).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Please select at least one table to sync.');
                return;
            }
            startBatchProcess(selected);
        }

        function startBatchProcess(tables) {
            syncQueue = [...tables];
            syncTotalTables = tables.length;
            syncCurrentIndex = 0;
            isSyncRunning = true;
            isSyncPaused = false;

            // Open Modal
            const modal = document.getElementById('sync_progress_modal');
            const btnClose = document.getElementById('btn_close_sync_modal');
            const btnPause = document.getElementById('btn_pause_sync');
            if (modal) modal.classList.remove('hidden');
            if (btnClose) btnClose.classList.add('hidden');
            if (btnPause) {
                btnPause.innerHTML = '<i class="fas fa-pause mr-1"></i> Pause';
                btnPause.classList.remove('hidden');
            }

            clearSyncLogs();
            logToTerminal(`Initiating sequential batch sync for ${syncTotalTables} tables...`, 'start');

            // Timer
            syncStartTimestamp = Date.now();
            if (syncTimerInterval) clearInterval(syncTimerInterval);
            syncTimerInterval = setInterval(() => {
                const elapsedSec = Math.floor((Date.now() - syncStartTimestamp) / 1000);
                const mm = String(Math.floor(elapsedSec / 60)).padStart(2, '0');
                const ss = String(elapsedSec % 60).padStart(2, '0');
                const timeEl = document.getElementById('sync_time_elapsed');
                if (timeEl) timeEl.textContent = `Elapsed: ${mm}:${ss}`;
            }, 1000);

            updateProgressUI();
            processNextTableInQueue();
        }

        async function processNextTableInQueue() {
            if (!isSyncRunning) return;

            if (isSyncPaused) {
                logToTerminal('Sync process paused by user.', 'warn');
                return;
            }

            if (syncCurrentIndex >= syncTotalTables) {
                // Completed!
                finishSyncProcess();
                return;
            }

            const tableName = syncQueue[syncCurrentIndex];
            const targetLabel = document.getElementById('sync_current_target');
            if (targetLabel) targetLabel.textContent = `[${syncCurrentIndex + 1}/${syncTotalTables}] Syncing \`${tableName}\`...`;

            // Mark row in table as syncing
            updateRowStatus(tableName, 'syncing');

            logToTerminal(`[${syncCurrentIndex + 1}/${syncTotalTables}] Syncing table <strong>${tableName}</strong>...`);

            try {
                const response = await fetch(`sync_db.php?ajax_action=sync_single_table&table=${encodeURIComponent(tableName)}`);
                const data = await response.json();

                if (data.success) {
                    logToTerminal(`Table <strong>${tableName}</strong> synced: ${data.rows.toLocaleString()} rows in ${data.time}s`, 'success');
                    updateRowStatus(tableName, 'synced', data.prodRows, data.localRows);
                } else {
                    logToTerminal(`Error syncing <strong>${tableName}</strong>: ${data.error}`, 'error');
                    updateRowStatus(tableName, 'error');
                }
            } catch (err) {
                logToTerminal(`Network / Request error for <strong>${tableName}</strong>: ${err.message}`, 'error');
                updateRowStatus(tableName, 'error');
            }

            syncCurrentIndex++;
            updateProgressUI();

            // Next table
            setTimeout(processNextTableInQueue, 80);
        }

        function togglePauseSync() {
            if (!isSyncRunning) return;
            isSyncPaused = !isSyncPaused;
            const btnPause = document.getElementById('btn_pause_sync');
            if (btnPause) {
                btnPause.innerHTML = isSyncPaused 
                    ? '<i class="fas fa-play mr-1"></i> Resume' 
                    : '<i class="fas fa-pause mr-1"></i> Pause';
            }
            if (!isSyncPaused) {
                logToTerminal('Resuming sync...', 'start');
                processNextTableInQueue();
            }
        }

        function cancelSyncProcess() {
            if (!confirm('Are you sure you want to cancel the remaining sync queue?')) return;
            isSyncRunning = false;
            clearInterval(syncTimerInterval);
            logToTerminal('Sync cancelled by user.', 'warn');
            finishSyncProcess();
        }

        function finishSyncProcess() {
            isSyncRunning = false;
            clearInterval(syncTimerInterval);

            const targetLabel = document.getElementById('sync_current_target');
            const btnClose = document.getElementById('btn_close_sync_modal');
            const btnPause = document.getElementById('btn_pause_sync');

            if (targetLabel) targetLabel.textContent = 'All operations finished.';
            if (btnClose) btnClose.classList.remove('hidden');
            if (btnPause) btnPause.classList.add('hidden');

            logToTerminal('🎉 All synchronization tasks completed!', 'success');
        }

        function closeSyncModal() {
            const modal = document.getElementById('sync_progress_modal');
            if (modal) modal.classList.add('hidden');
        }

        // =========================================================================
        // SINGLE TABLE OPERATIONS
        // =========================================================================
        async function syncSingleTable(tableName, btn) {
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
            }

            updateRowStatus(tableName, 'syncing');

            try {
                const response = await fetch(`sync_db.php?ajax_action=sync_single_table&table=${encodeURIComponent(tableName)}`);
                const data = await response.json();

                if (data.success) {
                    updateRowStatus(tableName, 'synced', data.prodRows, data.localRows);
                } else {
                    alert(`Error syncing ${tableName}: ` + data.error);
                    updateRowStatus(tableName, 'error');
                }
            } catch (e) {
                alert(`Request error: ` + e.message);
                updateRowStatus(tableName, 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        function updateRowStatus(tableName, status, prodCount = null, localCount = null) {
            const row = document.getElementById(`row_${tableName}`);
            const statusCol = document.getElementById(`status_col_${tableName}`);
            const localCountCol = document.getElementById(`local_count_${tableName}`);

            if (localCount !== null && localCountCol) {
                localCountCol.textContent = Number(localCount).toLocaleString();
            }

            if (!statusCol) return;

            if (status === 'syncing') {
                statusCol.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-sans font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        <i class="fas fa-spinner fa-spin text-[9px]"></i> Syncing...
                    </span>
                `;
            } else if (status === 'synced') {
                statusCol.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-sans font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <i class="fas fa-circle-check text-[9px]"></i> In Sync
                    </span>
                `;
                if (localCountCol) {
                    localCountCol.className = 'p-4 text-right font-bold text-emerald-400';
                }
                if (row) row.setAttribute('data-has-diff', '0');
            } else if (status === 'error') {
                statusCol.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-sans font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                        <i class="fas fa-circle-xmark text-[9px]"></i> Error
                    </span>
                `;
            }
        }

        async function syncTableSchema(tableName, btn) {
            if (!confirm(`Create table \`${tableName}\` on server?`)) return;
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

            try {
                const res = await fetch(`sync_db.php?ajax_action=sync_table_schema&table=${encodeURIComponent(tableName)}`);
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (e) {
                alert('Request failed: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }

        async function syncSingleColumn(table, column, btn) {
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';

            try {
                const res = await fetch(`sync_db.php?ajax_action=sync_column&table=${encodeURIComponent(table)}&column=${encodeURIComponent(column)}`);
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    btn.parentElement.remove();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (e) {
                alert('Request failed: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }

        async function syncSingleIndex(table, indexKey, btn) {
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

            try {
                const res = await fetch(`sync_db.php?ajax_action=sync_index&table=${encodeURIComponent(table)}&index_key=${encodeURIComponent(indexKey)}`);
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    btn.parentElement.remove();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (e) {
                alert('Request failed: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }
    </script>
</body>
</html>