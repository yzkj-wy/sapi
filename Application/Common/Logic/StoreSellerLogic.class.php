<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
declare(strict_types=1);
namespace Common\Logic;
use Common\Model\StoreSellerModel;
use Common\Model\StoreModel;
use Admin\Model\UserModel;
use Think\SessionGet;
/**
 * 入驻申请成功逻辑处理
 * @author 王强
 * @version 1.0
 */
class StoreSellerLogic extends AbstractGetDataLogic
{
    //其他表搜索条件
    private $otherWhere = [];
    
    /**
     * 
     * @var integer
     */
    private $instertId = 0;
    
    /**
     * 获取插入的id
     * @return number
     */
    public function getInstertId()
    {
    	return $this->instertId;
    }
    
    /**
     * @param field_type $otherWhere
     */
    public function setAssociationWhere($otherWhere)
    {
        $this->otherWhere = $otherWhere;
    }
   
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = [], $split = '')
    {
        $this->data = $args;
        
        $this->splitKey = $split;
        
        $this->modelObj = new StoreSellerModel();
    }
    

    /**
     * 添加数据
     */
    public function getResult()
    {
       $data = $this->data;
       
       if (empty($data)) {
           $this->modelObj->rollback();
           return false;
       }
       
       $sellerData = [];

       $sellerData[StoreSellerModel::$sellerName_d] = $data['account'];
       
       $sellerData[StoreSellerModel::$storeId_d] = $data[StoreModel::$id_d];
       
       $sellerData[StoreSellerModel::$userId_d] = $data[StoreModel::$userId_d];
       
       $sellerData[StoreSellerModel::$isAdmin_d] = 1;
       
       $sellerData[StoreSellerModel::$groupId_d] = 0;
       $status = $this->modelObj->add($sellerData);
       
       if (!$this->modelObj->traceStation($status)) {
           return false;
       }
       return $status;
    }
    
    /**
     * 获取商家账号
     * @return array
     */
    public function getSellerDataByStore()
    {
        $data = $this->data;
        
        if (empty($data)) {
            return [];
        }
        
        $this->modelObj->setFindWhere(' and '. StoreSellerModel::$isAdmin_d.' = 1');
        
        $field = [
            StoreSellerModel::$sellerName_d,
            
            StoreSellerModel::$storeId_d.StoreSellerModel::DBAS.StoreSellerModel::$id_d
        ];
        
        $sellerData = $this->modelObj->getDataByOtherModel($data, $this->splitKey, $field, StoreSellerModel::$storeId_d);
        
        return $sellerData;
    }
    
    
    /**
     * 获取模型类名
     */
    public function getModelClassName()
    {
        return StoreSellerModel::class;
    }
    
    //获取 店铺数据
    public function getUserBySellerName() :array
    {
    	$seller = [];
    	
    	$seller['seller_name'] = $this->data['seller_name'];
    	$field="id,user_id,store_id,login_num,password,last_login_time,last_login_ip, group_id";
    	
    	$res = $this->modelObj->field($field)->where($seller)->find();
    	if (empty($res)) {
    		return [];
    	}
    	return $res;
    }
    
    //登录 验证

    public function loginCheck(){
        $data = $this->data;
        //根据账号 获取userID
      
        $user = $this->getUserBySellerName();
        if (count($user) === 0) {
            return array("status"=>"","message"=>'没有找到该店铺数据',"data"=>'');
        }
        if (md5($data['password'])!= $user['password']) {
            return array("status"=>"","message"=>"密码错误","data"=>"");
        }else{
            $user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
            $user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
            SessionGet::getInstance('store_id', $user['store_id'])->set();
            SessionGet::getInstance('user_id', $user['user_id'])->set();
            SessionGet::getInstance('store_seller_id', $user['id'])->set();
            if (empty($user['last_login_time'])) {
                SessionGet::getInstance('last_login_time',time())->set();
            }else{
                SessionGet::getInstance('last_login_time',$user['last_login_time'])->set();
            }
            if (empty($user['last_login_ip'])) {
                SessionGet::getInstance('last_login_ip',$user_IP)->set();
            }else{
                SessionGet::getInstance('last_login_ip',$user['last_login_ip'])->set();
            }
            //更新最后登录时间
            $date['last_login_time'] = time();
            $date['login_num'] = $user['login_num']+1;
            $date['last_login_ip'] = $user_IP;
            $where['id'] = $user['id'];
            $login_time = $this->modelObj->saveSeller($where,$date);
            if ($login_time['status'] == 0) {
                return array("status"=>"","message"=>$login_time['message'],"data"=>"");
            }
            SessionGet::getInstance('admin_id',$user['id'])->set();
            
            return array("status"=>1,"message"=>"登录成功!","data"=>array('token'=>session_id()));
        } 
    }
    //获取店铺账号列表\
    public function getAccountList(){
        $store_id = SessionGet::getInstance('store_id')->get();
        $where['store_id'] =$store_id;
        $field  = "id,user_id,seller_name,group_id,last_login_time,last_login_ip,login_num,create_time,status";
        $order = "create_time DESC"; 
        $res  = $this->modelObj->getAccountListByStoreId($where,$field,$order); 
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>"");
        }
        return array("status"=>1,"message"=>$res['message'],"data"=>$res['data']);
    }
    //角色列表
    function getRoleList(){
        $post = $this->data;
        $store_id = SessionGet::getInstance('store_id')->get();
        $where['store_id']=$store_id;
        $field='id,title,explain'; 
        $order = "create_time DESC";
        $res  = $this->modelObj->getRoleListByStoreId($where,$field,$order,$post['page']);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>"");
        }
        return array("status"=>1,"message"=>$res['message'],"data"=>$res['data']);

    }
    //获取角色列表不分页
    function getRole(){
        $where['store_id']=session('store_id');
        $field='id,title'; 
        $order = "create_time DESC";
        $res  = M('store_auth_group')->field($field)->where($where)->order($order)->select();
        if (empty($res)) {
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }
    //获取单条
    public function getRoleById(){
        $where['id']=$this->data['id'];
        $field='id,title,explain,rules';
        $order = "create_time DESC";
        $res  = $this->modelObj->getRoleListById($where,$field,$order);
        if (!$res) {
            return array("status"=>"","message"=>"获取失败","data"=>"");
        }
        if($res['rules']){
            $arr=explode(',',$res['rules']);
            $res['role']=$this->getPowerList1($arr);
            return array("status"=>1,"message"=>"成功","data"=>$res);
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }

    public function getPowerList1($arr){
        $cats = M('store_auth_menu')->where(['status'=>1])->field('id,remark,fid')->select();

        foreach($cats as $key=>$val) {
            if(in_array($val['id'],$arr)){
                $cats[$key]['choice'] = 1;
            }
            $tmp[$val['id']] = $cats[$key];
        }
        $res=$this->generateTree($tmp);
        return $res;
    }

    //添加角色
    public function addRole(){
        $data=$this->data;
        $res  = $this->modelObj->addRoles($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>"");
        }
        return array("status"=>1,"message"=>$res['message'],"data"=>$res['data']);

    }
    //修改角色
    public function updRole(){
        $id=$this->data['id'];
        $data['title']=$this->data['title'];
        $data['explain']=$this->data['explain'];
        $data['rules']=implode(',',$this->data['roles']);
        $res=M('store_auth_group')->where(['id'=>$id])->save($data);

        if (!$res) {
            return array("status"=>0,"message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>$res);
    }
//角色删除
    public function delRole(){
        $id=$this->data['id'];

        $res=M('store_auth_group')->where(['id'=>$id])->delete();

        if (!$res) {
            return array("status"=>0,"message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>$res);
}
//权限列表
    public function getPowerList(){
        $cats = M('store_auth_menu')->where(['status'=>1])->field('id,remark,fid')->select();
        foreach($cats as $key=>$val) {
            $tmp[$val['id']] = $cats[$key];
        }
        $res=$this->generateTree($tmp);
        if (!$res) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);

}

    public function generateTree($items){
        $tree = array();
        foreach($items as $item){
            if(isset($items[$item['fid']])){
                $items[$item['fid']]['son'][] = &$items[$item['id']];
            }else{
                $tree[] = &$items[$item['id']];
            }
        }
        return $tree;
    }




    //删除管理员
    public function delAccount(){
        $data = $this->data;
        $res  = $this->modelObj->del($data['id']);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>""); 
        }
        return array("status"=>1,"message"=>$res['message'],"data"=>"");
    }
    //批量删除管理员
    public function delAllAccount(){
        $data = $this->data;
        $res  = $this->modelObj->delAll($data['id']);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>""); 
        }
        return array("status"=>1,"message"=>$res['message'],"data"=>"");
    }
    //添加管理员
    public function addAccount(){ 
        $data = $this->data;
        $userModel = new UserModel();
        $name['user_name'] = $data['seller_name'];
        $field = "id";
        $res = $userModel->getUserByWhere($name,$field);
        if (!empty($res)) {
            return array("status"=>"","message"=>'登录名已存在',"data"=>"");
        }
        $mobile['mobile'] =  $data['mobile'];
        $res = $userModel->getUserByWhere($mobile,$field);
        if (!empty($res)) {
            $data['user_id'] = $res['id'];
        }else{
            $data['user_name'] = $data['seller_name'];
            $res = $userModel->addAccount($data);
            if ($res === false) {
                return array("status"=>"","message"=>'添加失败',"data"=>"");
            }
            $data['user_id'] = $res;
        } 
        $data['store_id'] = $_SESSION['store_id']; 
        $data['password'] = MD5($data['password']);
        $data['is_admin'] = 1;
        
        $this->modelObj->startTrans();
        
        $res  = $this->modelObj->addAccount($data);
        
        if ($res['status'] == 0){
            return array("status"=>"","message"=>$res['message'],"data"=>"");
        }
        
        $this->instertId = $res['data'];
        
        return array("status"=>1,"message"=>$res['message'],"data"=>"");
    }
    //获取账号信息
    public function getAccountInfo(){
        $data = $this->data;
        $seller['id'] = $data['id'];
        $field="id,seller_name,user_id,status,group_id";
        $account = $this->modelObj->field($field)->where($seller)->find();
        if ($account['status'] == 0) {
            return array("status"=>"","message"=>$account['message'],"data"=>$account['data']);
        }else{
            $where['id'] = $account['data']['user_id'];
        }
        $userField = "id,mobile,email";
        $userModel = new UserModel();
        $user = $userModel->getUserByWhere($where,$userField);
        if (empty($user)) {
            return array("status"=>"","message"=>'账号不存在',"data"=>"");
        }
        $date = array(
            "seller_name"=>$account['data']['seller_name'],
            "status"=>$account['data']['status'],
            "mobile"=>$user['mobile'],
            "email"=>$user['email'],
            "group_id"=>$account['data']['group_id'],
        );
        return array("status"=>1,"message"=>'获取成功',"data"=>$date);
    }
    //修改管理员
    public function saveAccount(){
        $userModel = new UserModel();
        $data = $this->data;
        $field = "id";
        $mobile['mobile'] =  $data['mobile'];
        $res = $userModel->getUserByWhere($mobile,$field);
        if (!empty($res)) {
            $data['user_id'] = $res['id'];
        }else{
            $res = $userModel->addAccount($data);
            if ($res === false) {
                return array("status"=>"","message"=>'修改失败',"data"=>"");
            }
            $data['user_id'] = $res;
        }  
        $data['password'] = MD5($data['password']); 
        $where['id'] = $data['id'];    
        $res  = $this->modelObj->saveSeller($where,$data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['message'],"data"=>$res['data']);
        }else{
            return array("status"=>1,"message"=>$res['message'],"data"=>$res['data']);
        }
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreSellerModel::$sellerName_d => [
                'required' => '请输入'.$comment[StoreSellerModel::$sellerName_d],
            ],
            StoreSellerModel::$password_d => [
                'required' => '请输入'.$comment[StoreSellerModel::$password_d],
            ],
        	StoreSellerModel::$groupId_d => [
        		'number' => '分组必须是数字',
        	]
        ];
        
        return $message;
    }
    
    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            StoreSellerModel::$sellerName_d => [
                'required' => true,
            ],
            StoreSellerModel::$password_d => [
                'required' => true,
            ],
        ];
        return $validate;
    }
    //退出登录
    public function getExitLogon(){
        SessionGet::getInstance('')->destroy();
        return array("status"=>1,"message"=>"退出成功","data"=>"");
    }
    
    /**
     * 验证商家账号
     * @return array
     */
    public function getMessageCheckValidateByStore() :array
    {
    	return [
    		StoreSellerModel::$sellerName_d => [
    			'required' => '店铺账号必填',
    			'specialCharFilter' => '店铺名称不能有特殊字符'
    		],
    	];
    }
    
    /**
     * 验证商家账号
     * @return array
     */
    public function getMessageCheckValidateByStorePassword() :array
    {
    	return [
    		StoreSellerModel::$password_d => [
    			'required' => '店铺账号必填',
    		],
    		'password_again' => [
    			'required' => '店铺账号必填',
    		],
    		'code' => [
    			'number' => '验证码必须是数字'
    		]
    	];
    }
    
    /**
     * 修改密码
     */
    public function changePwd() :bool
    {
    	if (!isset($_SESSION['rand_numer']) || $_SESSION['rand_numer'] != $this->data['code']) {
    		$this->errorMessage = '验证码不正确或已过期';
    		return false;
    	}
    	
    	if ($this->data['password_again'] !== $this->data[StoreSellerModel::$password_d]) {
    		$this->errorMessage = '两次密码输入不一致';
    		return false;
    	}
    	
    	$status = $this->saveData();
    	if ($status === false) {
    		$this->errorMessage = '修改密码失败';
    		return false;
    	}
    	
    	unset($_SESSION['rand_numer'], $_SESSION['seller_name_by_id']);
    	
    	return $status;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
     */
    protected function getParseResultBySave() :array
    {
    	$data = [
    		StoreSellerModel::$id_d => $_SESSION['seller_name_by_id'],
    		StoreSellerModel::$password_d => md5($this->data[StoreSellerModel::$password_d]),
    		StoreSellerModel::$updateTime_d => time()
    	];
    	return $data;
    }
    
    //获取 店铺数据
    public function getIdBySellerName() :array
    {
    	$seller = [];
    	
    	$seller['seller_name'] = $this->data['seller_name'];
    	$field="id";
    	
    	$res = $this->modelObj->field($field)->where($seller)->find();
    	if (empty($res)) {
    		return [];
    	}
    	return $res;
    }
    
    /**
     * 验证店铺账号是否存在
     */
    public function checIsExistBySellerName() :bool
    {
    	$data = $this->getIdBySellerName();
    	if (count($data) === 0) {
    		$this->errorMessage = '没有找到该用户';
    		return false;
    	}
    	
    	$_SESSION['seller_name_by_id'] = $data['id'];
    	
    	return true;
    }
}