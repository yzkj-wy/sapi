<?php
namespace Common\Logic;

use Common\Model\OrderWxpayModel;

/**
 * 订单微信信息逻辑处理
 * @author Administrator
 * @version 1.0.0
 */
class OrderWxpayLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new OrderWxpayModel();
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return OrderWxpayModel::class;
    }
    
    /**
     * 获取凭据
     */
    public function getOrderWx()
    {
        $data = $this->modelObj->where(OrderWxpayModel::$orderId_d .'= %d and '.OrderWxpayModel::$type_d.'= 0 ', $this->data[$this->splitKey])->find();
    	
    	return $data;
    }
    
    /**
     * 验证是否付款
     */
    public function checkIsPayment()
    {
        $payInfo = $this->getOrderWx();
        
        if (empty($payInfo)) {
            return false;
        }
        
        return (int)$payInfo[OrderWxpayModel::$status_d] === 1;
    }
}