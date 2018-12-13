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
use Common\TraitClass\SearchTrait;
use Common\Logic\FreightConditionLogic;
use Common\Logic\RegionLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 运费条件 控制器
 * @author 王强
 */
class FreightConditionController{
    use InitControllerTrait;
    use IsLoginTrait;
    use SearchTrait;
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = []){   
        $this->init();
        $this->isNewLoginAdmin();
        
        
        $this->args = $args;   
           
    }
    //设置 包邮地区
    public function setFreightCondition(){
    	
    	$this->logic = new FreightConditionLogic($this->args);
    	$checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
        $freight = $this->logic->setFreight();
        $this->objController->promptPjax($freight);
        $this->objController->ajaxReturnData('');
    }
    //获取包邮地址
    public function getFreightCondition(){ 
        $this->logic = new FreightConditionLogic($this->args);
        $freight = $this->logic->getFreightInfo();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],1,$freight['message']);
    }
    //获取地区
    public function getArea(){
        $this->region = new RegionLogic($this->args);
        $data = $this->region->getProvAndCity(6);
        $this->objController->promptPjax($data['status'],$data['message']);
        $this->objController->ajaxReturnData($data['data'],1,$data['message']);
    }
}