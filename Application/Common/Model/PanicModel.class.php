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
// | 订单数量模型
// +----------------------------------------------------------------------
// | Another ：王强
// +----------------------------------------------------------------------

class PanicModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//抢购编号

	public static $panicTitle_d;	//抢购标题

	public static $panicPrice_d;	//抢购价格

	public static $goodsId_d;	//商品编号

	public static $panicNum_d;	//参加抢购数量

	public static $quantityLimit_d;	//限购数量

	public static $alreadyNum_d;	//已购买

	public static $startTime_d;	//开始时间

	public static $endTime_d;	//结束时间

	public static $status_d;	//审核状态【0拒绝，1通过，2审核中】

	public static $storeId_d;	//店铺【编号】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间


    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}