<?php

namespace Core;

// Router

class Router {
    private $Controller;
    private $Request;
    private $paramerters = [];

    function __construct() {
        $this->setRequest();
        $this->setContoller($_SERVER['QUERY_STRING']);
    }
    
    public function setRequest() {
        $this->Request = $this->toStringCamelCase($_SERVER['REQUEST_METHOD']);
    }
    
    public function getRequest() {
        return $this->Request;
    }

    public function setContoller($request) {
        $this->Controller = $this->toStringCapitalize(explode('/', $request)[0]);
    }
    
    public function getController() {
        return $this->Controller;
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