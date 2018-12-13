<?php
namespace Common\Logic;

use Common\Model\StoreLevelByPlatformModel;
use Think\Cache;

class StoreLevelByPlatformLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        
        $this->modelObj = new StoreLevelByPlatformModel();
        
        $this->splitKey = $split;
        
//         $this->covertKey = StoreLevelByPlatform;
    }
    
    /**
     * 获取店品牌数据
     */
    public function getResult()
    {
        $field = [
            StoreLevelByPlatformModel::$id_d,
            StoreLevelByPlatformModel::$levelName_d,
        ];
        
        return $this->getDataByOtherModel($field, StoreLevelByPlatformModel::$id_d);
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return StoreLevelByPlatformModel::class;
    }
    
    /**
     * 店铺会员等级
     */
    public function getStoreLevelDataCache()
    {
        $cacheObj = Cache::getInstance('', ['expire' => 160]);
        
        $data = $cacheObj->get('platform_item');
        
        if (empty($data)) {
            $data = $this->modelObj->select();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        $cacheObj->set('platform_item', $data);
        
        return $data;
    }
}