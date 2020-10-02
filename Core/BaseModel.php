<?php

namespace Core;

require_once('DB/DB.php');

use \Core\DB\DB as DB;



class BaseModel {
    
    protected $conn;
    protected $params = [];
    
    public function __construct($params) {
        $this->params = $params;
        $this->conn = new DB();
    }

    public function getAll($table) {
        $sql = "SELECT * FROM" . $table;
        $stmt = $this->conn->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
