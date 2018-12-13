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

use Admin\Logic\GoodsSpecLogic;
use Common\Logic\GoodsTypeLogic;
use Admin\Model\GoodsSpecModel;
use Common\Logic\GoodsSpecItemLogic;
use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use PlugInUnit\Validate\CheckParam;
use Common\Logic\GoodsLogic;
use Common\Logic\SpecGoodsPriceLogic;
use Common\Logic\StoreBindClassLogic;

/**
 * 规格控制器
 * 
 * @author Administrator
 */
class GoodsSpecController
{
    use IsLoginTrait;
    use InitControllerTrait;

    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new GoodsSpecLogic($args);
    }

    /**
     * 商品规格列表
     */
    public function index()
    {
        // 获取分页结果
        $result = $this->logic->getPageResult();
        $this->objController->promptPjax($result);
        
        // 获取规格项
        $goodsSpecItems = new GoodsSpecItemLogic($result['tmp']);
        $result['tmp'] = $goodsSpecItems->getSpecItems();
        // 调用工具类
        Tool::connect('parseString');
        // 关联规格项
        $goodsType = new GoodsTypeLogic($result['tmp'], GoodsSpecModel::$typeId_d);
        $result['tmp'] = $goodsType->getDataByGoodsAttribute();
        sort($result['tmp']);
        
        $this->objController->ajaxReturnData($result);
    }

    /**
     * 添加商品规格
     */
    public function add()
    {
        // 验证数据
        $this->checkParamByClient();
        
        // 调用添加方法
        $result = $this->logic->addSpec();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        
        // 接口返回数据
        $this->objController->ajaxReturnData([
            'url' => $_SERVER['SERVER_NAME'] . U("index")
        ]);
    }

    /**
     * 修改商品规格
     */
    public function edit()
    {
        // 验证数据
        $this->checkParamByClient();
        
        // 调用添加方法
        $result = $this->logic->saveSpec();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        
        // 接口返回数据
        $this->objController->ajaxReturnData([
            'url' => $_SERVER['SERVER_NAME'] . U("index")
        ]);
    }

    /**
     * 查看规格详细信息
     * 
     * @param int $id            
     */
    public function showInfo()
    {
        // 验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        // 获取单个商品规格信息
        $row = $this->logic->getGoodsSpecInfo();
        $this->objController->promptPjax($row, $this->logic->getErrorMessage());
        
        Tool::connect('parseString');
        // 关联查询，根据type_id的到规格类型名字
        $goodsType = new GoodsTypeLogic($row, GoodsSpecModel::$typeId_d);
        $row = $goodsType->getDataByGoodsAttribute();
        sort($row);
        
        $this->objController->ajaxReturnData($row);
    }

    /**
     * 移除规格
     * 
     * @param int $id            
     */
    public function remove()
    {
        
        // 验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        // 调用删除方法
        $result = $this->logic->deleteSpec();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        
        // 接口返回数据
        $this->objController->ajaxReturnData([]);
    }

    /**
     * 改变显示状态
     */
    public function changeStatus()
    {
        // 验证数据
        $checkObj = new CheckParam($this->logic->getChangeMessageNotice(), $this->args);
        
        $status = $checkObj->checkParam();
        
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
        
        // 调用添加方法
        $result = $this->logic->changeStatus();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        
        // 接口返回数据
        $this->objController->ajaxReturnData([]);
    }

    /**
     * 根据商品分类获取所有规格信息用于添加商品
     */
    public function specInfo()
    {
        // 验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $specData = $this->logic->getGoodsSpecInfo();
        
        $this->objController->promptPjax($specData, '请添加规格组');
        
        // 获取规格项
        $goodsSpecItems = new GoodsSpecItemLogic($specData, $this->logic->getSplitKeyById());
        
        Tool::connect('parseString');
        
        $specItemData = $goodsSpecItems->specItemArrange();
        
        $this->objController->ajaxReturnData($specItemData);
    }
    
    /**
     * 获取规格组（根据商品分类）
     */
    public function getSpecGroup()
    {
        $checkObj = new CheckParam($this->logic->getMessageByGoodsClass(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $storeBindLogic = new StoreBindClassLogic($this->args);
        
        $isAdd = $storeBindLogic->checkPublicationOfCommodityCategories();
        
        $this->objController->promptPjax($isAdd, '请先绑定分类');
        
        $specResult = $this->logic->getSpecGroupByGoodsClass();
        
        $this->objController->ajaxReturnData($specResult);
        
    }
    
    /**
     * 商品规格处理生成回html
     */
    public function getAddContentByGoodsAttribute()
    {
        $checkObj = new CheckParam($this->logic->getBuildBySpecialMessage(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $htmlString = '';
        
        // 获取笛卡尔积
        $data = Tool::connect('ArrayChildren', $_POST['spec'])->parseSpecific();
        
        $id = array();
        
        if (!empty($_SESSION['insertId'])) {
            
            Tool::connect('parseString');
            
            $_SESSION['goodsIdArr'] = (new GoodsLogic(['goods_id' => $_SESSION['insertId']]))->getUnioData();
        }
        // 获取规格表
        $specData = $this->logic->getDataBySpecial();
        
        // 获取规格项
        $goodsSpcItemLogic = new GoodsSpecItemLogic($this->args);
        
        $goodsSpcItemData = $goodsSpcItemLogic->getDataBySpecialItem();
       
        // 组合所有数据
        $specGoodsPriceLogic = new SpecGoodsPriceLogic($specData);
        
//         $specGoodsPriceLogic->setModelByGoods($this->logic->getModelClassName());
        
        $htmlString = $specGoodsPriceLogic->getAttributeBuildGoodsInfo($data, $goodsSpcItemData);
        $this->objController->ajaxReturnData($htmlString);
    }
    
    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect(){
       
        $checkObj = new CheckParam($this->logic->getMessageBySpec(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        
        $specList = $this->logic->getSpecGroupByGoodsClass();
        
        $this->objController->promptPjax($specList);
        
        $goodsSpecItemLogic = new GoodsSpecItemLogic($specList, $this->logic->getSplitKeyById());
        
        Tool::connect('parseString');
        
        $goodsSpecItem = $goodsSpecItemLogic->specItemArrange();
        
        $goodsLogic = new GoodsLogic($this->args);
        
        $goodsIdString = $goodsLogic->innerJoin();
        
        $specGoodsPriceLogic = new SpecGoodsPriceLogic(['id_string' =>$goodsIdString]);
        
        $spriceData = $specGoodsPriceLogic->getSpecItemPrice();
        
        $this->objController->ajaxReturnData([
            'item_id'  => $spriceData,
            'specList' => $goodsSpecItem
        ]);
    }
}