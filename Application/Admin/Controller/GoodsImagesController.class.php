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
use Admin\Logic\GoodsImagesLogic;
use PlugInUnit\Validate\CheckParam;
Use Common\TraitClass\InitControllerTrait;
Use Common\TraitClass\ThumbNailTrait;
Use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\GETConfigTrait;
/**
 * 商品后台管理
 * @author Administrator
 */
class GoodsImagesController
{
    use InitControllerTrait;
    
    use ThumbNailTrait;
    
    use GETConfigTrait;
    
    use IsLoginTrait;
   
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->init();
        
        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsImagesLogic($args);
    }

    /**
     * 获取图片
     */
    public function getImages()
    {
        $check = new CheckParam($this->logic->checkValidateByGoodsId(), $this->args);

        $this->objController->promptPjax($check->checkParam(), $check->getErrorMessage());
        
        //关联商品图片
        $images = $this->logic-> getGoodsImages();

        $this->objController->ajaxReturnData($images);

    }

    /**
     *  保存图片
     */
    public function savePicture()
    {
        $this->checkParamByClient();
        
        //删除空的
        $thumbWith = $this->getConfig('thumb_image_width');
        
        $thumbHeight = $this->getConfig('thumb_image_height');
        
        $this->logic->setImageWidth($thumbWith);

        $this->logic->setImageHeight($thumbHeight);

        $status = $this->logic->editPicture();

        $this->objController->promptPjax($status, '保存失败');

        $this->objController->ajaxReturnData($status);
    }
    
    /**
     * 添加商品相册
     */
    public function pictureAlbum()
    {
        empty($_SESSION['insertId']) ? $this->objController->ajaxReturnData([], 0, '数据错误,请从头添加') : true;
     
        $this->checkParamByClient();
        
        $status = $this->logic->addPicture();
        
        $this->objController->updateClient($status, '添加');
    }
    
    /**
     * 获取图片列表
     */
    public function getImageList()
    {
        $checkObj = new CheckParam($this->logic->getMessageByGoods(), $this->args);
    
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    
        $this->objController->ajaxReturnData($this->logic->getImageListByGoods());
    }

    /**
     * 删除数据库图片数据
     */
    public function deleteImageByDb()
    {
        $checkObj = new CheckParam($this->logic->getMessageByDelImage(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $status = $this->logic->deleteManyPicture();
    
        $this->objController->updateClient($status, '操作');
    }
    
}