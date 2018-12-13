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
use Common\Tool\Tool;
use Common\TraitClass\callBackClass;
use Common\TraitClass\MethodTrait;
use Common\Tool\Extend\CombineArray;
use Common\Tool\Extend\ArrayChildren;

/**
 * 订单模型 
 * @author 王强
 * @version 1.0.1
 */
class ServiceModel extends BaseModel
{

    



	public static $id_d;	//

	public static $servicetypeId_d;	//客服类型id

	public static $status_d;	//是否显示  1为显示  0不显示

	
    public static $sort_d;
	public static $storeId_d;	//店铺id

	public static $name_d;	//客服名称

	public static $isMain_d;	//是否主客服 0不是 1是

	public static $tool_d;	//客服工具

	public static $account_d;	//客服账号


    private static $obj ;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = ! (static::$obj instanceof $name) ? new static() : static::$obj;
    }
    //客服管理列表
    public function manageList(){

        return $this->where(['store_id'=>session('store_id')])->select();

}
    //修改ById
    public function upd($where,$data){
      return $this->where($where)->data($data)->save();
    }


    //是否显示
    public function IsShow($data){
        $where['id']=$data['id'];
        if($data['isShow']==1){
            $info['status']=1;
           return $this->upd($where,$info);
        }else{
            $info['status']=0;
            return $this->upd($where,$info);
        }

    }
    //是否主客服
    public function IsMainService($data){
        $where['id']=$data['id'];

        if($data['isMain']==1){
           $mainservice=$this->where(['store_id'=>session('store_id'), 'is_main'=>1])->find();
            $info['is_main']=1;
           if(!empty($mainservice)){
               return false;
           }else{
             return $this->upd($where,$info);
           }

        }else{
            $info['is_main']=0;
            return $this->upd($where,$info);
        }
    }
    //添加客服
    public function addService(array $add){
        if (empty($add)) {
            return array('status'=>0,"mes"=>"数据出错!");
        }
        if(!empty($add['service_id'])){
            $res = $this->where(['id'=>$add['service_id']])->save($add);
        }else{
            $add['store_id']=session('store_id');
            
            $res = $this->add($add);
        }

        if (!$res) {
            return array('status'=>0,"mes"=>"操作失败!");
        }
        return array('status'=>1,'data'=>$res,"mes"=>"操作成功");
    }
}