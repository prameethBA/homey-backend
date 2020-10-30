<?php

namespace Core;

require_once('Token.php');

use \Core\Token as Token;

class BaseController extends Token {
    
    protected $params = [];
    protected $secureParams = [];

    private $uniqueKeyString = "THIS_IS_THE_KEY_STRING_TO_GENERATE_UNIQUE_KEY";
    
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

    // Generate unique key

    protected function uniqueKey($key) {
        return md5(time() . sha1($key . $this->uniqueKeyString ));
    }


} //End of the class
