<?php

namespace Controllers;

use PDO;
use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Property.php');

use Models\Property as PropertyModel;

require_once('Models/ServiceFees.php');

use Models\ServiceFees as ServiceFees;

require_once('Models/PropertyUpdate.php');

use Models\PropertyUpdate as PropertyUpdate;

require_once('Models/Favourite.php');

use Models\Favourite as Favourite;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class PropertyUpdate extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new PropertyModel();
        new ServiceFees();
        new PropertyUpdate();
        // new Favourite();
    }

    public function get()
    {
        try {
            if (isset($this->params[0])) {
                // switch ($this->params[0]) {
                //     case 'all':
                //         $stmt = DB::execute(PropertyModel::join('*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
                //         // $stmt = DB::execute(PropertyModel::get(['_id', 'title', 'price', 'description'], (int)$this->params[1], (int)$this->params[1] * (int)$this->params[2]));
                //         http_response_code(200);
                //         echo $resolve = json_encode($stmt->fetchAll());
                //         break;
                //     case 'search':
                //         $stmt = DB::execute(PropertyModel::join('*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE (property.title LIKE '%{$this->params[1]}%' OR property.description LIKE '%{$this->params[1]}%') AND property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
                //         // $stmt = DB::execute(PropertyModel::get(['_id', 'title', 'price', 'description'], (int)$this->params[1], (int)$this->params[1] * (int)$this->params[2]));
                //         http_response_code(200);
                //         echo $resolve = json_encode($stmt->fetchAll());
                //         break;
                //     default:
                //         http_response_code(200);
                //         die($reject = '{
                //                 "status": "400",
                //                 "message": "Invalid request."
                //         }');
                //         break;
                // }
            }
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "message": "' . $err->getMessage() . '"
            }');
        }
    } //End of GET

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'get':

                        // $userId = $this->secureParams['userId'];
                        // $token = $this->secureParams['token'];


                        // if ($this->authenticateUser($userId, $token)) {
                        //     $id = $this->uniqueKey($userId);
                        //     $location = json_encode($this->secureParams['location']);
                        //     $facilities = json_encode($this->secureParams['facilities']);

                        //     $data = [
                        //         '_id' => $id,
                        //         'user_id' => $this->secureParams['userId'],
                        //         'title' => $this->secureParams['title'],
                        //         'location' => $location,
                        //         'rental_period' => $this->secureParams['rentalperiod'],
                        //         'price' => (int)$this->secureParams['price'],
                        //         'key_money' => (int)$this->secureParams['keyMoney'],
                        //         'minimum_period' => (int)$this->secureParams['minimumPeriod'],
                        //         'available_from' => $this->secureParams['availableFrom'],
                        //         'property_type_id' => $this->secureParams['propertyType'],
                        //         'description' => $this->secureParams['description'],
                        //         'district_id' => $this->secureParams['district'], //This is unnecceary, can be removed
                        //         'city_id' => $this->secureParams['city'],
                        //         'facilities' => $facilities
                        //     ];

                        //     $stmt = DB::execute(PropertyModel::save($data));

                        //     // save images
                        //     if (isset($this->secureParams['images'])) {

                        //         $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/propertyImages/" . $id;

                        //         // Make a folder for each property with property ID
                        //         if ($this->makeDir($path, 0777, false)) {
                        //             // Save each image for the created directory
                        //             $index = 1;
                        //             foreach ($this->secureParams['images'] as $img) {
                        //                 // if file not saved correctly throw an error
                        //                 if (!$this->base64ToImage($img, $path . "/" . $index++)) {
                        //                     http_response_code(200);
                        //                     die($reject = '{
                        //                         "status": "424",
                        //                         "error": "true",
                        //                         "message": "Failed to put images into database"
                        //                         }
                        //                     }');
                        //                 }
                        //             }
                        //         } else throw new Exception("Permission Denied. Server side failure.");
                        //     } //End of save images
                        //     http_response_code(201);
                        //     echo $resolve = '{
                        //         "action": "true",
                        //         "propertyId": "' . $id . '",
                        //         "message": "The advertisement saved successfully."
                        //     }
                        //     ';
                        // } else throw new Exception("Authentication failed. Unauthorized request.");

                        break;

                    case 'add':
                        $userId = $this->secureParams['userId'];
                        $token = $this->secureParams['token'];
                        $propertyId = $this->secureParams['propertyId'];
                        if ($this->authenticateUser($userId, $token)) {
                            $stmt = DB::execute(PropertyModel::get('key_money as keyMoney, title', ("_id = '" . $propertyId . "'")));
                            
                            $results = $stmt->fetch();

                            $result['title'] = $results['title'];
                            $result['keyMoney'] = $results['keyMoney'];
                            
                            $stmt = DB::execute(ServiceFees::get('fee', ("service = 'reserve'")));
                            $result['fee'] = $stmt->fetch()['fee'];

                            DB::execute(PropertyUpdate::save(['property_id' => $propertyId, 'user_id' => $userId]));

                            http_response_code(200);
                            echo json_encode($result);
                        } else throw new Exception("Authentication failed. Unauthorized request.");
                        break;


                    default:
                        throw new Exception("Invalid parameter");
                } //End of the switch

            } else throw new Exception("Invalid request.No parameters given");
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                "status": "500",
                "error": "true",
                "message": "' . $err->getMessage() . '"
            }');
        }
    } //End of POST

    // Private methods

    // Authenticate User 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    } //end of authenticateUser()

    // Check if a directory exits or, create new directory
    private function makeDir($path, $mode, $recursive)
    {
        return is_dir($path) || mkdir($path, $mode, $recursive);
    }

}//End of Class
