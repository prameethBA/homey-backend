<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;



class AdminUsers extends Controller
{

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'all-users':

                        $stmt = $this->execute(Login::join(
                            [
                                'user.user_id as userId',
                                'login.email',
                                'login.mobile',
                                'login.user_status as status',
                                'user.first_name as firstName',
                                'user.last_name as lastName'
                            ],
                            (", user WHERE login.user_id = user.user_id AND login.user_type = 0")
                        ));
                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetchAll());
                        break;

                    case 'all-admins':

                        $stmt = $this->execute(Login::join(
                            [
                                'user.user_id as userId',
                                'login.email',
                                'login.mobile',
                                'login.user_status as status',
                                'user.first_name as firstName',
                                'user.last_name as lastName'
                            ],
                            (", user WHERE login.user_id = user.user_id AND login.user_type = 1")
                        ));
                        http_response_code(200);
                        echo $resolve = json_encode($stmt->fetchAll());
                        break;

                    case 'get':

                        $stmt = $this->execute(Login::join(
                            [
                                'login.email',
                                'login.mobile',
                                'login.user_status as status',
                                'user.first_name as firstName',
                                'user.last_name as lastName'
                            ],
                            (", user WHERE login.user_id = user.user_id AND user.user_id = {$this->secureParams['profile']}")
                        ));

                        $result['userData'] = $stmt->fetch();

                        $stmt = $this->execute($this->get('property',['_id', 'title', 'created'], ("user_id = {$this->secureParams['profile']} AND privated = 0")));

                        $result['ownPropertyData'] = $stmt->fetchAll();

                        http_response_code(200);
                        echo $resolve = json_encode($result);
                        break;


                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        } //End of try catch

    } //End of GET

    // Authenticate Admin 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateAdmin($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    }
}//End of Class
