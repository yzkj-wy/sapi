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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\WxNofityTrait;
use Common\TraitClass\AlipayNotifyTrait;
use Think\Hook;
use Common\Behavior\Decorate;
use Common\Behavior\AlipaySerialNumber;
use Common\TraitClass\OrderNoticeTrait;
use Think\Log;
use Common\Behavior\Balance;
use Common\TraitClass\PayTrait;
use Think\SessionGet;

/**
 * 开店回调通知
 * @author Administrator
 *
 */
class OpenShopNofityController
{
	use InitControllerTrait;
	use WxNofityTrait;
	use AlipayNotifyTrait;
	use OrderNoticeTrait;
	use PayTrait;
	/**
	 * 回调配置
	 * @var array
	 */
	private $storeCallBack = [
		'persoStoreCallBack',
		'companyStoreCallBack'
	];
	
	/**
	 * 余额充值相关页面
	 * @var string
	 */
	const RECHARGE_RELEVANT = 'Nofity/rechargeRelevant';
	
	public function __construct(array $args)
	{
		$this->headerOriginInit();
	}
	
	/**
	 * 支付宝开店充值通知
	 */
	public function aplipayNotify ()
	{
		$this->data = $_POST;
		
		$data = $this->alipayResultParse();
		
		Log::write(print_r($data, true).'---Alipay---', Log::INFO, '', './Log/open_shop/'.date('y_m_d'));
		$this->msg($data);
		
		$this->tradeNo = $this->data['trade_no'];
		
		Hook::add('aplipayBalanceSerial', AlipaySerialNumber::class);
		
		Hook::add($this->storeCallBack[$data['store_type']], OpenShopCallBack::class);
		
		$this->result = $data;
		
		$this->listener = $this->storeCallBack[$data['store_type']];
		
		$status = $this->opShopNofity();
		
		$this->msg($status);
		
		echo 'SUCCESS';
		die();
		
	}
	
	/**
	 * 微信开店通知
	 */
	public function wxNotify()
	{
		$this->returnData= file_get_contents('php://input');
		
		$this->args = $this->getTheCustomParamter();
		
		$this->sessionInit();
		
		$config = SessionGet::getInstance('pay_config_by_user')->get();
		
		$this->msg($config);
		
		$this->getPayConfig($config);
		
		$status = $this->nofityWx();
		
		$this->msg($status);
		
		$orderDataByShop = SessionGet::getInstance('order_data')->get();
		$this->msg($orderDataByShop);
		
		Hook::add('aplipayBalanceSerial', Decorate::class);
		
		Hook::add($this->storeCallBack[$orderDataByShop['store_type']], OpenShopCallBack::class);
		
		$this->tradeNo = $this->args['out_trade_no'];
		
		$this->listener = $this->storeCallBack[$orderDataByShop['store_type']];
		
		$status = $this->opShopNofity();
		
		$this->msg($status);
		
		echo 'SUCCESS';die();
	}
	
	/**
	 * 余额通知balanceuser
	 */
	public function balanceNotify()
	{
		$this->data = $_POST;
		
		$this->msg($this->data);
		
		$this->msg(SessionGet::getInstance('ley_user')->get() == $this->data['ley_user']);
		
		$orderDataByShop = SessionGet::getInstance('order_data_by_open_shop')->get();
		
		$this->msg($orderDataByShop);
		
		Hook::add( 'aplipayBalanceSerial', Balance::class );
		
		Hook::add($this->storeCallBack[$orderDataByShop['store_type']], OpenShopCallBack::class);
		
		$this->result = $this->data;
		
		$this->listener = $this->storeCallBack[$orderDataByShop['store_type']];
		
		$status = $this->opShopNofity();
		
		$this->msg($status);
		
		echo 'SUCCESS';die();
	}
	
	private function msg($status)
	{
		if (empty($status)) {
			echo 'ERROR';
			die();
		}
	}
	
	public function __destruct()
	{
		unset($this->args, $this->data, $this->notify);
	}
}