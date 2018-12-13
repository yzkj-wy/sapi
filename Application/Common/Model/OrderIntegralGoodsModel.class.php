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

namespace Common\Model;
use Common\Model\BaseModel;

/**
 * 订单模型 
 * @author 王强
 * @version 1.0.1
 */
class OrderIntegralGoodsModel extends BaseModel
{
    
    private static $obj ;


	public static $id_d;	//编号

	public static $orderId_d;	//订单【编号】

	public static $goodsId_d;	//商品【编号】

	public static $integral_d;	//积分

	public static $money_d;	// 商品价格

	public static $goodsNum_d;	//商品数量

	public static $status_d;	//-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功

	public static $comment_d;	//是否已评价（0未评价1已评价）

	public static $integralId_d;	//积分商品【编号】

	public static $storeId_d;	//店铺【编号】

	public static $freightId_d;	//运费模板

	public static $userId_d;	//用户【id】


    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
}