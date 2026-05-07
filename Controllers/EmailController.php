<?php
namespace Controllers;

use Models\EmailModel;

class EmailController {
    private $model;

    public function __construct() {
        $this->model = new EmailModel();
        $this->model->createTable();
    }

    public function index() {
        $settings = $this->model->getSettings();
        include 'Views/emails/index.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST['settings'] ?? [];
            $this->model->updateSettings($data);
            header('Location: index.php?controller=email&success=1');
            exit;
        }
    }
}
