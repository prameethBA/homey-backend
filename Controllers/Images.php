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

}//End of Class
