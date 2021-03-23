<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');
use Core\Controller as Controller;

class Facility extends Controller {

    public function get() {
        try {
            $stmt = $this->execute($this->getAll('facilities'));
            
            http_response_code(200);
            echo $resolve = '{
                "data":' . json_encode($stmt->fetchAll()) . '
            }
            ';

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
