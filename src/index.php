<?php

require_once('./Core/Router.php');

$router = new Core\Router();

$controllerFile = './Controllers/' . $router->getController() . ".php";

if(file_exists($controllerFile)) {
   
    require_once($controllerFile);
    $controllerPath = "Controllers\\" . $router->getController();
    $controller = new $controllerPath($router->getParameters(),$router->getHeaderParameters());
    if(method_exists($controller,$router->getRequest())) {
        $method = $router->getRequest();
        $controller->{$method}();
    } else {
        http_response_code(406);
        die('{
            "message": "Invalid request."
        }');
    }

} else {
    http_response_code(500);
    die('{
        "message": "Internal Server Error."
    }');
}
