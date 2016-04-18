<?php
class AdviceModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function addAdvice($data)
    {
        $result = $this->db->insert('advice', $data);
        return $result;

    }

    function updateAdvice($data)
    {
        $this->db->where('userId', $data['userId']);
        return $this->db->update('advice', array('content' => $data['content'])); 
    }


}