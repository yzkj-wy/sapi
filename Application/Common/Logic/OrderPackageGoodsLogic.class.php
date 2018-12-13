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

use Common\Model\OrderPackageGoodsModel;
use Think\Cache;
use Common\Tool\Constant\OrderStatus;

class OrderPackageGoodsLogic extends AbstractGetDataLogic
{
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data, $split= null)
	{
		$this->data = $data;
		
		$this->modelObj = new OrderPackageGoodsModel();
		
		$this->splitKey = $split;
	}
	
	/**
	 * 获取数据
	 */
	public function getResult()
	{
		
	}
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName()
	{
		return OrderPackageGoodsModel::class;
	}
	
	/**
	 * 获取从表字段（根据主表数据查从表数据的附属方法）
	 * @return array
	 */
	protected function getSlaveField() :array
	{
		return $this->getTableColum();
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
	 */
	protected function getTableColum():array
	{
		$field = [
			OrderPackageGoodsModel::$goodsId_d,
			OrderPackageGoodsModel::$packageNum_d,
			OrderPackageGoodsModel::$orderId_d,
			OrderPackageGoodsModel::$packageDiscount_d,
			OrderPackageGoodsModel::$goodsDiscount_d,
			OrderPackageGoodsModel::$packageId_d
		];
		return $field;
	}
	
	/**
	 * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
	 */
	protected function getSlaveColumnByWhere() :string
	{
		return OrderPackageGoodsModel::$orderId_d;
	}
	
	/**
	 * 根据订单编号查询商品编号
	 * @return array
	 */
	public function getGoodsIdByOrderId()
	{
		
		$data = $this->modelObj->field($this->getTableColum())
			->where(OrderPackageGoodsModel::$orderId_d.'= %d', $this->data['id'])
			->select();
		return $data;
	}
	
	/**
	 * 根据订单编号查询商品编号
	 * @return array
	 */
	public function getGoodsIdByOrderIdCache() :array
	{
		$key = $_SESSION['store_id'].$this->data['id'].'order_package';
		
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
	 * 更新订单商品发货
	 * @return bool
	 */
	public function updateGoodsSendStatus() :bool
	{
		try {
			$status = $this->modelObj
			->where(OrderPackageGoodsModel::$orderId_d.'=:o_id and '.OrderPackageGoodsModel::$userId_d.'=:u_id')
			->bind([
				':o_id' => $this->data['id'],
				':u_id' => $this->data['user_id']
			])->save([
				OrderPackageGoodsModel::$status_d => OrderStatus::AlreadyShipped
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