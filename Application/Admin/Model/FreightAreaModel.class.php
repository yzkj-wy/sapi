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
use Common\Model\RegionModel;
/**
 * 包邮地区模型
 */
class FreightAreaModel extends BaseModel
{
    /**
     * @var FreightAreaModel
     */
    private static $obj;

	public static $freightId_d;	//包邮条件编号

	public static $mailArea_d;	//地区编号

    /**
     * 获取类的实例
     * @return \Admin\Model\FreightAreaModel
     */
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    //删除数据
    public function area_del($condition_id){
        if (empty($condition_id)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $id = $this->where(['freight_id'=>$condition_id])->find();
        if (!empty($id)) {
            $res = $this->where(['freight_id'=>$condition_id])->delete();
            if (!$res) {
                M()->rollback();
                return array("status"=>"","message"=>"删除失败","data"=>"");
            }
            return array("status"=>1,"message"=>"删除成功","data"=>"");
        }else{
            return array("status"=>1,"message"=>"删除成功","data"=>"");
        }
        
    }
    //根据条件查询数据
    public function getAreaByWhere($where){
        if (empty($where)) {
           return "";
        }
        $res = $this->field('freight_id,mail_area')->where($where)->select();
        if (empty($res)) {
            return "";
        }else{
            $area = D('Region')->getRegionByFreightArea($res);             
        }
        return $area;
    }
    //更新数据
    public function saveArea($where,$data){
        if (empty($where)||empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->area_del($where['freight_id']);
        if ($res['status'] == 1) {
            foreach ($data as $key => $value) {
                $date[$key]['freight_id'] = $where['freight_id'];
                $date[$key]['mail_area'] = $value;
            }
            $result = $this->addAll($date);
            if (!$result) {
                 M()->rollback();
                return array("status"=>"","message"=>"提交失败","data"=>"");
            }
             M()->commit();
            return array("status"=>"","message"=>"提交成功","data"=>"");
        }else{
            M()->rollback();
            return $res;
        }
    }
}