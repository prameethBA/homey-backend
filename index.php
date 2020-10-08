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
        die('{
            status: 400,
            message: "Invalid request."
        }');
    }

} else {
    die('{
        status: 500,
        message: "Internal Server Error"
    }');
}
