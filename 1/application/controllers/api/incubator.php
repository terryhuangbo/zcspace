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

class Incubator extends REST_Controller
{
 
    function getInfo_post() 
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

            if ($page<1) {
              array_push($errors, array(
                'code' => '203',
                'message' => '页码必须大于0'
                ));
              $this->response(array(
                "response_status"=>"error",
                "response_error_data" => $errors), 200);
            }
            
            $incubators = array();
            $this->load->model('incubatorModel');

            //删除添加未完成的记录
            $this->incubatorModel->deleteIncubator(array(
                'userId' => $userId,
                'completed' => 0
            )); 

            $results = $this->incubatorModel->getIncubatorsByUserId(array('userId' => $userId,
                'page' => $page,
                'pageSize' => $pageSize
            )); 

            if(count($results)){
                foreach ($results as $result) {
                    $incubatorId = $result->id;
                    $res = $this->incubatorModel->getIncubator(array('id' => $incubatorId));
                    if(count($res)){
                        $incubators[] = array(
                                'id' => $incubatorId,
                                'name' => $res['incubatorName'],
                                'logoUrl' => $res['logoUrl'],
                                'location' => array(
                                        'province' => $res['province'],
                                        'city' => $res['city'],
                                        'county' => $res['county']
                                    ),
                                'address' => $res['addressDetail'],
                                'acreage' => $res['acreage'],
                                'introduce' => $res['introduction'], 
                                'price' => $res['price'],
                                'requirement' => $res['requirement'],
                                'propertyService' => $res['propertyService'],
                                'specialService' => $res['specialService'],
                                'starProjects' => $res['starProjects'],
                                'file' => $res['files']
                            );
                    }  
                }

            }


            $sysList = array('response_status' => 'success',
                'response_success_data' => array('incubators' => $incubators));
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

            $this->load->model('incubatorModel');

