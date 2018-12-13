<?php
namespace Admin\Controller;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\OrderStatusTrait;
use Common\TraitClass\InitControllerTrait;

class OrderStatusController
{
	use InitControllerTrait;
	use IsLoginTrait;
	
	use OrderStatusTrait;
	
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->isNewLoginAdmin();
	}
	
	/**
	 * 获取订单所有状态
	 */
	public function getOrderAllStatus()
	{
		$this->objController->ajaxReturnData($this->getOrderStatus());
	}
}