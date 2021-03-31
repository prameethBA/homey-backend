<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

// require payment configurations
require_once('Core/Config/PaymentGateway.php');

use Core\Config\PaymentGateway as PaymentGateway;

class Payment extends Controller
{
    public function Request($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $propertyId = $param['propertyId'];
            $amount = $param['amount'];

            $result['order_id'] = 'reserve' . time();

            $stmt = $this->execute($this->get('property', 'title, user_id as payee_id', ("_id = '" . $propertyId . "'")));
            $resultSet = $stmt->fetch();
            $result['items'] = 'Reserve: ' . $resultSet['title'];
            $payeeId = $resultSet['payee_id'];

            $stmt = $this->execute($this->join('user', '
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

            $stmt = $this->execute($this->get('user', '
                                    address1,
                                    address2,
                                    address3,
                                    city
                                    ', ("user_id = '" . $payeeId . "'")));

            $resultSet = $stmt->fetch();
            $result['delivery_address'] = isset($resultSet['address1']) ? $resultSet['address1'] . ", " . $resultSet['address2'] . ", " . $resultSet['address3'] : "";
            $result['delivery_city'] = $resultSet['city'];
            $result['merchant_id'] = PaymentGateway::$merchantId;
            $result['return_url'] = PaymentGateway::$returnUrl;
            $result['cancel_url'] = PaymentGateway::$cancelUrl;
            $result['notify_url'] = PaymentGateway::$notifyUrl;
            $result['currency'] = PaymentGateway::$currency;

            $result['amount'] = $amount;
            $result['custom_1'] = $propertyId;

            $this->execute($this->save('payment', [
                'request' => json_encode($result),
                'order_id' => $result['order_id'],
                'user_id' => $userId,
                'property_id' => $propertyId,
                'status_code' => 3
            ]));
            $this->resolve(json_encode($result), 200);
            $this->addLog($userId . " resquest payment gateway request on " . $propertyId, "payment-gatway-request", json_encode($result));
        } catch (Exception $err) {
            $this->addLog("Payment gateway request failed", "payment-gatway-request-failed", (string)$err->getMessage());

            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    public function Notify()
    {
        try {
            // $this->addLog(" resquest payment gateway request on ", "payment-gatway-request", "json_encode");


            $this->execute($this->update(
                'payment',
                [
                    'payment_id' => $_POST['payment_id'],
                    'payhere_amount' => $_POST['payhere_amount'],
                    'payhere_currency' => $_POST['payhere_currency'],
                    'status_code' => $_POST['status_code'],
                    'payment_type' => $_POST['custom_2'],
                    'status_message' => $_POST['status_message'],
                    'method' => $_POST['method'],
                    'card_holder_name' => $_POST['card_holder_name'],
                    'card_no' => $_POST['card_no'],
                    'card_expiry' => $_POST['card_expiry'],
                    'recurring' => $_POST['recurring'],
                ],
                "order_id = '{$_POST['order_id']}'"
            ));

            $stmt = $this->execute($this->get('payment', "user_id", "order_id = '" . (string)$_POST['order_id'] . "'"));

            $payeeId = (int)($stmt->fetch()['user_id']); //payee ID
            $this->execute($this->save('notification', ['user_id' => $payeeId, 'message' => (string)$_POST['custom_1'] . " Reserve successful"]));

            //property owner id
            $stmt = $this->execute($this->get('property', "user_id as userId", "_id = '" . (string)$_POST['custom_1'] . "'"));
            $propertyOwnerId = (int)$stmt->fetch()['userId']; //owner ID

            //saved for reserved
            $this->execute($this->save('propertyreserved', [
                'property_id' => $_POST['custom_1'],
                'user_id' => $payeeId
            ]));

            //update property setting as reserved = 1
            $this->execute($this->update('propertysettings', [
                'reserved' => 1
            ], "property_id='{$_POST['custom_1']}'"));

            //send mail to payee
            $stmt = $this->execute($this->get('login', 'email, mobile', "user_id=" . (int)$payeeId));
            $result = $stmt->fetch();
            $payeeEmail = $result['email'];
            $payeeMobile = $result['mobile'];

            $stmt = $this->execute($this->get('user', 'first_name, last_name', "user_id=" . (int)$payeeId));
            $result = $stmt->fetch();
            $payeeName = $result['first_name'] . " " . $result['last_name'];
            //get owner details
            $stmt = $this->execute($this->get('login', 'email', "user_id=" . (int)$payeeId));
            $ownerEmail = $stmt->fetch()['email'];

            $content = "Your property <a href='https://homey.lk/property/{$_POST['custom_1']}'>{$_POST['custom_1']} - Click here to view</a> has been reserved by {$payeeName}, Email : {$payeeEmail}, Mobile: {$payeeMobile}";
            //include email
            require("/assets/email-common.php");
            $this->sendMail($ownerEmail, $message, 'Property reserved - Payment approved at Homey.lk'); //semd email to property owner

            $content = "Your payment on <a href='https://homey.lk/property/{$_POST['custom_1']}'>{$_POST['custom_1']} - Click here to view</a> has been approved.<br>Thank you for deal with Homey.lk";
            //require email
            require("/assets/email-common.php");
            $this->sendMail($ownerEmail, $message, 'Property reserved - Payment approved at Homey.lk'); //semd email to payee


            //add a notifications
            $this->execute($this->save('notification', ['user_id' => $propertyOwnerId, 'message' => $_POST['custom_1'] . " Reserved successfully"]));

            $this->addLog((string)$_POST['order_id'] . " payment successfull as " . (string)$_POST['payment_id'], "payment-success");
        } catch (Exception $err) {
            $this->addLog("CTRITICAL:Failed to add to the database.", "payment-record-adding-failed", (string)$err->getMessage());

            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }


    // all-transactions
    public function AllTransactions($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->getAll('payment', '*', "user_id =" . (int)$userId));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
          "status": "500",
          "error": "true",
          "message": "' . $err->getMessage() . '"
      }', 200);
        }
    }


    // all-paid
    public function AllPaid($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->join('payment', '*', "INNER JOIN property ON property._id=payment.property_id WHERE payment.user_id=" . (int)$userId));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
          "status": "500",
          "error": "true",
          "message": "' . $err->getMessage() . '"
      }', 200);
        }
    }

