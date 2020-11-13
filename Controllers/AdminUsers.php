<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;
require_once('Models/Login.php');
use Models\Login as Login;
require_once('Models/User.php');
use Models\User as User;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class AdminUsers extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
        new Login();
        new User();
    }

    public function post() {
        try {
            if(isset($this->params[0])) {
                if(!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'all-users':

                        $stmt = DB::execute(Login::join(
                            [
                                'login.email', 
                                'login.mobile', 
                                'login.user_status as status', 
                                'user.first_name as firstName',
                                'user.last_name as lastName'
                                ]
                            , (", user WHERE login.user_id = user.user_id AND login.user_type = 0")));
                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetchAll());
                        break;

                        case 'all-admins':

                            $stmt = DB::execute(Login::join(
                                [
                                    'login.email', 
                                    'login.mobile', 
                                    'login.user_status as status', 
                                    'user.first_name as firstName',
                                    'user.last_name as lastName'
                                    ]
                                , (", user WHERE login.user_id = user.user_id AND login.user_type = 0")));
                            http_response_code(200);
                            echo $resolve = json_encode($stmt->fetchAll());
                            break;
                    
                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");

        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        }//End of try catch
            
    }//End of GET

    // Authenticate Admin 
    private function authenticate() {
        if(isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if($this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    }

}//End of Class
