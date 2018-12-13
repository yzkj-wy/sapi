<?php
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreMemberLevelLogic;
use Common\Logic\StoreLevelByPlatformLogic;
use Common\Tool\Tool;

/**
 * 店铺等级
 * @author 王强
 */
class StoreMemberLevelController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new StoreMemberLevelLogic($args);
    }
    
    /**
     * 会员列表
     */
    public function levelList()
    {
        $platformLogic = new StoreLevelByPlatformLogic($this->args, $this->logic->getSplitKeyByLevelId());
         
        Tool::connect('parseString');
        
        $where = $platformLogic->getAssociationCondition();
        
        $this->logic->setAssociationWhere($where);
        
        $data = $this->logic->getDataList();
        
        $this->objController->promptPjax($data['data']);
        
        $platformLogic->setData($data['data']);
        
        $data['data'] = $platformLogic->getResult();
        
        $this->objController->ajaxReturnData($data);
    }
    
    /**
     * 编辑
     */
    public function edit() 
    {
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
        
        $data = $this->logic->getFindOne();
        
        $this->objController->promptPjax($data);
        
        $this->objController->ajaxReturnData([
            'store' => $data,
        ]);
    }
    
    /**
     * 保存编辑
     */
    public function save()
    {
        $this->checkParamByClient();
        
        $status = $this->logic->saveData();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData('');
    }
    
    /**
     * 添加会员等级
     */
    public function add()
    {
        $this->checkParamByClient();
        
        $status = $this->logic->addData();
        
        $this->objController->updateClient($status, '保存');
    }
}