<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;


class Login extends Controller
{

    public function GetAllUsers()
    {
        try {
            $stmt = $this->execute($this->get('login', ['user_id', 'email', 'mobile']));
            $this->resolve(json_encode($stmt->fetchAll()));
        } catch (Exception $err) {
            $this->reject('{
                "status": "500",
                "login": "false",
                "message": "' . $err->getMessage() . '"
            }');
        }
    }


    public function RequestLogin($a, $param)
    {
        try {
            $userName = $param['userName'];
            $password = md5($param['password']); //Encode the password
            $stmt = $this->execute($this->get(
                'login',
                [
                    'user_id as userId',
                    'email',
                    'user_status as userStatus',
                    'user_type as userType'
                ],
                "(email='{$userName}' OR mobile='{$userName}') AND password='{$password}' AND login_attempt <= 5"
            ));
            $rows = $stmt->rowCount();
            $result = $stmt->fetch();
            if ($rows == 1) {
                //Check wheather user able to login the system
                switch ($result['userStatus']) {
                    case '0':
                        $this->reject('{
                            "status": "401",
                            "signup": "false",
                            "message": "Confirm the email before login"
                        }', 200);
                        break;
                    case '2':
                        $this->reject('{
                                "status": "403",
                                "signup": "false",
                                "message": "User temporarily blocked by admin"
                            }', 200);
                        break;
                    case '3':
                        $this->reject('{
                                    "status": "403",
                                    "signup": "false",
                                    "message": "User permanently banned"
                                }', 200);
                        break;
                    case '4':
                        $this->reject('{
                                        "status": "401",
                                        "signup": "false",
                                        "message": "Confirm the email before login.As admin requested."
                                    }', 200);
                        break;
                    default:
                        //No default
                        break;
                }
                $payload = "{id:" . $result['userId'] . ",email:'" . $result['email'] . "'}";

                $this->setToken($payload, $result['userId']);

                $result['token'] = $this->getToken();
                $result['login'] = true;

                $this->resolve(json_encode($result), 200);
                $this->addLog($result['userId'] . " logged in.", "login-success");
            } elseif ($stmt->rowCount() > 1) {
                $this->addLog($userName . " loging failed.", "login-failed", "Database error occured; duplicated entries found.");
                $this->reject('{
                    "status": "500",
                    "login": "false",
                    "message": "Database error! Contact administration."
                }', 200);
            } else {
                // $this->exec($this->update("login", ['login_attempt' => 'login_attempt + 1'], "email='{$userName}' OR mobile='{$userName}"));
                $this->addLog($userName . " loging failed.", "invalid-login", "Login failed! due to Invalid Email, Mobile, Password or Blocked");
                $this->reject('{
                    "status": "404",
                    "login": "false",
                    "message": "Login failed! <br> Invalid Email, Mobile, Password"
                }', 200);
            }
        } catch (Exception $err) {
            $this->addLog("Login attempt failed", "login-attepmt-failed", (string)$err->getMessage());
            $this->reject('{
                "status": "500",
                "login": "false",
                "message": "' . $err->getMessage() . '"
            }', 200);
        }
    } //End of request Login

    //Change password
    public function changePassword($a, $param)
    {
        try {
            $userId = (int)$param['userId'];
            $token = (string)$param['token'];
            if (!$this->authenticateUser($token, $userId)) throw new Exception("Authentication failed.");
            $password = md5($param['old']);
            $newPassword = (string)md5($param['new']);
            $stmt = $this->execute($this->get('login', 'password', "user_id='" . $userId . "' AND password = '{$password}'"));
            if ($stmt->rowCount() != 1) throw new Exception("Current password is invalid!");
            $stmt = $this->execute($this->update('login', ['password' => $newPassword], ("user_id = '{$userId}'")));
            $this->resolve('{
                                        "message": "Password has been changed."
                                    }', 201);
            $this->addLog($userId . " has changed the password.", "password-change-success");
        } catch (Exception $err) {
            $this->addLog(" Atempt of password changing failed.", "password-change-failed", (string)$err->getMessage());
            $this->reject('{
                            "status": "500",
                            "error": "true",
                            "message": "' . $err->getMessage() . '"
                    }', 200);
        }
    }

    //     //Login method
    //     public function post()
    //     {
    //         $this->setAccessToken();
    //     } //End of POST

    //     //patch
    //     public function patch()
    //     {
    //         try {
    //             if (isset($this->params[0])) {
    //                 if (!$this->authenticate()) throw new Exception("Unauthorized request.");
    //                 switch ($this->params[0]) {
    //                     case 'password':
    //                         $param['password'] = $param['old'];
    //                         $param['userName'] = $param['email'];
    //                         $newPassword = md5($param['new']);
    //                         if (!$this->userLogin()) throw new Exception("Current password is invalid!");
    //                         $stmt = static::execute(static::update('login', ['password' => $newPassword], ("user_id = '{$param['userId']}'")));
    //                         http_response_code(201);
    //                         echo $resolve = '{
    //                             "message": "Password has been changed."
    //                         }';
    //                         break;

    //                         // case 'update':
    //                         //     break;

    //                     default:
    //                         throw new Exception("Invalid Request");
    //                 }
    //             } else throw new Exception("Invalid Parmeters");
    //         } catch (Exception $err) {
    //             http_response_code(200);
    //             die($reject = '{
    //                     "status": "500",
    //                     "error": "true",
    //                     "message": "' . $err->getMessage() . '"
    //             }');
    //         } //End of try catch
    //     } //End of patch

