<?php

namespace Core;

require_once('DB/DB.php');

use \Core\DB\DB as DB;

use PDO;
use PDOException;



class BaseModel extends DB{

    protected $conn;
    
    public function __construct() {
        $this->conn = new DB();
        $this->conn = $this->conn->connection;
    }

    public function getAll($table) {
        $sql = "SELECT * FROM " . $table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }

    public function get($table, $condition='', $limit=1, $offset=0) {
        $condition = $condition === '' ? '' : ' WHERE ' .$condition;
        $sql = "SELECT * FROM " . $table . $condition . " LIMIT " . $offset . ", " .$limit;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }

    public function delete($table, $condition) {
        $sql = "DELETE FROM " .$table . " WHERE " . $condition;
        $this->conn->exec($sql);
    }

    public function save($table, $columns) {
        $sql = "INSERT INTO " . $table . "(";
        
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

        $this->conn->exec($sql);
    }
    
    public function update($table, $columns, $condition) {
        $sql = "UPDATE " . $table . " SET ";
       
        foreach ($columns as $key => $value) {
            if(is_int($value))
                $sql .= $key . "= " . $value . " ";
            else 
                $sql .= $key . "= '" . $value . "', "; 
        }
        $sql = rtrim($sql, ", ");//remove last comma seperator
        $sql .= $condition === '' ? '' : ' WHERE ' .$condition;
        // $this->conn->exec($sql);
    }

    public function validateUser($userId) {
        $sql = "SELECT * FROM user WHERE user_id = {$userId} AND access_token = '{$_SERVER['HTTP_AUTHORIZATION']}'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() == 1)
            return true;
        else
            return false;
    }

}
