<?php

require_once "src/controllers/SecurityController.php";
require_once "src/controllers/DashboardController.php";

class Routing {

    public static $routes = [
        'login' => [
            'controller' => "SecurityController",
            'action' => 'login'
        ],
        'register' => [
            'controller' => "SecurityController",
            'action' => 'register'
        ],
        'dashboard' => [
            'controller' => "DashboardController",
            'action' => 'index'
        ],
        'search-cards' => [
            'controller' => "DashboardController",
            'action' => 'search'
        ]
    ];

    // TODO bez switch case
    // TODO dashboard/....
    // TODO singleton aby nie tworzyc nowych kontrolerow

    public static function run(string $path) {
        if (array_key_exists($path, self::$routes)){
                $controller = new Routing::$routes[$path]['controller'];
                $action = Routing::$routes[$path]['action'];
                $controller->$action();
        } else include 'public/views/404.html';
    }
}