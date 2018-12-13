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
use Common\Logic\StoreAddressLogic;
use Common\Logic\StorePersonLogic;
use Common\Logic\StoreGradeLogic;
use Common\Logic\StoreInformationLogic;
use Common\Logic\StoreManagementCategoryLogic;
use Common\Logic\StoreClassLogic;
use Common\Logic\RegionLogic;
use Common\Logic\GoodsClassLogic;
/**
*个人入驻
**/
class PersonalAdmissionController 
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
        
    }
    public function index(){
        var_dump($this->logic);exit;
    }
    /**
     * 个人店铺申请第一步店铺以及联系人信息
     */
    public function shopsAndContacts()
    {   
        $this->person = new StorePersonLogic($this->args);
        $this->address = new StoreAddressLogic($this->args);
        //验证数据
        $status = $this->address->checkParam(); 
        $this->objController->promptPjax($status, $this->address->getErrorMessage());
        //添加店铺地址
        $storeAddress = $this->address->addAddress();
        $this->objController->promptPjax($storeAddress['status'],$storeAddress['message']);
       
        //添加个人申请   
        $person = $this->person->addData($post);
        $this->objController->promptPjax($person['status'],$person['message']);
        $this->objController->ajaxReturnData($person['data'],1,$person['message']);        
    }
    /**
     * 个人店铺申请第二步结算(银行卡)账号信息
     */
    public function settlement()
    {   $this->person = new StorePersonLogic($this->args);
        //修改个人入驻申请表
        $person = $this->person->saveBank();
        $this->objController->promptPjax($person['status'],$person['message']);
        $this->objController->ajaxReturnData($person['data'],1,$person['message']);
    }
    /**
     * 个人店铺申请第三步店铺经营信息
     */
    public function businessInformation()
    {   
        $this->args['status'] = 0;   
        $this->information = new StoreInformationLogic($this->args);
        $this->management = new StoreManagementCategoryLogic($this->args);
          //验证数据
        $status = $this->information->checkParam();
        $this->objController->promptPjax($status, $this->information->getErrorMessage());        
          //添加店铺经营类目表
        $status = $this->management->checkParam(); 
        $this->objController->promptPjax($status, $this->information->getErrorMessage());
        $management = $this->management->addData();
        $this->objController->promptPjax($management['status'],$management['message']);
        //添加店铺经营信息数据
        $information = $this->information->addData();
        $this->objController->promptPjax($information['status'],$information['message']);
        $this->objController->ajaxReturnData($information['data'],1,$information['message']);
    }
    //获取店铺等级
    public function getStoreGrade(){
        $this->grade = new StoreGradeLogic();
        $grade = $this->grade->getStoreGrade();

        
        $this->objController->promptPjax($grade['status'],$grade['message']);
        $this->objController->ajaxReturnData($grade['data'],1,$grade['message']);
    }
    //获取店铺分类
    public function getStoreClass(){
        $this->class = new StoreClassLogic();
        $class = $this->class->getStoreClass();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],1,$class['message']);
    }
    //获取省份
    public function getProv(){
        $this->prov = new RegionLogic();
        $prov = $this->prov->getProv();
        $this->objController->promptPjax($prov['status'],$prov['message']);
        $this->objController->ajaxReturnData($prov['data'],1,$prov['message']);
    }
    //获取下级地区
    public function lowerlevelArea(){
        $this->prov = new RegionLogic($this->args);
        $prov = $this->prov->getUpDataById();
        $this->objController->promptPjax($prov['status'],$prov['message']);
        $this->objController->ajaxReturnData($prov['data'],1,$prov['message']);
    }
    //获取商品分类
    public function getGoodsClass(){
        $this->goodsClass = new GoodsClassLogic();
        $class = $this->goodsClass->getGoodsTopClass();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],1,$class['message']);
    }
     //获取商品下级分类
    public function getGoodsNextClass(){
        $this->goodsClass = new GoodsClassLogic($this->args);
        $class = $this->goodsClass->getGoodsNextClass();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],1,$class['message']);
    }
}