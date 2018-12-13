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

use Common\Logic\FreightsLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;

/**
 * 运费模板列表
 * @author 王强
 * @version 1.0.1
 */
class FreightsController
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
    
        $this->logic = new FreightsLogic($this->args);
    }
    //运费模板列表
    public function freightList(){
        $list = $this->logic->getFreightList();
        $this->objController->promptPjax($list['status'],$list['message']);
        $this->objController->ajaxReturnData($list['data'],1,$list['message']);
    }
    //添加运费模板
    public function freightAdd(){
    	$this->checkParamByClient();
    	
        $freight = $this->logic->freightAdd();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],$freight['status'],$freight['message']);
    }
    //编辑运费模板
    public function freightSave(){ 
    	
    	$this->checkParamByClient(); 
        $freight = $this->logic->freightSave();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],$freight['status'],$freight['message']);
    }
    //删除运费模板
    public function freightDel(){
        $freight = $this->logic->freightDel();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],$freight['status'],$freight['message']);
    }
    //搜索运费模板
    public function freightSearch(){
        $freight = $this->logic->freightSearch();
        $this->objController->promptPjax($freight['status'],$freight['message']);
        $this->objController->ajaxReturnData($freight['data'],$freight['status'],$freight['message']);
    }
    //运费模板详情
    public function freightDetail(){
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $list = $this->logic->getFreightDetail();
        $this->objController->promptPjax($list['status'],$list['message']);
        $this->objController->ajaxReturnData($list['data'],1,$list['message']);
    }
    
    /**
     * 获取运费列表
     */
    public function getFreightList()
    {
    	$this->objController->ajaxReturnData($this->logic->getResult());
    }
    
}