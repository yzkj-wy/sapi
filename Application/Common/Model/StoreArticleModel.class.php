<?php
/**
 * Created by PhpStorm.
 * User: qingCai
 * Date: 2018/1/19 0019
 * Time: 15:44
 */

namespace Common\Model;
class StoreArticleModel extends BaseModel
{
    private static $obj;
    private $info;

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
        $id = \intval( $this->info[ 'id' ] );
        $storeId = $this->where(['id'=>$id])->getField('store_id');
        return empty($storeId)? 0 : $storeId;
    }

}