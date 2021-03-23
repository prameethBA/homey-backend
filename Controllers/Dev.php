<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');
use Core\Controller as Controller;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Dev extends Controller {

    public function get() {
        try {
            if(isset($this->params[0])) {
                switch($this->params[0]) {
                    case 'tables':
                        $stmt = DB::execute("SHOW TABLES");
                        echo json_encode($stmt->fetchAll());
                        break;
                    case 'data': 
                        $stmt = DB::execute("SELECT * FROM " . $this->params[1]);
                        echo json_encode($stmt->fetchAll());
                        break;

                }


            } else throw new Exception("Invalid request.");

        } catch(Exception $err) {
            http_response_code(500);
            die($reject = '{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }');
        }
            
    }//End of GET

}//End of Class
