<?php

// Front controller
// echo $_SERVER['REQUEST_URI'];

// Import Router
require "Core/Router.php";

$router = new Router();

$router->add('', ["controller" => "home", "method" => "index"]);
$router->add('/post/add', ["controller" => "home", "method" => "index"]);

if($router->match($_SERVER['REQUEST_URI'])) print_r($router->getParams());
else echo "<pre>";print_r($router->getRoutes());
