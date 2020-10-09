<?php

namespace Core;

require_once('DB/DB.php');

use \Core\DB\DB as DB;

use PDO;
use PDOException;

class BaseModel extends DB{

    protected $table;
    protected $schema = [];
    
    public function __construct() {
        $this->table = strtolower(basename(get_class($this)));
    }
 
    public function getAll($select = '*', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " .$limit : "";

        if(is_string($select))
            $sql = "SELECT * FROM " . $this->table . " LIMIT " . $offset . ", " .$limit;
        else
            $sql = "SELECT " . implode(', ', $select) ." FROM " . $this->table .$limit;

        return $sql;
    }

    public function get($condition='', $limit='', $offset=0) {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " .$limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' WHERE ' .$condition;

        $sql = "SELECT * FROM " . $this->table . $condition .$limit;

        return $sql;
        
    }

    // Execute select
    public static function execute($sql) {
        $stmt = parent::connect()->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }

    // Excucute Insert, update and delete 

    public static function exec($sql) {
        return parent::connect()->exec($sql);
    }

    public function delete($condition) {
        $sql = "DELETE FROM " . $this->table . " WHERE " . $condition;
    }

    public function save($columns) {
        $sql = "INSERT INTO " . $this->table . "(";
        
        foreach ($columns as $key => $value) {
            $sql .= "`" . $key ."`"; 
        }

        $sql .= ") VALUES (";

        foreach ($columns as $key => $value) {
            if(is_int($value))
                $sql .= $value;
            else 
                $sql .= "`" . $value ."`"; 
        }
    }
    
    public function update($columns, $condition) {
        $sql = "UPDATE " . $this->table . " SET ";
       
        foreach ($columns as $key => $value) {
            if(is_int($value))
                $sql .= $key . "= " . $value . " ";
            else 
                $sql .= $key . "= '" . $value . "', "; 
        }
        $sql = rtrim($sql, ", ");//remove last comma seperator
        $sql .= $condition === '' ? '' : ' WHERE ' .$condition;
    }

    public function validateUser($userId) {
        $sql = "SELECT * FROM user WHERE user_id = {$userId} AND access_token = '{$_SERVER['HTTP_AUTHORIZATION']}'";
        $stmt = parent::connect()->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() == 1)
            return true;
        else
            return false;
    }

}
