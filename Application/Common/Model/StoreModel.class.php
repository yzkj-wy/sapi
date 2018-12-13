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
 * 店铺模型
 * @author Administrator
 */
class StoreModel extends BaseModel
{
    private static $obj ;


	public static $id_d;	//主键编号

	public static $shopName_d;	//店铺名称

	public static $classId_d;	//店铺分类【编号】

	public static $gradeId_d;	//店铺等级

	public static $storeAddress_d;	//地址编号

	public static $userId_d;	//店主【编号】

	public static $storeState_d;	//店铺状态【0关闭，1开启，2审核中】

	public static $storeSort_d;	//店铺排序

	public static $startTime_d;	//店铺营业开始时间

	public static $endTime_d;	//店铺营业结束时间

	public static $status_d;	//推荐【0为否，1为是，默认为0】

	public static $themeId_d;	//店铺当前主题

	public static $storeCollect_d;	//店铺收藏数量

	public static $printDesc_d;	//打印订单页面下方说明文字

	public static $storeSales_d;	//店铺销量

	public static $freePrice_d;	//超出该金额免运费【大于0才表示该值有效】

	public static $decorationSwitch_d;	//店铺装修开关【0-关闭 装修编号-开启】

	public static $decorationOnly_d;	//开启店铺装修【仅显示店铺装修(1-是 0-否】

	public static $imageCount_d;	//店铺装修相册图片数量

	public static $isOwn_d;	//是否自营店铺 【1是 0否】

	public static $buildAll_d;	//自营店是否绑定全部分类【 0否1是】

	public static $barType_d;	//店铺商品页面左侧显示类型【 0默认1商城相关分类品牌商品推荐】

	public static $isDistribution_d;	//是否分销店铺【0-否，1-是】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $type_d;	//店铺类型【0个人入驻 1企业入驻】

	public static $storeLogo_d;	//店铺logo

	public static $commission_d;	//佣金比例【0-100】

	public static $description_d;	//描述

	public static $wxAccout_d;	//微信账号

	public static $alipayAccount_d;	//支付宝账号

	public static $bankAccount_d;	//银行卡号

	public static $credibility_d;	//信誉

	public static $mobile_d;	//联系方式

	public static $personName_d;	//联系人姓名

    public static $ebcCodevar_d;  //电商企业的海关注册登记编号
    public static $ebcNamevar_d;  //电商企业的海关注册登记名称
    public static $agentCodevar_d;  //代理企业代码（海关）
    public static $inspEntCodevar_d;  //申报单位编号（国检）
    public static $inspEntNamevar_d;  //申报单位在国检备案的名称
    public static $inspCbeCodevar_d;  //电商企业编号（国检）
    public static $mcht_idvar_d;  //报关用的商户号,支付企业分配
    public static $ebpCodevar_d;  //电商平台代码(海关)
    public static $ebpNamevar_d;  //电商平台名称(海关)
    public static $inspEcpCodevar_d;  //电商平台编号（国检）
    public static $mcht_id_cebvar_d;  //报关用的商户号
    public static $busimodevar_d;  //通关模式
    public static $SenderIDvar_d;  //BC发送方编号
    public static $internetdomainnamevar_d;  //电商平台域名
    public static $platform_no_d;  //NULL平台编号
    public static $platform_short_d;  //NULL平台简称
    public static $assurecodevar_d;  //担保扣税的企业海关注册登记编号
    public static $electricCodevar_d;  //电商企业编码
    public static $cbepcomCode_d;  //  电商平台编码
    public static $copCode_d;  //  传输企业代码
    public static $copName_d;  //  传输企业名称
    public static $dxpId_d;  //  报文传输编号
    public static $csuc_code_d;  //  企业社会统一信用代码
 	public static $domain_name_d;  //  二级域名

	public static $inspOrgCode_d;	//监管机构代码（检）

	public static $bcSign_d;	//BC sign标记

    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
    protected function _before_insert(&$data, $options)
    {
        $data[static::$createTime_d] = time();
        
        $data[static::$updateTime_d] = time();
        
        $data[static::$storeSort_d] = 50;
        
        return $data;
    }
    
    protected function _before_update(&$data, $options)
    {

        $data[static::$updateTime_d] = time();
        
        return $data;
    }
    public function getStoreById($where,$field){
        if (empty($where)||empty($field)) {
        	return array("status"=>'',"message"=>"参数错误","data"=>"");
        }
        $data = $this->field($field)->where($where)->find();
        if (empty($data)) {
        	return array("status"=>'',"message"=>"暂无数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$data);
    }
}