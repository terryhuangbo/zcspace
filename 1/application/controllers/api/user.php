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
class User extends REST_Controller
{ 
    function verifyCode_post()  
    { 
        try
        {
            $jsonArray = json_decode(urldecode(file_get_contents('php://input'))); 

            $errors = array();
            if (!array_key_exists('phoneNumber', $jsonArray)) {
              array_push($errors, array(
                  'code' => '缺少参数',
                  'msg' => '缺少参数:phoneNumber'
                ));
            }

            if (!array_key_exists('actionType', $jsonArray)) {
              array_push($errors, array(
                  'code' => '缺少参数',
                  'msg' => '缺少参数:actionType'
                ));
            }

            if(count($errors) > 0)
            {
                $this->response(array('error_response' => 
                array('errors' => $errors)), 200);
            }

            $phoneNumber = $jsonArray->phoneNumber;
            $actionType = $jsonArray->actionType;

            srand((double)microtime()*1000000);//create a random number feed.
            $ychar="0,1,2,3,4,5,6,7,8,9";
            $list=explode(",",$ychar);
            $code = "";
            for($i=0;$i<6;$i++){
                $randnum=rand(0,9); 
                $code.=$list[$randnum];
            }
//var_dump($this->config->config);
            $configs = $this->config->config;
            $submail=new MESSAGEXsend($configs);
            $submail->AddTo($phoneNumber);
            $submail->SetProject('9unWm');
            $submail->AddVar('code',$code); 
              
            $xsend=$submail->xsend();

            $result = array("response_status"=>"success",
                          "response_success_data" =>  array('status' => $xsend['status'],
                                                       'msg' => '验证码已发送')
                        );
          $this->response($result, 200);
        }
        catch(Exception $e)
        {
            $this->response(array(
                'response_status' => 'error',
                'response_error_data' => array(
                    'code' => 201,
                    'message' => $e->getMessage())), 200);
        }
    }

    function phoneVerify_post()
    {
      try
        {
          $jsonArray = json_decode(urldecode(file_get_contents('php://input'))); 
          $errors = array();
          if (!array_key_exists('phoneNumber', $jsonArray)) {
            array_push($errors, array(
                'code' => '缺少参数',
                'msg' => '缺少参数:phoneNumber'
              ));
          }

          if (!array_key_exists('verifyCode', $jsonArray)) {
            array_push($errors, array(
                'code' => '缺少参数',
                'msg' => '缺少参数:verifyCode'
              ));
          }

          if(count($errors) > 0)
          {
              $this->response(array('error_response' => 
              array('errors' => $errors)), 200);
          }

            $result = array("response_status"=>"success",
                          "response_success_data" =>  array('code' => 200,
                                                       'msg' => '手机验证成功')
                        );
          $this->response($result, 200);
        }
        catch(Exception $e)
        {
            $this->response(array(
                'response_status' => 'error',
                'response_error_data' => array(
                    'code' => 201,
                    'message' => $e->getMessage())), 200);
        }
    }

    function send($mobile, $code, $smsAccount, $smsPassword)
    {
      $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

      $post_data = "account=".$smsAccount."&password=".$smsPassword . "&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$code."。［91创业］");
      //密码可以使用明文密码或使用32位MD5加密
      $gets =  $this->xml_to_array($this->Post($post_data, $target));

      return $gets['SubmitResult'];
    }

    function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

