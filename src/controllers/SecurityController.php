<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';
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
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");
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

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->render('login', ['messages' => 'Invalid email or password']);
        }

        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        $this->activityRepository->checkAndCompleteExpiredActivities($user['id']);
        
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
        exit();
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function register(){  
        if (!$this->isPost()){
            return $this->render("register");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password1'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $this->userRepository->createUser(
                $email,
                $hashedPassword,
                $firstname,
                $lastname
            );
        } catch (Exception $e) {
            return $this->render('register', ['messages' => 'Registration failed. Please try again later.']);
        }

        return $this->render('login', ['messages' => 'Registration completed, please login']);
    }
}