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
declare(strict_types=1);
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Admin\Model\OrderModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use Think\Cache;
use Common\Tool\Constant\OrderStatus;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class OrderLogic extends AbstractGetDataLogic
{
    protected $orderStatus = [2, 3, 4];//获取指定订单状态
    
    /**
     * 订单数据
     * @var array
     */
    private $orderSendData = [];
    
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
       $this->data = $data;
       
       $this->modelObj = new OrderModel();

       $this->covertKey = OrderModel::$orderSn_id_d;
       
       $this->splitKey = $split === null ? OrderModel::$id_d : $split;
    }
    
    public function getOrderSendData() :array
    {
    	return $this->orderSendData;
    }
    
    /**
     * 获取指定状态数据
     */
    public function getAppointData ():array
    {
        $orderData = $this->getOrderStatus();
    
        $flag = array();
    
        foreach ($orderData as $key => $value)
        {
            if (!in_array($key, $this->orderStatus, true)) {
                continue;
            }
            $flag[$key] = $value;
        }
    
        return $flag;
    }
    
    /**
     * 获取数据
     */
    public function getResult()
    {
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return OrderModel::class;
    }
    
    /**
     * 获取订单状态
     */
    public function getOrderStatus ()
    {
        if(!($cache = S('order')))
        {
            //获取全部订单状态
            $orderModel = new \ReflectionClass(OrderModel::class);
    
            $data       = $orderModel->getConstants();
            Tool::connect('ArrayChildren', $data);
            //删除不是状态的属性
            $data = Tool::deleteByCondition();
    
            Tool::setData($data);
            //状态 改为键  value改为汉字提示；
            $data = Tool::changeKeyValueToPrompt( C('order'));
            $cache = $data;
            S('order', $data, 86400);
        }
    
        return $cache;
    }
    
    
    /**
     * 获取订单状态
     */
    public function getOrderStatusByUser()
    {
        if (!is_numeric($this->data[$this->splitKey]))
        {
            return false;
        }
        return $this->modelObj->where(OrderModel::$id_d.' = %d', $this->data[$this->splitKey])->getField(OrderModel::$status_d);
    }
    
    /**
     * 是否已支付
     */
    public function isAlready()
    {
        $orderStatus = $this->getOrderStatusByUser();
       
        if ($orderStatus != OrderModel::YesPaid && $orderStatus > OrderModel::AlreadyShipped)
        {
            $this->errorMessage = '订单状态错误';
            return false;
        }
        
        return true;
    }
    
    /**
     * 返回快递分割键
     * @return string
     */
    public function getExpressSplitKey()
    {
        return OrderModel::$expId_d;
    }
    
    /**
     * 返回地区分割键
     * @return string
     */
    public function getAddressSplitKey()
    {
        return OrderModel::$addressId_d;
    }
    
    /**
     * 返回用户分割键
     */
    public function getUserSplitKey()
    {
        return OrderModel::$userId_d;
    }
    
    /**
     * 返回店铺分割键
     */
    public function getStoreSplitKey()
    {
        return OrderModel::$storeId_d;
    }
    /**
     * 返回支付类型key
     * @return string
     */
    public function getPayTypeSplitKey()
    {
        return OrderModel::$payType_d;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getFindOne()
     */
    public function getFindOne()
    {
        $data = parent::getFindOne();
        
        $_SESSION['shippingMonery'] = $data[OrderModel::$shippingMonery_d];
        
        return $data;
    }
    
    /**
     * 发货
     */
    public function deliverGoods()
    {
        $data = $this->data;
        
        $data[OrderModel::$orderStatus_d] = OrderModel::AlreadyShipped;
        return $this->modelObj->save($data);
    }
    
    
    /**
     * 已发货的条件
     */
    public function buildOrderWhere(array $where)
    {
        $where[OrderModel::$orderStatus_d] = ['between', OrderModel::InDelivery.','.OrderModel::ReceivedGoods];
        
        return $where;
    }
    
    /**
     * 根据退货信息查找 订单数据
     */
    public function getOrderByOrderReturn()
    {
       
        $cacheKey = md5(json_encode($this->data));
        
        $cache = Cache::getInstance('', ['expire' => 60]);
        
        $data = $cache->get($cacheKey);
        
        if (!empty($data)) {
          
        	return $data;
        }
        
        
        $field = [
        	OrderModel::$orderSn_id_d,
        	OrderModel::$id_d,
        ];
        
        $data = $this->getDataByOtherModel($field, OrderModel::$id_d);
        
        if (empty($data)) {
            return array();
        }
        
        $cache->set($cacheKey, $data);
        
        return $data;
    }
    
    /**
     * 获取订单编号
     */
    public function getOrderSn()
    {
        $orderSn = $this->modelObj->where(OrderModel::$id_d.' = %d', $this->data[$this->splitKey])->getField(OrderModel::$orderSn_id_d);
        
        return $orderSn;
    }
    
    /**
     * 根据退货信息 查询订单标志
     */
    public function getOrderDataByRefund()
    {
    	$orderSn = $this->getOrderSn();
    	
    	if (empty($orderSn)) {
    		return $this->data;
    	}
    	
    	$result = $this->data;
    	
    	$result[OrderModel::$orderSn_id_d] = $orderSn;
    	
    	return $result;
    }
    
    /**
     * 获取发货单列表
     * @return array
     */
    public function getSendOrderList()
    {
    	$this->searchTemporary = [
    		OrderModel::$storeId_d => $_SESSION['store_id'],
    		OrderModel::$orderStatus_d => '1',
    	];
    	
    	return parent::getDataList();
    }
    
    /**
     * 合并数据
     */
    public function getOrderSnMergeData()
    {
        $orderSn = $this->getOrderSn();
        
        if (empty($orderSn)) {
            return $this->data;
        }
        
        $data = $this->data;
        
        $data[OrderModel::$orderSn_id_d] = $orderSn;
        
        return $data;
    }
    
    /**
     * 根据编号获取订单数据
     */
    public function getOrderData()
    {
    	$key = $this->data[$this->splitKey].'_'.$_SESSION['store_id'];
    	
    	$cache = Cache::getInstance('', ['expire' => 100]);
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
        $data = $this->modelObj->where(OrderModel::$id_d.'=%d', $this->data[$this->splitKey])->find();
    
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 退货退钱时处理信息
     */
    public function getOrderByOrderReturnOne()
    {
    	$data = $this->getOrderData();
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$result = $this->data;
    	
    	$result[OrderModel::$payType_d] = $data[OrderModel::$payType_d];
    	
    	$result[OrderModel::$platform_d] = $data[OrderModel::$platform_d];
    	
    	$result[OrderModel::$orderSn_id_d] = $data[OrderModel::$orderSn_id_d];
    	
    	$result[OrderModel::$shippingMonery_d] = $data[OrderModel::$shippingMonery_d];
    	
    	$result['total_money'] = $data[OrderModel::$priceSum_d] + $data[OrderModel::$shippingMonery_d];
    	
    	return $result;
    	
    	
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            OrderModel::$orderSn_id_d
        ];
    }

    /**
     * @return array|void
     */
    protected function getTableColum() :array
    {
        return [
            OrderModel::$id_d,
            OrderModel::$orderSn_id_d,
            OrderModel::$createTime_d,
            OrderModel::$orderStatus_d,
            OrderModel::$userId_d,
            OrderModel::$priceSum_d,
        	OrderModel::$addressId_d,
        	OrderModel::$payType_d,
        	OrderModel::$platform_d,
        	OrderModel::$remarks_d,
        	OrderModel::$payTime_d,
        	OrderModel::$shippingMonery_d,
        	OrderModel::$expressId_d,
            OrderModel::$couponDeductible_d,
            OrderModel::$expId_d,
            OrderModel::$deliveryTime_d
        ];
    }

    /**
     * 获取订单商品模型关联字段
     */
    public function getOrderGoodsSplitKey()
    {
        return OrderModel::$id_d;
    }

    /**
     * @return mixed
     *订单管理 li
     */
    public function LogCommentManage(){
        $data=$this->data;

        return $this->modelObj->_getOrderComment($data);
    }



/**
 * 立即发货Li
 */
    public function logSendGoodsAtOnce(){

        $order_id=$this->data['order_id'];
        $express_id = $this->data['express_id'];
        $orderModel=M('order');
        $orderGoodsModel=M('order_goods');
        if(is_numeric($order_id)) {

            $order_status = $orderModel->where(['id' => $order_id])->getField('order_status');
            if ($order_status == 1) {
                $order['order_id'] = $order_id;
                $order['express_id'] = $express_id;
                $order['order_status'] = '3';
                $orderGoodsModel->where(['order_id' =>$order_id])->save(['status' => 3]);
                return $this->modelObj->_updOrder($order);
            }
        }

        if(is_array($order_id)){

            foreach($order_id as $v){
                $order_status = $orderModel->where(['id' =>$v])->getField('order_status');

                if ($order_status == 1) {
                    $order['order_id'] = $v;
                    $order['order_status'] = '3';
                    $orderGoodsModel->where(['order_id' =>$v])->save(['status' => 3]);
                    return $this->modelObj->_updOrder($order);
                }

            }

        }

    }

    /**
     * 发货单管理
     */

    public function logSendGoods(){

        $data=$this->data;

        return $this->modelObj->_getOrderByStoreId($data);
    }

    //订单管理
    public function logOrderManagement(){ 
        $data=$this->data;

        return $this->modelObj->_getOrderByStoreId($data);
    }

    //获取 订单 信息
    public function getOrderInfo(){
        //待付款订单
        $pay['store_id'] = $_SESSION['store_id'];
        $pay['order_status'] = '0';
        $pendingPayment = $this->modelObj->getNumberByWhere($pay);
        //待发货订单
        $very['store_id'] = $_SESSION['store_id'];
        $very['order_status'] = '1';
        $pendingDelivery = $this->modelObj->getNumberByWhere($very);
        //待评价订单
        $to['store_id'] = $_SESSION['store_id'];
        $to['order_status'] = '4';
        $to['comment_status'] = "0";
        $toBeEvaluated = $this->modelObj->getNumberByWhere($to);
        //退款中订单
        $funds['store_id'] = $_SESSION['store_id'];
        $funds['order_status'] = '8';
        $refunds = $this->modelObj->getNumberByWhere($funds);
        $data = array(
            "pendingPayment"=>$pendingPayment,
            "pendingDelivery"=>$pendingDelivery,
            "toBeEvaluated"=>$toBeEvaluated,
            "refunds"=>$refunds,
        );
        return $data;
    }
    //获取今日订单数
    public function orderToday(){
        $today = date('Y-m-d', time());
        $start = strtotime($today.' 00:00:00');  
        $end  = strtotime($today.' 23:59:59');
        $where['store_id'] = $_SESSION['store_id'];
        $where['create_time'] = array('between',array($start,$end));
        $count = $this->modelObj->getNumberByWhere($where);       
        return $count;
    }
    //获取今日收益
    public function profitToday(){
        $today = date('Y-m-d', time());
        $start = strtotime($today.' 00:00:00');  
        $end  = strtotime($today.' 23:59:59');
        $where['order_status'] = array('GT',1);
        $where['store_id'] = $_SESSION['store_id'];
        $where['pay_time'] = array('between',array($start,$end));
        $field = "price_sum";
        $res = $this->modelObj->statistics($where,$field);
        return $res;
    }
    //销售情况统计
    public function getSalesStatistics(){
        for ($i=0; $i < 11 ; $i++) { 
            $date = "-".$i." "."day";
            $today = date("Y-m-d",strtotime($date));
            $start = strtotime($today.' 00:00:00');  
            $end  = strtotime($today.' 23:59:59');
            $where['order_status'] = array('GT',1);
            $where['store_id'] = $_SESSION['store_id'];
            $where['pay_time'] = array('between',array($start,$end));
            $field = "price_sum";
            $res = $this->modelObj->statistics($where,$field);
            $data[$i]['time'] = $today;
            $data[$i]['price'] = $res['price_sum'];
        }
        return $data;
    }
    //评价管理--删除
    public function logDeleteComment(){
        $data=$this->data;
        return  M('order_comment')
                ->where([
                'goods_id'=>$data['goods_id'],
                'order_id'=>$data['order_id']
                ])
                ->delete();
    }
    //评价管理--回复
    public function logAnswerComment(){
        $data=$this->data;
        $res=$this->modelObj->answerComment($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);

    }




    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->servicetype->getComment();
        $message = [
            'answer' => [
                'required' => '请输入'.$comment['name'],
            ],

        ];

        return $message;
    }

    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate() :array
    {
        $validate = [
            'answer'=> [
                'required' => true,
                'specialCharFilter' => true
            ],

        ];
        return $validate;
    }
    
    /**
     * 修改发货信息快递单检测
     * @return array
     */
    public function getMessageValidate() :array
    {
    	return [
    		OrderModel::$id_d => [
    			'number' => 'id必须是数字',
    		],
    		OrderModel::$expressId_d => [
    			'number' => '快递单号必须是数字'
    		],
    		OrderModel::$expId_d => [
    			'number' => '快递公司编号必须是数字'
    		]
    	];
    }
    /**
     * 根据结算信息 获取订单信息
     */
    public function getOrderListBySettlement()
    {
        $data = $_SESSION['order_where_by_settlement'];
        
        if (empty($data['store_id'])) {
            throw new \Exception('系统极度异常酝酿中。。。。');
        }
        
        $this->searchTemporary = [
            OrderModel::$storeId_d  => $data['store_id'],
            OrderModel::$overTime_d => ['between', [$data['start_time'], $data['end_time']]],
        ];
        
        return parent::getDataList();
    }
   
    //订单确定发货
    public function getOrderSendGoods() :bool
    {
    	
        $this->modelObj->startTrans();
       
        $res = $this->saveData();
        
        if (!$this->traceStation($res)) {
        	$this->errorMessage = '发货失败';
        	return false;
        }
       	
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultBySave()
     */
    protected function getParseResultBySave() :array
    {
    	$data = $this->data;
    	
    	$data[OrderModel::$deliveryTime_d] = time();
    	
    	$data[OrderModel::$orderStatus_d] = OrderStatus::AlreadyShipped;
    	
    	$findOneData = $this->getFindOne();
    	
    	$data[OrderModel::$userId_d] = $findOneData[OrderModel::$userId_d];
    	
    	$this->orderSendData = $data;
    	
    	return $data;
    }
    
    //修改运单号
    public function getSaveWaybill(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $order = $this->modelObj->field(['id,exp_id,express_id'])->where($where)->find();
        if (empty($order)) {
            return array("status"=>0,"message"=>"获取失败","data"=>"");
        }
        $order['exp_name'] = M("express")->where(['id'=>$order['exp_id']])->getField("name");
        return array("status"=>1,"message"=>"获取成功","data"=>$order);
    }
    
    /**
     * 获取具体搜索字段（非模糊）
     * @return
     */
    protected  function searchArray() {
    	
    	return [
    		OrderModel::$orderStatus_d
    	];
    }
    
    /**
     * 获取发货单
     */
    public function getOrderInvoice()
    {
    	$this->searchTemporary = [
    		OrderModel::$orderStatus_d => OrderStatus::AlreadyShipped,
    		OrderModel::$storeId_d => $_SESSION['store_id']
    	];
    	
    	return parent::getDataList();
    }
    
    /**
     * 获取排序
     */
    protected function getSearchOrderKey()
    {
    	return OrderModel::$createTime_d.' DESC, '. OrderModel::$payTime_d . ' DESC';
    }

}