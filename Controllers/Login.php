<?php

namespace Controllers;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

class Login extends BaseController{

    public function get() {
        $resolve  = '{
            status:200,
            data:{
                message: "Ok"
            }
        }';
        $reject = '{
            status: 500,
            message: "Internal Server Error"
        }';

        echo ($resolve);
    }
}
