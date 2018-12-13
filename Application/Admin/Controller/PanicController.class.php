<?php
namespace Admin\Controller;

use Common\Logic\GoodsLogic;
use Common\Model\PanicModel;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\PanicLogic;
use Common\Tool\Tool;


class PanicController
{
    use IsLoginTrait;
    use InitControllerTrait;

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->init();

//        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new PanicLogic($args);
    }

    //抢购列表
    public function panicList(){
        $res = $this->logic->getPanicList();
        $this->objController->promptPjax($res['data'], '没有数据');
        Tool::connect('parseString');
        $goodsLogic = new GoodsLogic($res['data'],$this->logic->getPanicSplitKey());
        $data = $goodsLogic->getDataByGoodsId();
        $this->objController->promptPjax($data);
        $this->objController->ajaxReturnData($data);
    }

    //添加抢购
    public function addPanic(){
        $this->checkParamByClient();
        $res = $this->logic->getAddPanic();
        $this->objController->promptPjax($res['status'],$res['message']);
        $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
    }
    
    //删除抢购
    public function delPanic(){
        $res = $this->logic->getDelPanic();
        $this->objController->promptPjax($res['status'],$res['message']);
        $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
    }

    //修改抢购
    public function updatePanic(){
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $this->checkParamByClient();
        $res = $this->logic->getUpdatePanic();
        $this->objController->promptPjax($res['status'],$res['message']);
        $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
    }

    //获取单条信息
    public function getFiled(){
        $res = $this->logic->getFiledOne();
        $this->objController->promptPjax($res['status'],$res['message']);
        $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
    }
}