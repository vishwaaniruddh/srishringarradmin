<?php
namespace Controllers;

use Core\Controller;
use Models\ProductModel;

class AnalyticsController extends Controller {

    public function index() {
        // Connect to db
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'u464193275_srishringarr';
        
        $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // 1. General Metrics
        $sessQ = mysqli_query($conn, "SELECT COUNT(DISTINCT session_id) FROM analytics_events");
        $totalSessions = (int)mysqli_fetch_row($sessQ)[0];

        $pvQ = mysqli_query($conn, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'page_view'");
        $totalPageViews = (int)mysqli_fetch_row($pvQ)[0];

        $prodVQ = mysqli_query($conn, "SELECT COUNT(*) FROM product_views");
        $totalProductViews = (int)mysqli_fetch_row($prodVQ)[0];

        // 2. Top Visited Products
        $topProducts = [];
        $topProdQ = mysqli_query($conn, "
            SELECT product_id, product_name, product_type, COUNT(*) as count 
            FROM product_views 
            GROUP BY product_id, product_name, product_type 
            ORDER BY count DESC 
            LIMIT 5
        ");
        while ($row = mysqli_fetch_assoc($topProdQ)) {
            $topProducts[] = $row;
        }

        // 3. Top Search Queries
        $topSearches = [];
        $topSearchQ = mysqli_query($conn, "
            SELECT query, results_count, COUNT(*) as search_count 
            FROM analytics_searches 
            GROUP BY query, results_count 
            ORDER BY search_count DESC 
            LIMIT 10
        ");
        while ($row = mysqli_fetch_assoc($topSearchQ)) {
            $topSearches[] = $row;
        }

        // 4. Conversion Funnel Events
        $funnel = [
            'product_views' => $totalProductViews,
            'cart_adds' => 0,
            'checkout_starts' => 0,
            'purchases' => 0
        ];

        // Cart Adds
        $cartAddsQ = mysqli_query($conn, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_add'");
        if ($cartAddsQ) {
            $funnel['cart_adds'] = (int)mysqli_fetch_row($cartAddsQ)[0];
        }

        // Checkout Starts
        $checkoutQ = mysqli_query($conn, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'checkout_start'");
        if ($checkoutQ) {
            $funnel['checkout_starts'] = (int)mysqli_fetch_row($checkoutQ)[0];
        }

        // Purchases (we can pull from order table)
        $ordersQ = mysqli_query($conn, "SELECT COUNT(*) FROM phppos_rent");
        if ($ordersQ) {
            $funnel['purchases'] = (int)mysqli_fetch_row($ordersQ)[0];
        }

        // 5. Popular Categories
        $topCategories = [];
        $catQ = mysqli_query($conn, "
            SELECT metadata, COUNT(*) as count 
            FROM analytics_events 
            WHERE event_type = 'category_view' 
            GROUP BY metadata 
            ORDER BY count DESC 
            LIMIT 5
        ");
        while ($row = mysqli_fetch_assoc($catQ)) {
            $meta = json_decode($row['metadata'], true);
            $topCategories[] = [
                'label' => $meta['categoryLabel'] ?? $meta['categoryKey'] ?? 'Unknown',
                'count' => $row['count']
            ];
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
