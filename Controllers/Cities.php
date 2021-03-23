<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class Cities extends Controller
{

    public function All()
    {
        try {
            $stmt = $this->execute($this->getAll('cities', ['_id', 'name_en as name']));

            $this->resolve('{
                "data":' . json_encode($stmt->fetchAll()) . '
            }', 200);
        } catch (Exception $err) {

            $this->reject('{
                "data": {
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }', 200);
        }
    } //End of GET


    public function GetDistrict($param)
    {
        try {
            if (isset($params[0])) throw new Exception("Invalid parameters");
            $distrcitId = (int)$param[0];
            $stmt = $this->execute($this->get('cities', ['_id', 'name_en as city'], ("district_id = {$distrcitId}")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {

            $this->reject('{
                "data": {
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }', 200);
        }
    }

    // public function post()
    // {
    //     try {
    //         if (isset($this->params[0])) {

    //             switch ($this->params[0]) {
    //                 case 'nearest-city':
    //                     $ltd = (float)$this->secureParams['ltd'];
    //                     $lng = (float)$this->secureParams['lng'];

    //                     $stmt = $this->execute($this->getHaving('city', ['district_id as district', 'name_en as city', '(6371 * ACOS(COS(RADIANS(' . $ltd . ')) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(' . $lng . ')) + SIN(RADIANS(' . $ltd . ')) * SIN(RADIANS(latitude)))) AS distance'], ("distance < 25 ORDER BY distance"), 5));

    //                     $this->resolve(json_encode($stmt->fetchAll()), 200);

    //             }
    //         } else throw new Exception("Invalid parameter");
    //     } catch (Exception $err) {

    //         $this->reject('{
    //             "data": {
    //                 "error": "true",
    //                 "message": "' . $err->getMessage() . '"
    //             }
    //         }',500);
    //     }
    // } //End of POST

}//End of Class
