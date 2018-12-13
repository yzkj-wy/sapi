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

class PayModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//编号

	public static $payType_id_d;	//支付类型【编号】

	public static $payAccount_d;	//支付账号或APP_ID

	public static $mchid_d;	//受理人编号或收款方支付宝账号【一般情况下与合作这身份id一样】

	public static $payKey_d;	//支付秘钥

	public static $openId_d;	//微信openID

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $payName_d;	//支付类名【不可更改】

	public static $returnName_d;	//退款类名

	public static $type_d;	//设备类型 0pc 1 wap 2 app 3 微商城

	public static $privatePem_d;	//私钥

	public static $publicPem_d;	//公钥

	public static $notifyUrl_d;	//异步通知url

	public static $returnUrl_d;	//同步通知地址

	public static $specialStatus_d;	//特殊支付【0 商品支付，1套餐支付，2积分支付，3开店支付，4余额充值 ,5线下订单】

	public static $storeId_d;	//店铺编号

	public static $payCode_d;	//支付企业代码

	public static $enterpriseName_d;	//支付企业名称

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
 
    
    /**
     * 根据主键获取支付信息 
     */
    public function getPayConfigByPrimarykey($id, $isSpecial = FALSE)
    {
        if (($id = intval($id)) === 0) {
            $this->error = '参数不正确';
            return array();
        }
        
        $data = S('PAY_CONFIG_BY_ID');
        
        if (empty($data)) {
            $selectColums = $this->selectColums;
            
            $field = empty($selectColums) ? $this->getDbFields() : $this->selectColums;
            
            $data = $this->field($field, $isSpecial)->where(self::$id_d.'= %d', $id)->find();
            
            if (empty($data)) {
                $this->error = '暂无数据';
                return array();
            }
            
            S('PAY_CONFIG_BY_ID', $data, 20);
        }
        return $data;
    }
    
    /**
     * 获取账号信息 
     * @param int $payTypeId 支付类型
     * @return array
     */
    public function getPayAccount ($payTypeId)
    {
        if (($payTypeId = intval($payTypeId)) === 0) {
            $this->error = '参数不正确';
            return array();
        }
        
        return $this->where(self::$payType_id_d.'=%d and '.self::$type_d.' = 0', $payTypeId)->find();
    }
    
   
}