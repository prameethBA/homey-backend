<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class User extends Controller
{

    //get user Name by Id
    public function GetName($params)
    {
        try {
            $stmt = $this->execute($this->get('user', 'first_name as firstName, last_name as lastName', 'user_id=' . (int)$params[0]));
            $this->resolve(json_encode($stmt->fetch()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //count visitors
    public function CountNew()
    {
        try {
            $ip = $_SERVER['HTTP_CLIENT_IP'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['REMOTE_ADDR']);
            $this->execute($this->save('visitorcount', ['ip' => (string)$ip, 'detail' => "new user"]));
            $this->resolve('{"count":"incremented"}', 200);
        } catch (Exception $err) {
            $this->reject('{
              "status": "500",
              "error": "true",
              "message": "' . $err->getMessage() . '"
          }', 200);
        }
    }


    //SignUp method
    // public function post() {

    //     try {

    //         if(isset($this->params[0])) {
    //             switch ($this->params[0]) {
    //                 // deactivate a user
    //                 case 'deactivate':
    //                     if(!$this->authenticateAdmin($param['userId'], $param['token'])) throw 'Unauthorized request';
    //                     

    //                     break;

    //                     // activate a user
    //                 case 'activate':
    //                     if(!$this->authenticateAdmin($param['userId'], $param['token'])) throw 'Unauthorized request';
    //                     


    //                     break;

    //                 default:
    //                     $this->reject('{
    //                         "status": "400",
    //                         "signup": "false",
    //                         "message": "Invalid user type."
    //                     }',200);
    //                     //End of Default
    //             }//End of Switch
    //         } else throw "Invalid parameters";

    //     } catch (Exception $err) {
    //         $this->reject('{
    //             "status": "500",
    //             "error": "true",
    //             "message": "' . $err->getMessage() . '"
    //     }',200);
    // } //End of try catch

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

    // }//End of POST

    //Logout method

}
