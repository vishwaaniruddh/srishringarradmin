<?php
namespace Controllers;

use Core\Controller;
use Models\StatsModel;

class DashboardController extends Controller {
    public function index() {
        $this->view('dashboard');
    }
}
