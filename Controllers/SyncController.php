<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Core\ProductSyncService;

class SyncController extends Controller {

    public function index() {
        $con = Database::getConnection('con');
        $logs = [];
        $stats = [
            'total_synced' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'last_sync' => 'Never'
        ];

        if ($con) {
            // Stats
            $res = mysqli_query($con, "SELECT 
                COUNT(*) as total, 
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_cnt,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_cnt,
                MAX(synced_at) as last_time
                FROM product_sync_logs");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $stats['total_synced'] = (int)($row['total'] ?? 0);
                $stats['success_count'] = (int)($row['success_cnt'] ?? 0);
                $stats['failed_count'] = (int)($row['failed_cnt'] ?? 0);
                $stats['last_sync'] = !empty($row['last_time']) ? date('M j, Y g:i A', strtotime($row['last_time'])) : 'Never';
            }

            // Recent Logs
            $logRes = mysqli_query($con, "SELECT * FROM product_sync_logs ORDER BY synced_at DESC LIMIT 100");
            if ($logRes) {
                while ($r = mysqli_fetch_assoc($logRes)) {
                    $logs[] = $r;
                }
            }
        }

        $productModel = new \Models\ProductModel();
        $categories = $productModel->getCategories();
        $syncSettings = ProductSyncService::getSyncSettings();

        $this->view('sync/index', [
            'logs' => $logs,
            'stats' => $stats,
            'categories' => $categories,
            'syncSettings' => $syncSettings
        ]);
    }

    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $syncAll = !empty($_POST['sync_all']);
        $enabledCategories = $_POST['categories'] ?? [];

        $saved = ProductSyncService::saveSyncSettings($enabledCategories, $syncAll);
        if ($saved) {
            $this->json(['success' => true, 'message' => 'Sync Configuration saved successfully!']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to save configuration settings']);
        }
    }

    public function syncSingle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? 'jewellery';

        if (!$id) {
            $this->json(['success' => false, 'message' => 'Product ID is required'], 400);
            return;
        }

        $result = ProductSyncService::syncProduct($id, $type, 'manual');
        $this->json($result);
    }

    public function syncBulk() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        // Increase execution time limit for bulk operations
        set_time_limit(300);

        $result = ProductSyncService::syncAllProducts('manual');
        $this->json($result);
    }
}
