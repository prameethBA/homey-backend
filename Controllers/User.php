<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');
use Core\Controller as Controller;

class User extends Controller {

    //SignUp method
    public function post() {

        try {

            if(isset($this->params[0])) {
                switch ($this->params[0]) {
                    // deactivate a user
                    case 'deactivate':
                        if(!$this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) throw 'Unauthorized request';
                        $this->execute($this->update('login',['user_status' =>  2/*2 for blocked*/],'user_id = ' . $this->params['1']));
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
                        $this->execute($this->update('update',['user_status' =>  1/*1 for activate*/],'user_id = ' . $this->params['1']));
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

        // $stmt = User::execute($this->get('user',"(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));

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

        //     $this->update('user',['access_token' => $this->getToken(), "next" =>"val"], "user_id = {$result['user_id']}");
            
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
