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


class TlNotify {

    private $params=array();
    private $config=array();

    public function __construct(array $data = [],array $config = [] )
    {
        $this->config=$config;
        $this->params=$data;
    }

    public function check(){
    $params = array();
    foreach($this->params as $key=>$val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
        $params[$key] = $val;
    }
    if(count($params)<1){//如果参数为空,则不进行处理
        return false;
    }
    if(AppUtil::ValidSign($params, $this->config['pay_key'])){//验签成功
        //此处进行业务逻辑处理
        return true;
    }
    else{
        return false;
    }

    }
    

}