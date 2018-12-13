<?php
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsAttrLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 商品属性
 * @author Administrator
 */
class GoodsAttrController
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
        $this->isNewLoginAdmin();
    
        $this->args = $args;
    
        $this->logic = new GoodsAttrLogic($args);
    }
    
    /**
     * 添加 属性
     */
    public function addGoodsAttribute()
    {
        $checkObj = new CheckParam($this->logic->getMessageByAttr(), $this->args);
    
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    
        $this->objController->promptPjax($_SESSION['insertId'], '添加失败');
    
        $status = $this->logic->addAttributeData();
    
        $this->objController->updateClient($status, '添加');
    }
    

    /**
     * 修改商品属性
     */
    public function editGoodsAttribute()
    {
        $this->objController->promptPjax($this->logic->getMessageByGoodsAttr(), $this->logic->getErrorMessage());
    
        $status = $this->logic->editAttributeData();
    
        $this->objController->updateClient($status, '保存');
    }
}