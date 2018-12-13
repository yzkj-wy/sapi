<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreSellerLogic;

// 登录不验证的控制器
class PublicController
{
    use IsLoginTrait;
    use InitControllerTrait;
    /**
     * 架构方法
     */              
    public function __construct(array $args =[])
    {   
    	$this->init();
        $this->args = $args;
        $this->logic = new StoreSellerLogic($args);
        
    }  
    // 登录验证
    public function login()
    {    
        $status = $this->logic->loginCheck(); 
       
        $this->objController->promptPjax($status['status'],$status['message']);
        $this->objController->ajaxReturnData($status['data'],1,$status['message']); 
    }
    //退出登录
    public function exitLogon()
    {    
        $status = $this->logic->getExitLogon(); 
        $this->objController->promptPjax($status['status'],$status['message']);
        $this->objController->ajaxReturnData($status['data'],1,$status['message']); 
    }
}