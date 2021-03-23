<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Property.php');

use Models\Property as Property;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class AdminPropertySummary extends BaseController
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
                        $stmt = $this->execute($this->get('property', ['_id', 'title', 'user_id', 'created'], ("property_status = 0 ORDER BY created")));

                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetchAll());
                        break;

                    case 'approve':
                        $stmt = $this->execute(Property::update(['property_status' => 1], ("_id = '{$this->secureParams['propertyId']}'")));

                        http_response_code(200);
                        echo $resolve = '{
                            "status": "204",
                            "message": "Approved"
                    }';
                        break;

                    case 'reject':
                        $stmt = $this->execute(Property::update(['property_status' => 2], ("_id = '{$this->secureParams['propertyId']}'")));

                        http_response_code(200);
                        echo $resolve = '{
                                "status": "204",
                                "message": "Rejected"
                        }';
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
