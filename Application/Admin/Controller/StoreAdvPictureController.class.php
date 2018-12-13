<?php 
namespace Admin\Controller;

use Common\Logic\StoreAdvPostionLogic;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use PlugInUnit\Validate\CheckParam;
use Common\Tool\Extend\CURL;

/**
 * 店铺广告图片上传
 * @author Administrator
 */
class StoreAdvPictureController
{
    use IsLoginTrait;
    use InitControllerTrait;
     
    /**
     * 架构方法
     */
    public function __construct(array $args =[])
    {   
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new StoreAdvPostionLogic($this->args);
    }
    
    
    /**
     * 上传图片
     */
    public function uploadPicture()
    {
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
       
        $this->objController->promptPjax(!empty($_FILES['adv_content']), '请上传图片');
        
        $checkObj = new CheckParam($this->logic->getMessageByPic(), $_FILES['adv_content']);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $this->objController->promptPjax($this->logic->checkImageWidthAndHeight(), $this->logic->getErrorMessage());
        
        $curlFile = new CURL($_FILES['adv_content'], C('create_ad_image'));
        
        $file = $curlFile->uploadFile();
        
        echo $file;die; 
    }
    
    /**
     * 删除广告图片
     */
    public function delPic() 
    {
        $curlFile = new CURL($this->args, C('unlink_image_no_thumb'));
        
         echo $curlFile->deleteFile();die;
    }
    
}