<?php

// Front controller
// echo $_SERVER['REQUEST_URI'];

// Import Router
require "Core/Router.php";

$router = new Router();

$router->add('', "home", "index");
$router->add('/post/add', "post", "add");
$router->add('/post/set', "post");

if($router->match($_SERVER['REQUEST_URI'])) print_r($router->getParams());
print_r($router->getParameters());