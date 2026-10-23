<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;

class VideoGeneratorController extends Controller
{
    private $db;
    private $videoStorageDir;
    private $productModel;

    public function __construct()
    {
        $this->db = Database::getConnection('con');
        $this->productModel = new ProductModel();
        
        $this->videoStorageDir = dirname(__DIR__, 2) . '/yn/uploads/product_videos';
        if (!is_dir($this->videoStorageDir)) {
            @mkdir($this->videoStorageDir, 0755, true);
        }
    }

    /**
     * Main Studio View
     */
    public function index()
    {
        // 1. Fetch structured categories for dropdowns
        $rawCategories = $this->productModel->getCategories();
        $categoriesTree = [];

        foreach ($rawCategories as $mainDept => $deptData) {
            $children = [];
            if (!empty($deptData['children'])) {
                foreach ($deptData['children'] as $key => $child) {
                    $children[] = [
                        'key' => $key,
                        'name' => $child['name'],
                        'count' => $child['count']
                    ];
                }
            }
            $categoriesTree[$mainDept] = [
                'count' => $deptData['count'],
                'children' => $children
            ];
        }

        // 2. Initial Category (default to Rings or Necklace Sets or All)
        $defaultCategory = 'jewel_parent:18'; // Rings (95 products) or jewel_parent:1 (Necklaces)
        $initialProducts = $this->fetchProductsByFilter([
            'category' => $defaultCategory,
            'limit' => 30
        ]);

        $data = [
            'pageTitle' => 'AI Product Video Generator (Gemini Powered)',
            'categoriesTree' => $categoriesTree,
            'initialCategory' => $defaultCategory,
            'initialProducts' => $initialProducts
        ];

        $this->view('video_generator/index', $data);
    }

