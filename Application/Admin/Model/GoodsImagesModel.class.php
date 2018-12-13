<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\BaseModel;
use Common\Tool\Tool;
use Common\Tool\Extend\UnlinkPicture;
use Common\Tool\Event;
use Common\TraitClass\MethodTrait;

class GoodsImagesModel extends BaseModel
{
    use MethodTrait;
    
    private static  $obj;
    
    //主键
    public static $id_d;
    
    //商品编号
    public static $goodsId_d;
    
    //商品图片
    public static $picUrl_d;
    
    //商品状态
    public static $status_d;
    
	public static $isThumb_d;	//缩略图【1是 0否】

    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    /**
     * 重写方法
     * {@inheritDoc}
     * @see \Think\Model::addAll()
     */
    public function addAll($dataList, $options = [], $replace = FALSE)
    {
        if (empty($dataList) || !$dataList = $this->create($dataList))
        {
            return false;
        }
        $arr = array();
        foreach ($dataList[static::$picUrl_d] as $key => &$value) {
            $arr[$key][static::$goodsId_d] = $dataList[static::$goodsId_d];
            $arr[$key][static::$picUrl_d]  = $value;
            $arr[$key][static::$status_d]  = 1; 
            $arr[$key][static::$isThumb_d] = false !== strpos($value, 'thumb_') ? 1 : 0;
        }
        sort($arr);
        unset($dataList);
        $status =  parent::addAll($arr, $options, $replace);
        
        return $status;
    }
    
   
    
    /**
     * 删除图片 
     */
    public function deletePicture ($id)
    {
        if (($id = intval($id)) === 0) {
            $this->rollback();
            return false;
        }
      
        $img = $this->where(static::$goodsId_d.' = %d', $id)->getField(static::$id_d.','.static::$picUrl_d);
        
        if (empty($img)) {
            $this->commit();
            return true;
        }
        $status = $this->where(static::$goodsId_d.' = %d', $id)->delete();
        
        //删除本地图片
        
        //添加删除缩略图监听
        Event::insetListen('thumbImage', function (array & $param){
          
            if (empty($param)) {
                return false;
            }
            $thumb =  $tmp = null;
            foreach ($param as $key => $value) {
                 
                $tmp = substr($value, strrpos($value, '/')+1);
        
                $thumb = 'thumb_'.$tmp;
                $value = './'.str_replace($tmp, $thumb, $value);
                 
                if (!is_file($value)) {
                    continue;
                }
                unlink($value);
            }
        });
        
        Tool::partten($img, UnlinkPicture::class);
        if ($status !== false) {
            $this->commit();
            return true;
        }
        return false;
    }
}