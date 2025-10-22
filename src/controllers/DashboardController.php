<?php

require_once "AppController.php";
class DashboardController extends AppController {

    // TODO zmiana na dashboard

    public function index(){
        return $this->render("dashboard");
    }
}