    /**
     * AJAX: Fetch Products by Category Dropdown & Search Filter
     */
    public function getProductsByCategory()
    {
        $category = trim($_GET['category'] ?? '');
        $department = trim($_GET['department'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $hasVideoFilter = trim($_GET['has_video'] ?? 'all');
        $limit = (int)($_GET['limit'] ?? 50);

        $products = $this->fetchProductsByFilter([
            'category' => $category,
            'department' => $department,
            'search' => $search,
            'has_video' => $hasVideoFilter,
            'limit' => $limit
        ]);

        $this->json([
            'success' => true,
            'count' => count($products),
            'products' => $products
        ]);
    }

    /**
     * Fetch Products with Images and Video Status
     */
    public function fetchProductsByFilter($params = [])
    {
        $categoryParam = $params['category'] ?? '';
        $department = $params['department'] ?? 'all';
        $search = trim($params['search'] ?? '');
        $hasVideo = $params['has_video'] ?? 'all';
        $limit = (int)($params['limit'] ?? 40);
        if ($limit <= 0 || $limit > 200) $limit = 40;

        $jewelWhere = ["1=1"];
        $garmentWhere = ["1=1"];

        // Text search
        if (!empty($search)) {
            $escSearch = mysqli_real_escape_string($this->db, $search);
            $jewelWhere[] = "(p.product_code LIKE '%$escSearch%' OR p.product_name LIKE '%$escSearch%')";
            $garmentWhere[] = "(g.gproduct_code LIKE '%$escSearch%' OR g.gproduct_name LIKE '%$escSearch%')";
        }

        // Category filter
        if (!empty($categoryParam) && $categoryParam !== 'all') {
            if (strpos($categoryParam, ':') !== false) {
                list($type, $id) = explode(':', $categoryParam);
                $id = (int)$id;

                if ($type === 'garment') {
                    $garmentWhere[] = "(g.garment_id = $id OR g.product_for = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = g.gproduct_id AND pc.product_type = 'garments' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id = $id)))";
                    $jewelWhere[] = "1=0";
                } elseif ($type === 'jewel_parent' || $type === 'jewel_main') {
                    $jewelWhere[] = "(p.categories_id = $id OR p.subcat_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id) OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery' AND (pc.legacy_category_id = $id OR pc.legacy_subcategory_id IN (SELECT subcat_id FROM subcat1 WHERE maincat_id = $id))))";
                    $garmentWhere[] = "1=0";
                } elseif ($type === 'jewel_child' || $type === 'jewel_sub') {
                    $jewelWhere[] = "(p.subcat_id = $id OR EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery' AND pc.legacy_subcategory_id = $id))";
                    $garmentWhere[] = "1=0";
                }
            }
        } elseif ($department === 'jewellery') {
            $garmentWhere[] = "1=0";
        } elseif ($department === 'garments' || $department === 'apparel') {
            $jewelWhere[] = "1=0";
        }

        $jewelWhereSql = implode(' AND ', $jewelWhere);
        $garmentWhereSql = implode(' AND ', $garmentWhere);

        $sql = "
            (SELECT 
                p.product_id as id,
                p.product_code as sku,
                p.product_name as name,
                'jewellery' as type,
                p.sales_price as price,
                p.rent_price as rent_price,
                p.product_image as main_image,
                COALESCE(s.name, j.categories_name, 'Jewellery') as category_name
            FROM product p
            LEFT JOIN subcat1 s ON p.subcat_id = s.subcat_id
            LEFT JOIN jewel_subcat j ON p.categories_id = j.subcat_id
            WHERE $jewelWhereSql
            ORDER BY p.product_id DESC
            LIMIT $limit)
            UNION ALL
            (SELECT 
                g.gproduct_id as id,
                g.gproduct_code as sku,
                g.gproduct_name as name,
                'garments' as type,
                g.sales_price as price,
                g.rent_price as rent_price,
                g.gproduct_image as main_image,
                'Bridal Apparel' as category_name
            FROM garment_product g
            WHERE $garmentWhereSql
            ORDER BY g.gproduct_id DESC
            LIMIT $limit)
            LIMIT $limit";

        $res = mysqli_query($this->db, $sql);
        $products = [];

        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $pid = (int)$row['id'];
                $sku = trim($row['sku']);
                $isGarment = ($row['type'] === 'garments');

                // Get images for this product
                $imgWhere = $isGarment ? "(gproduct_id = $pid OR pro_code = '$sku')" : "(product_id = $pid OR pro_code = '$sku')";
                $imgSql = "SELECT id, img_name, rank FROM product_images_new 
                           WHERE $imgWhere AND img_name != '' 
                           ORDER BY rank ASC, id ASC LIMIT 8";
                $imgRes = mysqli_query($this->db, $imgSql);
                $photos = [];

                if ($imgRes) {
                    while ($ir = mysqli_fetch_assoc($imgRes)) {
                        $rawImg = trim($ir['img_name']);
                        $cleanRel = ltrim(preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $rawImg), '/');
                        $parts = explode('/', $cleanRel);
                        $encoded = array_map('rawurlencode', $parts);
                        $serverUrl = 'https://srishringarr.com/yn/uploads/' . implode('/', $encoded);
                        $photos[] = [
                            'id' => (int)$ir['id'],
                            'url' => $serverUrl,
                            'file_name' => basename($rawImg)
                        ];
                    }
                }

                if (empty($photos) && !empty($row['main_image'])) {
                    $cleanRel = ltrim(preg_replace('#^(\.\./|\./)*(yn/uploads/|uploads/)?#i', '', $row['main_image']), '/');
                    $photos[] = [
                        'id' => 0,
                        'url' => 'https://srishringarr.com/yn/uploads/' . $cleanRel,
                        'file_name' => basename($row['main_image'])
                    ];
                }

                $primaryImage = !empty($photos[0]['url']) ? $photos[0]['url'] : 'assets/default-product.jpg';

                // Check video status in product_generated_videos
                $escSku = mysqli_real_escape_string($this->db, $sku);
                $vidSql = "SELECT id, video_url, video_filename, duration, preset, file_size_mb, created_at 
                           FROM product_generated_videos 
                           WHERE product_sku = '$escSku' 
                           ORDER BY id DESC LIMIT 1";
                $vidRes = mysqli_query($this->db, $vidSql);
                $videoData = null;
                if ($vidRes && $vr = mysqli_fetch_assoc($vidRes)) {
                    $rawUrl = $vr['video_url'];
                    $cleanUrl = ltrim(preg_replace('#^(\.\./|\./)*#', '', $rawUrl), '/');
                    $vr['raw_url'] = $cleanUrl;
                    $vr['video_url'] = '../' . $cleanUrl;
                    $videoData = $vr;
                }

                // Filter by hasVideo if requested
                if ($hasVideo === 'yes' && !$videoData) continue;
                if ($hasVideo === 'no' && $videoData) continue;

                $row['primary_image'] = $primaryImage;
                $row['photos'] = $photos;
                $row['photos_count'] = count($photos);
                $row['has_video'] = ($videoData !== null);
                $row['video'] = $videoData;

                $products[] = $row;
            }
        }

