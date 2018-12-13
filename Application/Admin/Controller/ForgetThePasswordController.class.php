<?php
declare(strict_types=1);
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\Logic\StoreSellerLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 忘记密码
 * @author Administrator
 */
class ForgetThePasswordController
{
	use InitControllerTrait;
	
	public function __construct(array $args =[])
	{
		$this->args = $args;
		
		$this->init();
		
		$this->logic = new StoreSellerLogic($args);
	}
	
	/**
	 * 验证店铺账号是否存在
	 */
	public function checIsExistBySellerName() :void
	{
		$checkObj = new CheckParam($this->logic->getMessageCheckValidateByStore(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$status = $this->logic->checIsExistBySellerName();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData(session_id());
	}
	
	/**
	 * 修改密码
	 */
	public function parseReqByPassword()
	{
		$checkParam = new CheckParam($this->logic->getMessageCheckValidateByStorePassword(), $this->args);
		
		$this->objController->promptPjax($checkParam->checkParam(), $checkParam->getErrorMessage());
		
		$status = $this->logic->changePwd();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($status);
	}
}