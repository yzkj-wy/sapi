<?php
namespace Common\Logic;

use Common\Model\SystemConfigModel;


/**
 * 店铺逻辑处理
 * @author Administrator
 */
class SystemConfigLogic extends AbstractGetDataLogic
{
  
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct( array $args,array $storeInfo = [] )
    {
        $this->data = $args;

        $this->storeInfo = $storeInfo;

        $this->modelObj = new SystemConfigModel();

    }

    /**
     * 实现方法
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
       
    }

    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return StoreModel::class;
    }

    //获取公司配置
    public function getConfig(){
    	$where['class_id'] = 66;
    	$field = "id,key,config_value";
    	$data = $this->modelObj->getConfigByWhere($where,$field);
    	return $data;
    }
 
}