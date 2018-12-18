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
        file_put_contents('kjt_test_log.txt',"here\n", FILE_APPEND | LOCK_EX);
        $this->config = $config;

        $this->orderData = $orderData[0];
    }

    public function pay(){


        $info = $this->orderData ;
        file_put_contents('kjt_test_log.txt',json_encode($info,320)."\n", FILE_APPEND | LOCK_EX);
        $res = M('offline_order')->field('order_sn_id,actual_amount')->where('id=' . $this->orderData['order_id'])->find();
        file_put_contents('kjt_test_log.txt',json_encode($res,320)."\n", FILE_APPEND | LOCK_EX);

        $this->orderData['order_sn_id'] = $res['order_sn_id'];

        $arr_str=$res['actual_amount']*100;

        $this->orderData['priceSum']=substr($arr_str,0,strlen($arr_str)-3);

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
        file_put_contents('kjt_test_log.txt',json_encode($data,320)."\n", FILE_APPEND | LOCK_EX);
        return [

            'data' =>$data,

            'message' => '成功',

            'status' => 1
        ];
    }

}
