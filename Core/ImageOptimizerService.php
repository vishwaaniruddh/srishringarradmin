<?php
namespace Core;

/**
 * ImageOptimizerService
 * 
 * Comprehensive automated image optimizer, responsive variant generator (Original, Large, Tab, Mobile),
 * and high-resolution scanner for Sri Shringarr & YN admin systems.
 */
class ImageOptimizerService
{
    /**
     * Responsive variant definitions matching modern viewport breakpoints
     */
    public const VARIANTS = [
        'large' => [
            'suffix' => '_large',
            'max_dim' => 1200,
            'quality' => 82,
            'format' => 'webp',
            'label' => 'Best for Large Device (Desktop 1200px Retina)'
        ],
        'tab' => [
            'suffix' => '_tab',
            'max_dim' => 800,
            'quality' => 80,
            'format' => 'webp',
            'label' => 'Best for Tabs (Tablet 800px)'
        ],
        'mobile' => [
            'suffix' => '_mobile',
            'max_dim' => 480,
            'quality' => 78,
            'format' => 'webp',
            'label' => 'Best for Mobile (Smartphones 480px)'
        ]
    ];

    /**
     * Formats bytes to human-readable string
     */
    public static function formatBytes($bytes)
    {
        $bytes = (float)$bytes;
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Returns standard scanned upload directories across the platform
     */
    public static function getAvailableRoots()
    {
        $roots = [];

        // 1. Sri Shringarr product uploads (ss/yn/uploads)
        $ssUploads = realpath(__DIR__ . '/../../yn/uploads');
        if (!$ssUploads) {
            $ssUploads = realpath(__DIR__ . '/../yn/uploads');
        }
        if ($ssUploads && is_dir($ssUploads)) {
            $roots['ss_uploads'] = [
                'id' => 'ss_uploads',
                'name' => 'Sri Shringarr Product Uploads',
                'folder' => 'ss/yn/uploads',
                'path' => str_replace('\\', '/', $ssUploads),
                'web_prefix' => '/yn/uploads/'
            ];
        }

        // 2. YN Admin Collections (yn/admin/uploads/collections)
        $ynCollections = realpath('C:/xampp/htdocs/yn/admin/uploads/collections');
        if ($ynCollections && is_dir($ynCollections)) {
            $roots['yn_collections'] = [
                'id' => 'yn_collections',
                'name' => 'YN Collections & Lookbooks',
                'folder' => 'yn/admin/uploads/collections',
                'path' => str_replace('\\', '/', $ynCollections),
                'web_prefix' => '/admin/uploads/collections/'
            ];
        }

        // 3. YN Admin Products (yn/admin/uploads/products)
        $ynProducts = realpath('C:/xampp/htdocs/yn/admin/uploads/products');
        if ($ynProducts && is_dir($ynProducts)) {
            $roots['yn_products'] = [
                'id' => 'yn_products',
                'name' => 'YN Product Catalog Images',
                'folder' => 'yn/admin/uploads/products',
                'path' => str_replace('\\', '/', $ynProducts),
                'web_prefix' => '/admin/uploads/products/'
            ];
        }

        return $roots;
    }

    /**
     * Check if a file is an automatically generated responsive variant
     */
    public static function isVariantFile($filePath)
    {
        $name = pathinfo($filePath, PATHINFO_FILENAME);
        return (bool)preg_match('/(_large|_tab|_mobile)$/i', $name);
    }

    /**
     * Resolve companion variant file paths for a given source image path
     */
    public static function getVariantFilePaths($sourceFullPath)
    {
        $dir = pathinfo($sourceFullPath, PATHINFO_DIRNAME);
        $filename = pathinfo($sourceFullPath, PATHINFO_FILENAME);

        $paths = [];
        foreach (self::VARIANTS as $key => $conf) {
            $paths[$key] = str_replace('\\', '/', $dir . '/' . $filename . $conf['suffix'] . '.' . $conf['format']);
        }
        return $paths;
    }

    /**
     * Check which responsive variants exist on disk for a given source image
     */
    public static function checkVariantStatus($sourceFullPath)
    {
        $variantPaths = self::getVariantFilePaths($sourceFullPath);
        $status = [
            'has_all' => true,
            'existing_count' => 0,
            'total_count' => count(self::VARIANTS),
            'variants' => []
        ];

        foreach (self::VARIANTS as $key => $conf) {
            $vp = $variantPaths[$key];
            $exists = file_exists($vp) && filesize($vp) > 0;
            if ($exists) {
                $status['existing_count']++;
            } else {
                $status['has_all'] = false;
            }
            $status['variants'][$key] = [
                'exists' => $exists,
                'path' => $vp,
                'size' => $exists ? filesize($vp) : 0,
                'size_fmt' => $exists ? self::formatBytes(filesize($vp)) : 'Missing',
                'label' => $conf['label']
            ];
        }

        return $status;
    }

    /**
     * Generate responsive variants for a single source image file
     * Creates:
     *   - [name]_large.webp (max 1200px, quality 82)
     *   - [name]_tab.webp (max 800px, quality 80)
     *   - [name]_mobile.webp (max 480px, quality 78)
     */
    public static function generateResponsiveVariants($sourceFullPath, $force = false, $compressOriginal = false)
    {
        if (!file_exists($sourceFullPath) || !is_readable($sourceFullPath)) {
            return ['success' => false, 'error' => 'Source image file not found or not readable: ' . basename($sourceFullPath)];
        }

        // Avoid processing a variant of a variant
        if (self::isVariantFile($sourceFullPath)) {
            return ['success' => false, 'error' => 'File is already a generated variant.'];
        }

        $origSize = filesize($sourceFullPath);
        $ext = strtolower(pathinfo($sourceFullPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return ['success' => false, 'error' => "Unsupported format: .$ext"];
        }

        $imageInfo = @getimagesize($sourceFullPath);
        if (!$imageInfo) {
            return ['success' => false, 'error' => 'Invalid or corrupt image header'];
        }

        $origW = $imageInfo[0];
        $origH = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Load source image
        $srcImg = null;
        switch ($mime) {
            case 'image/jpeg':
                $srcImg = @imagecreatefromjpeg($sourceFullPath);
                break;
            case 'image/png':
                $srcImg = @imagecreatefrompng($sourceFullPath);
                break;
            case 'image/webp':
                $srcImg = @imagecreatefromwebp($sourceFullPath);
                break;
        }

        if (!$srcImg) {
            return ['success' => false, 'error' => 'GD failed to create image resource from file'];
        }

        // Auto-rotate if EXIF orientation exists
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourceFullPath);
            if (!empty($exif['Orientation'])) {
                switch ((int)$exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($srcImg, 180, 0);
                        if ($rotated) { imagedestroy($srcImg); $srcImg = $rotated; }
                        break;
                    case 6:
                        $rotated = imagerotate($srcImg, -90, 0);
                        if ($rotated) {
                            imagedestroy($srcImg);
                            $srcImg = $rotated;
                            $tmp = $origW; $origW = $origH; $origH = $tmp;
                        }
                        break;
                    case 8:
                        $rotated = imagerotate($srcImg, 90, 0);
                        if ($rotated) {
                            imagedestroy($srcImg);
                            $srcImg = $rotated;
                            $tmp = $origW; $origW = $origH; $origH = $tmp;
                        }
                        break;
                }
            }
        }

        $variantPaths = self::getVariantFilePaths($sourceFullPath);
        $results = [];
        $totalVariantBytes = 0;

        foreach (self::VARIANTS as $key => $conf) {
            $targetPath = $variantPaths[$key];

            // If already exists and not forcing regeneration, skip
            if (!$force && file_exists($targetPath) && filesize($targetPath) > 0) {
                $sz = filesize($targetPath);
                $totalVariantBytes += $sz;
                $results[$key] = [
                    'status' => 'skipped_exists',
                    'path' => $targetPath,
                    'size' => $sz,
                    'size_fmt' => self::formatBytes($sz),
                    'dimensions' => 'Retained'
                ];
                continue;
            }

            $maxDim = $conf['max_dim'];
            $quality = $conf['quality'];

            // Scale down if larger than max dimension
            if ($origW > $maxDim || $origH > $maxDim) {
                $ratio = min($maxDim / $origW, $maxDim / $origH, 1.0);
                $targetW = max(1, (int)round($origW * $ratio));
                $targetH = max(1, (int)round($origH * $ratio));
            } else {
                $targetW = $origW;
                $targetH = $origH;
            }

            $dstImg = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
            imagefilledrectangle($dstImg, 0, 0, $targetW, $targetH, $transparent);

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

            // Write atomically via temporary file
            $tempFile = dirname($targetPath) . '/.v_tmp_' . uniqid() . '.webp';
            $saved = @imagewebp($dstImg, $tempFile, $quality);
            imagedestroy($dstImg);

            if ($saved && file_exists($tempFile)) {
                @rename($tempFile, $targetPath);
                @chmod($targetPath, 0666);
                $varSize = filesize($targetPath);
                $totalVariantBytes += $varSize;
                $results[$key] = [
                    'status' => 'created',
                    'path' => $targetPath,
                    'size' => $varSize,
                    'size_fmt' => self::formatBytes($varSize),
                    'dimensions' => "{$targetW} × {$targetH} px"
                ];
            } else {
                @unlink($tempFile);
                $results[$key] = [
                    'status' => 'error',
                    'path' => $targetPath,
                    'error' => 'Failed to write webp variant'
                ];
            }
        }

        // Clean up source memory
        if (is_resource($srcImg) || is_object($srcImg)) {
            imagedestroy($srcImg);
        }

        // Optional: Compress master original in-place if requested and large
        $originalComp = null;
        if ($compressOriginal && $origSize > 1048576) {
            $originalComp = self::compressImage($sourceFullPath, 'lossy', 1800, 82);
        }

        return [
            'success' => true,
            'original' => [
                'path' => $sourceFullPath,
                'size' => filesize($sourceFullPath),
                'size_fmt' => self::formatBytes(filesize($sourceFullPath)),
                'dimensions' => "{$origW} × {$origH} px",
                'compressed' => $originalComp
            ],
            'variants' => $results,
            'total_variants_size' => $totalVariantBytes,
            'total_variants_size_fmt' => self::formatBytes($totalVariantBytes)
        ];
    }

