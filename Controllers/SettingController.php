<?php
namespace Controllers;

use Core\Controller;
use Core\Database;
use Models\ProductModel;
use Controllers\AllInOneWriterController;

class SettingController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection('con');
    }

    /**
     * Default settings dashboard - routes directly to AI Writer settings
     */
    public function index()
    {
        $this->aiwriter();
    }

    /**
     * Dedicated AI Writer Settings Page
     */
    public function aiwriter()
    {
        $settings = AllInOneWriterController::getAiWriterSettings();

        // Check configured API keys
        $secretsFile = __DIR__ . '/../Config/secrets.php';
        $hasGemini = false;
        $hasOpenAi = false;
        if (file_exists($secretsFile)) {
            $sec = include($secretsFile);
            $hasGemini = !empty($sec['GEMINI_API_KEY']);
            $hasOpenAi = !empty($sec['OPENAI_API_KEY']);
        }

        $this->view('settings/ai_writer', [
            'settings' => $settings,
            'hasGemini' => $hasGemini,
            'hasOpenAi' => $hasOpenAi,
            'pageTitle' => 'AI Writer Settings'
        ]);
    }

    /**
     * Save AI Writer settings via AJAX or form POST
     */
    public function saveAiWriter()
    {
        $raw = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $mode = trim($raw['image_analysis_mode'] ?? 'default');
        if (!in_array($mode, ['default', 'multi'])) {
            $mode = 'default';
        }

        $maxImages = (int)($raw['max_images'] ?? 10);
        $maxImages = max(1, min(10, $maxImages));

        $includeContent = !empty($raw['include_content_analysis']);

        $updated = AllInOneWriterController::saveAiWriterSettings([
            'image_analysis_mode' => $mode,
            'max_images' => $maxImages,
            'include_content_analysis' => $includeContent
        ]);

        if (!$updated) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Failed to write configuration file.'], 500);
            }
            $this->redirect('index.php?controller=setting&action=aiwriter&error=Failed+to+save+settings');
        }

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'message' => 'AI Writer settings saved successfully.',
                'settings' => $updated
            ]);
        }

        $this->redirect('index.php?controller=setting&action=aiwriter&success=1');
    }

    /**
     * Inspect a SKU's images to test default vs multi-image analysis
     */
    public function inspectSku()
    {
        $sku = trim($_GET['sku'] ?? ($_POST['sku'] ?? ''));
        if (empty($sku)) {
            $this->json(['success' => false, 'error' => 'Please provide a SKU code to inspect.'], 400);
        }

        if (!$this->db) {
            $this->json(['success' => false, 'error' => 'Database connection unavailable.'], 500);
        }

        $escSku = mysqli_real_escape_string($this->db, $sku);

        // Search in Jewellery
        $jquery = "SELECT product_id as id, product_name as name, product_code as code, product_image as main_img, 'jewellery' as type FROM product WHERE product_code = '$escSku' LIMIT 1";
        $jres = mysqli_query($this->db, $jquery);
        $prod = $jres ? mysqli_fetch_assoc($jres) : null;

        if (!$prod) {
            // Search in Garments
            $gquery = "SELECT gproduct_id as id, gproduct_name as name, gproduct_code as code, gproduct_image as main_img, 'garment' as type FROM garment_product WHERE gproduct_code = '$escSku' LIMIT 1";
            $gres = mysqli_query($this->db, $gquery);
            $prod = $gres ? mysqli_fetch_assoc($gres) : null;
        }

        if (!$prod) {
            $this->json(['success' => false, 'error' => "No product found with SKU '$sku'."], 404);
        }

        $productModel = new ProductModel();
        $images = $productModel->getProductImages((int)$prod['id'], $prod['type']);
        $settings = AllInOneWriterController::getAiWriterSettings();
        $maxImages = (int)($settings['max_images'] ?? 10);

        $resolvedList = [];
        $index = 0;
        foreach ($images as $img) {
            $raw = $img['img_name'] ?? '';
            if (empty($raw)) continue;

            $index++;
            $clean = ltrim(str_replace(['../../yn/uploads', '../yn/uploads', '/yn/uploads', 'yn/uploads/', 'uploads/'], '', $raw), '/');
            $local = 'C:/xampp/htdocs/ss/yn/uploads/' . $clean;
            $existsLocal = file_exists($local);
            $url = $existsLocal ? '../yn/uploads/' . $clean : 'https://srishringarr.com/yn/uploads/' . $clean;

            $isDefaultModeIncluded = ($index === 1);
            $isMultiModeIncluded = ($index <= $maxImages);

            $resolvedList[] = [
                'index' => $index,
                'raw_name' => $raw,
                'url' => $url,
                'is_main' => ($raw === $prod['main_img'] || $index === 1),
                'local_exists' => $existsLocal,
                'default_mode_analyzed' => $isDefaultModeIncluded,
                'multi_mode_analyzed' => $isMultiModeIncluded
            ];
        }

        $this->json([
            'success' => true,
            'sku' => $prod['code'],
            'name' => $prod['name'],
            'type' => $prod['type'],
            'total_images_found' => count($resolvedList),
            'default_analyzed_count' => min(1, count($resolvedList)),
            'multi_analyzed_count' => min($maxImages, count($resolvedList)),
            'max_allowed' => $maxImages,
            'active_mode' => $settings['image_analysis_mode'] ?? 'default',
            'images' => $resolvedList
        ]);
    }

    private function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
            || isset($_GET['ajax']);
    }
}
