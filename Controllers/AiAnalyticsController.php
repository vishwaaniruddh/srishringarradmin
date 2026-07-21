<?php
namespace Controllers;

use Core\Controller;

class AiAnalyticsController extends Controller {

    private function getDbConnection() {
        $con = mysqli_connect("localhost", "root", "", "u464193275_srishrinjewels");
        if (!$con) {
            return null;
        }
        return $con;
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
            mysqli_close($con);
        }
        
        $this->view('ai_analytics/index', ['sessions' => $sessions]);
    }
}
