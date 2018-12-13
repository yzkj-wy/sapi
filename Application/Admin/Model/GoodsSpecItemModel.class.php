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


namespace Admin\Model;


use Think\Model;
use Common\Model\BaseModel;

/**
 * 商品规格 模型
 * @author 王强
 * @version 1.0.0
 */
class GoodsSpecItemModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//规格项id

	public static $specId_d;	//规格id

	public static $item_d;	//规格项
    
	public static $storeId_d;	//店铺【编号】


    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
}