<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Models/Login.php');
require_once('Models/User.php');
require_once('Models/Hash.php');
use Models\Login as Login;
use Models\User as User;
use Models\Hash as Hash;

class Signup extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Login();
        new User();
        new Hash();
    }

    public function get() {
        http_response_code(406);
            die($reject = '{
                "data": {
                    "message": "Invalid Request"
                }
            }');    
    }

    //SignUp method
    public function post() {
        if(isset( $this->secureParams['Firstname'], $this->secureParams['Lastname'], $this->secureParams['Email'],  $this->secureParams['Password'])) {
            $email = $this->secureParams['Email'];

            $stmt = Login::execute(Login::get("email = '{$email}'"));

            if ($stmt->rowCount() == 0) {
                $firstName = $this->secureParams['Firstname'];
                $lastName = $this->secureParams['Lastname'];
                $password = md5($this->secureParams['Password']); //Encrypt password
    
                echo $stmt = Login::execute(Login::save(['email' => $email, 'password' => $password]));
                echo $stmt = Login::execute(Login::get("email = '{$email}' AND password = '{$password}'"));
                echo $stmt = User::execute(User::save(['first_name' => $firstName, 'last_name' => $lastName]));
                echo $stmt = Hash::execute(Hash::save(['user_id' => $email, 'Hash' => $password]));
            } else {
                http_response_code(409);
                die($reject  = '{
                    "data":{
                        "message": "An account with the given email already exits."
                    }
                 }');
            }


        } else {
                http_response_code(400);
                die($reject  = '{
                    "data":{
                        "message": "Invalid parameters."
                    }
                }');
        }

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
    public function delete() {
        if(isset($this->params[0])) {
            $userId = $this->params[0];
            if(User::validateUser($userId)) {
                User::delete( "user-id = {$userId}");
                echo $resolve  = '{
                    "status":"200",
                    "data":{
                        message: "Logout Succesfull."
                    }
                }';
            } else {
                die($reject  = '{
                    "status":"500",
                    "data":{
                        "message": "Failed the log out procces."
                    }
                }');
            }
        }
        else {
            die($reject  = '{
                "status":"400",
                "data":{
                    "message": "Invalid parameters."
                }
            }');
        }
    }
}
