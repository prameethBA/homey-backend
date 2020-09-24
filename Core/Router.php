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
        $this->setParameters($_SERVER['QUERY_STRING']);
    }
    
    private function setRequest() {
        $this->Request = strtolower($_SERVER['REQUEST_METHOD']);
    }
    
    public function getRequest() {
        return $this->Request;
    }

    private function setContoller($request) {
        $this->Controller = $this->toStringCapitalize(explode('/', $request)[0]);
    }
    
    public function getController() {
        return $this->Controller;
    }

    private function setParameters($request) {
        $paramerters = [];
        $index = -1;
        foreach(explode('/', $request) as $value) {
            if($index === -1) {
                $index++;
                continue;
            }
            $paramerters[$index] =  $value;
            $index++;
        } 

        $this->paramerters = $paramerters;
    }

    public function getParameters() {
        return $this->paramerters;
    }

    // Capitalize String
    protected function toStringCapitalize($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

   
}