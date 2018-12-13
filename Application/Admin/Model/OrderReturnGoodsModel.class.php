<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\BaseModel;
use Think\AjaxPage;
use Common\Tool\Tool;

/**
 * @author 王强【订单退货模型】
 */
class OrderReturnGoodsModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//退货id

	public static $orderId_d;	//订单【id】

	public static $tuihuoCase_d;	//退货理由

	public static $createTime_d;	//申请时间

	public static $revocationTime_d;	//撤销时间

	public static $updateTime_d;	//审核时间

	public static $goodsId_d;	//退货的商品【id】

	public static $explain_d;	//退货(退款)说明

	public static $price_d;	//退货(退款)金额

	public static $isReceive_d;	//退款及其换货时是否收到货【0未收到1收到】

	public static $status_d;	//审核状态【0审核中1审核失败2审核通过3退货中4退货完成 5已撤销】

	public static $userId_d;	//用户编号

	public static $number_d;	//申请数量

	public static $applyImg_d;	//申请图片

	public static $content_d;	//审核内容

	public static $isOwn_d;	//是否自营【0否 1是】

	public static $expressId_d;	//快递【编号】

	public static $waybillId_d;	//运单号

	public static $remark_d;	//备注

	public static $storeId_d;	//店铺【编号】

	public static $applyId_d;	//审核人

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        
        return static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }
    
    /**
     * 获取退货列表数据 
     */
    public function getContent($order, array $where = array())
    {
        $dbFields = $this->getDbFields();
        if (!in_array($order['orderBy'], $dbFields, true) || 
            empty($order['sort']) || 
            !in_array($order['sort'], [static::asc, static::desc], true)
          ) 
        {
            return array();
        }
        
        if (!empty($where[static::$orderId_d])) {
            $where[static::$orderId_d] = ['in', str_replace('"', null, $where[static::$orderId_d])];
        } elseif (isset($where[static::$orderId_d])) {
            unset($where[static::$orderId_d]);
        }
        
        $cache = S('ORDER_RETURN_CACHE_DATA');
       
        if (empty($cache)) {
            
            $cache = $this->getDataByPage([
                'field' => [
                    static::$id_d,
                    static::$orderId_d,
                    static::$goodsId_d,
                    static::$createTime_d,
                    static::$type_d,
                    static::$status_d,
                    static::$isReceive_d
                ],
                'where' => $where,
                'order' => $order['orderBy'].' '.$order['sort'],
            ], 10, false, AjaxPage::class);
          
            if (empty($cache)) {
                return array();
            }
          
            S('ORDER_RETURN_CACHE_DATA', $cache, 2);
        }
        return $cache;
    }
    
    
    protected function _before_update(&$data, $options) {
        $data[static::$updateTime_d] = time();
        return $data;
    }
    
    
    
    /**
     * 修改退货状态【退款成功后】 
     * @param int $orderId
     * @param array $goodsIdArray
     */
    public function editReturnStatus($orderId, array & $goodsIdArray)
    {
        file_put_contents("./Uploads/ssfsd.txt", $goodsIdArray);
        file_put_contents("./Uploads/ssfgsd.txt", $orderId);
        if ( ($orderId = intval($orderId)) === 0 || !$this->isEmpty($goodsIdArray)) {
            return false;
        }
        
        $goodsIdArray = Tool::characterJoin($goodsIdArray, $this->split);
        
       file_put_contents("./Uploads/sssd.txt", $goodsIdArray);
       
       file_put_contents("./Uploads/ssswssd.txt", $this->split);
        
        if (empty($goodsIdArray)) {
            return FALSE;
        }
        
        $this->startTrans();
        
        $status = $this->where(static::$orderId_d.'=%d and '.static::$goodsId_d.' in ('.$goodsIdArray.')', $orderId)->save([
            static::$status_d => 5,
        ]);
        file_put_contents("./Uploads/daf.txt", $this->getLastSql());
        if (!$this->traceStation($status)) {
            $this->rollback();
            return false;
        }
        return $status;
    }
}