<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Feedback.php');

use Models\Feedback as Feed;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class Feedback extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new Feed();
    }

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'add':

                        $data = [
                            'user_id' => (int)$this->secureParams['anonymous'] == 0 ? $this->secureParams['userId'] : 0,
                            'property_id' => $this->secureParams['propertyId'],
                            'feedback' => $this->secureParams['feedback']
                        ];

                        $stmt = DB::execute(Feed::save($data));
                        http_response_code(201);
                        echo $resolve = '{
                            "action": "true",
                            "message": "comment saved"
                        }';
                        break;
                    case 'get':

                        switch ($this->params[1]) {
                            case 'all':

                                $stmt = DB::execute(Feed::getAll('_id as id', "property_id='{$this->secureParams['propertyId']}'"));
                                http_response_code(201);
                                echo json_encode($stmt->fetchAll());
                                break;
                            default:
                                $stmt = DB::execute(Feed::get('*', "_id='{$this->params[1]}'"));
                                http_response_code(201);
                                echo json_encode($stmt->fetch());
                                break;
                        }
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

    } //End of post

    // Authenticate User 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    } //end of authenticateUser()

}//End of Class
