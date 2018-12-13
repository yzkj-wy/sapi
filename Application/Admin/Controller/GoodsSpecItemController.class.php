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
use Common\Logic\GoodsSpecItemLogic;
use PlugInUnit\Validate\CheckParam;
Use Common\TraitClass\InitControllerTrait;
Use Common\TraitClass\ThumbNailTrait;
Use Common\TraitClass\IsLoginTrait;
use Admin\Logic\GoodsSpecLogic;
use Common\Tool\Tool;
/**
 * 商品后台管理
 * @author Administrator
 */
class GoodsSpecItemController
{
    use InitControllerTrait;
    
    use ThumbNailTrait;
    
    use IsLoginTrait;
   
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsSpecItemLogic($args);
    }

    /**
     * 获取规格项
     */
    public function getSpecItem()
    {
        $check = new CheckParam($this->logic->checkValidateBySpec(), $this->args);

        $this->objController->promptPjax($check->checkParam(), $check->getErrorMessage());
    
        $specData = $this->logic-> getSpecData();

        $this->objController->ajaxReturnData($specData);
    }
    
    /**
     * 获取 规格项列表
     */
    public function getSpecItemList()
    {
        // 实例化快递逻辑处理（用于组装搜索条件）
        $goodsSpecLogic = new GoodsSpecLogic($this->args, $this->logic->getSplitKeyBySpec());
       
        // 链接parseString 可以静态调用此类的方法
        Tool::connect('parseString');
        
        //获取 快递的筛选条件
        $where = $goodsSpecLogic->getAssociationCondition();
        
        $this->logic->setAssociationWhere($where);
        
        $data = $this->logic->getDataList();
        
        $this->objController->promptPjax($data['data']);
        
        $goodsSpecLogic->setData($data['data']);
        
        $data['data'] = $goodsSpecLogic->getDataBySpecItem();
        
        $this->objController->ajaxReturnData($data);
    }
    
    /**
     * 编辑规格项
     */
    public function editSpecItem()
    {
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
        
        $data = $this->logic->getFindOne();
        
        $this->objController->promptPjax($data);
        
        $goodsSpecLogic = new GoodsSpecLogic($data, $this->logic->getSplitKeyBySpec());
        
        $classData = $goodsSpecLogic->getClassIdBySpecItem();
        
        $this->objController->ajaxReturnData([
            'spec_item' => $data,
            'class_id'  => $classData,
        ]);
    }
    
    /**
     * 保存编辑
     */
    public function saveSpecItem()
    {
        $this->objController->promptPjax($this->logic->CheckMessageSaveBySpecialItem(), $this->logic->getErrorMessage());
        
        $this->objController->updateClient($this->logic->saveListBySpec(), "更新");
    }
    
    /**
     * 添加规格项
     */
    public function addSpecialItem()
    {
        $this->objController->promptPjax($this->logic->CheckMessageBySpecialItem(), $this->logic->getErrorMessage());
        
        $status = $this->logic->addSpecItem();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData($status);
    }
}