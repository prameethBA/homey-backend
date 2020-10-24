<?php

namespace Controllers;

use Exception;

use \Core\DB\DB as DB;
use Core\BaseController as BaseController;
use Models\User as User;

class Login extends BaseController {

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

    //Login method
    public function post() {
        if(isset($this->secureParams['Username']) && isset($this->secureParams['Password'])) {
            $username = $this->secureParams['Username'];
            $password = $this->secureParams['Password'];
        }
        else {
            http_response_code(400);
            die($reject  = '{
                "data":{
                    "message": "Invalid parameters."
                }
            }');
        }

        $stmt = DB::execute(User::get("(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));

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
                    "message": "Login Succesfull"
                }
            }';

            DB::update(['access_token' => $this->getToken(), "next" =>"val"], "user_id = {$result['user_id']}");
            
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
