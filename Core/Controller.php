<?php

namespace Core;

require_once('Token.php');

use \Core\Token as Token;

class Controller extends Token
{

    protected $params = [];
    protected $secureParams = [];

    private $uniqueKeyString = "THIS_IS_THE_KEY_STRING_TO_GENERATE_UNIQUE_KEY";

    protected $state = [];

    //echo resolve
    protected function resolve($data, $status = 200)
    {
        http_response_code($status);
        echo $data;
    } //end of echio resolve

    //echo reject
    protected function reject($data, $status = 500)
    {
        http_response_code($status);
        die($data);
    } //end of rejecct

    // Generate unique key

    protected function uniqueKey($key)
    {
        return md5(time() . sha1($key . $this->uniqueKeyString));
    }

    protected function sendMail($receiver = [], $message = "homey.lk", $subject = 'Message From no-reply@homey.lk')
    {
        // Multiple recipients
        $to = is_string($receiver) ? $receiver : implode(', ', $receiver);

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = "Content-Type: text/html; charset=UTF-8";

        // Additional headers
        // $headers = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
        $headers[] = 'From: Homey.lk <no-reply@homey.lk>';
        // $headers = 'Cc: birthdayarchive@example.com';
        // $headers = 'Bcc: birthdaycheck@example.com';

        // Mail it
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    protected function addLog($message, $type, $description = ' ')
    {
        $this->execute($this->save('logs',[
            'message' => $message,
            'type' => $type,
            'description' => $description
        ]));
    }
} //End of the class
