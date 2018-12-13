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
 * 招商(企业)入驻银行信息
 * @author Administrator
 */
class StoreCompanyBankInformationModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//店铺编号

	public static $accountName_d;	//开户名

	public static $companyAccount_d;	//公司银行账号

	public static $branchBank_d;	//开户银行支行名称

	public static $branchNumber_d;	//支行联行号 

	public static $bankElectronic_d;	//开户银行许可证电子版

	public static $isSettle_d;	//是否以开户行作为结算账号 0-否 1-是

	public static $settleName_d;	//结算账户开户名

	public static $settleAccount_d;	//结算公司银行账号

	public static $settleBank_d;	//结算开户银行支行名称

	public static $settleNumber_d;	//结算支行联行号

	public static $certificateNumber_d;	//税务登记证号

	public static $identificationNumber_d;	//纳税人识别号

	public static $registrationElectronic_d;	//税务登记证号电子版


	public static $alipayAccount_d;	//支付宝支付账号

	public static $wxAccount_d;	//微信支付账号

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
    //添加数据
    public  function addCompanyBank($data){
    	if (empty($data)) {
        	return array('status'=>0,"mes"=>"数据出错!");
        }
        $res = $this->data($data)->add();
        if (!$res) {
            return array('status'=>0,"mes"=>"添加失败!");
        }
        return array('status'=>1,'data'=>$res,"mes"=>"添加成功");
    }
}