        return $products;
    }

    /**
     * Call Gemini API to calculate motion parameters & choreography for a product
     */
    public function planProductMotion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $sku = trim($input['sku'] ?? 'PROD');
        $title = trim($input['title'] ?? 'Bridal Product');
        $category = trim($input['category'] ?? 'Jewellery');
        $duration = (int)($input['duration'] ?? 30);
        if ($duration < 4) $duration = 4;
        if ($duration > 60) $duration = 30;
        $preset = trim($input['preset'] ?? 'luxury_showcase');

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $geminiKey = $secrets['GEMINI_API_KEY'] ?? '';

        $blueprint = [
            'sku' => $sku,
            'duration' => $duration,
            'preset' => $preset,
            'camera_orbit_speed' => 1.0,
            'zoom_amplitude' => 1.15,
            'glint_burst_intensity' => 0.85,
            'reflection_intensity' => 0.45,
            'lighting_warmth' => '#fef08a',
            'ambient_particles' => 'gold_bokeh',
            'brand_watermark' => 'SRI SHRINGARR'
        ];

        if (!empty($geminiKey)) {
            $prompt = "You are a luxury bridal jewelry & haute couture cinematic video director for Sri Shringarr.
Analyze product SKU {$sku}: {$title} ({$category}).
Design optimal motion video generation parameters for a {$duration}-second video clip using style: {$preset}.
Respond with valid JSON only:
{
  \"camera_orbit_speed\": 1.0,
  \"zoom_amplitude\": 1.2,
  \"glint_burst_intensity\": 0.8,
  \"lighting_warmth\": \"#fef08a\",
  \"ambient_particles\": \"gold_bokeh\",
  \"cinematic_title\": \"{$title}\",
  \"gemini_note\": \"Brief 1-sentence camera direction note\"
}";

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent?key=" . $geminiKey;
            $payload = json_encode([
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['responseMimeType' => 'application/json']
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $decoded = json_decode($response, true);
                $rawText = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $parsed = json_decode($rawText, true);
                if (is_array($parsed)) {
                    $blueprint = array_merge($blueprint, $parsed);
                }
            }
        }

        $this->json(['success' => true, 'blueprint' => $blueprint]);
    }

    /**
     * Save generated video and link to product in database
     */
    public function saveVideo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $productSku = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['sku'] ?? 'PROD');
        $productName = trim($_POST['product_name'] ?? '');
        $productType = trim($_POST['type'] ?? 'jewellery');
        $duration = (int)($_POST['duration'] ?? 15);
        $preset = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['preset'] ?? 'turntable');
        $aspectRatio = preg_replace('/[^0-9:]/', '', $_POST['aspect_ratio'] ?? '9:16');

        $fileName = 'prod_' . $productSku . '_' . $preset . '_' . $duration . 's_' . time() . '.webm';
        $targetPath = $this->videoStorageDir . '/' . $fileName;

        $saved = false;
        if (isset($_FILES['video_blob']) && $_FILES['video_blob']['error'] === UPLOAD_ERR_OK) {
            $saved = move_uploaded_file($_FILES['video_blob']['tmp_name'], $targetPath);
        } else {
            $rawBody = file_get_contents('php://input');
            if (!empty($rawBody)) {
                $parsed = json_decode($rawBody, true);
                if (!empty($parsed['base64_video'])) {
                    $b64 = $parsed['base64_video'];
                    if (preg_match('#^data:video/\w+;base64,#i', $b64, $match)) {
                        $b64 = substr($b64, strlen($match[0]));
                    }
                    $binary = base64_decode($b64);
                    $saved = file_put_contents($targetPath, $binary) !== false;
                }
            }
        }

        if ($saved && file_exists($targetPath)) {
            $fileSizeBytes = filesize($targetPath);
            $fileSizeMb = round($fileSizeBytes / (1024 * 1024), 2);
            $videoUrl = 'yn/uploads/product_videos/' . $fileName;

            $escSku = mysqli_real_escape_string($this->db, $productSku);
            $escName = mysqli_real_escape_string($this->db, $productName);
            $escType = mysqli_real_escape_string($this->db, $productType);
            $escFile = mysqli_real_escape_string($this->db, $fileName);
            $escUrl = mysqli_real_escape_string($this->db, $videoUrl);
            $escPreset = mysqli_real_escape_string($this->db, $preset);
            $escAr = mysqli_real_escape_string($this->db, $aspectRatio);

            $insertSql = "INSERT INTO product_generated_videos 
                          (product_id, product_type, product_sku, product_name, video_filename, video_url, file_size_mb, duration, preset, aspect_ratio, created_at)
                          VALUES ($productId, '$escType', '$escSku', '$escName', '$escFile', '$escUrl', $fileSizeMb, $duration, '$escPreset', '$escAr', NOW())";
            mysqli_query($this->db, $insertSql);
            $insertId = mysqli_insert_id($this->db);

            $this->json([
                'success' => true,
                'id' => $insertId,
                'sku' => $productSku,
                'file_name' => $fileName,
                'file_size_mb' => $fileSizeMb,
                'duration' => $duration,
                'preset' => $preset,
                'url' => '../' . $videoUrl,
                'raw_url' => $videoUrl,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return;
        }

        $this->json(['error' => 'Failed to save generated video file'], 500);
    }

    /**
     * CORS Proxy for remote images to avoid tainted canvas errors in browser
     */
    public function proxyImage()
    {
        $url = $_GET['url'] ?? '';
        if (empty($url)) {
            http_response_code(400);
            echo 'Missing URL';
            exit;
        }

        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');
        $allowedHosts = ['srishringarr.com', 'www.srishringarr.com', 'yosshitaneha.com', 'localhost', '127.0.0.1'];

        if (!in_array($host, $allowedHosts) && !str_ends_with($host, 'srishringarr.com')) {
            http_response_code(403);
            echo 'Host not allowed';
            exit;
        }

        $localPath = null;
        if (isset($parsed['path'])) {
            $path = $parsed['path'];
            if (strpos($path, 'uploads/') !== false) {
                $subPath = substr($path, strpos($path, 'uploads/'));
                $checkLocations = [
                    dirname(__DIR__, 2) . '/yn/' . $subPath,
                    dirname(__DIR__, 2) . '/' . $subPath,
                    __DIR__ . '/../../yn/' . $subPath
                ];
                foreach ($checkLocations as $loc) {
                    if (file_exists($loc)) {
                        $localPath = $loc;
                        break;
                    }
                }
            }
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Cache-Control: public, max-age=86400');

        if ($localPath && file_exists($localPath)) {
            $mime = mime_content_type($localPath) ?: 'image/jpeg';
            header("Content-Type: $mime");
            readfile($localPath);
            exit;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $data = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($data) {
            header("Content-Type: " . ($contentType ?: 'image/jpeg'));
            echo $data;
        } else {
            http_response_code(404);
            echo 'Image not found';
        }
        exit;
    }

    /**
     * Start Google Veo 3.1 AI Video Generation (Long-Running Operation)
     */
    public function startVeoGeneration()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $sku = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['sku'] ?? 'PROD');
        $name = trim($input['name'] ?? 'Luxury Bridal Jewellery');
        $duration = (int)($input['duration'] ?? 6);
        if ($duration > 8) {
            $this->json(['error' => 'Google Veo 3.1 AI API limits single generative clips to 8 seconds max. For 10s to 30s full product showcases, please switch the engine dropdown to "AI Luxury Studio Engine (Up to 30s)".'], 400);
            return;
        }
        if ($duration < 4) $duration = 4;
        $preset = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['preset'] ?? 'turntable');
        $aspectRatio = in_array($input['aspect_ratio'] ?? '', ['9:16', '16:9']) ? $input['aspect_ratio'] : '9:16';
        $imageUrl = trim($input['image_url'] ?? '');

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';
        if (empty($apiKey)) {
            $this->json(['error' => 'GEMINI_API_KEY is missing in secrets.php'], 500);
            return;
        }

        // 1. Fetch Image Binary (Local or Remote)
        $imgBinary = null;
        $mimeType = 'image/jpeg';

        if (!empty($imageUrl)) {
            if (strpos($imageUrl, 'uploads/') !== false) {
                $subPath = substr($imageUrl, strpos($imageUrl, 'uploads/'));
                $checkLocations = [
                    dirname(__DIR__, 2) . '/yn/' . $subPath,
                    dirname(__DIR__, 2) . '/' . $subPath,
                    __DIR__ . '/../../yn/' . $subPath
                ];
                foreach ($checkLocations as $loc) {
                    if (file_exists($loc)) {
                        $imgBinary = file_get_contents($loc);
                        $mimeType = mime_content_type($loc) ?: 'image/jpeg';
                        break;
                    }
                }
            }

            if (!$imgBinary && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $ch = curl_init($imageUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
                $imgBinary = curl_exec($ch);
                $mimeType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
                curl_close($ch);
            }
        }

        if (!$imgBinary) {
            $this->json(['error' => 'Could not load product image for AI video generation.'], 400);
            return;
        }

        // 2. Craft Ultra-Luxury Prompt for Sri Shringarr
        $productType = trim($input['type'] ?? 'jewellery');
        $itemDesc = ($productType === 'apparel') ? 'designer luxury apparel / couture outfit' : 'luxury fine jewelry masterpiece';

        $presetDescriptions = [
            'turntable' => 'exquisite 360-degree slow turntable rotation, smooth cinematic camera orbit, continuous seamless 3D perspective showcasing intricate craftsmanship and texture',
            'macro_sparkle' => 'dramatic macro close-up glide, vibrant diamond sparkle flare, shimmering specular glints catching the light, luxury editorial finish',
            'bridal_glamour' => 'opulent bridal studio lighting, warm golden highlights, romantic slow-motion floating camera move, regal royal heritage aesthetic',
            'editorial' => 'high-fashion runway editorial commercial video, crisp studio spotlights, deep rich background, high contrast professional studio lighting'
        ];
        $stylePrompt = $presetDescriptions[$preset] ?? $presetDescriptions['turntable'];

        $prompt = "A high-end cinematic luxury commercial advertisement showcasing this exact {$itemDesc}: {$name} (SKU: {$sku}). Features {$stylePrompt}. Flawless professional commercial lighting, sharp crisp details, the product is the clear hero centered in frame, photorealistic 8k video quality, smooth stabilized motion.";

        // 3. Send to Google Veo 3.1
        $model = "veo-3.1-fast-generate-preview";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:predictLongRunning?key=" . $apiKey;

        $payload = [
            'instances' => [
                [
                    'prompt' => $prompt,
                    'image' => [
                        'bytesBase64Encoded' => base64_encode($imgBinary),
                        'mimeType' => (strpos($mimeType, 'png') !== false) ? 'image/png' : 'image/jpeg'
                    ]
                ]
            ],
            'parameters' => [
                'aspectRatio' => $aspectRatio,
                'sampleCount' => 1,
                'durationSeconds' => $duration
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($res, true);
        if ($httpCode === 200 && !empty($data['name'])) {
            $this->json([
                'success' => true,
                'operation_name' => $data['name'],
                'sku' => $sku,
                'model' => $model,
                'duration' => $duration
            ]);
            return;
        }

        $errorMsg = $data['error']['message'] ?? ('HTTP ' . $httpCode . ': ' . $res);
        $this->json(['error' => 'Veo Initiation Failed: ' . $errorMsg], 500);
    }

    /**
     * Poll Google Veo Long-Running Operation and save MP4 upon completion
     */
    public function checkVeoOperation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $opName = trim($input['operation_name'] ?? '');
        $sku = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['sku'] ?? 'PROD');
        $productId = (int)($input['product_id'] ?? 0);
        $productName = trim($input['name'] ?? 'Product');
        $productType = trim($input['type'] ?? 'jewellery');
        $duration = (int)($input['duration'] ?? 6);
        $preset = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['preset'] ?? 'veo_cinematic');
        $aspectRatio = preg_replace('/[^0-9:]/', '', $input['aspect_ratio'] ?? '9:16');

        if (empty($opName)) {
            $this->json(['error' => 'Missing operation name'], 400);
            return;
        }

        $secrets = include(__DIR__ . '/../Config/secrets.php');
        $apiKey = $secrets['GEMINI_API_KEY'] ?? '';

        $url = "https://generativelanguage.googleapis.com/v1beta/{$opName}?key=" . $apiKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($res, true);
        if (!$data) {
            $this->json(['done' => false, 'status' => 'polling']);
            return;
        }

        if (!empty($data['error'])) {
            $this->json(['done' => true, 'error' => $data['error']['message'] ?? 'Veo Generation Error'], 500);
            return;
        }

        if (empty($data['done'])) {
            $this->json(['done' => false, 'status' => 'rendering']);
            return;
        }

        // Operation is done! Extract video download URI
        $samples = $data['response']['generateVideoResponse']['generatedSamples'] ?? [];
        $videoUri = $samples[0]['video']['uri'] ?? '';

        if (empty($videoUri)) {
            $this->json(['done' => true, 'error' => 'Veo completed but returned no video URI.'], 500);
            return;
        }

        // Download Video
        $downloadUrl = $videoUri . (str_contains($videoUri, '?') ? '&' : '?') . 'key=' . $apiKey;
        $ch2 = curl_init($downloadUrl);
        curl_setopt_array($ch2, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $videoBinary = curl_exec($ch2);
        $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        if ($httpCode2 !== 200 || empty($videoBinary)) {
            $this->json(['done' => true, 'error' => 'Failed to download generated Veo MP4 video file.'], 500);
            return;
        }

        $fileName = 'prod_' . $sku . '_veo_' . time() . '.mp4';
        $targetPath = $this->videoStorageDir . '/' . $fileName;

        if (file_put_contents($targetPath, $videoBinary) === false) {
            $this->json(['done' => true, 'error' => 'Failed to write Veo video to disk.'], 500);
            return;
        }

        $fileSizeBytes = filesize($targetPath);
        $fileSizeMb = round($fileSizeBytes / (1024 * 1024), 2);
        $videoUrl = 'yn/uploads/product_videos/' . $fileName;

        $escSku = mysqli_real_escape_string($this->db, $sku);
        $escName = mysqli_real_escape_string($this->db, $productName);
        $escType = mysqli_real_escape_string($this->db, $productType);
        $escFile = mysqli_real_escape_string($this->db, $fileName);
        $escUrl = mysqli_real_escape_string($this->db, $videoUrl);
        $escPreset = mysqli_real_escape_string($this->db, $preset);
        $escAr = mysqli_real_escape_string($this->db, $aspectRatio);

        $insertSql = "INSERT INTO product_generated_videos 
                      (product_id, product_type, product_sku, product_name, video_filename, video_url, file_size_mb, duration, preset, aspect_ratio, created_at)
                      VALUES ($productId, '$escType', '$escSku', '$escName', '$escFile', '$escUrl', $fileSizeMb, $duration, '$escPreset', '$escAr', NOW())";
        mysqli_query($this->db, $insertSql);
        $insertId = mysqli_insert_id($this->db);

        $this->json([
            'done' => true,
            'success' => true,
            'id' => $insertId,
            'sku' => $sku,
            'file_name' => $fileName,
            'file_size_mb' => $fileSizeMb,
            'duration' => $duration,
            'preset' => $preset,
            'url' => '../' . $videoUrl,
            'raw_url' => $videoUrl,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
