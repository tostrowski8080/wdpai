<?php

require_once "src/controllers/SecurityController.php";
require_once "src/controllers/DashboardController.php";
require_once "src/controllers/CalendarController.php";
require_once "src/controllers/ProfileController.php";
require_once "src/controllers/AddActivityController.php";
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
        'add-activity' => [
            'controller' => "AddActivityController",
            'action' => 'add'
        ],
        'modify-activity' => [
            'controller' => "AddActivityController",
            'action' => 'modify'
        ],
        'calendar' => [
            'controller' => "CalendarController",
            'action' => 'index'
        ],
        'logout' => [
            'controller' => 'SecurityController',
            'action' => 'logout'
        ],
        'profile' => [
            'controller' => 'ProfileController',
            'action' => 'index'
        ],
        'update-profile-info' => [
            'controller' => 'ProfileController',
            'action' => 'updateInfo'
        ],
        'change-password' => [
            'controller' => 'ProfileController',
            'action' => 'changePassword'
        ],
        'deactivate-account' => [
            'controller' => 'ProfileController',
            'action' => 'deactivate'
        ],
    ];

public static function run(string $path) {
        $urlParts = explode("/", $path);
        $actionKey = $urlParts[0];

        if ($actionKey === 'activity' && isset($urlParts[1]) && is_numeric($urlParts[1])) {
            $controllerName = 'AddActivityController';
            $methodName = 'modify';
            $id = (int)$urlParts[1];
        } 
        elseif (array_key_exists($actionKey, self::$routes)) {
            $controllerName = self::$routes[$actionKey]['controller'];
            $methodName = self::$routes[$actionKey]['action'];
            $id = $urlParts[1] ?? null;
        } 
        else {
            include 'public/views/404.html';
            return;
        }

        if (!isset(self::$instances[$controllerName])) {
            self::$instances[$controllerName] = new $controllerName();
        }
        
        $object = self::$instances[$controllerName];

        checkRequestAllowed($object, $methodName);

        $object->$methodName($id);
    }
}