<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Payment.php');

use Models\Payment as PaymentModel;

require_once('Models/Property.php');

use Models\Property as Property;

require_once('Models/User.php');

use Models\User as User;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

// require payment configurations
require_once('Core/Config/PaymentGateway.php');

use Core\Config\PaymentGateway as PaymentGateway;

class Payment extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new PaymentModel();
        new Property();
        new User();
    }

    public function get()
    {
        // try {
        //     $stmt = DB::execute(PaymentModel::getAll());

        //     http_response_code(200);
        //     echo $resolve = '{
        //         "data":' . json_encode($stmt->fetchAll()) . '
        //     }
        //     ';
        // } catch (Exception $err) {
        //     http_response_code(500);
        //     die($reject = '{
        //         "data": {
        //             "error": "true",
        //             "message": "' . $err->getMessage() . '"
        //         }
        //     }');
        // }
    } //End of GET

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if ($this->params[0] == 'notify') {
                    $fp = fopen('/data.txt', 'a'); //opens file in append mode  
                    fwrite($fp, ' this is additional text ');
                    fwrite($fp, 'appending data');
                    fclose($fp);
                }
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'request':

                        $userId = $this->secureParams['userId'];
                        $token = $this->secureParams['token'];
                        $propertyId = $this->secureParams['propertyId'];
                        $amount = $this->secureParams['amount'];

                        if ($this->authenticateUser($userId, $token)) {

                            $result['order_id'] = 'reserve' . time();

                            $stmt = DB::execute(Property::get('title, user_id as payee_id', ("_id = '" . $propertyId . "'")));
                            $resultSet = $stmt->fetch();
                            $result['items'] = 'Reserve: ' . $resultSet['title'];
                            $payeeId = $resultSet['payee_id'];

                            $stmt = DB::execute(User::join('
                                    user.first_name as first_name,
                                    user.last_name as last_name,
                                    login.email as email,
                                    login.mobile as phone,
                                    user.address1 as address1,
                                    user.address2 as address2,
                                    user.address3 as address3,
                                    user.city as city
                                    ', ("INNER JOIN login ON user.user_id = login.user_id WHERE user.user_id = '{$userId}'")));
                            $resultSet = $stmt->fetch();
                            $result['first_name'] = $resultSet['first_name'];
                            $result['last_name'] = $resultSet['last_name'];
                            $result['email'] = $resultSet['email'];
                            $result['phone'] = $resultSet['phone'];
                            $result['address'] = ($resultSet['address1'] != NULL) ? $resultSet['address1'] . ", " . $resultSet['address2'] . ", " . $resultSet['address3'] : "";
                            $result['city'] = $resultSet['city'];

                            $stmt = DB::execute(User::get('
                                    address1,
                                    address2,
                                    address3,
                                    city
                                    ', ("user_id = '" . $payeeId . "'")));

                            $resultSet = $stmt->fetch();
                            $result['delivery_address'] = ($resultSet['address1'] != NULL) ? $resultSet['address1'] . ", " . $resultSet['address2'] . ", " . $resultSet['address3'] : "";
                            $result['delivery_city'] = $resultSet['city'];

                            $result['merchant_id'] = PaymentGateway::$merchantId;
                            $result['return_url'] = PaymentGateway::$returnUrl;
                            $result['cancel_url'] = PaymentGateway::$cancelUrl;
                            $result['notify_url'] = PaymentGateway::$notifyUrl;
                            $result['currency'] = PaymentGateway::$currency;

                            $result['amount'] = $amount;
                            $result['custom_1'] = $propertyId;

                            http_response_code(200);
                            echo (json_encode($result));
                        }
                        break;

                    default:
                        throw new Exception("Invalid parameter");
                } //End of the switch

            } else throw new Exception("Invalid request.No parameters given");
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
                }');
        }
    } //End of POST

    // Authenticate User 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    } //end of authenticateUser()

}//End of Class
