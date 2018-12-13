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
namespace Common\Model;

class StoreClassModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//主键编号

	public static $scName_d;	//分类名称

	public static $scBail_d;	//保证金数额

	public static $scSort_d;	//排序

	public static $status_d;	//是否开启 【0关闭 1开启】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间


    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    } 
    
    /**
     * 重写父类方法自动添加时间
     */
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
        
        return $data;
    }

    /** 
     * 重写父类方法
     */
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
        
        return $data;
    }
    //查询店铺分类
    public function getStoreClass(){
        $where['status'] = 1; 
        $field = "id,sc_name,sc_bail";
        $res = $this->field($field)->where($where)->order('sc_sort')->select();
        if (empty($res)) {
            return array('status'=>0,"mes"=>"失败!");
        }
        return array('status'=>1,"mes"=>"成功","data"=>$res);
    }
    //根据条件查询
    public function getOneClassByWhere($where,$field){
        if (empty($where)) {
            return false;
        }
        $res = $this->field($field)->where($where)->find();
        return $res;
    }
}