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

use Common\Logic\FreightConditionLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\SearchTrait;
use Common\Logic\FreightAreaLogic;
use Common\Logic\RegionLogic;
use Common\Tool\Tool;

/**
 * 运费条件 控制器
 * @author 王强
 */
class FreightConditionController
{
    use InitControllerTrait;
    use IsLoginTrait;
    use SearchTrait;
    
    private $areaURL = [
        '/adminprov.php/FreightCondition/saveEdit',
        '/adminprov.php/FreightCondition/addArea'
    ];
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->isNewLoginAdmin();
    
        $this->args = $args;
    
        $this->logic = new FreightConditionLogic($args);
    
        $this->init();
    }
    
    /**
     * 指定条件 包邮
     */
    public function specifyCondition()
    {
        $this->objController->errorArrayNotice($this->args);
    
        $this->edit();
    
        $this->objController->display();
    }
    
    private function edit ()
    {
        $array = array();
    
        $array = $this->logic->getFreightOneData();
         
        //包邮地区
    
        $areaLogic = new FreightAreaLogic($array);
    
        $comment = $this->logic->getComment();
    
        //获取包邮地区编号 传递给地区表
        $areaData  = $areaLogic->getAddressArea();
        
        $regionLogic = new RegionLogic($areaData);
    
        $regionLogic->setSplitKey($areaLogic->getMialAreaSplitKey());
    
        Tool::connect('parseString');
        $regData      = $regionLogic->getFreightArea();
    
        $this->objController->assign('areaModel', $areaLogic->getModelClassName());
    
        $this->objController->assign('regData', $regData);

        $this->objController->assign('add', (int)$this->logic->getCurretIdIsEmpty() );
    
        //包邮数据
        $this->objController->assign('data', $array);
    
        $this->objController->assign('areaURL', $this->areaURL);
    
        $this->objController->assign('tabComment', $this->logic->tableComment());
    
        $this->objController->assign('rules', json_encode($this->logic->getCheckValidate()));
    
        $this->objController->assign('message', json_encode($this->logic->getMessageNotice()));
    
        $this->objController->assign('comment', $comment);
    }
    
    /**
     * 保存编辑
     */
    public function saveEdit()
    {
        $this->checkValidate();
       
        //保存
        $saveStatus = $this->logic->saveCondition ();
         
        $this->objController->promptPjax($saveStatus, $this->logic->getErrorMessage());
        
        $this->addAreaToDb();
       
    
    }
    
    /**
     * 检测参数
     */
    private function checkValidate()
    {
        $status = $this->logic->checkParam();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
    }
    
    /**
     * 保存地区
     */
    private function addAreaToDb($inertId = 0)
    {   
        //保存地区
        
        $this->args['id'] = $inertId === 0  ? $this->args['id'] : $inertId;
        
        $areaLogic = new FreightAreaLogic($this->args, $this->logic->getI);
        $saveStatus = $areaLogic->addArea();
        
        $this->objController->promptPjax($saveStatus, $areaLogic->getErrorMessage());
        
        $this->objController->ajaxReturnData(array(
            'url' => U('FreightTemplate/lists')
        ), '操作');
    }
    /**
     * 获取地区
     */
    public function selectArea ()
    {
         //保存地区
        $this->getArea();
    }
    
    
    /**
     * 添加包邮
     */
    public function addArea()
    {
        $this->checkValidate();
        
        $insertId = $this->logic->addCondition();
    
        $this->objController->promptPjax($insertId, $this->logic->getErrorMessage());
        
        $this->addAreaToDb($insertId);
    }
}