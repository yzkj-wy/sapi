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
declare(strict_types = 1);

namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreBindClassLogic;
use Admin\Logic\GoodsClassLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 经营分类申请
 * @author Administrator
 */
class StoreBindClassController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	
	public function __construct($args = null)
	{
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->args = $args;
		
		$this->logic =  new StoreBindClassLogic($this->args);
	}
	
	/**
	 * 列表
	 */
	public function index() :void
	{
		$data = $this->logic->getDataList();
		
		$this->objController->promptPjax($data['data'], $this->logic->getErrorMessage());
		
		$goodsClassLogic = new GoodsClassLogic($this->logic->getWhereByClass($data['data']), '', $data['data']);
		
		$data['data'] = $goodsClassLogic->parseClassData();
		
		$this->objController->ajaxReturnData($data);
	}
	
	/**
	 * 编辑时获取数据
	 */
// 	public function getBindClassByEdit() :void
// 	{
// 		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
// 		$data = $this->logic->getFindOne();
		
// 		$this->objController->ajaxReturnData($data);
// 	}
	
	/**
	 * 添加
	 */
	public function add() :void
	{
		$checkParam = new CheckParam($this->logic->getValidateMessage(), $this->args);
		
		$this->objController->promptPjax($checkParam->checkParam(), $checkParam->getErrorMessage());
		
		$status = $this->logic->addAll();
		
		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($status);
	}
	
	/**
	 * 编辑
	 */
// 	public function save() :void
// 	{
// 		$checkParam = new CheckParam($this->logic->getValidateMessage(), $this->args);
		
// 		$this->objController->promptPjax($checkParam->checkParam(), $checkParam->getErrorMessage());
		
// 		$status = $this->logic->saveEdit();
		
// 		$this->objController->promptPjax($status, $this->logic->getErrorMessage());
		
// 		$this->objController->ajaxReturnData($status);
// 	}
	
	/**
	 * 删除申请的经营类目
	 */
	public function delete() :void
	{
		$this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
		
		$status = $this->logic->delete();
		
		$this->objController->promptPjax($status, '删除失败');
		
		$this->objController->ajaxReturnData('');
	}
}