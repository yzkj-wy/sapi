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
declare(strict_types=1);
namespace Admin\Controller;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\RegionLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 获取地区
 * @author Administrator
 *
 */
class RegionController
{
	use IsLoginTrait;
	
	use InitControllerTrait;
	
	
	/**
	 * 架构方法
	 */
	public function __construct(array $args =[])
	{  
		$this->args = $args;
		
		$this->init();
	
		$this->isNewLoginAdmin();
		
		$this->logic = new RegionLogic($this->args);
	}
	
	//获取下级地区
	public function lowerlevelArea() :void
	{
	
		$checkParam = new CheckParam($this->logic->getCheckValidateByRegion(), $this->args);
		
		$this->objController->promptPjax($checkParam->checkParam(), $checkParam->getErrorMessage());
		
		$prov = $this->logic->getUpDataById();
		
		$this->objController->promptPjax($prov, $this->logic->getErrorMessage());
		
		$this->objController->ajaxReturnData($prov);
	}
}