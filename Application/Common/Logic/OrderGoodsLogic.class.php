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
namespace Common\Logic;

use Common\Model\OrderGoodsModel;
use Common\Tool\Tool;
use Think\ModelTrait\Select;
use Think\Cache;
use Common\Tool\Constant\OrderStatus;

/**
 * 订单商品逻辑处理
 * @author 王强
 */
class OrderGoodsLogic extends AbstractGetDataLogic
{
    private $statusWhere = [];
    
    private $orderById = [];
    
    /**
     * 退货审核 状态转换
     *  1 退货审核失败 2 退货审核成功【商品退货表里的】
     * @var array
     */
    private $orderStatus = [
    	1 => 6,
    	2 => 7
    ];
    /**
     * @return the $orderById
     */
    public function getOrderById()
    {
        return $this->orderById;
    }

    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new OrderGoodsModel();
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return OrderGoodsModel::class;
    }
    
    /**
     * 返回商品相关字段
     */
    public function getGoodsSplitKey()
    {
        return OrderGoodsModel::$goodsId_d;
    }
    
    /**
     * 根据订单编号查询商品编号
     * @return array
     */
    public function getGoodsIdByOrderId()
    {
    	$data = $this->modelObj->field($this->getTableColum())
    		->where(OrderGoodsModel::$orderId_d.'= %d', $this->data['id'])
    		->select();
    	return $data;
    }
    
    /**
     * 根据订单编号查询商品编号
     * @return array
     */
    public function getGoodsIdByOrderIdCache() :array
    {
    	$key = $_SESSION['store_id'].$this->data['id'].'order_custom';
    	
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
     * 获取订单商品
     */
    public function getOderGoods()
    {
    	$field = $this->getTableColum() ;
    	
    	$data = $this->modelObj->field($field)->where(OrderGoodsModel::$goodsId_d.'=:gd and '.OrderGoodsModel::$storeId_d.' = :sd')
    		->bind([
    			':gd' => $this->data[OrderGoodsModel::$goodsId_d],
    			':sd' => $_SESSION['store_id']
    		])
    		->select();
    	
    	return $data;
    }
    
    /**
     * 获取订单商品并缓存
     */
    public function getOrderGoodsCache()
    {
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$key = $this->data[OrderGoodsModel::$goodsId_d].'_ddssk';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		$this->errorMessage = '暂无数据';
    		return $data;
    	}
    	
    	$data = $this->getOderGoods();
    	
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
    		OrderGoodsModel::$id_d,
    		OrderGoodsModel::$goodsId_d,
    		OrderGoodsModel::$goodsNum_d,
    		OrderGoodsModel::$freightId_d,
    		OrderGoodsModel::$status_d,
    		OrderGoodsModel::$goodsPrice_d,
    		OrderGoodsModel::$orderId_d
    	];
    	
    }
    
    /**
     * 获取状态
     */
    public function getStatus ()
    {
        $orderStatus = $this->modelObj->where(OrderGoodsModel::$orderId_d .'= %d and '.OrderGoodsModel::$goodsId_d .'= %d', $this->data)->getField(OrderGoodsModel::$status_d);
        
        return $orderStatus;
    }
    
 
    /**
     * 验证退货款状态 
     */
    public function getIsStatus() :bool
    {
        $order = $this->getOrderData();
        return (int)$order[OrderGoodsModel::$status_d] === 5 ? true : false;
    }
    
    /**
     * 获取订单商品数据
     * @return array
     */
    public function getOrderData()
    {
        $data = $this->modelObj->where(OrderGoodsModel::$orderId_d .'= %d and '.OrderGoodsModel::$goodsId_d .'= %d', $this->data)->find();
        
        $this->orderById = $data;
        
        return $data;
    }
    
    /**
     * 获取退货商品买了几件
     * @return integer
     */
    public function getGoodsNumber() :int
    {
        $data = $this->getOrderData();
        
        if (empty($data)) {
            return 0;
        }
        return (int)$data[OrderGoodsModel::$goodsNum_d];
    }
    
    /**
     * 修改状态（退货审核成功）
     */
    public function updateStatus( )
    {
    	//-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功
        
    	$orederReturnData = $this->data['args'];
    	
    	if (!isset($this->orderStatus[$orederReturnData['status']])) {
    		$this->modelObj->rollback();
    		return false;
    	}
    	
    	
    	$save = [
    		OrderGoodsModel::$status_d => $this->orderStatus[$orederReturnData['status']]
        ];
  
    	$data = $this->data['data'];
    	
        $condition = [
        	$data['order_id'],
        	$data[$this->splitKey]
        ];
        
        $status = $this->modelObj->where(OrderGoodsModel::$orderId_d.'=%d and '.OrderGoodsModel::$goodsId_d.'=%d', $condition)->save($save);
      
        if (!$this->modelObj->traceStation($status)) {
            $this->errorMessage = '订单商品状态修改失效';
            return $status;
        }
        
        $this->modelObj->commit();
        
        return $status;
    }
    
    /**
     * 修改状态
     */
    public function editStatus ()
    {
        
        $condition = [$this->data['order_id'], $this->data['goods_id']];
      
        $status = $this->modelObj->where(OrderGoodsModel::$orderId_d .'= %d and '.OrderGoodsModel::$goodsId_d .'= %d', $condition)->save([OrderGoodsModel::$status_d => '9']);
        if (!$this->traceStation($status)) {
            return false;
        }
        $this->modelObj->commit();
    
        return $status;
    }
    
    /**
     * 删除退货信息时修改状态
     */
    public function deleteRefundGoodsByUpdateStatus()
    {
        $status = $this->modelObj->where(OrderGoodsModel::$orderId_d .'= %d and '.OrderGoodsModel::$goodsId_d .'= %d', $this->data)->save([OrderGoodsModel::$status_d => '4']);
       
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        $isCommit = $this->modelObj->commit();
        return $status;
    }

    /**
     * 根据订单数据获取订单商品数据
     * @return array
     */
    public function getDataByOrder() :array
    {
        $data = $this->data;

        if (empty($data)) {
            return [];
        }

        $idString = Tool::characterJoin($data, $this->splitKey);
        if (empty($idString)) {
            return [];
        }

        $field = [
            OrderGoodsModel::$goodsId_d,
            OrderGoodsModel::$goodsNum_d,
            OrderGoodsModel::$orderId_d,
            OrderGoodsModel::$goodsPrice_d
        ];

        $orderGoodsData = $this->modelObj->field($field)->where(OrderGoodsModel::$orderId_d.' in (%s)', $idString)->select();

        return $orderGoodsData;
    }
    
    /**
     * 获取从表字段（根据主表数据查从表数据的附属方法）
     * @return array
     */
    protected function getSlaveField() :array
    {
    	$field = [
    		OrderGoodsModel::$goodsId_d,
    		OrderGoodsModel::$goodsNum_d,
    		OrderGoodsModel::$orderId_d,
    		OrderGoodsModel::$goodsPrice_d
    	];
    	return $field;
    }
    
    /**
     * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
     */
    protected function getSlaveColumnByWhere() :string
    {
    	return OrderGoodsModel::$orderId_d;
    }
    
    /**
     * 获取 验证信息
     */
    public function getMessageByValidate() :array
    {
    	return [
    		OrderGoodsModel::$goodsId_d => [
    			'number' => '商品编号必须是数字',
    		]
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
	    		->where(OrderGoodsModel::$orderId_d.'=:o_id and '.OrderGoodsModel::$userId_d.'=:u_id')
	    		->bind([
	    			':o_id' => $this->data['id'],
	    			':u_id' => $this->data['user_id']
	    		])->save([
	    			OrderGoodsModel::$status_d => OrderStatus::AlreadyShipped
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