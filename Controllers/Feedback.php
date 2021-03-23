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
                            'user_id' => (int)$param['anonymous'] == 0 ? $param['userId'] : 0,
                            'property_id' => $param['propertyId'],
                            'feedback' => $param['feedback']
                        ];

                        $stmt = $this->execute($this->save('feed',$data));
                        $this->resolve('{
                            "action": "true",
                            "message": "comment saved"
                        }',201);

                        break;
                    case 'get':

                        switch ($this->params[1]) {
                            case 'all':

                                $stmt = $this->execute($this->get('feed','_id as id', "property_id='{$param['propertyId']}'"));
                                
                                $this->resolve(json_encode($stmt->fetchAll()),200);

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
                                
                                $this->resolve(json_encode($stmt->fetch()),201);
                                break;
                        }
                        break;

                    case 'report':

                        switch ($this->params[1]) {
                            case 'save':

                                $data = [
                                    'user_id' => $param['userId'],
                                    'property_id' => $param['propertyId'],
                                    'reason' => $param['reason'],
                                    'message' => $param['message']
                                ];

                                $stmt = $this->execute($this->save('report',$data));
                                
                                $this->reject('{
                                    "action": "true",
                                    "message": "Property reported."
                                }',201);

                                break;
                            case 'all':
                                $stmt = $this->execute($this->getAll('report'));
                                $this->resolve(json_encode($stmt->fetchAll()),200);
                                break;
                        }
                        break;

                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");
        } catch (Exception $err) {
            
            $this->reject('{
                "status": "500",
                "error": "true",
                "message": "' . $err->getMessage() . '"
        }',200);
        
        } //End of try catch

    } //End of post

    // Authenticate User 
    private function authenticate()
    {
        if (isset($param['userId'], $param['token'])) {
            if ($this->authenticateUser($param['userId'], $param['token'])) return true;
            else return false;
        } else return false;
    } //end of authenticateUser()

}//End of Class
