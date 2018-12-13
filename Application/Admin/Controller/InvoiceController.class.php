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
namespace Admin\Controller;

use Common\Tool\Tool;
use Common\Model\UserAddressModel;
use Common\TraitClass\GETConfigTrait;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderLogic;
use Common\Tool\Event;
use Common\TraitClass\OrderPjaxTrait;
use Common\Logic\OrderGoodsLogic;
use Common\Logic\GoodsLogic;

/**
 * 发货单 
 */
class InvoiceController 
{
    use GETConfigTrait;
    
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use OrderPjaxTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new OrderLogic($args);
    
        $this->init();
    }
    
    public function index ()
    {
        $this->objController->ajaxReturnData($this->logic->getAppointData());
    }
    
    /**
     * 获取配货单 
     */
    public function ajaxGetDataSource ()
    {
        new Event("buildOrderWhere", $this->logic);
        
        $this->ajaxGetData();
        
    }
    
    /**
     * 配货单详情 
     */
    public function picking ()
    {
        $data = $this->getOrder();

        $addressData = $this->getAddressInfo($data);
        
        $data = array_merge($addressData, $data);
        
        //商品信息
        $goods = $this->getGoodsInfoByOrder($data);
       
        //获取网站配置
        $this->objController->assign('intnetConfig', $this->getIntnetInformation());
        
        $this->objController->assign('addressModel', UserAddressModel::class);
        
        $this->objController->assign('orderData', $data);
        
        $this->objController->assign('orderDataGoods', $goods);

        $this->objController->display();
    }
    
    /**
     * 获取订单信息 
     */
    private function getGoodsInfoByOrder ($data)
    {
        if (empty($data)) {
            return array();
        }
       
        //传递给商品订单模型
        $orderGoodsLogic = new OrderGoodsLogic($data);
        
        //去除不查询的字段
        $orderGoods  = $orderGoodsLogic->getGoodsIdByOrderId();
        
        $goodsLogic = new GoodsLogic($orderGoods, $orderGoodsLogic->getGoodsSplitKey());
        
        Tool::connect('parseString');
        //传递给商品模型
        $goodsDatail = $goodsLogic->getGoodsData();;
        
        return $goodsDatail;
    }
}