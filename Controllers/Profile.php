<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Login.php');
use Models\Login as Login;
require_once('Models/User.php');
use Models\User as User;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Profile extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Login();
        new User();
    }

    public function post() {
        try {
            if(isset($this->params[0])) {
                if(!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'info':

                        $stmt = DB::execute(Login::get(['email', 'mobile'], ("user_id = '{$this->secureParams['userId']}'")));
                        $authData = json_encode($stmt->fetch());
                        $stmt = DB::execute(User::get(['first_name as firstName', 'last_name as lastName', 'nic'], ("user_id = '{$this->secureParams['userId']}'")));
                        $userData = json_encode($stmt->fetch());
                        http_response_code(200);
                        echo $resolve = '{
                            "authData": ' . $authData . ',
                            "userData": ' . $userData . '
                        }';
                        break;
                    
                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");

        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        }//End of try catch
            
    }//End of GET

    // Authenticate Admin 
    private function authenticate() {
        if(isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    }

}//End of Class
