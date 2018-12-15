<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 20:06
 */
namespace Extend\Tl;
require_once 'AppConfig.php';
require_once 'AppUtil.php';

class TlClient {
    
    private $params = array();
    private $config;


    public function __construct(array $config = [], array $orderData = [])
    {
        $this->config=$config;
        $this->arams["cusid"] = $config['pay_account'];
        $this->params["appid"] = $config['merchantId'];
        $this->params["version"] = '11';
        $this->params["trxamt"] = $orderData['actual_amount'];
        $this->params["reqsn"] = $orderData['order_sn_id'];//订单号,自行生成
        $this->params["paytype"] = "W01";
        $this->params["randomstr"] = "JKL3J23J4LKDNF3";//
        $this->params["limit_pay"] = "no_credit";
        $this->params["validtime"] = "60";
        $this->params["notify_url"] = $config['notify_url'];
        

    }
    public function SignArray()
    {
        $this->params["sign"] = AppUtil::SignArray($this->params,$this->config['pay_key']);
    }
    
    public function  submit()
    {
        $this->SignArray;
        $paramsStr = AppUtil::ToUrlParams($this->params);
        $url = AppConfig::APIURL . "/pay";
        $rsp = $this->request($url, $paramsStr);
        $rspArray = json_decode($rsp, true); 

        if($this->validSign($rspArray)){
            echo "验签正确,进行业务处理";
        }
    }

    public function request($url,$params){
        $ch = curl_init();
        $this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
         
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
         
        $output = curl_exec($ch);
        curl_close($ch);
        return  $output;
    }

    public function validSign($array){
        if("SUCCESS"==$array["retcode"]){
            $signRsp = strtolower($array["sign"]);
            $array["sign"] = "";
            $sign =  strtolower(AppUtil::SignArray($array, $this->config['pay_key']));
            if($sign==$signRsp){
                return TRUE;
            }
            else {
                // echo "验签失败:".$signRsp."--".$sign;
            }
        }
        else{
            // echo $array["retmsg"];
        }
        
        return FALSE;
    }
}