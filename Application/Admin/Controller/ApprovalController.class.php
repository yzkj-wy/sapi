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
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreAuthGroupAccessLogic;
use Common\Logic\StoreAuthGroupLogic;
use Common\Logic\StoreAuthMenuLogic;

/**
 * 权限菜单
 * @author 王强
 * @version 1.0
 */
class ApprovalController
{
	use InitControllerTrait;
	
	use IsLoginTrait;
	
	/**
	 * 
	 * @param array $args
	 */
    public function __construct(array $args = [])
    {
        $this->args = $args;
        
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->logic = new StoreAuthGroupAccessLogic($this->args);
    }
    
    /**
     * 获取权限
     */
    public function getApproval()
    {
        $accessGroup = $this->logic->getResult();
      
        $this->objController->promptPjax($accessGroup, $this->logic->getErrorMessage());
        
        $storeAuth = new StoreAuthGroupLogic($accessGroup, $this->logic->getSplitKeyByGroupId());
        
        //获取验证规则数据
        $rule = $storeAuth->getResult();
     
        $this->objController->promptPjax($rule, $storeAuth->getErrorMessage());
        
        //获取权限菜单
        $menuLogic = new StoreAuthMenuLogic($rule);
        
        $menu = $menuLogic->getResult();
        
        $this->objController->ajaxReturnData($menu);
    }
    
}