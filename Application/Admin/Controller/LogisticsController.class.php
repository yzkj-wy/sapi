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
use Common\Logic\SendAddressLogic;
/**
*物流模板
**/
class LogisticsController 
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
        $this->logic = new SendAddressLogic($this->args);
    }
    //发货地址列表
    public function shippingAddressList(){
        
        $address = $this->logic->getAddressList();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //发货地址修改
    public function shippingAddressSave(){
    
        //验证数据
        $this->checkParamByClient(); 
        
        $address = $this->logic->addressSave();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //发货地址添加
    public function shippingAddressAdd(){
        
        //验证数据
    	$this->checkParamByClient();
    	
        $address = $this->logic->addressAdd();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //发货地址删除
    public function shippingAddressDel(){
        
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $address = $this->logic->addressDel();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //发货地址搜索
    public function shippingAddressSearch(){
        
        $address = $this->logic->addressSearch();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //获取已开启仓库
    public function alreadyOpened(){
        $address = $this->logic->getAlreadyOpened();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
    //发货地址详情
    public function shippingAddressDetail(){
         //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $address = $this->logic->getAddressDetail();
        $this->objController->promptPjax($address['status'],$address['message']);
        $this->objController->ajaxReturnData($address['data'],1,$address['message']);
    }
}