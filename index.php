<?php

require_once('./Core/Router.php');

$router = new Core\Router();

if(file_exists('./Controllers/' . $router->getController() . ".php")) {
    
} else {
    echo "Class " . $router->getController() . " does not exist.";
}
echo "<br>";
echo $router->getRequest();
