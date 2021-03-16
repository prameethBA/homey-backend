<?php

namespace Core;

class Token {

    private $token;

    private $SECRET = "SECRET_KEY";

    private $header = "{
        'alg': ['sha1','md5'],
        'typ': 'JWT'
    }";

    private $payload;
    private $signature;

    private function setToken($info) {
        $this->token = md5($info . "." . $this->signature);
    }

    protected function generateToken($payload){
        $this->payload = $payload;
        $info = base64_encode($this->header) . "." . base64_encode($this->payload) . "." . sha1($this->SECRET);
        $this->signature = md5($info);

        //Setting up the token
        $this->setToken($info);
    }

    protected function getToken() {
        return $this->token;
    }

    protected function verifyToken($token) {
        $explodedToken = explode(".",$token);
        $header = $explodedToken[0];
        $payload = $explodedToken[1];
        // $signature = $explodedToken[2];
        $info = $header . "." . $payload . "." . sha1($this->SECRET);
        $tokenSignature = md5($info);
        $this->generateToken(base64_decode($payload));

        if($this->signature === $tokenSignature)
            return true;
        else
            return false;
    }


    protected function authenticateUser($token) {
        if($this->verifyToken($token)) {
            $data = json_decode($this->payload, true);
            $userId = $data['userId'];

            require_once('Core/DB/DB.php');

            // *** Critical point
            //This method of token gemerating always gives the same token for certain user.That should be risky.
            //Some code may store in the databse like a random value solve this issue.
            //*** Evelouvate and solve this issue before move forword
            $stmt = DB\DB::execute("SELECT user_id FROM login WHERE user_id = {$userId} AND access_token ='{$token}'");

            if($stmt->rowCount() == 1) return true;

            return false;

        } else {
            //echo out a authentication faild message.
            die('{
                "data": {
                    "error": "true",
                    "message": "Authentication failed."
                }
            }');
        }
    } 

    
    // protected function authenticateUser($userId, $token) {
    //     require_once('Core/DB/DB.php');

    //     $stmt = DB\DB::execute("SELECT user_id FROM login WHERE user_id = {$userId} AND access_token ='{$token}'");

    //     if($stmt->rowCount() == 1) return true;

    //     return false;
    // }

    // protected function authenticateAdmin($userId, $token) {
    //     require_once('Core/DB/DB.php');
        
    //     $stmt = DB\DB::execute("SELECT user_id FROM login WHERE user_id = {$userId} AND access_token ='{$token}' AND user_type = 1");

    //     if($stmt->rowCount() == 1) return true;

    //     return false;
    // }

}
