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
 * 包邮条件表 
 */
class FreightConditionModel extends BaseModel
{
    /**
     * @var FreightConditionModel
     */
    private static $obj;

	public static $id_d;	//id

	public static $freightId_d;	//运费主表Id

	public static $mailArea_num_d;	//包邮件数，默认0

	public static $mailArea_wieght_d;	//包邮重量

	public static $mailArea_volume_d;	//包邮体积

	public static $mailArea_monery_d;	//包邮金额

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    /**
     * 获取类的实例
     * @return \Admin\Model\FreightConditionModel
     */
    public static function getInitnation()
    {
        
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
    /**
     * 添加前操作
     * {@inheritDoc}
     * @see \Think\Model::_before_insert()
     */
    protected function _before_insert(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
        $data[static::$createTime_d] = time();
        return $data;
    }
    /**
     * 更新前操作
     * {@inheritDoc}
     * @see \Think\Model::_before_update()
     */
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
    
        return $data;
    }
    //删除数据
    public function condition_delete($freight_id){
        if (empty($freight_id)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $condition_id = $this->where(['freight_id'=>$freight_id])->getField('id');
        if (!empty($condition_id)) {
            $res = $this->where(['freight_id'=>$freight_id])->delete();
            if (!$res) {
                 M()->rollback();
                return array("status"=>"","message"=>"删除失败","data"=>"");
            }
            $res = D('FreightArea')->area_del($condition_id);
            return array("status"=>1,"message"=>"删除成功","data"=>"");
        }else{
            return array("status"=>1,"message"=>"删除成功","data"=>"");
        }  
    }
    //获取数据
    public function getFreightOne($where,$field){
        if (empty($where)||empty($field)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->field($field)->where($where)->find();
        if (empty($res)) {
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }else{
            $res['area'] = D('FreightArea')->getAreaByWhere(['freight_id'=>$res['id']]);
            return array("status"=>1,"message"=>"获取成功","data"=>$res);
        }
    }
    //更新数据
    public function saveFreight($where,$data){
        if (empty($where)||empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        M()->startTrans();
        $res = $this->where($where)->save($data);
        if (!$res) {
             M()->rollback();
            return array("status"=>"","message"=>"更新失败","data"=>"");
        }
        return array("status"=>1,"message"=>"更新成功","data"=>"");
    }
    //添加数据
    public function addFreight($data){
        if (empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        M()->startTrans();
        $res = $this->add($data);
        if (!$res) {
             M()->rollback();
            return array("status"=>"","message"=>"更新失败","data"=>"");
        }
        return array("status"=>1,"message"=>"更新成功","data"=>$res);
    }
}