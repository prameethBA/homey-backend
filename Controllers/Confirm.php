<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');
use Core\Controller as Controller;

class Confirm extends Controller {

   

    public function get() {
        try {
            if(isset($this->params[0], $this->params[1])) {
                $userId = base64_decode($this->params[0]);
                $hash = $this->params[1];

                $stmt = $this->execute($this->get('hash',['_id'], ("user_id = {$userId} AND hash = '{$hash}'")));

                if($stmt->rowCount() == 1) {
                    $this->exec($this->delete('hash',"user_id = {$userId}"));
                    $this->exec($this->update('login',['user_status' => 1], ("user_id = {$userId}")));

                    http_response_code(200);
                    echo $resolve = '{
                        "verified": "true",
                        "message": "Account activated successfully."
                    }';

                } else throw new Exception("Link may expired or invalid.");

            } else throw new Exception("Invalid request.");
            
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
