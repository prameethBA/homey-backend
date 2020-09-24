<?php

namespace Core;

class BaseController {

    protected $params = [];

    public function __construct($params) {
        $this->params = $params;
    }

    public function get() {}
    public function post() {}
    public function put() {}
    public function head() {}
    public function delete() {}
    public function patch() {}
}