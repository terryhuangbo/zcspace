<?php
class ProjectModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        date_default_timezone_set('Asia/Shanghai') ;
        parent::__construct();
    }

    function getProject($data)//获取项目的所有信息
    {   
        $projectId = $data['id'];
        $project = array();
        $selected = array( 
                'project.*',
                'project.id AS projectId',
                'project.name AS projectName',
                'images.*', 
                'images.url AS imgUrl', 
                'projectFinance.financeSource AS financeSource',
                'projectFinance.financeAmount AS financeAmount',
                'projectFinance.planFinanceAmount AS planFinanceAmount'
                
            ); 
        $where = array(
                'project.id' => $data['id'],
                'project.deleted' => 0
            );
        $this->db->select($selected);
        $this->db->where($where);
        $this->db->join('images', 'images.id=project.logoImgId', 'left');
        $this->db->join('projectFinance', 'projectFinance.id=project.projectFinanceId', 'left');

        $result = $this->db->get('project')->row();
        $project = array(
                'projectId' => isset($result->projectId)?$result->projectId:'',
                'projectFouderUserId' => isset($result->userId)?$result->userId:'',
                'projectName' => isset($result->projectName)?$result->projectName:'',
                'logoUrl' => isset($result->imgUrl)&&($result->imgUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$result->imgUrl:'',
                'entreOrentation' => isset($result->entreOrentation)?$result->entreOrentation:'',
                'projectProcess' => isset($result->projectProcess)?$result->projectProcess:'',
                'province' => isset($result->province)?$result->province:'',
                'city' => isset($result->city)?$result->city:'',
                'county' => isset($result->county)?$result->county:'',
                'brief' => isset($result->brief)?$result->brief:'',
                'introduction' => isset($result->introduction)?$result->introduction:'',
                'advantage' => isset($result->advantage)?$result->advantage:'',
                'financeSource' => isset($result->financeSource)?$result->financeSource:'',
                'financeSourceName' => isset($result->financeSourceName)?$result->financeSourceName:'',
                'financeAmount' => isset($result->financeAmount)?$result->financeAmount:'',
                'planFinanceAmount' => isset($result->planFinanceAmount)?$result->planFinanceAmount:'',

            );
        
        //标签
        $tags = array();
        $this->db->select('*');
        $this->db->where('projectTag.projectId', $data['id']);
        $reses = $this->db->get('projectTag')->result();
        if(count($reses)){
            foreach ($reses as $res) {
                // $tags[] =  array(
                //         'tagId' => $res->id,
                //         'name' => $res->tag
                //     );
                $tags[] =  $res->tag;

            }
        }
        $project['tags'] = $tags;

        //项目前景
        $prospect =  array();
        $prospectId = isset($result->prospectId)?$result->prospectId:'';        
        $this->db->select('*');
        $this->db->where('projectProspect.id', $prospectId);
        $res = $this->db->get('projectProspect')->row();
        $prospect = array(
                'estimateTime' => isset($res->estimateTime)?$res->estimateTime:'',
                'estimatePrice' => isset($res->estimatePrice)?$res->estimatePrice:0,
                'prospectDetail' => isset($res->detail)?$res->detail:''
            );

        $project['prospect'] = $prospect;

        //团队
        $team = array();
        $this->db->select('*');
        $this->db->where('team.projectId', $projectId);
        $re = $this->db->get('team')->row();
        
        $teamId = count($re)?$re->id:'';
        $team['teamId'] = $teamId;
        $team['name'] = count($re)?$re->name:'';
        $team['members'] = array();
        $this->db->select(array(
                'teamMember.*',
                'teamMember.id AS teamerId',
                'teamMember.name AS teamMemberName',
                'images.*',
                'images.url AS imgUrl'
            ));
        $this->db->where('teamMember.teamId', $teamId);
        $this->db->join('images', 'images.id=teamMember.avatarImageId', 'left');
        $res = $this->db->get('teamMember')->result();        

        foreach ($res as $v) {
            $team['members'][] = array(
                    'teamerId' => !is_null($v->teamerId)?$v->teamerId:'',
                    'avatarUrl' => !is_null($v->imgUrl)&&($v->imgUrl!='')?'http://'.$_SERVER['SERVER_NAME'].$v->imgUrl:'',
                    'name' => !is_null($v->teamMemberName)?$v->teamMemberName:'',
                    'position' => !is_null($v->position)?$v->position:'',
                    'memberPhoneNumber' => !is_null($v->mobile)?$v->mobile:'',
                    'memberBrief' => !is_null($v->brief)?$v->brief:'',
                    'school' => !is_null($v->school)?$v->school:''
                );
        }
        $project['team'] = $team;

        //召唤合伙人
        $newPartner = array();
        $this->db->select(array(
                'newPartner.*',
                'newPartner.id AS recruitId'                
            ));
        $this->db->where('newPartner.projectId', $data['id']);
        $res = $this->db->get('newPartner')->result();
        if(count($res)){
            foreach ($res as $re) {
                $newPartner[] = array(
                    'recruitId' => !is_null($re->recruitId)?$re->recruitId:'',
                    'position' => !is_null($re->position)?$re->position:'',
                    'cooperation' => !is_null($re->cooperation)?$re->cooperation:'',
                    'salary' => !is_null($re->salary)?$re->salary:'',
                    'stock' => !is_null($re->stock)?$re->stock:''
                );
            }
        }
        $project['newPartner'] = $newPartner;

        //图片
        $projectImages = array();
        $this->db->select('*');
        $this->db->where('projectImages.projectId', $data['id']);
        $res = $this->db->get('projectImages')->result();
        if(count($res)){
            foreach ($res as $r) {
               $projectImages[] = site_url('api/util/image?id='.isset($r->imageId)?$r->imageId:''); 
            }
        }
        $project['projectImages'] = $projectImages; 

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
        $project['praises'] = $praises; 

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
        $project['concerns'] = $concerns; 

        //评论 前三条
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
        $project['comments'] = $comments;

        
        return count($result)?$project:array();

    }

    function updateProject($projectId, $data)
    {

        $result = true;
        $this->db->select('*');
        $this->db->where('id', $projectId);
        $project = $this->db->get('project')->row();
        if(!count($project)){
            return false;
        }

        /*更新project表相关*/
        $value = array();        
        //项目名称
        array_key_exists('projectName', $data)?$value['name']=$data['projectName']:false;         
        //省份
        array_key_exists('province', $data)?$value['province']=$data['province']:false;
        //城市
        array_key_exists('city', $data)?$value['city']=$data['city']:false;
        //县
        array_key_exists('county', $data)?$value['county']=$data['county']:false;
        //创业方向
        array_key_exists('entreOrentation', $data)?$value['entreOrentation']=$data['entreOrentation']:false;
        //简介
        array_key_exists('brief', $data)?$value['brief']=$data['brief']:false;
        //详细介绍
        array_key_exists('introduction', $data)?$value['introduction']=$data['introduction']:false;
        //优势
        array_key_exists('advantage', $data)?$value['advantage']=$data['advantage']:false;
        //项目阶段
        array_key_exists('projectProcess', $data)?$value['projectProcess']=$data['projectProcess']:false;
        //更改状态：设为删除状态
        array_key_exists('deleted', $data)?$value['deleted']=$data['deleted']:false;
        //用户是否完成添加操作
        array_key_exists('completed', $data)?$value['completed']=$data['completed']:false;
        if(count($value)){
            $this->db->where('id', $projectId);
            $result = $this->db->update('project', $value);
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
        //添加projectProcess表记录
        if(isset($value['projectProcess'])&&$value['projectProcess']!=''){
            $projectProcess = trim($value['projectProcess']);
            $this->db->select('*');
            $this->db->where(array('name' => $projectProcess));
            $results = $this->db->get('projectProcess')->result();
            if(!count($results)){
                $this->db->insert('projectProcess', array(
                        'id' => uniqid('process'),
                        'name' => $projectProcess
                    ));
            }
        }

        //添加projectProcess表记录
        if(isset($value['projectProcess'])&&$value['projectProcess']!=''){
            $projectProcess = trim($value['projectProcess']);
            $this->db->select('*');
            $this->db->where(array('name' => $projectProcess));
            $results = $this->db->get('projectProcess')->result();
            if(!count($results)){
                $this->db->insert('projectProcess', array(
                        'id' => uniqid('process'),
                        'name' => $projectProcess
                    ));
            }
        }

        /*更新项目前景*/
        $value = array();        
        //上市时间
        array_key_exists('estimateTime', $data)?$value['estimateTime']=$data['estimateTime']:false; 
        //融资金额
        array_key_exists('estimatePrice', $data)?$value['estimatePrice']=$data['estimatePrice']:false; 
        //融资详情
        array_key_exists('prospectDetail', $data)?$value['detail']=$data['prospectDetail']:false;
        if(count($value)){
            $this->db->select('*');
            $this->db->where('id', $project->prospectId);
            $res = $this->db->get('projectProspect')->row();
            if(count($res)){
                $this->db->where('id', $project->prospectId);
                $result =$result&&$this->db->update('projectProspect', $value);
            }else{
                $projectProspectId = uniqid('prospect');
                $value['id'] = $projectProspectId;
                $result = $result&&$this->db->insert('projectProspect', $value);
                $this->db->where('id', $projectId);
                $result = $result&&$this->db->update('project', array('prospectId' => $projectProspectId));
            }
        }

        /*更新融资*/
        $value = array();        
        //资金来源Id
        array_key_exists('financeSource', $data)?$value['financeSource']=$data['financeSource']:false; 
        //融资金额
        array_key_exists('financeAmount', $data)?$value['financeAmount']=$data['financeAmount']:false; 
        //计划融资金额
        array_key_exists('planFinanceAmount', $data)?$value['planFinanceAmount']=$data['planFinanceAmount']:false;
        if(count($value)){
            $this->db->select('*');
            $this->db->where('id', $project->projectFinanceId);
            $res = $this->db->get('projectFinance')->row();
            if(count($res)){
                $this->db->where('id', $project->projectFinanceId);
                $result =$result&&$this->db->update('projectFinance', $value);
            }else{
                $projectFinanceId = uniqid('profinance');
                $value['id'] = $projectFinanceId;
                $value['projectId'] = $projectId;
                $result = $result&&$this->db->insert('projectFinance', $value);
                $this->db->where('id', $projectId);
                $result = $result&&$this->db->update('project', array('projectFinanceId' => $projectFinanceId));
            }
            
        }
        //添加financeSource表记录
        if(isset($value['financeSource'])){
            $financeSource = trim($value['financeSource']);
            $this->db->select('*');
            $this->db->where(array('name' => $financeSource));
            $results = $this->db->get('financeSource')->result();
            if(!count($results)){
                $this->db->insert('financeSource', array(
                        'id' => uniqid('source'),
                        'name' => $financeSource
                    ));
            }
        }

        return $result;
    }

    function updateTeamer($teamerId, $data)
    {
        $result = true;
        $this->db->select('*');
        $this->db->where('id', $teamerId);
        $project = $this->db->get('teamMember')->row();
        if(!count($project)){
            return false;
        }

        /*更新team表相关*/
        $value = array();        
        //名字
        array_key_exists('name', $data)?$value['name']=$data['name']:false; 
        //角色名称
        array_key_exists('position', $data)?$value['position']=$data['position']:false; 
        //学校名称
        array_key_exists('school', $data)?$value['school']=$data['school']:false; 

        if(count($value)){
            $this->db->where('id', $teamerId);
            $result = $this->db->update('teamMember', $value);
        }

        return $result;
    }

    function updateRecruit($projectId, $data)
    {

        $result = true;
        $this->db->select('*');
        $this->db->where('id', $projectId);
        $project = $this->db->get('newPartner')->row();
        if(!count($project)){
            return false;
        }

        /*更新project表相关*/
        $value = array();        
        //项目名称
        array_key_exists('projectName', $data)?$value['name']=$data['projectName']:false;         
        //省份
        array_key_exists('province', $data)?$value['province']=$data['province']:false;
        //城市
        array_key_exists('city', $data)?$value['city']=$data['city']:false;
        //县
        array_key_exists('county', $data)?$value['county']=$data['county']:false;
        //创业方向
        array_key_exists('entreOrentation', $data)?$value['entreOrentation']=$data['entreOrentation']:false;
        //简介
        array_key_exists('brief', $data)?$value['brief']=$data['brief']:false;
        //详细介绍
        array_key_exists('introduction', $data)?$value['introduction']=$data['introduction']:false;
        //优势
        array_key_exists('advantage', $data)?$value['advantage']=$data['advantage']:false;
        //项目阶段Id
        array_key_exists('projectProcess', $data)?$value['projectProcess']=$data['projectProcess']:false;
        //更改状态：设为删除状态
        array_key_exists('deleted', $data)?$value['deleted']=$data['deleted']:false;
        //用户是否完成添加操作
        array_key_exists('completed', $data)?$value['completed']=$data['completed']:false;
        if(count($value)){
            $this->db->where('id', $projectId);
            $result = $this->db->update('project', $value);
        }

        /*更新项目前景*/
        $value = array();        
        //上市时间
        array_key_exists('estimateTime', $data)?$value['estimateTime']=$data['estimateTime']:false; 
        //融资金额
        array_key_exists('estimatePrice', $data)?$value['estimatePrice']=$data['estimatePrice']:false; 
        //融资详情
        array_key_exists('prospectDetail', $data)?$value['detail']=$data['prospectDetail']:false;
        if(count($value)){
            $this->db->select('*');
            $this->db->where('id', $project->prospectId);
            $res = $this->db->get('projectProspect')->row();
            if(count($res)){
                $this->db->where('id', $project->prospectId);
                $result =$result&&$this->db->update('projectProspect', $value);
            }else{
                $projectProspectId = uniqid('prospect');
                $value['id'] = $projectProspectId;
                $result = $result&&$this->db->insert('projectProspect', $value);
                $this->db->where('id', $projectId);
                $result = $result&&$this->db->update('project', array('prospectId' => $projectProspectId));
            }
        }

        /*更新融资*/
        $value = array();        
        //资金来源Id
        array_key_exists('financeSource', $data)?$value['financeSource']=$data['financeSource']:false; 
        //融资金额
        array_key_exists('financeAmount', $data)?$value['financeAmount']=$data['financeAmount']:false; 
        //计划融资金额
        array_key_exists('planFinanceAmount', $data)?$value['planFinanceAmount']=$data['planFinanceAmount']:false;
        if(count($value)){
            $this->db->select('*');
            $this->db->where('id', $project->projectFinanceId);
            $res = $this->db->get('projectFinance')->row();
            if(count($res)){
                $this->db->where('id', $project->projectFinanceId);
                $result =$result&&$this->db->update('projectFinance', $value);
            }else{
                $projectFinanceId = uniqid('profinance');
                $value['id'] = $projectFinanceId;
                $value['projectId'] = $projectId;
                $result = $result&&$this->db->insert('projectFinance', $value);
                $this->db->where('id', $projectId);
                $result = $result&&$this->db->update('project', array('projectFinanceId' => $projectFinanceId));
            }
            
        }

        return $result;
    }

    function addProject($userId, $projectId, $teamId)
    {
        //添加project表记录
        $data = array(
                'id' => $projectId,
                'userId' => $userId
            );
        $result = $this->db->insert('project', $data);

        //添加team表记录
        $data = array(
                'id' => $teamId,
                'projectId' => $projectId
            );
        $result = $result&&$this->db->insert('team', $data);

        //添加teamMember表记录,将user设为创建人
        $teamerId = uniqid('teamer');
        $this->db->select('*');
        $this->db->where('id', $userId);
        $user = $this->db->get('user')->row();
        $data = array(
                'id' => $teamerId,
                'teamId' => $teamId,
                'projectId' => $projectId,
                'position' => '创始人',
                'name' => isset($user->nick)?$user->nick:'',
                'avatarImageId' => isset($user->avatarImgId)?$user->avatarImgId:'',
                'mobile' => isset($user->mobile)?$user->mobile:'',
            );
        $result = $result&&$this->db->insert('teamMember', $data);

        return $result;
    }

    function deleteProject($data)
    {
        //删除project表记录
        $this->db->where($data);
        $result = $this->db->delete('project');

        return $result;
    }

    function getComments($data)
    {
        $this->db->select('*');
        $this->db->where($data);
        $results = $this->db->get('project')->result();

        return $results;
    }

    function getProjectsByUserId($data)
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
        $results = $this->db->get('project')->result();

        return $results;
    }

    function rank($data)
    {   
        $rankId = $data['rankId'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $role = 'role55388437541c3';//项目roleId
        $projectIds = array();
        switch ($rankId) {
            case 'rankzzsatyqfds121':   //全部
                $sql = "SELECT * FROM project WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";      
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->id;
                }
                return $projectIds;
            break;

            case 'rankfdsajlfads1ar':   //智能排序?
                $sql = "SELECT * FROM project WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->id;
                }
                return $projectIds;
            break;

            case 'rankfdsa90gfds125':   //人气最高
                $sql = "SELECT c.role_id, SUM(c.praise) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.praise) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->role_id;
                }
                return $projectIds;
            break;

            case 'rankf23sajlfds123':   //评价最好
                $sql = "SELECT c.role_id, SUM(c.comment) AS num FROM (SELECT * FROM comment WHERE role = '".$role."') AS c GROUP BY c.role_id ORDER BY SUM(c.comment) DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->role_id;
                }
                return $projectIds;
            break;

            case 'rankt23sajlfds12f':   //发布时间
                $sql = "SELECT * FROM project WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->id;
                }
                return $projectIds;
            break;
            
            default:                    //其他
                $sql = "SELECT * FROM project WHERE `deleted` = 0 AND `completed` =1 ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
                $query = $this->db->query($sql);
                foreach ($query->result() as $row)
                {
                    $projectIds[] = $row->id;
                }
                return $projectIds;
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
        $projectIds = array();

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
        $results = $this->db->get('project')->result();
        return $results;
    }

    function keywords($data)
    {
        $keywords = $data['keywords'];
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        $begin = $pageSize*($page-1);

        $projectIds = array();

        $sql = "SELECT * FROM project WHERE `deleted` = 0 AND `completed` =1 AND (`name` LIKE '%{$keywords}%' OR `brief` LIKE '%{$keywords}%') ORDER BY create_time DESC LIMIT {$begin}, {$pageSize}";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row)
        {
            $projectIds[] = $row->id;
        }

        return $projectIds;
    }

    function getRank()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('rank')->result();

        return $results;
    }

    function getEntreorentation()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('entreorentation')->result();
        
        return $results;
    }

    function getProvince()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('province')->result();
        
        return $results;
    }

    function getCity()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('city')->result();
        
        return $results;
    }

    function getTag()
    {
        $this->db->select('*');
        $results = $this->db->get('tag')->result();
        
        return $results;
    }

    function getProjectProcess()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('projectProcess')->result();
        
        return $results;
    }

    function getFinanceSource()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('financeSource')->result();
        
        return $results;
    }

    function getPosition()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('position')->result();
        
        return $results;
    }

    function getCooperation()
    {
        $this->db->select('*');
        $this->db->order_by('id');
        $results = $this->db->get('cooperation')->result();
        
        return $results;
    }

    function getSalary()
    {
        $this->db->select('*');
        $results = $this->db->get('salary')->result();
        
        return $results;
    }

    function getStock()
    {
        $this->db->select('*');
        $results = $this->db->get('stock')->result();
        
        return $results;
    }

    function getTeam($data)
    {
        $this->db->select('*');
        $this->db->where($data);
        $result = $this->db->get('team')->row();
        
        return $result;
    }

    function addTeamer($data)
    {
        $result = $this->db->insert('teamMember', $data);
        return $result;
    }

    function deleteTeamer($data)
    {
        $this->db->where($data);
        $result = $this->db->delete('teamMember');
        
        return $result;
    }    

    function addNewPartner($data)
    {
        $result = $this->db->insert('newPartner', $data);
        return $result;
    }

    function deleteRecuit($data)
    {
        $this->db->where($data);
        $result = $this->db->delete('newPartner');
        
        return $result;
    } 

    function updateRecuit($recruitId, $data)
    {
        $this->db->where('id', $recruitId);
        $result = $this->db->update('newPartner', $data);
        
        return $result;
    }

    function addTag($data)
    {
        $result = $this->db->insert('projectTag', $data);
        
        return $result;
    }

    function deleteTag($data)
    {
        $this->db->where($data);
        $result = $this->db->delete('projectTag');
        
        return $result;
    }
    function getImg($projectId)
    {
        $this->db->select('*');
        $this->db->where('id', $projectId);
        $result = $this->db->get('project')->row();
        
        return $result;
    }


}