    function xml_to_array($xml){
      $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
      if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
        $subxml= $matches[2][$i];
        $key = $matches[1][$i];
          if(preg_match( $reg, $subxml )){
            $arr[$key] = xml_to_array( $subxml );
          }else{
            $arr[$key] = $subxml;
          }
        }
      }
      return $arr;
    }

    function register_post()
    {
        try
        {

          $jsonArray = json_decode(urldecode(file_get_contents('php://input'))); 

          $errors = array();
          if (!array_key_exists('phoneNumber', $jsonArray)) {
              array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:phoneNumber'
                ));
          }

          if (!array_key_exists('verifyCode', $jsonArray)) {
              array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:verifyCode'
                ));
          }

          if (!array_key_exists('pwd', $jsonArray)) {
              array_push($errors, array(
                  'code' => '缺少参数',
                  'message' => '缺少参数:pwd'
                ));
          }

          if(count($errors) > 0)
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 200);
          }

          $phoneNumber = trim($jsonArray->phoneNumber);
          $verifyCode = trim($jsonArray->verifyCode);
          $pwd = trim($jsonArray->pwd);

          if($phoneNumber == '')
          {
              array_push($errors, array(
                  'code' => 20104,
                  'message' => $this->lang->line('020104')
                ));
          }
          else if(strlen($phoneNumber) != 11)
          {
              array_push($errors, array(
                  'code' => 20108,
                  'message' => $this->lang->line('020108')//放在文件web/application/language/chinese/api_message_lang.php，暂时先不考虑
                ));
          }

          /*
          $code = $this->session->userdata('verifyCode');

          if(trim($code) != trim($verifyCode))
          {
            array_push($errors, array(
                  'code' => '验证码错误',
                  'message' => '验证码错误'//放在文件web/application/language/chinese/api_message_lang.php，暂时先不考虑
                ));
          }*/

          if(count($errors) > 0)
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 200);
          }


          $this->load->model('UserModel');

          if($this->UserModel->isPhoneRegistered($phoneNumber))
          {
            array_push($errors, array(
                  'code' => 20112,
                  'message' => $this->lang->line('020112')
                ));
          } 

          if(count($errors) > 0)
          {
            $this->response(array(
              "response_status"=>"error",
              'response_error_data' => $errors), 200);
          }
          $userId = uniqid('user');
          $this->UserModel->add(array(
                        'id' => $userId,
                        'mobile' => $phoneNumber));
          $this->load->model('AuthModel');
          $this->AuthModel->add(array(
                        'id' => uniqid('auth'),
                        'userId' => $userId,
                        'password' => md5(strtoupper($pwd))
                      ));

          $this->load->library('session');
          $this->session->set_userdata('userId', $userId);

          $this->session->unset_userdata('verifyCode');//调用session，删除session

          $result = array('message' => '注册成功',
                          'account' => $userId,
                          'sessionKey' => $this->session->userdata('session_id'));

          $result = array("response_status"=>"success",
                          "response_success_data" =>  array(
                                                  "message" => "注册成功",
                                                  "userId" => $userId,
                                                  "sessionKey" => $this->session->userdata('session_id'))
                        );
          $this->response($result, 200);
        }
        catch(Exception $e)
        {
            $this->response(array(
                'response_status' => 'error',
                'response_error_data' => array(
                    'code' => 201,
                    'message' => $e->getMessage())), 200);
        }
    }

    function login_post()
    {
        try
        {
            $jsonArray = json_decode(file_get_contents('php://input')); 
                      $errors = array();
              if (!array_key_exists('phoneNumber', $jsonArray)) {
                  array_push($errors, array(
                      'code' => '缺少参数',
                      'message' => '缺少参数:phoneNumber'
                    ));
              }

              if (!array_key_exists('pwd', $jsonArray)) {
                  array_push($errors, array(
                      'code' => '缺少参数',
                      'message' => '缺少参数:pwd'
                    ));
              }

            if(count($errors) > 0)
            {
                $this->response(array(
                  "response_status"=>"error",
                  'response_error_data' => $errors), 200);
            }

            $phoneNumber = trim($jsonArray->phoneNumber);
            $pwd = md5(strtoupper(trim($jsonArray->pwd)));

            $this->load->model('UserModel');

            $user = $this->UserModel->login($phoneNumber, $pwd);

            $userId = count($user)?$user->userId:'';

            //学校
            $schools = array();
            $results = $this->UserModel->getSchools(array('userId' => $userId));
            if(count($results)){
              foreach ($results as $result) {
                array_push($schools, array(
                    'name' => $result->name,
                    'duration' => $result->duration));
              }
            }

            //项目经验
            $projectExperience = array();
            $results = $this->UserModel->getprojectExperience(array('userId' => $userId));
            if(count($results)){
              foreach ($results as $result) {
                array_push($projectExperience, array(
                    'name' => $result->name,
                    'detail' => $result->detail));
              }
            }

            //工作经验 公司
            $workExperience = array();
            $results = $this->UserModel->getWorkExperience(array('userId' => $userId));
            if(count($results)){
              foreach ($results as $result) {
                array_push($workExperience, array(
                    'name' => $result->company,
                    'duration' => $result->duration));
              }
            } 

            //角色
            $roles = array();
            $results = $this->UserModel->getUserRoles(array('userId' => $userId));
            if(count($results)){
              foreach ($results as $result) {
                array_push($roles, $result->roleId);
              }
            }            

            $result = NULL;
            if($user)
            {
                $result = array('response_status' => 'success',
                                'response_success_data' => array(
                                    'userId' => $userId,
                                    'avatarUrl' => $user->avatarImgId,
                                    'nick' => $user->nick,
                                    'gender' => $user->gender,
                                    'birth' => $user->birthday,
                                    'gender' => $user->gender,
                                    'location' => array(
                                            'province' => !is_null($user->province)?$user->province:'',
                                            'city' => !is_null($user->city)?$user->city:'',
                                            'county' => !is_null($user->county)?$user->county:''),
                                    'phoneNum' => $user->mobile,
                                    'email' => $user->email,
                                    'schools' => $schools,
                                    'projectExperience' => $projectExperience,
                                    'workExperience' => $workExperience,
                                    'role' => $roles,
                                    'weixinBound' =>  isset($user->weixinOpenId) || !empty($user->weixinOpenId),
                                    'weiboBound' =>  isset($user->weiboAccessToken)|| !empty($user->weiboAccessToken),
                                    'qqBound' =>  isset($user->qqOpenId)|| !empty($user->qqOpenId)

                                ));

                $this->load->model('AuthModel');
                $this->AuthModel->updateLastUpdateTime($user->id);
                $this->session->set_userdata('userId', $userId);

            }
            else
            {

                $result = array('response_status' => 'error',
                                'response_error_data' => array(
                                    array('code' => 201,
                                          'message' => '密码错误')));
            }
            $this->response($result, 200);
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

    function loginOff_post()
    {
        try{

            $this->session->unset_userdata('userId');//调用session，删除session
            
            $result = array('response_status' => 'success',
                            'response_success_data' => array(
                                        'loggedOff' => true,
                                        'message' => '注销了'));

            $this->response($result, 200);

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

    function modifyPwd_post()
    {
        try
        {
            $jsonArray = json_decode(file_get_contents('php://input')); 
            $errors = array();
            $phoneNumber = '';
            $userId = '';
            if (array_key_exists('userId', $jsonArray)) {
            /*array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:userId'
              ));
              $this->response(array(
                "response_status"=>"error",
                "response_error_data" => $errors), 200);
                */
              $userId = trim($jsonArray->userId);
            }
            if (array_key_exists('phoneNumber', $jsonArray)) {
              $phoneNumber = trim($jsonArray->phoneNumber);
            }
            if (!array_key_exists('newPwd', $jsonArray)) {
              array_push($errors, array(
                'code' => '缺少参数',
                'message' => '缺少参数:newPwd'
                ));
            }
            // if (!array_key_exists('pwdOld', $jsonArray)) {
            //   array_push($errors, array(
            //     'code' => '缺少参数',
            //     'message' => '缺少参数:pwdOld'
            //     ));
            // }
            if($userId == '' && $phoneNumber == '')
            {
              array_push($errors, array(
                'code' => '缺少参数',
                'message' => '缺少参数userId和phoneNumber'
                ));
            }
            if(count($errors) > 0)
            {
              $this->response(array(
                "response_status"=>"error",
                'response_error_data' => $errors), 200);
            }

            $newPwd = trim($jsonArray->newPwd);
            // $pwdOld = trim($jsonArray->pwdOld);
            // $phoneNumber = trim($jsonArray->phoneNumber);

            if(count($errors) > 0)
            {
              $this->response(array(
                "response_status"=>"error",
                'response_error_data' => $errors), 200);
            }

            $this->load->model('AuthModel');
            $this->load->model('UserModel');
            if ($userId == '' && $phoneNumber != '') {
              $user = $this->UserModel->getUserByPhoneNum($phoneNumber);
              if(count($user) > 0)
              {
                $userId = $user->id;
              }
            }

            $isModified = $this->AuthModel->modifyPwd($userId, $newPwd);
            $message = $isModified?'密码更改成功！':'密码更改失败！';

            $result = array(
                'message' => $message,
                'isModified' => $isModified
              );

            $sysList = array('response_status' => 'success',
                'response_success_data' => array('incubators' => $result));
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

    function getIndex_post() //获取首页数据，推荐项目，推荐投资人，推荐孵化器是不是按照评分来的？
    {
        try
        {

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

            $result = array('response_status' => 'success',
              'response_success_data' => array(
                array('isModified' => $isModified,
                      'message' => $message)));

            $this->response($result, 200);
          

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
          $roles = array();
          $this->load->model('userModel');
          $results = $this->userModel->getRoles();
          if(count($results)){
            foreach ($results as $result) {
              array_push($roles, array(
                'id' => $result->id,
                'name' => $result->name
              ));
            }
          }

          $result = array(
            'response_status' => 'success',
            'response_success_data' => $roles);
          $this->response($result, 200);
        

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

          $this->load->model('userModel');
          $result = $this->userModel->getUser($userId); 
          $user = $result;
          $nick = '';
          $avatarUrl = '';
          $gender = '';
          $birth = '';
          $province = '';
          $city = '';
          $county = '';
          $phoneNumber = '';
          $email = '';

          if(count($result)){
            $nick = $result->nick;
            $avatarUrl = !is_null($result->imgUrl)?'http://'.$_SERVER['SERVER_NAME'].$result->imgUrl:'';
            $gender = $result->gender?'男':'女';
            $birth = $result->birthday;
            $province = !is_null($result->province)?$result->province:'';
            $city = !is_null($result->city)?$result->city:'';
            $county = !is_null($result->county)?$result->county:'';            
            $phoneNumber = $result->mobile;
            $email = $result->email;
          }

          //毕业院校
          $schools = array();
          $results = $this->userModel->getSchools(array('userId' => $userId));

          if(count($results)){
            foreach ($results as $result) {
              array_push($schools, array(
                'schoolId' => $result->id,
                'name' => $result->name,
                'duration' => $result->duration));

            }
          }

          //项目经验
          $projectExperience = array();
          $results = $this->userModel->getProjectExperience(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($projectExperience, array(
                'experienceId' => $result->id,
                'name' => $result->name,
                'detail' => $result->detail));
            }
          }

          //工作经验
          $workExperience = array();
          $results = $this->userModel->getWorkExperience(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($workExperience, array(
                'workId' => $result->id,
                'name' => $result->company,
                'duration' => $result->duration));
            }
          }

          $roles = array();
          $results = $this->userModel->getUserRole(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($roles, array(
                'roleId' => $result->roleId,
                'roleName' => isset($result->roleName)?$result->roleName:''
                ));
            }
          }

          $results = array(
              'userId' => $userId,
              'avatarUrl' => $avatarUrl,
              'nick' => $nick,
              'gender' => $gender,
              'birth' => $birth,
              'location' => array(
                  'province' => $province,
                  'city' => $city,
                  'county' => $county
                ),
              'phoneNum' => $phoneNumber,
              'email' => $email,
              'schools' => $schools,
              'projectExperience' => $projectExperience,
              'workExperience' => $workExperience,
              'roles' => $roles,
              'weixinBound' =>  isset($user->weixinOpenId),
              'weiboBound' =>  isset($user->weiboAccessToken),
              'qqBound' =>  isset($user->qqOpenId)
            );

          $result = array(
            'response_status' => 'success',
            'response_success_data' => $results);
          $this->response($result, 200);
        
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
          /* $userId = $this->session->userdata('userId');

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
          } */
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
          
          $this->load->model('userModel');

          $data = array();
          array_key_exists('avatarUrl', $jsonArray)?$data['avatarUrl']=trim($jsonArray->avatarUrl):false;
          array_key_exists('nick', $jsonArray)?$data['nick']=trim($jsonArray->nick):false;
          array_key_exists('gender', $jsonArray)?$data['gender']=trim($jsonArray->gender):false;
          array_key_exists('birth', $jsonArray)?$data['birthday']=trim($jsonArray->birth):false;
          array_key_exists('province', $jsonArray)?$data['province']=trim($jsonArray->province):false;
          array_key_exists('city', $jsonArray)?$data['city']=trim($jsonArray->city):false;
          array_key_exists('county', $jsonArray)?$data['county']=trim($jsonArray->county):false;
          // array_key_exists('phoneNum', $jsonArray)?$data['mobile']=trim($jsonArray->phoneNum):false;
          array_key_exists('email', $jsonArray)?$data['email']=trim($jsonArray->email):false;

          $updated = $this->userModel->updateInfo($userId, $data);



          $message = $updated?'更新成功！':'更新失败！';

          $result = array(
                'code' => 200,
                'updated' => $updated,
                'message' => $message,
                'account' => $userId);
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

    function addSchoolExperience_post()
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

          if (!array_key_exists('schoolName', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:schoolName'
              ));
          }
          if (!array_key_exists('duration', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:duration'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }


          $schoolName = trim($jsonArray->schoolName);
          $duration = trim($jsonArray->duration);

          if (!strlen($schoolName)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '学校名不能为空'
              ));
          }
          if (!strlen($duration)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '入学/毕业日期不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          } 

          $this->load->model('userModel');

          $schoolId = uniqid('school');
          $added = $this->userModel->addSchoolExperience(array(
              'id' => $schoolId,
              'userId' => $userId,
              'name' => $schoolName,
              'duration' => $duration
            ));

          $message = $added?'添加成功！':'添加失败！';

          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'schoolId' => $schoolId);

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

    function deleteSchoolExperience_post()
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

          if (!array_key_exists('schoolId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:schoolId'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $schoolId = trim($jsonArray->schoolId);

          $this->load->model('userModel');

          $deleted = $this->userModel->deleteSchoolExperience(array(
                'id' => $schoolId));

          $message = $deleted?'删除成功！':'删除失败！';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
                'message' => $message,
                'schoolId' => $schoolId);

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

    function addProjectExperience_post()
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

          if (!array_key_exists('name', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:name'
              ));
          }
          if (!array_key_exists('detail', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:detail'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $name = trim($jsonArray->name);
          $detail = trim($jsonArray->detail);

          if (!strlen($name)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '项目名不能为空'
              ));
          }
          if (!strlen($detail)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '项目描述不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('userModel');

          $proExpId = uniqid('proExp');
          $added = $this->userModel->addProjectExperience(array(
                'id' => $proExpId,
                'userId' => $userId,
                'name' => $name,
                'detail' => $detail));

          $message = $added?'添加成功！':'添加失败！';

          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'experienceId' => $proExpId);

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

    function deleteProjectExperience_post()
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

          $this->load->model('userModel');

          $deleted = $this->userModel->deleteProjectExperience(array(
                'id' => $projectId,
                'userId' => $userId));

          $message = $deleted?'删除成功！':'删除失败！';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
                'message' => $message,
                'projectId' => $projectId);

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

    function addWorkExperience_post()
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

          if (!array_key_exists('companyName', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:companyName'
              ));
          }
          if (!array_key_exists('duration', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:duration'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $companyName = trim($jsonArray->companyName);
          $duration = trim($jsonArray->duration);

          if (!strlen($companyName)) {
            array_push($errors, array(
              'code' => '203',
              'message' => '公司名称不能为空'
              ));
          }
          if (!strlen($duration)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '在职时间不能为空'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $this->load->model('userModel');

          $workExpId = uniqid('workExp');
          $added = $this->userModel->addWorkExperience(array(
                'id' => $workExpId,
                'userId' => $userId,
                'company' => $companyName,
                'duration' => $duration));

          $message = $added?'添加成功！':'添加失败！';

          $result = array(
                'code' => 200,
                'added' => $added,
                'message' => $message,
                'workId' => $workExpId);

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

    function deleteWorkExperience_post()
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

          if (!array_key_exists('companyId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:companyId'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $companyId = trim($jsonArray->companyId);

          $this->load->model('userModel');

          $deleted = $this->userModel->deleteWorkExperience(array(
                'id' => $companyId));

          $message = $deleted?'删除成功！':'删除失败！';

          $result = array(
                'code' => 200,
                'deleted' => $deleted,
                'message' => $message,
                'companyId' => $companyId);

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

    function updateRoles_post()
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

          if (!array_key_exists('roles', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:roles'
              ));
          }
          if(count($errors))
          {
            $this->response(array("response_status"=>"error",
              'response_error_data' => $errors), 
            200);
          }

          $roles = $jsonArray->roles;
          if(!is_array($roles)&&count($roles)){
            array_push($errors, array(
              'code' => '203',
              'message' => 'roles必须为数组'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $this->load->model('userModel');

          $updated = $this->userModel->updateUserRole(array(
                'roles' => $roles,
                'userId' => $userId));

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

    function weixinLogin_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $errors = array();
          if (!array_key_exists('openId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:openId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $openId = trim($jsonArray->openId);

          $this->load->model('userModel');

          $user = $this->userModel->getInfoByWeixinOpenId($openId);
          $userId = uniqid('user');
          if($user)
          {
            $userId = $user->id;
          }
          else
          {
            $this->userModel->add(array('id' => $userId, 'weixinOpenId'=>$openId));
          }
          $result = array('userId'=>$userId);

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

    function weiboLogin_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $errors = array();
          if (!array_key_exists('accessToken', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:accessToken'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $accessToken = trim($jsonArray->accessToken);
          $this->load->model('userModel');
          $user = $this->userModel->getInfoByWeiboAccessToken($accessToken);
          $userId = uniqid('user');
          if($user)
          {
            $userId = $user->id;
          }
          else{
            $this->userModel->add(array('id' => $userId, 'weiboAccessToken'=>$accessToken));
          }

          $result = array('userId'=>$userId);

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

    function qqLogin_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $errors = array();
          if (!array_key_exists('openId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:openId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $openId = trim($jsonArray->openId);
          $this->load->model('userModel');

          $user = $this->userModel->getInfoByQQOpenId($openId);
          $userId = uniqid('user');
          if($user)
          {
            $userId = $user->id;
          }
          else
          {
            $this->userModel->add(array('id' => $userId, 'qqOpenId'=>$openId));
          }
          $result = array('userId'=>$userId);

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

    function autoLogin_post()
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

          $this->load->model('userModel');
          $result = $this->userModel->getUser($userId); 
          $nick = '';
          $avatarUrl = '';
          $gender = '';
          $birth = '';
          $province = '';
          $city = '';
          $county = '';
          $phoneNumber = '';
          $email = '';
          $weixinBound =  false;
          $weiboBound =  false;
          $qqBound =  false;

          if(count($result)){
            $nick = $result->nick;
            $avatarUrl = !is_null($result->imgUrl)?'http://'.$_SERVER['SERVER_NAME'].$result->imgUrl:'';
            $gender = $result->gender?'男':'女';
            $birth = $result->birthday;
            $province = !is_null($result->province)?$result->province:'';
            $city = !is_null($result->city)?$result->city:'';
            $county = !is_null($result->county)?$result->county:'';
            $phoneNumber = $result->mobile;
            $email = $result->email;
            $weixinBound =  isset($result->weixinOpenId) || !empty($result->weixinOpenId);
            $weiboBound =  isset($result->weiboAccessToken) || !empty($result->weiboAccessToken);
            $qqBound = isset($result->qqOpenId) || !empty($result->qqOpenId);
          }
 
          //毕业院校
          $schools = array();
          $results = $this->userModel->getSchools(array('userId' => $userId));

          if(count($results)){
            foreach ($results as $result) {
              array_push($schools, array(
                'name' => $result->name,
                'duration' => $result->duration));

            }
          }

          //项目经验
          $projectExperience = array();
          $results = $this->userModel->getProjectExperience(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($projectExperience, array(
                'name' => $result->name,
                'detail' => $result->detail));
            }
          }

          //工作经验
          $workExperience = array();
          $results = $this->userModel->getWorkExperience(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($workExperience, array(
                'name' => $result->company,
                'duration' => $result->duration));
            }
          }

          $roles = array();
          $results = $this->userModel->getUserRole(array('userId' => $userId));
          if(count($results)){
            foreach ($results as $result) {
              array_push($roles, array(
                'roleId' => $result->roleId,
                'roleName' => isset($result->roleName)?$result->roleName:''
                ));
            }
          }

          $results = array(
              'userId' => $userId,
              'avatarUrl' => $avatarUrl,
              'nick' => $nick,
              'gender' => $gender,
              'birth' => $birth,
              'location' => array(
                  'province' => $province,
                  'city' => $city,
                  'county' => $county
                ),
              'phoneNum' => $phoneNumber,
              'email' => $email,
              'schools' => $schools,
              'projectExperience' => $projectExperience,
              'workExperience' => $workExperience,
              'roles' => $roles,
              'weixinBound' => $weixinBound,
              'weiboBound' => $weiboBound,
              'qqBound' =>  $qqBound,
            );

          $result = array(
            'response_status' => 'success',
            'response_success_data' => $results);
          $this->response($result, 200);

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

    function weixinBind_post()
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

          if (!array_key_exists('openId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:openId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }
          $userId = trim($jsonArray->userId);
          $openId = trim($jsonArray->openId);

          $this->load->model('userModel');
          $this->userModel->weixinBind($userId, $openId);

          $result = array('bound'=>true);

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

    function weiboBind_post()
    {
      try
      {
          $jsonArray = json_decode(file_get_contents('php://input')); 
          $errors = array();
          if (!array_key_exists('accessToken', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:accessToken'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

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
          $accessToken = trim($jsonArray->accessToken);

          $this->load->model('userModel');
          $this->userModel->weiboBind($userId, $accessToken);

          $result = array('bound'=>true);

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

    function qqBind_post()
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

          if (!array_key_exists('openId', $jsonArray)) {
            array_push($errors, array(
              'code' => '缺少参数',
              'message' => '缺少参数:openId'
              ));
            $this->response(array(
              "response_status"=>"error",
              "response_error_data" => $errors), 200);
          }

          $userId = trim($jsonArray->userId);
          $openId = trim($jsonArray->openId);

          $this->load->model('userModel');
          $this->userModel->qqBind($userId, $openId);

          $result = array('bound'=>true);

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
