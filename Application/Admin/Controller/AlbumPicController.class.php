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
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use PlugInUnit\Validate\CheckParam;

/**
 * 图片控制器
 * @author Administrator
 */
class AlbumPicController
{
    use IsLoginTrait;
    use InitControllerTrait;

    /**
     * 构造方法
     */
    public function __construct(array $args = [])
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new AlbumPicLogic($args);
    }

    /**
     * 相册图片列表
     */
    public function albumPicList()
    {
        //验证相册编号
        $checkObj = new CheckParam($this->logic->getIndexMessageNotice(), $this->args);
        $status   = $checkObj->checkParam();
        $this->objController->promptPjax($status, $checkObj->getErrorMessage());

        //获取相册图片列表
        $result = $this->logic->getAlbumPicList();
        
        //接口返回数据
        $this->objController->ajaxReturnData($result);

    }
    
    /**
     * 添加图片到图片空间空间
     */
    public function uploadPicture()
    {
        $this->objController->promptPjax($this->logic->getCheckDataByAlbumPic(), $this->logic->getErrorMessage());
        
        $this->objController->updateClient($this->logic->addPic(), '添加');
        
    }
    
    /**
     * 将图片设置为封面
     */
    public function changeCover()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        $result = $this->logic->changeCoverPic();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData([]);
    }

    /**
     * 删除图片(可批量删除)
     * @param  int or string(1,2,...)
     */
    public function delPic()
    {

        $result = $this->logic->delPics();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData([],1,'删除成功');
    }

    /**
     * 移动图片(可批量移动)
     */
    public function movePic()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        $result = $this->logic->moveAlbum();
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData([]);
    }
}