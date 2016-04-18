<?php //defined('BASEPATH') OR exit('No direct script access allowed');

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
 
class Upload extends CI_Controller
{    
   function __construct() 
   {
    parent::__construct();
    $this->load->helper(array('form', 'url'));
   }

  function index()
  { 
    $this->load->view('upload_form', array('error' => ' ' ));
  }

  function response($data)
  {
    echo str_replace("\\/", "/",  json_encode($data));
    die();
  }

  function do_upload($upfile, $path, $salt='')
  {

  /*  if(!isset($_FILES[$upfile]['tmp_name'])){
      log_message('debug', 1);
      return (object)$result = array(
                  'code' => 400,
                  'uploaded' => FALSE,
                  'message' => '缺少参数或者客户端图片未能上传');    
    } 
    if(!is_uploaded_file($_FILES[$upfile]['tmp_name'])){
      log_message('debug', 2);
      return (object)$result = array(
                  'code' => 401,
                  'uploaded' => FALSE,
                  'message' => '客户端图片未能上传');    
    }   */
    $file = $_FILES[$upfile]; 
    //$error = $file["error"];
    /*if($error==1){
      $result = array(
                  'code' => 402,
                  'uploaded' => FALSE,
                  'message' => '超过了文件大小');  
      return (object)$result;
    }else if($error==2){
      $result = array(
                  'code' => 403,
                  'uploaded' => FALSE,
                  'message' => '超过了文件的大小MAX_FILE_SIZE选项指定的值'); 
      return (object)$result;
    }else if($error==3){
      $result = array(
                  'code' => 404,
                  'uploaded' => FALSE,
                  'message' => '文件只有部分被上传'); 
      return (object)$result;
    }else if($error==4){
      $result = array(
                  'code' => 401,
                  'uploaded' => FALSE,
                  'message' => '客户端图片未能上传');
      return (object)$result; 
    }else if($error==5){
      $result = array(
                  'code' => 406,
                  'uploaded' => FALSE,
                  'message' => '上传文件大小为0 '); 
      return (object)$result;
    }
*/

    //允许上传的 文件类型
    $type=array("jpg","gif","bmp","jpeg","png", "doc", "docx", "pdf", "txt");
    $filetype = substr(strrchr($file['name'], '.'), 1); //扩展名
    $filename = $file['name'];//原文件名

    $filePath = $_SERVER['DOCUMENT_ROOT'].$path;
    // $fileName = $salt.$file['name'];
    $fileName = $salt.'.'.$filetype; //新文件名

    if(!in_array(strtolower($filetype), $type))
    {   
        $text=implode(",",$type);   
        $result = array(
                  'code' => 407,
                  'uploaded' => FALSE,
                  'message' => "您只能上传以下类型文件: ,$text,"); 
        return (object)$result;
     }  
    if (!file_exists($filePath))
    {
      mkdir($filePath);
    }

    if(move_uploaded_file($file['tmp_name'], $filePath.$fileName)){
        $result = array(
          'code' => 200,
          'uploaded' => TRUE,
          'message' => '上传成功',
          'fileName' => $fileName, //file现在的名称
          'name' => $filename,//file原来的名称
          'filePath' => $filePath,
          'fileDir' => $path.$fileName,
          'url' => 'http://'.$_SERVER['SERVER_NAME'].$path.$fileName
          ); 

        return (object)$result;
    }else{
        $result = array(
                  'code' => 207,
                  'uploaded' => FALSE,
                  'message' => '上传失败'); 
        return (object)$result;

    }

    
 }

