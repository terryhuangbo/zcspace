<?php
class IndexModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getIndex()//获取项目的所有信息
    {
        $limit = 10;
        $recmend = array();
        //获取推荐项目id
        $role = 'role55388437541c3';
        $recProjects = array();
        $sql = "SELECT c.role, c.role_id, SUM(c.concern)+SUM(c.praise)+SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY (SUM(c.concern)+SUM(c.praise)+SUM(c.comment)) DESC LIMIT ".$limit;
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $recProjects[] = $row->role_id;
        }
        $recmend['recProjects'] = $recProjects;

        //获取推荐孵化器id
        $role = 'role5538880cd6634';
        $recIncubators = array();
        $sql = "SELECT c.role, c.role_id, SUM(c.concern)+SUM(c.praise)+SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY (SUM(c.concern)+SUM(c.praise)+SUM(c.comment)) DESC LIMIT ".$limit;
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $recIncubators[] = $row->role_id;
        }
        $recmend['recIncubators'] = $recIncubators;

        //获取推荐投资者id
        $role = 'role553888d05a887';
        $recInvestors = array();
        $sql = "SELECT c.role, c.role_id, SUM(c.concern)+SUM(c.praise)+SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY (SUM(c.concern)+SUM(c.praise)+SUM(c.comment)) DESC LIMIT ".$limit;
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $recInvestors[] = $row->role_id;
        }
        $recmend['recInvestors'] = $recInvestors;

        return $recmend;

    }

   


}