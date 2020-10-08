<?php

namespace Models;

// use PDO;
// use PDOException;
require_once('Core/BaseModel.php');

use Core\BaseModel as BaseModel;

class User extends BaseModel{

    public $schema = [
        'user_id',
        'email',
        'mobile',
        'password',
        'access_token'
    ]; 

}