  function avatar()
  {
    $userId = $this->input->post('userId');//isset($_POST['userId'])?$_POST['userId']:'';
    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/user/';
    $avatarImageId = uniqid('images');
    $upload = $this->do_upload('avatarUrl', $path, $avatarImageId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'account' => $userId,
              'avatarImageId' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$avatarImageId,
          'url'=>$upload->fileDir
    )); 
    //更新用户头像
    $this->db->where('id', $userId);
    $res = $res&&$this->db->update('user', array('avatarImgId' => $avatarImageId));
    if(!$res){
      array_push($errors, array(
              'code' => 408,
              'message' => '插入数据库失败',
              'account' => $userId,
              'avatarImageId' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => $upload->code,
        'message' => $upload->message,
        'account' => $userId,
        'avatarImageId' => $avatarImageId,
        'imageName' => $upload->fileName,
        'url' => $upload->url
      );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }


  function projectLogo()
  {
    $projectId = $this->input->post('projectId');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    if($projectId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'projectId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/project/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'projectId' => $projectId,
              'logoImgId' => $logoImgId,
              'imageName' => '',//$upload->fileName,
              'url' => ''//$upload->url
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    )); 
    //更新项目logo
    $this->db->where('id', $projectId);
    $res = $res&&$this->db->update('project', array('logoImgId' => $logoImgId));
    if(!$res){
      array_push($errors, array(
              'code' => 408,
              'message' => '插入数据库失败',
              'projectId' => $projectId,
              'logoImgId' => $logoImgId,
              'imageName' => $upload->fileName,
              'url' => $upload->url
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => $upload->code,
        'message' => $upload->message,
        'projectId' => $projectId,
        'logoImgId' => $logoImgId,
        'imageName' => $upload->fileName,
        'url' => $upload->url
      );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function incubatorLogo()
  {
    $incubatorId = $this->input->post('incubatorId');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    if($incubatorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'incubatorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/incubator/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'incubatorId' => $incubatorId,
              'logoImgId' => '',
              'imageName' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    )); 
    //更新孵化器logo
    $this->db->where('id', $incubatorId);
    $res = $res&&$this->db->update('incubator', array('logoImgId' => $logoImgId));
    if(!$res){
      array_push($errors, array(
              'code' => 408,
              'message' => '插入数据库失败',
              'incubatorId' => $incubatorId,
              'logoImgId' => '',
              'imageName' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => $upload->code,
        'message' => $upload->message,
        'incubatorId' => $incubatorId,
        'logoImgId' => $logoImgId,
        'imageName' => $upload->fileName,
        'url' => $upload->url
      );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function investorLogo()
  {
    $investorId = $this->input->post('investorId');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    if($investorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'investorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/investor/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'investorId' => $incubatorId,
              'logoImgId' => $logoImgId,
              'imageName' => $upload->fileName,
              'url' => $upload->url
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    )); 
    //更新孵化器logo
    $this->db->where('id', $investorId);
    $res = $res&&$this->db->update('investor', array('logoImgId' => $logoImgId));
    if(!$res){
      array_push($errors, array(
              'code' => 408,
              'message' => '插入数据库失败',
              'investorId' => $investorId,
              'logoImgId' => $logoImgId,
              'imageName' => $upload->fileName,
              'url' => $upload->url
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => $upload->code,
        'message' => $upload->message,
        'investorId' => $investorId,
        'logoImgId' => $logoImgId,
        'imageName' => $upload->fileName,
        'url' => $upload->url
      );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function incubatorFile()
  {
    $incubatorId = $this->input->post('incubatorId');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    if($incubatorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'incubatorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/files/';
    $fileId = uniqid('files');
    $upload = $this->do_upload('files', $path, $fileId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'incubatorId' => $incubatorId,
              'fileId' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入文档记录
    $res = $this->db->insert('files', array(
          'id'=>$fileId,
          'url'=>$upload->fileDir,
          'name'=>$upload->name,
          'incubatorId' =>$incubatorId
    )); 
    
    $result = array(
        'code' => $upload->code,
        'message' => $upload->message,
        'incubatorId' => $incubatorId,
        'fileId' => $fileId,
        'fileName' => $upload->name,
        'url' => $upload->url
      );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function deleteIncubatorFile()
  {
    $fileId = $this->input->post('fileId');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($fileId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'fileId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $this->db->select('*');
    $this->db->where('id', $fileId);
    $res = $this->db->get('files')->row();
    if(!$res){
      array_push($errors, array(
              'code' => 300,
              'message' => '数据库中无此数据'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    $path = count($res)?$_SERVER['DOCUMENT_ROOT'].$res->url:'';

    $this->db->where('id', $fileId);
    $res = $this->db->delete('files');
    if(!$res){
      array_push($errors, array(
              'code' => 301,
              'message' => '删除数据库失败'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    if(!file_exists($path)){
      array_push($errors, array(
              'code' => 302,
              'message' => '文件不存在或者路径错误'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if(!unlink($path)){
      array_push($errors, array(
              'code' => 303,
              'message' => '文件删除失败，权限不够'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
              'code' => 200,
              'message' => '文件删除成功'
          );
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

    
  }

  function starProject()
  {
    $incubatorId = $this->input->post('incubatorId');
    $incuProId = $this->input->post('incuProId');
    $userId = $this->input->post('userId');
    $name = $this->input->post('name');
    $entreOrentation = $this->input->post('entreOrentation');
    $brief = $this->input->post('brief');
    $process = $this->input->post('process');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($incubatorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'incubatorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/incubator/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'incuProId' => '',
              'logoImgId' => '',
              'url' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    //查看有无记录
    $this->db->select('');
    $this->db->where('id', $incuProId);
    $resl = $this->db->get('incubatorProject')->row();

    //有记录，更新操作
    if(count($resl)){
      $this->db->where('id', $resl->logoImgId);    
      $this->db->delete('images');

      //插入图片记录
      $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
        ));

      //更新明星项目记录
      $this->db->where('id', $incuProId);
      $res = $res&&$this->db->update('incubatorProject', array(
            'incubatorId' => $incubatorId,
            'logoImgId' => $logoImgId,
            'name' => $name,
            'entreOrentation' => $entreOrentation,
            'brief' => $brief,
            'process' => $process
        ));

      if(!$res){
        array_push($errors, array(
          'code' => 401,
          'message' => '更新数据库失败',
          'incuProId' => '',
          'logoImgId' => '',
          ));
        $this->response(array('response_status' => 'error',
         'response_success_data' => $errors));
      }

      $result = array(
          'code' => 200,
          'message' => '上传图片成功，更新明星项目成功',
          'incuProId' => $incuProId,
          'logoImgId' => $logoImgId,
          'url' => $upload->url
        );
      
    }else{//无记录，插入新数据

      //插入图片记录
      $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
        ));

      //添加明星项目记录
      $incuProId = uniqid('incupro');  
      $res = $res&&$this->db->insert('incubatorProject', array(
          'id' => $incuProId,
          'incubatorId' => $incubatorId,
          'logoImgId' => $logoImgId,
          'name' => $name,
          'entreOrentation' => $entreOrentation,
          'brief' => $brief,
          'process' => $process
        )); 
      if(!$res){
        array_push($errors, array(
            'code' => 401,
            'message' => '插入数据库失败',
            'incuProId' => '',
            'logoImgId' => '',
            'url' => ''
          ));
        $this->response(array('response_status' => 'error',
         'response_success_data' => $errors));
      }

      $result = array(
        'code' => 200,
        'message' => '上传图片成功，添加明星项目成功',
        'incuProId' => $incuProId,
        'logoImgId' => $logoImgId,
        'url' => $upload->url
        );

    }
    

    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function teamer()
  {
    $projectId = $this->input->post('projectId');
    $teamerId = $this->input->post('teamerId');
    $userId = $this->input->post('userId');
    $name = $this->input->post('name');
    $position = $this->input->post('position');
    $school = $this->input->post('school');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($projectId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'projectId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/project/teamer';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('avatarUrl', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'teamerId' => '',
              'logoImgId' => '',
              'url' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    //获取teamId
    $this->db->select('');
    $this->db->where('projectId', $projectId);
    $res = $this->db->get('team')->row();
    $teamId = isset($res->id)?$res->id:'';

    //查看有无记录
    $this->db->select('');
    $this->db->where('id', $teamerId);
    $resl = $this->db->get('teamMember')->row();

    //有记录，更新操作
    if(count($resl)){
      $this->db->where('id', $resl->avatarImageId);    
      $this->db->delete('images');

      //插入图片记录
      $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
        ));

      //更新团队成信息
      $this->db->where('id', $teamerId);
      $res = $res&&$this->db->update('teamMember', array(
            'projectId' => $projectId,
            'avatarImageId' => $logoImgId,
            'name' => $name,
            'position' => $position,
            'school' => $school
        ));

      if(!$res){
        array_push($errors, array(
          'code' => 401,
          'message' => '更新数据库失败',
          'teamerId' => '',
          'logoImgId' => '',
          'url' => '',
          ));
        $this->response(array('response_status' => 'error',
         'response_success_data' => $errors));
      }

      $result = array(
          'code' => 200,
          'message' => '上传图片成功，更新明星项目成功',
          'teamerId' => $teamerId,
          'logoImgId' => $logoImgId,
          'url' => $upload->url
        );
      
    }else{//无记录，插入新数据

      //插入图片记录
      $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
        ));

      //添加团队成员记录
      $teamerId = uniqid('teamer');  
      $res = $res&&$this->db->insert('teamMember', array(
          'id' => $teamerId,
          'teamId' => $teamId,
          'projectId' => $projectId,
          'avatarImageId' => $logoImgId,
          'name' => $name,
          'school' => $school,
          'position' => $position
        )); 
      if(!$res){
        array_push($errors, array(
            'code' => 401,
            'message' => '插入数据库失败',
            'teamerId' => '',
            'logoImgId' => '',
            'url' => ''
          ));
        $this->response(array('response_status' => 'error',
         'response_success_data' => $errors));
      }

      $result = array(
          'code' => 200,
          'message' => '上传图片成功，添加明星项目成功',
          'teamerId' => $teamerId,
          'logoImgId' => $logoImgId,
          'url' => $upload->url
        );

    }
    
    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function incuActInvestor()
  {
    $incubatorActivityId = $this->input->post('incubatorActivityId');
    $name = $this->input->post('name');
    $userId = $this->input->post('userId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($incubatorActivityId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'incubatorActivityId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    } 

    $path = '/91chuangye/incubator/activity/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('avatarUrl', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'incuProId' => '',
              'logoImgId' => '',
              'url' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    ));  

    //添加活动投资人记录
    $actInvId = uniqid('incuinvs');  
    $res = $res&&$this->db->insert('incuActInvestor', array(
          'id' => $actInvId,
          // 'incubatorId' => $incubatorId,
          'incubatorActivityId' => $incubatorActivityId,
          'avatarImgId' => $logoImgId,
          'name' => $name
    )); 
    if(!$res){
      array_push($errors, array(
              'code' => 401,
              'message' => '插入数据库失败',
              'actInvId' => '',
              'logoImgId' => '',
              'url' => ''
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => 200,
        'message' => '上传图片成功，添加明星项目成功',
        'actInvId' => $actInvId,
        'logoImgId' => $logoImgId,
        'url' => $upload->url
      );

    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function investPartner()
  {
    $investorId = $this->input->post('investorId');
    $userId = $this->input->post('userId');
    $name = $this->input->post('name');
    $investPartId = $this->input->post('investPartId');

    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($investorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'investorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    
    $path = '/91chuangye/investor/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'investPartId' => '',
              'logoImgId' => '',
              'url' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    ));    

    $this->db->select('*');
    $this->db->where('id', $investPartId);
    $resul = $this->db->get('investPartner')->result();
    if(count($resul)){//有记录，做更新操作
      $this->db->where('id', $investPartId);
      $this->db->update('investPartner', array(
          'name' => $name,
          'avatarImgId' => $logoImgId
        ));
      $result = array(
          'code' => $upload->code,
          'message' => $upload->message,
          'investPartId' => $investPartId,
          'logoImgId' => $logoImgId,
          'url' => $upload->url
        );

    }else{//没有记录添加投资合伙人记录      
      $investPartId = uniqid('investPartner');  
      $res = $res&&$this->db->insert('investPartner', array(
          'id' => $investPartId,
          'investorId' => $investorId,
          'avatarImgId' => $logoImgId,
          'name' => $name
        )); 
      if(!$res){
        array_push($errors, array(
            'code' => 401,
            'message' => '插入数据库失败',
            'incuProId' => '',
            'logoImgId' => '',
          ));
        $this->response(array('response_status' => 'error',
         'response_success_data' => $errors));
      }

      $result = array(
          'code' => $upload->code,
          'message' => $upload->message,
          'investPartId' => $investPartId,
          'logoImgId' => $logoImgId,
          'url' => $upload->url
        );
    }    

    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }

  function investProject()
  {
    $investorId = $this->input->post('investorId');
    $investProId = $this->input->post('invProId');
    $userId = $this->input->post('userId');
    $name = $this->input->post('name');
    $entreOrentation = $this->input->post('entreOrentation');
    $brief = $this->input->post('brief');
    $process = $this->input->post('process');
    log_message('debug', $name . 'name is');
    $errors = array();
    if($userId==''){
      array_push($errors, array(
              'code' => '208',
              'message' => 'userId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    if($investorId==''){
      array_push($errors, array(
              'code' => '207',
              'message' => 'investorId不能为空'
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $path = '/91chuangye/investor/';
    $logoImgId = uniqid('images');
    $upload = $this->do_upload('logo', $path, $logoImgId);
    if(!$upload->uploaded){
      array_push($errors, array(
              'code' => $upload->code,
              'message' => $upload->message,
              'invProId' => '',
              'logoImgId' => '',
              'url' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }
    //插入图片记录
    $res = $this->db->insert('images', array(
          'id'=>$logoImgId,
          'url'=>$upload->fileDir
    ));  

    //添加投资项目记录
    if($investProId == '')
    {
      $invProId = uniqid('incupro');  

      $res = $res&&$this->db->insert('investProject', array(
            'id' => $invProId,
            'investorId' => $investorId,
            'logoImgId' => $logoImgId,
            'name' => $name,
            'entreOrentation' => $entreOrentation,
            'brief' => $brief,
            'process' => $process
      )); 
    }
    else
    {
      $invProId = $investProId;
      $this->db->where('id', $investProId);
      $res = $res&&$this->db->update('investProject', array(
            'investorId' => $investorId,
            'logoImgId' => $logoImgId,
            'name' => $name,
            'entreOrentation' => $entreOrentation,
            'brief' => $brief,
            'process' => $process
      )); 
    }
    if(!$res){
      array_push($errors, array(
              'code' => 401,
              'message' => '插入数据库失败',
              'invProId' => '',
              'logoImgId' => '',
          ));
      $this->response(array('response_status' => 'error',
                             'response_success_data' => $errors));
    }

    $result = array(
        'code' => 200,
        'message' => '上传图片成功，添加明星项目成功',
        'invProId' => $invProId,
        'logoImgId' => $logoImgId,
        'url' => $upload->url
      );

    $this->response(array('response_status' => 'success',
                             'response_success_data' => $result));

  }



}
