<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Models/User.php');
use Models\User as User;

class Signup extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new User();
    }

    public function get() {
        try {
            $stmt = User::execute(User::getAll(['user_id', 'email', 'mobile']));
            
            http_response_code(200);
            echo $resolve = '{
                "data": {
                    ' . json_encode($stmt->fetchAll()) . '
                }
            }
            ';

        } catch(Exception $err) {
            http_response_code(500);
            die($reject = '{
                "data": {
                    "login": "false",
                    "message": "' . $err->getMessage() . '"
                }
            }');
        }
            
    }

    //SignUp method
    public function post() {
        if(isset( $this->secureParams['Firstname'], $this->secureParams['Lastname'], $this->secureParams['Email'],  $this->secureParams['Password'])) {
            $firstName = $this->secureParams['Firstname'];
            $lastName = $this->secureParams['Lastname'];
            $email = $this->secureParams['Email'];
            $password = $this->secureParams['Password'];

           $stmt = User::execute(User::save(['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'password' => $password]));

        } else {
            die($reject  = '{
                "status":"400",
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
