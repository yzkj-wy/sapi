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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Model;
/**
 * 店铺经营类目
 * @author 王强
 * @version 1.0
 */
class StoreInformationModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//公司入驻表编号

	public static $shopAccount_d;	//商家账号

	public static $shopName_d;	//店铺名称

	public static $levelId_d;	//店铺等级

	public static $shopLong_d;	//开店时长

	public static $shopClass_d;	//店铺分类

	public static $scBail_d;	//店铺分类保证金

	public static $payingCertificate_d;	//付款凭证

	public static $payingCertif_d;	//付款凭证说明

	public static $status_d;	//入驻类型 0公司入驻  1 企业入驻

    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    //添加 店铺经营信息
    public function addInformation(array $data){
    	if (empty($data)) {
    		return array('status'=>0,"mes"=>"数据出错!");
    	}
        $date = "+".$data['shop_long']."year";
        $data['shop_long'] = strtotime(date('Y-m-d H:i:s',strtotime($date)));//
    	$res = $this->data($data)->add();
    	if (!$res) {
            M()->rollback();
    		return array('status'=>0,"mes"=>"添加店铺经营失败!");
    	}
    	return array('status'=>1,"data"=>$data['store_id'],"mes"=>"添加店铺经营成功!");
    }
    //根据 条件查询
    public function getInfo($where,$field){
        if (empty($where)||empty($field)) {
            return array("status"=>"","mes"=>"参数出错!","data"=>"");
        }
        $res = $this->field($field)->where($where)->find();
        if (empty($res)) {
            return array("status"=>"","mes"=>"数据不存在!","data"=>"");
        }
        return array("status"=>"","mes"=>"参数出错!","data"=>$res);
    }
}