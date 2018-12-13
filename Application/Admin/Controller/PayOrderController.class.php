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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\DispatcherPayTrait;
use Common\Logic\PayLogic;
use Validate\CheckParam;
use Common\Logic\OfflineOrderLogic;
use Think\SessionGet;

/**
 * 线下订单支付
 * @author Administrator
 */
class PayOrderController
{
    use InitControllerTrait;
    use DispatcherPayTrait;

    public function __construct(array $args)
    {
        $this->args = $args;

        $this->init();

        //线下订单支付
        $args['platform'] = 0;

        $args['special_status'] = 5;

        $this->logic = new PayLogic($args);
    }

    /**
     * 线下订单支付
     */
   public function openShop()
    {
        $this->objController->promptPjax(IS_POST , '不允许请求');
        //获取支付信息
        $payConfig = $this->logic->getResult();

        $this->objController->promptPjax($payConfig, '无法获取支付配置');

        $orderData = SessionGet::getInstance('order_data')->get();

        $result = $this->dispatcherPay($payConfig, $orderData);
        $this->objController->ajaxReturn($result);
    }
}