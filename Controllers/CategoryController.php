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
        $result = $this->categoryModel->updateCategory($type, $id, $_POST);
        
        header('Location: index.php?controller=category&action=index&' . ($result ? 'success=Updated' : 'error=Failed'));
    }
}
