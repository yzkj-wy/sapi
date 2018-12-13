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
use Common\Model\OrderIntegralModel;
use Think\Cache;
use Common\Tool\Extend\ArrayChildren;
use Common\Tool\Constant\OrderStatus;

/**
 * 商品规格
 * @author 王强
 */
class OrderIntegralLogic extends AbstractGetDataLogic
{
 
	/**
	 * 订单数据
	 * @var array
	 */
	private $orderSendData = [];
	
	public function getOrderSendData() :array
	{
		return $this->orderSendData;
	}
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
         
        $this->modelObj = new OrderIntegralModel();
    
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
     * 获取地区关联字段
     * @return string
     */
    public function getAddressSplitKey() :string
    {
    	return OrderIntegralModel::$addressId_d;
    }
    
    /**
     * 获取用户关联字段
     * @return string
     */
    public function getUserSplitKey() :string
    {
    	return OrderIntegralModel::$userId_d;
    }
    
    /**
     * 返回快递分割键
     * @return string
     */
    public function getExpressSplitKey() :string
    {
    	return OrderIntegralModel::$expId_d;
    }
    
    /**
     * 获取支付类型关联字段
     * @return string
     */
    public function getPayTypeSplitKey() :string
    {
    	return OrderIntegralModel::$payType_d;
    }
    
    /**
     * 获取订单状态
     */
    public function getOrderStatus ()
    {
        $cacheObj = Cache::getInstance('', ['expire' => 864000]);
        
        $orderStatus = $cacheObj->get('order_status');
        
        if(empty($orderStatus))
        {
            //获取全部订单状态
            $obj = new \ReflectionObject($this);
            $data       = $obj->getConstants();
           
            //状态 改为键  value改为汉字提示；
            $orderStatus = (new ArrayChildren($data))->changeKeyValueToPrompt( C('order'));
            
            $cacheObj->set('order_status', $orderStatus);
            
        }
    
        return $orderStatus;
    }
    
    
    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
    	return OrderIntegralModel::class;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            OrderIntegralModel::$orderSn_d
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::searchArray()
     */
    protected function searchArray()
    {
    	return [
    		OrderIntegralModel::$orderStatus_d			
    	];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getDataList()
     */
    public function getDataList() 
    {
    	$this->searchTemporary = [
    		OrderIntegralModel::$storeId_d => $_SESSION['store_id']
    	];
    	
    	return parent::getDataList();
    }
    
    /**
     * 修改发货信息快递单检测
     * @return array
     */
    public function getMessageValidate() :array
    {
    	return [
    		OrderIntegralModel::$id_d => [
    			'number' => 'id必须是数字',
    		],
    		OrderIntegralModel::$expressId_d => [
    			'number' => '快递单号必须是数字'
    		],
    		OrderIntegralModel::$expId_d => [
    			'number' => '快递公司编号必须是数字'
    		]
    	];
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	return [
    		OrderIntegralModel::$id_d,
    		OrderIntegralModel::$orderSn_d.' as order_sn_id',
    		OrderIntegralModel::$storeId_d,
    		OrderIntegralModel::$expressId_d,
    		OrderIntegralModel::$orderStatus_d,
    		OrderIntegralModel::$payTime_d,
    		OrderIntegralModel::$createTime_d,
    		OrderIntegralModel::$userId_d
    	];
    }
    
    //订单确定发货
    public function getOrderSendGoods() :bool
    {
    	
    	$this->modelObj->startTrans();
    	
    	$res = $this->saveData();
    	
    	if (!$this->traceStation($res)) {
    		$this->errorMessage = '发货失败';
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
     */
    protected function getParseResultBySave() :array
    {
    	$data = $this->data;
    	
    	$data[OrderIntegralModel::$deliveryTime_d] = time();
    	
    	$data[OrderIntegralModel::$orderStatus_d] = OrderStatus::AlreadyShipped;
    	
    	$findOneData = $this->getFindOne();
    	
    	$data[OrderIntegralModel::$userId_d] = $findOneData[OrderIntegralModel::$userId_d];
    	
    	$this->orderSendData = $data;
    	
    	return $data;
    }
}