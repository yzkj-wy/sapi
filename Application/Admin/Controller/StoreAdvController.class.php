<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王波 <18302817805>
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
use Common\Logic\StoreAdvLogic;
use PlugInUnit\Validate\CheckParam;
/**
*广告管理
**/
class StoreAdvController 
{
    use IsLoginTrait;
    use InitControllerTrait;
    // /**
    //  * 架构方法
    //  */              
    public function __construct(array $args =[])
    {   $this->init();
        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new StoreAdvLogic($this->args);
    }
    //  /**
    //  * 广告列表
    //   */
    public function adList(){ 

        //获取广告列表 
        $account = $this->logic->getAdList(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告--添加
    //   */
    public function adAdd(){ 
        //验证数据 
        // $status = $this->logic->checkParam();
        // $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $account = $this->logic->getAdAdd(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告--修改
    //   */
    public function adSave(){ 
    //验证数据 
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage()); 
        
        $checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage()); 
        $account = $this->logic->getAdSave(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告--删除
    //   */
    public function adDel(){ 
    //验证数据 
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());  
        $account = $this->logic->getAdDel(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告--搜索
    //   */
    public function adSearch(){ 
        $account = $this->logic->getAdSearch(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告--获取单条
    //   */
    public function adInfo(){ 
    //验证数据 
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());  
        $account = $this->logic->getAdInfo(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']); 
    }
    //  /**
    //  * 广告--是否显示
    //   */
    public function adReveal(){ 
    //验证数据 
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());  
        $account = $this->logic->getAdReveal(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
}