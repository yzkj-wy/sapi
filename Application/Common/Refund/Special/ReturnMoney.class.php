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
namespace Common\Refund\Special;

use Common\Refund\Refund;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\OrderLogic;

class ReturnMoney implements Refund
{
    private $data = [];
    
    /**
     * 架构方法
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    /**
     * {@inheritDoc}
     * @see \PlugInUnit\Refund\Refund::refund()
     */
    public function refund()
    { 
//         $orderLogic = new OrderLogic($this->data, 'order_id');
//         //是否已支付
//         $isAlipay = (int)$orderLogic->isAlready();
        
//         $this->promptParse($isAlipay >= OrderModel::YesPaid || $isAlipay < OrderModel::ReturnMonerySucess , '未支付');
    
        //检测状态 是否正常
        $orderGoodsLogic = new OrderGoodsLogic([$this->data['order_id'], $this->data['goods_id']]);
    
        $status = $orderGoodsLogic->getIsStatus();
      
        if ($status === false) {
            return null;
        }
        $orderLogic = new OrderLogic($this->data, 'order_id');
        
        $orderData = $orderLogic->getOrderData();
       
        //获取退款信息
        
        $refundInfo = $orderGoodsLogic->getOrderById();
        
        return [
            'order_data'   => $orderData,
            'refund_info'  => $refundInfo,
        ];
    }
}