<?php
/**
 * Created by PhpStorm.
 * User: qingCai
 * Date: 2018/1/22 0022
 * Time: 11:21
 */
namespace Common\Model;

class StoreNavModel extends BaseModel
{
    private static $obj;
    private $info;

    public static $id_d;	//编号

    public static $name_d;	//导航名称

    public static $url_d;	//链接地址

    public static $storeId_d;	//店铺id

    public static $isShow_d;	//是否显示,0-不显示,1-显示

    public static $orderBy_d;	//排序

    public function init( array $data )
    {
        $this->info = $data;
        return $this;
    }




    public static function getInitnation()
    {

    }

    public function getStoreId()
    {
        $id      = \intval( $this->info[ 'id' ] );
        $storeId = $this->where( [ 'id' => $id ] )->getField( 'store_id' );
        return empty( $storeId ) ? 0 : $storeId;
    }
}