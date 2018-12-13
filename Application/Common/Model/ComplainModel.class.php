<?php
namespace Common\Model;

/**
 * 投诉主题模型
 * @author 王强
 * @version 1.0.1
 */
class ComplainModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//投诉id

	public static $orderId_d;	//订单id

	public static $orderGoods_id_d;	//订单商品ID

	public static $accuserId_d;	//原告id

	public static $accusedId_d;	//被告id

	public static $complainId_d;	//投诉主题id

	public static $complainContent_d;	//投诉内容

	public static $complainPic1_d;	//投诉图片1

	public static $complainPic2_d;	//投诉图片2

	public static $complainPic3_d;	//投诉图片3

	public static $complainDatetime_d;	//投诉时间

	public static $handleDatetime_d;	//投诉处理时间

	public static $handleMember_id_d;	//投诉处理人id

	public static $appealMessage_d;	//申诉内容

	public static $appealDatetime_d;	//申诉时间

	public static $appealPic1_d;	//申诉图片1

	public static $appealPic2_d;	//申诉图片2

	public static $appealPic3_d;	//申诉图片3

	public static $finalMessage_d;	//最终处理意见

	public static $finalDatetime_d;	//最终处理时间

	public static $finalId_d;	//最终处理人【id】

	public static $complainState_d;	//投诉状态【0-新投诉/1-投诉通过转给被投诉人/2-被投诉人已申诉/3-提交仲裁/4-已关闭】

	public static $complainActive_d;	//投诉是否通过平台审批【0未通过/1通过】


    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    //统计投诉数量
    public function getComplainNumByWhere($where){
        $count = $this->where($where)->count();
        return $count;
    }
}