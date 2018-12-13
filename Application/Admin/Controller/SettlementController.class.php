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
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreBillLogic;
use PlugInUnit\Validate\CheckParam;

/**
 * 结算
 * @author 王强
 * @version 1.0
 */
class SettlementController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
        
        $this->args = $args;
        
        $this->logic = new StoreBillLogic($args);
    }
    
    /**
     * 结算列表
     */
    public function listBySettlement()
    {
        $data = $this->logic->getDataList();

        $config = C('settle');
        
        $this->objController->ajaxReturnData([
            'settle' => $data,
            'status' => $config
        ]);
    }
    
    /**
     * 订单列表
     */
    public function orderList()
    {
        
        $this->objController->promptPjax( $this->logic->checkIdIsNumric(),$this->logic->getErrorMessage() );
        
        $orderWhere = $this->logic->getOrderWhere();
        
        $this->objController->promptPjax( $orderWhere,'数据异常' );
        
        $_SESSION[ 'order_where_by_settlement' ] = $orderWhere;
        
        $this->objController->ajaxReturnData( [
                'ajaxURL' => 'Order/orderListByStore',
                'timegp'  => $this->logic->getTimeGpKey()
            ]
        );
    }
    
    /**
     * 商家确认
     */
    public function shopConfirm()
    {
        $checkObj = new CheckParam($this->logic->getMessageByShopConfirm(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $status = $this->logic->saveData();
        
        $this->objController->updateClient($status, '操作');
        
    }
    
}