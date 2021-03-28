<?php

namespace Controllers;

use Exception;

require_once('Core/Controller.php');

use Core\Controller as Controller;



class AdminSummary extends Controller
{



    /* +++ADMIN+++ */
    //count visitors
    public function All($params, $param)
    {
        try {
            $userId = (string)$param['userId'];
            if (!$this->authenticateAdmin($param['token'], $userId)) throw new Exception("Authentication failed.");

            //visitors
            $stmt = $this->execute($this->get('visitorcount', "COUNT(_id) as count", "MONTH(created) = MONTH(CURDATE())"));
            $result['visitor']['count'] = $stmt->fetch()['count'];
            $stmt = $this->execute($this->get('visitorcount', "COUNT(_id) as count"));
            $result['visitor']['totalCount'] = $stmt->fetch()['count'];

            //Ads
            $stmt = $this->execute($this->get('property', "COUNT(_id) as count", "MONTH(created) = MONTH(CURDATE())"));
            $result['ads']['count'] = $stmt->fetch()['count'];
            $stmt = $this->execute($this->get('property', "COUNT(_id) as count"));
            $result['ads']['totalCount'] = $stmt->fetch()['count'];

            //registerd users
            $stmt = $this->execute($this->get('user', "COUNT(_id) as count", "MONTH(created) = MONTH(CURDATE())"));
            $result['user']['count'] = $stmt->fetch()['count'];
            $stmt = $this->execute($this->get('user', "COUNT(_id) as count"));
            $result['user']['totalCount'] = $stmt->fetch()['count'];

            //pending approvals
            $stmt = $this->execute($this->get('property', "COUNT(property_status) as count", "property_status = 0"));
            $result['pending'] = $stmt->fetch()['count'];
            //rejected
            $stmt = $this->execute($this->get('property', "COUNT(property_status) as count", "property_status = 2"));
            $result['rejected'] = $stmt->fetch()['count'];

            //reports
            $stmt = $this->execute($this->get('report', "COUNT(_id) as count", "status = 0"));
            $result['reports']['pending'] = $stmt->fetch()['count'];
            $stmt = $this->execute($this->get('report', "COUNT(_id) as count", "status = 1"));
            $result['reports']['reviewd'] = $stmt->fetch()['count'];

            $this->resolve(json_encode($result), 200);
        } catch (Exception $err) {
            $this->reject('{
              "status": "500",
              "error": "true",
              "message": "' . $err->getMessage() . '"
          }', 200);
        }
    }


    //get traffic data for chart
    public function GetTraffic($params, $param)
    {
        try {
            $userId = (string)$param['userId'];
            if (!$this->authenticateAdmin($param['token'], $userId)) throw new Exception("Authentication failed.");

            //visitors
            $stmt = $this->execute($this->join('visitorcount', "DATE(created) AS date, COUNT(_id) as hits", "GROUP BY DATE(created) ORDER BY date"));
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
    //             if (!$this->authenticate()) throw new Exception("Unautherized request.");
    //             switch ($this->params[0]) {
    //                 case 'all-users':

    //                     $stmt = $this->execute(Login::join(
    //                         [
    //                             'user.user_id as userId',
    //                             'login.email',
    //                             'login.mobile',
    //                             'login.user_status as status',
    //                             'user.first_name as firstName',
    //                             'user.last_name as lastName'
    //                         ],
    //                         (", user WHERE login.user_id = user.user_id AND login.user_type = 0")
    //                     ));

    //                     $this->resolve(json_encode($stmt->fetchAll()),200);
    //                     break;

    //                 case 'all-admins':

    //                     $stmt = $this->execute(Login::join(
    //                         [
    //                             'user.user_id as userId',
    //                             'login.email',
    //                             'login.mobile',
    //                             'login.user_status as status',
    //                             'user.first_name as firstName',
    //                             'user.last_name as lastName'
    //                         ],
    //                         (", user WHERE login.user_id = user.user_id AND login.user_type = 1")
    //                     ));


    //                     $this->resolve(json_encode($stmt->fetchAll()),200);
    //                     break;

    //                 case 'get':

    //                     $stmt = $this->execute(Login::join(
    //                         [
    //                             'login.email',
    //                             'login.mobile',
    //                             'login.user_status as status',
    //                             'user.first_name as firstName',
    //                             'user.last_name as lastName'
    //                         ],
    //                         (", user WHERE login.user_id = user.user_id AND user.user_id = {$param['profile']}")
    //                     ));

    //                     $result['userData'] = $stmt->fetch();

    //                     $stmt = $this->execute($this->get('property',['_id', 'title', 'created'], ("user_id = {$param['profile']} AND privated = 0")));

    //                     $result['ownPropertyData'] = $stmt->fetchAll();

    //                     $this->resolve(json_encode($result),200);

    //                     break;


    //                 default:
    //                     throw new Exception("Invalid Request");
    //             }
    //         } else throw new Exception("Invalid Parmeters");
    //     } catch (Exception $err) {

    //         $this->reject('{
    //             "status": "500",
    //             "error": "true",
    //             "message": "' . $err->getMessage() . '"
    //     }',200);
    //     } //End of try catch

    // } //End of GET

    // // Authenticate Admin 
    // private function authenticate()
    // {
    //     if (isset($param['userId'], $param['token'])) {
    //         if ($this->authenticateAdmin($param['userId'], $param['token'])) return true;
    //         else return false;
    //     } else return false;
    // }
}//End of Class
