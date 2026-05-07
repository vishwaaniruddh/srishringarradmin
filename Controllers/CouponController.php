<?php
namespace Controllers;

use Models\CouponModel;

class CouponController {
    private $model;

    public function __construct() {
        $this->model = new CouponModel();
    }

    public function index() {
        $coupons = $this->model->getAll();
        $pageTitle = 'Coupons';
        include 'Views/coupon/index.php';
    }

    public function add() {
        $pageTitle = 'Add New Coupon';
        $coupon = null; // For the shared form
        include 'Views/coupon/form.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=coupon');
            exit;
        }
        $coupon = $this->model->getById($id);
        if (!$coupon) {
            header('Location: index.php?controller=coupon');
            exit;
        }
        $pageTitle = 'Edit Coupon: ' . $coupon['code'];
        include 'Views/coupon/form.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'code' => $_POST['code'],
                'description' => $_POST['description'] ?? '',
                'discount_type' => $_POST['discount_type'],
                'coupon_amount' => $_POST['coupon_amount'],
                'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                'usage_limit' => !empty($_POST['usage_limit']) ? $_POST['usage_limit'] : null,
                'usage_limit_per_user' => !empty($_POST['usage_limit_per_user']) ? $_POST['usage_limit_per_user'] : null,
                'minimum_amount' => !empty($_POST['minimum_amount']) ? $_POST['minimum_amount'] : null,
                'maximum_amount' => !empty($_POST['maximum_amount']) ? $_POST['maximum_amount'] : null,
                'individual_use' => isset($_POST['individual_use']) ? 1 : 0,
                'exclude_sale_items' => isset($_POST['exclude_sale_items']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active'
            ];

            if ($id) {
                $this->model->update($id, $data);
                $msg = 'Coupon updated successfully';
            } else {
                $this->model->create($data);
                $msg = 'Coupon created successfully';
            }

            header('Location: index.php?controller=coupon&success=' . urlencode($msg));
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->delete($id);
        }
        header('Location: index.php?controller=coupon&success=Coupon deleted successfully');
        exit;
    }
}
