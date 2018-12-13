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

use Common\Logic\OrderGoodsLogic;
use PlugInUnit\Validate\CheckParam;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\GoodsLogic;

/**
 * 订单商品
 * @author Administrator
 */
class OrderGoodsController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	/**
	 * 架构方法
	 * @param array $args
	 */
	public function __construct(array $args = [])
	{
		$this->init();
		
		$this->isNewLoginAdmin();
		
		$this->args = $args;
		
		$this->logic = new OrderGoodsLogic($args);
	}
	
	/**
	 * 获取订单商品
	 */
	public function getOrderGoods()
	{
		$checkObj = new CheckParam($this->logic->getMessageByValidate(), $this->args);
		
		$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
		
		$data = $this->logic->getOrderGoodsCache();
		
		$this->objController->promptPjax($data, $this->logic->getErrorMessage());
		
		//获取商品
		$goodsLogic = new GoodsLogic($data, $this->logic->getGoodsSplitKey());
		
		$goods = $goodsLogic->getGoodsData();
		
		$this->objController->ajaxReturnData($goods);
	}
}