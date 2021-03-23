<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Signup extends Controller
{


    public function User($a, $param)
    {
        try {

            $email = $param['email'];

            $stmt = $this->execute($this->get('login', 'user_id', "email = '{$email}'"));

            if ($stmt->rowCount() == 0) {
                $firstName = $param['firstName'];
                $lastName = $param['lastName'];
                $password = md5($param['password']); //Encrypt password

                $stmt = $this->execute($this->save('login', ['email' => $email, 'password' => $password]));
                $stmt = $this->execute($this->get('login', 'user_id', "email = '{$email}' AND password = '{$password}'"));
                $userId = (int)($stmt->fetch())['user_id'];
                $stmt = $this->execute($this->save('user', ['user_id' => $userId, 'first_name' => $firstName, 'last_name' => $lastName]));
                $hash = (string)sha1(md5(time() . $this->uniqueKey("HASH"))); //Generate hash value for user confirmation
                $stmt = $this->execute($this->save('hash', ['user_id' => $userId, 'hash' => $hash]));

                $this->addLog($userId . " user account created.", "new-user-created");

                $subject = "Homey - Activate account.";
                $message = "Data base error.";

                //include confirmation mail file
                include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/email-confirmation.php');

                if (!$this->sendMail($email, $subject, $message)) {
                    http_response_code(202);
                    $this->addLog($userId . " email sending failed.", "new-user-confirmation-mail-failed", "Confirmation email sending failed");

                    die($reject  = '{
                        "signup": "true",
                        "message": "User account succesfully created. But confirmation email sent was failed. Try again later with email <b>' . $email . '<b> ."
                    }');
                }

                http_response_code(201);
                echo $resolve  = '{
                    "signup": "true",
                    "message": "User account succesfully created. An email was sent to <b>' . $email . '<b> ."
                }';
                $this->addLog($userId . " confirmation mail sent to " . $email, "confirmation-mail-sent");
            } else {
                http_response_code(200);
                $this->addLog($email . " already exists", "email-exists", 'Attepmt to sign up for existing email address');

                die($reject  = '{
                    "status": "409",
                    "signup": "false",
                    "message": "An account with the given email already exits."
                }');
            }
        } catch (Exception $err) {
            $this->addLog("A signup attempt failed", "signup-attepmt-failed", (string)$err->getMessage());
            $this->reject('{
                "status": "500",
                "signup": "false",
                "message": "' . $err->getMessage() . '"
            }', 200);
        }
    } //End of User()

    // //SignUp method
    // public function post() {

    //     if(isset($this->params[0])) {
    //         switch ($this->params[0]) {
    //             // signup a user
    // case 'user':
    //if(isset( $param['firstName'], $param['lastName'], $param['email'],  $param['password'])) {

    //                 break;//End of signup method for User

    //             //signup a admin
    //             case 'admin':
    //                 if(isset( $param['Firstname'], $param['Lastname'], $param['Email'],  $param['Password'], $param['Nic'])) {
    //                     $email = $param['Email'];
    //                     $stmt = DB::execute(Login::get('user_id', "email = '{$email}'"));

    //                     if ($stmt->rowCount() == 0) {
    //                         $firstName = $param['Firstname'];
    //                         $lastName = $param['Lastname'];
    //                         $nic = $param['Nic'];
    //                         $password = md5($param['Password']); //Encrypt password

    //                         $stmt = DB::execute(Login::save(['email' => $email, 'password' => $password]));
    //                         $stmt = DB::execute(Login::get('user_id', "email = '{$email}' AND password = '{$password}'"));
    //                         $userId = ($stmt->fetch())['user_id'];
    //                         $stmt = DB::execute(Admin::save(['user_id' => $userId, 'first_name' => $firstName, 'last_name' => $lastName, 'nic' => $nic]));
    //                         $stmt = DB::execute(Hash::save(['user_id' => $userId, 'Hash' => $password]));

    //                         http_response_code(201);
    //                         echo $resolve  = '{
    //                             "signup": "true",
    //                             "message": "User account succesfully created."
    //                             }';

    //                     } else {
    //                         http_response_code(200);
    //                         die($reject  = '{
    //                             "signup": "false",
    //                             "message": "An account with the given email already exits."
    //                         }');
    //                     }
    //                 } else {
    //                     http_response_code(406);
    //                     die($reject  = '{
    //                         "signup": "false",
    //                         "message": "Invalid parameters."
    //                     }');
    //                 }

    //                 break;//End of signup method for User

    //             case 'confirm':
    //                 if(isset( $param['hash'], $param['userId'])) {
    //                     $hash = $param['hash'];
    //                     $userId = (int)base64_decode($param['userId']);//derive userId

    //                     $stmt = DB::execute(Hash::get('user_id', "user_id = '{$userId}' AND hash = '{$hash}'"));

    //                     if ($stmt->rowCount() == 1) {
    //                         $stmt = DB::execute(Hash::delete("user_id = {$userId}"));
    //                         $stmt = DB::execute(Login::get(['user_status'], ("user_id = {$userId}")));
    //                         $result = $stmt->fetch();
    //                         if($result['user_status'] == 0) {
    //                             $stmt = DB::execute(Login::update(['user_status' => 1], ("user_id = {$userId}")));
    //                             http_response_code(201);
    //                             echo $resolve  = '{
    //                                 "signup": "true",
    //                                 "message": "User account succesfully Activated."
    //                                 }';
    //                         } else {
    //                             http_response_code(200);
    //                             die($reject  = '{
    //                                 "status": "403",
    //                                 "signup": "false",
    //                                 "message": "Banned user!"
    //                             }');
    //                         }
    //                     } else {
    //                         http_response_code(203);
    //                         die($reject  = '{
    //                             "status": "203",
    //                             "signup": "false",
    //                             "message": "Invalid request or Link has been expired."
    //                         }');
    //                     }
    //                 } else {
    //                     http_response_code(200);
    //                     die($reject  = '{
    //                         "status": "406",
    //                         "signup": "false",
    //                         "message": "Invalid parameters."
    //                     }');
    //                 }
    //                 break;

    //             default:
    //                 http_response_code(200);
    //                 die($reject  = '{
    //                     "status": "400",
    //                     "signup": "false",
    //                     "message": "Invalid user type."
    //                 }');
    //                 //End of Default
    //         }//End of Switch
    //     } else {

    //             http_response_code(200);
    //             die($reject  = '{
    //                 "status": "400",
    //                 "signup": "false",
    //                 "message": "Invalid parameters."
    //             }');

    //     }//End of POST

    //     // $stmt = User::execute(User::get("(email='{$username}' OR mobile='{$username}') AND password='{$password}'"));

    //     // if($stmt->rowCount() == 1) {
    //     //     $result = $stmt->fetch();
    //     //     $payload = "{
    //     //         id: " . $result['user_id'] . ",
    //     //         email: '" . $result['email'] . "'
    //     //     }";
    //     //     $this->setToken($payload);
    //     //     http_response_code(201);
    //     //     echo $resolve = '{
    //     //         "data" : {
    //     //             "login": "true",
    //     //             "token": "' . $this->getToken() . '",
    //     //             "message": "Login Succesfull"
    //     //         }
    //     //     }';

    //     //     User::update(['access_token' => $this->getToken(), "next" =>"val"], "user_id = {$result['user_id']}");

    //     // } else {
    //     //     http_response_code(404);
    //     //     echo $reject = '{
    //     //         "data": {
    //     //             "login": "false",
    //     //             "message": "Login failed! <br> Invalid Email, Mobile or Password."
    //     //         }
    //     //     }';
    //     // }

    // }//End of POST

    // //Logout method
    // public function delete() {
    //     if(isset($this->params[0])) {
    //         $userId = $this->params[0];
    //         if(User::validateUser($userId)) {
    //             User::delete( "user-id = {$userId}");
    //             echo $resolve  = '{
    //                 "status":"200",
    //                 "data":{
    //                     message: "Logout Succesfull."
    //                 }
    //             }';
    //         } else {
    //             die($reject  = '{
    //                 "status":"500",
    //                 "data":{
    //                     "message": "Failed the log out procces."
    //                 }
    //             }');
    //         }
    //     }
    //     else {
    //         http_response_code(200);
    //         die($reject  = '{
    //             "status":"400",
    //             "message": "Invalid parameters."
    //         }');
    //     }
    // }
}
