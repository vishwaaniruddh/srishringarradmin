<?php
namespace Controllers;

use Core\Controller;

class AianalyticsController extends Controller
{

    private function getDbConnection()
    {
        return \Core\Database::getConnection('con');
    }

    public function index()
    {
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

            @mysqli_query($con, "ALTER TABLE ai_analytics ADD COLUMN website VARCHAR(100) DEFAULT 'srishringarr'");
            $res = mysqli_query($con, "SELECT COUNT(*) as gens, SUM(num_images) as imgs, SUM(total_tokens) as tokens, SUM(cost_estimate) as cost FROM ai_analytics");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $image_totals['total_generations'] = $row['gens'] ?? 0;
                $image_totals['total_images'] = $row['imgs'] ?? 0;
                $image_totals['total_tokens'] = $row['tokens'] ?? 0;
                $image_totals['total_cost'] = (float) ($row['cost'] ?? 0.00);
            }

            $logSql = "SELECT a.*, 
                              COALESCE(p.product_code, gp.gproduct_code) AS product_sku
                       FROM ai_analytics a
                       LEFT JOIN product p ON (LOWER(a.product_type) LIKE '%jewel%' AND a.product_id = p.product_id)
                       LEFT JOIN garment_product gp ON ((LOWER(a.product_type) LIKE '%garment%' OR LOWER(a.product_type) LIKE '%apparel%') AND a.product_id = gp.gproduct_id)
                       ORDER BY a.created_at DESC 
                       LIMIT 200";
            $logRes = mysqli_query($con, $logSql);
            if ($logRes) {
                while ($row = mysqli_fetch_assoc($logRes)) {
                    $numImgs = (int) ($row['num_images'] ?? 0);
                    $opType = $row['operation_type'] ?? ($numImgs > 0 ? 'image' : 'text');
                    $row['operation_type'] = $opType;

                    if ($opType === 'image' && (float) $row['cost_estimate'] < 0.1) {
                        $row['cost_estimate'] = ($numImgs > 0 ? $numImgs : 1) * 0.03 * 86;
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
