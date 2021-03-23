<?php

namespace Core;

require_once('Core/Model.php');

use Core\Model as Model;


class Token extends Model
{

    private $token;

    private $SECRET = "SECRET_KEY";

    private $header = "{'alg':['md5'],'typ':'JWT'}";

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

        $stmt = $this->execute($this->get('login', 'user_id', 'user_id=' . $userId));
        if ($stmt->rowCount() == 1) {
            $this->execute($this->update('login', ['access_token' => $this->timevalue], 'user_id=' . $userId));
            $preInfo = base64_encode($this->header) . "." . base64_encode($this->payload);
            $info = base64_encode($this->header) . "." . base64_encode($this->payload) . "." . md5($this->SECRET . $this->timevalue);
            $this->signature = md5($info);

            $this->token = $preInfo . "." . $this->signature;
        }
    }

    protected function getToken()
    {
        return $this->token;
    }

    protected function verifyToken($token, $userId, $userType = '')
    {
        $explodedToken = explode(".", $token);

        $header = $explodedToken[0];
        $payload = $explodedToken[1];
        $signature = $explodedToken[2];

        $stmt = $this->execute($this->get('login', ['access_token'], "user_id=" . $userId . $userType));
        $info = ($header) . "." . ($payload) . "." . md5($this->SECRET . $stmt->fetch()['access_token']);
        $tokenSignature = md5($info);

        if ($signature == $tokenSignature) return true;

        return false;
    }

    protected function authenticateUser($token, $userId)
    {
        return $this->verifyToken($token, $userId);
    }

    protected function authenticateAdmin($token, $userId)
    {
        return $this->verifyToken($token, $userId, " AND  user_type=1");
    }
}
