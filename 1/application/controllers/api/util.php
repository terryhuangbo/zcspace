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

class Util extends CI_Controller
{

    function guid()
    {
        $type = $this->input->post('type');
        $guid = uniqid($type);
        echo $guid;
    }
    
    function php()
    {
        $jsonArray = json_decode(urldecode(file_get_contents('php://input')));
        $type=$jsonArray->type;
        $guid = phpinfo();
        $this->response($guid, 200);
    }

    function image()
    {
        $imageId=$this->input->get('id');
        $this->load->model('ImageModel');
        $image = $this->ImageModel->getData($imageId);

        $imageData = $image->url ? $image->url : '';
        $mime = substr($imageData, strlen('data:'), strpos($imageData, ';base64')-5);
        //echo $mime;
        Header("Content-type: ". $mime);
        //echo "   |";
        $base64Data = substr($imageData, strpos($imageData, 'base64,') + strlen('base64,'));
        //echo $base64Data;
        echo base64_decode($base64Data);

    } 
}