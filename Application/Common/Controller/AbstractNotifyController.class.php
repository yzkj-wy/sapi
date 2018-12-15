<?php
declare(strict_types = 1);
namespace Common\Controller;

use Think\Hook;
use Common\Behavior\Balance;
use Common\Behavior\AlipaySerialNumber;
use Common\Behavior\Decorate;
use Common\TraitClass\WxNofityTrait;
use Common\TraitClass\OrderNoticeTrait;
use Common\TraitClass\AlipayNotifyTrait;
use Common\TraitClass\WxListenResTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\GETConfigTrait;
use Common\TraitClass\PayTrait;
use Think\SessionGet;
/**
 * 通知抽象类
 * @author Administrator
 *
 */
abstract class AbstractNotifyController {
	use GETConfigTrait;
	use OrderNoticeTrait;
	use AlipayNotifyTrait;
	use InitControllerTrait;
	use WxNofityTrait;
	use WxListenResTrait;
	use PayTrait;
	
	
	
	/**
	 * pc 和 wap 回调
	 */
	public function wxNofity() :void
	{
		$this->returnData = file_get_contents('php://input');
			
		$this->args = $this->getTheCustomParamter();
		
		$this->sessionInit();
		
		$payConfig = SessionGet::getInstance('pay_config_by_user')->get();
		
		$this->msg($payConfig);
		
		$this->getPayConfig($payConfig);
		
		$resource = $this->nofityWx();
		
		$this->msg($resource);
		
		Hook::add( 'aplipaySerial',Decorate::class );
		
		$this->getPayIntegral();
		
		$status = $this->orderNotice();
		
		$this->msg( $status );
		
		echo 'SUCCESS';
		die();
		
	}

    public function tlNofity ():void
    {
        $this->returnData = $_POST;
        $this->notify=$this->returnData;
//        file_put_contents('kjt_test_log.txt',$args['order_sn']."\n", FILE_APPEND | LOCK_EX);
        file_put_contents('kjt_test_log.txt',json_encode( $this->notify,320)."\n", FILE_APPEND | LOCK_EX);

        $where['mchid']=$this->notify["merchantId"];
        $where['pay_type_id']=5;
        $where['special_status']=0;
        $this->payConfData=M('pay')->field('create_time,update_time,payCode,payName',true)->where($where)->find();
//        file_put_contents('kjt_test_log.txt',json_encode( $this->payConfData,320)."\n", FILE_APPEND | LOCK_EX);
        $this->msg( $this->payConfData);

        $tlnotify = new TlNotify($this->returnData, $this->payConfData);

        $verify=$tlnotify->check();
//        file_put_contents('kjt_test_log.txt',$verify."\n", FILE_APPEND | LOCK_EX);
//        file_put_contents('kjt_test_log.txt','vertify past'."\n", FILE_APPEND | LOCK_EX);
        $this->msg($verify);


        $where=array();

        $where["order_sn_id"]=$this->notify["orderNo"];

        $this->orderdata=M('order')->field('id,order_sn_id,order_status,store_id')->where($where)->select();


//        Hook::add('TlSerial', TlSerialNumber::class);

//        $this->notify['total_amount'] = $this->notify['orderAmount']*100;

//        $this->result = $data;

//        $this->sessionInit();

//        $this->getPayIntegral();

//        $status = $this->orderNotice();
        $sql['id'] =   $this->orderdata["id"];

        $sql['payment_order_id'] =  $this->notify['paymentOrderId'];

        $sql['status'] =  '1';

        $status=M('offline_order')->save($sql);

        $this->msg($status);

        echo 'SUCCESS';

        die();

    }


    /**
	 * 异步通知
	 */
	public function alipayNotify() :void
	{
		$this->data = $_POST;
		
		$alipayConf = $this->parseResultConf();
		
		$this->msg($alipayConf);
		
		$this->args = $alipayConf;
		
		$this->sessionInit();
		
		$data = $this->alipayResultParse();
		
		$this->msg($data);
		
		$this->tradeNo = $this->data['trade_no'];
		
		Hook::add( 'aplipaySerial',AlipaySerialNumber::class );
		
		$this->getPayIntegral();
		
		$status = $this->orderNotice();;
		
		$this->msg($status);
		
		echo "SUCCESS";
		die();
	}
	
	
	/**
	 * 余额支付通知
	 */
	public function balanceNofty() :void
	{
		$this->sessionInit();
		
		Hook::add( 'aplipaySerial', Balance::class );
		
		$this->getPayIntegral();
		
		$status = $this->orderNotice();
		
		$this->msg($status);
		
		echo 'SUCCESS';die();
	}
	
// 	/**
// 	 * 银联同步回调
// 	 */
// 	public function UnionSynchronous()
// 	{
// 		$this->redirect( 'Home/Order/order_details',[ 'id' => (int)\substr( I( 'orderId' ),24 ) ] );
// 	}
	
	
// 	/**
// 	 * 银联异步回调
// 	 */
// 	public function UnionAsynchronous()
// 	{
// 		$data = I( 'post.' );
// 		if( empty( $data ) ){
// 			E( '非法请求' );
// 		}
// 		if( $data[ 'respCode' ] != '00' && $data[ 'respCode' ] != 'A6' ){
// 			die;
// 		}
		
// 		$info = AcpService::validate( $data );
		
// 		if( !$info ){
// 			die( '验签失败' );
// 		}
// 		echo '验签成功';
// 		$orderId   = (int)\substr( $data[ 'orderId' ],24 );
// 		$OrderData = BaseModel::getInstance( OrderModel::class )->where( [ 'id' => $orderId ] )->getField( 'order_status' );
// 		if( $OrderData !== '0' ){
// 			E( '订单错误' );
// 		}
		
// 		$status = $this->orderNotice( $orderId );
		
// 		//将部分数据写入银联退款表
// 		$refundData                    = [];
// 		$refundData[ 'order_sn_id_r' ] = $data[ 'orderId' ];
// 		$refundData[ 'origQryId' ]     = $data[ 'queryId' ];
// 		$refundData[ 'money' ]         = (float)$data[ 'txnAmt' ] / 100;
// 		$status2                       = M( 'unionrefund' )->add( $refundData );
		
// 		if( !$status ){
// 			Log::write( '订单-' . $orderId . '-修改状态失败' );
// 		}
// 		if( !$status2 ){
// 			Log::write( '订单-' . $orderId . '-插入退款表失败' );
// 		}
// 		die;
// 	}
	
	
	/**
	 * 获取积分比例
	 */
	private function getPayIntegral() :void
	{
		$this->key         = 'integral';
		$payIntegral       = $this->getGroupConfig()[ 'integral_proportion' ];
		$this->payIntegral = $payIntegral;
	}
	
	/**
	 * 余额支付通知
	 */
	private function sendBalanceSms()
	{
		if( M( 'sms_check' )->where( [ 'check_title' => '余额支付提示' ] )->getField( 'status' ) ){
			$userPhone = M( 'user' )->where( [ 'id' => $_SESSION[ 'user_id' ] ] )->getField( 'mobile' );
			$sms       = new MsmFactory();
			$sms->factory( $userPhone,5 );//5为 余额通知的短信模板
		}
	}
	
	private function msg($status) :void
	{
		if (empty($status)) {
			echo 'ERROR';
			die();
		}
	}
	
	public function __destruct()
	{
		unset($this->data, $this->errorMessage, $this->orderGoodsParseClass, $this->payConfData, $this->payData, $this->payReturnData);
	}
}