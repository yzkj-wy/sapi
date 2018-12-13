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

class StoreAuthGroupAccessModel extends BaseModel
{
	/**
	 * 类实例承载着
	 * @var BrandModel
	 */
	private static  $obj;
	
	
	public static $uid_d;	//管理员编号
	
	public static $groupId_d;	//分组编号
	
	
	public static function getInitnation()
	{
		$name = __CLASS__;
		return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
	}
}