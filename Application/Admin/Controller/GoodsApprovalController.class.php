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

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\EditStatusTrait;
use Common\Logic\GoodsLogic;
use Common\TraitClass\GoodsTrait;
use Common\Tool\Extend\CheckParam;
use Common\Logic\StoreMsgTplLogic;
use Common\Logic\StoreMsgSettingLogic;
use Common\Strategy\SendMessageContent;

/**
 * 入驻用户商品上架审核
 * @author 王强
 * @version 1.0.0
 */
class GoodsApprovalController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use EditStatusTrait;
    
    use GoodsTrait;
    
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new GoodsLogic($args);
        
        $this->init();
    }
    
    /**
     * 审核界面
     */
    public function approval()
    {
        $this->objController->errorArrayNotice($this->args);
        
        $data = $this->logic->getFindOne();
        
        $comment = $this->logic->getComment();
        
        $this->objController->assign('data', $data);
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->display();
    }
    

    /**
     * 通过审核
     */
    public function approvalOk()
    {
        
        $this->checkParam();
        
        $status = $this->logic->approvalGoodsOk();
        
        $this->objController->updateClient($status, '通过审核');
    }
    
    private function checkParam()
    {
        $checkObj = new CheckParam($this->args);
        
        $isNumeric = $checkObj->isNumeric(['id']);
        
        $must = $checkObj->keyExits(['id', 'remark', 'title', 'store_id']);
        
        $this->objController->promptPjax($must && $isNumeric, '数据错误'); 
    }
    
    public function approvalOFF()
    {
        $this->checkParam();
        
        $status = $this->logic->approvalGoodsOFF();
        
        $this->objController->promptPjax($status, '数据有误');
        
        $status = $this->auxiliary();
        
        $this->objController->updateClient($status, '拒绝审核');
    }
    
    /**
     * 辅助方法
     */
    protected function auxiliary()
    {
        //获取消息配置
        $tmplConfigObj = new StoreMsgTplLogic([], 'goods_verify');
        
        $goodsVerfity = $tmplConfigObj->getResult();
        
        //用户消息配置
        $userSettingoObj = new StoreMsgSettingLogic($this->args, 'goods_verify');
        
        $userSetting = $userSettingoObj->getResult();
        
        $userSetting = array_merge($userSetting, $this->args);
        
        //发送消息
        $sendObj = new SendMessageContent($userSetting, $goodsVerfity);
        
        $status = $sendObj->send();
        
        return $status;
    }
}