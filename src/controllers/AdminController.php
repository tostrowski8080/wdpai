<?php

require_once 'AppController.php';
require_once __DIR__ .'/../repository/UserRepository.php';

class AdminController extends AppController {

    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function users() {
        $this->checkAdmin();
        $users = $this->userRepository->getUsers();
        $this->render('admin-users', ['users' => $users]);
    }

    public function updateRole() {
        $this->checkAdmin();

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            $this->userRepository->updateUserRole((int)$decoded['id'], $decoded['newRole']);

            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['message' => 'Role updated']);
        }
    }

    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: /dashboard");
            exit();
        }
    }
}