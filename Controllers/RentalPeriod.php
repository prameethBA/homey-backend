<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class RentalPeriod extends Controller
{


    public function All()
    {
        try {
            $stmt = $this->execute($this->getAll('rentalperiod', "*"));

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

    public function post()
    {
    } //End of POST

}//End of Class
