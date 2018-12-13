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
use Common\Logic\StoreMemberLogic;
use Admin\Logic\UserLogic;
use Common\Tool\Tool;

/**
 * 会员管理
 * @author 王强
 */
class StoreMemberController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        session('store_id',18);
//        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new StoreMemberLogic($args);
    }
    
    /**
     * 会员列表
     */
    public function memberList()
    {
        $userLogic = new UserLogic($this->args, $this->logic->getSplitKeyByUserId());
        
        Tool::connect('parseString');
        
        $where = $userLogic->getAssociationCondition();
        
        $this->logic->setAssociationWhere($where);
        
        $data = $this->logic->getDataList();

        $this->objController->promptPjax($data['data']);
        
        $userLogic->setData($data['data']);
        
        $data['data'] = $userLogic->getResult();
        
        $this->objController->ajaxReturnData($data);
        
    }
}