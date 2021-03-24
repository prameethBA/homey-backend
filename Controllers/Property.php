<?php

namespace Controllers;

use PDO;
use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Property extends Controller
{


    // get all properties     
    public function All($param)
    {
        try {
            $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
                             "status": "500",
                             "error": "true",
                             "message": "' . $err->getMessage() . '"
                     }', 200);
        }
    }

    //get Add/remove form Favourite 
    public function ToggleFavourite($params, $param)
    {
        try {

            $userId = (string)$param['userId'];
            $propertyId = (string)$param['propertyId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");
            if ($params[0] == 'add') {
                $this->execute($this->save('favourite', ['user_id' => $userId, 'property_id' => $propertyId]));

                $this->resolve('{
                                    "status": "204",
                                    "message": "Added to favourite"
                                }', 200);
            } else   if ($params[0] == 'remove') {
                $this->execute($this->delete('favourite', ("property_id = '{$propertyId}' AND user_id = '{$userId}'")));

                $this->resolve('{
                                    "status": "204",
                                    "message": "Remove from favourite"
                                }', 200);
            }
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get  Favourite status 
    public function GetFavouriteStatus($params, $param)
    {
        try {

            $userId = (string)$param['userId'];
            $propertyId = (string)$param['propertyId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");
            $stmt = $this->execute($this->get('favourite', 'COUNT(_id) as count', ("user_id = {$userId} AND property_id = '{$propertyId}'")));

            $this->resolve('{
                            "action": "' . (int)$stmt->fetch()['count'] . '",
                            "message": "retived"
                    }', 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }


    public function GetProperty($params, $param)
    {
        try {
            $stmt = $this->execute($this->get('property', '*', ("_id = '" . (string)$param['propertyId'] . "'")));
            $result = $stmt->fetch();
            $stmt = $this->execute($this->get('propertysettings', 'reserved', ("property_id = '" . (string)$param['propertyId'] . "'")));
            $result['reserved'] = $stmt->fetch()['reserved'];
            $this->resolve(json_encode($result), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get all favourites
    public function GetAllFavourites($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->join('property', '*', ("p, 
            propertysettings s, favourite f WHERE p._id = s.property_id AND 
            p._id = f.property_id AND NOT p.user_id = '" . $userId . "' AND f.user_id = '" . $userId . "' AND p.privated = 0 AND p.property_status = 1 ORDER BY p.created DESC")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get reserved properties
    public function GetReserved($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->join('property', '*', ("
                                        p, propertysettings s, propertyreserved r  
                                            WHERE p._id = s.property_id 
                                            AND p._id = r.property_id
                                            AND r.user_id = '{$userId}' 
                                        ")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get own properties
    public function GetOwn($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' ORDER BY property.created DESC")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get reserved properties
    public function AddNew($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $id = $this->uniqueKey($userId);
            $location = json_encode($param['location']);
            $facilities = json_encode($param['facilities']);

            $data = [
                '_id' => $id,
                'user_id' => $userId,
                'title' => $param['title'],
                'location' => $location,
                'rental_period' => $param['rentalperiod'],
                'price' => (int)$param['price'],
                'key_money' => (int)$param['keyMoney'],
                'minimum_period' => (int)$param['minimumPeriod'],
                'available_from' => $param['availableFrom'],
                'location' => json_encode($param['location']),
                'property_type_id' => $param['propertyType'],
                'description' => $param['description'],
                'district_id' => $param['district'], //This is unnecceary, can be removed
                'city_id' => $param['city'],
                'facilities' => $facilities
            ];

            $this->execute($this->save('property', $data));

            // save images
            if (isset($param['images'])) {

                $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/propertyImages/" . $id;

                // Make a folder for each property with property ID
                if ($this->makeDir($path, 0777, false)) {
                    // Save each image for the created directory
                    $index = 1;
                    foreach ($param['images'] as $img) {
                        // if file not saved correctly throw an error
                        if (!$this->base64ToImage($img, $path . "/" . $index++)) {

                            $this->reject('{
                                                "status": "424",
                                                "error": "true",
                                                "message": "Failed to put images into database"
                                                }
                                            }', 200);
                        }
                    }
                } else throw new Exception("Permission Denied. Server side failure.");
            } //End of save images

            $this->resolve('{
                                "action": "true",
                                "propertyId": "' . $id . '",
                                "message": "The advertisement saved successfully."
                            }', 201);
            $this->addLog($id . " Property addtion succesfull", "add-property-success");
        } catch (Exception $err) {
            $this->addLog("Property addtion failed", "add-property-failed", (string)$err->getMessage());
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //Save properties
    public function SaveSettings($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $data = [
                'property_id' => $param['propertyId'],
                'boost' => (bool)$param['boost'] ? 1 : 0,
                'schedule' => (bool)$param['schedule'] ? 1 : 0,
                'schedule_date' => $param['scheduleDate'],
                'schedule_time' => $param['scheduleTime'],
                'sharing' => (bool)$param['sharing'] ? 1 : 0,
            ];
            $this->execute($this->save('propertysettings', $data));
            $this->execute($this->update('property', ['privated' => (bool)$param['privated'] ? 1 : 0], ("_id = '{$param['propertyId']}'")));

            $this->resolve('{
                                "status": "500",
                                "message": "Property Settings Applied."
                            }', 201);
            $this->addLog($param['propertyId'] . " Property settings added succesfull", "save-property-settings-success");
        } catch (Exception $err) {
            $this->addLog("Property settings addtion failed", "save-property-settings-failed", (string)$err->getMessage());
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }


    //Toggle accept online payment
    public function OnlinePayment($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $this->exec($this->update('propertysettings', ['accept_online_payment' => (int)$param['onlinePayment']], ("property_id = '{$param['propertyId']}'")));

            $this->resolve('{
                                "status": "204",
                                "message": "Updated"
                            }', 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //Toggle visibility
    public function Visibility($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $this->exec($this->update('property', ['privated' => (int)$param['visibility']], ("_id = '{$param['propertyId']}'")));

            $this->resolve('{
                                "status": "204",
                                "message": "Updated"
                            }', 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //Remove own property
    public function Remove($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $this->exec($this->delete('property', "_id = '{$param['propertyId']}'"));
            $this->exec($this->delete('propertysettings', "property_id = '{$param['propertyId']}'"));


            $this->resolve('{
                            "status": "204",
                            "message": "Property Removed"
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

    //Search property public
    public function Search($params, $param)
    {
        try {
            $district = isset($param['district']) ? (int)($param['district']) : 0;
            $city = isset($param['city']) ? (int)($param['city']) : 0;
            $propertyType = isset($param['propertype']) ? (int)($param['propertype']) : 0;

            if ($district == 0) $district = "";
            else $district = "AND district_id = " . $district;

            if ($city == 0) $city = "";
            else $city = "AND city_id = " . $city;

            if ($propertyType == 0) $propertyType = "";
            else $propertyType = "AND property_type_id = " . $propertyType;

            $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id 
                WHERE (property.title LIKE '%{$params[0]}%' 
                    OR property.description LIKE '%{$params[0]}%') 
                AND property.privated = 0 
                AND property.property_status = 1 
                 {$district}
                 {$city}
                 {$propertyType}
                    ORDER BY property.created 
                    DESC")));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
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
    //         if (isset($params[0])) {
    //             switch ($params[0]) {
    //                 case 'all':
    //                     // $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.privated = 0 AND property.property_status = 1 ORDER BY property.created DESC")));
    //                     // // $stmt = $this->execute($this->get('property',['_id', 'title', 'price', 'description'], (int)$params[1], (int)$params[1] * (int)$params[2]));

    //                     // $this->resolve(json_encode($stmt->fetchAll()), 200);

    //                     // break;
    //                 case 'search':
    //                     
    //                     break;
    //                 default:
    //                     $this->reject('{
    //                         "status": "400",
    //                         "message": "Invalid request."
    //                     }', 200);
    //                     break;
    //             }
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
    //         if (isset($params[0])) {
    //             if (!$this->authenticate()) throw new Exception("Unautherized request.");
    //             switch ($params[0]) {
    //                 case 'add-new':

    //                     $userId = $param['userId'];
    //                     $token = $param['token'];


    //                     if ($this->authenticateUser($userId, $token)) {
    //                         
    //                     } else throw new Exception("Authentication failed. Unauthorized request.");

    //                     break;

    //                 case 'get':
    //                     switch ($params[1]) {
    //                         case 'property':
    //                             $userId = $param['userId'];
    //                             $token = $param['token'];
    //                             if ($this->authenticateUser($userId, $token)) {
    //                                 
    //                             } else throw new Exception("Authentication failed. Unauthorized request.");
    //                             break;

    //                         case 'own':
    //                             // $stmt = $this->execute(PropertyModel::join('*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property._id = '{$param['propertyId']}'")));
    //                             
    //                             break;

    //                         default:
    //                             throw new Exception("Authentication failed. Unauthorized request.");
    //                     }

    //                     break;
    //                 case 'remove':
    //                     $this->exec($this->delete('property', "_id = '{$param['propertyId']}'"));
    //                     $this->exec($this->delete('propertysettings', "property_id = '{$param['propertyId']}'"));


    //                     $this->resolve('{
    //                         "status": "204",
    //                         "message": "Property Removed"
    //                     }', 200);
    //                     break;

    //                 case 'favourite':
    //                     switch ($params[1]) {
    //                         case 'get':
    //                             $stmt = $this->execute($this->get('favourite', 'COUNT(_id) as count', ("user_id = {$param['userId']} AND property_id = '{$param['propertyId']}'")));

    //                             $this->resolve('{
    //                                 "action": "' . $stmt->fetch()['count'] . '",
    //                                 "message": "retived"
    //                             }', 200);
    //                             break;

    //                             break;

    //                         case 'getAll':
    //                             $userId = $param['userId'];
    //                             $token = $param['token'];
    //                             if ($this->authenticateUser($userId, $token)) {
    //                                 $stmt = $this->execute($this->join('property', '*', ("INNER JOIN favourite  ON property._id = favourite.property_id WHERE NOT property.user_id = '" . $param['userId'] . "' AND favourite.user_id = '" . $param['userId'] . "'")));

    //                                 $this->resolve(json_encode($stmt->fetchAll()), 200);
    //                             } else throw new Exception("Authentication failed. Unauthorized request.");
    //                             break;

    //                         case 'add':
    //                             $stmt = $this->execute($this->save('favourite', ['user_id' => $param['userId'], 'property_id' => $param['propertyId']]));

    //                             $this->resolve('{
    //                                 "status": "204",
    //                                 "message": "Added to favourite"
    //                             }', 200);
    //                             break;

    //                         case 'remove':
    //                             $stmt = $this->execute($this->delete('favourite', ("property_id = '{$param['propertyId']}' AND user_id = '{$param['userId']}'")));

    //                             $this->resolve('{
    //                                 "status": "204",
    //                                 "message": "Remove from favourite"
    //                             }', 200);
    //                             break;
    //                         default:
    //                             throw new Exception("Authentication failed. Unauthorized request.");
    //                     }
    //                     break;
    //                 case 'filter':
    //                     //2nd switch
    //                     switch ($params[2]) {

    //                         case 'own':
    //                             $userId = $param['userId'];
    //                             $token = $param['token'];
    //                             if (!$this->authenticateUser($userId, $token)) throw new Exception("Authentication failed. Unauthorized request.");
    //                             //filter filter option switch
    //                             switch ($params[3]) {
    //                                 case 'boosted':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND propertysetting.boosted = 1 ORDER BY property.created DESC")));
    //                                     break;
    //                                 case 'pending':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND property.property_status = 0 ORDER BY property.created DESC")));
    //                                     break;
    //                                 case 'private':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND property.privated = 1 ORDER BY property.created DESC")));
    //                                     break;
    //                                 case 'public':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND property.privated = 0 ORDER BY property.created DESC")));
    //                                     break;
    //                                 case 'rejected':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND property.property_status = 2 ORDER BY property.created DESC")));
    //                                     break;
    //                                 case 'blocked':
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' AND property.property_status = 3 ORDER BY property.created DESC")));
    //                                     break;
    //                                 default:
    //                                     $stmt = $this->execute($this->join('property', '*', ("INNER JOIN propertysettings ON property._id = propertysettings.property_id WHERE property.user_id = '{$userId}' ORDER BY property.created DESC")));
    //                                     break;
    //                             } //End of filter option switch

    //                             $this->resolve(json_encode($stmt->fetchAll()), 200);
    //                             break;
    //                     } //End of second switch
    //                     break;
    //                 case 'reserved':
    //                     switch ($params[1]) {
    //                         case 'own':
    //                             $userId = $param['userId'];
    //                             $token = $param['token'];
    //                             if ($this->authenticateUser($userId, $token)) {
    //                                 
    //                             } else throw new Exception("Authentication failed. Unauthorized request.");
    //                             break;

    //                         default:
    //                             throw new Exception("Authentication failed. Unauthorized request.");
    //                     }
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
    //         }', 200);
    //     }
    // } //End of POST


    // //patch
    // public function patch()
    // {
    //     try {
    //         if (isset($params[0])) {
    //             if (!$this->authenticate()) throw new Exception("Unauthorized request.");
    //             switch ($params[0]) {
    //                 case 'settings':
    //                     $data = [
    //                         'property_id' => $param['propertyId'],
    //                         'boost' => (bool)$param['boost'] ? 1 : 0,
    //                         'schedule' => (bool)$param['schedule'] ? 1 : 0,
    //                         'schedule_date' => $param['scheduleDate'],
    //                         'schedule_time' => $param['scheduleTime'],
    //                         'sharing' => (bool)$param['sharing'] ? 1 : 0,
    //                     ];
    //                     $this->execute($this->save('propertysettings', $data));
    //                     $this->execute($this->update('property', ['privated' => (bool)$param['privated'] ? 1 : 0], ("_id = '{$param['propertyId']}'")));

    //                     $this->resolve('{
    //                         "message": "Property Settings Applied."
    //                     }', 200);
    //                     break;

    //                 case 'online-payment':

    //                     $this->exec($this->update('property', ['accept_online_payment' => (int)$param['onlinePayment']], ("property_id = '{$param['propertyId']}'")));

    //                     $this->resolve('{
    //                             "status": "204",
    //                             "message": "Updated"
    //                         }', 200);
    //                     break;

    //                 case 'visibility':

    //                     $this->exec($this->update('property', ['privated' => (int)$param['visibility']], ("_id = '{$param['propertyId']}'")));

    //                     $this->resolve('{
    //                                 "status": "204",
    //                                 "message": "Updated"
    //                             }', 200);

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
    // } //End of patch

    // Private methods

    // Check if a directory exits or, create new directory
    private function makeDir($path, $mode, $recursive)
    {
        return is_dir($path) || mkdir($path, $mode, $recursive);
    }

    // Save base64 immage to as a file
    private function base64ToImage($base64, $file)
    {

        // split the string on commas
        $data = explode(',', $base64); //$data[ 1 ] == <actual base64 string>

        // RegX to get extention
        $regx = '/(?<=\/)(.*?)(?=;)/'; //$data[ 0 ] == "data:image/png;base64"
        preg_match($regx, $data[0], $matches);

        $extention = $matches[0];

        // Save file
        if (file_put_contents($file . "." . $extention, base64_decode($data[1]))) return true;
        return false;
    }
}//End of Class
