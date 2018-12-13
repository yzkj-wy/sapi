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



use Admin\Logic\GoodsClassLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\SearchTrait;
use Common\Tool\Tool;
use Common\Model\BaseModel;
use Admin\Model\GoodsClassModel;
use PlugInUnit\Validate\CheckParam;


/**
 * 商品分类控制器 
 */
class GoodsClassController
{
    use SearchTrait;


    use IsLoginTrait;
    use InitControllerTrait;

    public function __construct(array $args =[])
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsClassLogic($args);
    }

   
    /**
     * 商品分类显示列表【三秒钟缓存】
     */
    public function index()
    {
        $model = BaseModel::getInstance(GoodsClassModel::class);

        Tool::connect('parseString');
        $tree  = $model->buildClass();

        $this->objController->ajaxReturnData($tree);
    }

    /**
     * 添加商品分类
     */
    public function addGoodsClass()
    {
        //验证数据
        $this->checkParamByClient();

        //调用添加方法
        $result = $this->logic->addClass();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);

    }

    public function getClassByNameData()
    {
        static::$keyName = 'key_words';
        
        static::$checkFauther = false;
        
        $this->getClassByName();
        
    }

    /**
     * 商品分类修改
     * @param integer $id 该商品分类的id
     */
    public function editClass(){
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $this->checkParamByClient();

        $result = $this->logic->saveClass();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);
    }

    /**
     * 是否推荐
     */
    public function isRecommend ()
    {
        Tool::checkPost($_POST, array('is_numeric' => array('id')), true, array('id')) ? true : $this->ajaxReturnData(null, 0, '参数错误');
        
        $status = BaseModel::getInstance(GoodsClassModel::class)->save($_POST);
        
        $this->updateClient($status, '操作');
    }

    /**
     * 单击是否显示
     */
    public function changeHideStatus()
    {
        $check = new CheckParam($this->logic->getChangeMessageNotice(),$this->args);
        $status   = $check->checkParam();
        $this->objController->promptPjax($status, $check->getErrorMessage());

        //调用方法修改状态
        $result = $this->logic->changeStatus();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        $this->objController->ajaxReturnData([]);

    }

    /**
     * 商品分类删除
     * @param int $id 商品分类的id
     */
    public function remove(){
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        Tool::connect('parseString');

        $result = $this->logic->deleteClass();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData([]);


    }

    /**
     * 手动单击切换选项推荐
     */
    public function changType(){
        $id = I("get.id");
        $data_flag = I("get.data_flag");
        $type = I("get.type");
        $result = M("GoodsClass")->field('id')->find($id);
        if(	$data_flag == "true"){
            $result[$type] = 0;
            if(M("GoodsClass")->save($result)){
                $this->ajaxReturn("no");
            }
        }else{
            $result[$type] = 1;
            if(M("GoodsClass")->save($result)){
                $this->ajaxReturn("yes");
            }
        }
    }

    /**
     * 分类详细信息
     */
    public function showInfo()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        //获取单个商品规格信息
        $row = $this->logic->getGoodsClassInfo();
        $this->objController->promptPjax($row, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData($row);
    }

    /**
     * 获取所有商品分类
     */
    public function allClass()
    {
        $result = $this->logic->getAllClass();
        $this->objController->ajaxReturnData($result);
    }

    /**
     * 分类三级联动
     */
    // public function getGoodsClass()
    // {
    //     $result = $this->logic->getGoodsClass();

    //     $this->objController->ajaxReturnData($result);
    // }

    /**
     * 根据编号获取分类(三级联动)
     */
    public function getClassById()
    {
        $checkObj = new CheckParam($this->logic->checkClassId(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $this->objController->ajaxReturnData($this->logic->getCacheByClass());
        
    }

}

