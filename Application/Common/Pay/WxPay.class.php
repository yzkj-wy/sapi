<?php
declare(strict_types = 1);
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
use Extend\Wxpay\Pay\UnifiedOrderPub;
use Common\Logic\OrderWxpayLogic;
use Extend\Wxpay\WxPayConfPub;
use Think\SessionGet;

/**
 * 微信h5支付
 * @author 王强
 */
class WxPay
{
	
	private $config = [];
	
	private $orderData = [];
	
	use PayTrait;
	
	/**
	 * 构造方法
	 */
	public function __construct(array $config = [], array $orderData = [])
	{
		$this->config = $config;
	
		$this->orderData = $orderData;
		
	}
	
	
	/**
	 * 微信支付
	 */
	public function pay() :array
	{
		$info = $this->orderData ;
		$priceSum = $this->totaMoney();

		
		if ($priceSum <= 0 || $this->isPass === false) {
			return [
				'data'=> '',
				'message'=>  '价格异常 或者 运费计算错误 价格必须大于0',
				'status'=>  0
			];
		}
		
		$orderWxPay = new OrderWxpayLogic($info, '', $this->config['special_status']);
		
		
		
		$payConfig = $this->getPayConfig($this->config);
		
		$urlNofity = WxPayConfPub::$NOTIFY_URL ;
		
		$orderWxId = $info[0]['order_id'];
		
		$unifiedOrderPub = new UnifiedOrderPub();
		
		$config = $this->config;
		
		$token = $config['token'];
		
		unset($config['token']);
		SessionGet::getInstance('pay_config_by_user', $config)->set();
		//自定义参数
		$unifiedOrderPub->setParameter("body", "亿速网络"); // 商品描述
		$unifiedOrderPub->setParameter("out_trade_no", $info[0]['order_id']); // 商户订单号
		$unifiedOrderPub->setParameter("total_fee", $priceSum * 100); // 总金额
		$unifiedOrderPub->setParameter("notify_url", $urlNofity); // 通知地址
		$unifiedOrderPub->setParameter("trade_type", "NATIVE"); // 交易类型
		$unifiedOrderPub->setParameter("attach", $token);
		
		// 获取统一支付接口结果
		$unifiedOrderResult = $unifiedOrderPub->getResult();
		
		// 商户根据实际情况设置相应的处理流程
		if ($unifiedOrderResult["return_code"] == "FAIL") {
			// 商户自行增加处理流程
			return [
				'message' =>'通信出错：' . $unifiedOrderResult['return_msg'],
				'status'  => '0',
				'data'    => '',
			];
		} elseif ($unifiedOrderResult["result_code"] == "FAIL") {
			// 商户自行增加处理流程
			return [
				'message'=> '错误代码：' . $unifiedOrderResult['err_code'] . '错误代码描述：' . $unifiedOrderResult['err_code_des'],
				'status'=> '0',
				'data'=> ''
			];
		}
		
		$orderIdString = implode(',', array_column($info, 'order_id'));
		
		$callBackURL = C('call_back_url_wx');
		
		return[
			'status'=> 1,
			'data'=> [
				'code_url' => $unifiedOrderResult['code_url'],
				'order_sn' => $orderWxId,
				'order_id' => $orderIdString,
				'callback_url' => $callBackURL[$config['special_status']]
			],
			'message'=> '成功'
		];
	}
	
	public function __destruct()
	{
		unset( $this->config, $this->orderData);
	}
}