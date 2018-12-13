<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\BaseModel;


/**
 * 订单模型 
 * @author 王强
 * @version 1.0.1
 */
class ServiceTypeModel extends BaseModel
{


	public static $id_d;	//主键id

	public static $name_d;	//客户类型名称

	public static $status_d;	//是否启用 0不启用  1启用

	public static $sort_d;	//排序

	public static $storeId_d;	//店铺id

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !(static::$obj instanceof $class) ? new static() : static::$obj;

    }
    //客服类型列表
    public function _getTypeList(){
     return  $this->where(['store_id'=>session('store_id')])->select();

    }
    //添加客服类型
    public function addType(array $add){
        if (empty($add)) {
            return array('status'=>0,"mes"=>"数据出错!");
        }

        if(!empty($add['type_id'])){
        	
        	$add['update_time'] = time();
        	
            $res = $this->where(['id'=>$add['type_id']])->data($add)->save();
        }else{
            $add['store_id']=session('store_id');
            
            $add['create_time'] = $time = time();
            
            $add['update_time'] = $time;
            
            $res =$this->add($add);
        }

        if ($res) {
            return array('status'=>1,'data'=>$res,"mes"=>"添加成功");
        }else{
            return array('status'=>0,"mes"=>"添加失败!");
        }

    }
    //获取客户类型名称byid
    public function getTypeNameById($id){

      return  $this->where(['id'=>$id])->find();
    }

    //修改by id
    public function upd($where,$data){
      return  $this->where($where)->data($data)->save();
    }

    //是否启用
    public function isUse($data){
        $where['id']=$data['id'];
        if($data['isUse']==1){
            $info['status']=1;
          return  $this->upd($where,$info);
        }else{
            $info['status']=0;
           return $this->upd($where,$info);
        }

    }

}