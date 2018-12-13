<?php
/**
 * Created by PhpStorm.
 * User: qingCai
 * Date: 2018/1/22 0022
 * Time: 11:21
 */
namespace Common\Model;

class StoreNavColorModel extends BaseModel
{
    private static $obj;
    private $info;

	public static $id_d;	//编号

	public static $storeId_d;	//店铺id

	public static $color_d;	//导航背景色-16进制


    public static function getInitnation()
    {

    }
    
    public function init( array $data )
    {
        $this->info = $data;
        return $this;
    }
    
    public function getStoreId()
    {
        $id      = \intval( $this->info[ 'id' ] );
        $storeId = $this->where( [ 'id' => $id ] )->getField( 'store_id' );
        return empty( $storeId ) ? 0 : $storeId;
    }
}