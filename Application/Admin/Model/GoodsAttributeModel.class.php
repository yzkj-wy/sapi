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
use Common\Model\IsExitsModel;
use Think\AjaxPage;

class GoodsAttributeModel extends BaseModel implements IsExitsModel
{
     const SHOW  =  1;
     const Close = 1;
    
	public static $id_d;	//属性id

	public static $attrName_d;	//属性名称

	public static $classOne_d;	//一级分类

	public static $classTwo_d;	//二级分类

	public static $classThree_d;	//三级分类

	public static $attrIndex_d;	//0不需要检索 1关键字检索

	public static $attrType_d;	//0唯一属性 1单选属性 2复选属性

	public static $inputType_d;	// 0 手工录入 1从列表中选择 2多行文本框

	public static $attrValues_d;	//可选值列表

	public static $orderSort_d;	//属性排序

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    
     public static function getInitnation()
     {
         $name = __CLASS__;
         return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
     }
     
     
     /**
      * 重写父类方法
      */
     protected  function _before_insert(& $data, $options)
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
         
         $data[static::$updateTime_d] = time();
          
         return $data;
     }
     
     /**
      * 获取属性
      * @param array $data  属性数组
      */
     public function parseAttribute()
     {
         $attrData = $this->where(static::$status_d .' = '.static::SHOW)->getField(static::$id_d. ',' .static::$attribute_d);
         
         return $attrData;
     }
    
     /**
      * @param array $id
      * @param array $where
      * @param int $number
      * @return boolean
      */
     private function parseId(array $id, array $where, $number)
     {
         if (!is_numeric($number))
         {
             return false;
         }
         
         if (empty($id)) {
             return  parent::delete($where);
         } else {
             $id = Tool::characterJoin($id, static::$id_d).','.'"'.$number.'"';
             $id  = str_replace('"', null, $id);
            
             $where['where'][static::$id_d] = array('in', $id);
             return empty($id) ? false : parent::delete($where);
         }
     }

    /**
     * {@inheritDoc}
     * @see \Common\Model\IsExitsModel::IsExits()
     */
    public function IsExits($post)
    {
        // TODO Auto-generated method stub
        
        if (!$this->isEmpty($post)) {
            return true;
        }
        
        return $this->where(static::$attrName_d.'= "%s"', $post[static::$attrName_d])->getField(static::$id_d);
     
    }
    
    /**
     * 根据商品类型编号 获取商品属性数据【新方式以后再用】 
     */
    public function getAttributeByTypeId ($id)
    {
        if (($id = intval($id)) === 0) {
            return array();
        }
        
        $data = $this->getAttributeAndCache();
        if (empty($data)) {
            return array();
        }
        
        foreach ($data as $key => $value) {
            if ($id === (int)$value[static::$typeId_d]) {
                continue;
            }
            unset($data[$key]);
        }
        return $data;
    }
    
    /**
     * 获取全部属性 并缓存 
     */
    protected function getAttributeAndCache ()
    {
        $data = S('GOODS_ATTRIBUTE_SPECIAL');
        
        if (empty($data)) {
            $field = static::$id_d.','.static::$attrName_d.','.static::$typeId_d.','.static::$inputType_d.','.static::$attrValues_d;
           
            $data = $this->order(static::$id_d.static::DESC.','.static::$order_d.static::DESC)->getField($field);
            
            if (empty($data)) {
                return array();
            }
            S('GOODS_ATTRIBUTE_SPECIAL', $data, 60);
        }
        return $data;
    }
    
    /**
     * 获取列表 
     */
    public function getList ($page, $where)
    {
        if (($page = intval($page)) === 0) {
            return  array();
        }
        
        return $this->getDataByPage([
            'field' => array(static::$createTime_d, static::$updateTime_d),
            'where' => $where,
            'order' => static::$order_d.static::DESC.','.static::$id_d.static::DESC
        ], $page, true, AjaxPage::class);
        
    }

    /**
     * 判断是否存在属性
     */
    public function isExistAttr($id)
    {
        $result = $this->where(static::$id_d . ' = %d ' , $id)->find();
        return empty($result) ? false : true ;
    }
     
}