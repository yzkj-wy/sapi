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
use Common\Logic\AbstractGetDataLogic;
/**
 * 运送方式表 
 */
class FreightModeModel extends BaseModel
{
    /**
     * 类的实例
     * @var FreightModeModel
     */
    private static $obj;

	public static $id_d;	//ID

	public static $freightId_d;	//运费模板编号

	public static $firstThing_d;	//首件

	public static $firstWeight_d;	//首重

	public static $fristVolum_d;	//首体积

	public static $fristMoney_d;	//首运费【起步价】

	public static $continuedHeavy_d;	//续重

	public static $continuedVolum_d;	//续体积

	public static $continuedMoney_d;	//续费

	public static $carryWay_d;	//运送方式编号

	public static $continuedThing_d;	//续件
    public static $storeId_d;   //店铺id

    /**
     * 获取类的实例
     *
     */
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
    /**
     * 获取运送方式编号 
     * @param int $limit 读几条
     * @param array $where 搜索条件
     */
    public function getData($where,$field,$limit,$page){
        if (empty($where)||empty($field)) {
            return array("status"=>"","message"=>"参数错误");
        }
        $res = $this->field($field)->where($where)->page($page.",".$limit)->select();
        $count = $this->where($where)->count();
        $page  = ceil($count/$limit);
        if (empty($res)) {
            return array("status"=>"","message"=>"暂无数据");
        }
        $Freights = D('Freights')->getFreightTitle($res);
        $Express = D('Express')->getExpressName($Freights); 
        $area = D("FreightSend")->getSendByData($Express);
        return array("status"=>1,"message"=>"获取成功","data"=>array("data"=>$area,"page"=>$page));
    }
    //获取单挑数据
    public function getDataOne($where,$field){
        if (empty($where)||empty($field)) {
            return array("status"=>"","message"=>"参数错误");
        }
        $res = $this->field($field)->where($where)->find();
        if (empty($res)) {
            return array("status"=>"","message"=>"暂无数据");
        }
        $Freights = D('Freights')->getFreightTitleOne($res);
        $Express = D('Express')->getExpressNameOne($Freights); 
        $area = D("FreightSend")->getSendByDataOne($Express);
        return array("status"=>1,"message"=>"获取成功","data"=>$area);
    }
    /**
     * 根据订单信息 获取运送方式 及其 运费 金额 
     * @param array $data 订单数组
     * @param BaseModel $model 订单对象
     * @return array
     */
    public function getShippingMode(array $data, BaseModel $model)
    {
        if (!$this->isEmpty($data) || !($model instanceof BaseModel)) {
            return array();
        }
        
        $shippingData = S('SHIPPING_CACHE_DATA');
        
        if (empty($shippingData)) {
            $shippingData = $this->getDataByOtherModel($data, $model::$freightId_d, [
                static::$id_d. static::DBAS .$model::$freightId_d,
                static::$carryWay_d,
            ], static::$id_d);
            
            if (empty($shippingData)) {
                return array();
            }
            S('SHIPPING_CACHE_DATA', $shippingData, 6);
        }
        return $shippingData;
    }
    //添加数据
    public function addFreightMode($data){
        if (empty($data)) {
            return array("status"=>"","message"=>"参数错误");
        }
        $result = $this->field('freight_id,store_id')
            ->where(['freight_id'=>$data['freight_id'],'store_id'=>$_SESSION['store_id']])
            ->find();
        if(!empty($result)){
            return array("status"=>"","message"=>"数据已存在，请勿重复添加");
        }
        M()->startTrans();
        $res = $this->add($data);
        if (!$res) {
            M()->rollback();
            return array("status"=>"","message"=>"添加失败");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>$res);
    }
    //修改数据
    public function saveFreightMode($where,$data){
        if (empty($where)||empty($data)) {
            return array("status"=>"","message"=>"参数错误");
        }
        M()->startTrans();
        $res = $this->where($where)->save($data);
        if ($res === false) {
            M()->rollback();
            return array("status"=>"","message"=>"修改失败");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>$res);
    }
    //删除数据
    public function delFreightMode($where){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误");
        }
        M()->startTrans();
        $res = $this->where($where)->delete();
        if (!$res) {
            M()->rollback();
            return array("status"=>"","message"=>"删除失败");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>"");
    }
}