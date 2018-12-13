<?php
namespace Common\Model;

use Think\Model;

/**
 * 微信支付 凭据模型 
 */
class OrderTlpayModel extends BaseModel
{
    private static $obj ;


	public static $id_d;	//支付宝订单退款编号

	public static $orderId_d;	//订单号

	public static $paymentOrderId_d;	//支付宝流水号

	public static $type_d;	//支付类型 0 商品支付， 1余额充值

    public static $pay_time_id;	//支付类型 0 商品支付， 1余额充值

    public static function getInitnation()
    {
        $class = __CLASS__;
        return self::$obj = !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    
   
}