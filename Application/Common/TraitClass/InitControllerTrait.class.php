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
namespace Common\TraitClass;

use Common\Controller\ProductController;
use PlugInUnit\Validate\CheckParam;

/**
 * 控制器初始化
 * @author Administrator
 * @version 1.0.0
 */
trait InitControllerTrait
{
    /**
     * @param array 属性
     */
    private $args;

    /**
     * 店铺逻辑处理层对象
     */
    private $logic;

    private $logicClassName;

    /**
     * 控制器对象
     * @var ProductController
     */
    private $objController;

    //要验证的数据字段
    private $validate = [];

    /**
     * @return the $args
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return the $logic
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @return the $objController
     */
    public function getObjController()
    {
        return $this->objController;
    }

    /**
     * 是否实例化 页面相关信息
     */
    private function init()
    {

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        $allowOrigin = C('accept');

        if(in_array($origin, $allowOrigin)){
            header("Access-Control-Allow-Origin:" . $origin);//跨域解决
            header('Access-Control-Allow-Methods:POST,GET');
            header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
            header('Access-Control-Allow-Credentials: true');
        }

        $this->objController = new ProductController();

        $sessionId = isset($_POST['token']) ? $_POST['token'] : null;

        if ($sessionId) {
            session_write_close();
            session_id($sessionId);
            session_start();
        }
    }

    protected function headerOriginInit() :void
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        $allowOrigin = C('accept');

        if(in_array($origin, $allowOrigin)){
            header("Access-Control-Allow-Origin:" . $origin);//跨域解决
            header('Access-Control-Allow-Methods:POST,GET');
            header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept');
            header('Access-Control-Allow-Credentials: true');
        }

        $sessionId = isset($_POST['token']) ? $_POST['token'] : null;

        if ($sessionId) {
            session_write_close();
            session_id($sessionId);
            session_start();
        }

    }

    /**
     * 添加时 删除id验证
     */
    public function parperParam($message)
    {
        $model = $this->logic->getModelObj();

        unset($message[$model::$id_d]);

        return $message;
    }

    /**
     * 验证客户端参数(添加或编辑用到)
     */
    private function checkParamByClient()
    {
        $checkObj = new CheckParam($this->logic->getMessageNotice(), $this->args);

        $status   = $checkObj->checkParam();

        $this->objController->promptPjax($status, $checkObj->getErrorMessage());
    }

    /**
     * 切换状态
     */
    public function changeStatus()
    {
        $checkObj = new CheckParam($this->logic->getMessageByChangeStatus(), $this->args);

        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());

        $status = $this->logic->chanageSaveStatus();

        $this->objController->updateClient($status, '修改');
    }

    /**
     * 保存
     */
    public function saveData()
    {
        $status = $this->saveDataToDataBase();

        $this->objController->ajaxReturnData($status);
    }

    /**
     * 保存到数据库
     */
    private function saveDataToDataBase()
    {
        $this->checkParamByClient();

        $status = $this->logic->saveData();

        $this->objController->promptPjax($status !== false, $this->logic->getErrorMessage());

        $isSuccess = $this->additionalBySave();

        $this->objController->promptPjax($isSuccess, '保存失败');

        return $status;
    }

    /**
     * 保存数据额外的方法
     */
    private function additionalBySave()
    {
        return true;
    }
}