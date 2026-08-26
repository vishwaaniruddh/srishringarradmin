<?php
namespace Controllers;

use Core\Controller;
use Models\StatsModel;

class DashboardController extends Controller {
    public function index() {
        $this->view('dashboard');
    }

    public function systemInfo() {
        $this->view('system_info');
    }

    public function testUpload() {
        header('Content-Type: application/json');
        
        $startTime = microtime(true);
        
        if (empty($_FILES['test_file'])) {
            echo json_encode([
                'success' => false,
                'message' => 'No file received by PHP. The file may exceed post_max_size or upload_max_filesize.'
            ]);
            exit;
        }

        $file = $_FILES['test_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMap = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder in PHP.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            echo json_encode([
                'success' => false,
                'error_code' => $file['error'],
                'message' => $errorMap[$file['error']] ?? 'Unknown upload error.'
            ]);
            exit;
        }

        $sizeBytes = (int)$file['size'];
        $name = $file['name'];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $tmpName = $file['tmp_name'];

        $isZip = ($ext === 'zip');
        $zipCount = 0;
        $zipError = null;

        if ($isZip && class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            $res = $zip->open($tmpName);
            if ($res === true) {
                $zipCount = $zip->numFiles;
                $zip->close();
            } else {
                $zipError = 'ZipArchive failed to read archive (Error Code: ' . $res . ')';
            }
        }

        $elapsed = round(microtime(true) - $startTime, 4);

        echo json_encode([
            'success' => true,
            'filename' => $name,
            'size_bytes' => $sizeBytes,
            'size_formatted' => $this->formatBytes($sizeBytes),
            'server_processing_seconds' => $elapsed,
            'is_zip' => $isZip,
            'zip_files_count' => $zipCount,
            'zip_error' => $zipError,
            'memory_used' => $this->formatBytes(memory_get_peak_usage(true))
        ]);
        exit;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

