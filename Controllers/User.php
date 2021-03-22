<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Models/Login.php');
use Models\Login as Login;
require_once('Models/User.php');
use Models\User as UserModel;
require_once('Models/Admin.php');
use Models\Admin as Admin;
require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class User extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Login();
        new UserModel();
        new Admin();
    }

    public function get() {
        http_response_code(406);
            die($reject = '{
                "message": "Invalid Request"
            }');    
    }

    //SignUp method
    public function post() {

        try {

            if(isset($this->params[0])) {
                switch ($this->params[0]) {
                    // deactivate a user
                    case 'deactivate':
                        if(!$this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) throw 'Unauthorized request';
                        DB::execute(Login::update(['user_status' =>  2/*2 for blocked*/],'user_id = ' . $this->params['1']));
                        http_response_code(200);
                        echo '{
                            "status":"200",
                            "action":"true",
                            "message":"user blocked"
                        }';
                        break;

                        // activate a user
                    case 'activate':
                        if(!$this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) throw 'Unauthorized request';
                        DB::execute(Login::update(['user_status' =>  1/*1 for activate*/],'user_id = ' . $this->params['1']));
                        http_response_code(200);
                        echo '{
                            "status":"200",
                            "action":"true",
                            "message":"user activated"
                        }';
                        break;
    
                    default:
                        http_response_code(200);
                        die($reject  = '{
                            "status": "400",
                            "signup": "false",
                            "message": "Invalid user type."
                        }');
                        //End of Default
                }//End of Switch
            } else throw "Invalid parameters";

        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        } //End of try catch

        // $stmt = User::execute(User::get("(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));

        // if($stmt->rowCount() == 1) {
        //     $result = $stmt->fetch();
        //     $payload = "{
        //         id: " . $result['user_id'] . ",
        //         email: '" . $result['email'] . "'
        //     }";
        //     $this->setToken($payload);
        //     http_response_code(201);
        //     echo $resolve = '{
        //         "data" : {
        //             "login": "true",
        //             "token": "' . $this->getToken() . '",
        //             "message": "Login Succesfull"
        //         }
        //     }';

        //     User::update(['access_token' => $this->getToken(), "next" =>"val"], "user_id = {$result['user_id']}");
            
        // } else {
        //     http_response_code(404);
        //     echo $reject = '{
        //         "data": {
        //             "login": "false",
        //             "message": "Login failed! <br> Invalid Email, Mobile or Password."
        //         }
        //     }';
        // }
        
    }//End of POST

    //Logout method
    
}
