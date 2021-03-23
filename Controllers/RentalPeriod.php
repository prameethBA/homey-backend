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

            http_response_code(200);
            echo $resolve = json_encode($stmt->fetchAll());
        } catch (Exception $err) {
            http_response_code(500);
            die($reject = '{
                "data": {
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }
            }');
        }
    } //End of GET

    public function post()
    {
    } //End of POST

}//End of Class
