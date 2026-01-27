<?php

require_once "AppController.php";
require_once __DIR__ . '/../repository/UserRepository.php';

class ProfileController extends AppController {

    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function index() {
        $this->checkSession();
        $userId = $_SESSION['user_id'];
        
        $user = $this->userRepository->getUserById($userId);
        
        return $this->render('profile', ['user' => $user]);
    }

    public function updateInfo() {
        $this->checkSession();
        if (!$this->isPost()) { header("Location: /profile"); exit(); }

        $userId = $_SESSION['user_id'];
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $user = $this->userRepository->getUserById($userId);

        if (!$this->isValidName($firstname)) {
            return $this->render('profile', [
                'user' => $user, 
                'error_info' => 'First name must have at least 2 characters and only contain letters!'
            ]);
        }

        if (!$this->isValidName($lastname)) {
            return $this->render('profile', [
                'user' => $user, 
                'error_info' => 'Last name must have at least 2 characters and only contain letters!'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('profile', [
                'user' => $user, 
                'error_info' => 'Invalid email format!'
            ]);
        }
        try {
            $this->userRepository->updateUserInfo($userId, $firstname, $lastname, $email);
            
            $_SESSION['user_name'] = $firstname;

            $user = $this->userRepository->getUserById($userId);
            
            return $this->render('profile', [
                'user' => $user, 
                'success_info' => 'Profile updated successfully.'
            ]);
        } catch (Exception $e) {
            return $this->render('profile', [
                'user' => $user, 
                'error_info' => 'Failed to update profile. Email might be taken.'
            ]);
        }
    }

    public function changePassword() {
        $this->checkSession();
        if (!$this->isPost()) { header("Location: /profile"); exit(); }

        $userId = $_SESSION['user_id'];
        $currentPwd = $_POST['current_password'] ?? '';
        $newPwd = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';

        $user = $this->userRepository->getUserById($userId);

        if (empty($currentPwd) || empty($newPwd) || empty($confirmPwd)) {
            return $this->render('profile', ['user' => $user, 'error_pwd' => 'All fields are required.']);
        }

        if (!password_verify($currentPwd, $user['password'])) {
            return $this->render('profile', ['user' => $user, 'error_pwd' => 'Current password is incorrect.']);
        }

        if (!$this->isPasswordStrong($newPwd)) {
            return $this->render('profile', [
                'user' => $user, 
                'error_pwd' => 'New password must be at least 8 characters long and contain at least 1 lowercase, uppercase, number and special character!'
            ]);
        }

        if ($newPwd !== $confirmPwd) {
            return $this->render('profile', ['user' => $user, 'error_pwd' => 'New passwords do not match.']);
        }

        $hashedPwd = password_hash($newPwd, PASSWORD_BCRYPT);
        $this->userRepository->updateUserPassword($userId, $hashedPwd);

        return $this->render('profile', [
            'user' => $user, 
            'success_pwd' => 'Password changed successfully.'
        ]);
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }

    private function isValidName($name) {
        if (mb_strlen($name) < 2) return false;
        return preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ \-]+$/u', $name);
    }

    private function isPasswordStrong($password) {
        if (strlen($password) < 8) return false;
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $password);
    }

    public function deactivate() {
        $this->checkSession();
        
        if (!$this->isPost()) { 
            header("Location: /profile"); 
            exit(); 
        }

        $userId = $_SESSION['user_id'];
        
        $this->userRepository->deleteUser($userId);
        
        session_unset();
        session_destroy();
        
        header("Location: /login");
        exit();
    }
}