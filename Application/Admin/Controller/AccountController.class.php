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
use Common\Logic\StoreSellerLogic;
use Common\Logic\StoreAuthGroupAccessLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * *账号管理
 * *
 */
class AccountController {
	use IsLoginTrait;
	use InitControllerTrait;
	/**
	 * 架构方法
	 */
	public function __construct(array $args = []) {
		$this->init ();
		$this->isNewLoginAdmin ();
		$this->args = $args;
		$this->logic = new StoreSellerLogic ( $this->args );
	}
	public function index() {
		$user_IP = ($_SERVER ["HTTP_VIA"]) ? $_SERVER ["HTTP_X_FORWARDED_FOR"] : $_SERVER ["REMOTE_ADDR"];
		$user_IP = ($user_IP) ? $user_IP : $_SERVER ["REMOTE_ADDR"];
		
		echo $user_IP;
	}
	/**
	 * 账号列表
	 */
	public function accountList() {
		// 获取账号列表
		$account = $this->logic->getAccountList ();
		$this->objController->promptPjax ( $account ['status'], $account ['message'] );
		$this->objController->ajaxReturnData ( $account ['data'], 1, $account ['message'] );
	}
	
	// 删除管理员
	public function delAccount() {
		$res = $this->logic->delAccount ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], 1, $res ['message'] );
	}
	// 批量删除管理员
	public function delAllAccount() {
		$res = $this->logic->delAllAccount ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], 1, $res ['message'] );
	}
	// 添加管理员
	public function addAccount() {
		// 验证数据
		
		$checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);
		
		$status = $checkObj->checkParam ();
		$this->objController->promptPjax ( $status, $this->logic->getErrorMessage () );
		$res = $this->logic->addAccount ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		
		$data = [ ];
		
		$data ['uid'] = $this->logic->getInstertId ();
		
		$data ['group_id'] = $this->args ['group_id'];
		
		$storeGroup = new StoreAuthGroupAccessLogic ( $data );
		
		$status = $storeGroup->addUser ();
		
		$this->objController->promptPjax ( $status, $storeGroup->getErrorMessage () );
		
		$this->objController->ajaxReturnData ( 1 );
	}
	// 角色列表
	public function roleList() {
		$account = $this->logic->getRoleList ();
		$this->objController->promptPjax ( $account ['status'], $account ['message'] );
		$this->objController->ajaxReturnData ( $account ['data'], 1, $account ['message'] );
	}
	// 角色列表(不分页)
	public function getRoleList() {
		$account = $this->logic->getRole ();
		$this->objController->promptPjax ( $account ['status'], $account ['message'] );
		$this->objController->ajaxReturnData ( $account ['data'], 1, $account ['message'] );
	}
	// 获取单条信息
	public function getRoleById() {
		$account = $this->logic->getRoleById ();
		$this->objController->promptPjax ( $account ['status'], $account ['message'] );
		$this->objController->ajaxReturnData ( $account ['data'], 1, $account ['message'] );
	}
	
	// 添加角色
	public function addRole() {
		$res = $this->logic->addRole ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], $res ['status'], $res ['message'] );
	}
	// 修改角色
	public function updRole() {
		$res = $this->logic->updRole ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], $res ['status'], $res ['message'] );
	}
	// 角色删除
	public function delRole() {
		$res = $this->logic->delRole ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], $res ['status'], $res ['message'] );
	}
	// 权限列表
	public function getPowerList() {
		$res = $this->logic->getPowerList ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], $res ['status'], $res ['message'] );
	}
	
	// 修改管理员
	public function saveAccount() {
		// 验证数据
		$status = $this->logic->checkIdIsNumric ();
		$this->objController->promptPjax ( $status, $this->logic->getErrorMessage () );
		$status = $this->logic->checkParam ();
		$this->objController->promptPjax ( $status, $this->logic->getErrorMessage () );
		$res = $this->logic->saveAccount ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], 1, $res ['message'] );
	}
	// 获取账号信息
	public function getAccountInfo() {
		// 验证数据
		$status = $this->logic->checkIdIsNumric ();
		$this->objController->promptPjax ( $status, $this->logic->getErrorMessage () );
		$res = $this->logic->getAccountInfo ();
		$this->objController->promptPjax ( $res ['status'], $res ['message'] );
		$this->objController->ajaxReturnData ( $res ['data'], 1, $res ['message'] );
	}
}