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

/**
 * 换货模型
 * @author 王强
 * @version 1.0.0
 */
class ExchangeOrderGoodsModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//

	public static $orderId_d;	//订单编号

	public static $goodsId_d;	//

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    
    public static function getInitnation()
    {
        $class = __CLASS__;
    
        return static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
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
     * {@inheritDoc}
     * @see \Think\Model::_before_update()
     */
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
    
        return $data;
    }
}