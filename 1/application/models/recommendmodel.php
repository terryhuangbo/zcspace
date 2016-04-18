<?php
class RecommendModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getTeams()
    {
        $sql = "SELECT * FROM team ORDER BY concern DESC LIMIT 10";
        $query = $this->db->query($sql);

        return $query->result();
    }

    function addTeamConcern($teamId)
    {
        $this->db->select('*');
        $this->db->where('id', $teamId);
        $team = $this->db->get('team')->row();
        if(!count($team)){
            return false;
        }

        $this->db->where('id', $teamId);
        return $this->db->update('team', array('concern' => $team->concern+1));
    }

    function addProjectConcern($projectId)
    {
        $this->db->select('*');
        $this->db->where('role_id', $projectId);
        $project = $this->db->get('comment')->row();
        if(!count($project)){
            return false;
        }

        $this->db->where('role_id', $projectId);
        return $this->db->update('comment', array('concern' => $project->concern+1));
    }



   


}