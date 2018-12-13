<?php
namespace Common\Logic;

use Common\Model\AlipaySerialNumberModel;
use Common\Tool\Tool;
use Think\Log;
use Think\Cache;

class AlipaySerialNumberLogic extends AbstractGetDataLogic
{
	private $orderALiData = [];
	
	/**
	 * 支付宝订单号
	 * @var unknown
	 */
	private $aliOrderId;
	
	/**
	 * @return \Common\Logic\unknown
	 */
	public function getAliOrderId()
	{
		return $this->aliOrderId;
	}
	
	/**
	 * 构造方法
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '') 
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new AlipaySerialNumberModel();
	}
	
	/**
	 * 更新支付宝单号
	 */
	public function getResult() 
	{
		$data = $this->data;
		if (empty($data)) {
			$this->rollback();
			return false;
		}
		
		$status = $this->addAll();
		if (!$this->traceStation($status)) {
			return false;
		}
		
		return $status;
	}
	
	/**
	 * 添加时处理参数
	 * @return array
	 */
	protected function getParseResultByAddAll()
	{
		$bitchData = explode(',', $this->data['order_id']);
		
		$result = [];
		$i = 0;
		
		foreach ($bitchData as $key => $value) {
			
			$result[$i][AlipaySerialNumberModel::$orderId_d] = $value;
			
			$result[$i][AlipaySerialNumberModel::$alipayCount_d] = $this->data['trade_no'];
			
			$result[$i][AlipaySerialNumberModel::$type_d] = $this->data['type'];
			
			$i++;
		}
		
		return $result;
	}
	
	
	/**
	 * 获取凭据
	 */
	public function getOrderAli()
	{
		if (empty($this->data['id'])) {
			return [];
		}
		
		return $this->modelObj->field('order_id, alipay_count')
			->where('order_id = %d and '.AlipaySerialNumberModel::$type_d.'= %d' , [$this->data['id'],$this->data['pay_logic_type']])
			->find();
	}
	/**
	 * 获取商品支付支付宝订单号
	 */
	public function getAlipayByOrder()
	{
		if (empty($this->data['id'])) {
			return [];
		}
		
		$cache = Cache::getInstance('', ['expire' => 160]);
		
		$key = 'AlipaySeral_'.$this->data['id'].'_'.$_SESSION['store_id'];
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			return $data; 
		}
		
		 $data = $this->modelObj->field('order_id, alipay_count')
			->where('order_id = %d and '.AlipaySerialNumberModel::$type_d.'= 0 ' , [$this->data['order_id']])
			->find();
		 
		if (empty($data)) {
			return [];
		}
		$cache->set($key, $data);
		
		return $data;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName() {
		return AlipaySerialNumberModel::class;
	}
	
	/**
	 * 余额充值处理
	 */
	public function parseByRecharge()
	{
		$status = $this->addData();
		
		if (!$this->traceStation($status)) {
			return false;
		}
		return true;
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
	 */
	protected function getParseResultByAdd() :array
	{
		$result = [
			AlipaySerialNumberModel::$alipayCount_d => $this->data['trade_no'],
			AlipaySerialNumberModel::$orderId_d => $this->data['order_id'],
			AlipaySerialNumberModel::$type_d => $this->data['type']
		];
		
		return $result;
		
	}
	
}