<?php
namespace Controllers;

use Core\Controller;
use Models\WooProductModel;

class WooproductController extends Controller {
    
    private $wooModel;
    
    public function __construct() {
        $this->wooModel = new WooProductModel();
    }
    
    public function index() {
        if (!$this->wooModel->isConnected()) {
            return $this->view('woo_products/connection_error');
        }

        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;

        $products = $this->wooModel->getProducts([
            'search' => $search,
            'page' => $page,
            'limit' => $limit
        ]);

        $totalCount = $this->wooModel->getTotalCount($search);
        $totalPages = ceil($totalCount / $limit);

        $this->view('woo_products/index', [
            'products' => $products,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ]);
    }

    public function export() {
        if (!$this->wooModel->isConnected()) {
            die("Database not connected");
        }

        $params = ['limit' => 5000];
        
        // Handle SKU file upload if present (Supports CSV and Excel)
        if (isset($_FILES['sku_file']) && $_FILES['sku_file']['error'] === UPLOAD_ERR_OK) {
            $skus = [];
            $ext = pathinfo($_FILES['sku_file']['name'], PATHINFO_EXTENSION);

            if (in_array($ext, ['xlsx', 'xls'])) {
                // Read from Excel
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['sku_file']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                foreach ($rows as $row) {
                    if (!empty($row[0])) $skus[] = trim($row[0]);
                }
            } else {
                // Read from CSV
                $handle = fopen($_FILES['sku_file']['tmp_name'], "r");
                while (($row = fgetcsv($handle, 0, ",", "\"", "\\")) !== FALSE) {
                    if (!empty($row[0])) $skus[] = trim($row[0]);
                }
                fclose($handle);
            }
            
            if (!empty($skus)) $params['skus'] = $skus;
        }

        $products = $this->wooModel->getProducts($params); 

        if (ob_get_level()) ob_end_clean();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['SKU', 'Name', 'Description', 'Type', 'Category_ID', 'Subcat_ID', 'S_Price', 'Rental_Price', 'Deposit', 'Images'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue([$i + 1, 1], $header);
        }

        // Data
        $rowNum = 2;
        foreach ($products as $p) {
            $sku = $p['sku'] ?: 'REMOTE-' . $p['ID'];
            $catName = $p['categories'] ? trim(explode(',', $p['categories'])[0]) : '';
            $price = $p['price'] ?: 0;
            $rental = floor($price * 0.3); // Guessing 30%
            $deposit = $price; // Guessing 100%

            $sheet->setCellValue([1, $rowNum], $sku);
            $sheet->setCellValue([2, $rowNum], $p['name']);
            $sheet->setCellValue([3, $rowNum], strip_tags($p['description'] ?? ''));
            $sheet->setCellValue([4, $rowNum], 'jewellery');
            $sheet->setCellValue([5, $rowNum], $catName);
            $sheet->setCellValue([6, $rowNum], '');
            $sheet->setCellValue([7, $rowNum], $price);
            $sheet->setCellValue([8, $rowNum], $rental);
            $sheet->setCellValue([9, $rowNum], $deposit);
            $sheet->setCellValue([10, $rowNum], $p['image_url'] ?? '');
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="yn_woo_export_'.date('YmdHis').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
