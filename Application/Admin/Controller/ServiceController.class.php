<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Common\Logic\ServiceLogic;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\OrderPjaxTrait;

use Think\Controller;
use PlugInUnit\Validate\CheckParam;

/**
 * 客服控制器
 * @author 李向红
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class ServiceController
{
    use InitControllerTrait;

    use IsLoginTrait;

    use OrderPjaxTrait;

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
      //$this->isNewLoginAdmin();
        $this->args = $args;

       $this->logic =new ServiceLogic($args);


    }

    /**
     * 客服类型--列表
     */
    public function typeList(){

        $re=$this->logic->logTypeList();
        $this->objController->ajaxReturnData($re);
    }
    /**
    * 客服类型--添加及修改
    */
    public function addtype(){
        //验证数据
    	$this->checkParamByClient();
        $result=$this->logic->logAddtype();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],1,$result['message']);
    }
    /**
     * 根据id获取客服类型详情
     */
    public function getTypeDetailById(){
        $re=$this->logic->loggetTypeDetailById();

        $this->objController->ajaxReturnData($re);
    }



    /**
     * 客服类型删除
     */
    public function deletetype(){
        //验证数据
    	$this->checkParamByClient();
        $result=$this->logic->logdeltype();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],1,$result['message']);
    }




    //客服类型是否启用
    public function typeIsUse(){

       $re=$this->logic->logtypeIsUse();
        if($re){
            $this->objController->ajaxReturnData($re);
        }else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }
    }



/**
 * 客服管理--列表
 */
    public function manageList(){
        $re=$this->logic->logManageList();

        $this->objController->ajaxReturnData($re);

    }
    /**
     * 根据id获取客服详情
     */
    public function getDetailById(){
        $re=$this->logic->loggetDetailById();

        $this->objController->ajaxReturnData($re);
    }

/**
 * 客服管理是否显示
 */
    public function isShow(){
        $re=$this->logic->logIsShow();
        if($re){
            $this->objController->ajaxReturnData($re);
        }else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }
    }
/**
 * 客服管理--是否主客服
 */
    public function isMainServer(){
     $re=$this->logic->logIsMainServer();
        if($re){
            $this->objController->ajaxReturnData($re);
        }elseif($re==false){
            $this->objController->ajaxReturnData('',0,'只能设置一个主客服');
        } else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }

    }
    /**
     * 客服管理--添加和修改
     */
    public function addService(){

    	$checkObj = new CheckParam($this->logic->getCheckValidate(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
        $result=$this->logic->logAddservice(); //添加
        
        $this->objController->ajaxReturnData($result['data'],1,$result['message']);
    }


    /**
     * 客服管理--删除
     */
    public function delService(){

        $result=$this->logic->logdelservice(); //添加

        $this->objController->ajaxReturnData($result['data'],1,$result['message']);

    }
}