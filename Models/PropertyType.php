<?php

namespace Models;

// use PDO;
// use PDOException;
require_once('Core/BaseModel.php');

use Core\BaseModel as BaseModel;

class PropertyType extends BaseModel{

    protected static $table;

    public function __construct() {
        $table = (explode('\\',strtolower(basename(get_called_class()))));
        self::$table = isset($table[1]) ? $table[1] : $table[0];
    }

    public $schema = [
        'property_type_id',
        'property_type_name'
    ]; 

}