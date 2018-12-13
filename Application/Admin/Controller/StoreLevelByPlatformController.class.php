<?php
namespace Admin\Controller;

use Common\Logic\StoreLevelByPlatformLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;

/**
 * 平台会员等级控制器
 * @author Administrator
 *
 */
class StoreLevelByPlatformController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new StoreLevelByPlatformLogic($args);
    }
    
    /**
     * 等级列表
     */
    public function levealList()
    {
        $platLevel     = $this->logic->getStoreLevelDataCache();
        
        $this->objController->ajaxReturnData($platLevel);
    }
}