    //     //Logout method
    //     public function delete()
    //     {
    //         //Only clear the token when valid token was sent
    //         if ($this->validateLoggedUser()) {
    //             $result = $this->state['result'];

    //             DB::exec(LoginModel::update(['access_token' => ''], "user_id = {$result['user_id']}"));

    //             http_response_code(204);
    //             echo $resolve = '{
    //                 "logout": "true",
    //                 "message": "Succesfuly log out."
    //             }';
    //         } else {
    //             // unauthorized access attempts will be handle here +TODO
    //             http_response_code(200);
    //             die($reject  = '{
    //                 "status": "400",
    //                 "message": "Invalid request with invalid parameters."
    //             }');
    //         }
    //     } //End of DELETE


    //     // Private methods

    //     // Authenticate User 
    //     private function authenticate()
    //     {
    //         if (isset($param['userId'], $param['token'])) {
    //             if ($this->authenticateUser($param['userId'], $param['token'])) return true;
    //             else return false;
    //         } else return false;
    //     } //end of authenticateUser()

    //     // Login method for non logged user
    //     private function userLogin()
    //     {
    //         if (isset($param['userName'], $param['password'])) {
    //             $userName = $param['userName'];
    //             $password = md5($param['password']); //Encode the password
    //             $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'user_status', 'user_type'], "(email='{$userName}' OR mobile='{$userName}') AND password='{$password}'"));
    //             if ($stmt->rowCount() == 1) {
    //                 $this->state['result'] = $stmt->fetch();
    //                 return true;
    //             } elseif ($stmt->rowCount() > 1) {
    //                 http_response_code(200);
    //                 die($reject = '{
    //                     "status": "500",
    //                     "login": "false",
    //                     "message": "Database error! Contact administration."
    //                 }');
    //             } else {
    //                 http_response_code(200);
    //                 die($reject = '{
    //                     "status": "404",
    //                     "login": "false",
    //                     "message": "Login failed! <br> Invalid Email, Mobile or Password."
    //                 }');
    //             }
    //         } else return false;
    //     } //End of userLogin()

    //     // validate already logged user with token and userName
    //     private function validateLoggedUser()
    //     {
    //         if (isset($param['userId'], $param['token'])) {
    //             $userId = $param['userId'];
    //             $token = $param['token'];
    //             $stmt = DB::execute(LoginModel::get(['user_id', 'email', 'access_token', 'user_status', 'user_type'], "access_token='{$token}' AND user_id='{$userId}'"));
    //             if ($stmt->rowCount() == 1) {
    //                 $this->state['result'] = $stmt->fetch();
    //                 return true;
    //             } elseif ($stmt->rowCount() > 1) {
    //                 http_response_code(200);
    //                 die($reject = '{
    //                     "status": "500",
    //                     "action": "false",
    //                     "message": "Database error! Contact administration."
    //                 }');
    //             } else {
    //                 // unauthorized access attempts will be handle here +TODO
    //                 http_response_code(200);
    //                 die($reject = '{
    //                     "status": "404",
    //                     "action": "false",
    //                     "message": "Sign up before continue."
    //                 }');
    //             }
    //         } else return false;
    //     } //End of the validateLoggedUser()

    //     private function checkUserStatus($userStatus)
    //     {
    //         switch ($userStatus) {
    //             case 0:
    //                 http_response_code(200);
    //                 die($reject = '{
    //                     "status": "401",
    //                     "action": "false",
    //                     "message": "Confirm the email address to activate account."
    //                 }');
    //                 break;

    //             case 1:
    //                 return true;
    //                 break;

    //             default:
    //                 die($reject = '{
    //                     "status": "401",
    //                     "action": "false",
    //                     "message": "Confirm the email address to activate account."
    //                 }');
    //                 break;
    //         } //End of switch
    //     } //End of checkUserStatus()

    //     private function setAccessToken()
    //     {
    //         if ($this->validateLoggedUser() || $this->userLogin()) {
    //             $result = $this->state['result'];
    //             $payload = "{
    //                 id: " . $result['user_id'] . ",
    //                 email: '" . $result['email'] . "'
    //             }";

    //             $this->setToken($payload,$result['user_id']);

    //             DB::exec(LoginModel::update(['access_token' => $this->getToken()], "user_id = {$result['user_id']}"));
    //             $stmt = DB::execute(LoginModel::join(
    //                 [
    //                     'login.user_id as userId',
    //                     'login.email as email',
    //                     'login.access_token as token',
    //                     'login.user_status as status',
    //                     'login.user_type as userType',
    //                     'user.first_name as firstName',
    //                     'user.last_name  as lastName',
    //                 ],
    //                 "INNER JOIN user ON login.user_id = user.user_id WHERE login.user_id={$result['user_id']}"
    //             ));
    //             // Check the userStatus
    //             if ($this->checkUserStatus($result['user_status'])) {
    //                 http_response_code(201);
    //                 echo $resolve = '{
    //                     "login": "true",
    //                     "userData": ' . json_encode($stmt->fetch()) . ',
    //                     "token": "' . $this->getToken() . '",
    //                     "message": "Login Succesfull."
    //                 }';
    //             }
    //         } else {
    //             // unauthorized access attempts will be handle here +TODO
    //             http_response_code(200);
    //             die($reject  = '{
    //                 "status": "400",
    //                 "message": "Invalid request with invalid parameters."
    //             }');
    //         }
    //     }
}//End of the class
