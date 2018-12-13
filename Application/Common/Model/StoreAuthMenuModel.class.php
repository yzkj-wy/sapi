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

class StoreAuthMenuModel extends BaseModel
{
	/**
	 * 类实例承载着
	 * @var BrandModel
	 */
	private static  $obj;
	
	public static $id_d;	//编号
	
	public static $path_d;	//名称
	
	public static $redirect_d;	//显示状态
	
	public static $name_d;	//规则
	
	public static $component_d;	//创建时间
	
	public static $createTime_d;	//角色说明
	
	public static $updateTime_d;
	
	public static $fid_d;
	
	public static $remark_d;
	
	public static $status_d;
	
	public static $condition_d;
	
	public static $sort_d;
	
	public static function getInitnation()
	{
		$name = __CLASS__;
		return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
	}
}