<?php

// Router

class Router {

    // routing table
    protected $routes = [];

    // matched routes
    protected $params = [];

    // Add route to the routing table
    public function add($route, $params) {
        $this->routes[$route] = $params;
    }

    // Return the routing table 
    public function getRoutes() {
        return $this->routes;
    }

    // match route
    public function match($request) {
        // foreach($this->routes as $route => $params) {
        //     if($request == $route){
        //         $this->params = $params; return true;
        //     }
        // }
        // return false;

        $extractor = "/^(?P<controller>[a-z-]+)\/(?P<method>[a-z-]+)$/";

        if(preg_match($extractor, $request, $matches)) {
            $params = [];

            foreach($matches as $key => $match) {
                if(is_string($key)) {
                    $params[$key] = $match;
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    public function getParams() {
        return $this->params;
    }
}
