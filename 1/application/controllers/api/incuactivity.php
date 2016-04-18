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

class Incuactivity extends REST_Controller
{
    function get_post()
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
          if (!array_key_exists('incubatorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incubatorId'
              ));            
          }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $incubatorId = trim($jsonArray->incubatorId);

          if (!strlen($incubatorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'salaryId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }


          $this->load->model('incubatorModel');   
          $this->load->model('investorModel');     
          $activities = array();
          
          $result =  $this->incubatorModel->getIncubator(array('id' => $incubatorId));
          $activities = $result['activities'];
          
          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('activities' => $result['activities']));
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
              'code' => '却少参数',
              'message' => '却少参数:userId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $userId = trim($jsonArray->userId);
          if (!array_key_exists('incubatorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incubatorId'
              ));            
          }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $incubatorId = trim($jsonArray->incubatorId);
          $name = trim($jsonArray->name);
          $date = trim($jsonArray->date);
          $detail = trim($jsonArray->detail);
          if (!strlen($incubatorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          }
          if (!strlen($name)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'name不能为空'
              ));
          }
          if (!strlen($incubatorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          }
          if (!strlen($date)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'date不能为空'
              ));
          }
          if (!strlen($detail)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'detail不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('incuactivityModel');

          $incuActId = uniqid('incuact');
          $added = $result =  $this->incuactivityModel->add(array(
              'id' => $incuActId,
              'incubatorId' => $incubatorId,
              'name' => $name,
              'time' => $date,
              'detail' => $detail));
          $message = $added?'添加成功':'添加失败';
          $result = array(
            'code' => 200,
            'added' => $added,
            'message' => $message,
            'incuActId' => $added?$incuActId:'');

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

    function delete_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
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
          if (!array_key_exists('incuActId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incuActId'
              ));            
          }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $incuActId = trim($jsonArray->incuActId);

          if (!strlen($incuActId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          }          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('incuactivityModel');

          $deleted = $result =  $this->incuactivityModel->delete(array('id' => $incuActId));
          $message = $deleted?'删除成功':'删除失败';

          $result = array(
            'code' => 200,
            'added' => $deleted,
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
          if (!array_key_exists('incuActId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incuActId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $incuActId = trim($jsonArray->incuActId);

          $this->load->model('incuactivityModel');          
          
          $data = array();
          array_key_exists('name', $jsonArray)?$data['name']=trim($jsonArray->name):false;
          array_key_exists('date', $jsonArray)?$data['time']=trim($jsonArray->date):false;
          array_key_exists('detail', $jsonArray)?$data['detail']=trim($jsonArray->detail):false;

          if(!count($data)){
              $result = array(
                'code' => 205,
                'updated' => false,
                'message' => '不需要更新');
              $sysList = array('response_status' => 'success',
               'response_success_data' => $result);
              $this->response($sysList, 200);
          }

          $updated  = $this->incuactivityModel->update($incuActId , $data);
          $message = $updated?'更新成功':'更新失败';

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

    function addInvestor_post()
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
          if (!array_key_exists('incuActId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incuActId'
              ));            
          }
          if (!array_key_exists('investors', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investors'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $incuActId = trim($jsonArray->incuActId);
          $investors = $jsonArray->investors;

          if (!strlen($incuActId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          } 
          if (!is_array($investors)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          }  
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('incuactivityModel');

          $i = 0;
          $added = true;
          if(count($investors)){
            foreach ($investors as $investorId) {
              $added = $added&&$this->incuactivityModel->addInvestor(array(
                  'id' => uniqid('incuInvs'.$i),
                  'incubatorActivityId' => $incuActId,
                  'investorId' => $investorId,
                ));
            }
          }
          
          $message = $added?'添加成功':'添加失败';
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

    function deleteInvestor_post()
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
          if (!array_key_exists('actInvId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:actInvId'
              ));            
          }          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $actInvId = trim($jsonArray->actInvId);

          if (!strlen($actInvId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
          }           
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('incuactivityModel');

          $deleted = $result =  $this->incuactivityModel->deleteInvestor(array('id' => $actInvId));
          $message = $deleted?'删除成功':'删除失败';

          $result = array(
            'code' => 200,
            'added' => $deleted,
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