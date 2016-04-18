<?php
class CommentModel extends CI_Model {
    function __construct()
    {
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getComments($data, $pageData = array())//获取comment表的信息，包括评论，点赞，推荐
    {   
        $page = isset($pageData['page'])?$pageData['page']:0;
        $pageSize = isset($pageData['pageSize'])?$pageData['pageSize']:0;
        $this->db->select('*');
        $this->db->where($data); 
        $this->db->group_by('role_id');
        isset($pageData['page'])?$this->db->limit($pageSize, ($page-1)*$pageSize):FALSE;
        $results = $this->db->get('comment')->result();
        $comments = array();
        if(count($results)){
            foreach ($results as $result) {
                if($result->role=='role55388437541c3'){//创业者
                    $this->db->select(array(
                            'project.*',
                            'project.id AS projectId',
                            'user.*'
                        ));
                    $where = array(
                            'project.id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $this->db->join('user', 'user.id=project.userId', 'left');
                    $res = $this->db->get('project')->row();                    
                    $comments[] = array(
                            'role' => '创业者',
                            'role_id' => count($res)?$res->projectId:'',
                            'role_name' => count($res)?$res->nick:'',
                        );

                }else if($result->role=='role5538880cd6634'){ //孵化器
                    $this->db->select(array(
                            'incubator.*',
                            'incubator.id AS incubatorId',
                            'user.*'
                        ));
                    $where = array(
                            'incubator.id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $this->db->join('user', 'user.id=incubator.userId', 'left');
                    $res = $this->db->get('incubator')->row();                    
                    $comments[] = array(
                            'role' => '孵化器',
                            'role_id' => is_null((count($res)?$res->incubatorId:'')) ? '' : (count($res)?$res->incubatorId:''),
                            'role_name' => is_null((count($res)?$res->nick:'')) ? '' : (count($res)?$res->nick:''),
                        );
                    
                }else if($result->role=='role553888d05a887'){//投资者
                    $this->db->select(array(
                            'investor.*',
                            'investor.id AS investorId',
                            'user.*'
                        ));
                    $where = array(
                            'investor.id' => $result->role_id,
                            'deleted' => 0
                        );
                    $this->db->where($where);
                    $this->db->join('user', 'user.id=investor.userId', 'left');
                    $res = $this->db->get('investor')->row();                    
                    $comments[] = array(
                            'role' => '投资者',
                            'role_id' => is_null((count($res)?$res->investorId:'')) ? '' : (count($res)?$res->investorId:''),
                            'role_name' => is_null((count($res)?$res->nick:'')) ? '' : (count($res)?$res->nick:''),
                        );
                }
            }

        }



        return $comments;
    }

    function addComment($data)
    {
        return $this->db->insert('comment', $data);
    }

    function getComment($data) //获取评论
    {
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);
        //评论
        $comments = array();
        $this->db->select(array(
                'comment.*',
                'comment.id AS commentId',
                'comment.dateTime AS commentTime',
                'user.nick AS commenterName',
                'images.url AS commenterImgUrl'

            ));
        $this->db->join('user', 'user.id=comment.userId', 'left');
        $this->db->join('images', 'images.id=user.avatarImgId', 'left');
        $this->db->where(array(
                'role_id' => $data['role_id'],
                'comment' => 1
            ));
        $this->db->limit($pageSize, $begin);
        $res = $this->db->get('comment')->result(); 
        if(count($res)){
            foreach ($res as $r) {
                $comments[] = array(
                    'commentId' => $r->commentId,
                    'commenterImgUrl' => !is_null($r->commenterImgUrl)?'http://'.$_SERVER['SERVER_NAME'].$r->commenterImgUrl:'',
                    'commenterName' => !is_null($r->commenterName)?$r->commenterName:'',
                    'text' => $r->content,
                    'commentTime' => $r->commentTime

                    );
            }
        }

        return $comments;
    }
    
}