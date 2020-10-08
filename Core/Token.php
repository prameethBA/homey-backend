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

    public function setToken($payload){
        $this->payload = $payload;
        $info = base64_encode($this->header) . "." . base64_encode($this->payload) . "." . sha1($this->SECRET);
        $this->signature = md5($info);

        $this->token = $info . "." . $this->signature;
    }

    public function getToken() {
        return $this->token;
    }

    public function verifyToken($token) {
        $explodedToken = explode(".",$token);
        $header = $explodedToken[0];
        $payload = $explodedToken[1];
        // $signature = $explodedToken[2];

        $info = base64_encode($header) . "." . base64_encode($payload) . "." . sha1($this->SECRET);
        $tokenSignature = md5($info);
        setToken($payload);

        if($this->token == $info . "." . $tokenSignature)
            return true;
        else
            return false;
    }

}
