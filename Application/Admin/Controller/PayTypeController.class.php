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
declare(strict_types=1);
namespace Admin\Controller;
use Common\Tool\Tool;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Admin\Logic\PayTypeLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 支付方式
 * @author 王波
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class PayTypeController
{
    use InitControllerTrait;

    use IsLoginTrait;


    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();


        $this->args = $args;

        $this->logic = new PayTypeLogic($args);
    }


    //订单列表 - 全部订单
    public function payTypeList() :void
    {
        $data = $this->logic->getPayTypeList();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data);
    }
}