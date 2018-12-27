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

        $this->orderData = $orderData[0];
    }

    public function pay(){


        $info = $this->orderData ;

        $res = M('offline_order')->field('order_sn_id,actual_amount')->where('id=' . $this->orderData['order_id'])->find();

        $this->orderData['order_sn_id'] = $res['order_sn_id'];

        $this->orderData['priceSum']=$res['actual_amount']*100;

        if (bccomp( $this->orderData['priceSum'], 0.00, 5) === -1 ) {
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

        $data=$tl->submit();

        if($data['retcode']=='SUCCESS')

        return [

            'data' =>$data,

            'message' => '成功',

            'status' => 1
        ];

        else
            return [

                'data' =>null,

                'message' => $data['retmsg'],

                'status' => 0
            ];

    }

}
