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

use Common\Logic\StoreCompanyBankInformationLogic;
use Common\Tool\Event;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use PlugInUnit\Validate\CheckParam;

/**
 * 店铺申请详细信息
 * @author 王强
 */
class AjaxGetShopPersonByBankController 
{
	use InitControllerTrait;
    
	use IsLoginTrait;
    
    
    
    public function __construct($args = null)
    {
    	$this->init();
    	
    	$this->isNewLoginAdmin();
    	
    	$this->args = $args;
    	
    	$this->logic =  new StoreCompanyBankInformationLogic($this->args);
    }
    
    /**
     * 开户银行信息等
     */
    public function bankDetailByShopPersion ()
    {
        $checkObj = new CheckParam($this->logic->getMessageValidateStore(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam() ,$checkObj->getErrorMessage());
      
        $result = $this->logic->getResult();
       
        $this->objController->ajaxReturnData($result);
    }
    
    
    /**
     * 个人入驻
     */
    public function personOperationInformation()
    {
        Event::insetListen('person', function(&$param){
            $_SESSION['store_type'] = 1;// 企业入驻
            $param->setType(1);
        });
        
        $this->tmp = 'PersonApproval';
        
        $this->storeOperationInformation();
    }
}