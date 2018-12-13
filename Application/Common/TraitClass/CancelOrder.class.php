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
namespace Common\TraitClass;

use Common\Model\PayModel;
use Admin\Model\OrderModel;
use Common\Logic\PayLogic;
use Think\Cache;

/**
 * 退货
 */
trait CancelOrder
{

    private $currtModel;
    
    private $errorMsg = '';

    /**
     *  公共退款
     * @param array $result 订单编号
     * @return mixed
     */
    public function cancelOrder($result)
    {
        if (empty($result)) {
            return [];
        }
        
        // 获取支付类型
        
        $cache = Cache::getInstance('', ['expire' => 20]);
        
        $key = $_SESSION['store_id'].'_click';
        
        $click = $cache->get($key);
       
        if ($click >= 3) {
            $this->errorMsg = '杜绝恶意点击';
            return false;
        }
        
        if (empty($click) || $click < 3) {
        
            $click += 1;
            
            $cache->set('click', $click);
        }
        
        $data = $this->getPayConfigByDataBase($result);
        
        if (empty($data)) {
            return false;
        }
        
        
        try {
            $className = str_replace('/', '\\', $data['return_name']) ;
            
            $obj = new \ReflectionClass($className);
            
            $instance = $obj->newInstanceArgs([$result, $data]);
            
            $status = $obj->getMethod('refundMonery')->invoke($instance);
            
            $this->errorMsg = $obj->getMethod('getError')->invoke($instance);
            return $status;
            
        } catch (\Exception $e) {
            
        	$this->errorMsg = $e->getMessage();
        	return false;
        }
        
       
    }

    /**
     * 获取配置数据
     */
    private function getPayConfigByDataBase(array $info)
    {
        if (empty($info)) {
            return array();
        }
        $payModel = new PayLogic($info);
        
        $data = $payModel->getResult();
       
        if (empty($data)) {
            return array();
        }
        return $data;
    }

}