<?php
class MessageModel extends CI_Model {
    function __construct()
    {
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getComments($data)
    {
        $this->db->select('*');
        $this->db->where($data);
        $results = $this->db->get('comment')->result();

        $comments = array();
        if(count($results)){
            foreach ($results as $result) {
                if($result->role=='role55388437541c3'){//创业者
                    $this->db->select('*');
                    $this->db->where('id', $result->role_id);
                    $res = $this->db->get('project')->row();                    
                    $comments[] = array(
                            'role' => '创业者',
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:'',
                        );

                }else if($result->role=='role5538880cd6634'){ //孵化器
                    $this->db->select('*');
                    $this->db->where('id', $result->role_id);
                    $res = $this->db->get('incubator')->row();                    
                    $comments[] = array(
                            'role' => '孵化器',
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:'',
                        );
                    
                }else if($result->role=='role553888d05a887'){//投资者
                    $this->db->select('*');
                    $this->db->where('id', $result->role_id);
                    $res = $this->db->get('investor')->row();                    
                    $comments[] = array(
                            'role' => '投资者',
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:'',
                        );
                }
            }

        }

        return $comments;

    }
    
}