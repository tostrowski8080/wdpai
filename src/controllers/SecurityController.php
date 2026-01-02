<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {

    private $userRepository;

    public function __construct(){
        $this->userRepository = new UserRepository();
    }

    public function login() {
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
        
        return $this->render('dashboard');
    }

    public function register(){  
        if (!$this->isPost()){
            return $this->render("register");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password1'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');

        if (empty($email) || empty($password) || empty($firstname) || empty($lastname)) {
            return $this->render('register', ['messages' => 'Fill all fields']);
        }

        if (strlen($email) > 254 || 
            strlen($password) > 128 || 
            strlen($password2) > 128 ||
            strlen($firstname) > 100 || 
            strlen($lastname) > 100) {
            return $this->render('register', ['messages' => 'Invalid input length']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('register', ['messages' => 'Invalid email format']);
        }

        $existingUser = $this->userRepository->getUserByEmail($email);
        if ($existingUser) {
            return $this->render('register', ['messages' => 'Email already in use']);
        }

        if ($password !== $password2){
            return $this->render('register', ['messages' => 'Passwords must be the same']);
        }

        if (strlen($password) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $password)){
            return $this->render('register', ['messages' => 'Password must be 8+ characters, with at least one uppercase, lowercase, number, and special character.']);
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
            return $this->render('register', ['messages' => 'Registration failed. Please try again later.']);
        }

        return $this->render('login', ['messages' => 'Registration completed, please login']);
    }
}