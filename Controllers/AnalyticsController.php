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

        $pvQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'page_view'");
        $totalPageViews = $pvQ ? (int)mysqli_fetch_row($pvQ)[0] : 0;

        $prodVQ = mysqli_query($db, "SELECT COUNT(*) FROM product_views");
        $totalProductViews = $prodVQ ? (int)mysqli_fetch_row($prodVQ)[0] : 0;

        // 2. Top Visited Products
        $topProducts = [];
        $topProdQ = mysqli_query($db, "
            SELECT product_id, product_name, product_type, COUNT(*) as count 
            FROM product_views 
            GROUP BY product_id, product_name, product_type 
            ORDER BY count DESC 
            LIMIT 5
        ");
        if ($topProdQ) {
            while ($row = mysqli_fetch_assoc($topProdQ)) {
                $topProducts[] = $row;
            }
        }

        // 3. Top Search Queries
        $topSearches = [];
        $topSearchQ = mysqli_query($db, "
            SELECT query, results_count, COUNT(*) as search_count 
            FROM analytics_searches 
            GROUP BY query, results_count 
            ORDER BY search_count DESC 
            LIMIT 10
        ");
        if ($topSearchQ) {
            while ($row = mysqli_fetch_assoc($topSearchQ)) {
                $topSearches[] = $row;
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
            SELECT metadata, COUNT(*) as count 
            FROM analytics_events 
            WHERE event_type = 'category_view' 
            GROUP BY metadata 
            ORDER BY count DESC 
            LIMIT 5
        ");
        if ($catQ) {
            while ($row = mysqli_fetch_assoc($catQ)) {
                $meta = json_decode($row['metadata'], true);
                $topCategories[] = [
                    'label' => $meta['categoryLabel'] ?? $meta['categoryKey'] ?? 'Unknown',
                    'count' => $row['count']
                ];
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
            'topCategories' => $topCategories
        ]);
    }
}
