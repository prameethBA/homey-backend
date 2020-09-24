<?php

namespace Controllers;

class Login {

    private $params = [];

    public function __construct($params) {
        $this->params = $params;
    }

    public function get() {
        print_r($this->params);
    }
}
