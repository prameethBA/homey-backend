<?php

namespace Core;

require_once('BaseModel.php');
require_once('Token.php');

use \Core\BaseModel as BaseModel;
use \Core\Token as Token;



class BaseController extends Token {
    
    protected $conn;
    protected $params = [];
    
    public function __construct($params) {
        $this->params = $params;
        $this->conn = new BaseModel();
    }

    public function get() {}
    public function post() {}
    public function put() {}
    public function head() {}
    public function delete() {}
    public function patch() {}

    public function __destruct() {
        $this->conn->close();
    }
}
