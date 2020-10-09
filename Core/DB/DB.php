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

    // public static $connection;

    // public function __construct() {
    //    $this->connect();
    // }

    protected static function connect() {
        try {

            $connection = new PDO("mysql:host=" . Config::$serverName . ";dbname=" . Config::$dbName, Config::$userName, Config::$password);
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

}