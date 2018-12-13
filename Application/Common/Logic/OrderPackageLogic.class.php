<?php
namespace Common\Logic;

use Common\Model\OrderPackageModel;
use Common\Tool\Constant\OrderStatus;

/**
 * 套餐订单
 * @author Administrator
 *
 */
class OrderPackageLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param unknown $data
	 */
	public function __construct(array $data = [], $split = null)
	{
		$this->data = $data;
		
		$this->modelObj = new OrderPackageModel();
		
		$this->splitKey = $split;
		
	}
	
	/**
	 * 获取店铺地址数据
	 */
	public function getResult()
	{
		
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getDataList()
	 */
	public function getDataList()
	{
		$this->searchTemporary = [
			OrderPackageModel::$storeId_d => $_SESSION['store_id']
		];
		
		return parent::getDataList();
	}
	
	/**
	 * 获取模型类名
	 * @return string
	 */
	public function getModelClassName() :string
	{
		return OrderPackageModel::class;
	}
	
	/**
	 * 获取关联字段
	 * @return string
	 */
	public function getUserSplitKey() :string
	{
		return OrderPackageModel::$userId_d;
	}
	
	/**
	 * 获取运费关联字段
	 * @return string
	 */
	public function getExpressSplitKey() :string
	{
		return OrderPackageModel::$expressId_d;
	}
	
	/**
	 * 获取支付类型关联字段
	 * @return string
	 */
	public function getPayTypeSplitKey():string
	{
		return OrderPackageModel::$payType_d;
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
		
		$data[OrderPackageModel::$deliveryTime_d] = time();
		
		$data[OrderPackageModel::$orderStatus_d] = OrderStatus::AlreadyShipped;
		
		$findOneData = $this->getFindOne();
		
		$data[OrderPackageModel::$userId_d] = $findOneData[OrderPackageModel::$userId_d];
		
		$this->orderSendData = $data;
		
		return $data;
	}
	
}