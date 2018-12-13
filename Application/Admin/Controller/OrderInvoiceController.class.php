<?php
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\OrderPjaxTrait;
use Common\Logic\OrderLogic;

/**
 * 发货单
 * @author Administrator
 */
class OrderInvoiceController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	use OrderPjaxTrait;
	
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->args = $args;
		
		$this->logic = new OrderLogic($args);
	}
	
	
	//发货单列表 
	public function orderList()
	{
		$data = $this->ajaxGetData();
		
		$this->objController->promptPjax($data, $this->errorMessage);
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 获取订单列表
	 */
	private function getOrderList()
	{
		$data = $this->logic->getOrderInvoice();
		return $data;
	}
	
	/**
	 * 打印发货单（订单数据）
	 */
	public function getInvoiceData()
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
		$orderData = $this->logic->getFindOne();
		
		$this->objController->promptPjax($orderData, '数据异常');
		
		$this->objController->ajaxReturnData($orderData);
	}
}