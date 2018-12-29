<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
//declare(strict_types=1);
namespace Admin\Controller;

use Common\Tool\Tool;

use Common\Model\BaseModel;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\CustomsLogic;
use Common\TraitClass\MakeMessageTrait;

//use Common\TraitClass\OrderPjaxTrait;

use Think\Upload;
/**
 * 订单推送控制器
 * @author 王强
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class DLKJController
{
    use InitControllerTrait;

    use IsLoginTrait;

    use MakeMessageTrait;

//    use OrderPjaxTrait;

    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new CustomsLogic($args);
    }


    //订单列表
    public function send_DaLianKJ(){

        $orderdata = $this->logic->getOfflineOrderDetails();


        if($orderdata['statue']==0){

            $data=array (

                "status" => 0,

                "data" => "",

                "message" => "该订单未支付"
            );
        }
        else{
            $string=$this->DaLianKJ($orderdata,$this->args['type']);

            if (empty ($string)) {

                $data=array (

                    "status" => 0,

                    "data" => "",

                    "message" => "暂无数据"
                );
            }

            else{
                //$string组装成文件，提供下载
				
				try {
				    $path='/message/'.$orderdata['platform_short'].'/order/'.date('Ymd').'/';
					$filename = 'JKF_'.$orderdata['platform_short'].'_1_CEB311_'.$orderdata['order_sn_id'].'_'.date('YmdHis').'.xml';//xml文件名称
					$fp = fopen($path.$filename, 'w');
					fwrite($fp, $string);
					fclose($fp);				
					
					$data=array (

						"status" => 1,

						"data" => array (
							"downurl" => $path.$filename,
							"filename" => $filename
						),

						"message" => ""
					);
                    $sql['id']=$orderdata['id'];
                    $sql['send_order_status']='1';
                    M('offline_order')->save($sql);
				} catch (exception $e) {
					$data=array (

						"status" => 0,

						"data" => "",

						"message" => "XML文件生成失败，请与管理员联系!"
					);
				}
								
				
				
				
            }
        }

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    public function send_TongLianZF(){

        $orderdata = $this->logic->getOfflineOrderDetails();


        if($orderdata['statue']==0){

            $data=array (

                "status" => 0,

                "data" => "",

                "message" => "该订单未支付"
            );
        }
        else{
            $string=$this->DaLiaTongLianZF($orderdata);

            $path='/message/log/'.$orderdata['platform_short'].'/pay/'.date('Ymd').'/';
            $filename = $orderdata['platform_short'].'_'.$orderdata['order_sn_id'].'_'.date('YmdHis').'.xml';//xml文件名称
            $fp = fopen($path.$filename, 'w');
            fwrite($fp, $string);
            fclose($fp);

            if (empty ($string)) {

                $data=array (

                    "status" => 0,

                    "data" => "",

                    "message" => "暂无数据"
                );
            }

            else{

                //传送报文
                $url='https://service.allinpay.com/customs/pvcapply';

                $res=$this->curl_post($url,$string);

                if($res=='0000'){
                    $data=array (

                        "status" => 1,

                        "data" => "",

                        "message" => ""
                    );
                $sql['id']=$orderdata['id'];
                $sql['send_pay_status']='1';
                M('offline_order')->save($sql);
                }
                else{
                    $data=array (

                        "status" => 0,

                        "data" => $res,

                        "message" => "支付单推送失败"
                    );
                }

            }
        }

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    public function send_BCKuaJing(){

        $orderdata = $this->logic->getOfflineOrderDetails();


        if($orderdata['statue']==0){

            $data=array (

                "status" => 0,

                "data" => "",

                "message" => "该订单未支付"
            );
        }
        else{
            $string=$this->BCKuaJing($orderdata,$this->args['type']);

            $path='/message/log/'.$orderdata['platform_short'].'/BC/'.date('Ymd').'/';
            $filename = $orderdata['platform_short'].'_'.$orderdata['order_sn_id'].'_'.date('YmdHis').'.xml';//xml文件名称
            $fp = fopen($path.$filename, 'w');
            fwrite($fp, $string);
            fclose($fp);

            if (empty ($string)) {

                $data=array (

                    "status" => 0,

                    "data" => "",

                    "message" => "暂无数据"
                );
            }

            else{

                //传送报文
                $url='http://IP:8080/ ApiServer/service/custReceiceService?wsdl';

                $soap = new SoapClient(null, array('location'=> $url));

                $res=$soap->custReceice ( base64_encode($string), $orderdata['ebcCode'], md5($orderdata['bc_sign']));

                $res=$this->xml_to_array($res);

                if($res['Declaration']['Response'][0]==null)

                $msg=$res['Declaration']['Response'];

            else

                $msg=$res['Declaration']['Response'][0];


                if($msg['Status']>0 ){

                    $data=array (

                        "status" => 1,

                        "data" => "",

                        "message" => ""
                    );
                    $sql['id']=$orderdata['id'];
                    $sql['send_express_status']='1';
                    M('offline_order')->save($sql);
                }
                else{
                    $data=array (

                        "status" => 0,

                        "data" => $res['Declaration']['Response'],

                        "message" => '报送异常'
                    );
                }

            }
        }

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    public function send_DaLianZT(){

        $orderdata = $this->logic->getOfflineOrderDetails();


        if($orderdata['statue']==0){

            $data=array (

                "status" => 0,

                "data" => "",

                "message" => "该订单未支付"
            );
        }
        else{
            $string=$this->DaLianZT($orderdata);

            $path='/message/log/'.$orderdata['platform_short'].'/BBC/'.date('Ymd').'/';
            $filename = $orderdata['platform_short'].'_'.$orderdata['order_sn_id'].'_'.date('YmdHis').'.txt';//xml文件名称
            $fp = fopen($path.$filename, 'w');
            fwrite($fp, $string);
            fclose($fp);

            if (empty ($string)) {

                $data=array (

                    "status" => 0,

                    "data" => "",

                    "message" => "暂无数据"
                );
            }

            else{

                //传送报文
                $url='http://121.33.205.117:18080/customs/rest/custjson/addPubOrder.do';

                $res=$this->curl_post($url,$string);

                $res=$this->xml_to_array($res);

                $msg=json_decode($res,true);

                if($msg['status']=='1' ){

                    $data=array (

                        "status" => 1,

                        "data" => "",

                        "message" => ""
                    );
                    $sql['id']=$orderdata['id'];
                    $sql['send_express_status']='1';
                    M('offline_order')->save($sql);

                }
                else{

                    $data=array (

                        "status" => 0,

                        "data" => '',

                        "message" => $msg['notes']
                    );
                }

            }
        }

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    //单条订单数据
    public function orderDetail(){
        $data = $this->logic->getOrderDetail();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    //修改订单数据
    public function orderSave(){
        $data = $this->logic->getOrderSave();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }

    public function curl_post($url, $send_data){

        $options = array(
            CURLOPT_RETURNTRANSFER =>true,
            CURLOPT_HEADER =>false,
            CURLOPT_POST =>true,
            //CURLOPT_HTTPHEADER=>array(
            //   "Content-type: text/xml;charset=\"utf-8\"",
            //),
            CURLOPT_POSTFIELDS => $send_data,
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function xml_to_array($xml)
    {
        if (!$xml) {
            return false;
        }

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA),320), true);
        return $data;
    }
}