<?php

require_once "AppController.php";
require_once __DIR__ . '/../repository/CategoryRepository.php';
require_once __DIR__ . '/../attribute/AllowedMethods.php';

class CategoryController extends AppController {

    private $categoryRepository;

    public function __construct() {
        $this->categoryRepository = new CategoryRepository();
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function add() {
        $this->checkSession();

        if (!$this->isPost()) {
            return $this->render('add-category');
        }

        $name = trim($_POST['category_name'] ?? '');
        $color = $_POST['category_color'] ?? '#3b82f6';
        $icon = trim($_POST['category_icon'] ?? '');

        if (empty($name)) {
            return $this->render('add-category', ['messages' => 'Category name is required']);
        }

        if (strlen($icon) > 4) {
             return $this->render('add-category', ['messages' => 'Icon should be a short emoji or character']);
        }

        if ($this->categoryRepository->categoryExists($_SESSION['user_id'], $name)) {
            return $this->render('add-category', ['messages' => 'Category already exists']);
        }

        try {
            $this->categoryRepository->addCategory($_SESSION['user_id'], $name, $color, $icon);
            return $this->render('add-category', ['success' => 'Category created successfully!']);
        } catch (Exception $e) {
            return $this->render('add-category', ['messages' => 'Failed to create category']);
        }
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }
}