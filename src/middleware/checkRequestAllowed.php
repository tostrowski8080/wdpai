<?php
require_once __DIR__.'/../attribute/AllowedMethods.php';

function checkRequestAllowed(object $controller, string $methodName) {
   $reflection = new ReflectionMethod($controller, $methodName);
   $attributes  = $reflection->getAttributes(AllowedMethods::class);

   if (!empty($attributes)) {
       $instance = $attributes[0]->newInstance();
       $allowed = $instance->methods;

       if (!in_array($_SERVER['REQUEST_METHOD'], $allowed)) {
           die("Method Not Allowed"); // TODO Exception i strona bledu
       }
   }
}
