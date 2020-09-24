<?php

require_once('./Core/Router.php');

$router = new Core\Router();

$controllerFile = './Controllers/' . $router->getController() . ".php";

if(file_exists($controllerFile)) {
    require_once($controllerFile);
    $controllerPath = "Controllers\\" . $router->getController();
    $controller = new $controllerPath($router->getParameters());

    if(method_exists($controller,$router->getRequest())) {
        $method = $router->getRequest();
        $controller->{$method}();
    } else {
        die("Method '" . $router->getRequest() . "()' does not exist.");
    }

} else {
    die("Class " . $router->getController() . " does not exist.");
}


require_once('./Core/DB/DB.php');

$conn = new Core\DB\DB();