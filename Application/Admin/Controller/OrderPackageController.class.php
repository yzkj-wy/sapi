<?php
declare(strict_types=1);
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\GoodsTrait;
use Common\Logic\OrderPackageLogic;
use Common\TraitClass\OrderPjaxTrait;
use Common\Logic\OrderPackageGoodsLogic;
use Common\Logic\GoodsPackageLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 套餐订单
 * @author Administrator
 *
 */
class OrderPackageController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	use GoodsTrait;
	
	use OrderPjaxTrait;
	
	/**
	 * 构造方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->args = $args;
		
		$this->logic = new OrderPackageLogic($args);
	}
	
	
	//订单列表 - 全部订单
	public function orderList() :void
	{
		$data = $this->ajaxGetData();
		
		$this->objController->promptPjax($data, $this->errorMessage);
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 获取订单列表
	 */
	private function getOrderList() :array
	{
		$data = $this->logic->getDataList();
		return $data;
	}
	
	/**
	 * 获取订单商品信息
	 */
	private function getOrderGoods(array $data)
	{
		//获取订单商品信息
		$orderGoodsLogic = new OrderPackageGoodsLogic($data['data'], $this->logic->getPrimaryKey());
		
		$orderGoodsData = $orderGoodsLogic->getSlaveDataByMaster();
		return $orderGoodsData;
	}
	
	/**
	 * 回掉方法
	 */
	private function callBack(array $data) :array
	{
		$goodsPackage = new GoodsPackageLogic($data, 'package_id');
		
		$data = $goodsPackage->getResult();
		
		return $data;
	}
	
	/**
	 * 订单详情获取商品信息
	 * @return array
	 */
	private function getGoodsInfo($data) :array
	{
		//传递给商品订单模型
		$orderGoodsLogic = new OrderPackageGoodsLogic($data);
		
		
		$orderGoods  = $orderGoodsLogic->getGoodsIdByOrderId();
		
		return $orderGoods;
	}
	
	/**
	 * 订单详情--确定发货
	 * 王波
	 */
	public function orderSendGoods() :void
	{
		//验证数据
		$checkObj = new CheckParam($this->logic->getMessageValidate(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$status = $this->logic->getOrderSendGoods();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$orderGoodsLogic = new OrderPackageGoodsLogic($this->logic->getOrderSendData());
		
		$status = $orderGoodsLogic->updateGoodsSendStatus();
		
		$this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
		
		$this->objController->ajaxReturnData("");
	}
}