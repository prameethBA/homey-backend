<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Cities.php');
use Models\Cities as City;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Cities extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new City();
    }

    public function get() {
        try {
            if(isset($this->params[0], $this->params[1])) {
                if($this->params[0] == 'districtId') $stmt = DB::execute(City::get(['_id', 'name_en as city'], ("district_id = {$this->params[1]}")));
                else throw new Exception("Invalid parameter");
            } else if(isset($this->params[0])) throw new Exception("Invalid parameter");
            else $stmt = DB::execute(City::getAll());
            
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

    public function post() {
        try {
            if(isset($this->params[0])) {

                switch ($this->params[0]) {
                    case 'nearest-city':
                        $ltd = (double)$this->secureParams['ltd'];
                        $lng = (double)$this->secureParams['lng'];

                        $stmt = DB::execute(City::getHaving(['district_id as district', 'name_en as city', '(6371 * ACOS(COS(RADIANS(' . $ltd . ')) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(' . $lng . ')) + SIN(RADIANS(' . $ltd . ')) * SIN(RADIANS(latitude)))) AS distance'], ("distance < 25 ORDER BY distance"), 5));

                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetchAll());
                }

            } else throw new Exception("Invalid parameter");
            

        } catch(Exception $err) {
            http_response_code(500);
            die($reject = '{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }');
        }
            
    }//End of POST

}//End of Class
