<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/ActivityRepository.php';
require_once __DIR__ . '/../attribute/AllowedMethods.php';

class SecurityController extends AppController {

    private $userRepository;
    private $activityRepository;

    public function __construct(){
        $this->userRepository = new UserRepository();
        $this->activityRepository = new ActivityRepository();
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            header("Location: /dashboard");
            exit();
        }

        if (!$this->isPost()) {
            return $this->render("login");
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? ''; 

        if (empty($email) || empty($password)) {
            return $this->render('login', ['messages' => 'Fill the form']);
        }

        $user = $this->userRepository->getUserByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return $this->render('login', ['messages' => 'Invalid email or password']);
        }

        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['role'] = $user->getRole();

        $this->activityRepository->checkAndCompleteExpiredActivities($user->getId());
        
        header("Location: /dashboard");
        exit();
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function register(){  
        if (!$this->isPost()){
            return $this->render("register");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password1'] ?? '';
        $confirmedPassword = $_POST['password2'] ?? '';
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');

        if (!$this->isValidName($firstname)) {
            return $this->render('register', ['messages' => 'First name must have at least 2 characters and only contain letters!']);
        }

        if (!$this->isValidName($lastname)) {
            return $this->render('register', ['messages' => 'Last name must have at least 2 characters and only contain letters!']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('register', ['messages' => 'Invalid email format!']);
        }

        if (!$this->isPasswordStrong($password)) {
            return $this->render('register', ['messages' => 'Password must be at least 8 characters long and contain at least 1 lowercase, uppercase, number and special character!']);
        }

        if ($password !== $confirmedPassword) {
            return $this->render('register', ['messages' => 'Passwords must be the same!']);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $this->userRepository->createUser(
                $email,
                $hashedPassword,
                $firstname,
                $lastname
            );
        } catch (Exception $e) {
            return $this->render('register', ['messages' => 'Registration failed. Email might already be taken.']);
        }

        return $this->render('login', ['messages' => 'Registration completed, please login']);
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }

    private function isValidName($name) {
        if (mb_strlen($name) < 2) return false;
        
        return preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ \-]+$/u', $name);
    }

    private function isPasswordStrong($password) {
        if (strlen($password) < 8) return false;

        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $password);
    }
}