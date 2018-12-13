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
use Common\Tool\Event;
class GoodsAttrModel extends BaseModel
{
    
    public static $id_d;           //主键
    
    private static  $obj;
  

	public static $attributeId_d;	//商品属性编号

	public static $goodsId_d;	//商品id

	public static $attrValue_d;	//属性值

	public static $attrPrice_d;	//属性价格

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间
    
	protected $productId; //商品编号
    
	protected $varriableType = false; // 商品属性类型是否变了
	
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
   
    /**
     * @return the $productId
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    /**
     * @param field_type $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }
    
    /**
     * @return the $varriableType
     */
    public function getVarriableType()
    {
        return $this->varriableType;
    }
    
    /**
     * @param boolean $varriableType
     */
    public function setVarriableType($varriableType)
    {
        $this->varriableType = $varriableType;
    }
     
}