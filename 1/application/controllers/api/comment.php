<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package     CodeIgniter
 * @subpackage  Rest Server
 * @category    Controller 
 * @author      Phil Sturgeon
 * @link        http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php'; 
require APPPATH.'/libraries/messagexsend.php';

class Comment extends REST_Controller  
{   

    function getMessages_post()
    {
        try
        {
            $jsonArray = json_decode(file_get_contents('php://input')); 

            $errors = array();          
            if (!array_key_exists('userId', $jsonArray)) {
              array_push($errors, array(
                'code' => '缺少参数',
                'message' => '缺少参数:userId'
                ));
            $this->response(array(
                "response_status"=>"error",
                "response_error_data" => $errors), 200);
            }
            $userId = trim($jsonArray->userId);

            if (!array_key_exists('page', $jsonArray)||!array_key_exists('pageSize', $jsonArray)) {
            array_push($errors, array(
                'code' => '缺少参数',
                'message' => '缺少参数page或者pageSize'
              ));
            $this->response(array(
                "response_status"=>"error",
                "response_error_data" => $errors), 200);
            }
            $page = trim($jsonArray->page); 
            $pageSize = trim($jsonArray->pageSize);

          if ($page<1) {
            array_push($errors, array(
              'code' => '203',
              'message' => '页码必须大于0'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          } 

            $messages = array();
            $concerns = array();
            $praises = array(); 

            $this->load->model('commentModel');

            $concerns = $this->commentModel->getComments(
              array(
                    'userId' => $userId,
                    'concern' => '1'),
              array(
                    'page' => $page,
                    'pageSize' => $pageSize)
            );

            $praises = $this->commentModel->getComments(
              array(
                    'userId' => $userId,
                    'praise' => '1'),
              array(
                    'page' => $page,
                    'pageSize' => $pageSize)
              );
 
            $messages = array(
                    'concerns' => $concerns,
                    'praises' => $praises);

            $sysList = array(
                    'response_status' => 'success',
                    'response_success_data' => array('messages' => $messages));
            $this->response($sysList, 200);
          

        }
        catch(Exception $e)
        {
            $this->response(array("response_status"=>"error",
                'response_error_data' => array(
                                        array(
                                      'code' => 10000,
                                      'message' => $this->lang->line('010000'). '\n'. $e->getMessage()))), 
            500);
        }  
    } 

    function add_post()
    {
        try
        {
            $jsonArray = json_decode(file_get_contents('php://input')); 
            $errors = array();
             
            if (!array_key_exists('userId', $jsonArray)) {
                array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:userId'
                  ));
                $this->response(array(
                  "response_status"=>"error",
                  "response_error_data" => $errors), 200);
            }
            $userId = trim($jsonArray->userId); 

            if (!array_key_exists('roleId', $jsonArray)) {
                array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:roleId'
                  ));
            }
            if (!array_key_exists('role_id', $jsonArray)) {
                array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:role_id'
                  ));
            }
            if (!array_key_exists('content', $jsonArray)) {
                array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:content'
                  ));
            }

            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }

            $roleId = trim($jsonArray->roleId);
            $role_id = trim($jsonArray->role_id);
            $content = trim($jsonArray->content);

            if (!strlen($roleId)) {
                array_push($errors, array(
                  'code' => '203',
                  'message' => 'roleId不能为空'
                  ));
            }
            if (!strlen($role_id)) {
                array_push($errors, array(
                  'code' => '203',
                  'message' => 'role_id不能为空'
                  ));
            }
            if (!strlen($content)) {
                array_push($errors, array(
                  'code' => '203',
                  'message' => 'content不能为空'
                  ));
            }

            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }


            $this->load->model('commentModel');

            $res = $this->commentModel->getComments(array(
                    'userId' => $userId,
                    'role' => $roleId,
                    'role_id' => $role_id,
                    'comment' => 1
                ));
            if(!count($res)){
                $permission = 1;
                $commentId = uniqid('comment');
                $added = $this->commentModel->addComment(array(
                    'id' => $commentId,
                    'userId' => $userId,
                    'role' => $roleId,
                    'role_id' => $role_id,
                    'content' => $content,
                    'comment' => '1'
                    ));
            }else{
                $permission = 0;
                $added = 0;
                $commentId = '';
            } 

            $message = $added?'添加成功':'添加失败';

            $result = array(
                'code' => 200,
                'permission' => $permission,
                'added' => $added,
                'message' => $message,
                'commentId' => $commentId);

            $sysList = array(
                    'response_status' => 'success',
                    'response_success_data' => $result);
            $this->response($sysList, 200);
          

        }
        catch(Exception $e)
        {
            $this->response(array("response_status"=>"error",
                'response_error_data' => array(
                                        array(
                                      'code' => 10000,
                                      'message' => $this->lang->line('010000'). '\n'. $e->getMessage()))), 
            500);
        }  
    } 

    function get_post()
    {
        try
        {
            $jsonArray = json_decode(file_get_contents('php://input')); 
            $errors = array();
             
            // if (!array_key_exists('userId', $jsonArray)) {
            //     array_push($errors, array(
            //       'code' => '缺少参数',
            //       'message' => '缺少参数:userId'
            //       ));
            //     $this->response(array(
            //       "response_status"=>"error",
            //       "response_error_data" => $errors), 200);
            // }
            // $userId = trim($jsonArray->userId); 

            if (!array_key_exists('role_id', $jsonArray)) {
                array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:role_id'
                  ));
            }
            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }

            $role_id = trim($jsonArray->role_id);

            if (!strlen($role_id)) {
                array_push($errors, array(
                  'code' => '203',
                  'message' => 'role_id不能为空'
                  ));
            }
            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }
            if (!array_key_exists('page', $jsonArray)||!array_key_exists('pageSize', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数page或者pageSize'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
            }
            $page = trim($jsonArray->page);
            $pageSize = trim($jsonArray->pageSize);

            if ($page<1) {
                array_push($errors, array(
                  'code' => '203',
                  'message' => '页码必须大于0'
                  ));
                $this->response(array(
                  "response_status"=>"error",
                  "response_error_data" => $errors), 200);
            }


            $this->load->model('commentModel');
            
            $res = $this->commentModel->getComment(array(
                    'role_id' => $role_id,
                    'page' => $page,
                    'pageSize' => $pageSize
                ));
            $commentNum = count($res);
            $comments = count($commentNum)?$res:array();
            
            $result = array(
                'comments' => $comments,
                'commentNum' => $commentNum);

            $sysList = array(
                    'response_status' => 'success',
                    'response_success_data' => $result);
            $this->response($sysList, 200);
          

        }
        catch(Exception $e)
        {
            $this->response(array("response_status"=>"error",
                'response_error_data' => array(
                                        array(
                                      'code' => 10000,
                                      'message' => $this->lang->line('010000'). '\n'. $e->getMessage()))), 
            500);
        }  
    }
}