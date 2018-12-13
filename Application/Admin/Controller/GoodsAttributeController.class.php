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

use Common\Logic\GoodsAttributeLogic;
use Admin\Model\GoodsAttributeModel;
use Common\Logic\GoodsTypeLogic;
use Common\Model\BaseModel;
use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use PlugInUnit\Validate\CheckParam;
use Admin\Model\GoodsTypeModel;


class GoodsAttributeController
{

    use IsLoginTrait;
    use InitControllerTrait;

    public function __construct(array $args =[])
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsAttributeLogic($args);
    }

    /**
     * ajax 获取数据(属性分页列表)
     */
    public function ajaxGetData()
    {
        //获取分页结果
        $result = $this->logic->getPageResult();
        $this->objController->promptPjax($result);

        Tool::connect('parseString');
        //关联商品类型名称
        $goodsType = new GoodsTypeLogic($result['rows'],GoodsAttributeModel::$typeId_d);
        $result['rows'] = $goodsType->getDataByGoodsAttribute();
        sort($result['rows']);

        $this->objController->ajaxReturnData($result);
    }
    
    /**
     * 添加属性 
     */
    public function addAttr()
    {

        //验证数据
        $this->checkParamByClient();

        //调用添加方法
        $result = $this->logic->addAttr();

        //接口返回数据
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);

    }

    /**
     * 获取 商品属性详情
     */
    public function showInfo()
    {

        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        //获取单个商品属性信息
        $row[0] = $this->logic->getGoodsAttrInfo();
        $this->objController->promptPjax($row[0], '不存在的属性');

        Tool::connect('parseString');
        //关联查询，根据type_id的到类型名字
        $goodsType = new GoodsTypeLogic($row,GoodsAttributeModel::$typeId_d);
        $row= $goodsType->getDataByGoodsAttribute();
        sort($row);

        $this->objController->ajaxReturnData($row);

    }
    
    /**
     * 保存编辑 
     */
    public function saveEditAttribute()
    {
        
        $this->checkNumber[] = 'id';
        
        $this->checkIsExits[] = 'id';
        
        $this->saveOrAddAuxiliary('save', '更新');
        
    }
    
    /**
     * 删除 属性 
     */
    public function removeAttr()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        $result = $this->logic->deleteAttr();

        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData([],1,'删除成功');

    }

    /**
     * 更新属性
     */
    public function editAttr()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $this->checkParamByClient();

        $result = $this->logic->saveAttr();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);
    }

    /**
     * 改变筛选状态
     */
    public function changeIndex()
    {
        //验证数据
        $checkObj = new CheckParam($this->logic->getChangeMessageNotice(), $this->args);

        $status   = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());


        //调用添加方法
        $result = $this->logic->changeIndex();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData([]);
    }

    /**
     * 检索查询
     */
    public function getIndexData()
    {

        $result = $this->logic->validateType();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //获取分页结果
        $result = $this->logic->getPageResult(true);
        $this->objController->promptPjax($result);

        Tool::connect('parseString');
        //关联商品类型名称
        $goodsType = new GoodsTypeLogic($result['rows'],GoodsAttributeModel::$typeId_d);
        $result['rows'] = $goodsType->getDataByGoodsAttribute();
        sort($result['rows']);

        $this->objController->ajaxReturnData($result);
    }
}