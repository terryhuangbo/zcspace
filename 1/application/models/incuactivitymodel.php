<?php
class IncuactivityModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function add($data)
    {
       return $this->db->insert('incubatorActivity', $data);
    }

    function delete($data)
    {
       $this->db->where($data);
       return $this->db->delete('incubatorActivity');
    }

    function update($incuActId, $data)
    {
        $result = true;
        $this->db->select('*');
        $this->db->where('id', $incuActId);
        $incubator = $this->db->get('incubatorActivity')->row();        
        if(!count($incubator)){
            return false;
        }

        /*更新incubatorActivity表相关*/
        $value = array();        
        //孵化器活动名称
        array_key_exists('name', $data)?$value['name']=$data['name']:false; 
        //孵化器活动时间
        array_key_exists('time', $data)?$value['time']=$data['time']:false; 
        //孵化器活动详情
        array_key_exists('detail', $data)?$value['detail']=$data['detail']:false;
        if(count($value)){
            $this->db->where('id', $incuActId);
            $result = $result&&$this->db->update('incubatorActivity', $value);
        }

        return $result;
    }

    function addInvestor($data)
    {
       return $this->db->insert('incuactInvestor', $data);
    }

    function deleteInvestor($data)
    {
       $this->db->where($data);
       return $this->db->delete('incuActInvestor');
    
    }

    // function getIncubatorActivity($incubatorId)
    // {
    //     $this->db->select(array(
    //             'incubatorActivity.*',
    //             'incubatorActivity.id AS incuActId',
    //             'incuActInvestor.*',
    //             'incuActInvestor.id AS incuActInvestorId'


    //         ));
    //     $this->db->where('incubatorId', $incubatorId);
    //     $this->db->join('incuActInvestor', 'incuActInvestor.incubatorActivityId=incubatorActivity.id', 'left');
    //     $results = $this->db->get('incubatorActivity')->result();
        
    //     return $results;
    // }




}