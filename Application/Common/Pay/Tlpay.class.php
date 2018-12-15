<?php
namespace Common\Pay;
use Common\TraitClass\PayTrait;
use Extend\Tl\TlClient;
use Think\SessionGet;
use Common\Model\OrderModel;
class Tlpay{

    use PayTrait;

    private $config = [];

    private $orderData = [];

    public function __construct(array $config = [], array $orderData = [])
    {

        $this->config = $config;

        $this->orderData = $orderData;
    }

    public function pay(){


        $info = $this->orderData ;
        
            $res = M('offline_order')->field('order_sn_id，actual_amount')->where('id=' . key($this->orderData))->find();
//        file_put_contents('kjt_test_log.txt',json_encode($res,320)."\n", FILE_APPEND | LOCK_EX);

            $this->orderData['order_sn_id'] = $res['order_sn_id'];
        
        $this->orderData['priceSum']=$res['actual_amount'];

        if (bccomp( $this->orderData['priceSum'], 0.00, 2) === -1 ) {
            return [

                'data'=> '',

                'message'=>  '价格异常',

                'status'=>  0
            ];
        }

        $payConfig = $this->config;

        $token = $payConfig['token'];

        unset($payConfig['token']);

        SessionGet::getInstance('pay_config_by_user', $payConfig)->set();

        $tl=new TlClient($this->config,$this->orderData);
        
        return [

            'data' =>$tl->submit(),

            'message' => '成功',

            'status' => 1
        ];
    }

}
