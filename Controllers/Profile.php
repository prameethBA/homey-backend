<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Profile extends Controller
{

    public function GetInfo($a, $param)
    {
        try {
            $userId = (int)$param['userId'];
            $token = (string)$param['token'];
            if (!$this->authenticateUser($token, $userId)) throw new Exception("Authentication failed.");
            $stmt = $this->execute($this->get('login', ['email', 'mobile', 'updated as lastLogin'], ("user_id = '{$userId}'")));
            $authData = json_encode($stmt->fetch());
            $stmt = $this->execute($this->get('user', [
                'first_name as firstName',
                'last_name as lastName',
                'address1',
                'address2',
                'address3',
                'city',
                'district',
                'dob',
                'nic'
            ], ("user_id = '{$userId}'")));
            $userData = json_encode($stmt->fetch());


            $this->resolve('{
                            "authData": ' . $authData . ',
                            "userData": ' . $userData . '
                        }', 200);
        } catch (Exception $err) {
            $this->reject('{
                        "status": "500",
                        "error": "true",
                        "message": "' . $err->getMessage() . '"
                    }', 200);
        }
    }

    //Update Profile
    public function UpdateProfile($params, $param)
    {
        try {
            $userId = (int)$param['userId'];
            $token = (string)$param['token'];
            if (!$this->authenticateUser($token, $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->get("login", 'COUNT(mobile) as count', "mobile ='{$param['mobile']}' AND NOT user_id=" . $userId));
            if ($stmt->fetch()['count'] >= 1) throw new Exception("Another account owns this mobile number");

            $stmt = $this->execute($this->get("login", 'COUNT(email) as count', "email ='{$param['email']}' AND NOT user_id=" . $userId));
            $mailCount = $stmt->fetch()['count'];
            if ($mailCount >= 1) throw new Exception("Another account owns this newly entered Email number");
            else if ($mailCount == 1) {
                //include confirmation mail file
                include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/email-confirmation.php');

                if (!$this->sendMail($email, $message, $subject)) {
                    $this->addLog($userId . " email sending failed.", "confirmation-mail-on-mail-update-failed", "Confirmation email sending failed");
                    $this->reject('{
                        "signup": "true",
                        "message": "Confirmation email sent was failed. Try again later with email <b>' . $email . '<b> ."
                    }', 202);
                } else {
                    $this->addLog($userId . " confirmation mail sent to " . $param['email'], "confirmation-mail-sent-on-mail-update");
                    $result['emailUpdate'] = true;
                }
            }


            $loginData = [
                'email' => $param['email'],
                'mobile' => $param['mobile'] == '' ? NULL : $param['mobile'],
            ];


            $userData = [
                'first_name' => $param['firstName'],
                'last_name' => $param['lastName'],
                'nic' => $param['nic'],
                'address1' => $param['address1'],
                'address2' => $param['address2'],
                'address3' => $param['address3'],
                'city' => $param['city'],
                'district' => $param['district'],
                'dob' => $param['dob'],
            ];

            //update user status to unconfirm if user changed email address
            if ($result['emailUpdate'])
                $this->execute($this->update('login', ['user_status' => 0], ("user_status = '{$userId}'")));

            $this->execute($this->update('login', $loginData, ("user_id = '{$userId}'")));
            $this->execute($this->update('user', $userData, ("user_id = '{$userId}'")));

            if ($result['emailUpdate'])
                $this->resolve('{
                                "message" : "Profile update successfully.You have change the email address and you need to confirm the email address to continue with the system."
                            }', 201);
            else $this->resolve('{
                                "message" : "Profile update successfully"
                            }', 201);
        } catch (Exception $err) {
            $this->reject('{
                            "status": "500",
                            "error": "true",
                            "message": "' . $err->getMessage() . '"
                    }', 200);
        }
    }


    // validate mobile
    public function ValidateMobile($a, $param)
    {
        try {
            $userId = (int)$param['userId'];
            $token = (string)$param['token'];
            if (!$this->authenticateUser($token, $userId)) throw new Exception("Authentication failed.");
            $stmt = $this->execute($this->get('login', 'mobile', ("user_id = '{$userId}'")));
            $mobile = $stmt->fetch()['mobile'];
            if ($mobile != NULL) {
                $resolve = '{
                                "action":"true",
                                "mobile":"' . $mobile . '",
                                "message" : "Mobile number updated"
                            }';
            } else {
                $resolve = '{
                                "action":"false",
                                "message" : "Mobile not number updated"
                            }';
            }
            $this->resolve($resolve, 200);
        } catch (Exception $err) {
            $this->reject('{
                            "status": "500",
                            "error": "true",
                            "message": "' . $err->getMessage() . '"
                    }', 200);
        }
    }

    // login logs
    public function GetLoginLogs($a, $param)
    {
        try {
            $userId = (int)$param['userId'];
            $token = (string)$param['token'];
            if (!$this->authenticateUser($token, $userId)) throw new Exception("Authentication failed.");
            $stmt = $this->execute($this->get('logs', '*', ("message LIKE '{$userId} logged in.' AND type LIKE 'login-%' ORDER BY created DESC LIMIT 10")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
                             "status": "500",
                             "error": "true",
                             "message": "' . $err->getMessage() . '"
                     }', 200);
        }
    }

    // public function post()
    // {
    //     try {
    //         if (isset($params[0])) {
    //             if (!$this->authenticate()) throw new Exception("Unautherized request.");
    //             switch ($params[0]) {
    //                 case 'info':


    //                     break;

    //                 case 'update':

    //                     $loginData = [
    //                         'email' => $param['email'],
    //                         'mobile' => $param['mobile'] == '' ? NULL : $param['mobile'],
    //                     ];

    //                     $userData = [
    //                         'first_name' => $param['firstName'],
    //                         'last_name' => $param['lastName'],
    //                         'nic' => $param['nic'],
    //                         'address1' => $param['address1'],
    //                         'address2' => $param['address2'],
    //                         'address3' => $param['address3'],
    //                         'city' => $param['city'],
    //                         'district' => $param['district'],
    //                         'dob' => $param['dob'],
    //                     ];

    //                     $stmt = $this->execute($this->update('login', $loginData, ("user_id = '{$userId}'")));
    //                     $stmt = $this->execute($this->update('user', $userData, ("user_id = '{$userId}'")));

    //                     $this->resolve('{
    //                         "message" : "Profile update successfully"
    //                     }', 201);

    //                     break;

    //                 case 'validate':
    //                     switch ($params[1]) {
    //                         case 'mobile':
    //                             $stmt = $this->execute($this->get('login', 'mobile', ("user_id = '{$userId}'")));
    //                             $mobile = $stmt->fetch()['mobile'];
    //                             if ($mobile != NULL) {
    //                                 $resolve = '{
    //                                         "action":"true",
    //                                         "mobile":"' . $mobile . '",
    //                                         "message" : "Mobile number updated"
    //                                     }';
    //                             } else {
    //                                 $resolve = '{
    //                                         "action":"false",
    //                                         "message" : "Mobile not number updated"
    //                                     }';
    //                             }
    //                             $this->resolve($resolve, 200);
    //                             break;
    //                     }
    //                     break;

    //                 default:
    //                     throw new Exception("Invalid Request");
    //             }
    //         } else throw new Exception("Invalid Parmeters");
    //     } catch (Exception $err) {
    //         $this->reject('{
    //             "status": "500",
    //             "error": "true",
    //             "message": "' . $err->getMessage() . '"
    //     }', 200);
    //     } //End of try catch

    // } //End of post

    // // Authenticate User 
    // private function authenticate()
    // {
    //     if (isset($userId, $param['token'])) {
    //         if ($this->authenticateUser($userId, $param['token'])) return true;
    //         else return false;
    //     } else return false;
    // } //end of authenticateUser()

}//End of Class
