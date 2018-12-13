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
 * 个人入驻模型
 * @author 王强
 * @version 1.0
 */
class StorePersonModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//主键编号

	public static $userId_d;	//用户编号

	public static $storeName_d;	//店铺名称

	public static $personName_d;	//姓名

	public static $idCard_d;	//身份证号码

	public static $idcardPositive_d;	//身份证正面

	public static $otherSide_d;	//身份证反面

	public static $bankName_d;	//银行名称

	public static $bankAccount_d;	//银行账号

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

	public static $status_d;	//申请状态 【0-已提交申请 1-缴费完成  2-审核成功 3-审核失败 4-缴费审核失败 5-审核通过开店】

	public static $mobile_d;	//联系人电话

	public static $alipayAccount_d;	//支付宝支付账号

	public static $wxAccount_d;	//微信支付账号


    public static function getInitnation()
    {
        $class = __CLASS__;
        return static::$obj = !( static::$obj instanceof $class ) ? new static() : static::$obj;
    }

    public function addPerson( array $data )
    {
        $where[ 'store_name' ] = $data[ 'store_name' ];//店铺名称
        $res                   = $this->where( $where )->find();
        if( !empty( $res ) ){
            return array( 'status' => 0,"mes" => "店铺名称已存在!" );
        }
        $data[ 'create_time' ] = time();//添加时间
        $res                   = $this->data( $data )->add();
        if( !$res ){
            return array( 'status' => 0,"mes" => "申请失败!" ); 
        }
        return array( 'status' => 1,'data' => $res,"mes" => "" );
    }

    //修改银行结算信息
    public function saveBank( $where,$person )
    {
        if( empty( $where ) || empty( $data ) ){
            return array( 'status' => 0,"mes" => "数据出错!" );
        }
        $person[ 'update_time' ] = time();
        $res                     = $this->where( $where )->save( $data );
        if( !$res ){
            return array( 'status' => 0,"mes" => "失败!" );
        }
        return array( 'status' => 1,"mes" => "成功","data" => $where[ 'id' ] );
    }

    public function getInfo()
    {
        $storeInfo             = ( new StoreModel() )->field('user_id,commission')->where( [ 'id' => $_SESSION[ 'store_id' ] ] )->find();
        $field           = self::$id_d . ',' . self::$userId_d . ',' . self::$status_d . '' . self::$updateTime_d . ',' . self::$createTime_d;
        $storePersonInfo = $this->field( $field,true )->where( [ self::$userId_d => $storeInfo['user_id'] ] )->find();
        $storeInformation = ( new StoreInformationModel())->where([StoreInformationModel::$storeId_d=>$_SESSION['store_id']])->find();
        $data = \array_merge($storeInfo,$storePersonInfo,$storeInformation);
        return $data;
    }


}