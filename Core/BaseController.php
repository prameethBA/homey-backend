<?php

namespace Core;

require_once('DB/DB.php');

use \Core\DB\DB as DB;



class BaseController {
    
    protected $conn;
    protected $params = [];
    
    public function __construct($params) {
        $this->params = $params;
        $this->conn = new DB();
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
