<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 李向红
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Admin\Model\OrderModel;
use Common\Model\BaseModel;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderLogic;
use Common\TraitClass\OrderPjaxTrait;
use Think\Controller;

/**
 * 交易控制器
 * 
 * @author 李向红
 * @copyright 亿速网络
 * @version v1.1.2
 * @link http://yisu.cn
 */
class TransactionController {
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	use OrderPjaxTrait;
	
	/**
	 * 构造方法
	 * 
	 * @param array $args        	
	 */
	public function __construct(array $args = []) {
		$this->init ();
		$this->isNewLoginAdmin ();
		
		$this->args = $args;
		$this->logic = new OrderLogic ( $args );
	}
	// 订单管理
	public function orderManagement() {
		$data = $this->logic->logOrderManagement ();
		
		$this->objController->ajaxReturnData ( $data );
	}
	// 订单管理--订单状态
	public function orderStatus() {
		$orderModel = BaseModel::getInstance ( OrderModel::class );
		$store_id = session ( 'store_id' );
		$data = $orderModel->_getOrderStatus ( $store_id );
		$this->objController->ajaxReturnData ( $data );
	}
	// 发货单管理
	public function sendGoods() {
		$data = $this->logic->logSendGoods ();
		
		$this->objController->ajaxReturnData ( $data );
	}
	// 发货单管理--立即发货
	public function sendGoodsAtOnce() {
		$data = $this->logic->logSendGoodsAtOnce ();
		
		if ($data == false) {
			$this->objController->ajaxReturnData ( '', 0, '不是等待发货状态订单' );
		} else {
			$this->objController->ajaxReturnData ( $data );
		}
	}

	

	
	// 评价管理
	public function commentManage() {
		$data = $this->logic->LogCommentManage ();
		
		$this->objController->ajaxReturnData ( $data );
	}
	// 评价管理--删除
	public function deleteComment() {
		$data = $this->logic->logDeleteComment ();
		if (! empty ( $data )) {
			$this->objController->ajaxReturnData ( $data );
		} else {
			$this->objController->ajaxReturnData ( '', 0, '删除失败' );
		}
	}
	// 评价管理--回复
	public function answerComment() {
		// 验证数据
		$status = $this->logic->checkParam ();
		$this->objController->promptPjax ( $status, $this->logic->getErrorMessage () );
		
		$result = $this->logic->logAnswerComment ();
		$this->objController->promptPjax ( $result ['status'], $result ['message'] );
		$this->objController->ajaxReturnData ( $result ['data'], 1, $result ['message'] );
	}
}