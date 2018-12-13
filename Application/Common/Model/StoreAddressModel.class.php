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
 * 店铺地址表【公司地址表】
 * @author Administrator
 *
 */
class StoreAddressModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//店铺编号

	public static $provId_d;	//省

	public static $city_d;	//市

	public static $dist_d;	//区

	public static $storeZip_d;	//邮政编码


	public static $address_d;	//详细地址


    public static $country_d;	//详细地址

	public static $status_d;	//入驻类型 0公司入驻  1 企业入驻



    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
    public function addAddress(array $address){ 
        if (empty($address)) {
        	return array('status'=>0,"mes"=>"数据出错!");
        }
        M()->startTrans();
        $res = $this->data($address)->add();
        if (!$res) {
            return array('status'=>0,"mes"=>"添加失败!");
        }
        return array('status'=>1,'data'=>$res,"mes"=>"添加成功");
    }
}