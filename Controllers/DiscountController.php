<?php
namespace Controllers;

use Models\DiscountModel;

class DiscountController {
    private $model;

    public function __construct() {
        $this->model = new DiscountModel();
    }

    public function index() {
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add_rule':
                        $this->handleAddRule();
                        break;
                    case 'update_rule':
                        $this->handleUpdateRule();
                        break;
                    case 'update_settings':
                        $this->handleUpdateSettings();
                        break;
                }
            }
        }

        // Handle delete
        if (isset($_GET['delete_id'])) {
            $this->model->deleteRule($_GET['delete_id']);
            header('Location: index.php?controller=discount');
            exit;
        }

        $rules = $this->model->getRules();
        $settings = $this->model->getSettings();
        $pageTitle = 'Discounts';
        
        include 'Views/discount/index.php';
    }

    private function handleAddRule() {
        $data = [
            'scope' => $_POST['scope'],
            'target' => $_POST['targets_json'] ?? '',
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'weight' => $_POST['weight'] ?? 0,
            'threshold' => $_POST['threshold'] ?? null,
            'threshold_max' => $_POST['threshold_max'] ?? null
        ];

        if ($data['scope'] === 'global') {
            $data['target'] = 'all';
        }

        $this->model->addRule($data);
        header('Location: index.php?controller=discount&status=saved');
        exit;
    }

    private function handleUpdateRule() {
        $data = [
            'id' => $_POST['rule_id'],
            'scope' => $_POST['scope'],
            'target' => $_POST['targets_json'] ?? '',
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'weight' => $_POST['weight'] ?? 0,
            'threshold' => $_POST['threshold'] ?? null,
            'threshold_max' => $_POST['threshold_max'] ?? null
        ];

        if ($data['scope'] === 'global') {
            $data['target'] = 'all';
        }

        $this->model->updateRule($data);
        header('Location: index.php?controller=discount&status=updated');
        exit;
    }

    private function handleUpdateSettings() {
        $settings = [
            'da_badge_position',
            'da_show_on_shop',
            'da_show_on_archive',
            'da_show_on_single',
            'da_show_on_related',
            'da_show_on_search',
            'da_show_on_cart'
        ];

        foreach ($settings as $key) {
            $value = isset($_POST[$key]) ? '1' : '0';
            if ($key === 'da_badge_position') {
                $value = $_POST[$key];
            }
            $this->model->updateSetting($key, $value);
        }

        header('Location: index.php?controller=discount&status=settings_saved');
        exit;
    }

    public function search() {
        $type = $_GET['type'] ?? 'product';
        $q = $_GET['q'] ?? '';
        $results = $this->model->searchTargets($type, $q);
        header('Content-Type: application/json');
        echo json_encode(['results' => $results]);
        exit;
    }
}
