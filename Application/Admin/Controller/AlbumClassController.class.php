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


use Common\Logic\AlbumPicLogic;
use Common\Logic\AlbumClassLogic;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;

/**
 * 相册控制器
 * @author Administrator
 */
class AlbumClassController
{

    use IsLoginTrait;
    use InitControllerTrait;

    /**
     * 构造方法
     */
    public function __construct(array $args =[])
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new AlbumClassLogic($args);
    }

    /**
     * 添加相册
     */
    public function addAlbum()
    {

        //验证数据
        $this->checkParamByClient();

        //调用添加方法
        $result = $this->logic->addAlbum();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);

    }

    /**
     * 获取相册列表
     */
    public function albumList()
    {
        //获取分页结果
        $result = $this->logic->getDataList();
       
        $this->objController->promptPjax($result['data']);

        //获取相册图片数量
        $data = new AlbumPicLogic($result['data']);
        $result['data'] = $data->getAlbumPicNumber();

        $this->objController->ajaxReturnData($result);
    }

    /**
     * 删除相册
     */
    public function delAlbum()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        //调用删除方法
        $result = $this->logic->deleteAlbum();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData([]);
    }

    /**
     * 获取相册信息
     */
    public function showInfo()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        //调用删除方法
        $result = $this->logic->getFindOne();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData($result);
    }

    /**
     *编辑相册
     */
    public function editAlbum()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        //验证数据
        $this->checkParamByClient();

        //调用添加方法
        $result = $this->logic->editAlbum();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        //接口返回数据
        $this->objController->ajaxReturnData(['url'=>$_SERVER['SERVER_NAME'] . U("index")]);
    }

    /**
     * 获取所有相册
     */
    public function allAlbum()
    {
        $this->objController->ajaxReturnData($this->logic->getAllAlbum());
    }

}