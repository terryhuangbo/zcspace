<?php
class AuthModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
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


    function modifyPwd($userId, $pwdNew)
    {
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('id', $userId); 
        $row = $this->db->get()->row();
        if(!empty($row))
        {
            $data = array(
                   'userId' => $userId,
                   'password' => md5($pwdNew) 
                );
            // var_dump($row);
            $this->db->where('auth.userId', $userId);
            return $this->db->update('auth', $data); 
        }

        return false;
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