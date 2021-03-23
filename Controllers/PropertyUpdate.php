<?php

namespace Controllers;

use PDO;
use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class PropertyUpdate extends Controller
{

    public function AddStatus($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $propertyId = (string)$param['propertyId'];
            $stmt = $this->execute($this->get('property', 'key_money as keyMoney, title', ("_id = '" . $propertyId . "'")));

            $results = $stmt->fetch();

            $result['title'] = $results['title'];
            $result['keyMoney'] = $results['keyMoney'];

            $stmt = $this->execute($this->get('servicefees', 'fee', ("service = 'reserve'")));
            $result['fee'] = $stmt->fetch()['fee'];

            $stmt = $this->execute($this->get('propertyupdate', "user_id", "property_id ='" . $propertyId . "' AND user_id =" . $userId));
            if ($stmt->rowCount() == 0) $this->execute($this->save('propertyupdate', ['property_id' => $propertyId, 'user_id' => $userId]));
            else if ($stmt->rowCount() >= 1) $this->execute($this->update('propertyupdate', ['created' => 'CURRENT_TIMESTAMP'], "property_id = '" . $propertyId . "' AND user_id ='" . $userId . "'"));

            $stmt = $this->execute($this->get('propertyupdate', 'COUNT(property_id) as count', ("property_id = '" . $propertyId . "'")));
            $result['userCount'] = $stmt->fetch()['count'];

            $this->resolve(json_encode($result), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
             }
         }', 200);
        }
    }

    //Remove status

    public function RemoveStatus($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $propertyId = (string)$param['propertyId'];
            $this->exec($this->delete('propertyupdate', "property_id = '" . $propertyId . "' AND user_id ='" . $userId . "'"));

            $this->resolve('{
                            "action":"true",
                            "message":"removed"
                        }', 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
             }
         }', 200);
        }
    }



    // public function get()
    // {
    //     try {
    //         if (isset($this->params[0])) {
    //             // switch ($this->params[0]) {
    //             //     case 'all':
    //             //         $stmt = $this->execute($this->join('property','*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
    //             //         // $stmt = $this->execute($this->get('property',['_id', 'title', 'price', 'description'], (int)$this->params[1], (int)$this->params[1] * (int)$this->params[2]));
    //             //         http_response_code(200);
    //             //         echo $resolve = json_encode($stmt->fetchAll());
    //             //         break;
    //             //     case 'search':
    //             //         $stmt = $this->execute($this->join('property','*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE (property.title LIKE '%{$this->params[1]}%' OR property.description LIKE '%{$this->params[1]}%') AND property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
    //             //         // $stmt = $this->execute($this->get('property',['_id', 'title', 'price', 'description'], (int)$this->params[1], (int)$this->params[1] * (int)$this->params[2]));
    //             //         http_response_code(200);
    //             //         echo $resolve = json_encode($stmt->fetchAll());
    //             //         break;
    //             //     default:
    //             //         http_response_code(200);
    //             //         die($reject = '{
    //             //                 "status": "400",
    //             //                 "message": "Invalid request."
    //             //         }');
    //             //         break;
    //             // }
    //         }
    //     } catch (Exception $err) {
    //         $this->reject('{
    //             "status": "500",
    //             "message": "' . $err->getMessage() . '"
    //     }', 200);
    //     }
    // } //End of GET

    // public function post()
    // {
    //     try {
    //         if (isset($this->params[0])) {
    //             if (!$this->authenticate()) throw new Exception("Unautherized request.");
    //             switch ($this->params[0]) {
    //                 case 'add':

    //                     break;

    //                 case 'remove':
    //                     $userId = $param['userId'];
    //                     $token = $param['token'];
    //                     $propertyId = $param['propertyId'];
    //                     if ($this->authenticateUser($userId, $token)) {
    //                         $stmt = $this->exec($this->delete('propertyupdate', "property_id = '" . $propertyId . "' AND user_id ='" . $userId . "'"));

    //                         $this->resolve('{
    //                                 "action":"true",
    //                                 "message":"removed"
    //                             }', 200);
    //                     } else throw new Exception("Authentication failed. Unauthorized request.");
    //                     break;


    //                 default:
    //                     throw new Exception("Invalid parameter");
    //             } //End of the switch

    //         } else throw new Exception("Invalid request.No parameters given");
    //     } catch (Exception $err) {
    //         $this->reject('{
    //             "status": "500",
    //             "error": "true",
    //             "message": "' . $err->getMessage() . '"
    //         }', 500);
    //     }
    // } //End of POST

    // // Private methods

    // // Authenticate User 
    // private function authenticate()
    // {
    //     if (isset($param['userId'], $param['token'])) {
    //         if ($this->authenticateUser($param['userId'], $param['token'])) return true;
    //         else return false;
    //     } else return false;
    // } //end of authenticateUser()

    // // Check if a directory exits or, create new directory
    // private function makeDir($path, $mode, $recursive)
    // {
    //     return is_dir($path) || mkdir($path, $mode, $recursive);
    // }
}//End of Class
