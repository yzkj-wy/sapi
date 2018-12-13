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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Model;

class StoreSellerModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//卖家编号

	public static $sellerName_d;	//卖家用户名

	public static $userId_d;	//用户编号

	public static $groupId_d;	//卖家组编号

	public static $storeId_d;	//店铺编号

	public static $isAdmin_d;	//是否管理员(0-不是 1-是)

	public static $sellerQuicklink_d;	//卖家快捷操作

	public static $lastLogin_time_d;	//最后登录时间

	public static $isClient_d;	//是否客户端用户 0-否 1-是

	public static $lastLogin_ip_d;	//最后登录ip

	public static $loginNum_d;	//登录次数

	public static $status_d;	//0正常1禁用

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间


	public static $password_d;	//登录密码

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
 
    //修改
    public function saveSeller($where,$date){ 
    	if (empty($where)||empty($date)) {   
        	return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $res = $this->where($where)->save($date); 
        if (!$res) {
        	return array("status"=>0,"message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>"");
    }
    //删除
    public function del($id){   
    	if (empty($id)) {   
        	return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $where['id'] =$id;
        $res = $this->where($where)->delete();
        if (!$res) {
        	return array("status"=>0,"message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>"");
    }
    //批量删除
    public function delAll($id){   
        if (empty($id)) {   
            return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $s_id =implode(',',$id);
        $where['id'] =array("IN",$s_id);
        $res = $this->where($where)->delete();
        if (!$res) {
            return array("status"=>0,"message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>"");
    }
    //获取账号列表
    public  function getAccountListByStoreId($where,$field=null,$order =null){
        if (empty($where)) {
        	return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $_GET['page'] = empty($_GET['page'])?0:$_GET['page'];
        $res = $this->field($field)->where($where)->order($order)->page($_GET['page'].',10')->select();

        $store_auth_group_model=M('store_auth_group');

        foreach($res as $k=>$v){
           $res[$k]['role']= $store_auth_group_model->field('title')->where(['id'=>$v['group_id']])->find()['title'];
        }

        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($res)) {
        	return array("status"=>0,"message"=>"暂时没有数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>array("list"=>$res,"count"=>$count,"totalPages"=>$totalPages,"page_size"=>10));
    }
    //添加账号
    public function addAccount($data){ 
        if (empty($data)) {
            return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $data['create_time'] = time();
        $res = $this->add($data);
        if (!$res) {
            return array("status"=>0,"message"=>"添加失败","data"=>"");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
    //获取角色列表
    public function getRoleListByStoreId($where,$field=null,$order =null,$page = 0){

        if (empty($where)) {
            return array("status"=>0,"message"=>"参数错误","data"=>"");
        }

        $res = M('store_auth_group')->field($field)->where($where)->order($order)->page($page.',10')->select();


        $count      =  M('store_auth_group')->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($res)) {
            return array("status"=>0,"message"=>"暂时没有数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>array("list"=>$res,"count"=>$count,"totalPages"=>$totalPages,"page_size"=>10));
    }
    //获取单条
    public function getRoleListById($where,$field=null,$order =null){
        if (empty($where)) {
            return array("status"=>0,"message"=>"参数错误","data"=>"");
        }

        $res = M('store_auth_group')->field($field)->where($where)->order($order)->find();

       return $res;
    }



//添加角色
    public function addRoles($data){
        if(empty($data)){
            return array("status"=>0,"message"=>"参数错误","data"=>"");
        }
        $data['store_id']=session('store_id');
        $data['create_time'] = time();
        $data['rules']=implode(',',$data['roles']);
        $res = M('store_auth_group')->add($data);
        if (!$res) {
            return array("status"=>0,"message"=>"添加失败","data"=>"");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>$res);


    }



}