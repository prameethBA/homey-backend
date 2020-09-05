<?php

// Front controller

// Autoloader
spl_autoload_register(function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/homey-backend/' .str_replace('\\', '/', $class) . '.php';
    if(is_readable($file)) {
        require_once $file;
    }
});

spl_autoload_register(function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/homey-backend/App/Controller/' .str_replace('\\', '/', $class) . '.php';
    if(is_readable($file)) {
        require_once $file;
    }
});


$router = new Core\Router();

$router->add('', "home", "index");
$router->add('/post/add', "post", "add");
$router->add('/post/set', "post");

if($router->dispatch($_SERVER['REQUEST_URI'])) print_r($router->getParams());
print_r($router->getParameters());