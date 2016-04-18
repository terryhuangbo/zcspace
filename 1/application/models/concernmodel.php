<?php
class ConcernModel extends CI_Model {
    function __construct()
    {
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getComments($data, $pageData)
    {
        $page = $pageData['page'];
        $pageSize = $pageData['pageSize'];
        $this->db->select('*');
        $this->db->where($data);
        $this->db->group_by('role_id');
        $this->db->limit($pageSize, ($page-1)*$pageSize);
        $results = $this->db->get('comment')->result();

        $concerns = array();
        $concerns['projects'] = array();
        $concerns['incubators'] = array();
        $concerns['investors'] = array();
        if(count($results)){
            foreach ($results as $result) {
                if($result->role=='role55388437541c3'){//创业者
                    $this->db->select('*');
                    $where = array(
                            'id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $res = $this->db->get('project')->row();                    
                    $concerns['projects'][] = array(
                            'role' => 'role55388437541c3',
                            'roleName' => '创业者',
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:'',
                        );

                }else if($result->role=='role5538880cd6634'){ //孵化器
                    $this->db->select('*');
                    $where = array(
                            'id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $res = $this->db->get('incubator')->row();                    
                    $concerns['incubators'][] = array(
                            'role' => 'role5538880cd6634',
                            'roleName' => '孵化器',
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:''
                        );
                    
                }else if($result->role=='role553888d05a887'){//投资者
                    $this->db->select('*');
                    $where = array(
                            'id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $res = $this->db->get('investor')->row();                    
                    $concerns['investors'][] = array(
                            'role' => 'role553888d05a887',
                            'roleName' => '投资者', 
                            'role_id' => count($res)?$res->id:'',
                            'role_name' => count($res)?$res->name:''
                        );
                }
            }

        }

        return $concerns;

    }

    
    
}