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

class Recommend extends REST_Controller
{

    function getProjects_post()
    {
      try
      {

          $errors = array();
          
          $this->load->model('IndexModel');
          $this->load->model('ProjectModel');
          $results = $this->IndexModel->getIndex();

          //推荐项目
          $rcdProjects = array();
          if(count($results['recProjects'])){
            foreach ($results['recProjects'] as $proId) {
              $res = $this->ProjectModel->getProject(array('id' => $proId));
              if(count($res)){
                  $rcdProjects[] = array(
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['projectName'],
                      'entreOrentation' => $res['entreorentationName'],
                      'brief' => $res['brief'],
                      'process' => $res['projectProcessName'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
              }
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('rcdProjects' => $rcdProjects));

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

    function addProjects_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input'));
          $errors = array();

          if (!array_key_exists('projectIds', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectIds'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectIds = $jsonArray->projectIds;

          if (!is_array($projectIds)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectIds必须为数组'
              ));
          }

          $added = true;
          $this->load->model('recommendModel');
          $this->load->model('CommentModel');
          $i = 0;
          $roleId = 'role55388437541c3';
          foreach ($projectIds as $projectId) {
            $commentId = uniqid('comment'.$i);
            $added = $added&&$this->CommentModel->addComment(array(
                    'id' => $commentId,
                    // 'userId' => $curUserId,
                    'role' => $roleId,
                    'role_id' => $projectId,
                    'concern' => '1'
                ));
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

    function getTeams_post()
    {
      try
      {

          $errors = array();

          $recTeams = array();
          $this->load->model('recommendModel');
          $results = $this->recommendModel->getTeams();//取出推荐数量前10位的团队
          if(count($results)){
            foreach ($results as $result) {
              $recTeams[] = array(
                  'teamId' => $result->id,
                  'logoUrl' => $result->logoImgId,
                  'name' => $result->name,
                  'brief' => $result->brief
                );
            }
          }

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('recTeams' => $recTeams));
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

    function addTeams_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input'));
          $errors = array();

          if (!array_key_exists('teamIds', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:teamIds'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $teamIds = $jsonArray->teamIds;

          if (!is_array($teamIds)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'teamIds必须为数组'
              ));
          }

          $added = true;
          $this->load->model('recommendModel');
          foreach ($teamIds as $teamId) {
            $added = $added&&$this->recommendModel->addTeamConcern($teamId);
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


}