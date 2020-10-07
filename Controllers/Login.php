<?php

namespace Controllers;

// use PDO;
// use PDOException;
require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

class Login extends BaseController{

    public function get() {
        $stmt = $this->conn->getAll('user');

        
        if($stmt->rowCount() == 1) {
            echo $resolve = "{
                status: 200,
                data : {
                    login: true,
                    token: USER_TOKEN,
                    message: 'Login Succesfull'
                }
            }";
        } else {
            echo $reject = "{
                status: 404,
                data: {
                    login: false,
                    message: 'Login failed.User Not found'
                }
            }";
        }
    }

    public function post() {
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

        $stmt = $this->conn->get('user',"(email='{$username}' OR mobile='{$username}') AND password='{$password}'");

        if($stmt->rowCount() == 1) {
            $result = $stmt->fetch();
            $payload = "{
                id: " . $result['user_id'] . ",
                email: '" . $result['email'] . "'
            }";
            $this->setToken($payload);
            echo $resolve = "{
                status: 200,
                data : {
                    login: true,
                    token: '" . $this->getToken() . "',
                    message: 'Login Succesfull'
                }
            }";

            $this->conn->update('user',['access_token' => $this->getToken()], "user_id = {$result['user_id']}");
            
        } else {
            echo $reject = "{
                status: 404,
                data: {
                    login: false,
                    message: 'Login failed.User Not found'
                }
            }";
        }
        
    }
}
