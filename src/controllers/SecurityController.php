<?php

require_once "AppController.php";
class SecurityController extends AppController {

    // TODO pobieramy z formularza email, login
    //sprawdzamy czy taki istnieje w db

    public function login(){
        return $this->render("login");
    }
}