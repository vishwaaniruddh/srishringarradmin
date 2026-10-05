<?php
namespace Controllers;

use Core\Controller;
use Models\WooProductModel;

class WooproductController extends Controller
{
    private $wooModel;

    public function __construct()
    {
        $this->wooModel = new WooProductModel();
    }

    public function index()
    {
        if (!$this->wooModel->isConnected()) {
            return $this->view('woo_products/connection_error');
        }

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $categoryId = (int)($_GET['category_id'] ?? 0);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;

        $products = $this->wooModel->getProducts([
            'search' => $search,
            'status' => $status,
            'category_id' => $categoryId,
            'page' => $page,
            'limit' => $limit
        ]);

        $totalCount = $this->wooModel->getTotalCount($search, $status, $categoryId);
        $totalPages = max(1, (int)ceil($totalCount / $limit));
        $stats = $this->wooModel->getStats();
        $categories = $this->wooModel->getCategories();

        $this->view('woo_products/index', [
            'products' => $products,
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'stats' => $stats,
            'categories' => $categories
        ]);
    }

    public function export()
    {
        if (!$this->wooModel->isConnected()) {
            die("Database not connected");
        }

        $params = ['limit' => 10000];
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $categoryId = (int)($_GET['category_id'] ?? 0);

        if (!empty($search)) $params['search'] = $search;
        if (!empty($status)) $params['status'] = $status;
        if ($categoryId > 0) $params['category_id'] = $categoryId;

        // Handle SKU file upload if present (Supports CSV and Excel)
        if (isset($_FILES['sku_file']) && $_FILES['sku_file']['error'] === UPLOAD_ERR_OK) {
            $skus = [];
            $ext = strtolower(pathinfo($_FILES['sku_file']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, ['xlsx', 'xls'])) {
                // Read from Excel
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['sku_file']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                foreach ($rows as $row) {
                    if (!empty($row[0])) {
                        $skus[] = trim($row[0]);
                    }
                }
            } else {
                // Read from CSV
                $handle = fopen($_FILES['sku_file']['tmp_name'], "r");
                while (($row = fgetcsv($handle, 0, ",", "\"", "\\")) !== FALSE) {
                    if (!empty($row[0])) {
                        $skus[] = trim($row[0]);
                    }
                }
                fclose($handle);
            }

            if (!empty($skus)) {
                $params['skus'] = $skus;
            }
        }

        $products = $this->wooModel->getProducts($params);

        if (ob_get_level()) {
            ob_end_clean();
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['ID', 'SKU', 'Name', 'Description', 'Categories', 'Price', 'Sale Price', 'Stock', 'Status', 'Storefront URL', 'Image URL'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue([$i + 1, 1], $header);
        }

        // Data
        $rowNum = 2;
        foreach ($products as $p) {
            $sku = $p['sku'] ?: 'YN-' . $p['id'];
            $storefrontUrl = 'https://yosshitaneha.com/product/' . ($p['slug'] ?? '') . '/';

            $sheet->setCellValue([1, $rowNum], $p['id']);
            $sheet->setCellValue([2, $rowNum], $sku);
            $sheet->setCellValue([3, $rowNum], $p['name']);
            $sheet->setCellValue([4, $rowNum], strip_tags($p['description'] ?? ''));
            $sheet->setCellValue([5, $rowNum], $p['categories'] ?? '');
            $sheet->setCellValue([6, $rowNum], (float)($p['price'] ?? 0));
            $sheet->setCellValue([7, $rowNum], !empty($p['sale_price']) ? (float)$p['sale_price'] : '');
            $sheet->setCellValue([8, $rowNum], (int)($p['stock'] ?? 0));
            $sheet->setCellValue([9, $rowNum], ucfirst($p['status'] ?? 'published'));
            $sheet->setCellValue([10, $rowNum], $storefrontUrl);
            $sheet->setCellValue([11, $rowNum], $p['image_url'] ?? '');
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="yn_products_export_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