    // all-received
    public function AllReceived($params, $param)
    {
        try {

            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $stmt = $this->execute($this->join('payment', '*', "INNER JOIN property ON property._id=payment.property_id WHERE property.user_id=" . (int)$userId));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
          "status": "500",
          "error": "true",
          "message": "' . $err->getMessage() . '"
      }', 200);
        }
    }

    // public function post()
    // {
    //     try {
    //         if (isset($this->params[0])) {
    //             switch ($this->params[0]) {
    //                 case 'request':
    //                     if (!$this->authenticate()) throw new Exception("Unautherized request.");
    //                     $userId = $param['userId'];
    //                     $token = $param['token'];
    //                     $propertyId = $param['propertyId'];
    //                     $amount = $param['amount'];

    //                     if ($this->authenticateUser($userId, $token)) {

    //                         $result['order_id'] = 'reserve' . time();

    //                         $stmt = $this->execute($this->get('property', 'title, user_id as payee_id', ("_id = '" . $propertyId . "'")));
    //                         $resultSet = $stmt->fetch();
    //                         $result['items'] = 'Reserve: ' . $resultSet['title'];
    //                         $payeeId = $resultSet['payee_id'];

    //                         $stmt = $this->execute($this->join('user', '
    //                                 user.first_name as first_name,
    //                                 user.last_name as last_name,
    //                                 login.email as email,
    //                                 login.mobile as phone,
    //                                 user.address1 as address1,
    //                                 user.address2 as address2,
    //                                 user.address3 as address3,
    //                                 user.city as city
    //                                 ', ("INNER JOIN login ON user.user_id = login.user_id WHERE user.user_id = '{$userId}'")));
    //                         $resultSet = $stmt->fetch();
    //                         $result['first_name'] = $resultSet['first_name'];
    //                         $result['last_name'] = $resultSet['last_name'];
    //                         $result['email'] = $resultSet['email'];
    //                         $result['phone'] = $resultSet['phone'];
    //                         $result['address'] = ($resultSet['address1'] != NULL) ? $resultSet['address1'] . ", " . $resultSet['address2'] . ", " . $resultSet['address3'] : "";
    //                         $result['city'] = $resultSet['city'];

    //                         $stmt = $this->execute($this->get('user', '
    //                                 address1,
    //                                 address2,
    //                                 address3,
    //                                 city
    //                                 ', ("user_id = '" . $payeeId . "'")));

    //                         $resultSet = $stmt->fetch();
    //                         $result['delivery_address'] = ($resultSet['address1'] != NULL) ? $resultSet['address1'] . ", " . $resultSet['address2'] . ", " . $resultSet['address3'] : "";
    //                         $result['delivery_city'] = $resultSet['city'];

    //                         $result['merchant_id'] = PaymentGateway::$merchantId;
    //                         $result['return_url'] = PaymentGateway::$returnUrl;
    //                         $result['cancel_url'] = PaymentGateway::$cancelUrl;
    //                         $result['notify_url'] = PaymentGateway::$notifyUrl;
    //                         $result['currency'] = PaymentGateway::$currency;

    //                         $result['amount'] = $amount;
    //                         $result['custom_1'] = $propertyId;

    //                         $this->execute($this->save('payment', [
    //                             'request' => json_encode($result),
    //                             'order_id' => $result['order_id'],
    //                             'user_id' => $userId,
    //                             'property_id' => $propertyId,
    //                             'status_code' => 3
    //                         ]));
    //                         $this->resolve(json_encode($result), 200);
    //                     }
    //                     break;

    //                 case 'notify':

    //                     // $fp = fopen('data.txt', 'a'); //opens file in append mode  
    //                     // fwrite($fp, ' this is additional text');
    //                     // fwrite($fp, PropertyReserved::save([
    //                     //     'property_id' => $_POST['custom_2'],
    //                     //     'user_id' => "(" . PaymentModel::get("user_id", "order_id = \"{$_POST['order_id']}\"") . ")"
    //                     // ]));
    //                     // fclose($fp);

    //                     break;

    //                 default:
    //                     throw new Exception("Invalid parameter");
    //             } //End of the switch

    //         } else throw new Exception("Invalid request.No parameters given");
    //     } catch (Exception $err) {

    //         $this->reject('{
    //                 "status": "500",
    //                 "error": "true",
    //                 "message": "' . $err->getMessage() . '"
    //             }', 500);
    //     }
    // } //End of POST

    // // Authenticate User 
    // private function authenticate()
    // {
    //     if (isset($param['userId'], $param['token'])) {
    //         if ($this->authenticateUser($param['userId'], $param['token'])) return true;
    //         else return false;
    //     } else return false;
    // } //end of authenticateUser()

}//End of Class
