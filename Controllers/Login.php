<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Login.php');
use Models\Login as LoginModel;
require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Login extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new LoginModel();
    }

    public function get() {
        try {
            $stmt = LoginModel::execute(LoginModel::getAll(['user_id', 'email', 'mobile']));
            
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

    //Login method
    public function post() {
        if(isset($this->secureParams['Username']) && isset($this->secureParams['Password'])) {
            $username = $this->secureParams['Username'];
            $password = md5($this->secureParams['Password']);//Encode the password
            $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'access_token', 'user_status'], "(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));
            if($stmt->rowCount() == 1) {
                $result = $stmt->fetch();
                $payload = "{
                    id: " . $result['user_id'] . ",
                    email: '" . $result['email'] . "'
                }";
                $this->setToken($payload);
                http_response_code(201);
                echo $resolve = '{
                    "data" : {
                        "login": "true",
                        "token": "' . $this->getToken() . '",
                        "message": "Login Succesfull."
                    }
                }';
    
                DB::exec(LoginModel::update(['access_token' => $this->getToken()], "user_id = {$result['user_id']}"));
                
            } elseif($stmt->rowCount() > 1) {
                http_response_code(500);
                echo $reject = '{
                    "data": {
                        "login": "false",
                        "message": "Database error! Contact administration."
                    }
                }';
            } else {
                http_response_code(404);
                echo $reject = '{
                    "data": {
                        "login": "false",
                        "message": "Login failed! <br> Invalid Email, Mobile or Password."
                    }
                }';
            }
        }
        else {
            http_response_code(400);
            die($reject  = '{
                "data":{
                    "message": "Invalid parameters."
                }
            }');
        }

    }//End of POST

    //Logout method
    public function delete() {
        if(isset($this->params[0])) {
            $userId = $this->params[0];
            if(LoginModel::validateUser($userId)) {
                LoginModel::delete( "user-id = {$userId}");
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
