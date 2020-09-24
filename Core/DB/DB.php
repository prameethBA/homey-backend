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


class DB extends Config {

    public $connection;

    public function __construct() {
       $this->connectToDB();
    }

    private function connectToDB() {
        try {

            $connection = new PDO("mysql:host=$this->serverName;dbname=$this->dbName", $this->userName, $this->password);
            // set the PDO error mode to exception
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection = $connection;
            
          } catch(PDOException $err) {
              
            die("Connection failed: " . $err->getMessage());

          }
    }

    public function close() {
        $this->connection = null;
    }

}