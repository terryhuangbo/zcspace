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

class Project extends REST_Controller  
{  

    function getInfo_post()    
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

          $this->load->model('projectModel');

          //删除添加未完成的记录
          $this->projectModel->deleteProject(array(
            'userId' => $userId,
            'completed' => 0
          ));

          $projects = array();
          $schoolId = uniqid('school');
          $results = $this->projectModel->getProjectsByUserId(array('userId' => $userId,
                'page' => $page,
                'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $result) {
                $projectId = $result->id;
                $res = $this->projectModel->getProject(array('id' => $projectId));
                if(count($res)){
                    $projects[] = array(
                        'projectId' => $res['projectId'],
                        'fouderUserId' => $res['projectFouderUserId'],
                        'logoUrl' => $res['logoUrl'],
                        'name' => $res['projectName'],
                        'location' => array(
                                'province' => $res['province'],
                                'city' => $res['city'],
                                'county' => $res['county'] 
                            ),
                        'entreOrentation' => $res['entreOrentation'],
                        'tags' => $res['tags'],
                        'brief' => $res['brief'],
                        'introduce' => $res['introduction'],
                        'advantage' => $res['advantage'],
                        'prospect' => $res['prospect'],
                        'team' => array(
                                'num' => count($res['team']['members']),
                                'teamId' => $res['team']['teamId'],
                                'members' => $res['team']['members']
                            ),
                        'finance' => array(
                                'financeSource' => $res['financeSource'],
                                'financeAmount' => $res['financeAmount']
                            ),
                        'process' => $res['projectProcess'],
                        'newPartner' => $res['newPartner'],
                        'projectImages' => $res['projectImages'],
                        
                    );
                }
            }
          }

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('projects' => $projects));

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

    function getData1_post()
    {
      try
      {

          $this->load->model('projectModel');
          $rank = array();
          $entreOrentation = array();
          $province = array();
          $city = array();

          $results = $this->projectModel->getRank();
          if(count($results)){
            foreach ($results as $result) {
              $rank[] = array(
                  'rankId' => $result->id,
                  'rankName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getEntreorentation();
          if(count($results)){
            foreach ($results as $result) {
              $entreOrentation[] = array(
                  'entreOrentationId' => $result->id,
                  'entreOrentation' => $result->name
                );
            }
          }

          $results = $this->projectModel->getProvince();
          if(count($results)){
            foreach ($results as $result) {
              $province[] = array(
                  'provinceId' => $result->id,
                  'province' => $result->name
                );
            }
          }

          $results = $this->projectModel->getCity();
          if(count($results)){
            foreach ($results as $result) {
              $city[] = array(
                  'cityId' => $result->id,
                  'city' => $result->name 
                );
            }
          }

          $results = array(
              'rank' => $rank,
              'entreOrentation' => $entreOrentation,
              'province' => $province,
              'city' => $city
            );
          
          $sysList = array('response_status' => 'success',
                           'response_success_data' => $results);
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

    function getData2_post()
    {
      try
      {

          $this->load->model('projectModel');
          $tags = array();
          $processes = array();
          $positions = array();
          $financeSources = array();
          $cooperations = array();
          $salaries = array();
          $stocks = array();
          $province = array();
          $city = array();
          $rank = array();
          $entreOrentation = array();

          $results = $this->projectModel->getTag();
          if(count($results)){
            foreach ($results as $result) {
              $tags[] = array(
                  'tagId' => $result->id,
                  'tagName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getProjectProcess();
          if(count($results)){
            foreach ($results as $result) {
              $processes[] = array(
                  'processId' => $result->id,
                  'processName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getFinanceSource();
          if(count($results)){
            foreach ($results as $result) {
              $financeSources[] = array(
                  'financeSource' => $result->id,
                  'financeSourceName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getPosition();
          if(count($results)){
            foreach ($results as $result) {
              $positions[] = array(
                  'position' => $result->id,
                  'postionName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getCooperation();
          if(count($results)){
            foreach ($results as $result) {
              $cooperations[] = array(
                  'cooperation' => $result->id,
                  'cooperationName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getSalary();
          if(count($results)){
            foreach ($results as $result) {
              $salaries[] = array(
                  'salary' => $result->id,
                  'salaryName' => $result->value
                );
            }
          }

          $results = $this->projectModel->getStock();
          if(count($results)){
            foreach ($results as $result) {
              $stocks[] = array(
                  'stock' => $result->id,
                  'stockName' => $result->value
                );
            }
          }

          $results = $this->projectModel->getProvince();
          if(count($results)){
            foreach ($results as $result) {
              $province[] = array(
                  'province' => $result->name
                );
            }
          }

          $results = $this->projectModel->getCity();
          if(count($results)){
            foreach ($results as $result) {
              $city[] = array(
                  'cityId' => $result->id,
                  'city' => $result->name
                );
            }
          }
          $results = $this->projectModel->getRank();
          if(count($results)){
            foreach ($results as $result) {
              $rank[] = array(
                  'rankId' => $result->id,
                  'rankName' => $result->name
                );
            }
          }

          $results = $this->projectModel->getEntreorentation();
          if(count($results)){
            foreach ($results as $result) {
              $entreOrentation[] = array(
                  'entreOrentationId' => $result->id,
                  'entreOrentation' => $result->name
                );
            }
          }

          $results = array(
              'tags' => $tags,
              'processes' => $processes,
              'financeSources' => $financeSources,
              'positions' => $positions,
              'cooperations' => $cooperations,
              'salaries' => $salaries,
              'stocks' => $stocks,
              'province' => $province,
              'city' => $city,
              'rank' => $rank,
              'entreOrentation' => $entreOrentation
            );
          
          $sysList = array('response_status' => 'success',
                           'response_success_data' => $results);
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

          $this->load->model('projectModel');

          $projectId = uniqid('project');
          $teamId = uniqid('team');
          $added = $this->projectModel->addProject($userId, $projectId, $teamId);
          $message = $added?'添加成功':'添加失败';

          $result = array(
                'code' => 200,
                'projectId' => $projectId,
                'teamId' => $teamId,
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

    function delete_post()
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

          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $projectId = trim($jsonArray->projectId);

          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }


          $this->load->model('projectModel');

          $data = array(
              'deleted' => 1
            ); 

          $deleted  = $this->projectModel->updateProject($projectId, $data);
          
          $message = $deleted?'删除成功':'删除失败';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
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

    function rank_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 

          $errors = array();
          if (!array_key_exists('rankId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:rankId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $rankId = trim($jsonArray->rankId);

          if (!strlen($rankId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'rankId不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
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

          //排序之后的项目
          $projects = array();
          $this->load->model('projectModel');
          $results = $this->projectModel->rank(array(
              'rankId' => $rankId,
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $proId) {
              $res = $this->projectModel->getProject(array('id' => $proId));
              if(count($res)){
                  $projects[] = array(
                      'projectId' => $res['projectId'],
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['projectName'],
                      'entreOrentation' => $res['entreOrentation'],
                      'brief' => $res['brief'],
                      'process' => $res['projectProcess'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
              }
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('projects' => $projects));

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

    function keywords_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 

          $errors = array();
          if (!array_key_exists('keywords', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:keywords'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $keywords = trim($jsonArray->keywords);

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

          //排序之后的项目
          $projects = array();
          $this->load->model('projectModel');
          $results = $this->projectModel->keywords(array(
              'keywords' => $keywords,
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $proId) {
              $res = $this->projectModel->getProject(array('id' => $proId));
              if(count($res)){
                  $projects[] = array(
                      'projectId' => $res['projectId'],
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['projectName'],
                      'entreOrentation' => $res['entreOrentation'],
                      'brief' => $res['brief'],
                      'process' => $res['projectProcess'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
              }
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('projects' => $projects));

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

    function filter_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 

          $errors = array();
          if (!array_key_exists('entreOrentation', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:entreOrentation'
              ));
          }
          if (!array_key_exists('location', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:location'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $entreOrentation = trim($jsonArray->entreOrentation);
          $city = trim($jsonArray->location);

          if (!strlen($entreOrentation)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '创业方向不能为空'
              ));
          }
          if (!strlen($city)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '城市不能为空'
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

          $projects = array();
          $this->load->model('projectModel');
          $results = $this->projectModel->filter(array(
              'entreOrentation' => $entreOrentation,
              'city' => $city, //或者 'province' => $location
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $result) {
              $res = $this->projectModel->getProject(array('id' => $result->id));
              if(count($res)){
                  $projects[] = array(
                      'projectId' => $res['projectId'],
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['projectName'],
                      'entreOrentation' => $res['entreOrentation'],
                      'brief' => $res['brief'],
                      'process' => $res['projectProcess'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
              }
            }
          }

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('projects' => $projects));

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

    function detail_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 

          $errors = array();
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $projectId = trim($jsonArray->projectId);

          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          //排序之后的项目
          $project = array();
          $this->load->model('projectModel');
          $this->load->model('commentModel');
          
          $res = $this->projectModel->getProject(array('id' => $projectId));
          if (!count($res)) {
            array_push($errors, array(
              'code' => '205',
              'message' => '相关数据不存在'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(count($res)){
            $teamMembers = array();
            $teamSize = count($res['team']['members']);
            if(count($res['team'])&&$teamSize){
              foreach ($res['team']['members'] as $v) {
                $teamMembers[] = array(
                  'avatarUrl' => $v['avatarUrl'],
                  'name' => $v['name'],
                  'position' => $v['position'],
                  'phoneNumber' => $v['memberPhoneNumber'],
                  'school' => $v['school']
                  );
              }
            }

            //评论
            $comments = array();
            $commentNum = count($res['comments']);
            if($commentNum){
              foreach ($res['comments'] as $v) {
                $comments[] = array(
                  'commentId' => $v['commentId'],
                  'commenterImgUrl' => $v['commenterImgUrl'],
                  'commenterName' => $v['commenterName'],
                  'text' => $v['text'],
                  'commentTime' => $v['commentTime']
                  );
              }
            }

            $project = array(
              'projectId' => $res['projectId'],
              'fouderUserId' => $res['projectFouderUserId'],
              'logoUrl' => $res['logoUrl'],
              'name' => $res['projectName'],
              'entreOrentation' => $res['entreOrentation'],
              'brief' => $res['brief'],
              'tags' => $res['tags'],
              'teamMembers' => $teamMembers,
              'teamSize' => $teamSize,
              'financeAmount' => $res['financeAmount'],
              'entreOrentation' => $res['entreOrentation'],
              'location' => array(
                'county' => $res['county'],
                'city' => $res['city'],                
                'province' => $res['province']
                ),
              'introduce' => $res['introduction'],
              'prospect' => $res['prospect'],
              'process' => $res['projectProcess'],
              'praise' => count($res['praises']),
              'concern' => count($res['concerns']),
              'comments' => $comments,
              'commentNum' => $commentNum,
              'shareUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/zcspace/socailshare/project.html?id='.$projectId
            );
          }

          //评论
          $res = $this->commentModel->getComment(array(
                    'role_id' => $projectId,
                    'page' => 1,
                    'pageSize' => 3
                ));

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('project' => $project));

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

    function updateInfo_post()
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
          $name = array_key_exists('name', $jsonArray)?trim($jsonArray->name):false;
          $province = array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?trim($jsonArray->location->province):false;
          $entreOrentation = array_key_exists('entreOrentation', $jsonArray)?trim($jsonArray->entreOrentation):false;
          $brief = array_key_exists('brief', $jsonArray)?trim($jsonArray->brief):false;

          if(strlen($name)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '姓名不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($province)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '省份不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }          
          if(strlen($entreOrentation)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '创业方向不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($brief)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '简要描述不能为空不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $this->load->model('projectModel');          
          $projectId = trim($jsonArray->projectId);          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }
          //必须上传图片
          $res = $this->projectModel->getImg($projectId);
          $img = count($res)?$res->logoImgId:'';
          if(strlen($img)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '没有上传图片'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $data = array();
          array_key_exists('name', $jsonArray)?$data['projectName']=trim($jsonArray->name):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?$data['province']=trim($jsonArray->location->province):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->city)?$data['city']=trim($jsonArray->location->city):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->county)?$data['county']=trim($jsonArray->location->county):false;
          array_key_exists('entreOrentation', $jsonArray)?$data['entreOrentation']=trim($jsonArray->entreOrentation):false;
          array_key_exists('brief', $jsonArray)?$data['brief']=trim($jsonArray->brief):false;

          if(!count($data)){
              $result = array(
                'code' => 205,
                'updated' => false,
                'message' => '不需要更新');
              $sysList = array('response_status' => 'success',
               'response_success_data' => $result);
              $this->response($sysList, 200);
          }
          
          $updated = $this->projectModel->updateProject($projectId, $data);
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

    function updateProspect_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('introduction', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:introduction'
              ));
          }
          if (!array_key_exists('advantage', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:advantage'
              ));
          }
          if (!array_key_exists('prospect', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:prospect'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $introduction = trim($jsonArray->introduction);
          $advantage = trim($jsonArray->advantage);
          $prospect = $jsonArray->prospect;
          $estimateTime = isset($prospect->estimateTime)?$prospect->estimateTime:'';
          $estimatePrice = isset($prospect->estimatePrice)?$prospect->estimatePrice:'';
          $prospectDetail = isset($prospect->prospectDetail)?$prospect->prospectDetail:'';
          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!strlen($introduction)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '项目介绍不能为空'
              ));
          }
          if (!strlen($advantage)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '优势不能为空'
              ));
          }
          if (!strlen($estimateTime)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '上市时间不能为空'
              ));
          }
          if (!strlen($estimatePrice)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '预估价格不能为空'
              ));
          }
          if (!strlen($prospectDetail)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '具体描述不能为空'
              ));
          }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');       

          $data = array(
              'introduction' => $introduction,
              'advantage' => $advantage,
              'estimateTime' => $estimateTime,
              'estimatePrice' => $estimatePrice,
              'prospectDetail' => $prospectDetail
            );
          
          //用户完成添加操作
          $data['completed'] = 1; 

          $updated = $this->projectModel->updateProject($projectId, $data);
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

    function updateFinance_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('process', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:process'
              ));
          }
          if (!array_key_exists('finance', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:finance'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $process = trim($jsonArray->process);
          $finance = $jsonArray->finance;
          $financeSource = isset($finance->financeSource)?$finance->financeSource:'';
          $financeAmount = isset($finance->financeAmount)?$finance->financeAmount:'';
          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!strlen($process)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '项目id不能为空'
              ));
          }
          if (!strlen($process)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '项目阶段不能为空'
              ));
          }
          if (!strlen($financeSource)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '资金来源不能为空'
              ));
          }
          if (!strlen($financeAmount)) { 
            array_push($errors, array(
              'code' => '203',
              'message' => '融资金额不能为空'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');       

          $data = array(
              'projectProcess' => $process,
              'financeSource' => $financeSource,
              'financeAmount' => $financeAmount
            ); 

          $updated = $this->projectModel->updateProject($projectId, $data);
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

    function updatePlanFinanceAmount_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('planFinanceAmount', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:planFinanceAmount'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $planFinanceAmount = trim($jsonArray->planFinanceAmount);
                    
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!strlen($planFinanceAmount)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'planFinanceAmount不能为空'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');       

          $data = array(
              'planFinanceAmount' => $planFinanceAmount
            ); 

          $updated = $this->projectModel->updateProject($projectId, $data);
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

    function addTeamer_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array( 
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          
          if (!strlen($projectId)) { 
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');

          $result = $this->projectModel->getTeam(array('projectId' => $projectId));
          $teamId = count($result)?$result->id:'';

          $teamerId = uniqid('teamer');
          $data = array(
              'id' => $teamerId,
              'teamId' => $teamId,
              'projectId' => $projectId,
              'name' => '',
              'avatarImageId' => '',
              'position' => '',
              'school' => ''
            ); 

          $added = $this->projectModel->addTeamer($data);
          $message = $added?'添加成功':'添加失败';

          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'teamerId' => $added?$teamerId:'');
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

    function updateTeamer_post()
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
          
          $this->load->model('projectModel');          
          $teamerId = trim($jsonArray->teamerId);          
          if (!strlen($teamerId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'teamerId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $data = array();
          array_key_exists('name', $jsonArray)?$data['name']=trim($jsonArray->name):false;
          array_key_exists('position', $jsonArray)?$data['position']=trim($jsonArray->position):false;
          array_key_exists('school', $jsonArray)?$data['school']=trim($jsonArray->school):false;

          if(!count($data)){
              $result = array(
                'code' => 205,
                'updated' => false,
                'message' => '不需要更新');
              $sysList = array('response_status' => 'success',
               'response_success_data' => $result);
              $this->response($sysList, 200);
          }
          //用户完成添加操作
          // $data['completed'] = 1;

          $updated = $this->projectModel->updateTeamer($teamerId, $data);
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


    function deleteTeamer_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('teamerId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:teamerId'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $teamerId = trim($jsonArray->teamerId);
          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!strlen($teamerId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '团队成员id不能为空'
              ));
          }
           
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');

          $deleted  = $this->projectModel->deleteTeamer(array('id' => $teamerId));
          
          $message = $deleted?'删除成功':'删除失败';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
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



    function addRecruit_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('position', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:position'
              ));
          }
          if (!array_key_exists('cooperation', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:cooperation'
              ));
          }
          if (!array_key_exists('salary', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:salary'
              ));
          }
          if (!array_key_exists('stock', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:stock'
              ));
          }
          
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $position = trim($jsonArray->position);
          $cooperation = trim($jsonArray->cooperation);
          $salary = trim($jsonArray->salary);
          $stock = trim($jsonArray->stock);
          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!strlen($position)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'position不能为空'
              ));
          }
          if (!strlen($cooperation)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'cooperation不能为空'
              ));
          }
          if (!strlen($salary)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'salary不能为空'
              ));
          }
          if (!strlen($stock)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'stock不能为空'
              ));
          }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');

          $newpartnerId = uniqid('newpartner');

          $data = array(
              'id' => $newpartnerId,
              'projectId' => $projectId,
              'position' => $position,
              'cooperation' => $cooperation,
              'salary' => $salary,
              'stock' => $stock
            ); 

          $added = $this->projectModel->addNewPartner($data);
          $message = $added?'添加成功':'添加失败';

          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'recruitId' => $added?$newpartnerId:'');
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

    // function updateRecruit_post()
    // {
    //   try
    //   { 
    //       $jsonArray = json_decode(file_get_contents('php://input'));

    //       $errors = array();
    //       if (!array_key_exists('userId', $jsonArray)) {
    //         array_push($errors, array(
    //           'code' => '却少参数',
    //           'message' => '却少参数:userId'
    //           ));
    //         $this->response(array(
    //           "response_status"=>"error",
    //           "response_error_data" => $errors), 200);
    //       }
    //       $userId = trim($jsonArray->userId); 
    //       if (!array_key_exists('projectId', $jsonArray)) {
    //         array_push($errors, array( 
    //           'code' => '缺少参数',
    //           'message' => '缺少参数:projectId'
    //           ));
    //       }
          
    //       if(count($errors))
    //       {
    //         $this->response(array("response_status"=>"error",
    //           'response_error_data' => $errors), 
    //         200);
    //       }

    //       $recruitId = trim($jsonArray->recruitId);
          
    //       if (!strlen($recruitId)) {
    //         array_push($errors, array(
    //           'code' => '203',
    //           'message' => 'recruitId不能为空'
    //           ));
    //       }
          
    //       if(count($errors))
    //       {
    //         $this->response(array("response_status"=>"error",
    //           'response_error_data' => $errors), 
    //         200);
    //       }

    //       $this->load->model('projectModel');

    //       $data = array();
    //       array_key_exists('position', $jsonArray)?$data['position']=trim($jsonArray->position):false;
    //       array_key_exists('cooperation', $jsonArray)?$data['cooperation']=trim($jsonArray->cooperation ):false;
    //       array_key_exists('salary', $jsonArray)?$data['salary']=trim($jsonArray->salary):false;
    //       array_key_exists('stock', $jsonArray)?$data['stock']=trim($jsonArray->stock):false;

    //       $result = $this->projectModel->getTeam(array('projectId' => $recruitId));
    //       $teamId = count($result)?$result->id:'';

    //       $teamerId = uniqid('teamer');
    //       $data = array(
    //           'id' => $teamerId,
    //           'teamId' => $teamId,
    //           'projectId' => '',
    //           'name' => '',
    //           'avatarImageId' => '',
    //           'position' => '',
    //           'school' => ''
    //         ); 

    //       $added = $this->projectModel->addTeamer($projectId, $data);
    //       $message = $added?'添加成功':'添加失败';

    //       $result = array(
    //             'code' => 200,
    //             'added' => $added,
    //             'message' => $message,
    //             'teamerId' => $added?$teamerId:'');
    //       $sysList = array('response_status' => 'success',
    //                        'response_success_data' => $result);
    //       $this->response($sysList, 200);

    //   }
    //   catch(Exception $e)
    //   {
    //       $this->response(array("response_status"=>"error",
    //           'response_error_data' => array(
    //                                   array(
    //                                 'code' => 10000,
    //                                 'message' => $this->lang->line('010000'). '\n'. $e->getMessage()))), 
    //       500);
    //   }  
    // }

    function deleteRecruit_post()
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
          if (!array_key_exists('recruitId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:recruitId'
              ));
          }
          
          $recruitId = trim($jsonArray->recruitId);
          
          if (!strlen($recruitId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'recruitId'
              ));
          }
           
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');

          $deleted  = $this->projectModel->deleteRecuit(array('id' => $recruitId));
          
          $message = $deleted?'删除成功':'删除失败';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
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

    function updateRecruit_post()
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
          if (!array_key_exists('recruitId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:recruitId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          
          $recruitId = trim($jsonArray->recruitId);

          if (!strlen($recruitId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'recruitId不能为空'
              ));
          }           
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $data = array();
          array_key_exists('position', $jsonArray)?$data['position']=trim($jsonArray->position):false;
          array_key_exists('cooperation', $jsonArray)?$data['cooperation']=trim($jsonArray->cooperation):false;
          array_key_exists('salary', $jsonArray)?$data['salary']=trim($jsonArray->salary):false;
          array_key_exists('stock', $jsonArray)?$data['stock']=trim($jsonArray->stock):false;
          
          if(!count($data)){
              $result = array(
                'code' => 205,
                'updated' => false,
                'message' => '不需要更新');
              $sysList = array('response_status' => 'success',
               'response_success_data' => $result);
              $this->response($sysList, 200);
          }

          $this->load->model('projectModel');

          $updated  = $this->projectModel->updateRecuit($recruitId, $data);
          
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

    function updateTags_post()
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
          if (!array_key_exists('projectId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:projectId'
              ));
          }
          if (!array_key_exists('tags', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:tags'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $projectId = trim($jsonArray->projectId);
          $tags = $jsonArray->tags;
          
          if (!strlen($projectId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'projectId不能为空'
              ));
          }
          if (!count($tags)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'tags必须为数组'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('projectModel');

          $updated = $this->projectModel->deleteTag(array('projectId' => $projectId));

          $i = 0;          
          foreach ($tags as $tag) {
            $tagId = uniqid('tag'.$i);
            $data = array(
                'id' => $tagId,
                'projectId' => $projectId,
                'tag' => $tag
              ); 
            $updated = $updated&&$this->projectModel->addTag($data);
            $i++;
          }

          $message = $updated?'添加成功':'添加失败';

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

    // function deleteTag_post()
    // {
    //   try
    //   {
    //       $jsonArray = json_decode(file_get_contents('php://input'));

    //       $errors = array();
    //       if (!array_key_exists('userId', $jsonArray)) {
    //         array_push($errors, array(
    //           'code' => '缺少参数',
    //           'message' => '缺少参数:userId'
    //           ));
    //         $this->response(array(
    //           "response_status"=>"error",
    //           "response_error_data" => $errors), 200);
    //       }
    //       $userId = trim($jsonArray->userId); 
    //       if (!array_key_exists('tagId', $jsonArray)) {
    //         array_push($errors, array(
    //           'code' => '缺少参数',
    //           'message' => '缺少参数:tag'
    //           ));
    //       }
    //       if(count($errors))
    //       {
    //         $this->response(array("response_status"=>"error",
    //           'response_error_data' => $errors), 
    //         200);
    //       }

    //       $tagId = trim($jsonArray->tagId);
          
    //       if (!strlen($tagId)) {
    //         array_push($errors, array(
    //           'code' => '203',
    //           'message' => '亮点标签id不能为空'
    //           ));
    //       }
    //       if(count($errors))
    //       {
    //         $this->response(array("response_status"=>"error",
    //           'response_error_data' => $errors), 
    //         200);
    //       }

    //       $this->load->model('projectModel');

    //       $deleted  = $this->projectModel->deleteTag(array('id' => $tagId));
          
    //       $message = $deleted?'删除成功':'删除失败';

    //       $result = array(
    //             'code' => 200,
    //             'deleted' => $deleted,
    //             'message' => $message);
    //       $sysList = array('response_status' => 'success',
    //                        'response_success_data' => $result);
    //       $this->response($sysList, 200);

    //   }
    //   catch(Exception $e)
    //   {
    //       $this->response(array("response_status"=>"error",
    //           'response_error_data' => array(
    //                                   array(
    //                                 'code' => 10000,
    //                                 'message' => $this->lang->line('010000'). '\n'. $e->getMessage()))), 
    //       500);
    //   }  
    // }


}