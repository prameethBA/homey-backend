<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Districts.php');
use Models\Districts as Districts;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class District extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Districts();
    }

    public function get() {
        try {
            $stmt = DB::execute(Districts::getAll(['_id', 'name_en as district']));
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
