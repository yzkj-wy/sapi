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
namespace Common\TraitClass;

use Common\Logic\GoodsLogic;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\UserAddressLogic;
use Admin\Logic\UserLogic;
use Common\Logic\ExpressLogic;
use Common\Tool\Tool;
use Admin\Logic\PayTypeLogic;
use Common\Logic\RegionLogic;
use Admin\Logic\GoodsImagesLogic;

/**
 * 订单trait
 * @author 王强
 */
trait OrderPjaxTrait
{

	private $errorMessage = '';
    
	/**
     * ajax 获取数据
     */
    private function ajaxGetData() :array
    {
    	$this->parseWhere();
        // 获取订单数据

        $data = $this->getOrderList();
        if (empty($data)) {
        	$this->errorMessage = '暂无订单';
        	return [];
        }
        
        //获取订单商品信息

        $orderGoodsData = $this->getOrderGoods($data);
        if (empty($orderGoodsData)) {
        	$this->errorMessage = '订单商品错误';
        	return [];
        }

        $goodsLogic = new GoodsLogic($orderGoodsData, 'goods_id');

        $goodsData = $goodsLogic->getOrderInfo();
      
      	if (empty($goodsData)) {
      		$this->errorMessage = '商品数据错误';
      		return [];
      	}
        
      	$goodsData = $this->callBack($goodsData);
      	
      	if (empty($goodsData)) {
      		$this->errorMessage = '商品数据回调错误';
      		return [];
      	}
      	
        $goodsImage = new GoodsImagesLogic($goodsData, $goodsLogic->getSplitkeyByPId());
        
        $image = $goodsImage->getPicByOrderData();
      
        if (empty($image)) {
        	$this->errorMessage = '图片数据错误';
        	return [];
        }
        
        $userLogic = new UserLogic($image,  $this->logic->getUserSplitKey());
        
        $userData = $userLogic->getUserData();
      
        if (empty($userData)) {
        	$this->errorMessage = '用户数据错误';
        	return [];
        }
        $data['data'] = array_values($data['data']);
        $data['goods'] = $userData;
        return $data;

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
     * 处理搜索条件
     */
    private function parseWhere() :void
    {
    	$userLogic = new UserLogic($this->args, $this->logic->getUserSplitKey());
    	
    	Tool::connect('parseString');
    	
    	$userWhere = $userLogic->getAssociationCondition();
    	
    	$this->logic->setAssociationWhere($userWhere);
    }
    
    /**
     * 获取订单商品信息
     */
    private function getOrderGoods(array $data) :array
    {
    	//获取订单商品信息
    	$orderGoodsLogic = new OrderGoodsLogic($data['data'], $this->logic->getOrderGoodsSplitKey());
    	
    	$orderGoodsData = $orderGoodsLogic->getSlaveDataByMaster();
    	
    	return $orderGoodsData;
    }
    
    /**
     * 公共方法
     */
    private function getOrder() :array
    {
        $this->objController->errorArrayNotice($this->args);
    
        //获取订单信息
        $data = $this->logic->getFindOne();
        $this->objController->promptPjax($data, $this->logic->getErrorMessage());
    
        //获取运送方式
        $splitKey = $this->logic->getExpressSplitKey();
        $expressLogic = new ExpressLogic($data, $splitKey);
    
        $data[$splitKey] = $expressLogic->getExpressTitle();
         
        //获取支付方式
        $paySplitKey = $this->logic->getPayTypeSplitKey();
        $payTypeLogic = new PayTypeLogic($data, $paySplitKey);
    
        $data[$paySplitKey] = $payTypeLogic->getPayTypeName();
        return $data;
    }
    
    /**
     * 订单详情
     */
    public function orderDetail() :void
    {
    	$data = $this->getOrder();
    	
    	$this->objController->promptPjax($data, '没有数据集');
    	
    	//传递给用户模型
    	$userLogic = new UserLogic($data);
    	
    	$userData = $userLogic->userInfoByOrder();
    	
    	$receive     = $this->getAddressInfo($data);
    	
    	
    	//传递给商品模型
    	$goodsDatail = $this->getOrderGoodsInfo($data, true);
    	
    	$data = [
    			'receive' => empty($receive) ? "" : $receive,
    			'goods' => $goodsDatail,
    			'order' => $data
    	];
    	
    	$this->objController->ajaxReturnData($data);
    }
    
    /**
     * 地址信息
     */
    private function getAddressInfo(array $data) :array
    {
        //收货人信息
        $userAddressLogic = new UserAddressLogic($data, null);

        $receive = $userAddressLogic->receiveManByOrder();
        
        if (empty($receive)) {
        	$this->errorMessage = '收货地址错误';
        	return [];
        }
        
        $userModelClassName = $userAddressLogic->getModelClassName();
        
        //传递地区表
        $regionLogic = new RegionLogic($receive, $userModelClassName);
        
        $receive     = $regionLogic->getDefaultRegion();
        
        
        return $receive;
    }
    
    private function getOrderGoodsInfo(array $data) :array
    {
    	if (!is_array($data) || empty($data))
    	{
    		return array();
    	}
    	
    	$orderGoods = $this->getGoodsInfo($data);
    	
    	$goodsLogic = new GoodsLogic($orderGoods);
    	
    	Tool::connect('parseString');
    	//传递给商品模型
    	$goodsDatail = $goodsLogic->getOrderInfo();
    	
    	$goodsImageLogic = new GoodsImagesLogic($goodsDatail, $goodsLogic->getSplitkeyByPId());
    	
    	$goodsDatail = $goodsImageLogic->getPicByOrderData();
    	
    	return $goodsDatail;
    }
    
    /**
     * 订单详情获取商品信息
     */
    private function getGoodsInfo($data) :array
    {
    	//传递给商品订单模型
    	$orderGoodsLogic = new OrderGoodsLogic($data);
    	
    	
    	$orderGoods  = $orderGoodsLogic->getGoodsIdByOrderIdCache();
    	
    	return $orderGoods;
    }
    
    /**
     * 回掉方法
     */
    private function callBack(array $data) :array
    {
    	return $data;
    }
}