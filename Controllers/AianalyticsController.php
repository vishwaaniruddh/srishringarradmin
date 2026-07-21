<?php
namespace Controllers;

use Core\Controller;

class AianalyticsController extends Controller {

    private function getDbConnection() {
        return \Core\Database::getConnection('con');
    }

    public function index() {
        $con = $this->getDbConnection();
        $sessions = [];
        
        if ($con) {
            // Group by session_id, get latest created_at for sorting
            $sql = "SELECT session_id, MAX(created_at) as session_date, 
                           MAX(context_size) as size, 
                           MAX(context_details) as details 
                    FROM ai_playground_history 
                    GROUP BY session_id 
                    ORDER BY session_date DESC 
                    LIMIT 100";
            $res = mysqli_query($con, $sql);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $session_id = $row['session_id'];
                    $row['items'] = [];
                    
                    // Fetch all items for this session
                    $itemSql = "SELECT type, generated_data, created_at FROM ai_playground_history WHERE session_id = ? ORDER BY created_at ASC";
                    $stmt = $con->prepare($itemSql);
                    $stmt->bind_param("s", $session_id);
                    $stmt->execute();
                    $itemRes = $stmt->get_result();
                    while ($item = $itemRes->fetch_assoc()) {
                        if ($item['type'] === 'names') {
                            $item['generated_data'] = json_decode($item['generated_data'], true);
                        }
                        $row['items'][] = $item;
                    }
                    $stmt->close();
                    
                    $sessions[] = $row;
                }
            }

            // --- Fetch AI Image Generations (Cost Analytics) ---
            $image_totals = [
                'total_generations' => 0,
                'total_images' => 0,
                'total_tokens' => 0,
                'total_cost' => 0.00
            ];
            $image_logs = [];

            $res = mysqli_query($con, "SELECT COUNT(*) as gens, SUM(num_images) as imgs, SUM(total_tokens) as tokens FROM ai_analytics");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $image_totals['total_generations'] = $row['gens'] ?? 0;
                $image_totals['total_images'] = $row['imgs'] ?? 0;
                $image_totals['total_tokens'] = $row['tokens'] ?? 0;
                $image_totals['total_cost'] = ($row['imgs'] ?? 0) * 0.03 * 86; // $0.03/image * ₹86
            }

            $logRes = mysqli_query($con, "SELECT * FROM ai_analytics ORDER BY created_at DESC LIMIT 100");
            if ($logRes) {
                while ($row = mysqli_fetch_assoc($logRes)) {
                    // Ensure accurate Imagen 3 cost representation (~₹2.58 per image)
                    if ($row['cost_estimate'] < 0.1) {
                        $row['cost_estimate'] = ($row['num_images'] ?? 1) * 0.03 * 86;
                    }
                    $image_logs[] = $row;
                }
            }
        }
        
        $this->view('ai_analytics/index', [
            'sessions' => $sessions,
            'image_totals' => $image_totals,
            'image_logs' => $image_logs
        ]);
    }
}
