<?php
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreInformationLogic;
use PlugInUnit\Validate\CheckParam;
use Common\Logic\StoreGradeLogic;
use Common\Logic\StoreManagementCategoryLogic;
use Admin\Logic\GoodsClassLogic;

/**
 * 店铺信息
 * @author Administrator
 *
 */
class StoreInformationController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	
	
	public function __construct($args = null)
	{
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->args = $args;
		
		$this->logic =  new StoreInformationLogic($this->args);
	}
	/**
	 * 店铺经营信息
	 */
	public function storeOperationInformation()
	{
		$checkObj = new CheckParam($this->logic->getMessageValidateStore(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam() ,$checkObj->getErrorMessage());
		
		$result = $this->logic->getResult();
		
		$this->objController->promptPjax($result, $this->logic->getErrorMessage());
		
		$storeGradeLogic = new StoreGradeLogic($result, $this->logic->getSplitKeyByLevel());
		
		$levelResult = $storeGradeLogic->getResult();
		
		$result = array_merge($result, $levelResult);
		
		// 店铺经营类目
		$categoryLogic = new StoreManagementCategoryLogic($result, $this->logic->getSplitKeyByStore());
		
		$categoryIds = $categoryLogic->getResult();
		
		$classLogic = new GoodsClassLogic($categoryIds);
		
		$classResult = $classLogic->getResult();
		
		$data = [
			'store_information' => $result,
			'class_result' => $classResult
		];
		
		$this->objController->ajaxReturnData($data);
	}
	
}