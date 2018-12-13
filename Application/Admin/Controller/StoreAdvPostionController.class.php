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
use Common\Logic\StoreAdvPostionLogic;
/**
*广告位置管理
**/
class StoreAdvPostionController 
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
        $this->logic = new StoreAdvPostionLogic($this->args);
    }
    //  /**
    //  * 广告位置列表
    //   */
    public function adPostionList(){ 

        //获取广告位置 
        $account = $this->logic->getAdPostionList(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
    //  /**
    //  * 广告位置列表
    //   */
    public function adPostion(){ 

        //获取广告位置 
        $account = $this->logic->getAdPostion(); 
        $this->objController->promptPjax($account['status'],$account['message']);
        $this->objController->ajaxReturnData($account['data'],1,$account['message']);
    }
}