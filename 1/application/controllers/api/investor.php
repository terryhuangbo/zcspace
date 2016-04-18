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
// echo '<pre>';print_r(APPPATH);exit();
// require APPPATH.'/controllers/api/upload.php';

class Investor extends REST_Controller 
{

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

            $this->load->model('investorModel');

            $investorId = uniqid('investor');
            $added = $this->investorModel->addInvestor(array(
                'id' => $investorId,
                'userId' => $userId
            ));
            $message = $added?'添加成功':'添加失败';
            $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'investId' => $investorId);

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

    function getData_post()
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
                  'entreOrentation' => $result->id,
                  'entreOrentationName' => $result->name
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
            
            $this->load->model('projectModel');
            $this->load->model('investorModel');

            //删除添加未完成的记录
            $this->investorModel->deleteInvestor(array(
              'userId' => $userId,
              'completed' => 0
            ));

            //获取我的投资方信息
            $investor = array();
            $investProjects = array();
            $investorPartners = array();

            $result = $this->investorModel->getInvestorsByUserId(array('userId' => $userId));           
            $investorId = $result->id;
            $res = $this->investorModel->getInvestor(array('id' => $investorId));
            if(count($res)){
                if(count($res['investPartners'])){ //投资合伙人
                    foreach ($res['investPartners'] as $vid) {
                        $re = $this->investorModel->getInvestor(array('id' => $vid['partnerId'])); 
                        $investorPartners[] = array(
                                    'investPartId' => $re['partnerId'],
                                    'avatarUrl' => $re['logoUrl'],
                                    'name' => $re['investorName']                                            
                                ); 
                    }
                }

                $investor = array(
                        'investorId' => $investorId,
                        'name' => $res['investorName'],
                        'logoUrl' => $res['logoUrl'],
                        'location' => array(
                                'province' => $res['province'],
                                'city' => $res['city'],
                                'county' => $res['county']
                            ),
                        'brief' => $res['brief'],
                        'introduction' => $res['introduction'],
                        'concernedIndustries' => $res['industryConcerned'],
                        'investProjects' => $res['investProjects'],
                        'investPartners' => $res['investPartners']
                    ); 
            }        

            $sysList = array('response_status' => 'success',
                'response_success_data' => $investor);
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
          $this->load->model('investorModel'); 
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
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $investorId = trim($jsonArray->investorId);
          $name = array_key_exists('name', $jsonArray)?trim($jsonArray->name):false;
          $province = array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?trim($jsonArray->location->province):false;
          $brief = array_key_exists('brief', $jsonArray)?trim($jsonArray->brief):false;
          // $introduction = array_key_exists('introduction', $jsonArray)?trim($jsonArray->introduction):'a';
          
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
          if(strlen($brief)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '简要描述不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }         
          //必须上传图片
          $res = $this->investorModel->getImg($investorId);
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
          // array_key_exists('logoUrl', $jsonArray)?$data['logoUrl']=trim($jsonArray->logoUrl):false;
          array_key_exists('name', $jsonArray)?$data['investorName']=trim($jsonArray->name):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?$data['province']=trim($jsonArray->location->province):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->city)?$data['city']=trim($jsonArray->location->city):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->county)?$data['city']=trim($jsonArray->location->county):false;
          array_key_exists('brief', $jsonArray)?$data['brief']=trim($jsonArray->brief):false;
          array_key_exists('introduction', $jsonArray)?$data['introduction']=trim($jsonArray->introduction):false;

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
          $data['completed'] = 1;

          $updated  = $this->investorModel->updateInvestor($investorId , $data);
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

          //排序之后的投资人/方
          $investors = array();
          $this->load->model('investorModel');
          $this->load->model('projectModel');
          $results = $this->investorModel->rank(array(
              'rankId' => $rankId,
              'page' => $page,
              'pageSize' => $pageSize
            ));

          if(count($results)){
            foreach ($results as $inveId) {
                $res = $this->investorModel->getInvestor(array('id' => $inveId));
                if(count($res)){
                    $investors[] = array(
                      'investorId' => $inveId,
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['investorName'],
                      'ownerName' => $res['investOwnerName'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
                }  
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('investors' => $investors));

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

          //排序之后的投资人/方
          $investors = array();
          $this->load->model('investorModel');
          $this->load->model('projectModel');
          $results = $this->investorModel->keywords(array(
              'keywords' => $keywords,
              'page' => $page,
              'pageSize' => $pageSize
            ));

          if(count($results)){
            foreach ($results as $inveId) {
                $res = $this->investorModel->getInvestor(array('id' => $inveId));
                if(count($res)){
                    $investors[] = array(
                      'investorId' => $inveId,
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['investorName'],
                      'ownerName' => $res['investOwnerName'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
                }  
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('investors' => $investors));

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
          $userId = $this->session->userdata('userId');

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
          $location = trim($jsonArray->location);

          if (!strlen($entreOrentation)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '创业方向不能为空'
              ));
          }
          if (!strlen($location)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '方位不能为空'
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

          //筛选之后的投资人/方
          $investors = array();
          $this->load->model('investorModel');
          $this->load->model('projectModel');
          $results = $this->investorModel->filter(array(
              'entreOrentation' => $entreOrentation,
              'city' => $location, //或者 'province' => $location 
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $result) {
                $res = $this->investorModel->getInvestor(array('id' => $result->id));
                if(count($res)){                
                    
                    $investors[] = array(
                      'investorId' => $res['investorId'],
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['investorName'],
                      'ownerName' => $res['investOwnerName'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns'])
                  );
                }  
            }
          } 

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('investors' => $investors));

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
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $investorId = trim($jsonArray->investorId);

          if (!strlen($investorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'rankId不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          //排序之后的投资人/方
          $investor = array();
          $this->load->model('investorModel');
          $this->load->model('projectModel');          
            
          $res = $this->investorModel->getInvestor(array('id' => $investorId));
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
              // if(count($res['investProjects'])){ //已投资项目
              //     foreach ($res['investProjects'] as $projectId) {
              //         $re = $this->projectModel->getProject(array('id' => $projectId));
              //         if(count($re)){
              //             $investProjects[] = array(
              //                     'projectId' => $re['projectId'],
              //                     'logoUrl' => $re['logoUrl'],
              //                     'name' => $re['projectName'],
              //                     'entreOrentation' => $re['entreorentationName'],
              //                     'process' => $re['projectProcessName'],
              //                     'brief' => $re['brief']
              //                 ); 
              //         }                                
              //     }
              // }
              $investorPartners = array();
              if(count($res['investPartners'])){ //投资合伙人
                  foreach ($res['investPartners'] as $v) {
                      $investorPartners[] = array(
                                  'investId' => $v['investPartId'],
                                  'avatarUrl' => $v['avatarUrl'],
                                  'name' => $v['name']                                            
                              ); 
                  }
              }

              $investor = array(
                      'name' => $res['investorName'],
                      'logoUrl' => $res['logoUrl'],
                      'location' => array(
                              'province' => $res['province'],
                              'city' => $res['city'],
                              'county' => $res['county']
                          ),
                      'brief' => $res['brief'],
                      'introduction' => $res['introduction'],
                      'concernedIndustries' => $res['industryConcerned'],
                      'projects' => $res['investProjects'],
                      'invests' => $investorPartners,
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns']),
                      'comments' => $res['comments'],
                      'commentNum' => count($res['comments']),
                      'shareUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/zcspace/socailshare/investor.html?id='.$investorId
                  ); 
          }  
            
          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('investor' => $investor));
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

    function addInvestPartner_post()
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
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));            
          }
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));            
           }
          if (!array_key_exists('name', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:name'
              ));            
           }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $investorId = trim($jsonArray->investorId);
          $avatarUrl = trim($jsonArray->avatarUrl);
          $name = trim($jsonArray->name);

          if (!strlen($investorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'investorId不能为空'
              ));
          }
          if (!strlen($name)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'name不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }


          $this->load->model('investorModel');   

          $result = Upload::investPartner($investorId);

          $investPartnerId = uniqid('invespart');  
          $added = $this->investorModel->addInvestPartner(array(
                'id' => $investPartnerId,
                'investorId' => $investorId,
                'name' => $name,
                'avatarImgId' => $avatarUrl
            ));        
          
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

    function deleteInvestPartner_post()
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
          if (!array_key_exists('investPartId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investPartId'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $investPartId = trim($jsonArray->investPartId);

          if (!strlen($investPartId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'investorId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('investorModel');       

          $deleted = $this->investorModel->deleteInvestPartner(array('id' => $investPartId));        
          
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

    function addProject_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $userId = $this->session->userdata('userId');
          
          $errors = array();
          if(!$userId)
          {
            array_push($errors, array(
              'code' => 201,
              'message' => '还未登陆，请先登陆'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          } 
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));            
          }
          if (!array_key_exists('logoUrl', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:logoUrl'
              ));            
           }
          if (!array_key_exists('name', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:name'
              ));            
           }

          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $investorId = trim($jsonArray->investorId);
          $logoUrl = trim($jsonArray->logoUrl);
          $name = trim($jsonArray->name);
          $entreId = trim($jsonArray->entreId);
          $process = trim($jsonArray->process);
          $brief = trim($jsonArray->brief);

          if (!strlen($investorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'investorId不能为空'
              ));
          }
          if (!strlen($entreId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'entreId不能为空'
              ));
          }
          if (!strlen($name)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'name不能为空'
              ));
          }
          if (!strlen($entreId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'entreId不能为空'
              ));
          }
          if (!strlen($process)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'process不能为空'
              ));
          }
          if (!strlen($brief)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'brief不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('investorModel');       

          $projectId = uniqid('project');  
          $added = $this->investorModel->addProject(array(
                'id' => $projectId,
                'logoImgId' => $logoUrl,
                'name' => $name,
                'entreOrentation' => $entreId,
                'projectProcess' => $process,
                'brief' => $brief
            ));   

          $investProjectId = uniqid('invsproject');  
          $added = $added&&$this->investorModel->addInvestProject(array(
                'id' => $investProjectId,
                'investorId' => $investorId,
                'projectId' => $projectId
            ));    
          
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

    function deleteInvestProject_post()
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
          if (!array_key_exists('invProId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:invProId'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $invProId = trim($jsonArray->invProId);

          if (!strlen($invProId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'invProId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('investorModel');       

          $deleted = $this->investorModel->deleteInvestProject(array(
                'id' => $invProId
            ));        
          
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

    function addConcern_post()
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
          if (!array_key_exists('concern', $jsonArray)) {
            array_push($errors, array(
              'code' => '却少参数',
              'message' => '却少参数:concern'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $userId = trim($jsonArray->userId);
          if (!array_key_exists('investorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:investorId'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $investorId = trim($jsonArray->investorId);
          $concern = $jsonArray->concern;

          $this->load->model('investorModel');  

          $this->investorModel->deleteInvestConcert(array(
                'investorId' => $investorId
            )); 
          $added = true;
          if(count($concern))
          {
            foreach($concern as $c) {
              $concernId = uniqid('investConcert');
              $added = $added && $this->investorModel->addInvestConcert(array(
                    'id' => $concernId,
                    'investorId' => $investorId,
                    'entreOrentation' => $c
                ));     
            }
          }

          $message = $added? '添加成功':'添加失败';
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

    function deleteConcern_post()
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
          if (!array_key_exists('concernId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:concernId'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $userId = trim($jsonArray->userId);
          $concernId = trim($jsonArray->concernId);

          $this->load->model('investorModel');       

          $deleted = $this->investorModel->deleteInvestConcert(array(
                'id' => $concernId
            ));        
          
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