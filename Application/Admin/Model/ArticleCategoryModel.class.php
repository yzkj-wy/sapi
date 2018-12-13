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
class ArticleCategoryModel extends BaseModel
{
    protected $patchValidate = true;
    protected $_validate = [];

    private static $obj;

    public static $id_d;	//主键编号

    public static $name_d;	//分类名称

    public static $intro_d;	//分类详情

    public static $status_d;	//显示状态

    public static $sort_d;	//排序

    public static $isHelp_d;	//是否帮助

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