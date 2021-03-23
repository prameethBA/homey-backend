<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class Cities extends Controller
{


    public function get()
    {
        try {
            if (isset($this->params[0], $this->params[1])) {
                if ($this->params[0] == 'districtId') $stmt = $this->execute($this->get('city', ['_id', 'name_en as city'], ("district_id = {$this->params[1]}")));
                else throw new Exception("Invalid parameter");
            } else if (isset($this->params[0])) throw new Exception("Invalid parameter");
            else $stmt = $this->execute($this->getAll('city', ['_id', 'name_en as name']));

            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {

            $this->reject('{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }', 500);
        }
    } //End of GET

    public function post()
    {
        try {
            if (isset($this->params[0])) {

                switch ($this->params[0]) {
                    case 'nearest-city':
                        $ltd = (float)$this->secureParams['ltd'];
                        $lng = (float)$this->secureParams['lng'];

                        $stmt = $this->execute($this->getHaving('city', ['district_id as district', 'name_en as city', '(6371 * ACOS(COS(RADIANS(' . $ltd . ')) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(' . $lng . ')) + SIN(RADIANS(' . $ltd . ')) * SIN(RADIANS(latitude)))) AS distance'], ("distance < 25 ORDER BY distance"), 5));

                        $this->resolve(json_encode($stmt->fetchAll()), 200);

                }
            } else throw new Exception("Invalid parameter");
        } catch (Exception $err) {

            $this->reject('{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }',500);
        }
    } //End of POST

}//End of Class
