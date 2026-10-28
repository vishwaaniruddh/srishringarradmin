<?php
/**
 * CLI Tool: Batch Responsive Variant & Compression Engine for Existing Uploaded Products
 * 
 * Usage:
 *   php new_admin/scripts/generate_variants_cli.php [options]
 * 
 * Options:
 *   --dir=ss_uploads|yn_collections|yn_products|all    (default: all)
 *   --limit=100                                        (default: 0 = all)
 *   --force                                            (force regenerate existing variants)
 *   --compress                                         (also compress heavy originals > 1MB)
 *   --dry-run                                          (only scan & report missing variants)
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line (CLI).\n");
}

ini_set('memory_limit', '1024M');
set_time_limit(0);

require_once __DIR__ . '/../Core/ImageOptimizerService.php';

// Parse command line arguments
$options = getopt('', ['dir::', 'limit::', 'force', 'compress', 'dry-run', 'help']);

if (isset($options['help'])) {
    echo "Sri Shringarr & YN Batch Image Variant Generator CLI\n";
    echo "====================================================\n";
    echo "Usage: php generate_variants_cli.php [options]\n\n";
    echo "Options:\n";
    echo "  --dir=<id|all>    Target directory (ss_uploads, yn_collections, yn_products, all) [default: all]\n";
    echo "  --limit=<n>       Limit number of images to process [default: unlimited]\n";
    echo "  --force           Regenerate even if variants already exist\n";
    echo "  --compress        Compress heavy master originals (> 1MB) in-place\n";
    echo "  --dry-run         Scan and display missing variants without creating files\n";
    echo "  --help            Show this help text\n";
    exit(0);
}

$targetDirKey = $options['dir'] ?? 'all';
$limit = isset($options['limit']) ? (int)$options['limit'] : 0;
$force = isset($options['force']);
$compress = isset($options['compress']);
$dryRun = isset($options['dry-run']);

$availableRoots = \Core\ImageOptimizerService::getAvailableRoots();

$selectedRoots = [];
if ($targetDirKey === 'all') {
    $selectedRoots = $availableRoots;
} elseif (isset($availableRoots[$targetDirKey])) {
    $selectedRoots[$targetDirKey] = $availableRoots[$targetDirKey];
} else {
    // Check if custom path was provided
    if (is_dir($targetDirKey)) {
        $selectedRoots['custom'] = [
            'id' => 'custom',
            'name' => 'Custom Directory',
            'folder' => basename($targetDirKey),
            'path' => realpath($targetDirKey)
        ];
    } else {
        echo "[ERROR] Unknown directory key: '$targetDirKey'.\n";
        echo "Available keys: " . implode(', ', array_keys($availableRoots)) . ", all\n";
        exit(1);
    }
}

echo "\n";
echo "========================================================================\n";
echo "   Sri Shringarr & YN - Automated Image Variant & Optimizer Engine      \n";
echo "========================================================================\n";
echo "  Mode:       " . ($dryRun ? "DRY RUN (Scan Only)" : "LIVE PROCESSING") . "\n";
echo "  Force Mode: " . ($force ? "YES (Regenerate All)" : "NO (Skip Existing)") . "\n";
echo "  Compress:   " . ($compress ? "YES (Heavy Originals >1MB)" : "NO") . "\n";
echo "  Limit:      " . ($limit > 0 ? "$limit images" : "Unlimited") . "\n";
echo "  Target(s):  " . implode(', ', array_map(fn($r) => $r['name'], $selectedRoots)) . "\n";
echo "------------------------------------------------------------------------\n\n";

$startTime = microtime(true);
$grandTotalProcessed = 0;
$grandTotalCreated = 0;
$grandTotalSkipped = 0;
$grandTotalErrors = 0;
$grandTotalVariantBytes = 0;

foreach ($selectedRoots as $rootKey => $rootInfo) {
    echo "Scanning: {$rootInfo['name']} ({$rootInfo['path']})...\n";
    $scan = \Core\ImageOptimizerService::scanDirectory($rootInfo['path']);

    if (!$scan['success']) {
        echo "  [ERROR] " . ($scan['error'] ?? 'Scan failed') . "\n\n";
        continue;
    }

    $files = $scan['files'];
    $summary = $scan['summary'];

    echo "  Total Master Images: " . number_format($summary['total_files']) . " (" . $summary['total_size_fmt'] . ")\n";
    echo "  Variant Status: " . number_format($summary['variants_complete_count']) . " complete, " . number_format($summary['variants_missing_count']) . " need variants\n";
    echo "  Heavy Images (>1MB): " . number_format($summary['count_gt_1mb']) . " (" . $summary['heavy_size_fmt'] . ")\n";

    if ($summary['total_files'] === 0) {
        echo "  No images found to process.\n\n";
        continue;
    }

    // Filter candidate list: if not forcing, only process images that are missing variants
    $candidates = [];
    foreach ($files as $f) {
        if ($force || !$f['variants_status']['has_all']) {
            $candidates[] = $f;
        }
    }

    if ($limit > 0 && count($candidates) > $limit) {
        $candidates = array_slice($candidates, 0, $limit);
    }

    $candidateCount = count($candidates);
    echo "  Target Images to Process: " . number_format($candidateCount) . "\n\n";

    if ($dryRun) {
        echo "  [Dry Run Complete for this root. No files modified.]\n\n";
        continue;
    }

    $idx = 0;
    foreach ($candidates as $c) {
        $idx++;
        $fullPath = $c['full_path'];
        $baseName = basename($fullPath);

        $res = \Core\ImageOptimizerService::generateResponsiveVariants($fullPath, $force, $compress);
        $grandTotalProcessed++;

        if ($res['success']) {
            $createdCount = 0;
            $skippedCount = 0;
            foreach ($res['variants'] as $vKey => $vInfo) {
                if (($vInfo['status'] ?? '') === 'created') {
                    $createdCount++;
                    $grandTotalCreated++;
                } else {
                    $skippedCount++;
                    $grandTotalSkipped++;
                }
            }
            $grandTotalVariantBytes += ($res['total_variants_size'] ?? 0);

            // Progress bar
            $pct = round(($idx / $candidateCount) * 100);
            $barLen = 30;
            $filled = (int)round(($idx / $candidateCount) * $barLen);
            $bar = str_repeat('=', $filled) . str_repeat(' ', $barLen - $filled);

            $statusText = "[$createdCount created, $skippedCount retained]";
            printf("\r  [%s] %3d%% (%d/%d) %-30s %s", $bar, $pct, $idx, $candidateCount, substr($baseName, 0, 28), $statusText);
        } else {
            $grandTotalErrors++;
            echo "\n  [ERROR] {$baseName}: " . ($res['error'] ?? 'Unknown error') . "\n";
        }
    }
    echo "\n\n";
}

$elapsed = round(microtime(true) - $startTime, 2);

echo "========================================================================\n";
echo "   BATCH EXECUTION SUMMARY                                              \n";
echo "========================================================================\n";
echo "  Total Images Processed: " . number_format($grandTotalProcessed) . "\n";
echo "  New Variants Created:   " . number_format($grandTotalCreated) . " (Mobile, Tab, Large)\n";
echo "  Variants Retained:      " . number_format($grandTotalSkipped) . "\n";
echo "  Errors:                 " . number_format($grandTotalErrors) . "\n";
echo "  Variant Disk Footprint: " . \Core\ImageOptimizerService::formatBytes($grandTotalVariantBytes) . "\n";
echo "  Total Time Elapsed:     {$elapsed}s\n";
echo "========================================================================\n\n";
