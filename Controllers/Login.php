<?php

namespace Controllers;

// use PDO;
// use PDOException;
use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Models/User.php');
use Models\User as User;

class Login extends BaseController {

    public function __construct($params) {
        parent::__construct($params);
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
        // print_r($this->params);
        if(isset($this->params[0]) && isset($this->params[1])) {
            $username = $this->params[0];
            $password = $this->params[1];
        }
        else {
            die($reject  = "{
                status:400,
                data:{
                    message: 'Invalid parameters.'
                }
            }");
        }

        $stmt = User::execute(User::get("(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));

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

            User::update(['access_token' => $this->getToken(), "next" =>"val"], "user_id = {$result['user_id']}");
            
        } else {
            http_response_code(404);
            echo $reject = '{
                "data": {
                    "login": "false",
                    "message": "Login failed.User Not found"
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
