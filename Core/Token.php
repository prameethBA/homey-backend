<?php

namespace Core;

require_once('Core/Model.php');
use Core\Model as Model;


class Token extends Model
{

    private $token;

    private $SECRET = "SECRET_KEY";

    private $header = "{
        'alg': ['sha1','md5'],
        'typ': 'JWT'
    }";

    private $payload;
    private $signature;
    private $timevalue;

    private function setTimeValue()
    {
        $this->timevalue =  time();
    }

    protected function setToken($payload, $userId)
    {
        $this->payload = $payload;
        $this->setTimeValue();

        $stmt = static::execute(static::get('login', 'user_id', 'user_id=' . $userId));
        if ($stmt->rowCount() == 1) {
            static::execute(static::update('login', ['access_token' => $this->timevalue], 'user_id=' . $userId));
            $info = base64_encode($this->header) . "." . base64_encode($this->payload) . "." . sha1($this->SECRET . $this->timevalue);
            $this->signature = md5($info);

            $this->token = md5($info . "." . $this->signature);
        }
    }

    protected function getToken()
    {
        return $this->token;
    }

    protected function verifyToken($token)
    {
        $explodedToken = explode(".", $token);
        $header = $explodedToken[0];
        $payload = $explodedToken[1];
        // $signature = $explodedToken[2];

        $info = base64_encode($header) . "." . base64_encode($payload) . "." . sha1($this->SECRET);
        $tokenSignature = md5($info);
        $this->setToken($payload);

        if ($this->token == $info . "." . $tokenSignature)
            return true;
        else
            return false;
    }

    protected function authenticateUser($userId, $token)
    {
        require_once('Core/DB/DB.php');

        $stmt = DB\DB::execute("SELECT user_id FROM login WHERE user_id = {$userId} AND access_token ='{$token}'");

        if ($stmt->rowCount() == 1) return true;

        return false;
    }

    protected function authenticateAdmin($userId, $token)
    {
        require_once('Core/DB/DB.php');

        $stmt = DB\DB::execute("SELECT user_id FROM login WHERE user_id = {$userId} AND access_token ='{$token}' AND user_type = 1");

        if ($stmt->rowCount() == 1) return true;

        return false;
    }
}
