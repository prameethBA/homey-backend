<?php

namespace Controllers;

// use PDO;
// use PDOException;
require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

class Login extends BaseController{

    public function get() {
        $resolve  = "{
            status:200,
            data:{
                message: 'Ok'
            }
        }";
        $reject = "{
            status: 500,
            message: 'Internal Server Error'
        }";

        echo ($resolve);
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

        $sql = "SELECT * FROM user WHERE (email='{$username}' OR mobile='{$username}') AND password='{$password}'";

        $stmt = $this->conn->connection->prepare($sql);
        $stmt->execute();
        
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
}
