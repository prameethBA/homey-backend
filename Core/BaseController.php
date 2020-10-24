<?php

namespace Core;

// use \Core\DB\DB as DB;

spl_autoload_register(function($className) {
    $file = __DIR__ . '\\' . $className . '.php';
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    if (file_exists($file)) {
        include $file;
    }
});


require_once('Token.php');

use \Core\Token as Token;

class BaseController extends Token {
    
    protected $params = [];
    
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

    // public function __destruct() {
    //     $this->conn->close();
    // }
}
