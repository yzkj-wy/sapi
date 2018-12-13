<?php
namespace Common\Model;

/**
 * 店铺会员等级
 * @author 王强
 */
class StoreMemberLevelModel extends BaseModel
{
    private static $obj;
    
	public static $id_d;	//编号

	public static $levelId_d;	//【平台设置的】店铺会员等级【编号】

	public static $discount_d;	//折扣

	public static $conditionMoney_d;	//金额条件

	public static $conditionNum_d;	//交易笔数

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $storeId_d;	//店铺编号


	public static $moneyBig_d;	//金额上限

	public static $moneySmall_d;	//金额下限

	public static $numBig_d;	//交易笔数上限

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
}