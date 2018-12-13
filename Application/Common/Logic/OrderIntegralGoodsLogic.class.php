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
namespace Common\Logic;

use Common\Model\OrderIntegralGoodsModel;
use Think\Cache;
use Common\Tool\Constant\OrderStatus;

/**
 * 积分订单商品
 * @author Administrator
 */
class OrderIntegralGoodsLogic extends AbstractGetDataLogic
{
    
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
        
        $this->modelObj = new OrderIntegralGoodsModel();
        
        $this->splitKey = $split;
        
        //         $this->covertKey = GoodsSpec::$name_d;
    }
    
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        //TODO
    }
    
    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
    	return OrderIntegralGoodsModel::class;
    }
    
    /**
     * 根据订单数据获取订单商品数据
     * @return array
     */
    public function getDataByOrder()
    {
    	$data = $this->data;
    	
    	
    	$field = [
    		OrderIntegralGoodsModel::$goodsId_d,
    		OrderIntegralGoodsModel::$goodsNum_d,
    		OrderIntegralGoodsModel::$orderId_d,
    		OrderIntegralGoodsModel::$money_d
    	];
    	
    	$orderGoodsData = $this->getDataByOtherModel($field, $this->splitKey);
    	
    	return $orderGoodsData;
    }
    
    /**
     * 获取从表字段（根据主表数据查从表数据的附属方法）
     * @return array
     */
    protected function getSlaveField() :array
    {
    	$field = [
    		OrderIntegralGoodsModel::$goodsId_d,
    		OrderIntegralGoodsModel::$goodsNum_d,
    		OrderIntegralGoodsModel::$orderId_d,
    		OrderIntegralGoodsModel::$money_d
    	];
    	return $field;
    }
    
    /**
     * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
     */
    protected function getSlaveColumnByWhere() :string
    {
    	return OrderIntegralGoodsModel::$orderId_d;
    }
    
    /**
     * 获取商品关联字段
     */
    public function getGoodsSplitKey()
    {
    	return OrderIntegralGoodsModel::$goodsId_d;
    }
    
    /**
     * 根据订单编号查询商品编号
     * @return array
     */
    public function getGoodsIdByOrderId()
    {
    	
    	$data = $this->modelObj->field($this->getTableColum())
    	->where(OrderIntegralGoodsModel::$orderId_d.'= %d', $this->data['id'])
    	->select();
    	return $data;
    }
    
    /**
     * 根据订单编号查询商品编号
     * @return array
     */
    public function getGoodsIdByOrderIdCache() :array
    {
    	$key = $_SESSION['store_id'].$this->data['id'].'orderintegral';
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	$data = $this->getGoodsIdByOrderId();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
    	return [
    		OrderIntegralGoodsModel::$id_d,
    		OrderIntegralGoodsModel::$goodsId_d,
    		OrderIntegralGoodsModel::$goodsNum_d,
    		OrderIntegralGoodsModel::$freightId_d,
    		OrderIntegralGoodsModel::$status_d,
    		OrderIntegralGoodsModel::$money_d.' as goods_price',
    		OrderIntegralGoodsModel::$orderId_d
    	];
    	
    }
    
    
    /**
     * 更新订单商品发货
     * @return bool
     */
    public function updateGoodsSendStatus() :bool
    {
    	try {
    		$status = $this->modelObj
    			->where(OrderIntegralGoodsModel::$orderId_d.'=:o_id and '.OrderIntegralGoodsModel::$userId_d.'=:u_id')
	    		->bind([
    				':o_id' => $this->data['id'],
    				':u_id' => $this->data['user_id']
	    		])->save([
	    			OrderIntegralGoodsModel::$status_d => OrderStatus::AlreadyShipped
	    		]);
    		if (!$this->traceStation($status)) {
    			$this->errorMessage = '订单商品修改失败';
    			return false;
    		}
    		
    		$this->modelObj->commit();
    		
    		return true;
    	} catch (\Exception $e) {
    		$this->modelObj->rollback();
    		$this->errorMessage = $e->getMessage();
    		return false;
    	}
    }
    
}