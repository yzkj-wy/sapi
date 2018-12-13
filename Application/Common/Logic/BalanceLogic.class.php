<?php
namespace Common\Logic;

use Admin\Model\BalanceModel;
use Think\Cache;

/**
 * 余额支付
 * @author 王强
 *
 */
class BalanceLogic extends AbstractGetDataLogic
{
	/**
	 * 当前余额记录
	 * @var array
	 */
	private $source = [];
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data = [], $split = '')
	{
		$this->data = $data;
		$this->splitKey = $split;
		$this->modelObj = new BalanceModel();
	}
	
	/**
	 * 计算总价
	 */
	private function totaMoney()
	{
		$info = $this->data ;
		
		$totalMoney = 0;
		
		foreach ($info as $value) {
			$totalMoney += $value['total_money'];
		}
		
		return $totalMoney + $_SESSION['total_freight'];
	}
	
	/**
	 * 退款处理
	 * 获取结果
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
		return BalanceModel::class;
	}
	
	/**
	 * 获取余额
	 * @return float
	 */
	public function getBalanceMoney ()
	{
		$data = $this->modelObj
			->field(BalanceModel::$accountBalance_d.','.BalanceModel::$lockBalance_d)
			->where(BalanceModel::$userId_d.'=%d and '.BalanceModel::$status_d.'= 1', (int)$this->data['user_id'])
			->order(BalanceModel::$id_d.' DESC ')
			->find();
		if (empty($data)) {
			return 0;
		}
		$money = bcsub($data[BalanceModel::$accountBalance_d], $data[BalanceModel::$lockBalance_d], 2);
		
		return $money;
	}

	/**
	 * 余额充值
	 * @param array $recharge
	 * @param string $className
	 */
	public function rechargeMoney()
	{
		$recharge = $this->data;
		
		$userId = $recharge['mb_id'];
		
		$isHas = $this->modelObj
			->field(BalanceModel::$id_d.','.BalanceModel::$accountBalance_d.','.BalanceModel::$lockBalance_d)
			->where(BalanceModel::$userId_d.'= %d', $userId)
			->order(BalanceModel::$id_d.' DESC ')
			->find();
		
		$this->source = $isHas;
		
		$status=$this->addData();
		
		if (!$this->traceStation($status)) {
			$this->rollback();
			return false;
		}
		$key = $userId.'_'.$recharge['trade_no'];
		Cache::getInstance('', ['expire' => 1440])->set($key, $recharge['trade_no']);
		
		$this->modelObj->commit();
		return true;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAdd() :array
	 */
	protected function getParseResultByAdd() :array
	{
		$money = $this->getBalanceMoney();
		
		$refundMoney = $this->data['price'];
		
		$data = [
			BalanceModel::$userId_d => $this->data['user_id'],
			BalanceModel::$description_d => '退货退款',
			BalanceModel::$changesBalance_d => $refundMoney,
			BalanceModel::$accountBalance_d => $money + $refundMoney,
			BalanceModel::$type_d => 1,
			BalanceModel::$lockBalance_d => 0,
			BalanceModel::$rechargeTime_d => time(),
			BalanceModel::$status_d => 3,
			BalanceModel::$modifyTime_d => 0
		];
		return $data;
	}
}