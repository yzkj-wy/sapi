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
use Common\Logic\WaybillLogic;
use Common\Logic\ExpressLogic;
use Common\Tool\Tool;
use Common\TraitClass\EditStatusTrait;
use Common\Logic\WaybillPrintItemLogic;
use Common\Logic\WaybillPrintDataLogic;

/**
 * 运单控制器
 * @author 王强
 */
class WaybillController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use EditStatusTrait;
    
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new WaybillLogic($args);
    
        $this->init();
    }
    
    /**
     * 运单列表
     */
    public function waybillList()
    {
        $this->objController->assign('tabName', $this->logic->tableComment());
        
        $this->objController->assign('comment', $this->logic->getComment());
        
        $this->objController->display();
    }
    
    /**
     * ajax 获取运单列表
     */
    public function ajaxGetWaybillList()
    {
        $expressLogic = new ExpressLogic($this->args, $this->logic->getExpressSplitKey());
        
        Tool::connect('parseString');
        
        $where = $expressLogic->getAssociationCondition();
        
        $image = C('image_type');
        
        $this->logic->setAssociationWhere($where);
        
        $data = $this->logic->getDataList();
       
        $this->objController->promptPjax($data);
        
        $expressLogic->setData($data['data']);
        
        $data['data'] = $expressLogic->getResult(); 
       
        $this->objController->assign('data', $data);
        
        $this->objController->assign('comment', $this->logic->getComment());
        
        $this->objController->assign('imageType', $image);
        
        $this->objController->assign('jsonImageType', json_encode($image));
        
        $this->objController->assign('expressModel', $expressLogic->getModelClassName());
        
        $this->objController->assign('deleteURL', U('delete'));
        
        $this->objController->display();
    }
    
    /**
     * 设置状态
     */
    public function ajaxSetStatus()
    {
        $status = $this->logic->checkStatusUsable();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $status = $this->logic->saveData();
        
        $this->objController->updateClient($status, '设置');
    }
    
    /**
     * 编辑运单
     */
    public function editWaybill()
    {
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
        
        $data = $this->logic->getFindOne();
        
        $this->addOrSaveCommon();
        
        $this->objController->assign('data', $data);
        
        $this->objController->display();
    }
    
    /**
     * 添加或保存公共方法
     */
    private function addOrSaveCommon()
    {
        $expressLogic = new ExpressLogic([]);
        
        $expressData = $expressLogic->getDefaultOpen();
        
        $this->objController->assign('comment', $this->logic->getModelObj()->getComment());
        
        $this->objController->assign('tabName', $this->logic->tableComment());
        
        $this->objController->assign('expressData', $expressData);
        
        $this->objController->assign('expressModel', $expressLogic->getModelClassName());
        
        $this->objController->assign('radio', C('status_c'));
        
        $this->objController->assign('message', json_encode($this->logic->getMessageNotice()));
        
        $this->objController->assign('rule', json_encode($this->logic->getCheckValidate()));
        
    }
     
    /**
     * 保存 
     */
    public function save()
    {
        $status = $this->logic->checkParam();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $status= $this->logic->saveData();
        
        $this->objController->updateClient($status, '保存');
        
        $this->objController->updateClient([
            'url' => U('waybillList')
        ], '保存');
    }
    
    /**
     * 添加页面
     */
    public function addWaybill()
    {
        $this->addOrSaveCommon();
        
        $this->objController->display();
    }
    
    /**
     * 添加
     */
    public function add()
    {
//        new Event('parperParam', $this);//
       
       $status = $this->logic->checkParam();
       
       $this->objController->promptPjax($status, $this->logic->getErrorMessage());
       
       $status= $this->logic->addData();
       
       $this->objController->updateClient($status, '添加');
       
       $this->objController->updateClient([
           'url' => U('waybillList')
       ], '保存');
       
    }
    
    /**
     * 删除
     */
    public function delete()
    {
        $status = $this->logic->checkIdIsNumric();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $status = $this->logic->delete();
        
        $this->objController->updateClient($status, '删除失败');
    }
    
    /**
     * 设计模板
     */
    public function designWaybillTemplate()
    {
        $data = $this->logic->getFindOne();
       
        $printItem = (new WaybillPrintItemLogic());
        
        $printData = $printItem->getNoPageListCache();
        
        $waybillPrintData = new WaybillPrintDataLogic($printData, $this->logic->getSplitKeyById());
        
        $this->objController->assign('data', json_encode($data));
        
        $this->objController->assign('printModel', $printItem->getModelClassName());
        
        $this->objController->assign('printData', $printData);
        
        
        $this->objController->display();
    }
    
}