<?php

namespace Admin\Controller;
use Common\Tool\Tool;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\UserAddressLogic;
use Common\TraitClass\NoticeTrait;

/**
 * 订单控制器
 * @author 王强
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class UserAddressController 
{
    use InitControllerTrait;
    
    use IsLoginTrait;

    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new UserAddressLogic($args);
    }
    
    /**
     * 修改收货地址
     * 王波
     */
    public function addressSave()
    {
       
        //验证数据 
        $status = $this->logic->checkParam();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $class = $this->logic->getAddressSave();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],$class['status'],$class['message']);
    }
    /**
     * 修改收货地址--获取单条数据
     * 王波
     */
    public function addressInfo()
    {   
    	//验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $class = $this->logic->getAddressInfo();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],$class['status'],$class['message']);
    }
}