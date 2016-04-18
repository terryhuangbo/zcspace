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

class Advice extends REST_Controller
{

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

          if (!array_key_exists('content', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:content'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $content = trim($jsonArray->content);
          $userId = trim($jsonArray->userId);
          if($content==''){
            array_push($errors, array(
              'code' => 202,
              'message' => '评论不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $this->load->model('AdviceModel');
          $added = $this->AdviceModel->addAdvice(array(
              'id' => uniqid('advice'),
              'userId' => $userId,
              'content' => $content
            ));

          $message = $added?'添加成功！':'添加失败！';
          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message);
          
          $sysList = array('response_status' => 'success',
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

    function update_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $curUserId = $this->session->userdata('userId');

          $errors = array();
          if(!$curUserId)
          {
            array_push($errors, array(
              'code' => 201,
              'message' => '还未登陆，请先登陆'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          } 

          if (!array_key_exists('content', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:content'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $content = trim($jsonArray->content);
          if($content==''){
            array_push($errors, array(
              'code' => 202,
              'message' => '评论不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $this->load->model('AdviceModel');
          $updated = $this->AdviceModel->updateAdvice(array(
              'userId' => $curUserId,
              'content' => $content
            ));

          $message = $updated?'更新成功！':'更新失败！';
          $result = array(
                'code' => 200,
                'updated' => $updated,
                'message' => $message);
          
          $sysList = array('response_status' => 'success',
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