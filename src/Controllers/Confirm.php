<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/ConfirmationInfo.php');
use Models\ConfirmationInfo as Hash;
require_once('Models/Login.php');
use Models\Login as Login;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Confirm extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Hash();
        new Login();
    }

    public function get() {
        try {
            if(isset($this->params[0], $this->params[1])) {
                $userId = base64_decode($this->params[0]);
                $hash = $this->params[1];

                $stmt = DB::execute(Hash::get(['_id'], ("user_id = {$userId} AND hash = '{$hash}'")));

                if($stmt->rowCount() == 1) {
                    DB::exec(Hash::delete("user_id = {$userId}"));
                    DB::exec(Login::update(['user_status' => 1], ("user_id = {$userId}")));

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
