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

use Admin\Model\GoodsAttrModel;
use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\GoodsAttributeLogic;
use PlugInUnit\Validate\CheckParam;
use Common\Logic\GoodsAttrLogic;

/**
 * ajax 获取商品属性
 */
class AjaxGetAttributeController
{
    use InitControllerTrait;
    
    use IsLoginTrait;

    /**
     * 构造方法
     * 
     * @param array $args            
     */
    public function __construct(array $args = [])
    {
        
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new GoodsAttributeLogic($args);
    }

    /**
     * 获取商品属性
     */
    public function ajaxGetDataList()
    {
        $data = $this->logic->getNoPageList();
        
        $this->objController->ajaxReturnData($data);
    }

    /**
     * 商品属性显示列表
     */
    public function ajaxGetAttributeInput()
    {
        $this->objController->promptPjax(! empty($_SESSION['insertId']));
        
        $checkObj = new CheckParam($this->logic->checkMessageByClassId(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        // 获取商品属性数据
        
        $goodsAttributeData = $this->logic->getAttributeByTypeId();
        
        $this->objController->promptPjax($goodsAttributeData, $this->logic->getErrorMessage());
        
        $goodsAttrModel = new GoodsAttrLogic($goodsAttributeData, $this->logic->getPrimaryKey());
        
        Tool::connect('parseString');
        
        // 生成HTML数据
        $htmlString = $goodsAttrModel->buildHtmlString();
        
        $this->objController->ajaxReturnData($htmlString);
    }

    /**
     * 获取根据第三级分类获取商品属性
     * 
     * @param classid
     */
    public function getAttributeByThreeGoodsClass()
    {
        $checkObj = new CheckParam($this->logic->getMessageByGetAttribute(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $data = $this->logic->getAttributeByClass();
        
        $this->objController->ajaxReturnData($data);
    }
}