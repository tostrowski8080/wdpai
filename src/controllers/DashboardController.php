<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';

class DashboardController extends AppController {
    
    public function __construct(){
    }

    public function index(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        //TODO fetch stuff from db

        return $this->render('dashboard', [
            'user_name' => $_SESSION['user_name'], 
            'activities' => $activities
        ]);
    }
}