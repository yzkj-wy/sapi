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
declare(strict_types=1);
namespace Admin\Controller;

//短信工厂类->发货提示
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderIntegralLogic;
use Common\Logic\OrderIntegralGoodsLogic;
use Common\TraitClass\OrderPjaxTrait;
use PlugInUnit\Validate\CheckParam;

/**
 * 积分订单控制器
 * @author 王强
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class OrderIntegralController 
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use OrderPjaxTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new OrderIntegralLogic($args);
    }
    
    
    //订单列表 - 全部订单
    public function orderList() :void
    {
    	$data = $this->ajaxGetData();
    	
    	$this->objController->promptPjax($data, $this->errorMessage);
    	
    	$this->objController->ajaxReturnData($data);
    }
    
    /**
     * 获取订单商品信息
     */
    private function getOrderGoods(array $data) :array
    {
    	//获取订单商品信息
    	$orderGoodsLogic = new OrderIntegralGoodsLogic($data['data'], $this->logic->getPrimaryKey());
    	
    	$orderGoodsData = $orderGoodsLogic->getSlaveDataByMaster();
    	
    	return $orderGoodsData;
    }
    
    /**
     * 获取订单列表
     */
    private function getOrderList() :array
    {
    	$data = $this->logic->getDataList();
    	
    	return $data;
    }
    
    /**
     * 订单详情获取商品信息
     */
    private function getGoodsInfo($data) :array
    {
    	//传递给商品订单模型
    	$orderGoodsLogic = new OrderIntegralGoodsLogic($data);
    	
    	
    	$orderGoods  = $orderGoodsLogic->getGoodsIdByOrderIdCache();
    	
    	return $orderGoods;
    }
    
    /**
     * 订单详情--确定发货
     * 王波
     */
    public function orderSendGoods() :void
    {
    	//验证数据
    	$checkObj = new CheckParam($this->logic->getMessageValidate(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$status = $this->logic->getOrderSendGoods();
    	
    	$this->objController->promptPjax($status, $this->logic->getErrorMessage());
    	
    	$orderGoodsLogic = new OrderIntegralGoodsLogic($this->logic->getOrderSendData());
    	
    	$status = $orderGoodsLogic->updateGoodsSendStatus();
    	
    	$this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
    	
    	$this->objController->ajaxReturnData("");
    }
    
}