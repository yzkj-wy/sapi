<?php
namespace Common\Logic;

use Common\Model\GoodsPackageModel;
use Common\Tool\Extend\ArrayChildren;

/**
 * 报关：
 *  回调修改
 * 	修改 订单操作：
 * 	接口文件 导入到项目
 *  接口 配置 修改（自动化）
 *  接口回调处理
 * 报关
 *  延时回调
 *  后续流程
 */
class GoodsPackageLogic extends AbstractGetDataLogic
{
	
	/**
	 * 构造方法
	 * @param array $data
	 */
	public function __construct(array $data, $split= null)
	{
		$this->data = $data;
		
		$this->modelObj = new GoodsPackageModel();
		
		$this->splitKey = $split;
	}
	
	/**
	 * 获取数据
	 */
	public function getResult()
	{
		$field = [
			GoodsPackageModel::$title_d.' as package_title',
			GoodsPackageModel::$id_d
		];
		
		$data = $this->getDataByOtherModel($field, GoodsPackageModel::$id_d);
		
		return $data;
	}
	
	
	/**
	 * getDataByOtherModel附属方法
	 */
	protected function parseReflectionData(array $getData) :array
	{
		
		$getData = (new ArrayChildren($getData))->convertIdByData(GoodsPackageModel::$id_d);
		
		$merage = [];
		
		$data = $this->data;
		
		foreach ($data as $key => & $value) {
			
			if (isset($getData[$value[$this->splitKey]])) {
				$merage = $getData[$value[$this->splitKey]];
			}
			
			$value = array_merge($merage, $value);
		}
		
		return $data;
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName()
	{
		return GoodsPackageModel::class;
	}
}