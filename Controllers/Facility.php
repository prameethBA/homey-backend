<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Facility extends Controller
{

    public function All()
    {
        try {
            $stmt = $this->execute($this->getAll('facilities', "*"));


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
    } //End of GET

}//End of Class
