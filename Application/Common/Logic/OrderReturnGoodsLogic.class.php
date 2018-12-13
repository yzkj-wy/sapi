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

use Admin\Model\OrderReturnGoodsModel;

/**
 * 订单退货逻辑处理
 * @author 王强
 * @version 1.0
 */
class OrderReturnGoodsLogic extends AbstractGetDataLogic
{
    /**
     * 订单状态条件
     * @var array
     */
    private $oderStatusWhere = [];
    
    /**
     * 退货数量
     * @var int
     */
    private $refundGoodsNumber = 0;
    
    /**
     * 回归库存 信息
     */
    private $returnInventory = [];
    
    /**
     * 退货时 是否已收到
     * @var boolean
     */
    private $isRefundStock = true;
    /**
     * 状态对应的方法
     * 审核状态【0审核中1审核失败2审核通过3退货中4换货中5换货完成6退货完成7已撤销】
     * @var array
     */
    private $stateCorrespondenceMethod = [
        'save',
        'save',
        'save',
        'save',
        'save',
        'exchangeCompleted',
        'save',
        'rescinded'
    ];
    
    /**
     * 退货数据
     * @var array
     */
    private $orderReturnData = [];
    
    public function getOrderReturnData()
    {
    	return $this->orderReturnData;
    }
    
    /**
     * 是否使用了事务
     * @var boolean
     */
    private $doYouUseTransactions = FALSE;
    
    /**
     * @return the $doYouUseTransactions
     */
    public function getDoYouUseTransactions()
    {
        return $this->doYouUseTransactions;
    }

    /**
     * @return the $isRefundStock
     */
    public function getIstRefundStock()
    {
        return $this->isRefundStock;
    }

    /**
     * @return the $oderStatusWhere
     */
    public function getOderStatusWhere()
    {
        return $this->oderStatusWhere;
    }

