<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;

class Forum extends Controller
{


    //create new post
    public function Create($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");


            $data = [
                'user_id' => $userId,
                'title' => $param['title'],
                'content' => $param['content'],
            ];

            $this->execute($this->save('forum', $data));

            $this->resolve('{
                "action": "true",
                "message": "Post created"
            }', 201);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get all posts
    public function All()
    {
        try {

            $stmt = $this->execute($this->getAll('forum', '*'));

            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }


    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'create':

                        break;
                        /*
                    case 'get':

                        switch ($this->params[1]) {
                            case 'all':

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
                                    'user_id' => $param['userId'],
                                    'property_id' => $param['propertyId'],
                                    'reason' => $param['reason'],
                                    'message' => $param['message']
                                ];

                                $stmt = $this->execute($this->save('report',$data));
                                http_response_code(201);
                                echo $reject = '{
                                    "action": "true",
                                    "message": "Property reported."
                                }';
                                break;
                            case 'all':''
                                $stmt = $this->execute('payment',getAll('report'));
                                http_response_code(200);
                                echo json_encode($stmt->fetchAll());
                                break;
                        }
                        break;
                    */
                    default:
                        throw new Exception("Invalid Request");
                }
            } else throw new Exception("Invalid Parmeters");
        } catch (Exception $err) {

            $this->reject('{
                "status": "500",
                "error": "true",
                "message": "' . $err->getMessage() . '"
        }', 200);
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
