<?php
class BannerModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getAll()
    {
        $this->db->select('banner.id, banner.title, banner.targetUrl, images.url as imageUrl');
        $this->db->from('banner');
        $this->db->join('images', 'banner.imgUrl = images.id', 'left');
        $query = $this->db->get();
//echo $this->db->last_query();
        return $query->result(); 
    }
}