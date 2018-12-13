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
use Common\Tool\Tool;
use Extend\Alipay\Aop\AopClient;
use Extend\Alipay\Aop\Request\AlipayTradePagePayRequest;
use Think\SessionGet;

/**
 * 支付宝PC支付
 * @author Administrator
 *
 */
class Alipay
{
	use PayTrait;
	
	private $config = [];
	
	private $orderData = [];
	
	/**
	 * 架构方法
	 * @param array $config
	 * @param array $orderData
	 */
	public function __construct(array $config = [], array $orderData = [])
	{
		$this->config = $config;
	
		$this->orderData = $orderData;
	}
	
	
	/**
	 * 微信支付
	 */
	public function pay()
	{
		$info = $this->orderData ;
		$priceSum = $this->totaMoney();
		
		if (bccomp($priceSum, 0.00, 2) === -1 || $this->isPass === false) {
			return [
				'data'=> '',
				'message'=>  '价格异常 或者 运费计算错误',
				'status'=>  0
			];
		}
		
		$payConfig = $this->config;
		
		$token = $payConfig['token'];
		
		unset($payConfig['token']);
		
		SessionGet::getInstance('pay_config_by_user', $payConfig)->set();
		
		$payRequestBuilder = new AopClient();
		
		
		$payRequestBuilder->appId = $this->config['pay_account'];
		
		$payRequestBuilder->rsaPrivateKey = $this->config['private_pem'];
		
		$payRequestBuilder->signType = 'RSA2';
		
		$config = [
			'product_code' => 'FAST_INSTANT_TRADE_PAY',
			'subject'=> '多商户商品支付',
			'body' => 'OrderPay',
			'total_amount' => $priceSum,
			'passback_params'=> json_encode(['token' => $token], JSON_UNESCAPED_UNICODE),
			'out_trade_no' => Tool::connect('Token')->toGUID(),
		];
		
		$payRequest = new AlipayTradePagePayRequest();
		
		$payRequest->setReturnUrl($this->config['return_url']);
		
		$payRequest->setNotifyUrl($this->config['notify_url']);
		
		$payRequest->setBizContent(json_encode($config, JSON_UNESCAPED_UNICODE));
		
		$result=$payRequestBuilder->pageExecute($payRequest);
		
		return [
			'data' =>$result,
			'message' => '成功',
			'status' => 1
		];
	}
}