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
class OrderIntegralModel extends BaseModel
{
    private static $obj ;


	public static $id_d;	//编号

	public static $orderSn_d;	//订单标志

	public static $interaglTotal_d;	//使用积分总数

	public static $priceSum_d;	//花费金额

	public static $userId_d;	//用户

	public static $expressId_d;	//快递单编号

	public static $addressId_d;	//收货地址【编号】

	public static $createTime_d;	//创建时间

	public static $deliveryTime_d;	//发货时间

	public static $payTime_d;	//支付时间

	public static $overTime_d;	//完结时间

	public static $orderStatus_d;	//-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功, 

	public static $commentStatus_d;	//评价状态 0未评价 1已评价

	public static $wareId_d;	//仓库编号

	public static $payType_d;	//支付类型编号

	public static $remarks_d;	//订单备注

	public static $status_d;	//0正常1删除

	public static $translate_d;	//1需要发票，0不需要

	public static $shippingMonery_d;	//运费【这样 就不用 重复计算两遍】

	public static $expId_d;	//快递表编号

	public static $platform_d;	//平台【0代表pc，1代表app，2Wap】

	public static $orderType_d;	//订单类型【0普通订单1货到付款】

	public static $storeId_d;	//店铺


    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
}