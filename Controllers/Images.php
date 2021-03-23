<?php

namespace Controllers;

use Exception;

use DirectoryIterator;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Images extends Controller
{

    public function GetProfileImage($a, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");
            if (isset($a[0]))
                $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/profileImages/" . $a[0];
            else
                $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/profileImages/" . $userId;

            if ($this->dirExits($path)) {
                $dir = new DirectoryIterator($path);
                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        if (!($result = $this->imageToBase64($fileinfo->getPathname())))  throw new Exception("No images found.");
                    }
                }
            }

            $this->resolve('{
                    "image": "' . $result . '"
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

    public function ProfileSave($a, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");
            // save images
            if (isset($param['image'])) {

                $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/profileImages/" . $userId;

                // Make a folder for each property with property ID
                if ($this->makeDir($path, 0777, false)) {

                    //clear the directory
                    $this->clearDir($path);
                    // if file not saved correctly throw an error
                    if (!$this->base64ToImage($param['image'], $path . "/" . $userId)) {
                        $this->reject('{
                                        "status": "424",
                                        "error": "true",
                                        "message": "Failed to put images into database"
                                    }
                                }', 200);
                    }
                    $this->resolve('{
                                    "message": "Profile picture succesfully updated."
                                }', 201);
                } else throw new Exception("Permission Denied. Server side failure.");
            } //End of save images
        } catch (Exception $err) {
            $this->reject('{
            "status": "500",
            "error": "true",
            "message": "' . $err->getMessage() . '"
            }
        }', 200);
        }
    }


    //get property Images 
    public function GetProperty($param)
    {
        try {
            $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/propertyImages/" . $param[0];

            if ($this->dirExits($path)) {
                $dir = new DirectoryIterator($path);
                $data = "[";
                foreach ($dir as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        if ($result = $this->imageToBase64($fileinfo->getPathname())) {
                            $data .= '{"image" : "' . $result . '"},';
                        } else die("Invalid");
                    }
                }
                $data = rtrim($data, ',') . ']';
            }
            $this->resolve($data, 200);
        } catch (Exception $err) {
            $this->reject('{
            "status": "500",
            "error": "true",
            "message": "' . $err->getMessage() . '"
            }
        }', 200);
        }
    }

    // public function post()
    // {
    //     try {
    //         if (isset($this->params[0])) {
    //             if (!$this->authenticate()) throw new Exception("Unauthorized request.");
    //             switch ($this->params[0]) {
    //                 case 'property':
    //                     $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/propertyImages/" . $this->params[1];

    //                     if ($this->dirExits($path)) {
    //                         $dir = new DirectoryIterator($path);
    //                         $data = "[";
    //                         foreach ($dir as $fileinfo) {
    //                             if (!$fileinfo->isDot()) {
    //                                 if ($result = $this->imageToBase64($fileinfo->getPathname())) {
    //                                     $data .= '{"image" : "' . $result . '"},';
    //                                 } else die("Invalid");
    //                             }
    //                         }
    //                         $data = rtrim($data, ',') . ']';
    //                     }
    //                     http_response_code(201);
    //                     echo $resolve = $data;
    //                     break;

    //                 case 'profile':
    //                     switch ($this->params[1]) {
    //                         case 'save':
    //                             
    //                             break;
    //                         case 'get':

    //                             break;
    //                     }

    //                     break;
    //                 default:
    //                     http_response_code(200);
    //                     die($reject = '{
    //                             "status": "400",
    //                             "message": "Invalid request."
    //                     }');
    //                     break;
    //             }
    //         }
    //     } catch (Exception $err) {
    //         http_response_code(200);
    //         die($reject = '{
    //             "status": "500",
    //             "error": "true",
    //             "message": "' . $err->getMessage() . '"
    //             }
    //         }');
    //     }
    // } //End of POST

    // Check if a directory exits
    private function dirExits($path)
    {
        return is_dir($path);
    }

    // Save base64 immage to as a file
    private function imageToBase64($image)
    {
        $type = pathinfo($image, PATHINFO_EXTENSION);
        if ($data = file_get_contents($image)) {
            return $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return false;
    }

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

    //clear the directory
    private function clearDir($path)
    {
        $files = glob($path . '/*');
        // Deleting all the files in the list 
        foreach ($files as $file) {

            if (is_file($file))

                // Delete the given file 
                unlink($file);
        }
    } //End of clearDir()

    // // Authenticate User 
    // private function authenticate()
    // {
    //     if (isset($param['userId'], $param['token'])) {
    //         if ($this->authenticateUser($param['userId'], $param['token'])) return true;
    //         else return false;
    //     } else return false;
    // } //end of authenticateUser()

}//End of Class
