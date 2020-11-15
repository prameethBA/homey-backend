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

                        $stmt = DB::execute(Login::get(['email', 'mobile', 'updated as lastLogin'], ("user_id = '{$this->secureParams['userId']}'")));
                        $authData = json_encode($stmt->fetch());
                        $stmt = DB::execute(User::get([
                            'first_name as firstName',
                            'last_name as lastName',
                            'address1',
                            'address2',
                            'address3',
                            'city',
                            'district',
                            'dob',
                            'nic'
                            ], ("user_id = '{$this->secureParams['userId']}'")));
                        $userData = json_encode($stmt->fetch());
                        http_response_code(200);
                        echo $resolve = '{
                            "authData": ' . $authData . ',
                            "userData": ' . $userData . '
                        }';
                        break;

                    case 'update':

                        $loginData = [
                            'email' => $this->secureParams['email'],
                            'mobile' => $this->secureParams['mobile'],
                        ];

                        $userData = [
                            'first_name' => $this->secureParams['firstName'],
                            'last_name' => $this->secureParams['lastName'],
                            'nic' => $this->secureParams['nic'],
                            'address1' => $this->secureParams['address1'],
                            'address2' => $this->secureParams['address2'],
                            'address3' => $this->secureParams['address3'],
                            'city' => $this->secureParams['city'],
                            'district' => $this->secureParams['district'],
                            'dob' => $this->secureParams['dob'],
                        ];

                        $stmt = DB::execute(Login::update($loginData, ("user_id = '{$this->secureParams['userId']}'")));
                        $stmt = DB::execute(User::update($userData, ("user_id = '{$this->secureParams['userId']}'")));
                        http_response_code(201);
                        echo $resolve = '{
                            "message" : "Profile update successfully"
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
            
    }//End of post

    // Authenticate User 
    private function authenticate() {
        if(isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    }//end of authenticateUser()

}//End of Class
