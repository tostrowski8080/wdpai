<?php

require_once "src/controllers/SecurityController.php";
require_once "src/controllers/DashboardController.php";
require_once "src/controllers/CalendarController.php";
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
    ];

    public static function run(string $path) {
        $urlParts = explode("/", $path);
        
        $actionKey = $urlParts[0];

        if (!isset(self::$routes[$path])) {
            http_response_code(404);
            $notFoundPath = __DIR__ . '/public/views/404.html';
            if (file_exists($notFoundPath)) {
                include $notFoundPath;
            } else {
                echo "404 - Page not found";
            }
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