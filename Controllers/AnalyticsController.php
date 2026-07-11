<?php
namespace Controllers;

use Core\Controller;
use Models\ProductModel;

class AnalyticsController extends Controller {

    public function index() {
        // Fetch database connections from framework Database helper
        $db = \Core\Database::getConnection('con');  // u464193275_srishrinjewels
        $db3 = \Core\Database::getConnection('con3'); // u464193275_srishringarr
        
        if (!$db) {
            die("Connection failed: database 'con' not available.");
        }

        // 1. General Metrics
        $sessQ = mysqli_query($db, "SELECT COUNT(DISTINCT session_id) FROM analytics_events");
        $totalSessions = $sessQ ? (int)mysqli_fetch_row($sessQ)[0] : 0;

        $pvQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events");
        $totalPageViews = $pvQ ? (int)mysqli_fetch_row($pvQ)[0] : 0;

        $prodVQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'product_view' AND target_id IS NOT NULL");
        $totalProductViews = $prodVQ ? (int)mysqli_fetch_row($prodVQ)[0] : 0;

        // 2. Top Visited Products (from analytics_events where product_view has target_id)
        $topProducts = [];
        $topProdQ = mysqli_query($db, "
            SELECT target_id as product_id, target_type as product_type, page_path, COUNT(*) as view_count 
            FROM analytics_events 
            WHERE event_type = 'product_view' AND target_id IS NOT NULL 
            GROUP BY target_id, target_type, page_path 
            ORDER BY view_count DESC 
            LIMIT 5
        ");
        if ($topProdQ) {
            while ($row = mysqli_fetch_assoc($topProdQ)) {
                // Extract readable name from page_path slug: /product/barbie-3d-ball-gown-2394 → Barbie 3D Ball Gown
                $slug = basename($row['page_path'] ?? '');
                $slug = preg_replace('/-\d+$/', '', $slug); // remove trailing ID
                $name = ucwords(str_replace('-', ' ', $slug));
                $row['product_name'] = $name;
                $topProducts[] = $row;
            }
        }

        // 3. Top Search Queries — detect from ANY event with ?q= in page_path
        $topSearches = [];
        $topSearchQ = mysqli_query($db, "
            SELECT page_path, COUNT(*) as search_count 
            FROM analytics_events 
            WHERE page_path LIKE '%?q=%' 
            GROUP BY page_path 
            ORDER BY search_count DESC 
            LIMIT 10
        ");
        if ($topSearchQ) {
            while ($row = mysqli_fetch_assoc($topSearchQ)) {
                $queryStr = '';
                $parsed = parse_url($row['page_path']);
                if (isset($parsed['query'])) {
                    parse_str($parsed['query'], $params);
                    $queryStr = $params['q'] ?? '';
                }
                if (!empty($queryStr)) {
                    $topSearches[] = [
                        'query' => $queryStr,
                        'results_count' => 0,
                        'search_count' => $row['search_count']
                    ];
                }
            }
        }

        // 4. Conversion Funnel Events
        $funnel = [
            'product_views' => $totalProductViews,
            'cart_adds' => 0,
            'checkout_starts' => 0,
            'purchases' => 0
        ];

        // Cart Adds
        $cartAddsQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_add'");
        if ($cartAddsQ) {
            $funnel['cart_adds'] = (int)mysqli_fetch_row($cartAddsQ)[0];
        }

        // Checkout Starts
        $checkoutQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'checkout_start'");
        if ($checkoutQ) {
            $funnel['checkout_starts'] = (int)mysqli_fetch_row($checkoutQ)[0];
        }

        // Purchases (we pull from POS database)
        if ($db3) {
            $ordersQ = mysqli_query($db3, "SELECT COUNT(*) FROM phppos_rent");
            if ($ordersQ) {
                $funnel['purchases'] = (int)mysqli_fetch_row($ordersQ)[0];
            }
        }

        // 5. Popular Categories
        $topCategories = [];
        $catQ = mysqli_query($db, "
            SELECT page_path, COUNT(*) as cat_count 
            FROM analytics_events 
            WHERE event_type = 'category_view' 
            GROUP BY page_path 
            ORDER BY cat_count DESC 
            LIMIT 5
        ");
        if ($catQ) {
            while ($row = mysqli_fetch_assoc($catQ)) {
                // Extract readable label from path: /jewellery/earrings/antique → Earrings / Antique
                $parts = array_filter(explode('/', trim($row['page_path'], '/')));
                array_shift($parts); // remove "jewellery" or "bridal" prefix
                $label = ucwords(implode(' / ', array_map(function($p) {
                    return str_replace('-', ' ', $p);
                }, $parts)));
                if (empty($label)) {
                    // Root category page like /jewellery or /bridal
                    $root = trim($row['page_path'], '/');
                    $label = ucwords(str_replace('-', ' ', $root));
                }
                $topCategories[] = [
                    'label' => $label,
                    'count' => $row['cat_count']
                ];
            }
        }

        // 6. Session Activity Timeline — all sessions with their journeys
        $sessions = [];
        $sessListQ = mysqli_query($db, "
            SELECT session_id, 
                   MIN(created_at) as first_seen, 
                   MAX(created_at) as last_seen,
                   COUNT(*) as total_events 
            FROM analytics_events 
            GROUP BY session_id 
            ORDER BY last_seen DESC 
            LIMIT 20
        ");
        if ($sessListQ) {
            while ($sess = mysqli_fetch_assoc($sessListQ)) {
                $sid = mysqli_real_escape_string($db, $sess['session_id']);
                $eventsQ = mysqli_query($db, "
                    SELECT event_type, page_path, target_id, target_type, created_at 
                    FROM analytics_events 
                    WHERE session_id = '$sid' 
                    ORDER BY created_at ASC
                ");
                $events = [];
                if ($eventsQ) {
                    while ($ev = mysqli_fetch_assoc($eventsQ)) {
                        $events[] = $ev;
                    }
                }
                $sess['events'] = $events;
                $sessions[] = $sess;
            }
        }

        // Render analytics dashboard
        $this->view('analytics/index', [
            'totalSessions' => $totalSessions,
            'totalPageViews' => $totalPageViews,
            'totalProductViews' => $totalProductViews,
            'topProducts' => $topProducts,
            'topSearches' => $topSearches,
            'funnel' => $funnel,
            'topCategories' => $topCategories,
            'sessions' => $sessions
        ]);
    }
}
