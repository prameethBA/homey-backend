<?php

namespace Models;


require_once('Core/BaseModel.php');

use Core\BaseModel as BaseModel;

class ServiceFees extends BaseModel{

    protected static $table;

    public function __construct() {
        $table = (explode('\\',strtolower(basename(get_called_class()))));
        self::$table = isset($table[1]) ? $table[1] : $table[0];
    }

    public $schema = [
        'property_id',
        'property_title',
        'property_price',
        'key_money',
        'minimum_period',
        'available_from',
        'property_type_id',
        'description',
        'district_id',
        'city_id',
        'property_status',
        'facilities'
    ]; 

}