    /**
     * In-place image compressor (Lossy or Lossless mode)
     * Matches the proven engine from YN collections image optimizer
     */
    public static function compressImage($fullPath, $mode = 'lossy', $maxDim = 1800, $quality = null)
    {
        if (!file_exists($fullPath) || !is_readable($fullPath) || !is_writable($fullPath)) {
            return ['success' => false, 'message' => 'File not found or permissions denied'];
        }

        // Prevent compressing variants
        if (self::isVariantFile($fullPath)) {
            return ['success' => false, 'message' => 'Cannot compress an automatic variant file directly.'];
        }

        $origSize = filesize($fullPath);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return ['success' => false, 'message' => "Unsupported format: $ext"];
        }

        $imageInfo = @getimagesize($fullPath);
        if (!$imageInfo) {
            return ['success' => false, 'message' => 'Invalid or corrupt image header'];
        }

        $origW = $imageInfo[0];
        $origH = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Load source image
        $srcImg = null;
        switch ($mime) {
            case 'image/jpeg':
                $srcImg = @imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $srcImg = @imagecreatefrompng($fullPath);
                break;
            case 'image/webp':
                $srcImg = @imagecreatefromwebp($fullPath);
                break;
        }

        if (!$srcImg) {
            return ['success' => false, 'message' => 'GD failed to load image resource'];
        }

