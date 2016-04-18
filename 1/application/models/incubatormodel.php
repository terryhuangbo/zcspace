<?php
class IncubatorModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getIncubator($data)//获取孵化器的相关信息
    {
        $incubator = array();
        $incubatorId = $data['id'];
        $selected = array(
                'incubator.*', 
                'incubator.id AS incubatorId',
                'images.*',
                'images.url AS imgUrl',
                'incubator.name AS incubatorName',

            );
        $where = array(
                'incubator.id' => $data['id'],
                'incubator.deleted' => 0
            );
        $this->db->select($selected);
        $this->db->where($where);
        $this->db->join('images', 'images.id=incubator.logoImgId', 'left');

        $result = $this->db->get('incubator')->row();

        if(count($result)){ 
            $incubator = array(
                'incubatorId' => isset($result->incubatorId)?$result->incubatorId:'',
                'incubatorName' => isset($result->incubatorName)?$result->incubatorName:'',
                'logoUrl' => isset($result->imgUrl)&&($result->imgUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$result->imgUrl:'',
                'province' => isset($result->province)?$result->province:'',
                'city' => isset($result->city)?$result->city:'',
                'county' => isset($result->county)?$result->county:'',
                'addressDetail' => isset($result->address)?$result->address:'',
                'acreage' => isset($result->acreage)?$result->acreage:'',
                'brief' => isset($result->brief)?$result->brief:'',
                'introduction' => isset($result->introduction)?$result->introduction:'',
                'price' => isset($result->price)?$result->price:'',
                'requirement' => isset($result->requirement)?$result->requirement:'',
                'propertyService' => isset($result->propertyService)?$result->propertyService:'',
                'specialService' => isset($result->specialService)?$result->specialService:''

            );
        }

        //孵化器的联系方式
        $this->db->select('*');
        $this->db->where('id', isset($result->userId)?$result->userId:'');
        $res = $this->db->get('user')->row();
        $contact = count($res)?$res->mobile:'';
        $incubator['contact'] = $contact;

        //孵化器的相关活动
        $activities = array();
        $this->db->select('*');
        $this->db->where('incubatorId', $data['id']);
        $res = $this->db->get('incubatorActivity')->result();
        foreach ($res as $r) {
            $incuActId = $r->id;
            $incuactInvestors = array();//投资人
            $incuActId = isset($r->id)?$r->id:'';
            $this->db->select(array(
                    'incuActInvestor.*',
                    'incuActInvestor.id AS actInvId',
                    'images.url AS avatarUrl'
                ));
            $this->db->where('incuActInvestor.incubatorActivityId', $incuActId);
            $this->db->join('images', 'images.id=incuActInvestor.avatarImgId', 'left');
            $re = $this->db->get('incuActInvestor')->result();
            foreach ($re as $v) {
                $incuactInvestors[] = array(
                        'id' => isset($v->actInvId)&&!is_null($v->actInvId)?$v->actInvId:'',
                        // 'incuActId' => isset($v->incubatorActivityId)&&!is_null($v->incubatorActivityId)?$v->incubatorActivityId:'',
                        'name' =>  isset($v->name)&&!is_null($v->name)?$v->name:'',
                        'avatarUrl' =>  isset($v->avatarUrl)&&($v->avatarUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$v->avatarUrl:'',
                    );
            }
            $activities[] = array(
                    'activityId' => $incuActId,
                    'name' => isset($r->name)?$r->name:'',
                    'time' => isset($r->time)?$r->time:'',
                    'detail' => isset($r->detail)?$r->detail:'',
                    'incuactInvestors' => $incuactInvestors
                );
        }

        $incubator['activities'] = $activities;

        //明星项目
        $starProjects = array();
        $this->db->select(array(
                'incubatorProject.*',
                'images.*',
                'incubatorProject.id AS incuProId'
            ));
        $this->db->where('incubatorId', $incubatorId);
        $this->db->join('images', 'images.id=incubatorProject.logoImgId', 'left');
        $res = $this->db->get('incubatorProject')->result();
        if(count($res)){
            foreach ($res as $r) {
                $starProjects[] = array(
                    'id' => !is_null($r->id)?$r->incuProId:'',
                    'name' => !is_null($r->name)?$r->name:'',
                    'logoUrl' => isset($r->url)&&($r->url!='')?'http://'.$_SERVER['SERVER_NAME'].$r->url:'',
                    'entreOrentation' => !is_null($r->entreOrentation)?$r->entreOrentation:'',
                    'brief' => !is_null($r->brief)?$r->brief:'',
                    'process' => !is_null($r->process)?$r->process:''
                );
            }
        }
        
        $incubator['starProjects'] = $starProjects; 

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
        $incubator['praises'] = $praises; 

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
        $incubator['concerns'] = $concerns;

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
        $this->db->limit(3); 
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
        $incubator['comments'] = $comments;

        //上传的文件
        $files = array();
        $this->db->select(array(
            'files.*'
            ));
        $this->db->where(array(
            'incubatorId' => $incubatorId
            ));

        $filesFromDb = $this->db->get('files')->result(); 
        if(count($filesFromDb)){
            foreach ($filesFromDb as $file) {
                $files[] = array(
                    'fileId' => $file->id,
                    'fileUrl' => !(is_null($file->url)&&($file->url!=''))?'http://'.$_SERVER['SERVER_NAME'].$file->url:'',
                    'fileName' => !is_null($file->name)?$file->name:'',
                    'incubatorId' => $file->incubatorId
                    );
            }
        } 
        $incubator['files'] = $files;

        return count($result)?$incubator:array();
    }

    function updateIncubator($incubatorId, $data)
    {

        $result = true;
        $this->db->select('*');
        $this->db->where('id', $incubatorId);
        $incubator = $this->db->get('incubator')->row();        
        if(!count($incubator)){
            return false;
        }

        /*更新project表相关*/
        $value = array();        
        //孵化器名称
        array_key_exists('incubatorName', $data)?$value['name']=$data['incubatorName']:false;        
        //省份
        array_key_exists('province', $data)?$value['province']=$data['province']:false;
        //城市
        array_key_exists('city', $data)?$value['city']=$data['city']:false;
        //县
        array_key_exists('county', $data)?$value['county']=$data['county']:false;
        //孵化器方向Id
        array_key_exists('entreOrentation', $data)?$value['entreOrentation']=$data['entreOrentation']:false;
        //面积
        array_key_exists('acreage', $data)?$value['acreage']=$data['acreage']:false;
        //简介
        array_key_exists('brief', $data)?$value['brief']=$data['brief']:false;
        //详细介绍-入住情况
        array_key_exists('introduction', $data)?$value['introduction']=$data['introduction']:false;
        //租金
        array_key_exists('price', $data)?$value['price']=$data['price']:false;
        //入驻要求
        array_key_exists('requirement', $data)?$value['requirement']=$data['requirement']:false;
        //物业要求
        array_key_exists('propertyService', $data)?$value['propertyService']=$data['propertyService']:false;
        //特殊服务
        array_key_exists('specialService', $data)?$value['specialService']=$data['specialService']:false;
        //删除
        array_key_exists('deleted', $data)?$value['deleted']=$data['deleted']:false;
        //用户是否完成添加操作
        array_key_exists('completed', $data)?$value['completed']=$data['completed']:false;
        /*更新孵化器详细地址 addressDetail*/
        array_key_exists('addressDetail', $data)?$value['address']=$data['addressDetail']:false;
        if(count($value)){
            $this->db->where('id', $incubatorId);
            $result = $result&&$this->db->update('incubator', $value);
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

    function addIncubator($data)
    {
        //添加project表记录
        $result = $this->db->insert('incubator', $data);

        return $result;
    }

    function deleteIncubator($data)
    {
        //删除incubator表记录
        $this->db->where($data);
        $result = $this->db->delete('incubator');

        return $result;
    }

    function rank($data)
    {   
        $rankId = $data['rankId'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $role = 'role5538880cd6634';//孵化器roleId
        $incubatorIds = array();
        switch ($rankId) {
            case 'rankzzsatyqfds121':   //全部
                $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->id;
                }
                return $incubatorIds;
            break;

            case 'rankt23saghjds134':   //距离远近
                $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->id;
                }
                return $incubatorIds;
            break;

            case 'rankfdsajlfads1ar':   //智能排序?
                $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->id;
                }
                return $incubatorIds;
            break;

            case 'rankfdsa90gfds125':   //人气最高
                $sql = "SELECT c.role_id, SUM(c.praise) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.praise) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->role_id;
                }
                return $incubatorIds;
            break;

            case 'rankf23sajlfds123':   //评价最好
                $sql = "SELECT c.role_id, SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.comment) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->role_id;
                }
                return $incubatorIds;
            break;

            case 'rankt23sajlfds12f':   //发布时间
                $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->id;
                }
                return $incubatorIds;
            break;
            
            default:                    //其他
                $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $incubatorIds[] = $row->id;
                }
                return $incubatorIds;
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
        $results = $this->db->get('incubator')->result();
        
        return $results;
    }

    function keywords($data)
    {
        $keywords = $data['keywords'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $incubatorIds = array();

        $sql = "SELECT * FROM incubator WHERE `deleted` = 0 AND `completed` =1 AND (`name` LIKE '%{$keywords}%' OR `brief` LIKE '%{$keywords}%') ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $incubatorIds[] = $row->id;
        }

        return $incubatorIds;
    }

    function getIncubatorsByUserId($data)
    {
        $userId = $data['userId'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];

        $this->db->select('*');
        $where = array(
                'userId' => $data['userId'],
                'deleted' => 0
            );
        $this->db->where($where);
        $this->db->limit($pageSize, ($page-1)*$pageSize);
        $results = $this->db->get('incubator')->result();

        return $results;
    }

    function getProvince()
    {
        $this->db->select('*');
        $results = $this->db->get('province')->result();
        
        return $results;
    }

    function getCity()
    {
        $this->db->select('*');
        $results = $this->db->get('city')->result();
        
        return $results;
    }

    function addStarProject($data)
    {
        return $results = $this->db->insert('incubatorProject', $data);   
    }

    function deleteStarProject($data)
    {
        $this->db->where($data);
        return $this->db->delete('incubatorProject');   
    }
    function getImg($incubatorId)
    {
        $this->db->select('*');
        $this->db->where('id', $incubatorId);
        $result = $this->db->get('incubator')->row();
        
        return $result;
    }




}