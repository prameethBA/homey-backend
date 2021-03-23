<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Feedback extends Controller
{

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'add':

                        $data = [
                            'user_id' => (int)$this->secureParams['anonymous'] == 0 ? $this->secureParams['userId'] : 0,
                            'property_id' => $this->secureParams['propertyId'],
                            'feedback' => $this->secureParams['feedback']
                        ];

                        $stmt = $this->execute($this->save('feed',$data));
                        http_response_code(201);
                        echo $resolve = '{
                            "action": "true",
                            "message": "comment saved"
                        }';
                        break;
                    case 'get':

                        switch ($this->params[1]) {
                            case 'all':

                                $stmt = $this->execute($this->get('feed','_id as id', "property_id='{$this->secureParams['propertyId']}'"));
                                http_response_code(201);
                                echo json_encode($stmt->fetchAll());
                                break;
                            default:
                                $data = [
                                    'feedback.feedback as feedback',
                                    'feedback.created as created',
                                    'feedback.user_id as userId',
                                    'user.first_name as firstName',
                                    'user.last_name as lastName'
                                ];
                                $stmt = $this->execute($this->join('feed',$data, "LEFT JOIN user ON feedback.user_id=user._id WHERE feedback._id='{$this->params[1]}'"));
                                http_response_code(201);
                                echo json_encode($stmt->fetch());
                                break;
                        }
                        break;

                    case 'report':

                        switch ($this->params[1]) {
                            case 'save':

                                $data = [
                                    'user_id' => $this->secureParams['userId'],
                                    'property_id' => $this->secureParams['propertyId'],
                                    'reason' => $this->secureParams['reason'],
                                    'message' => $this->secureParams['message']
                                ];

                                $stmt = $this->execute($this->save('report',$data));
                                http_response_code(201);
                                echo $reject = '{
                                    "action": "true",
                                    "message": "Property reported."
                                }';
                                break;
                            case 'all':
                                $stmt = $this->execute($this->getAll('report'));
                                http_response_code(200);
                                echo json_encode($stmt->fetchAll());
                                break;
                        }
                        break;

                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");
        } catch (Exception $err) {
            http_response_code(200);
            die($reject = '{
                    "status": "500",
                    "error": "true",
                    "message": "' . $err->getMessage() . '"
            }');
        } //End of try catch

    } //End of post

    // Authenticate User 
    private function authenticate()
    {
        if (isset($this->secureParams['userId'], $this->secureParams['token'])) {
            if ($this->authenticateUser($this->secureParams['userId'], $this->secureParams['token'])) return true;
            else return false;
        } else return false;
    } //end of authenticateUser()

}//End of Class