            $incubatorId = uniqid('incubator');
            $added = $this->incubatorModel->addIncubator(array(
                'id' => $incubatorId,
                'userId' => $userId
              ));
            $message = $added?'添加成功':'添加失败';
            $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'incubatorId' => $incubatorId);

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

          if (!array_key_exists('incubatorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incubatorId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $incubatorId = trim($jsonArray->incubatorId);

          if (!strlen($incubatorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'incubatorId不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }


          $this->load->model('incubatorModel');

          $data = array(
              'deleted' => 1
            ); 

          $deleted  = $this->incubatorModel->updateIncubator($incubatorId, $data);
          
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

    function updateInfo_post()
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
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }          

          $incubatorId = trim($jsonArray->incubatorId);

          $name = array_key_exists('name', $jsonArray)?trim($jsonArray->name):false;
          $province = array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?trim($jsonArray->location->province):false;
          $address = array_key_exists('address', $jsonArray)?trim($jsonArray->address):false;
          $acreage = array_key_exists('acreage', $jsonArray)?trim($jsonArray->acreage):false;
          $introduction = array_key_exists('introduction', $jsonArray)?trim($jsonArray->introduction):false;
          $price = array_key_exists('price', $jsonArray)?trim($jsonArray->price):false;

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
          if(strlen($address)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '地址不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($acreage)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '面积不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($introduction)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '介绍不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($price)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '价格不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $this->load->model('incubatorModel');   

          //必须上传图片
          $res = $this->incubatorModel->getImg($incubatorId);
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
          array_key_exists('name', $jsonArray)?$data['incubatorName']=trim($jsonArray->name):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->province)?$data['province']=trim($jsonArray->location->province):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->city)?$data['city']=trim($jsonArray->location->city):false;
          array_key_exists('location', $jsonArray)&&isset($jsonArray->location->county)?$data['city']=trim($jsonArray->location->county):false;
          array_key_exists('address', $jsonArray)?$data['addressDetail']=trim($jsonArray->address):false;
          array_key_exists('acreage', $jsonArray)?$data['acreage']=trim($jsonArray->acreage):false;
          array_key_exists('introduction', $jsonArray)?$data['introduction']=trim($jsonArray->introduction):false;
          array_key_exists('price', $jsonArray)?$data['price']=trim($jsonArray->price):false;
          if(!count($data)){
              $result = array(
                'code' => 205,
                'updated' => false,
                'message' => '不需要更新');
              $sysList = array('response_status' => 'success',
               'response_success_data' => $result);
              $this->response($sysList, 200);
          }
          
          $updated  = $this->incubatorModel->updateIncubator($incubatorId , $data);
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

    function updateServe_post()
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
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $requirement = array_key_exists('requirement', $jsonArray)?trim($jsonArray->requirement):false;
          $propertyService = array_key_exists('propertyService', $jsonArray)?trim($jsonArray->propertyService):false;
          $specialService = array_key_exists('specialService', $jsonArray)?trim($jsonArray->specialService):false;
          if(strlen($requirement)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '入住要求不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($propertyService)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '物业服务不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          if(strlen($specialService)==0){
            array_push($errors, array(
              'code' => '207',
              'message' => '特色服务不能为空'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $incubatorId = trim($jsonArray->incubatorId);

          $this->load->model('incubatorModel');          
          
          $data = array();
          array_key_exists('requirement', $jsonArray)?$data['requirement']=trim($jsonArray->requirement):false;
          array_key_exists('propertyService', $jsonArray)?$data['propertyService']=trim($jsonArray->propertyService):false;
          array_key_exists('specialService', $jsonArray)?$data['specialService']=trim($jsonArray->specialService):false;

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

          $updated  = $this->incubatorModel->updateIncubator($incubatorId , $data);
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

    function addStarProject_post()
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

          $incuProId = uniqid('incupro');  
          $added = $this->incubatorModel->addStarProject(array(
                'id' => $incuProId,
                'incubatorId' => $incubatorId,
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

    function deleteStarProject_post()
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
          if (!array_key_exists('incuProId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incuProId'
              ));            
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $incuProId = trim($jsonArray->incuProId);

          if (!strlen($incuProId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'salaryId不能为空'
              ));
          }
          if (!strlen($incuProId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => 'stockId不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('incubatorModel');       

          $deleted = $this->incubatorModel->deleteStarProject(array(
                'id' => $incuProId
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

          //排序之后的孵化器
          $incubators = array();
          $this->load->model('incubatorModel');
          $results = $this->incubatorModel->rank(array(
              'rankId' => $rankId,
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $incuId) {
                $res = $this->incubatorModel->getIncubator(array('id' => $incuId));
                if(count($res)){
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

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('incubators' => $incubators));

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

          //排序之后的孵化器
          $incubators = array();
          $this->load->model('incubatorModel');
          $results = $this->incubatorModel->keywords(array(
              'keywords' => $keywords,
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $incuId) {
                $res = $this->incubatorModel->getIncubator(array('id' => $incuId));
                if(count($res)){
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

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('incubators' => $incubators));

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

          //筛选之后的孵化器
          $incubators = array();
          $this->load->model('incubatorModel');
          $results = $this->incubatorModel->filter(array(
              'entreOrentation' => $entreOrentation,
              'city' => $location, //或者 'province' => $location 
              'page' => $page,
              'pageSize' => $pageSize
            ));
          if(count($results)){
            foreach ($results as $result) {
                $incuId = $result->id;
                $res = $this->incubatorModel->getIncubator(array('id' => $incuId));
                if(count($res)){
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

          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('incubators' => $incubators));

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
          if (!array_key_exists('incubatorId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:incubatorId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $incubatorId = trim($jsonArray->incubatorId);

          if (!strlen($incubatorId)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '创业方向不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          //筛选之后的孵化器
          $incubator = array();
          $this->load->model('incubatorModel');
        
          $res = $this->incubatorModel->getIncubator(array('id' => $incubatorId));
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
            $incubator = array(
                  'name' => $res['incubatorName'],
                  'logoUrl' => $res['logoUrl'],
                  'location' => array(
                          'province' => $res['province'],
                          'city' => $res['city'],
                          'county' => $res['county'],
                          'address' => $res['addressDetail'] 
                      ),
                  'address' => $res['addressDetail'],
                  'acreage' => $res['acreage'],
                  'brief' => $res['brief'],
                  'introduce' => $res['introduction'],
                  'contact' => $res['contact'],
                  'price' => $res['price'],
                  'requirement' => $res['requirement'],
                  'propertyService' => $res['propertyService'],
                  'specialService' => $res['specialService'],
                  'activities' => $res['activities'],
                  'starProjects' => $res['starProjects'],
                  'praise' => count($res['praises']),
                  'concern' => count($res['concerns']),
                  'comments' => $res['comments'],
                  'commentNum' => count($res['comments']),
                  'files' => $res['files'],
                  'shareUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/zcspace/socailshare/incubator.html?id='.$incubatorId
              );
          } 


          $sysList = array('response_status' => 'success',
                           'response_success_data' => array('incubator' => $incubator));

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