<?php
namespace Common\Model;

/**
 * 投诉主题模型
 * @author 王强
 * @version 1.0.1
 */
class ComplainSubjectModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//投诉主题id

	public static $complainSubject_d;	//投诉主题

	public static $complainDesc_d;	//投诉主题描述

	public static $complainState_d;	//投诉主题状态(1-有效/0-失效)

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}