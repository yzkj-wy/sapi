<?php
namespace Admin\Controller;

use Common\Tool\Tool;
use Common\Model\BaseModel;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreJoinCompanyLogic;
use Common\Logic\StoreCompanyBankInformationLogic;
use Common\Logic\StoreAddressLogic; 
use Common\Logic\StoreInformationLogic;
use Common\Logic\StoreManagementCategoryLogic;
use Common\TraitClass;
/**
*企业入驻
**/
class EnterpriseEntryController 
{
    use IsLoginTrait;
    use InitControllerTrait;
    /**
     * 架构方法
     */              
    public function __construct(array $args =[])
    {   $this->init();
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
    }
    public function index(){
        var_dump($this->logic);exit;
    }
    /**
     * 企业入驻申请第一步公司及联系人信息
      */
    public function companyAndContactInformation(){ 
        $this->logic = new StoreJoinCompanyLogic($this->args);
        $this->address = new StoreAddressLogic($this->args);
        //添加店铺地址
        //验证数据

        $status = $this->address->checkParam(); 
        $this->objController->promptPjax($status, $this->address->getErrorMessage());
        $storeAddress = $this->address->addAddress();
        $this->objController->promptPjax($storeAddress['status'],$storeAddress['message']);
        //添加企业入驻申请表
        $status = $this->logic->checkParam(); 
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $storeJoinCompany = $this->logic->addData($storeAddress['data']); 
        $this->objController->promptPjax($storeJoinCompany['status'],$storeJoinCompany['message']);
        $this->objController->ajaxReturnData($storeJoinCompany['data'],1,$storeJoinCompany['message']);
    }
    /**
     * 企业入驻申请第二步开户(银行卡)账号信息
     */
    public function storeCompanyBankInformation()
    {   $this->bank = new StoreCompanyBankInformationLogic($this->args); 
        //添加开户(银行卡)账号信息
        $status = $this->bank->checkParam(); 
        $this->objController->promptPjax($status, $this->bank->getErrorMessage());
        $bank = $this->bank->addData();
        $this->objController->promptPjax($bank['status'],$bank['message']);
        $this->objController->ajaxReturnData($bank['data'],1,$bank['message']); 
    }
    /**
     * 企业入驻申请第三步店铺经营信息
     */
    public function businessInformation(){
        $this->args['status'] = 1;   
        $this->information = new StoreInformationLogic($this->args);
        $this->management = new StoreManagementCategoryLogic($this->args);
          //验证数据
        $status = $this->information->checkParam();
        $this->objController->promptPjax($status, $this->information->getErrorMessage());        
          //添加店铺经营类目表
        $status = $this->management->checkParam();
        $this->objController->promptPjax($status, $this->information->getErrorMessage());
        $management = $this->management->addData();
        $this->objController->promptPjax($management['status'],$management['message']);
        //添加店铺经营信息数据
        $information = $this->information->addData();
        $this->objController->promptPjax($information['status'],$information['message']);
        $this->objController->ajaxReturnData($information['data'],1,$information['message']); 
    }
}