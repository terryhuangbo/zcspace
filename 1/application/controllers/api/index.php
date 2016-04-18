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

class Index extends REST_Controller
{

    function get_post()
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

          $this->load->model('IndexModel');
          $results = $this->IndexModel->getIndex();
          $this->load->model('projectModel');
          $this->load->model('incubatorModel');
          $this->load->model('investorModel');

          //Banner
          $this->load->model('BannerModel');
          $banners = $this->BannerModel->getAll();
          $formattedBanners = array();
          if(count($banners))
          {
            foreach ($banners as $res) {
              array_push($formattedBanners, array(
                      'bannerId' => $res->id,
                      'title' => $res->title,
                      'targetUrl' => $res->targetUrl,
                      'imageUrl' => isset($res->imageUrl)&&($res->imageUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$res->imageUrl:''
                  ));
            }
          }

          //推荐项目
          $rcdProjects = array();
          if(count($results['recProjects'])){
            foreach ($results['recProjects'] as $proId) {
              $res = $this->projectModel->getProject(array('id' => $proId));
              if(count($res)){
                  $rcdProjects[] = array(
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

          //推荐孵化器
          $rcdIncubators = array();
          if(count($results['recIncubators'])){
            foreach ($results['recIncubators'] as $incuId) {
              $res = $this->incubatorModel->getIncubator(array('id' => $incuId));
              if(count($res)){
                  $rcdIncubators[] = array(
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

          //推荐投资人
          $rcdInvestors = array();
          if(count($results['recInvestors'])){
            foreach ($results['recInvestors'] as $invesId) {
              $res = $this->investorModel->getInvestor(array('id' => $invesId));
              if(count($res)){
                  $investPartners = array();
                  if(count($res['investPartners'])){ //投资合伙人
                    foreach ($investPartners as $vid) {
                      $re = $this->investorModel->getInvestor(array('id' => $vid)); 
                      $investorPartners[] = array(
                          'name' => $re['projectName']                                            
                        ); 
                    }
                  }
                  $rcdInvestors[] = array(
                      'investorId' => $invesId,
                      'logoUrl' => $res['logoUrl'],
                      'name' => $res['investorName'],
                      'brief' => $res['brief'],
                      'praise' => count($res['praises']),
                      'concern' => count($res['concerns']),
                      'investPartners' => $investPartners
                  );
              }
            }
          }

          $results = array(
              'banners' => $formattedBanners,
              'rcdProjects' => $rcdProjects,
              'rcdInvestors' => $rcdInvestors,
              'rcdIncubators' => $rcdIncubators
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


}