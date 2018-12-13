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

/**
 * 商品规格
 * @author 王强
 * @version 1.0.0
 */
class AlbumPicModel extends BaseModel
{
    protected $patchValidate = true;
    protected $_validate = [];

    private static $obj;

    public static $id_d;	//主键编号

    public static $picName_d;	//图片名称

    public static $ablId_d;	//相册id

    public static $picPath_d;	//图片路径

    public static $picSize_d;	//图片大小

    public static $picMeasure_d;	//图片尺寸

    public static $picType_d;	//图片类型

    public static $picSort_d;   //图片排序

    public static $isCover_d;   //是否为封面 1为是 0为否

    public static $createTime_d;   //上传时间

    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }

    /**
     * 添加前操作
     */
    protected function _before_insert(&$data,$options)
    {
        $data['create_time'] = time();

        return $data;
    }

}