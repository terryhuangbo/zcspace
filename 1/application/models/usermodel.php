<?php
class UserModel extends CI_Model {
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function add($data)
    {
        $this->db->insert('user', $data); 
    }

    function getInfo($userId)
    {
        $this->db->select('user.id, user.nick, user.mobile, images.id as avatar');
        $this->db->from('user');
        $this->db->join('images', 'user.avatarImgId = images.id', 'left');
        $this->db->where(array('user.id' => $userId));
        $query = $this->db->get();

        return $query->row(); 
    }

    function getInfoByWeixinOpenId($openId)
    {
        $this->db->select('user.id, user.nick, user.mobile, images.id as avatar');
        $this->db->from('user');
        $this->db->join('images', 'user.avatarImgId = images.id', 'left');
        $this->db->where(array('user.weixinOpenId' => $openId));
        $query = $this->db->get();

        return $query->row(); 
    }

    function weixinBind($userId, $openId)
    {
        $this->db->where('id', $userId);
        return $this->db->update('user', array('weixinOpenId'=>$openId));
    }

    function getInfoByWeiboAccessToken($accessToken)
    {
        $this->db->select('user.id, user.nick, user.mobile, images.id as avatar');
        $this->db->from('user');
        $this->db->join('images', 'user.avatarImgId = images.id', 'left');
        $this->db->where(array('user.weiboAccessToken' => $accessToken));
        $query = $this->db->get();

        return $query->row(); 
    }

    function weiboBind($userId, $accessToken)
    {
        $this->db->where('id', $userId);
        return $this->db->update('user', array('weiboAccessToken'=>$accessToken));
    }

    function getInfoByQQOpenId($openId)
    {
        $this->db->select('user.id, user.nick, user.mobile, images.id as avatar');
        $this->db->from('user');
        $this->db->join('images', 'user.avatarImgId = images.id', 'left');
        $this->db->where(array('user.qqOpenId' => $openId));
        $query = $this->db->get();

        return $query->row(); 
    }

    function qqBind($userId, $openId)
    {
        $this->db->where('id', $userId);
        return $this->db->update('user', array('user.qqOpenId'=>$openId));
    }

    function getUserByPhoneNum($data)
    {
        $this->db->select();
        $this->db->from('user');
        $this->db->where('mobile' ,$data);
        $row = $this->db->get()->row(); 
    
        return $row;

    }

    function getPropertyInfo($applyUserId)
    {
        $this->db->select('property.id, i1.id as licenseUrl');
        $this->db->from('property');
        $this->db->join('images i1', 'property.licenseImageId = i1.id', 'left');
        $this->db->where('applyUserId', $applyUserId); 
        $row = $this->db->get()->row();
        return $row;
    }

    function getAdminInfo($applyUserId)
    {
        $this->db->select('admin.id, admin.applyUserId');
        $this->db->from('admin');
        $this->db->where('applyUserId', $applyUserId); 
        $row = $this->db->get()->row();
        return $row;
    }

    function updateInfo($userId, $newData)
    {
        $data =array();
        if(isset($newData['nick'])&&strlen($newData['nick']) > 0)
        {
            $data['nick']=$newData['nick'];
        }
        if(isset($newData['gender'])&&strlen($newData['gender']) > 0)
        {
            $data['gender']=$newData['gender'];
        }
        if(isset($newData['birthday'])&&strlen($newData['birthday']) > 0)
        {
            $data['birthday']=$newData['birthday'];
        }
        if(isset($newData['province'])&&strlen($newData['province']) > 0)
        {
            $data['province']=$newData['province'];
        }
        if(isset($newData['city'])&&strlen($newData['city']) > 0)
        {
            $data['city']=$newData['city'];
        }
        if(isset($newData['county'])&&strlen($newData['county']) > 0)
        {
            $data['county']=$newData['county'];
        }
        // if(isset($newData['mobile'])&&strlen($newData['mobile']) > 0)
        // {
        //     $data['mobile']=$newData['mobile'];
        // }
        if(isset($newData['email'])&&strlen($newData['email']) > 0)
        {
            $data['email']=$newData['email'];
        }
        
        /*if(isset($newData['avatarUrl'])&&strlen($newData['avatarUrl']) > 0)
        {
            $imageId = uniqid('image');
            $this->db->insert('images', array('id'=>$imageId, 'url'=>$newData['avatarUrl'])); 
            $data['avatarImgId']=$imageId;
        }*/

        if(count($data)>0)
        {
            $this->db->where('id', $userId);
            return $this->db->update('user', $data);
        }else{
            return false;
        }
        
    }

