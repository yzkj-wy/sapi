<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Model;

/**
 * @author 王强
 * @version 1.0
 */
class StoreEvaluateModel extends BaseModel
{
    private static $obj;


	public static $id_d;	//评价ID

	public static $orderId_d;	//订单ID

	public static $createTime_d;	//评价时间

	public static $storeId_d;	//店铺编号

	public static $memberId_d;	//买家编号

	public static $desccredit_d;	//描述相符评分

	public static $servicecredit_d;	//服务态度评分

	public static $deliverycredit_d;	//发货速度评分

	
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    
    //获取店铺评分
    public function getScore(){
        $where['store_id'] = $_SESSION['store_id'];
        $desccredit = number_format($this->where($where)->avg("desccredit"), 2, '.', '');
        $servicecredit = number_format($this->where($where)->avg("servicecredit"), 2, '.', '');
        $deliverycredit = number_format($this->where($where)->avg("deliverycredit"), 2, '.', '');
        $comprehensive = number_format(($desccredit+$servicecredit+$deliverycredit)/3, 2, '.', '');
        $data = array(
            "desccredit"     =>$desccredit,
            "servicecredit"  =>$servicecredit,
            "deliverycredit" =>$deliverycredit,
            "comprehensive"  =>$comprehensive,
        );
        return $data;
    }
}