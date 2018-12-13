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

/**
 * 商品规格
 * @author 王强
 * @version 1.0.0
 */
class AlbumClassModel extends BaseModel
{
    protected $patchValidate = true;
    protected $_validate = [];

    private static $obj;


	public static $id_d;	//相册id

	public static $albName_d;	//相册名称

	public static $albDes_d;	//相册描述

	public static $albSort_d;	//排序

	public static $albCover_d;	//相册封面

	public static $storeId_d;	//商家编号

	public static $createTime_d;	//创建时间

	public static $isDefault_d;	//是否默认【0否1是】


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
        $data['store_id'] = $_SESSION['store_id'];
        return $data;
    }

}