<?php
namespace Controllers;

use Models\NewsletterModel;

class NewsletterController {
    private $model;

    public function __construct() {
        $this->model = new NewsletterModel();
    }

    public function index() {
        if (isset($_GET['delete_id'])) {
            $this->model->deleteSubscriber($_GET['delete_id']);
            header('Location: index.php?controller=newsletter&status=deleted');
            exit;
        }

        $totalSubscribers = $this->model->getTotalSubscribers();
        $subscribers = $this->model->getSubscribers();
        
        $pageTitle = 'Newsletter';
        include 'Views/newsletter/index.php';
    }

    public function search() {
        $q = $_GET['q'] ?? '';
        $results = $this->model->searchSubscribers($q);
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
}
