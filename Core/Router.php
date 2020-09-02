<?php

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
                $this->setParameters($request);
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

    public function getParameters() {
        return $this->parameters;
    }

    public function getParams() {
        return $this->params;
    }
}
