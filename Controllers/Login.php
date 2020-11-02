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
        $this->setAccessToken();

    }//End of POST

    //Logout method
    public function delete() {
        //Only clear the token when valid token was sent
        if($this->validateLoggedUser()) {
            $result = $this->state['result'];

            DB::exec(LoginModel::update(['access_token' => ''], "user_id = {$result['user_id']}"));  

            http_response_code(204);
            echo $resolve = '{
                "logout": "true",
                "message": "Succesfuly log out."
            }';
        } else {
            // unauthorized access attempts will be handle here +TODO
            http_response_code(200);
            die($reject  = '{
                "status": "400",
                "message": "Invalid request with invalid parameters."
            }');
        }
    }//End of DELETE


    // Private methods

    // Login method for non logged user
    private function userLogin() {
        if(isset($this->secureParams['userName'], $this->secureParams['password'])) {
            $userName = $this->secureParams['userName'];
            $password = md5($this->secureParams['password']);//Encode the password
            $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'user_status', 'user_type'], "(email='{$userName}' OR mobile='{$userName}') AND password='{$password}'"));
            if($stmt->rowCount() == 1) {
                $this->state['result'] = $stmt->fetch();
                return true;
            } elseif($stmt->rowCount() > 1) {
                http_response_code(200);
                die($reject = '{
                    "status": "500",
                    "login": "false",
                    "message": "Database error! Contact administration."
                }');
            } else {
                http_response_code(200);
                die($reject = '{
                    "status": "404",
                    "login": "false",
                    "message": "Login failed! <br> Invalid Email, Mobile or Password."
                }');
            }
        }
        else return false;

    }//End of userLogin()

    // validate already logged user with token and userName
    private function validateLoggedUser() {
        if(isset($this->secureParams['userId'], $this->secureParams['token'])) {
            $userId = $this->secureParams['userId'];
            $token = $this->secureParams['token'];
            $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'access_token', 'user_status', 'user_type'], "access_token='{$token}' AND user_id='{$userId}'"));
            if($stmt->rowCount() == 1) {
                $this->state['result'] = $stmt->fetch();
                return true;
            } elseif($stmt->rowCount() > 1) {
                http_response_code(200);
                die($reject = '{
                    "status": "500",
                    "action": "false",
                    "message": "Database error! Contact administration."
                }');
            } else {
                // unauthorized access attempts will be handle here +TODO
                http_response_code(200);
                die($reject = '{
                    "status": "404",
                    "action": "false",
                    "message": "Sign up before continue."
                }');
            }
        }
        else return false;
    }//End of the validateLoggedUser()
    
    private function checkUserStatus($userStatus) {
        switch($userStatus) {
            case 0: 
                http_response_code(200);
                die($reject = '{
                    "status": "401",
                    "action": "false",
                    "message": "Confirm the email address to activate account."
                }');
                break;

            case 1: 
                return true;
                break;

            default:
                die($reject = '{
                    "status": "401",
                    "action": "false",
                    "message": "Confirm the email address to activate account."
                }');
                break;
        }//End of switch
    }//End of checkUserStatus()

    private function setAccessToken() {
        if($this->validateLoggedUser() || $this->userLogin()) {
            $result = $this->state['result'];
            $payload = "{
                id: " . $result['user_id'] . ",
                email: '" . $result['email'] . "'
            }";

            $this->setToken($payload);

            DB::exec(LoginModel::update(['access_token' => $this->getToken()], "user_id = {$result['user_id']}"));  

            // Check the userStatus
            if($this->checkUserStatus($result['user_status'])) {
                http_response_code(201);
                echo $resolve = '{
                    "login": "true",
                    "userType": "' . $result['user_type'] . '",
                    "userId": "' . $result['user_id'] . '",
                    "token": "' . $this->getToken() . '",
                    "message": "Login Succesfull."
                }';
            }

        } else {
            // unauthorized access attempts will be handle here +TODO
            http_response_code(200);
            die($reject  = '{
                "status": "400",
                "message": "Invalid request with invalid parameters."
            }');
        }
    }
    


}//End of the class
