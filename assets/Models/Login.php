<?php

namespace Models;


require_once('Core/Model.php');

use Core\Model as Model;

class Login extends Model{

    protected static $table;

    public function __construct() {
        $table = (explode('\\',strtolower(basename(get_called_class()))));
        self::$table = isset($table[1]) ? $table[1] : $table[0];
    }

    public $schema = [
        'user_id',
        'email',
        'mobile',
        'password',
        'access_token',
        'user_status'
    ]; 

}