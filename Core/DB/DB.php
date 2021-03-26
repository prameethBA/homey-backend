<?php

namespace Core\DB;

use PDO;
use PDOException;

use \Core\Config\Config as Config;


$dbFile = 'Core/Config/Config.php';

if(file_exists($dbFile)) {
    require_once($dbFile);
} else {
    die("Database Configuration file does not found!.");
}


class DB extends Config{

    protected static function connect() {
        try {

            $connection = new PDO("mysql:host=" . Config::$serverName . ";dbname=" . Config::$dbName, Config::$userName, Config::$password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            // set the PDO error mode to exception
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
            
          } catch(PDOException $err) {
              
            die("Connection failed: " . $err->getMessage());
            
          }
          return null;
    }

    protected static function close($connection) {
        $connection = null;
    }

    
    // Execute insert
    // public static function insert($sql, $params=[]) {
    //     $stmt = self::connect()->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindParam($key, $value);
    //     }
    //     $stmt->execute();
    //     // $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //     return $stmt;
    // }
    
    // Execute select
     public static function execute($sql) {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt;
    }


    // // Execute select
    // public static function image($sql) {
    //     $stmt = self::connect()->prepare($sql);
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_BOUND);
    //     return $stmt;
    // }

    // Excucute update and delete 

    public static function exec($sql) {
        return self::connect()->exec($sql);
    }

}//End of Class