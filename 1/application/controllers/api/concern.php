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

class Concern extends REST_Controller 
{

    function get_post() 
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

            $myConcerns = array();
            $projects = array();
            $incubators = array(); 
            $investors = array(); 

            $this->load->model('concernModel');
            $this->load->model('projectModel');
            $this->load->model('incubatorModel');
            $this->load->model('investorModel');

            $this->load->model('concernModel');

            $results = $this->concernModel->getComments(
              array(
                    'userId' => $userId,
                    'concern' => '1'),
              array(
                    'page' => $page,
                    'pageSize' => $pageSize)
            );
            //我关注的项目
            if(count($results['projects'])){
                foreach ($results['projects'] as $result) {
                    if(strlen($result['role_id'])){
                        $res = $this->projectModel->getProject(array('id' => $result['role_id']));
                        $projects[] = array(
                                'projectId' => $res['projectId'],
                                'logoUrl' => $res['logoUrl'],
                                'name' => $res['projectName'],
                                'entrId' => $res['entreorentationId'],
                                'entrName' => $res['entreorentationName'],
                                'brief' => $res['brief'],
                                'processId' => $res['projectProcessId'],
                                'processName' => $res['projectProcessName'],
                                'praise' => count($res['praises']),
                                'concern' => count($res['concerns'])
                            );
                    }
                }

            }
            //我关注的孵化器
            if(count($results['incubators'])){
                foreach ($results['incubators'] as $result) {
                    if(strlen($result['role_id'])){
                        $res = $this->incubatorModel->getIncubator(array('id' => $result['role_id']));
                        $incubators[] = array(
                                'incubatorId' => $res['incubatorId'],
                                'logoUrl' => $res['logoUrl'],
                                'name' => $res['incubatorName'],
                                'brief' => $res['brief'],
                                'praise' => count($res['praises']),
                                'concern' => count($res['concerns'])
                            );
                    }
                }

            }
            //我关注的投资人
            if(count($results['investors'])){
                foreach ($results['investors'] as $result) {
                    if(strlen($result['role_id'])){
                        $res = $this->investorModel->getInvestor(array('id' => $result['role_id']));
                        $investors[] = array(
                                'investorId' => $res['investorId'],
                                'logoUrl' => $res['logoUrl'],
                                'name' => $res['investorName'],
                                'brief' => $res['brief'],
                                'praise' => count($res['praises']),
                                'concern' => count($res['concerns'])

                            );
                    }
                }

            }
            // $inc = $this->investorModel->getInvestor(array('id' => $result['role_id']));
            // echo '<pre>';print_r($incubators);exit();

            $myConcerns = array(
                    'projects' => $projects,
                    'incubators' => $incubators,
                    'investors' => $investors
                );            

            $sysList = array(
                    'response_status' => 'success',
                    'response_success_data' => array('myConcerns' => $myConcerns));
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

            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }

            $roleId = trim($jsonArray->roleId);
            $role_id = trim($jsonArray->role_id);

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

            if(count($errors))
            {
                $this->response(array("response_status"=>"error",
                  'response_error_data' => $errors), 
                200);
            }


            $this->load->model('commentModel');

            //现有的推荐数
            $res = $this->commentModel->getComments(array(
                    'role' => $roleId,
                    'role_id' => $role_id,
                    'concern' => 1
                ));
            $concerns = count($res);

            //该用户是否已经推荐过
            $res = $this->commentModel->getComments(array(
                    'userId' => $userId,
                    'role' => $roleId,
                    'role_id' => $role_id,
                    'concern' => 1
                ));
            if(!count($res)){
                $permission = 1;
                $commentId = uniqid('comment');
                $added = $this->commentModel->addComment(array(
                    'id' => $commentId,
                    'userId' => $userId,
                    'role' => $roleId,
                    'role_id' => $role_id,
                    'concern' => '1'
                    ));
            }else{
                $permission = 0;
                $added = 0;
            }

            $message = $added?'添加成功':'添加失败';
            $concerns = $added?$concerns+1:$concerns;

            $result = array(
                'code' => 200,
                'permission' => $permission,
                'added' => $added,
                'message' => $message,
                'concerns' => $concerns);
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