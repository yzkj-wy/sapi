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
 * 发货模地址型
 * @author Administrator
 * @version 1.0.0
 */
class FreightSendModel extends BaseModel
{
    /**
     * 类的实例
     * @var FreightSendModel
     */
    private static $obj;

	public static $freightId_d;	//模板编号

	public static $mailArea_d;	//地区编号

    /**
     * 获取类的实例
     * @return \Admin\Model\FreightSendModel
     */
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    //添加数据
    public function addFreightSend($data,$freight_id){
        if (empty($data)||empty($freight_id)) {
            return array("status"=>"","message"=>"参数错误");
        }
        foreach ($data as $key => $value) {
            $date[$key]['mail_area'] = $value;
            $date[$key]['freight_id'] = $freight_id;
        }
        $res = $this->addAll($date);
        if (!$res) {
            M()->rollback();
            return array("status"=>"","message"=>"添加失败");
        }else{
            M()->commit();
            return array("status"=>1,"message"=>"添加成功"); 
        }
    }
    //删除数据
    public function delFreightSend($where){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误");
        }
        $result = $this->where($where)->find();
        if (!empty($result)) {
            $res = $this->where($where)->delete();
            if (!$res) {
                M()->rollback();
                return array("status"=>"","message"=>"删除失败");
            }
            M()->commit();
            return array("status"=>1,"message"=>"删除成功");
        }else{
            M()->commit();
            return array("status"=>1,"message"=>"删除成功");
        }
    }
    //修改数据
    public function saveFreightSend($data,$freight_id){
        if (empty($data)||empty($freight_id)) {
            return array("status"=>"","message"=>"参数错误");
        }
        $where['freight_id'] = $freight_id;
        $send = $this->where($where)->find();
        if (!empty($send)) {
            $result = $this->where($where)->delete();
            if (!$result) {
                M()->rollback();
                return array("status"=>"","message"=>"修改失败");
            }
        }
        foreach ($data as $key => $value) {
            $date[$key]['mail_area'] = $value;
            $date[$key]['freight_id'] = $freight_id;
        }
        $res = $this->addAll($date);
        if (!$res) {
            M()->rollback();
            return array("status"=>"","message"=>"修改失败");
        }else{
            M()->commit();
            return array("status"=>1,"message"=>"修改成功"); 
        }
    }
    //获取数据
    public function getSendByData($data){
        if (empty($data)) {
            return "";
        }
        foreach ($data as $key => $value) {
            $where['freight_id'] = $value['id'];
            $res = $this->where($where)->select();
            foreach ($res as $k => $v) {
                $area['id'] = $v['mail_area'];
                $res[$k]['id'] = $v['mail_area'];
                $res[$k]['name'] = M('Region')->where($area)->getField("name");
            }
            $data[$key]['area'] = $res;
        }
        return $data;
    } 
     //获取数据
    public function getSendByDataOne($data){
        if (empty($data)) {
            return "";
        }
        
        $where['freight_id'] = $data['id'];
        $res = $this->where($where)->select();
        foreach ($res as $k => $v) {
            $area['id'] = $v['mail_area'];
            $res[$k]['id'] = $v['mail_area'];
            $res[$k]['name'] = M('Region')->where($area)->getField("name");
        }
        $data['area'] = $res;
        
        return $data;
    } 
}