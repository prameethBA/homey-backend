<?php

namespace Core;

// use PDO;
// use PDOException;

require_once('DB/DB.php');

use \Core\DB\DB as DB;

class Model extends DB
{

    protected $table;

    function __construct($table) {
        $this->table = $table;
    }

    //Schema is not essential for this application case
    // protected $schema = [];

    public function getAll($select = '*', $limit = '', $offset = 0)
    {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";

        if (is_string($select))
            $sql = "SELECT " . $select . " FROM " . $this->$table . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . $this->$table . $limit;
        return $sql;
    }

    public function get($select = '*', $condition = '', $limit = '', $offset = 0)
    {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' WHERE ' . $condition;

        if (is_string($select))
            $sql = "SELECT " . $select . " FROM " . $this->$table . $condition . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . $this->$table . $condition . $limit;

        return $sql;
    }

    public function getHaving($select = '*', $condition = '', $limit = '', $offset = 0)
    {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' : ' HAVING ' . $condition;

        if (is_string($select))
            $sql = "SELECT " . $select . " FROM " . $this->$table . $condition . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . $this->$table . $condition . $limit;

        return $sql;
    }

    public function join($select = '*', $condition = '', $limit = '', $offset = 0)
    {

        // set limits
        $limit = is_int($limit) ? " LIMIT " . $offset . ", " . $limit : "";
        // Set Conditon
        $condition = $condition === '' ? '' :  $condition;

        if (is_string($select))
            $sql = "SELECT " . $select . " FROM " . $this->$table . " " . $condition . $limit;
        else
            $sql = "SELECT " . implode(', ', $select) . " FROM " . $this->$table . " " . $condition . $limit;

        return $sql;
    }

    public function save($data)
    {

        $keys;
        $values;

        foreach ($data as $key => $value) {
            $keys .= $key . ", ";
            // $values .= ":" . $key . ", ";
            $values .= is_int($value) ? $value . ", " : "'" . $value . "', ";
        }

        $sql = "INSERT INTO " . $this->$table  . "(" . rtrim($keys, ', ') . ") VALUES(" . rtrim($values, ', ') . ")";

        return $sql;
    }

    public function delete($condition)
    {
        $sql = "DELETE FROM " . $this->$table . " WHERE " . $condition;
        return $sql;
    }

    public function update($columns, $condition)
    {
        $sql = "UPDATE " . $this->$table . " SET ";

        foreach ($columns as $key => $value) {
            if (is_int($value))
                $sql .= $key . "= " . $value . " ";
            else
                $sql .= $key . "= '" . $value . "', ";
        }
        $sql = rtrim($sql, ", "); //remove last comma seperator
        $sql .= $condition === '' ? '' : ' WHERE ' . $condition;

        return $sql;
    }

    // public function validateUser($userId) {
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
