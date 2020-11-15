<?php

namespace Controllers;

use PDO;
use Exception;

use DirectoryIterator;

require_once('Core/BaseController.php');
use Core\BaseController as BaseController;

require_once('Core/DB/DB.php');
use Core\DB\DB as DB;

class Images extends BaseController {

    public function __construct($params, $secureParams) {
        parent::__construct($params, $secureParams);
    }

    public function get() {
            
    }//End of GET

    public function post() {
        try {
            if (isset($this->params[0])) {
                switch ($this->params[0]) {
                    case 'property':
                        $data = "[";
                        foreach ($this->secureParams['ids'] as $value) {
                            $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/propertyImages/" . $value;
                            $data .= '{"id":"' . $value . '", "images": [';
                            
                            if($this->dirExits($path)) {
                                $dir = new DirectoryIterator($path);
                                foreach ($dir as $fileinfo) {
                                    if (!$fileinfo->isDot()) {
                                        if($result = $this->imageToBase64($fileinfo->getPathname())) {
                                            $data .= '{"image" : "' . $result . '"},';
                                        } else die("Invalid");
                                    }
                                }
                                $data = rtrim($data,',') . ']},';
                            }
                        }
                        $data = rtrim($data,',') . ']';
                        echo($data);
                        break;

                    case 'profile':
                        switch($this->params[1]) {
                            case 'save':
                                // save images
                                if(isset($this->secureParams['image'])) {
                
                                    $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/profileImages/" . $this->secureParams['userId'];
                
                                    // Make a folder for each property with property ID
                                    if($this->makeDir($path, 0777, false)) {

                                        //clear the directory
                                        $this->clearDir($path);
                                        // if file not saved correctly throw an error
                                        if(!$this->base64ToImage($this->secureParams['image'], $path . "/" . $this->secureParams['userId'] )) {
                                            http_response_code(200);
                                            die($reject = '{
                                                "status": "424",
                                                "error": "true",
                                                "message": "Failed to put images into database"
                                                }
                                            }');
                                        }
                                        http_response_code(201);
                                        echo $resolve = '{
                                            "message": "Profile picture succesfully updated."
                                        }';
                                    } else throw new Exception("Permission Denied. Server side failure.");
                                }//End of save images
                                break;
                            case 'get':
                                    $path  = $_SERVER["DOCUMENT_ROOT"] . "/data/profileImages/" . $this->secureParams['userId'];
                                    
                                    if($this->dirExits($path)) {
                                        $dir = new DirectoryIterator($path);
                                        foreach ($dir as $fileinfo) {
                                            if (!$fileinfo->isDot()) {
                                                if(!($result = $this->imageToBase64($fileinfo->getPathname())))  throw new Exception("No images found.");
                                            }
                                        }
                                    }
                                    http_response_code(201);
                                    echo $resolve = '{
                                        "image": "' . $result .'"
                                    }';
                                break;
                        }

                        break;
                    default:
                        http_response_code(200);
                        die($reject = '{
                                "status": "400",
                                "message": "Invalid request."
                        }');
                        break;
                    
                }
            }
        } catch(Exception $err) {
            http_response_code(200);
            die($reject = '{
                "status": "500",
                "error": "true",
                "message": "' . $err->getMessage() . '"
                }
            }');
        }
            
    }//End of POST

    // Check if a directory exits
    private function dirExits($path) {
         return is_dir($path);
    }

    // Save base64 immage to as a file
    private function imageToBase64($image) {
        $type = pathinfo($image, PATHINFO_EXTENSION);
        if($data = file_get_contents($image)) {
            return $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return false;
    }

    // Check if a directory exits or, create new directory
    private function makeDir($path,$mode, $recursive) {
        return is_dir($path) || mkdir($path, $mode, $recursive);
    }

    // Save base64 immage to as a file
    private function base64ToImage($base64, $file) {

        // split the string on commas
        $data = explode( ',', $base64 );//$data[ 1 ] == <actual base64 string>

        // RegX to get extention
        $regx = '/(?<=\/)(.*?)(?=;)/'; //$data[ 0 ] == "data:image/png;base64"
        preg_match($regx, $data[0], $matches);

        $extention = $matches[0];
    
        // Save file
        if(file_put_contents($file . "." . $extention, base64_decode($data[1]))) return true;
        return false; 
    }

    //clear the directory
    private function clearDir($path) {
        $files = glob($path.'/*');  
        // Deleting all the files in the list 
        foreach($files as $file) { 
        
            if(is_file($file))  
            
                // Delete the given file 
                unlink($file);  
        } 
    }//End of clearDir()

}//End of Class
