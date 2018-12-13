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
 * 发货地址列表 
 */
class SendAddressModel extends BaseModel
{

	public static $id_d;	//运送编号

	public static $prov_d;	//省

	public static $addressDetail_d;	//详细地址

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//是否启用[0启用1不启用]

	public static $stockName_d;	//仓库名称

	public static $def_d;	//是否默认[0不默认1默认]

	public static $city_d;	//市

	public static $dist_d;	//县

	public static $storeId_d;	//店铺id

    private static $obj;
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
    /**
     * 添加数据 
     */
    public function addAddress(array $post)
    {
        if (!$this->isEmpty($post) || empty($post[static::$addressId_d])) {
            return false;
        }
        
        $post[static::$addressId_d] = static::flag($post, static::$addressId_d);
        return $this->add($post);
    }
    
    /**
     * 保存数据 
     */
    public function saveEedit(array $post)
    {
      
        if (!$this->isEmpty($post) || empty($post[static::$addressId_d])) {
            return false;
        }
        $post[static::$addressId_d] = static::flag($post, static::$addressId_d);
       
        return $this->save($post);
    }
    /**
     * {@inheritDoc}
     * @see \Think\Model::_before_insert()
     */
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
        return $data;
    }
    
    /**
     * 更新数据
     * {@inheritDoc}
     * @see \Think\Model::_before_update()
     */
    // protected function _before_update(& $data, $options)
    // {
    //
    //     $data[static::$updateTime_d] = time();
    //     return $data;
    // }
    
    
    /**
     * 获取发货仓库信息 
     * @param int $id 仓库编号
     * @return array
     */
    public function getStockDataById ($id)
    {
        if (($id = intval($id)) === 0) {
            return array();
        }
        
        $data = $this->field(static::$updateTime_d.','.static::$createTime_d, true)->find($id);
        
        return $data;
    }
    
    /**
     * 设置默认 
     */
    public function setDefault ($post)
    {
        if (!$this->isEmpty($post) || ($status = intval($post[static::$id_d])) === 0) {
            return array();
        }
        
        $this->startTrans();
        $status = $this->where(static::$id_d.' != "%s"', $post[static::$id_d])->save(array(
            static::$default_d => 0
        ));
        
        if ($status === false) {
            $this->rollback();
            return false;
        }
        
        $status = $this->save($_POST);
        
        if ($status === false) {
            $this->rollback();
            return false;
        }
        $this->commit();
        
        return $status;
    }
    //根据条件获取发货地址列表
    public function getAddressByWhere($where,$field=""){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->field($field)->where($where)->select();   
        if (empty($res)) {
            return array("status"=>"","message"=>"暂没有设置发货地区","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }
    //获取单条数据
    public function getAddressByID($where){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->field($field)->where($where)->find();   
        if (empty($res)) {
            return array("status"=>"","message"=>"暂没有设置发货地区","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }
    //修改仓库地址
    public function saveAddress($where,$post){
        if (empty($where)||empty($post)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $isExits = $this->editIsOtherExit(static::$stockName_d, $post[static::$stockName_d]);
        if ($isExits) {
            return array("status"=>"","message"=>'已存在该名称：【'.$post[static::$stockName_d].'】',"data"=>"");
        }
        $this->startTrans();
        
        $res = $this->where($where)->save($post);
        if (!$res) {
            $this->rollback();
            return array("status"=>"","message"=>"修改失败","data"=>"");
        }
        $this->commit();
        return array("status"=>1,"message"=>"修改成功","data"=>"");
    }
    //添加仓库地址
    public function addressAdd($post){
        if (empty($post)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $isExits = $this->editIsOtherExit(static::$stockName_d, $post[static::$stockName_d]);
        if ($isExits === false) {
            return array("status"=>"","message"=>'已存在该名称：【'.$post[static::$stockName_d].'】',"data"=>"");
        }
        $isAddress = $this->editIsOtherExit(static::$addressDetail_d, $post[static::$addressDetail_d]);
        if ($isAddress === false) {
            return array("status"=>"","message"=>'详细地址已存在：【'.$post[static::$addressDetail_d].'】',"data"=>"");
        }
        $this->startTrans();
        $res = $this->add($post);
        if (!$res) {
            $this->rollback();
            return array("status"=>"","message"=>"添加失败","data"=>"");
        }
        $this->commit();
        return array("status"=>1,"message"=>"添加成功","data"=>"");
    }
    //删除发货仓库
    public function delAddress($id){
        if (empty($id)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $where['id'] = $id;
        $res = $this->where($where)->delete();
        if (!$res) {
            return array("status"=>"","message"=>"删除失败","data"=>"");
        }
        return array("status"=>1,"message"=>"删除成功","data"=>"");
    }
    //获取仓库信息
    public function getSendAddressByData(array $data){
        if (empty($data)) {
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        foreach ($data as $key => $value) {
            $where['id'] = $value['stock_id'];
            $field = "stock_name";
            $res = $this->field($field)->where($where)->find();
            $data[$key]['stock_name'] = $res['stock_name'];
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
    //获取仓库信息
    public function getSendAddressByOne($data){
        if (empty($data)) {
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        
        $where['id'] = $data['stock_id'];
        $field = "stock_name";
        $res = $this->field($field)->where($where)->find();
        $data['stock_name'] = $res['stock_name'];
       
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
}