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
namespace Admin\Controller;

use Common\Controller\AuthController;
use Common\Logic\CouponsLogic;
use Common\Model\BaseModel;
use Admin\Model\CouponModel;
use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\OrderPjaxTrait;

/**
 * 优惠劵列表
 */
class CouponsController extends AuthController {
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
//		$this->isNewLoginAdmin();
		$this->args = $args;
		
		$this->logic = new CouponsLogic ( $args );
	}
	/**
	 * 优惠劵列表
	 */
	public function couponsList() {
		$r = $this->logic->logList ();
		
		$this->objController->ajaxReturnData ( $r );
	}
	
	/**
	 * 添加代金卷 到数据库
	 */
	public function addCouponData() {
		
		// 验证数据
		$this->checkParamByClient();
		$result = $this->logic->logAddCoupon (); // 添加
		$this->objController->promptPjax ( $result ['status'], $result ['message'] );
		$this->objController->ajaxReturnData ( $result ['data'], $result ['status'], $result ['message'] );
	}
	/**
	 * 编辑优惠券
	 */
	public function updCoupon() {
		
		// 验证数据
		$status = $this->checkParamByClient();
		$result = $this->logic->logUpdCoupon ();
		$this->objController->promptPjax ( $result ['status'], $result ['message'] );
		$this->objController->ajaxReturnData ( $result ['data'], $result ['status'], $result ['message'] );
	}
	
	/**
	 * 编辑优惠券获取信息
	 */
	public function getCouponsById() {
		$result = $this->logic->logGetCouponsById ();
		
		if ($result) {
			$this->objController->ajaxReturnData ( $result, 1, '操作成功' );
		} else {
			$this->objController->ajaxReturnData ( '', 0, '暂无数据' );
		}
	}
	
	/**
	 * 发放优惠券--会员列表
	 */
	public function memberList() {
		$result = $this->logic->logMemberList ();
		
		$this->objController->ajaxReturnData ( $result );
	}
	
	/**
	 * 会员列表搜索
	 */
	public function memberListSearch() {
		$result = $this->logic->logListSearch ();
		
		$this->objController->ajaxReturnData ( $result );
	}
	
	/**
	 * 发放优惠券
	 */
	public function sendCoupon() {
		// 验证数据
		$result = $this->logic->logSendCoupon ();
		
		$this->objController->ajaxReturnData ( $result ['data'], $result ['status'], $result ['message'] );
	}
	
	/**
	 * 删除代金卷
	 */
	public function deleteCoupon() {
		Tool::checkPost ( $_POST, array (
						'is_numeric' => array (
										'id' 
						) 
		), true, array (
						'id' 
		) ) ? true : $this->ajaxReturnData ( null, 0, '参数错误' );
		
		$status = BaseModel::getInstance ( CouponModel::class )->delete ( array (
						'where' => array (
										CouponModel::$id_d => $_POST ['id'] 
						) 
		) );
		
		$this->updateClient ( $status, '操作' );
	}
}