    function isPhoneRegistered($phoneNumber)
    {
    	$user = $this->db->get_where('user', array('mobile' => $phoneNumber))->result();
    	return count($user) > 0;
    }

    function login($phoneNumber, $password)
    {
        $selected = array(
                'user.*',
                'user.id AS userId',
                'auth.*'
            );
    	$this->db->select($selected);
        $this->db->from('user');
        $this->db->join('auth', 'user.id = auth.userId', 'left');

        $this->db->where(array('user.mobile' => $phoneNumber, 'auth.password' => $password));
        $query = $this->db->get();
        return $query->row();
    }

    function getUserRoles($data)
    {
        $this->db->select('*');
        $this->db->from('userRole');
        $this->db->join('role', 'role.id=userRole.roleId');
        $this->db->where($data);
        $result = $this->db->get()->result();

        return $result;
    }

    function getRoles()
    {
        $this->db->select('*');
        $this->db->from('role');
        $result = $this->db->get()->result();

        return $result;
    }

    function getUser($data)
    {
        $selected = array(
                'user.*',
                'user.id AS userId',
                'images.url AS imgUrl'
            );
        $this->db->select($selected);
        $this->db->from('user');
        $this->db->join('images', 'user.avatarImgId = images.id', 'left');
        
        $this->db->where("user.id", $data);
        $query = $this->db->get();
        return $query->row();

    }

    function getSchools($data)
    {
        $this->db->select('*');
        $this->db->from('school');
        $this->db->where($data);
        $result = $this->db->get()->result();

        return $result;
    }

    function getProjectExperience($data)
    {
        $this->db->select('*');
        $this->db->from('projectExperience');
        $this->db->where($data);
        $result = $this->db->get()->result();

        return $result;
    }

    function getWorkExperience($data)
    {
        $this->db->select('*');
        $this->db->from('workExperience');
        $this->db->where($data);
        $result = $this->db->get()->result();

        return $result;
    }

    function addSchoolExperience($data)
    {
        $result = $this->db->insert('school', $data);
        return $result;
    }

    function deleteSchoolExperience($data)
    {
        $this->db->where($data);
        return $this->db->delete('school'); 
    }

    function addProjectExperience($data)
    {
        $result = $this->db->insert('projectExperience', $data);
        return $result;
    }

    function deleteProjectExperience($data)
    {
        $this->db->where($data);
        return $this->db->delete('projectExperience'); 
    }

    function addWorkExperience($data)
    {
        $result = $this->db->insert('workExperience', $data);
        return $result;
    }

    function deleteWorkExperience($data)
    {
        $this->db->where($data);
        return $this->db->delete('workExperience'); 
    }

    function getUserRole($data)
    {   
        $this->db->select(array(
                'userRole.roleId AS roleId',
                'role.name AS roleName'));

        $this->db->join('role','role.id=userRole.roleId', 'left');
        $this->db->where($data);
        $results = $this->db->get('userRole')->result();

        return $results;
    }

    function updateUserRole($data)
    {   
        $userId = $data['userId'];
        $roles = $data['roles'];
        $result = true;

        $this->db->where(array('userId' => $userId));
        $this->db->delete('userRole');
        $i=0;
        foreach ($roles as $role) {
            $userRoleId = uniqid('userRole'.$i);
            $result = $result&&$this->db->insert('userRole', array(
                    'id' => $userRoleId,
                    'userId' => $userId,
                    'roleId' => $role
                ));
        }

        return $result;
    }


    function getId($phoneNumber)
    {
        $this->db->select('id');
        $this->db->from('user');
        $this->db->where('mobile', $phoneNumber); 
        $row = $this->db->get()->row();

        $id = NULL;
        if(isset($row))
        {
            $id = $row->id;
        }

        return $id;
    }

    function updateAvatarUrl($id, $url)
    {
        $data = array(
           'avatar' => $url
        );
        $this->db->where('id', $id);
        $this->db->update('user', $data); 
    }

    function getPhoneNumber($userId)
    {
        $this->db->select('mobile');
        $this->db->where(array('userId' => $userId));
        $query = $this->db->get();

        $mobile = NULL;
        if(isset($row))
        {
            $mobile = $row->mobile;
        }

        return $mobile;
    }
}