<?php

namespace Core;

// use PDO;
// use PDOException;

require_once('DB/DB.php');
use \Core\DB\DB as DB;

class BaseModel extends DB{

    protected static $table;
    protected $schema = [];
    
    public static function getAll($select = '*', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";

        if(is_string($select))
            $sql = "SELECT " . $select . " FROM " . static::$table . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . static::$table . $limit;
        return $sql;
    }

    public static function get($select = '*', $condition='', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' WHERE ' .$condition;

        if(is_string($select))
            $sql = "SELECT " . $select . " FROM " . static::$table . $condition . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . static::$table . $condition .$limit;
        
        return $sql;
        
    }

    public static function getHaving($select = '*', $condition='', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' HAVING ' .$condition;

        if(is_string($select))
            $sql = "SELECT " . $select . " FROM " . static::$table . $condition . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . static::$table . $condition .$limit;
        
        return $sql;
        
    }

    public static function join($select = '*', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";

        if(is_string($select))
            $sql = "SELECT " . $select . " FROM " . static::$table . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . static::$table . $limit;
        return $sql;
    }

    public static function save($data) {

        $keys;
        $values;

        foreach ($data as $key => $value){ 
            $keys .= $key . ", ";
            // $values .= ":" . $key . ", ";
            $values .= is_int($value) ? $value . ", " : "'" . $value . "', ";
        } 
        
        $sql = "INSERT INTO " . static::$table  . "(" . rtrim($keys,', ') .") VALUES(" . rtrim($values,', ') . ")";

        return $sql;
        
    }

    public static function delete($condition) {
        $sql = "DELETE FROM " . static::$table . " WHERE " . $condition;
        return $sql;
    }
    
    public static function update($columns, $condition) {
        $sql = "UPDATE " . static::$table . " SET ";
       
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
