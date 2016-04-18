<?php
class Auth extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function add($data)
    {
        $this->db->insert('auth', $data); 
    }

    function updateLastUpdateTime($userId)
    { 
    	$data = array(
               'lastLoginDate' => date('YmdHis')
            );

		$this->db->where('userId', $userId);
		$this->db->update('auth', $data); 
    }

    function forgetPwd($phoneNumber, $pwd)
    {
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('mobile', $phoneNumber); 
        $row = $this->db->get()->row();

        if(isset($row))
        {
            $data = array(
                   'userId' => $row->id,
                   'password' => $pwd 
                );

            $this->db->where('userId', $row->id);
            $this->db->update('auth', $data); 
        }
    }

    function modifyPwd($phoneNumber, $pwd)
    {
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('mobile', $phoneNumber); 
        $row = $this->db->get()->row();

        if(isset($row))
        {
            $data = array(
                   'userId' => $row->id,
                   'password' => $pwd 
                );

            $this->db->where('userId', $row->id);
            $this->db->update('auth', $data); 
        }
    }

    function isPasswordCorrect($phoneNumber, $pwd)
    {
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('mobile', $phoneNumber); 
        $row = $this->db->get()->row();

        $isPasswordCorrect = FALSE;
        if(isset($row))
        {
            $query = $this->db->get_where('auth', 
                array('userId' => $row->id, 'password' => $pwd), 1, 0);

            $isPasswordCorrect = count($query->result_array());
        }

        return $isPasswordCorrect;
    }
}