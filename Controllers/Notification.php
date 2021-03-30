<?php

namespace Controllers;

use PDO;
use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Notification extends Controller
{

    public function CheckNew($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->get('notification', 'COUNT(_id) as count', "notification_status = 0 AND user_id =" . (int)$userId));
            $result['count'] = $stmt->fetch()['count'];
            $result['new'] = true;
            $this->resolve(json_encode($result), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get all
    public function AllNew($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->getAll('notification', '*', "notification_status = 0 AND user_id =" . (int)$userId));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }   
    }
}//End of Class
