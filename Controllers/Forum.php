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
    public function All($params)
    {
        try {

            if (isset($params[0]))
                $stmt = $this->execute($this->get('forum', '*', " user_id = " . (int)$params[0]));
            else
                $stmt = $this->execute($this->get('forum', '*'));

            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //get comments 
    public function GetComments($params, $param)
    {
        try {
            $stmt = $this->execute($this->join(
                'forumcomment',
                'f.user_id as user_id,
            f.created as created,
            f.comment as comment,
            u.first_name as firstName,
            u.last_name as lastName
            ',
                "f INNER JOIN user u
            WHERE u.user_id = f.user_id 
            AND f.forum_id=" . (int)$params[0]
            ));
            $this->resolve(json_encode($stmt->fetchAll()), 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //Removev post
    public function Remove($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            // if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");
            if (!$this->authenticateAdmin($param['token'], $userId))
                if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $this->execute($this->delete('forum', '_id = ' . (int)$params[0]));

            $this->resolve('{
                "action": "true",
                "message": "Post deleted"
            }', 200);
        } catch (Exception $err) {
            $this->reject('{
             "status": "500",
             "error": "true",
             "message": "' . $err->getMessage() . '"
         }', 200);
        }
    }

    //Add new comment
    public function AddNewComment($params, $param)
    {
        try {
            $userId = (string)$param['userId'];

            if (!$this->authenticateUser($param['token'], $userId)) throw new Exception("Authentication failed.");

            $this->execute($this->save('forumcomment', [
                'user_id' => $userId,
                'forum_id' => $param['forumId'],
                'comment' => $param['comment'],
            ]));

            $this->resolve('{
                "action": "true",
                "message": "New comment added"
            }', 201);
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
