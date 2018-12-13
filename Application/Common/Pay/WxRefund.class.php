<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\Pay;
use Common\TraitClass\PayTrait;
use Common\Logic\OrderWxpayLogic;
use Extend\Wxpay\Pay\RefundPub;
use Extend\Wxpay\WxPayConfPub;

class WxRefund
{
    use PayTrait;
    
    private $data = [];
    
    private $error = '';
    
    
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    public function __construct(array $data, array $payData)
    {
        $this->data = $data;
        
        
        $this->payData = $payData;
        
    }
    
    /**
     * 微信退款 
     */
    public function refundMonery()
    {
        if (empty($this->data)) {
            return array();
        }
        //到订单微信表
        $orderWxLogic = new OrderWxpayLogic($this->data, 'order_id');;
       
        $wxData = $orderWxLogic->getOrderWx();
      	
        if (empty($wxData)) {
            $this->error = '未找到凭据';
            return false;
        }
        
        
        $total = intval($this->data['total_money']*100);
        if ( empty($total)) {
        	return false;
        }
        $data = $this->getPayConfig($this->getPayData()); 
        $refundPub = new RefundPub();
        $refundPub->setParameter('out_trade_no', $wxData['wx_pay_id']);
        $refundPub->setParameter('out_refund_no',$wxData['wx_pay_id']);
        $refundPub->setParameter('total_fee',    $total);
        $refundPub->setParameter('refund_fee',   $total);
        $refundPub->setParameter('op_user_id',   WxPayConfPub::$MCHID_d);
       
        $res = $refundPub->getResult();
        $status = $this->parseResulte($res);
        return $status;
        
    }
    /**
     * 
     * Array(
    'return_code' => SUCCESS
    'return_msg' => OK
    'appid' => wx68fa4860d905394f
    'mch_id' => 1338796801
    'nonce_str' => i3ewE9koS6FduZg4
    'sign' => B4C2C9AAB6BAD65CA7F84D46FBDD7010
    'result_code' => SUCCESS
    'transaction_id' => 4004602001201708217456116894
    'out_trade_no' => wx_201708211618361113433830-26
    'out_refund_no' => wx_201708211618361113433830-26
    'refund_id' => 50000604142017082101600104127
    'refund_channel' => Array
        (
        )

    'refund_fee' => 1
    'coupon_refund_fee' => 0
    'total_fee' => 1
    'cash_fee' => 1
    'coupon_refund_count' => 0
    'cash_refund_fee' => 1
     */
    
    /**
     * @desc 处理返回结果 【微信】
     */
    public function parseResulte($res)
    {
        if (empty($res)) {
            return false;
        }
        
        if ($res['return_code'] === 'SUCCESS' && $res['result_code'] === 'SUCCESS') {
            return true;
        }
        $this->error = $res['err_code_des'];
        return false;
    }
    
}