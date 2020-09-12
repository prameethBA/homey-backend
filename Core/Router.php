<?php

namespace Core;

// Router

class Router {
    
    // routing table
    protected $routes = [];
    
    // matched routes
    protected $params = [];
    
    //retived parameters
    protected $parameters = []; 

    // Regx for split the URL
    private $extractor = "/^\/(?P<controller>[a-z-]*)\/*(?P<method>[a-z-]*)\/*/";
    
    
    // Add route to the routing table
    public function add($route, $controller, $method = 'index') {
        if(array_key_exists($route, $this->routes)) echo "Route already added.";
        else {
            $this->routes[$route]['controller'] = $controller;
            $this->routes[$route]['method'] = $method;
        }
    }

    // Return the routing table 
    public function getRoutes() {
        return $this->routes;
    }

    // match route
    public function match($request) {
        if(preg_match($this->extractor, $request, $matches)) {
            $params = [];
            foreach($matches as $key => $match) {
                if(is_string($key)) $params[$key] = $match;
                if($key === "method" && $match == NULL) $params['method'] = "index";
            }
            if(in_array($params, $this->routes)) {
                $this->params = $params;
                return true;
            } else
                return false;
        } else 
            return false;
    }

    // Retive parameters
    public function setParameters($request) {
        $parameterList = [];
        $parameterCount = 0;
        $parameters = explode('/', $request);

        foreach($parameters as $key => $parameter) {
            if($parameter == NULL) continue;
            if(in_array($parameter, $this->params)) continue;

            $parameterList[$parameterCount++] = $parameter;
        }

        $this->parameters = $parameterList;
    }

    // Dispatch
    public function dispatch($request) {
        if($this->match($request)) {
            $controller = $this->params['controller'];
            $controller = $this->toStringCapitalize($controller);
           
            if(class_exists($controller)) {
                $controllerObject = new $contoller();

                $method = $this->params['method'];
                $method = $this->toStringCamelCase($method);

                if(is_callable([$controllerObject, $method])) {
                    $controllerObject->method();
                    $this->setParameters($request);
                } else {
                    echo "Method $action (in controller $contoller) not found";
                }
            } else {
                echo "Controller class $controller not found";
            }
        } else {
            echo "No route matched";
        }
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getParams() {
        return $this->params;
    }

    // Capitalize String
    protected function toStringCapitalize($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    // Camel case String
    protected function toStringCamelCase($string) {
        return lcfirst($this->toStringCapitalize($string));
    }
}
