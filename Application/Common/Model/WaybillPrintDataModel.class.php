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
 * 运单调整设计模型
 * @author 王强
 * @version 1.0
 */
class WaybillPrintDataModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//

	public static $printId_d;	//打印项编号

	public static $waybillId_d;	//运单【编号】

	public static $printItem_d;	//打印项

	public static $dialogLeft_d;	//做偏移量（毫米）

	public static $dialogWidth_d;	//宽度偏移量

	public static $dialogHeight_d;	//高度偏移量（毫米）

	public static $dialogTop_d;	//上偏移量（毫米）

	public static $status_d;	//状态【0 废弃 1打印】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
        return $data;
    }
    
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
        return $data;
    }
}