    /**
     * @return the $refundGoodsNumber
     */
    public function getRefundGoodsNumber()
    {
        return $this->refundGoodsNumber;
    }

    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new OrderReturnGoodsModel();
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
        return OrderReturnGoodsModel::class;
    }
    
    /**
     * 返回订单表关联字段
     */
    public function getOrderSplitKey()
    {
        return OrderReturnGoodsModel::$orderId_d;
    }
    
    /**
     * 返回商品表关联字段
     */
    public function getGoodsSplitKey()
    {
        return OrderReturnGoodsModel::$goodsId_d;
    }
    
    /**
     * 返回用户表关联字段
     */
    public function getUserSplitKey()
    {
        return OrderReturnGoodsModel::$userId_d;
    }
    
    /**
     * 字段注释
     * @return array
     */
    public function showComment()
    {
        return [
            OrderReturnGoodsModel::$id_d,
            OrderReturnGoodsModel::$orderId_d,
            OrderReturnGoodsModel::$createTime_d,
            OrderReturnGoodsModel::$tuihuoCase_d,
            OrderReturnGoodsModel::$goodsId_d,
            OrderReturnGoodsModel::$type_d,
            OrderReturnGoodsModel::$status_d,
            OrderReturnGoodsModel::$auditor_d,
            OrderReturnGoodsModel::$isReceive_d
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getFindOne()
     * @return []
     */
    public function getFindOne()
    {
        $data = parent::getFindOne();
        
        if (empty($data)) {
            return [];
        }
        
        $this->oderStatusWhere = [
            $data[OrderReturnGoodsModel::$orderId_d], $data[OrderReturnGoodsModel::$goodsId_d]
        ];
        
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message =  [
            OrderReturnGoodsModel::$id_d => [
                'number' => $comment[OrderReturnGoodsModel::$id_d].'必须是数字'
            ],
            OrderReturnGoodsModel::$content_d => [
            	'required' => '数据错误'.$comment[OrderReturnGoodsModel::$content_d],
            ],
        	OrderReturnGoodsModel::$isReceive_d => [
        		'number' => $comment[OrderReturnGoodsModel::$isReceive_d].'必须是数字，且介于${0-1}',
        	]
        ];
        
        return $message;
    }
    
    /**
     * 更新数据
     */
    public function save()
    {
        return $this->saveData($this->data);
    }
    
    /**
     * 处理退货商品
     */
    public function parseRefundGoods()
    {
    	$this->modelObj->startTrans();
    	
    	$status = $this->saveData();
    	
    	if (!$this->traceStation($status)) {
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 验证 字段
     */
    public function checkIsReciveColum()
    {
        $comment = $this->modelObj->getComment();
        
        $message =  [
            OrderReturnGoodsModel::$id_d => [
                'required' => '数据错误'.$comment[OrderReturnGoodsModel::$id_d],
                'number' => $comment[OrderReturnGoodsModel::$id_d].'必须是数字'
            ],
            OrderReturnGoodsModel::$isReceive_d => [
                'required' => '数据错误'.$comment[OrderReturnGoodsModel::$isReceive_d],
                'number' => $comment[OrderReturnGoodsModel::$isReceive_d ].'必须是数字'
            ],
        ];
        
        $result = false;
        
        foreach ($message as $key => $value) {
        
            $result = $this->paramCheckNotify($value, $key);
             
            if ($result === false) {
                return false;
            }
        }
        
        return $result;
    }
    
    /**
     * 验证是否收到货物
     * @return bool
     */
    public function checkIsReceive()
    {
        $args = $this->data;
       
        if (empty($args)) {
            return false;
        }
        
        $args[OrderReturnGoodsModel::$isReceive_d] =  (int)$args[OrderReturnGoodsModel::$isReceive_d] === 0 ? 1 : 0;
        
        $this->isRefundStock = $args[OrderReturnGoodsModel::$isReceive_d] === 0 ? false : true;
        
        $this->modelObj->startTrans();
        
        $status = $this->modelObj->save($args);
       
        if (!$this->modelObj->traceStation($status)) {
            $this->errorMessage = '修改状态失败';
        }
        
        return $status;
    }
    
    /**
     * 返回类型字段
     */
    public function getTypeSplitKey()
    {
        return OrderReturnGoodsModel::$type_d;
    }
    
    /**
     * 铸造订单商品条件
     * @return array
     */
    public function buildOrderGoodsCondition()
    {
        $data = $this->getFindOne();
      
        if (empty($data)) {
            return [];
        }
        
        $this->refundGoodsNumber = (int)$data[OrderReturnGoodsModel::$number_d];
        
        $this->returnInventory = [
            'goods_id' => $data[OrderReturnGoodsModel::$goodsId_d],
            
            'goods_number' => $data[OrderReturnGoodsModel::$number_d]
        ];
        
        return [$data[OrderReturnGoodsModel::$orderId_d], $data[OrderReturnGoodsModel::$goodsId_d]];
    }
    /**
     * @return the $returnInventory
     */
    public function getReturnInventory()
    {
        return $this->returnInventory;
    }
    
    /**
     * 获取仓库关联字段
     */
    public function getAuditorSplitKey()
    {
        return OrderReturnGoodsModel::$auditor_d;
    }
    
    /**
     * 更新处理状态
     */
    public function saveStatus()
    {
        if ($this->data['order_goods_status'] != 9 && $this->data[OrderReturnGoodsModel::$status_d] == 6) {
            $this->errorMessage = '退货未完成退款:(';
            return false;
        }
        
        $method = $this->stateCorrespondenceMethod[$this->data[OrderReturnGoodsModel::$status_d]];
        
        $result = null;
        try {
            $reflection = new \ReflectionMethod(__CLASS__.'::'.$method);
            
            $result = $reflection->invoke($this);
            
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $result;
    }
    
    /**
     * 已撤销
     */
    public function rescinded()
    {
        $this->modelObj->startTrans();
        
        $this->doYouUseTransactions = true;
        
        $data = $this->data;
        
        $data[OrderReturnGoodsModel::$revocationTime_d] = time();
        
        $status = $this->modelObj->save($data);
        
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        return $status;
    }
    
    /**
     * 修改状态【退货完成】
     */
    public function parseRefundMoneyStatus ()
    {
        $this->modelObj->startTrans();
    	
        //退货完成
        $status = $this->modelObj->save([
            OrderReturnGoodsModel::$id_d => $this->data['id'],
            OrderReturnGoodsModel::$status_d => 4,
        ]);
     
        if (!$this->traceStation($status)) {
            return false;
        }
    
        return true;
    }
    /**
     * 删除 数据
     */
    public function delete()
    {
        $data = $this->getFindOne();
        
        if (empty($data)) {
            $this->errorMessage = '没有这条数据';
            return false;
        }
        
        if ((int)$data[OrderReturnGoodsModel::$status_d] >= 2) {
            $this->errorMessage = '审核通过，不能删除';
            return false;
        }
        
        $this->oderStatusWhere = [$data[OrderReturnGoodsModel::$orderId_d], $data[OrderReturnGoodsModel::$goodsId_d]];
        
        $this->modelObj->startTrans();
      
        $status = $this->modelObj->delete($this->data[OrderReturnGoodsModel::$id_d]);
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        return $status;
    }
    
    /**
     * 验证状态
     */
    public function getMessageByChangeStatus()
    {
    	////审核状态【0审核中1审核失败2审核通过3退货中4换货中5换货完成6退货完成7已撤销】
    	return [
    		OrderReturnGoodsModel::$id_d => [
    			'number' => '商品必须是数字'
    		],
    		OrderReturnGoodsModel::$status_d =>[
    			'number' => '状态必须是数字，且介于{1-2}'
    		]
    	];
    }
    
    /**
     * 修改状态时保存
     */
    public function chanageSaveStatus()
    {
    	$this->modelObj->startTrans();
    	
    	$status = parent::chanageSaveStatus();
    	
    	if (!$this->traceStation($status)) {
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 保存状态时处理参数
     */
    protected function getParseResultBySaveStatus() :array
    {
    	
    	$data = $this->data;
    	
    	$data[OrderReturnGoodsModel::$applyId_d] = $_SESSION['store_id'];
    	
    	return $data;
    }
    
    /**
     * 退钱是检查状态
     */
    public function refundMoneyStatus()
    {
    	$data = $this->getFindOne();
    	if (empty($data)) {
    		$this->errorMessage = '退货错误';
    		return false;
    	}
    	if ($data[OrderReturnGoodsModel::$status_d] != 2  || $data[OrderReturnGoodsModel::$isReceive_d] != 1) {
    		$this->errorMessage = '未审核或者未收到货';
    		return false;
    	}
    	$this->orderReturnData = $data;
    	
    	return true;
    }
}