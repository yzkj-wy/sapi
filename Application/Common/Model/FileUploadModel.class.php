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

namespace Common\Model;

use Common\Logic\AlbumPicLogic;
use Think\Upload;
use Common\Tool\Tool;
use Common\Logic\AbstractGetDataLogic;

/**
 * 上传模型 
 */
class FileUploadModel extends AbstractGetDataLogic
{
    private static  $obj;
    
    private $imageRouse = array();
    
    private $error = null;
    
    private $widthAndHeightConfig = array();

    private $goodsPicData;
    
    /**
     * @return the $widthAndHeightConfig
     */
    public function getWidthAndHeightConfig()
    {
        return $this->widthAndHeightConfig;
    }

    /**
     * @param multitype: $widthAndHeightConfig
     */
    public function setWidthAndHeightConfig($widthAndHeightConfig)
    {
        $this->widthAndHeightConfig = $widthAndHeightConfig;
    }

    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param field_type $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
    
    public function UploadFile($config, $file = '', $driver = 'Local', $driverConfig = null)
    {
        $this->imageRouse = empty($file) ? $_FILES : $file;
      
        //验证图片尺寸
        $isPass = $this->checkImageWidthAndHeight($this->widthAndHeightConfig);
        
        if ($isPass === false)
        {
            return array();
        }
        
        $upload = new Upload($config, $driver, $driverConfig);
      
        $file = $upload->upload($file);
       
        if (empty($file))
        {
            return array();
        }

        $file = Tool::array_depth($file) ===2 ? Tool::parseToArray($file) : $file;
       
        $filePath = str_replace('.', null,C('GOODS_UPLOAD.rootPath')).$file['savepath'].$file['savename'];

        $file['filePath'] = $filePath;

        //拼装图片数据
//         $this->goodsPicData($file);

//         //判断存取数据库是否成功,失败的话删除图片
//         $albumPic = new AlbumPicLogic($this->goodsPicData);
//         if($albumPic -> setGoodsPicInfoToDB() === false){

//             //删除图片
//             unlink(realpath(__ROOT__) . $filePath);
//             $this->error = '上传失败';
//             return false;

//         }

        return $filePath;
    }

    /**
     * 发布商品上传图片
     */
    public function UploadGoodsImage($config, $file = '', $driver = 'Local', $driverConfig = null)
    {
        $this->imageRouse = empty($file) ? $_FILES : $file;

        //验证图片尺寸
        $isPass = $this->checkImageWidthAndHeight($this->widthAndHeightConfig);

        if ($isPass === false)
        {
            return array();
        }

        $upload = new Upload($config, $driver, $driverConfig);

        $file = $upload->upload($file);
      
        if (empty($file))
        {
            return array();
        }

        $file = Tool::array_depth($file) ===2 ? Tool::parseToArray($file) : $file;

        $filePath = str_replace('.', null,C('GOODS_UPLOAD.rootPath')).$file['savepath'].$file['savename'];

        return $filePath;
    }

    /**
     * 检测图片宽高 
     * @param array $config 图片宽高数组
     * @return bool
     */
    public function checkImageWidthAndHeight (array $config)
    {
        if (empty($config)) {
            return false; //不予通过检测
        }

        $imageInfo = getimagesize($this->imageRouse['fileData']['tmp_name']);
        
        if (empty($imageInfo)) {
            return false;
        }

        $width = $imageInfo[0];

        $widthMinConfig = $config['min_width'];
        
        $widthMaxConfig = $config['max_width'];
        
        //最小宽度 > 实际宽度 || 最大配置宽度 < 实际宽度 
        if ($widthMinConfig > $width || $widthMaxConfig < $width) {
            $this->error = '图片宽度不符【'.$imageInfo[0].'】';
            return false;
        }
        
        $height = $imageInfo[1];
        
        $heightMinConfig = $config['min_height'];
        
        $heightMaxConfig = $config['max_height'];
        
        if ($heightMinConfig > $height || $heightMaxConfig < $height) {
            $this->error = '图片高度不符【'.$imageInfo[1].'】';
            return false;
        }
       
        $this->goodsPicData['pic_measure'] = $width . '×' . $height;
        return true;
    }

    /**
     * 组合图片数据
     */
    private function goodsPicData($file)
    {
        $this->goodsPicData['pic_name'] = $file['name'];
        $this->goodsPicData['pic_path'] = $file['filePath'];
        $this->goodsPicData['alb_id'] = I('alb_id');
        $this->goodsPicData['pic_size'] = $file['size'];
        $this->goodsPicData['pic_type'] = explode("/",$file['type'])[1];
        $this->goodsPicData['is_cover'] = 0;
        return;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return FileUploadModel::class;
    }

    
    public function getMessageNotice()
    {
        return [
            'fileData' => [
                'required' => true
            ]
        ];
    }

}