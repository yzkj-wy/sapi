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
use Common\Logic\AlipaySerialNumberLogic;
use Extend\Alipay\Wappay\BuilderModel\AlipayTradeRefundContentBuilder;
use Extend\Alipay\Wappay\Service\AlipayTradeService;

/**
 * 支付宝退款 
 * @author 王强
 * @version 1.0.1
 */
class AlipayRefund 
{
    use PayTrait;
    
    private $error = '';
    
    private $data = [];
	
    public function __construct(array $data, array $payData)
    {
    	//AlipaySubmit
    	$this->data = $data;
    	
    	$this->payData = $payData;
    	
    }
    
    /**
     * @return the $error
     */
    public function getError()
    {
    	return $this->error;
    }
    
    /**
     * 支付寶退款
     */
    public function refundMonery()
    {
    	
    	if (empty($this->data)) {
    		$this->error = '退货数据错误';
            return false;
        }
        
        // 获取支付宝配置
        $alipayConfig = $this->payData;
        
        if (empty($alipayConfig)) {
        	$this->error = '支付数据错误';
        	return false;
        }
        
        //获取流水号
        $logic = new AlipaySerialNumberLogic($this->data);
        
        $alipayOrder = $logic->getAlipayByOrder();
        
        if (empty($alipayOrder)) {
        	$this->error = '未找到支付宝支付信息';
        	return false;
        }
        
        $monery = $this->data['price'];
        if (empty($monery)) { 
           
        	$this->error = '金额错误';
        	
        	return false;
        	
        }
        
        $config = [];
        
        $config['app_id'] = $alipayConfig['pay_account'];
        $config['merchant_private_key'] = $alipayConfig['private_pem'];
        $config['alipay_public_key'] = $alipayConfig['public_pem'];
        
        $requestBuilder = new AlipayTradeRefundContentBuilder();
       
        $requestBuilder->setTradeNo($alipayOrder['alipay_count']);
        $requestBuilder->setOutTradeNo($this->data['order_sn_id']);
        $requestBuilder->setRefundAmount($monery);
        $requestBuilder->setRefundReason('正常退款');
        $requestBuilder->setOutRequestNo($this->data['id']);
        $response = new AlipayTradeService($config);
        
        //建立请求
        $res = $response->Refund($requestBuilder);
        if ($res->code != 10000) {
        	
        	$this->error = $res->sub_msg;
        	return false;
        }
        
        return true;
    }
}