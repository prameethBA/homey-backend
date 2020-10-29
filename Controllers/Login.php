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
                ' . json_encode($stmt->fetchAll()) . '
            }
            ';

        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                "status": "500",
                "login": "false",
                "message": "' . $err->getMessage() . '"
            }');
        }
            
    }

    //Login method
    public function post() {
        if(isset($this->secureParams['userName']) && isset($this->secureParams['password'])) {
            $userName = $this->secureParams['userName'];
            $password = md5($this->secureParams['password']);//Encode the password
            $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'access_token', 'user_status'], "(email='{$userName}' OR mobile='{$userName}') AND password='{$password}'"));
            if($stmt->rowCount() == 1) {
                $result = $stmt->fetch();
                $payload = "{
                    id: " . $result['user_id'] . ",
                    email: '" . $result['email'] . "'
                }";

                $this->setToken($payload);
                http_response_code(201);
                echo $resolve = '{
                    "login": "true",
                    "token": "' . $this->getToken() . '",
                    "message": "Login Succesfull."
                }';
    
                DB::exec(LoginModel::update(['access_token' => $this->getToken()], "user_id = {$result['user_id']}"));
                
            } elseif($stmt->rowCount() > 1) {
                http_response_code(200);
                echo $reject = '{
                    "status": "500",
                    "login": "false",
                    "message": "Database error! Contact administration."
                }';
            } else {
                http_response_code(200);
                echo $reject = '{
                    "status": "404",
                    "login": "false",
                    "message": "Login failed! <br> Invalid Email, Mobile or Password."
                }';
            }
        }
        else {
            http_response_code(200);
            die($reject  = '{
                "status": "400",
                "message": "Invalid parameters."
            }');
        }

    }//End of POST

    //Logout method
    public function delete() {
        if(isset($this->params[0])) {
            $userId = $this->params[0];
            if(LoginModel::validateUser($userId)) {
                LoginModel::delete( "user-id = {$userId}");
                http_response_code(200);
                echo $resolve  = '{
                    "logout": "true",
                    "message": "Logout Succesfull."
                }';
            } else {
                http_response_code(200);
                die($reject  = '{
                    "status":"500",
                    "message": "Failed the perform the log out procces."
                }');
            }
        }
        else {
            http_response_code(200);
            die($reject  = '{
                "status":"400",
                "message": "Invalid parameters."
            }');
        }
    }
}
