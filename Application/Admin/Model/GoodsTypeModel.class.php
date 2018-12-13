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
 * 商品类型模型
 * @author 王强<opjklu@126.com>
 * @version 1.0.0
 */
class GoodsTypeModel extends BaseModel
{
    private static $obj;
    //主键
    public static $id_d;
    //商品类型名称
    public static $name_d;
    //创建时间
    public static $createTime_d;
    //最后一次编辑时间
    public static $updateTime_d;
    public static $storeId_d;	//商家编号
    //显示状态
    public static $status_d;
    protected $patchValidate = true;
    protected $_validate = [
        ['name','require','商品类型不能为空'],

    ];


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
    protected function _before_insert(&$data,$options)
    {
        $data['create_time'] = time();
        $data['update_time'] = time();
        return $data;
    }
   
   /**
    *  更新前操作
    * {@inheritDoc}
    * @see \Think\Model::_before_update()
    */
    protected function _before_update(&$data, $options)
    {

        $data[static::$updateTime_d] = time();
        return $data;
    }

    /**
     * 获得所有的商品类型[显示的]
     * @return mixed
     */
    public function getList(){
        $rows = $this->where(static::$status_d.' = 1')->getField(static::$id_d.','.static::$name_d);
        return $rows;
    }
    
    /**
     * 获取商品类型并缓存
     * @return array
     */
    public function getType ()
    {
        $data = S('typeDataCache');
        
        if (empty($data)) {
            $data = $this->where(['store_id' => session('store_id')])->getField(static::$id_d.','.static::$name_d);
        }
        
        if (empty($data)) {
            return array();
        }
        
        S('typeDataCache', $data, 10);
        
        return $data;
    }
    
    /**
     * 组合数据
     * @param array $data  其他模型 读取的数据
     * @param string $split 以哪个字段拼接相关联的字段
     * @return array
     */
    public function getDataByGoodsAttribute( array $data, $split)
    {

        if (empty($split)) {
            return array();
        }

        return $this->getDataByOtherModel($data, $split, [
            static::$id_d,
            static::$name_d
        ], static::$id_d);
    }

    /**
     * 判断是否存在分类
     */
    public function isExistType($type_id)
    {
        $result = $this->where(static::$id_d . ' = %d ' , $type_id)->find();
        return empty($result) ? false : true ;
    }
}