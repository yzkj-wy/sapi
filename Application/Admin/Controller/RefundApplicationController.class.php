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
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\OrderReturnGoodsLogic;
use Common\Logic\OrderLogic;
use Common\Tool\Tool;
use Common\Logic\GoodsLogic;
use Admin\Logic\GoodsImagesLogic;
use Admin\Logic\UserLogic;
use PlugInUnit\Validate\CheckParam;
use Common\Logic\OrderGoodsLogic;
use Common\TraitClass\CancelOrder;

/**
 * 退货
 * @author Administrator
 */
class RefundApplicationController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	use CancelOrder;
	
	/**
	 * 构造方法
	 *
	 * @param array $args
	 */
	public function __construct(array $args = []) {
		
		$this->init ();
		
		$this->isNewLoginAdmin ();
		
		$this->args = $args;
		
		$this->logic = new OrderReturnGoodsLogic( $args );
	}
	
	/**
	 * 退货列表
	 */
	public function returnList()
	{
		$orderLogic = new OrderLogic($this->args, $this->logic->getOrderSplitKey());
		
		Tool::connect('parseString');
		
		$orderWhere = $orderLogic->getAssociationCondition();
		
		$userLogic = new UserLogic($this->args, $this->logic->getUserSplitKey());
		
		$userWhere = $userLogic->getAssociationCondition();

		$this->logic->setAssociationWhere(array_merge($orderWhere, $userWhere));
		
		$data = $this->logic->getDataList();
		
		$this->objController->promptPjax($data, '暂无退货');
		
		$orderLogic->setData($data['data']);
		
		$data['data'] = $orderLogic->getOrderByOrderReturn();
		
		//获取商品信息
		$goodsLogic = new GoodsLogic($data['data'], $this->logic->getGoodsSplitKey());
		
		$data['data'] = $goodsLogic->getGoodsData();
		
		//商品图片
		$goodsImageLogic = new GoodsImagesLogic($data['data'], $goodsLogic->getSplitkeyByPId());
		
		$data['data'] = $goodsImageLogic->getImageByResource();
		
		$userLogic->setData($data['data']);
		
		$data['data'] = $userLogic->getUserByOrderReturn();
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 退货详情
	 */
	public function getRefundDetail()
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric());
		
		$data = $this->logic->getFindOne();
		
		$this->objController->promptPjax($data, '空数据');
		
		$orderLogic = new OrderLogic($data, $this->logic->getOrderSplitKey());
		
		$data = $orderLogic->getOrderDataByRefund();
		
		//获取商品
		$goodsLogic = new GoodsLogic($data, $this->logic->getGoodsSplitKey());
		
		$data = $goodsLogic->getGoodsDataByRefund();
		
		//用户
		$userLogic = new UserLogic($data, $this->logic->getUserSplitKey());
		
		$data = $userLogic->getUserName();
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 切换状态覆盖Trait 方法
	 */
	public function changeStatus()
	{
		$checkObj = new CheckParam($this->logic->getMessageByChangeStatus(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$status = $this->logic->chanageSaveStatus();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$orderGoodsLogic = new OrderGoodsLogic(['data' => $this->logic->getFindOne(), 'args' => $this->args], $this->logic->getGoodsSplitKey());
		
		//修改订单商品状态
		$status = $orderGoodsLogic->updateStatus();
		
		$this->objController->updateClient($status, '修改');
	}
	
	/**
	 * 退款
	 */
	public function cancelReturnOrder()
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
		//检查状态
		$status = $this->logic->refundMoneyStatus();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		//获取订单数据
		$orderReturnData = $this->logic->getOrderReturnData();
		
		$orderLogic = new OrderLogic($orderReturnData, $this->logic->getOrderSplitKey());
		
		$result = $orderLogic->getOrderByOrderReturnOne();
		
		$this->objController->promptPjax($result, '退货错误');
		
		$res = $this->cancelOrder($result);
		
		$this->objController->promptPjax($res, $this->errorMsg);
		
		$status = $this->logic->parseRefundMoneyStatus();
		
		$this->objController->promptPjax($status, '更新退货状态失败');
		
		//更新状态
		//订单商品表
		$orderGoodsLogic = new OrderGoodsLogic($result);
		
		$status = $orderGoodsLogic->editStatus();
		
		$this->objController->promptPjax($status, '更新订单商品状态失败');
		
		$this->objController->ajaxReturnData($status);
	}
	
	/**
	 * 处理退货商品
	 */
	public function parseRefundGoods()
	{
		$this->checkParamByClient();
		
		$status = $this->logic->parseRefundGoods();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$goodsLogic = new GoodsLogic(['data' => $this->logic->getFindOne(), 'args' => $this->args]);
		
		$status = $goodsLogic->returnInventory();
		
		$this->objController->ajaxReturnData($status);
	}
}