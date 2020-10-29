<?php

namespace Core;

require_once('Token.php');

use \Core\Token as Token;

class BaseController extends Token {
    
    protected $params = [];
    protected $secureParams = [];
    
    protected $state = [];

    public function __construct($params, $secureParams) {
        $this->params = $params;
        $this->secureParams = $secureParams;
    }

    public function get() {}
    public function post() {}
    public function put() {}
    public function head() {}
    public function delete() {}
    public function patch() {}

} //End of the class
