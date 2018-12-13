<?php
namespace Admin\Controller;

use Common\Logic\ExpressLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;

/**
 * 快递列表
 * @author 王强
 * @version 1.0.1
 */
class ExpressController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {   $this->init();
        $this->isNewLoginAdmin();
    
        $this->args = $args;
    
        $this->logic = new ExpressLogic($this->args);
    }
    
    /**
     * 快递列表
     */
    public function freightList()
    {
        $freight = $this->logic->freightList();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    /**
     * 已启用快递列表
     */
    public function alreadyOpened()
    {
        $freight = $this->logic->freightAlreadyOpened();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //搜索快递公司
    public function frightSearch(){
        $freight = $this->logic->freightSearch();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //搜索快递公司
    public function expressSearch(){
        $freight = $this->logic->getExpressSearch();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //新增快递公司
    public function frightAdd(){
        //验证字段
        $this->checkParamByClient(); 
        
        $freight = $this->logic->freightAdd();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //是否启用
    public function statusSave(){
        //验证字段
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $freight = $this->logic->statusSave();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //是否常用
    public function orderSave(){
        //验证字段
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $freight = $this->logic->orderSave();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //是否支持服务站配送
    public function ztStateSave(){
        //验证字段
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $freight = $this->logic->ztStateSave();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
}