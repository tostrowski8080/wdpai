<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';
class SecurityController extends AppController {


    private $userRepository;

    public function __construct(){
        $this->userRepository = new UserRepository();
    }

    public function login(){
        if (!$this->isPost()){
            return $this->render("login");
        }
        
        $email = $_POST('email') ?? '';
        $password = $_POST('password') ?? '';

        if ($email == '' || $password == ''){
            return $this->render('login', ['messages' => 'Fill the form']);
        }

        //$userRepository = new UserRepository();
        //$user = $userRepository->getUserByEmail($email);
        $user = $this->$userRepository->getUserByEmail($email);

        if (!$user){
            return $this->render('login', ['messages' => 'User does not exist']);
        }

        if (!password_verify($password, $user('password'))){
            return $this->render('login', ['messages' => 'Wrong password']);
        }

        //TODO
        return $this->render('dashboard');
    }

    public function register(){  
        if (!$this->isPost()){
            return $this->render("register");
        }

        var_dump($POST);
        $email = $_POST('email') ?? '';
        $password = $_POST('password1') ?? '';
        $password2 = $_POST('password2') ?? '';
        $firstname = $_POST('firstname') ?? '';
        $lastname = $_POST('lastname') ?? '';

        if (empty($email) || empty($password) || empty($firstName)) {
            return $this->render('register', ['messages' => 'Fill all fields']);
        }

        //TODO Check if user exists

        if ($password != $password2){
            return $this->render('register', ['messages' => 'Passwords must be the same!']);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->userRepository->createUser(
            $email,
            $hashedPassword,
            $firstname,
            $lastname
        )

        return $this->render('login', ['messages' => 'Registration completed, please login']);
    }
}

