<?php
namespace Controllers;

use Core\Controller;
use Models\CategoryModel;

class CategoryController extends Controller {
    
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }
    
    public function index() {
        $this->view('categories/index', [
            'jewelCat' => $this->categoryModel->getJewelCategories(),
            'jewelSub' => $this->categoryModel->getJewelSubcategories(),
            'garmentCat' => $this->categoryModel->getGarmentCategories(),
            'garmentSub' => $this->categoryModel->getGarmentSubcategories()
        ]);
    }
    
    public function add() {
        $type = $_GET['type'] ?? 'jewel_cat';
        $parents = [];
        if ($type === 'jewel_sub') $parents = $this->categoryModel->getJewelCategories();
        if ($type === 'garment_sub') $parents = $this->categoryModel->getGarmentCategories();
        
        $this->view('categories/add', ['type' => $type, 'parents' => $parents]);
    }
    
    public function store() {
        $type = $_POST['type'];
        $result = false;
        if ($type === 'jewel_cat') $result = $this->categoryModel->saveJewelCategory($_POST);
        elseif ($type === 'jewel_sub') $result = $this->categoryModel->saveJewelSub($_POST);
        elseif ($type === 'garment_cat') $result = $this->categoryModel->saveGarmentCategory($_POST);
        elseif ($type === 'garment_sub') $result = $this->categoryModel->saveGarmentSub($_POST);
        
        header('Location: index.php?controller=category&action=index&' . ($result ? 'success=Created' : 'error=Failed'));
    }
    public function edit() {
        $type = $_GET['type'];
        $id = $_GET['id'];
        $category = $this->categoryModel->getCategory($type, $id);
        
        $parents = [];
        if ($type === 'jewel_sub') $parents = $this->categoryModel->getJewelCategories();
        if ($type === 'garment_sub') $parents = $this->categoryModel->getGarmentCategories();
        
        $this->view('categories/edit', [
            'type' => $type,
            'id' => $id,
            'category' => $category,
            'parents' => $parents
        ]);
    }
    
    public function update() {
        $type = $_POST['type'];
        $id = $_POST['id'];
        $data = $_POST;

        // Handle Image Upload
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileTmpPath = $_FILES['category_image']['tmp_name'];
            $fileName = $_FILES['category_image']['name'];
            $fileSize = $_FILES['category_image']['size'];
            $fileType = $_FILES['category_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $allowedExtensions = ['jpg', 'gif', 'png', 'jpeg', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data['image'] = $newFileName;
                }
            }
        }

        $result = $this->categoryModel->updateCategory($type, $id, $data);
        
        header('Location: index.php?controller=category&action=index&' . ($result ? 'success=Updated' : 'error=Failed'));
    }

    public function unmapped() {
        $productModel = new \Models\ProductModel();
        $jewelCategories = $productModel->getJewelCategories();
        $garmentCategories = $productModel->getGarments();

        $con = \Core\Database::getConnection('con');
        $jewelUnmappedCount = 0;
        $garmentUnmappedCount = 0;

        if ($con) {
            $resJ = mysqli_query($con, "SELECT COUNT(*) as cnt FROM product p WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
            $jewelUnmappedCount = (int)(mysqli_fetch_assoc($resJ)['cnt'] ?? 0);

            $resG = mysqli_query($con, "SELECT COUNT(*) as cnt FROM garment_product gp WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
            $garmentUnmappedCount = (int)(mysqli_fetch_assoc($resG)['cnt'] ?? 0);
        }

        $this->view('categories/unmapped', [
            'jewelCategories' => $jewelCategories,
            'garmentCategories' => $garmentCategories,
            'jewelUnmappedCount' => $jewelUnmappedCount,
            'garmentUnmappedCount' => $garmentUnmappedCount,
            'totalUnmappedCount' => $jewelUnmappedCount + $garmentUnmappedCount
        ]);
    }

    public function getUnmappedProducts() {
        $con = \Core\Database::getConnection('con');
        if (!$con) {
            $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
            return;
        }

        $typeFilter = $_GET['type'] ?? 'all'; // 'all', 'jewellery', 'garments'
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $searchEsc = mysqli_real_escape_string($con, $search);
        $searchWhereJ = !empty($search) ? " AND (p.product_name LIKE '%$searchEsc%' OR p.product_code LIKE '%$searchEsc%')" : "";
        $searchWhereG = !empty($search) ? " AND (gp.gproduct_name LIKE '%$searchEsc%' OR gp.gproduct_code LIKE '%$searchEsc%')" : "";

        $whereJ = "WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery') $searchWhereJ";
        $whereG = "WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments') $searchWhereG";

        $items = [];
        $total = 0;

        if ($typeFilter === 'jewellery') {
            $resCount = mysqli_query($con, "SELECT COUNT(*) as total FROM product p $whereJ");
            $total = (int)(mysqli_fetch_assoc($resCount)['total'] ?? 0);

            $sql = "SELECT p.product_id as id, p.product_code as code, p.product_name as name, 'jewellery' as type, p.categories_id as category_id, p.subcat_id as subcategory_id
                    FROM product p $whereJ ORDER BY p.product_id DESC LIMIT $offset, $limit";
            $res = mysqli_query($con, $sql);
            while ($r = mysqli_fetch_assoc($res)) {
                $items[] = $r;
            }
        } elseif ($typeFilter === 'garments') {
            $resCount = mysqli_query($con, "SELECT COUNT(*) as total FROM garment_product gp $whereG");
            $total = (int)(mysqli_fetch_assoc($resCount)['total'] ?? 0);

            $sql = "SELECT gp.gproduct_id as id, gp.gproduct_code as code, gp.gproduct_name as name, 'garments' as type, gp.garment_id as category_id, gp.product_for as subcategory_id
                    FROM garment_product gp $whereG ORDER BY gp.gproduct_id DESC LIMIT $offset, $limit";
            $res = mysqli_query($con, $sql);
            while ($r = mysqli_fetch_assoc($res)) {
                $items[] = $r;
            }
        } else {
            $resCount = mysqli_query($con, "SELECT SUM(cnt) as total FROM (
                SELECT COUNT(*) as cnt FROM product p $whereJ
                UNION ALL
                SELECT COUNT(*) as cnt FROM garment_product gp $whereG
            ) as t");
            $total = (int)(mysqli_fetch_assoc($resCount)['total'] ?? 0);

            $sqlCombined = "
                (SELECT p.product_id as id, p.product_code as code, p.product_name as name, 'jewellery' as type, p.categories_id as category_id, p.subcat_id as subcategory_id
                 FROM product p $whereJ)
                UNION ALL
                (SELECT gp.gproduct_id as id, gp.gproduct_code as code, gp.gproduct_name as name, 'garments' as type, gp.garment_id as category_id, gp.product_for as subcategory_id
                 FROM garment_product gp $whereG)
                ORDER BY id DESC LIMIT $offset, $limit";
            $resComb = mysqli_query($con, $sqlCombined);
            while ($r = mysqli_fetch_assoc($resComb)) {
                $items[] = $r;
            }
        }

        $this->json([
            'success' => true,
            'total' => $total,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'items' => $items
        ]);
    }

    public function getSubcategories() {
        $catId = (int)($_GET['cat_id'] ?? 0);
        $type = $_GET['type'] ?? 'jewellery';

        $productModel = new \Models\ProductModel();
        if ($type === 'jewellery') {
            $subs = $productModel->getJewelSubcategories($catId);
        } else {
            $subs = $productModel->getGarmentSubcategories($catId);
        }

        $this->json(['success' => true, 'subcategories' => $subs]);
    }

    public function saveProductCategoryMapping() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? 'jewellery';
        $catId = (int)($_POST['category_id'] ?? 0);
        $subId = (int)($_POST['subcategory_id'] ?? 0);

        if (!$id) {
            $this->json(['success' => false, 'message' => 'Invalid Product ID'], 400);
            return;
        }

        $model = new \Models\ProductModel();
        $con = \Core\Database::getConnection('con');

        if (!$con) {
            $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
            return;
        }

        try {
            // Update primary product table columns
            if ($type === 'jewellery') {
                $stmt = mysqli_prepare($con, "UPDATE product SET categories_id = ?, subcat_id = ? WHERE product_id = ?");
                mysqli_stmt_bind_param($stmt, "iii", $catId, $subId, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $stmt = mysqli_prepare($con, "UPDATE garment_product SET garment_id = ?, product_for = ? WHERE gproduct_id = ?");
                mysqli_stmt_bind_param($stmt, "iii", $catId, $subId, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            // Save relationship in product_categories
            $model->saveProductCategories($id, $type, $catId > 0 ? [$catId] : [], $subId > 0 ? [$subId] : []);

            $this->json(['success' => true, 'message' => "Successfully assigned category for Product #$id"]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function autoFixAllUnmapped() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $con = \Core\Database::getConnection('con');
        if (!$con) {
            $this->json(['success' => false, 'message' => 'Parent DB connection failed'], 500);
            return;
        }

        $model = new \Models\ProductModel();
        $fixedJ = 0;
        $fixedG = 0;

        try {
            // Auto-fix Jewellery
            $resJ = mysqli_query($con, "SELECT p.product_id, p.product_code, p.categories_id, p.subcat_id 
                                       FROM product p 
                                       WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.product_id AND pc.product_type = 'jewellery')");
            if ($resJ) {
                while ($r = mysqli_fetch_assoc($resJ)) {
                    $pid = (int)$r['product_id'];
                    $sku = $r['product_code'];
                    $cat = (int)$r['categories_id'];
                    $sub = (int)$r['subcat_id'];

                    if ($cat <= 0 && $sub <= 0) {
                        $upperSku = strtoupper($sku);
                        if (str_starts_with($upperSku, 'BR')) {
                            $cat = 22; // BRACELET
                        } elseif (str_starts_with($upperSku, 'JU')) {
                            $cat = 15; // KAMAR PATTA
                        } elseif (str_starts_with($upperSku, 'K')) {
                            $cat = 1;  // Necklace Sets
                            $sub = 3;  // Kundan
                        } elseif (str_starts_with($upperSku, 'EAR')) {
                            $cat = 17; // Earrings
                        } else {
                            $cat = 1;  // Default Necklace Sets
                        }
                        // Update primary product table
                        mysqli_query($con, "UPDATE product SET categories_id = $cat, subcat_id = $sub WHERE product_id = $pid");
                    }

                    $model->saveProductCategories($pid, 'jewellery', $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
                    $fixedJ++;
                }
            }

            // Auto-fix Garments
            $resG = mysqli_query($con, "SELECT gp.gproduct_id, gp.gproduct_code, gp.garment_id, gp.product_for 
                                       FROM garment_product gp 
                                       WHERE NOT EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = gp.gproduct_id AND pc.product_type = 'garments')");
            if ($resG) {
                while ($r = mysqli_fetch_assoc($resG)) {
                    $gid = (int)$r['gproduct_id'];
                    $sku = $r['gproduct_code'];
                    $cat = (int)$r['garment_id'];
                    $sub = (int)$r['product_for'];

                    if ($cat <= 0 && $sub <= 0) {
                        $upperSku = strtoupper($sku);
                        if (str_starts_with($upperSku, 'LEH')) {
                            $cat = 10; // LEHENGA CHOLI
                        } else {
                            $cat = 22; // Evening Gowns / Default
                        }
                        mysqli_query($con, "UPDATE garment_product SET garment_id = $cat, product_for = $sub WHERE gproduct_id = $gid");
                    }

                    $model->saveProductCategories($gid, 'garments', $cat > 0 ? [$cat] : [], $sub > 0 ? [$sub] : []);
                    $fixedG++;
                }
            }

            $totalFixed = $fixedJ + $fixedG;
            $this->json(['success' => true, 'message' => "Successfully processed $totalFixed unmapped products ($fixedJ jewellery, $fixedG garments) and inserted category mapping records!"]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error auto-fixing categories: ' . $e->getMessage()], 500);
        }
    }
}
