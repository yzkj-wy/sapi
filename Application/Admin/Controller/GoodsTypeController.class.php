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


use Admin\Model\GoodsAttributeModel;
use Common\Logic\GoodsTypeLogic;
use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;

/**
 * 商品类型控制器
 */
class GoodsTypeController
{

    use IsLoginTrait;

    use InitControllerTrait;

    public function __construct(array $args =[])
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsTypeLogic($args);
    }

    /**
     * 商品类型列表
     */
    public function index()
    {
        //获取分页结果
        $result = $this->logic->getPageResult();
        $this->objController->promptPjax($result);

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 商品添加 
     */
    public function add()
    {
        //验证数据
        $this->checkParamByClient();

        $result = $this->logic->addType();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);
    }

    /**
     * 商品编辑 
     * @param int $id
     */
    public function edit()
    {
        //验证数据
        $this->checkParamByClient();

        $result = $this->logic->saveType();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")],1,'修改成功');
    }

    /**
     * 查看类型详细信息
     * @param int $id
     */
    public function showInfo()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        //获取单个商品类型信息
        $row = $this->logic->getGoodsTypeInfo();
        $this->objController->promptPjax($row, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($row);
    }

    /**
     * 移除商品类型
     * @param int $id 商品类型编号
     */
    public function remove()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        $result = $this->logic->deleteType();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData([],1,'删除成功');
    }

    /**
     * 获取所有的商品类型
     */
    public function allType()
    {

        $result = $this->logic->getAllType();
        $this->objController->ajaxReturnData($result);

    }
}