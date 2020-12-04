<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/RentalPeriod.php');

use Models\RentalPeriod as Rental;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class RentalPeriod extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new Rental();
    }

    public function get()
    {
        try {
            $stmt = DB::execute(Rental::getAll());

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
