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
namespace Common\TraitClass;

use Common\Model\BaseModel;
use Home\Model\OrderModel;
use Common\Model\OrderGoodsModel;
use Home\Logical\AmountLogic;
use Common\Logic\OfflineOrderLogic;
use Think\Hook;
use Home\Logical\Model\IntegralUseLogic;


/**
 * 支付通知
 * 
 * @author Administrator
 */
trait OrderNoticeTrait 
{
    /**
     * 支付宝流水号
     * @var int
     */
    private $payIntegral;
    protected $tradeNo = 0;
    
    private function orderNotice($orderId)
    {

        $orderModel = BaseModel::getInstance(OrderModel::class);
        
        $status = $orderModel->paySuccessEditStatus($orderId);

        if (empty($status)) {
            return false;
        }
        
        $param = [
            'order_sn_id' => $orderId,
            'trade_no' => $this->tradeNo,
            'wx_order_id' => $orderId,
            'type'        => 0
        ];
        Hook::listen('aplipaySerial', $param);

        if (empty($param)) {
            return false;
        }

        //增加积分
        $order = BaseModel::getInstance(OrderGoodsModel::class);
        $data = $order -> getGoodsDataByOrderId($orderId);
        $user_id = M('order')->where(['id' => $orderId])->getField('user_id');

        $add_integral = new IntegralUseLogic($data, $this->payIntegral,$user_id);
        if(($data = $add_integral->addIntegral()) === false){
            return false;
        }


        $orderGoodsModel = BaseModel::getInstance(OrderGoodsModel::class);
        
        $status = $orderGoodsModel->updateOrderGoodsStatus($orderId);

        if (empty($status)) {
            return false;
        }

        // 减库存
        
        $amountModel = new AmountLogic($orderId, $orderGoodsModel);

        $status = $amountModel->amountParse();
        if (empty($status)) {
            return false;
        }
        
      
        return true;
    }
    /**
     * 开店回调
     */
    private function opShopNofity()
    {
        $orderData = SessionGet::getInstance('order_data')->get();

        $config = SessionGet::getInstance('pay_config_by_user')->get();

        $reslut = [
            'order_id' => $orderData['order_id'],
            'pay_id' => $config['pay_type_id'],
            'platform' => $config['type']
        ];

        $orderLogic = new OfflineOrderLogic($reslut);

        $status = $orderLogic->saveStatus();

        $day = date('y_m_d');

        Log::write('线下订单修改----'.$status, Log::INFO, '', './Log/open_shop/'.$day.'.txt');

        if ($status === false) {
            return false;
        }
        SessionGet::getInstance('store_data')->delete();
        SessionGet::getInstance('pay_config_by_user')->delete();
        return true;
    }


}