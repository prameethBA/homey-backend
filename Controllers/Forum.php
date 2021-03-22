<?php

namespace Controllers;

use Exception;

require_once('Core/BaseController.php');

use Core\BaseController as BaseController;

require_once('Models/Forum.php');

use Models\Forum as ForumModel;

require_once('Core/DB/DB.php');

use Core\DB\DB as DB;

class Forum extends BaseController
{

    public function __construct($params, $secureParams)
    {
        parent::__construct($params, $secureParams);
        new ForumModel();
    }

    public function post()
    {
        try {
            if (isset($this->params[0])) {
                if (!$this->authenticate()) throw new Exception("Unautherized request.");
                switch ($this->params[0]) {
                    case 'create':

                        $data = [
                            'user_id' => $this->secureParams['userId'] ,
                            'title' => $this->secureParams['title'],
                            'content' => $this->secureParams['content'],
                        ];

                        $stmt = DB::execute(ForumModel::save($data));
                        http_response_code(201);
                        echo $resolve = '{
                            "action": "true",
                            "message": "Post created"
                        }';
                        break;
                        /*
                    case 'get':

                        switch ($this->params[1]) {
                            case 'all':

                                $stmt = DB::execute(Feed::get('_id as id', "property_id='{$this->secureParams['propertyId']}'"));
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
                                $stmt = DB::execute(Feed::join($data, "LEFT JOIN user ON feedback.user_id=user._id WHERE feedback._id='{$this->params[1]}'"));
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

                                $stmt = DB::execute(Report::save($data));
                                http_response_code(201);
                                echo $reject = '{
                                    "action": "true",
                                    "message": "Property reported."
                                }';
                                break;
                            case 'all':
                                $stmt = DB::execute(Report::getAll());
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
