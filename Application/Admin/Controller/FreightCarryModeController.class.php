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
use Common\Logic\FreightsLogic;
use Common\Logic\ExpressLogic;
use Common\Logic\FreightSendLogic;
use Common\Logic\RegionLogic;

/**
 * 运费 控制器 
 */
class FreightCarryModeController
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
        $this->isNewLoginAdmin();
    
        $this->args = $args;
    
        $this->logic = new FreightModeLogic($args);
    
        $this->init();
    }
    
    public function ajaxGetFreightCarry()
    {
       
        $comment = $this->logic->getComment();
        
        //获取模板
        
        $templateLogic = new FreightsLogic($this->args, $this->logic->getFreightIdKey());
        
        Tool::connect('parseString');
        
        $where = $templateLogic->getAssociationCondition();
       
        $this->logic->setAssociationWhere($where);
        
        $data = $this->logic->getDataList();
       
        Tool::connect('parseString');
        
        $templateLogic->setData($data['data']);
        
        $data['data'] = $templateLogic->getTemplateDataByMode();
       
        //获取快递方式
        $expressLogic = new ExpressLogic($data['data'], $this->logic->getFreightCarryMode());
         
        $data['data'] = $expressLogic->getTemplateDataByMode();
        
        $this->objController->assign('notes', $comment);
        
        $this->objController->assign('data', $data);
        
        $this->objController->display();
    }
    
    /**
     * 首页
     */
    public function index()
    {
        $message = $this->logic->getSerachMessage();
        
        $this->objController->assign('tabComment', $this->logic->tableComment());
        
        $this->objController->assign('rule', json_encode($this->logic->getSerachValidate()));
        
        $this->objController->assign('comment', $this->logic->getTempMessage());
        
        $this->objController->assign('message', json_encode($message));
        
        $this->objController->display();
    }
    
    /**
     * 添加运费设置 
     */
    public function carryModeSet()
    {
        $comment = $this->logic->getComment();
        
        $this->getExpressAndTemplate();
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->display();
    }
    
    public function selectArea ()
    {
//         $sendModel = BaseModel::getInstance(FreightSendModel::class);
        
        $this->getArea();
    }
    
    /**
     * 公共方法（添加，保存）
     */
    private function getExpressAndTemplate () 
    {
        
        
        $company = (new ExpressLogic([], ''))->getDefaultOpen();
        
        $template = (new FreightsLogic([]))->getTemplate();
        
        $this->objController->assign('rule', json_encode($this->logic->getCheckValidate()));
        
        $this->objController->assign('message', json_encode($this->logic->getMessageNotice()));
        
        $this->objController->assign('company', $company);
        
        $this->objController->assign('template', $template);
    }
    
    /**
     * 添加运送方式 
     */
    public function addMode () 
    {
        $this->commenMethodBySaveOrAdd();
      
        $insertId = $this->logic->add();
        
        $this->objController->promptPjax($insertId, $this->logic->getErrorMessage());
        
        $this->operatorArea($insertId);
    }
    
    /**
     * 添加或保存公共方法
     */
    private function commenMethodBySaveOrAdd()
    {
        $status = $this->logic->checkParam();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
    }
    
    /**
     * 操作地区
     */
    private function operatorArea($insertId = 0)
    {
        //获取包邮地区编号 传递给地区表
        if ($insertId !== 0 && is_numeric($insertId)) {
            $this->args['id'] = $insertId;
        }
        
        $sendLogic = new FreightSendLogic($this->args, $this->logic->getIdBySendSplitKey());
        
        //保存地区
        $saveStatus = $sendLogic->addArea();
        
        $this->objController->promptPjax($saveStatus, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData(array(
            'url' => U('index')
        ));
    }
    
    /**
     * 编辑保存 
     */
    public function saveEdit()
    {   
        $this->commenMethodBySaveOrAdd();
       
        //保存
        $saveStatus = $this->logic->save ();
        
        $this->objController->promptPjax($saveStatus, $this->logic->getErrorMessage());
        
        $this->operatorArea();
    }
    
    /**
     * 编辑 运送方式
     */
    public function edit ()
    {
        $this->objController->errorArrayNotice($this->args);
        
        $comment = $this->logic->getComment();
        
        $data = $this->logic->getFindOne();
        
        $this->objController->promptParse($data);
        
        //获取地区
        //获取包邮地区编号 传递给地区表
        $sendLogic = new FreightSendLogic($data, $this->logic->getIdBySendSplitKey());
        
        $areaData  = $sendLogic->getSendAddress();
       
        $this->getExpressAndTemplate();
        
        $regionLogic = new RegionLogic($areaData);
        
        $regionLogic->setSplitKey($sendLogic->getRegionAddress());
        
        Tool::connect('parseString');
        
        $regData      = $regionLogic->getFreightArea();
        
        $this->objController->assign('regData', $regData);
        
        $this->objController->assign('areaModel', $sendLogic->getModelClassName());
        
        $this->objController->assign('data', $data);
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->display();
    }
}