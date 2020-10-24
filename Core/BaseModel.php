<?php

namespace Core;

use PDO;
use PDOException;

class BaseModel {

    protected static $table;
    protected $schema = [];
    
    public function __construct() {
        $table = (explode('\\',strtolower(basename(get_called_class()))));
        self::$table = isset($table[1]) ? $table[1] : $table[0];
    }
 
    public static function getAll($select = '*', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " .$limit : "";

        if(is_string($select))
            $sql = "SELECT * FROM " . self::$table . " LIMIT " . $offset . ", " .$limit;
        else
            $sql = "SELECT " . implode(', ', $select) ." FROM " . self::$table .$limit;

        return $sql;
    }

    public static function get($condition='', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' WHERE ' .$condition;

        $sql = "SELECT * FROM " . self::$table . $condition .$limit;
        
        return $sql;
        
    }

    public static function save($data) {

        $keys;
        $values;

        foreach ($data as $key => $value){ 
            $keys .= $key . ", ";
            $values .= is_int($value) ? $value . ", " : "'" . $value . "', ";
        } 
        
        $sql = "INSERT INTO " . self::$table  . "(" . rtrim($keys,', ') .") VALUES(" . rtrim($values,', ') . ")";

        return $sql;
        
    }

    public static function delete($condition) {
        $sql = "DELETE FROM " . self::$table . " WHERE " . $condition;
        return $sql;
    }
    
    public static function update($columns, $condition) {
        $sql = "UPDATE " . self::$table . " SET ";
       
        foreach ($columns as $key => $value) {
            if(is_int($value))
                $sql .= $key . "= " . $value . " ";
            else 
                $sql .= $key . "= '" . $value . "', "; 
        }
        $sql = rtrim($sql, ", ");//remove last comma seperator
        $sql .= $condition === '' ? '' : ' WHERE ' .$condition;

        return $sql;
    }

    // public static function validateUser($userId) {
    //     $sql = "SELECT * FROM user WHERE user_id = {$userId} AND access_token = '{$_SERVER['HTTP_AUTHORIZATION']}'";
    //     $stmt = parent::connect()->prepare($sql);
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //     if($stmt->rowCount() == 1)
    //         return true;
    //     else
    //         return false;
    // }

}