        // Auto-rotate if EXIF orientation exists
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($fullPath);
            if (!empty($exif['Orientation'])) {
                switch ((int)$exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($srcImg, 180, 0);
                        if ($rotated) { imagedestroy($srcImg); $srcImg = $rotated; }
                        break;
                    case 6:
                        $rotated = imagerotate($srcImg, -90, 0);
                        if ($rotated) { imagedestroy($srcImg); $srcImg = $rotated; $temp = $origW; $origW = $origH; $origH = $temp; }
                        break;
                    case 8:
                        $rotated = imagerotate($srcImg, 90, 0);
                        if ($rotated) { imagedestroy($srcImg); $srcImg = $rotated; $temp = $origW; $origW = $origH; $origH = $temp; }
                        break;
                }
            }
        }

        $targetW = $origW;
        $targetH = $origH;
        $dstImg = $srcImg;
        $didResample = false;

        if ($mode === 'lossy') {
            $quality = $quality ? (int)$quality : 82;
            $maxDim = max(800, min(3840, (int)$maxDim));

            // Scale down if larger than max dimension
            if ($origW > $maxDim || $origH > $maxDim) {
                $ratio = min($maxDim / $origW, $maxDim / $origH, 1.0);
                $targetW = max(1, (int)round($origW * $ratio));
                $targetH = max(1, (int)round($origH * $ratio));

                $dstImg = imagecreatetruecolor($targetW, $targetH);
                if ($mime === 'image/png' || $mime === 'image/webp') {
                    imagealphablending($dstImg, false);
                    imagesavealpha($dstImg, true);
                    $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
                    imagefilledrectangle($dstImg, 0, 0, $targetW, $targetH, $transparent);
                }
                imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
                $didResample = true;
            }

            if ($mime === 'image/png') {
                @imagetruecolortopalette($dstImg, false, 256);
            }
        } else {
            // Lossless mode
            $quality = $quality ? (int)$quality : 92;
            $targetW = $origW;
            $targetH = $origH;
        }

        // Save to temp file in same directory for safe atomic swap
        $tempFile = dirname($fullPath) . '/.comp_tmp_' . uniqid() . '.' . $ext;
        $saved = false;

        switch ($mime) {
            case 'image/jpeg':
                $saved = @imagejpeg($dstImg, $tempFile, $quality);
                break;
            case 'image/png':
                imagealphablending($dstImg, false);
                imagesavealpha($dstImg, true);
                $saved = @imagepng($dstImg, $tempFile, 9);
                break;
            case 'image/webp':
                $saved = @imagewebp($dstImg, $tempFile, $quality);
                break;
        }

        if ($didResample && (is_resource($dstImg) || is_object($dstImg))) {
            imagedestroy($dstImg);
        }
        if (is_resource($srcImg) || is_object($srcImg)) {
            imagedestroy($srcImg);
        }

        if (!$saved || !file_exists($tempFile)) {
            @unlink($tempFile);
            return ['success' => false, 'message' => 'Failed to save compressed image to disk'];
        }

        $newSize = filesize($tempFile);

        // Safety check: Only replace if the new file is smaller!
        if ($newSize < $origSize) {
            @rename($tempFile, $fullPath);
            @chmod($fullPath, 0666);
            clearstatcache(true, $fullPath);

            $savedBytes = $origSize - $newSize;
            $savingsPct = round(($savedBytes / $origSize) * 100, 1);

            // Also regenerate responsive variants so variants match the optimized master
            self::generateResponsiveVariants($fullPath, true);

            return [
                'success' => true,
                'unchanged' => false,
                'mode' => $mode,
                'old_size' => $origSize,
                'new_size' => $newSize,
                'old_size_fmt' => self::formatBytes($origSize),
                'new_size_fmt' => self::formatBytes($newSize),
                'saved_bytes' => $savedBytes,
                'saved_bytes_fmt' => self::formatBytes($savedBytes),
                'savings_percent' => $savingsPct,
                'dimensions' => "{$targetW} × {$targetH} px",
                'message' => "Saved {$savingsPct}% (" . self::formatBytes($savedBytes) . ")"
            ];
        } else {
            @unlink($tempFile);
            return [
                'success' => true,
                'unchanged' => true,
                'mode' => $mode,
                'old_size' => $origSize,
                'new_size' => $origSize,
                'old_size_fmt' => self::formatBytes($origSize),
                'new_size_fmt' => self::formatBytes($origSize),
                'saved_bytes' => 0,
                'saved_bytes_fmt' => '0 B',
                'savings_percent' => 0,
                'dimensions' => "{$origW} × {$origH} px",
                'message' => 'Already optimally compressed'
            ];
        }
    }

    /**
     * Scan an upload root directory for all master images and evaluate variant coverage
     */
    public static function scanDirectory($baseDir, $minSizeKb = 0)
    {
        $baseDir = realpath($baseDir);
        if (!$baseDir || !is_dir($baseDir)) {
            return ['success' => false, 'error' => 'Directory not found: ' . $baseDir];
        }

        $minSizeBytes = (int)$minSizeKb * 1024;
        $filesIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS)
        );

        $images = [];
        $totalFiles = 0;
        $totalBytes = 0;
        $countGt10mb = 0;
        $countGt5mb = 0;
        $countGt2mb = 0;
        $countGt1mb = 0;
        $countGt500kb = 0;
        $heavyBytes = 0;
        $variantsMissingCount = 0;
        $variantsCompleteCount = 0;

        $validExts = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($filesIterator as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $validExts)) continue;

            $pathname = str_replace('\\', '/', $file->getPathname());

            // Skip internal generated variants from main scan list
            if (self::isVariantFile($pathname)) {
                continue;
            }

            $size = $file->getSize();
            $totalFiles++;
            $totalBytes += $size;

            if ($size > 10 * 1048576) $countGt10mb++;
            if ($size > 5 * 1048576) $countGt5mb++;
            if ($size > 2 * 1048576) $countGt2mb++;
            if ($size > 1 * 1048576) {
                $countGt1mb++;
                $heavyBytes += $size;
            }
            if ($size > 500 * 1024) $countGt500kb++;

            // Check variant status
            $vStatus = self::checkVariantStatus($pathname);
            if ($vStatus['has_all']) {
                $variantsCompleteCount++;
            } else {
                $variantsMissingCount++;
            }

            if ($size >= $minSizeBytes) {
                $baseNorm = str_replace('\\', '/', $baseDir);
                $relPath = substr($pathname, strlen($baseNorm) + 1);
                $parts = explode('/', $relPath);
                $subfolder = count($parts) > 1 ? $parts[0] : 'root';

                $badgeType = 'success';
                if ($size > 5 * 1048576) {
                    $badgeType = 'danger';
                } elseif ($size > 1 * 1048576) {
                    $badgeType = 'warning';
                } elseif ($size > 500 * 1024) {
                    $badgeType = 'info';
                }

                $images[] = [
                    'id' => md5($relPath),
                    'rel_path' => $relPath,
                    'full_path' => $pathname,
                    'filename' => $file->getFilename(),
                    'folder' => $subfolder,
                    'size' => $size,
                    'size_fmt' => self::formatBytes($size),
                    'ext' => $ext,
                    'badge' => $badgeType,
                    'mtime' => $file->getMTime(),
                    'date_fmt' => date('d M Y, h:i A', $file->getMTime()),
                    'variants_status' => $vStatus
                ];
            }
        }

        // Sort descending by size (heaviest first)
        usort($images, fn($a, $b) => $b['size'] - $a['size']);

        // Inspect dimensions for the top 50 images for instant display
        $topCount = min(count($images), 50);
        for ($i = 0; $i < $topCount; $i++) {
            $dim = @getimagesize($images[$i]['full_path']);
            if ($dim) {
                $images[$i]['dimensions'] = "{$dim[0]} × {$dim[1]} px";
            }
        }

        $estSavingsBytes = (int)round($heavyBytes * 0.85);

        return [
            'success' => true,
            'summary' => [
                'total_files' => $totalFiles,
                'total_size_bytes' => $totalBytes,
                'total_size_fmt' => self::formatBytes($totalBytes),
                'count_gt_10mb' => $countGt10mb,
                'count_gt_5mb' => $countGt5mb,
                'count_gt_2mb' => $countGt2mb,
                'count_gt_1mb' => $countGt1mb,
                'count_gt_500kb' => $countGt500kb,
                'heavy_size_bytes' => $heavyBytes,
                'heavy_size_fmt' => self::formatBytes($heavyBytes),
                'est_savings_bytes' => $estSavingsBytes,
                'est_savings_fmt' => self::formatBytes($estSavingsBytes),
                'variants_complete_count' => $variantsCompleteCount,
                'variants_missing_count' => $variantsMissingCount
            ],
            'files_count' => count($images),
            'files' => $images
        ];
    }
}
