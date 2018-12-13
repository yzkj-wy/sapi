<?php

namespace Common\TraitClass;

use Common\Tool\Constant\OrderStatus;
use Common\Tool\Tool;

trait OrderStatusTrait
{
	/**
	 * 获取订单状态
	 */
	private function getOrderStatus() {
		// 获取全部订单状态
		$orderModel = new \ReflectionClass ( OrderStatus::class );
		
		$data = $orderModel->getConstants ();
		
		Tool::connect ( 'ArrayChildren', $data );
		
		// 状态 改为键 value改为汉字提示；
		$data = Tool::connect ( 'ArrayChildren', $data )->changeKeyValueToPrompt ( C ( 'order' ) );
		
		return $data;
	}
}