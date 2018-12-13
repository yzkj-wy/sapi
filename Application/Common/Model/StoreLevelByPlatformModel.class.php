<?php
namespace Common\Model;

class StoreLevelByPlatformModel extends BaseModel
{
    private static $obj;
    

	public static $id_d;	//编号

	public static $levelName_d;	//等级名称

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//状态【0 弃用 1启用】

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
}