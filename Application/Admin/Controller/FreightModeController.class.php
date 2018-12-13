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

use Common\TraitClass\SearchTrait;
use Common\Tool\Tool;
use Common\Logic\FreightModeLogic;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;

/**
 * 运费 控制器 
 */
class FreightModeController
{
    use InitControllerTrait;
    use IsLoginTrait;
    use SearchTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {   
    	$this->init();
        // $this->isNewLoginAdmin(); 
        $this->args = $args;    
        $this->logic = new FreightModeLogic($args);    
    }
    //获取运费设置列表
    public function getFreightModelList(){
    	$status = $this->logic->getFreightModelList();
    	$this->objController->promptPjax($status['status'],$status['message']);
        $this->objController->ajaxReturnData($status['data'],1,$status['message']);
    }
    //搜索运费设置列表
    public function getFreightModelSearch(){ 
    
    	$status = $this->logic->getFreightModelSearch(); 
    	$this->objController->promptPjax($status['status'],$status['message']);
        $this->objController->ajaxReturnData($status['data'],1,$status['message']);
    }
    //添加运费设置
    public function getFreightModelAdd(){
    	//验证数据
    	$this->checkParamByClient();
    	$Freight = $this->logic->getFreightModelAdd();
    	$this->objController->promptPjax($Freight['status'],$Freight['message']);
        $this->objController->ajaxReturnData($Freight['data'],1,$Freight['message']);
    }
    //修改运费设置
    public function getFreightModelSave(){
    	//验证数据 
    	$this->checkParamByClient();
    	$Freight = $this->logic->getFreightModelSave();
    	$this->objController->promptPjax($Freight['status'],$Freight['message']);
        $this->objController->ajaxReturnData($Freight['data'],1,$Freight['message']);
    }
    //删除运费设置 
    public function getFreightModelDel(){
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
    	$Freight = $this->logic->delFreightMode();
    	$this->objController->promptPjax($Freight['status'],$Freight['message']);
        $this->objController->ajaxReturnData($Freight['data'],1,$Freight['message']);
    }
    //获取运费设置
    public function getFreightModelOne(){
       //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage()); 
        $Freight = $this->logic->getFreightModelOne();
        $this->objController->promptPjax($Freight['status'],$Freight['message']);
        $this->objController->ajaxReturnData($Freight['data'],1,$Freight['message']);
    }
}