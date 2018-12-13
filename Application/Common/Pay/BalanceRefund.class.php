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
use Common\Logic\BalanceLogic;

/**
 * 余额退款
 * @author Administrator
 *
 */
class BalanceRefund
{
    
    private $data = [];
    
    private $error = '';
    
    private $payData = [];
    
    
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 
     * @param array $data 退款退货数据
     * @param array $payData 支付配置数据
     */
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
            return false;
        }
      	
        $balance = new BalanceLogic($this->data);
        
        $status = $balance->addData();
        
        return $status !== false;
    }
}