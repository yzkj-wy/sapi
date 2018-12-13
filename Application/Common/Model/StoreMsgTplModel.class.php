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

class StoreMsgTplModel extends BaseModel
{
    private static $obj ;

	public static $id_d;	//主键

	public static $smtCode_d;	//模板编码

	public static $smtName_d;	//模板名称

	public static $smtMessage_switch_d;	//站内信默认开关，0关，1开

	public static $smtMessage_content_d;	//站内信内容

	public static $smtMessage_forced_d;	//站内信强制接收，0否，1是

	public static $smtShort_switch_d;	//短信默认开关，0关，1开

	public static $smtShort_content_d;	//短信内容

	public static $smtShort_forced_d;	//短信强制接收，0否，1是

	public static $smtMail_switch_d;	//邮件默认开，0关，1开

	public static $smtMail_subject_d;	//邮件标题

	public static $smtMail_content_d;	//邮件内容

	public static $smtMail_forced_d;	//邮件强制接收，0否，1是

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
}