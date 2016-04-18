<?php
class InvestorModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getInvestor($data)//获取项目的相关信息
    {
        $investorId = $data['id'];
        $investor = array();
        $selected = array(
            'investor.*',
            'investor.id AS investorId',
            'investor.name AS investorName',
            'images.*',
            'images.url AS imgUrl',
            'investor.name AS investorName',
            'user.nick AS investOwnerName'
                 
            );
        $this->db->select($selected);
        $this->db->where('investor.id', $data['id']);
        $this->db->join('images', 'images.id=investor.logoImgId', 'left');
        $this->db->join('user', 'user.id=investor.userId', 'left');

        $result = $this->db->get('investor')->row();
        if(count($result)){
            $investor = array(
                    'investorId' => isset($result->investorId)?$result->investorId:'',
                    'investorName' => isset($result->investorName)?$result->investorName:'',
                    'investOwnerName' => isset($result->investOwnerName)?$result->investOwnerName:'',
                    'logoUrl' => isset($result->imgUrl)&&($result->imgUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$result->imgUrl:'',
                    'province' => isset($result->province)?$result->province:'',
                    'city' => isset($result->city)?$result->city:'',
                    'county' => isset($result->county)?$result->county:'',
                    'brief' => isset($result->brief)?$result->brief:'',
                    'introduction' => isset($result->introduction)?$result->introduction:'',
                );

        }

        //投资项目
        $investProjects = array();
        $this->db->select(array(
                'investProject.*',
                'images.*',
                'investProject.id AS invProId'
            ));
        $this->db->where('investorId', $investorId);
        $this->db->join('images', 'images.id=investProject.logoImgId', 'left');
        $res = $this->db->get('investProject')->result();

        if(count($res)){
            foreach ($res as $r) {
                $investProjects[] = array(
                    'id' => !is_null($r->id)?$r->invProId:'',
                    'name' => !is_null($r->name)?$r->name:'',
                    'logoUrl' => isset($r->url)&&($r->url!='')?'http://'.$_SERVER['SERVER_NAME'].$r->url:'',
                    'entreOrentation' => !is_null($r->entreOrentation)?$r->entreOrentation:'',
                    'brief' => !is_null($r->brief)?$r->brief:'',
                    'process' => !is_null($r->process)?$r->process:''
                );
            }
        }
        $investor['investProjects'] = $investProjects;

        //投资人
        $investPartners = array();
        $this->db->select(array(
                'investPartner.*',
                'investPartner.id AS partnerId',
                'images.*'
            ));
        $this->db->where('investPartner.investorId', $data['id']);
        $this->db->join('images', 'images.id=investPartner.avatarImgId', 'left');
        $res = $this->db->get('investPartner')->result();
        if(count($res)){
            foreach ($res as $r) {
                $investPartners[] = array(
                        'investPartId' => !is_null($r->partnerId)?$r->partnerId:'',
                        'name' => !is_null($r->name)?$r->name:'',
                        'avatarUrl' => isset($r->url)&&($r->url!='')?'http://'.$_SERVER['SERVER_NAME'].$r->url:'',
                    );
            }

        }
        $investor['investPartners'] = $investPartners;

        //关注领域
        $industryConcerned = array();
        $this->db->select('*');
        $this->db->where('industryConcerned.investorId', $investorId);
        $this->db->join('industry', 'industry.id=industryConcerned.entreOrentation', 'left');
        $res = $this->db->get('industryConcerned')->result();
        if(count($res)){
            foreach ($res as $r) {
                // $industryConcerned['id'] = !is_null($r->id)?$r->id:'';
                $industryConcerned[] = !is_null($r->entreOrentation)?$r->entreOrentation:'';
            }

        }
        $investor['industryConcerned'] = $industryConcerned;

        //点赞
        $praises = array();
        $this->db->select('*');
        $this->db->where(array(
                'role_id' => $data['id'],
                'praise' => 1
            ));
        $res = $this->db->get('comment')->result();  
        if(count($res)){
            foreach ($res as $r) {
                $praises[] = array(
                    'role' => $r->role,
                    'role_id' => $r->role_id,
                    'content' => $r->content);
            }
        }  
        $investor['praises'] = $praises; 

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
                'role_id' => $data['id'],
                'comment' => 1
            ));
        $res = $this->db->limit(3); 
        $res = $this->db->get('comment')->result(); 
        if(count($res)){
            foreach ($res as $r) {
                $comments[] = array(
                    'commentId' => $r->commentId,
                    'commenterImgUrl' => !is_null($r->commenterImgUrl)&&($r->commenterImgUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$r->commenterImgUrl:'',
                    'commenterName' => !is_null($r->commenterName)?$r->commenterName:'',
                    'text' => $r->content,
                    'commentTime' => $r->commentTime

                    );
            }
        }  
        $investor['comments'] = $comments;

        //关注
        $concerns = array();
        $this->db->select('*');
        $this->db->where(array(
                'role_id' => $data['id'],
                'concern' => 1
            ));
        $res = $this->db->get('comment')->result();  
        if(count($res)){
            foreach ($res as $r) {
                $concerns[] = array(
                    'role' => $r->role,
                    'role_id' => $r->role_id,
                    'content' => $r->content);
            }
        }  
        $investor['concerns'] = $concerns;

        return count($result)?$investor:array();

    }

    function addInvestor($data)
    {
        $result = $this->db->insert('investor', $data);
        return $result;
    }

    function updateInvestor($investorId, $data)
    {

        $result = true;
        $this->db->select('*');
        $this->db->where('id', $investorId);
        $investor = $this->db->get('investor')->row();
        if(!count($investor)){
            return false;
        }

        /*更新investor表相关*/
        $value = array();        
        //投资人/方名称
        array_key_exists('investorName', $data)?$value['name']=$data['investorName']:false; 
        //省份
        array_key_exists('province', $data)?$value['province']=$data['province']:false;
        //城市
        array_key_exists('city', $data)?$value['city']=$data['city']:false;
        //县
        array_key_exists('county', $data)?$value['county']=$data['county']:false;
        //投资人/方方向Id
        array_key_exists('entreOrentation', $data)?$value['entreOrentation']=$data['entreOrentation']:false;
        //简介
        array_key_exists('brief', $data)?$value['brief']=$data['brief']:false;
        //详细介绍
        array_key_exists('introduction', $data)?$value['introduction']=$data['introduction']:false;
        //用户是否完成添加操作
        array_key_exists('completed', $data)?$value['completed']=$data['completed']:false;
        if(count($value)){
            $this->db->where('id', $investorId);
            $result = $result&&$this->db->update('investor', $value);
        }

        /*更新关注领域-industryConcerned表相关*/
        if(array_key_exists('industryConcerned', $data)){
            if(is_array($data['industryConcerned'])&&count($data['industryConcerned'])){
                $this->db->where('investorId', $investorId);
                $result = $result&&$this->db->delete('industryConcerned');
                $i = 0;
                foreach ($data['industryConcerned'] as $v) {
                    $result = $result&&$this->db->insert('industryConcerned', array(
                            'id' => uniqid('indcon'.$i),
                            'investorId' => $investorId,
                            'entreOrentation' => $v));
                    $i++;
                }
            }
        }

        //添加创业方向记录
        if(isset($value['entreOrentation'])&&$value['entreOrentation']!=''){
            $entreOrentation = trim($value['entreOrentation']);
            $this->db->select('*');
            $this->db->where(array('name' => $entreOrentation));
            $results = $this->db->get('entreorentation')->result();
            if(!count($results)){
                $this->db->insert('entreorentation', array(
                        'id' => uniqid('entre'),
                        'name' => $entreOrentation
                    ));
            }

        }
        //添加city表记录
        if(isset($value['city'])&&$value['city']!=''){
            $city = trim($value['city']);
            $this->db->select('*');
            $this->db->where(array('name' => $city));
            $results = $this->db->get('city')->result();
            if(!count($results)){
                $this->db->insert('city', array(
                        'id' => uniqid('city'),
                        'name' => $city
                    ));
            }
        }
        //添加province表记录
        if(isset($value['province'])&&$value['province']!=''){
            $province = trim($value['province']);
            $this->db->select('*');
            $this->db->where(array('name' => $province));
            $results = $this->db->get('province')->result();
            if(!count($results)){
                $this->db->insert('province', array(
                        'id' => uniqid('province'),
                        'name' => $province
                    ));
            }
        }
        
        return $result;
        

    }

    function deleteInvestor($data)
    {
        //删除investor表记录
        $this->db->where($data);
        $result = $this->db->delete('investor');

        return $result;
    }

    function rank($data)
    {   
        $rankId = $data['rankId'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);           

        $role = 'role553888d05a887';//投资人roleId
        $investorIds = array();
        switch ($rankId) {
            case 'rankzzsatyqfds121':   //全部
                $sql = "SELECT * FROM investor WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->id;
                }
                return $investorIds;
            break;

            case 'rankfdsajlfads1ar':   //智能排序?
                $sql = "SELECT * FROM investor WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->id;
                }
                return $investorIds;
            break;

            case 'rankfdsa90gfds125':   //人气最高
                $sql = "SELECT c.role_id, SUM(c.praise) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.praise) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->role_id;
                }
                return $investorIds;
            break;

            case 'rankf23sajlfds123':   //评价最好
                $sql = "SELECT c.role_id, SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.comment) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->role_id;
                }
                return $investorIds;
            break;

            case 'rankt23sajlfds12f':   //发布时间
                $sql = "SELECT * FROM investor WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->id;
                }
                return $investorIds;
            break;
            
            default:                    //其他
                $sql = "SELECT * FROM investor WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $investorIds[] = $row->id;
                }
                return $investorIds;
            break;
        }
        
    }

    function filter($data)
    {
        $entreOrentation = $data['entreOrentation'];
        $city = $data['city'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $where = array();
        if($entreOrentation!='全部'){
            $where['entreOrentation'] = $entreOrentation;
        }
        if($city!='全部'){
            $where['city'] = $city;
        }
        $where['deleted'] = 0;
        $where['completed'] = 1;
        $this->db->where($where);
        $this->db->limit($pageSize, $begin);
        $results = $this->db->get('investor')->result();
        
        return $results;
    }

    function keywords($data)
    {
        $keywords = $data['keywords'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $investorIds = array();

        $sql = "SELECT * FROM investor WHERE `deleted` = 0 AND `completed` =1 AND (`name` LIKE '%{$keywords}%' OR `brief` LIKE '%{$keywords}%') ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $investorIds[] = $row->id;
        }

        return $investorIds;
    }

    function getInvestorsByUserId($data)//获取我的
    {   
        $userId = $data['userId'];
        $this->db->select('*');
        $this->db->where($data);
        $result = $this->db->get('investor')->row();
        //如果没有值，则插入一条
        if(!count($result)){
            $investorId = uniqid('investor');
            $this->db->insert('investor', array(
                    'id' => $investorId,
                    'userId' => $userId
                ));  

            $this->db->select('*');
            $this->db->where($data);
            $result = $this->db->get('investor')->row();       
        }
        return $result;
    }

    function addInvestPartner($data)
    {
        $result = $this->db->insert('investPartner', $data);
        return $result;
    }

    function deleteInvestPartner($data)
    {
        $this->db->where($data);
        $result = $this->db->delete('investPartner');
        return $result;
    }

    function addProject($data)
    {
        $result = $this->db->insert('project', $data);
        return $result;
    }

    function addInvestProject($data)
    {
        $result = $this->db->insert('investProject', $data);
        return $result;
    }

    function getEntreOrentation()
    {
        $this->db->select('*');
        $results = $this->db->get('entreOrentation')->result();
        
        return $results;
    }

    function deleteInvestProject($data)
    {
        $this->db->where($data);
        return $this->db->delete('investProject');   
    }

    function addInvestConcert($data)
    {
        $result = $this->db->insert('industryConcerned', $data);
        return $result;
    }

    function deleteInvestConcert($data)
    {
        $this->db->where($data);
        return $this->db->delete('industryConcerned');  
    }
    function getImg($investorId)
    {
        $this->db->select('*');
        $this->db->where('id', $investorId);
        $result = $this->db->get('investor')->row();
        
        return $result;
    }
}