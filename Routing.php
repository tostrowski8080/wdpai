<?php

require_once "src/controllers/SecurityController.php";
require_once "src/controllers/DashboardController.php";
require_once 'src/middleware/checkRequestAllowed.php';

class Routing {
    private static $instances = [];

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
    ];

    public static function run(string $path) {
        $urlParts = explode("/", $path);
        
        $actionKey = $urlParts[0];

        if (!array_key_exists($actionKey, self::$routes)) {
            include 'public/views/404.html';
            return;
        }

        $controllerName = self::$routes[$actionKey]['controller'];
        $methodName = self::$routes[$actionKey]['action'];

        if (!isset(self::$instances[$controllerName])) {
            self::$instances[$controllerName] = new $controllerName();
        }
        
        $object = self::$instances[$controllerName];

        checkRequestAllowed($object, $methodName);

        $id = $urlParts[1] ?? null;

        $object->$methodName($id);
    }
}