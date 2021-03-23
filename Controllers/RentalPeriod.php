<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class RentalPeriod extends Controller
{


    public function get()
    {
        try {
            $stmt = $this->execute($this->getAll('rental'));

            $this->resolve(json_encode($stmt->fetchAll()),200);

        } catch (Exception $err) {

            $this->reject('{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }',500);
        }
    } //End of GET

    public function post()
    {
    } //End of POST

}//End of Class
