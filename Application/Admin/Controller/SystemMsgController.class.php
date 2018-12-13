<?php
declare(strict_types=1);
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\OrderPjaxTrait;
use Common\Logic\StoreMsgLogic;

/**
 * 系统消息
 * @author Administrator
 */
class SystemMsgController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	use OrderPjaxTrait;
	
	/**
	 * 构造方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->args = $args;
		
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->logic =new StoreMsgLogic($args);
	}
	
	
	/**
	 * 消息列表
	 */
	public function index() :void
	{
		$listData = $this->logic->getDataList();
		
		$this->objController->ajaxReturnData($listData);
	}
	
	/**
	 * 删除消息
	 */
	public function deleteMsg() :void
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
		$status = $this->logic->delete();
		
		$this->objController->promptPjax($status, '删除失败');
		
		$this->objController->ajaxReturnData('');
	}
}