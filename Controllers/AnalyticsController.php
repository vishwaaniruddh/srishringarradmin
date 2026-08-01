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

        // Date Filter handling
        $preset = isset($_GET['preset']) ? trim($_GET['preset']) : '';
        $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

        if ($preset) {
            $today = date('Y-m-d');
            if ($preset === 'today') {
                $startDate = $today;
                $endDate = $today;
            } elseif ($preset === '7days') {
                $startDate = date('Y-m-d', strtotime('-6 days'));
                $endDate = $today;
            } elseif ($preset === '30days') {
                $startDate = date('Y-m-d', strtotime('-29 days'));
                $endDate = $today;
            } elseif ($preset === 'this_month') {
                $startDate = date('Y-m-01');
                $endDate = $today;
            } elseif ($preset === 'all') {
                $startDate = '';
                $endDate = '';
            }
        }

        // Validate date formats (Y-m-d)
        if ($startDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = '';
        }
        if ($endDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = '';
        }

        // Build SQL conditions
        $eventConditions = [];
        $rentConditions = [];

        if ($startDate) {
            $sEsc = mysqli_real_escape_string($db, $startDate . ' 00:00:00');
            $eventConditions[] = "created_at >= '$sEsc'";
            if ($db3) {
                $sEscRent = mysqli_real_escape_string($db3, $startDate . ' 00:00:00');
                $rentConditions[] = "bill_date >= '$sEscRent'";
            }
        }
        if ($endDate) {
            $eEsc = mysqli_real_escape_string($db, $endDate . ' 23:59:59');
            $eventConditions[] = "created_at <= '$eEsc'";
            if ($db3) {
                $eEscRent = mysqli_real_escape_string($db3, $endDate . ' 23:59:59');
                $rentConditions[] = "bill_date <= '$eEscRent'";
            }
        }

        $whereEvents = !empty($eventConditions) ? ' WHERE ' . implode(' AND ', $eventConditions) : '';
        $andEvents = !empty($eventConditions) ? ' AND ' . implode(' AND ', $eventConditions) : '';
        $whereRent = !empty($rentConditions) ? ' WHERE ' . implode(' AND ', $rentConditions) : '';

        // 1. General Metrics
        $sessQ = mysqli_query($db, "SELECT COUNT(DISTINCT session_id) FROM analytics_events{$whereEvents}");
        $totalSessions = $sessQ ? (int)mysqli_fetch_row($sessQ)[0] : 0;

        $pvQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events{$whereEvents}");
        $totalPageViews = $pvQ ? (int)mysqli_fetch_row($pvQ)[0] : 0;

        $prodVQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'product_view' AND target_id IS NOT NULL{$andEvents}");
        $totalProductViews = $prodVQ ? (int)mysqli_fetch_row($prodVQ)[0] : 0;

        // 2. Top Trending Products (from analytics_events where product_view has target_id)
        $topProducts = [];
        $topProdQ = mysqli_query($db, "
            SELECT target_id as product_id, target_type as product_type, page_path, COUNT(*) as view_count 
            FROM analytics_events 
            WHERE event_type = 'product_view' AND target_id IS NOT NULL{$andEvents} 
            GROUP BY target_id, target_type, page_path 
            ORDER BY view_count DESC 
            LIMIT 10
        ");
        if ($topProdQ) {
            while ($row = mysqli_fetch_assoc($topProdQ)) {
                $pid = (int)$row['product_id'];
                $type = $row['product_type'];

                // Get product name & SKU from DB
                $dbName = '';
                $dbSku = '';
                if ($type === 'jewellery') {
                    $pInfoQ = mysqli_query($db, "SELECT product_name, product_code FROM product WHERE product_id = $pid LIMIT 1");
                    if ($pInfoQ && $pInfo = mysqli_fetch_assoc($pInfoQ)) {
                        $dbName = $pInfo['product_name'] ?? '';
                        $dbSku = $pInfo['product_code'] ?? '';
                    }
                } else {
                    $pInfoQ = mysqli_query($db, "SELECT gproduct_name, gproduct_code FROM garment_product WHERE gproduct_id = $pid LIMIT 1");
                    if ($pInfoQ && $pInfo = mysqli_fetch_assoc($pInfoQ)) {
                        $dbName = $pInfo['gproduct_name'] ?? '';
                        $dbSku = $pInfo['gproduct_code'] ?? '';
                    }
                }

                // Fallback: extract name from page_path slug
                if (empty($dbName)) {
                    $slug = basename($row['page_path'] ?? '');
                    $slug = preg_replace('/-\d+$/', '', $slug);
                    $dbName = ucwords(str_replace('-', ' ', $slug));
                }

                // Get product image
                $imgField = ($type === 'jewellery') ? 'product_id' : 'gproduct_id';
                $imgQ = mysqli_query($db, "SELECT img_name FROM product_images_new WHERE $imgField = $pid ORDER BY rank ASC LIMIT 1");
                $imgRow = $imgQ ? mysqli_fetch_assoc($imgQ) : null;
                $imgPath = 'https://srishringarr.com/static/images/default.jpg';
                if ($imgRow && !empty($imgRow['img_name'])) {
                    $cleanImg = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads'], '', $imgRow['img_name']), '/');
                    $imgPath = "https://srishringarr.com/yn/uploads/" . $cleanImg;
                }

                // Build live website URL from page_path
                $websiteUrl = 'https://srishringarr.com' . ($row['page_path'] ?? '');

                $row['product_name'] = $dbName;
                $row['product_sku'] = $dbSku;
                $row['image_url'] = $imgPath;
                $row['website_url'] = $websiteUrl;
                $topProducts[] = $row;
            }
        }

        // Enrich: per-product cart_add counts + unique visitors for conversion rate
        foreach ($topProducts as &$tp) {
            $tpId = (int)$tp['product_id'];
            $cartQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_add' AND target_id = $tpId{$andEvents}");
            $tp['cart_adds'] = $cartQ ? (int)mysqli_fetch_row($cartQ)[0] : 0;

            $uvQ = mysqli_query($db, "SELECT COUNT(DISTINCT session_id) FROM analytics_events WHERE event_type = 'product_view' AND target_id = $tpId{$andEvents}");
            $tp['unique_visitors'] = $uvQ ? (int)mysqli_fetch_row($uvQ)[0] : 0;
        }
        unset($tp);

        // 3. Top Search Queries — detect from ANY event with ?q= in page_path
        $topSearches = [];
        $topSearchQ = mysqli_query($db, "
            SELECT page_path, COUNT(*) as search_count 
            FROM analytics_events 
            WHERE page_path LIKE '%?q=%'{$andEvents} 
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
        $cartAddsQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_add'{$andEvents}");
        if ($cartAddsQ) {
            $funnel['cart_adds'] = (int)mysqli_fetch_row($cartAddsQ)[0];
        }

        // Checkout Starts
        $checkoutQ = mysqli_query($db, "SELECT COUNT(*) FROM analytics_events WHERE event_type = 'checkout_start'{$andEvents}");
        if ($checkoutQ) {
            $funnel['checkout_starts'] = (int)mysqli_fetch_row($checkoutQ)[0];
        }

        // Purchases (we pull from POS database)
        if ($db3) {
            $ordersQ = mysqli_query($db3, "SELECT COUNT(*) FROM phppos_rent{$whereRent}");
            if ($ordersQ) {
                $funnel['purchases'] = (int)mysqli_fetch_row($ordersQ)[0];
            }
        }

        // 5. Popular Categories
        $topCategories = [];
        $catQ = mysqli_query($db, "
            SELECT page_path, COUNT(*) as cat_count 
            FROM analytics_events 
            WHERE event_type = 'category_view'{$andEvents} 
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
                    'count' => $row['cat_count'],
                    'website_url' => 'https://srishringarr.com' . $row['page_path']
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
            FROM analytics_events{$whereEvents} 
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
                    WHERE session_id = '$sid'{$andEvents} 
                    ORDER BY created_at ASC
                ");
                $events = [];
                if ($eventsQ) {
                    while ($ev = mysqli_fetch_assoc($eventsQ)) {
                        // Enrich product_view events with SKU and website URL
                        if ($ev['event_type'] === 'product_view' && $ev['target_id']) {
                            $evPid = (int)$ev['target_id'];
                            $evSku = '';

                            // Try jewellery table first
                            $skuQ = mysqli_query($db, "SELECT product_code FROM product WHERE product_id = $evPid LIMIT 1");
                            if ($skuQ && $skuRow = mysqli_fetch_assoc($skuQ)) {
                                $evSku = $skuRow['product_code'] ?? '';
                            }

                            // Fallback: try garment table
                            if (empty($evSku)) {
                                $skuQ2 = mysqli_query($db, "SELECT gproduct_code FROM garment_product WHERE gproduct_id = $evPid LIMIT 1");
                                if ($skuQ2 && $skuRow2 = mysqli_fetch_assoc($skuQ2)) {
                                    $evSku = $skuRow2['gproduct_code'] ?? '';
                                }
                            }

                            $ev['product_sku'] = $evSku;
                            $ev['website_url'] = 'https://srishringarr.com' . ($ev['page_path'] ?? '');
                        }
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
            'sessions' => $sessions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'preset' => $preset
        ]);
    }
}
