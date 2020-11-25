<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Property.php');

use Models\Property as Property;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class AdminPropertyPreview extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new Property();
    }

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'pending-approval':
                        $data = [
                            'price',
                            'title',
                            'rental_period as rentalPeriod',
                            'key_money as keyMoney',
                            'minimum_period as minimumPeriod',
                            'available_from as availableFrom',
                            'description',
                            'location',
                            'created',
                            'user_id userId',
                            'facilities',
                        ];
                        // 'city_id',
                        // 'property_type_id',

                        $stmt = DB::execute(Property::get($data, ("_id = '{$this->secureParams['id']}' AND property_status = 0")));

                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetch());
                        break;

                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        } //End of try catch

    } //End of GET

    // Authenticate Admin 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    }
}//End of Class
