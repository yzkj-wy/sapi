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
// | 手动输入订单模型
// +----------------------------------------------------------------------
// | Another ：王波
// +----------------------------------------------------------------------

class OfflineOrderModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//主键id

	public static $orderSn_id_d;	//订单编号

	public static $createTime_d;	//订单日期

	public static $payName_d;	//支付企业名称

	public static $paymentOrder_id_d;	//支付流水号

	public static $priceSum_d;	//商品金额

	public static $freight_d;	//运费

	public static $couponDeductible_d;	//非现金抵扣金额

	public static $taxFcy_d;	//代扣税款

	public static $actualAmount_d;	//实际支付金额

	public static $userName_d;	//订购人注册号

	public static $realName_d;	//订购人姓名

	public static $idNumber_d;	//订购人证件号码

	public static $mobile_d;	//订购人电话

	public static $prov_d;	//收货信息-用户所在省

	public static $city_d;	//收货信息-用户所在市

	public static $dist_d;	//收货信息-用户所在县（区）

	public static $address_d;	//收货信息-用户详细地址

	public static $insureFee_d;	//保费

	public static $goodsId_d;	//商品id

	public static $goodsNum_d;	//件数

	public static $type_d;	//申报类型

	public static $tpCode_d;	//第三方物流商编码

	public static $busiMode_d;	//进口模式

	public static $expressId_d;	//运单号

	public static $billno_d;	//提运单号

	public static $bakOne_d;	//运输方式代码（海关）*

	public static $bakTwo_d;	//工具的名称*

	public static $bakThree_d;	//运输工具代码

	public static $bakFour_d;	//航班航次号*

	public static $bakFive_d;	//起运国代码（海关）*

	public static $status_d;	//0未支付1已支付

	public static $addTime_d;	//添加时间

	public static $saveTime_d;	//修改时间

	public static $storeId_d;	//店铺id


	public static $sendExpress_status_d;	//物流单推送状态:0未推送1已推送


    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}