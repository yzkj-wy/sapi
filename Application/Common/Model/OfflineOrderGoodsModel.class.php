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
use Think\Model;
use Common\Tool\Tool;

// +----------------------------------------------------------------------
// | 手动输入订单商品模型
// +----------------------------------------------------------------------
// | Another ：王波
// +----------------------------------------------------------------------

class OfflineOrderGoodsModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//主键id

	public static $orderSn_id_d;	//订单编号

	public static $goodsId_d;	//商品id

	public static $goodsNum_d;	//商品数量

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间



    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}