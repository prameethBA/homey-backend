<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Models/Login.php');
use Models\Login as Login;
require_once('Models/User.php');
use Models\User as User;
require_once('Models/Admin.php');
use Models\Admin as Admin;
require_once('Models/ConfirmationInfo.php');
use Models\ConfirmationInfo as Hash;
require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Signup extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Login();
        new User();
        new Admin();
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

        if(isset($this->params[0])) {
            switch ($this->params[0]) {
                case 'user':
                    if(isset( $this->secureParams['Firstname'], $this->secureParams['Lastname'], $this->secureParams['Email'],  $this->secureParams['Password'])) {
                        $email = $this->secureParams['Email'];
                        $stmt = DB::execute(Login::get('user_id', "email = '{$email}'"));
            
                        if ($stmt->rowCount() == 0) {
                            $firstName = $this->secureParams['Firstname'];
                            $lastName = $this->secureParams['Lastname'];
                            $password = md5($this->secureParams['Password']); //Encrypt password
                
                            $stmt = DB::execute(Login::save(['email' => $email, 'password' => $password]));
                            $stmt = DB::execute(Login::get('user_id', "email = '{$email}' AND password = '{$password}'"));
                            $userId = ($stmt->fetch())['user_id'];
                            $stmt = DB::execute(User::save(['user_id' => $userId, 'first_name' => $firstName, 'last_name' => $lastName]));
                            $stmt = DB::execute(Hash::save(['user_id' => $userId, 'Hash' => $password]));

                            http_response_code(201);
                            echo $resolve  = '{
                                "signup": "true"
                                "data":{
                                    "message": "User account succesfully created."
                                }
                             }';

                        } else {
                            http_response_code(409);
                            die($reject  = '{
                                "signup" = "false"
                                "data":{
                                    "message": "An account with the given email already exits."
                                }
                            }');
                        }
                    }
                    break;//End of signup method for User

                case 'admin':
                    if(isset( $this->secureParams['Firstname'], $this->secureParams['Lastname'], $this->secureParams['Email'],  $this->secureParams['Password'], $this->secureParams['Nic'])) {
                        $email = $this->secureParams['Email'];
                        $stmt = DB::execute(Login::get('user_id', "email = '{$email}'"));
            
                        if ($stmt->rowCount() == 0) {
                            $firstName = $this->secureParams['Firstname'];
                            $lastName = $this->secureParams['Lastname'];
                            $nic = $this->secureParams['Nic'];
                            $password = md5($this->secureParams['Password']); //Encrypt password
                
                            $stmt = DB::execute(Login::save(['email' => $email, 'password' => $password]));
                            $stmt = DB::execute(Login::get('user_id', "email = '{$email}' AND password = '{$password}'"));
                            $userId = ($stmt->fetch())['user_id'];
                            $stmt = DB::execute(Admin::save(['user_id' => $userId, 'first_name' => $firstName, 'last_name' => $lastName, 'nic' => $nic]));
                            $stmt = DB::execute(Hash::save(['user_id' => $userId, 'Hash' => $password]));

                            http_response_code(201);
                            echo $resolve  = '{
                                "signup": "true"
                                "data":{
                                    "message": "User account succesfully created."
                                }
                                }';

                        } else {
                            http_response_code(409);
                            die($reject  = '{
                                "signup" = "false"
                                "data":{
                                    "message": "An account with the given email already exits."
                                }
                            }');
                        }
                    }
                    break;//End of signup method for User

                default:
                    http_response_code(400);
                    die($reject  = '{
                        "signup" = "false"
                        "data":{
                            "message": "Invalid user type."
                        }
                    }');
                    //End of Default
            }//End of Switch
        } else {

                http_response_code(400);
                die($reject  = '{
                    "signup" = "false"
                    "data":{
                        "message": "Invalid parameters."
                    }
                }');

        }//End of POST

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
