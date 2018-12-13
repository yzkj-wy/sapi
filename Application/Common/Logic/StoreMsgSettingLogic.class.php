<?php
namespace Common\Logic;

use Common\Model\StoreMsgSettingModel;

/**
 * 店主消息接收设置
 * @author Administrator
 * @version 1.0.0
 */
class StoreMsgSettingLogic extends AbstractGetDataLogic
{
    /**
     * 消息设置key
     * @var string
     */
    private $tmplKey;
    /**
     * 构造方法
     * @param array $data
     * @param string $tmplKey
     */
    public function __construct(array $data = [], $tmplKey = '')
    {
        $this->data = $data;
        
        $this->tmplKey = $tmplKey;
        
        $this->modelObj = StoreMsgSettingModel::getInitnation();
    }
    
    /**
     * 获取店主铺消息配置
     */
    public function getResult()
    {
        $key = $this->tmplKey.'_'.$this->data['store_id'].'_s';
        $setting = S($key);
        
        if (empty($setting)) {
            $setting = $this->modelObj->where(StoreMsgSettingModel::$smtCode_d.'="%s" and '.StoreMsgSettingModel::$storeId_d.'=%d', [ $this->tmplKey, $this->data['store_id']])->find();
        } else {
            return $setting;
        }
        
        if (empty($setting)) {
            return [];
        }
        
        S($this->tmplKey, $setting, 30);
        
        return $setting;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return StoreMsgSettingModel::class;
    }

}