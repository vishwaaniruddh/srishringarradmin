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
            $uploadDir = '../../uploads/categories/';
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
}
