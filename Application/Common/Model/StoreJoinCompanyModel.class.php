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
 * 公司入驻模型
 * @author 王强
 *
 */
class StoreJoinCompanyModel extends BaseModel
{
    private static $obj;
    

	public static $id_d;	//主键编号

	public static $userId_d;	//申请人

	public static $storeName_d;	//店铺名称

	public static $companyName_d;	//公司名称

	public static $numberEmployees_d;	//员工数

	public static $registeredCapital_d;	//注册资金数

	public static $licenseNumber_d;	//营业执照号

	public static $validityStart_d;	//营业执照开始日期

	public static $validityEnd_d;	//营业执照结束日期

	public static $electronicVersion_d;	//营业执照电子版

	public static $organizationCode_d;	//组织机构代码

	public static $organizationElectronic_d;	//组织机构代码证电子版

	public static $taxpayerCertificate_d;	//一般纳税人证明

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//申请状态 【0-已提交申请 1-缴费完成  2-审核成功 3-审核失败 4-缴费审核失败 5-审核通过开店】

	public static $remark_d;	//备注

	public static $mobile_d;	//联系人手机

	public static $companyMobile_d;	//公司电话

	public static $name_d;	//联系人姓名

	public static $scopeOf_operation_d;	//法定经营范围


    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
    /**
     * 重写父类方法自动添加时间
     */
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
    
        return $data;
    }
    
    /**
     * 重写父类方法
     */
    protected function _before_update(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
    
        return $data;
    }
    //添加 
    public function addJoin($data){
        if (empty($data)) {
            return array('status'=>0,"mes"=>"数据出错!");
        }
        $data['validity_start'] = strtotime($data['validity_start']);
        $data['validity_end']= strtotime($data['validity_end']);
        $data['create_time'] = time();
        $res = $this->data($store)->add();
        if (!$res) {
            M()->rollback();
            return array('status'=>0,"mes"=>"添加失败!");
        }
        return array('status'=>1,"data"=>$res,"mes"=>"添加成功!");
    }
}