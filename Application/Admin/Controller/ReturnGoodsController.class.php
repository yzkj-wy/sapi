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

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\CancelOrder;
use Common\Tool\Tool;
use Common\Logic\OrderReturnGoodsLogic;
use Admin\Model\OrderModel;
use Common\Logic\OrderLogic;
use Common\Logic\GoodsLogic;
use Admin\Logic\UserLogic;
use Common\Logic\OrderGoodsLogic;
use Common\Refund\RefundContent;
use Common\Logic\StoreLogic;

class ReturnGoodsController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use CancelOrder;
    // 【1退货0换货】
    private $refundType = [
        '换货',
        '退货',
    ];
    // 【1退货0换货】
    
    private $refType = [
        '/adminprov.php/ReturnGoods/exchangeIsReceive',
        '/adminprov.php/ReturnGoods/returnIsReceive'
    ];
    
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->isNewLoginAdmin();
        
        $this->args = $args;
    
        $this->logic = new OrderReturnGoodsLogic($args);
    
        $this->init();
    }
    
    /**
     * 单商品退货
     * @copyright 版权所有©亿速网络
     */
    public function returnGoods ()
    {
        $this->objController->assign('returnGoodsType', C('returnGoods'));

        $this->objController->display();
    }
    
    /**
     * ajax 获取 退货单
     * @copyright 版权所有©亿速网络
     */
    public function ajaxGetReturnGoods ()
    {
        $listTitle = $this->logic->getShowComment();
      
        $where = array();
        Tool::connect('ArrayChildren');
    
        Tool::connect('parseString');
         
        $data = $this->logic->getDataList();#$model->getContent($_POST, $where);
        
        $this->objController->promptPjax($data, '暂无数据');
        
        $orderLogic = new OrderLogic($data['data'], $this->logic->getOrderSplitKey());
        
        $data['data'] = $orderLogic->getOrderByOrderReturn();
        
        $goodsLogic = new GoodsLogic($data['data'], $this->logic->getGoodsSplitKey());
    
        $data['data'] = $goodsLogic->getGoodsData();
        
        //获取店铺数据
        
        $storeLogic = new StoreLogic($data['data']);
        
        $storeLogic->setSplitKey($this->logic->getAuditorSplitKey());
        
        $data['data'] = $storeLogic->getStoreData();
        
        //@copyright 版权所有©亿速网络
        $this->objController->assign('orderModel', $orderLogic->getModelClassName());
    
        $this->objController->assign('title', $listTitle);
    
        $this->objController->assign('goodsModel', $goodsLogic->getModelClassName());
    
        $this->objController->assign('typeData', C('returnGoods'));
       
        $this->objController->assign('refund', C('refund'));
        $this->objController->assign('isReceive', C('is_receive'));
        $this->objController->assign('refTypeURL', $this->refType);
        
        
        $this->objController->assign('orderType', $this->refundType);
        
        $this->objController->assign('imageType', C('image_type'));
        
        $this->objController->assign('storeModel', $storeLogic->getModelClassName());
        
        $this->objController->assign('data', $data);
        
        $this->objController->assign('deleteURL', U('delete'));

        $this->objController->display();
    }
    
    /**
     * 获取退货单详情
     */
    public function getReturnGoodsInfo()
    {
        //检测传值
        $this->objController->errorArrayNotice($this->args);
    
        //退货信息
        $detail = $this->logic->getFindOne();
    
        $this->objController->promptParse($detail, '没有数据集');
    
        $oderSplitKey = $this->logic->getOrderSplitKey();
        
        $orderLogic = new OrderLogic($detail, $oderSplitKey);
    
        //订单信息
        $detail = $orderLogic->getOrderSnMergeData();
    
        //商品信息
        $goodsSplitKey = $this->logic->getGoodsSplitKey();
        
        $goodsLogic = new GoodsLogic($detail, $goodsSplitKey);
        
        $detail = $goodsLogic->geGoodsTitleMergeData();
    
        //用户信息
        $userLogic = new UserLogic($detail, $this->logic->getUserSplitKey());
    
        $detail = array_merge($detail, $userLogic->getUserName());
        //是否退货成功
        $orderGoodsLogic = new OrderGoodsLogic($this->logic->getOderStatusWhere());
        
        $status = $orderGoodsLogic->getStatus();
        
        $this->objController->assign('refund', C('refund'));
        $this->objController->assign('returnGoods', C('returnGoods'));
        $this->objController->assign('order', $orderLogic->getModelClassName());
        $this->objController->assign('status', $status);
        $this->objController->assign('goods', $goodsLogic->getModelClassName());
        $this->objController->assign('user', $userLogic->getModelClassName());
        $this->objController->assign(data, $detail);
    
        $this->objController->display();
    }
    
    /**
     * 修改 退货状态
     */
    public function editReturnGoods ()
    {
        $isPass = $this->logic->checkParam();
        
        $this->objController->promptPjax($isPass, $this->logic->getErrorMessage());
        
        $status = $this->logic->saveStatus();

        $this->objController->promptPjax($status !== false, $this->logic->getErrorMessage());
        
        if ($this->logic->getDoYouUseTransactions()) {
            $orderGoodsLogic = new OrderGoodsLogic($this->args);
            $status = $orderGoodsLogic->updateStatus();
            $this->objController->promptPjax($status !== false, $this->logic->getErrorMessage());
        }
        
        $this->objController->ajaxReturnData(['url' => U('returnGoods')]);
    }
    
    /**
     * 退款
     */
    public function cancelReturnOrder()
    {
        $this->objController->errorArrayNotice($this->args);
       
        $data = $this->logic->getFindOne();
       
        $this->objController->promptParse($data, $this->logic->getErrorMessage());
       
        $contentRefund = new RefundContent(C('refund_type'), $data);
       
        $result = $contentRefund->execParse($this->logic->getTypeSplitKey());
        
        $res = $this->cancelOrder($result);
        
        $this->objController->promptParse($res, $this->errorMsg);
        
        $status = $this->logic->exchangeCompletedStatus();
        
        $this->objController->promptParse($status, '更新退货状态失败');
       
        //更新状态
        //订单商品表
        $orderGoodsLogic = new OrderGoodsLogic($data);
       
        $status = $orderGoodsLogic->editStatus();
        
        $this->objController->promptParse($status, '更新订单商品状态失败');
        
        $this->objController->success('退款成功，请查收');
    }
    
    /**
     * 收货状态更改
     */
    public function returnIsReceive ()
    {
        $status = $this->logic->checkIsReciveColum();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        //库存比较
        $orderGoodsLogic = new OrderGoodsLogic($this->logic->buildOrderGoodsCondition(), $this->logic->getGoodsSplitKey());
        
        $goodsNumber = $orderGoodsLogic->getGoodsNumber();
        
        $refundNumber = $this->logic->getRefundGoodsNumber();
       
        $this->objController->promptPjax($goodsNumber >= $refundNumber && $refundNumber !==0, '退货数量不正确');
        
        $status = $this->logic->checkIsReceive();
    
        $this->objController->promptPjax($status, '状态有误');
        
        $goodsLogic = new GoodsLogic($this->logic->getReturnInventory());
        
        $goodsLogic->setSymbol($this->logic->getIstRefundStock());
        
        $status = $goodsLogic->returnInventory();
       
        $this->objController->updateClient($status, '回归库存');
    }
    
    /**
     * 换货 修改状态
     */
    public function exchangeIsReceive()
    {
        $status = $this->logic->checkIsReciveColum();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $status = $this->logic->checkIsReceive();
    
        $this->objController->promptPjax($status, '没有收到货或者状态错误');
        
        $this->logic->getModelObj()->commit();
        
        $this->objController->updateClient($status, '已收到货');
    }
    
    /**
     * 删除
     */
    public function delete()
    {
       $this->objController->promptPjax(!empty($this->args['id']), '缺少数据编号');
       
       $status = $this->logic->delete();
       
       $this->objController->promptPjax($status, '删除失败');
       
       //修改订单商品状态
       
       $orderGoodsLogic = new OrderGoodsLogic($this->logic->getOderStatusWhere());
       
       $status = $orderGoodsLogic->deleteRefundGoodsByUpdateStatus();

       $this->objController->promptPjax($status, '订单商品状态更新失败');
      
       // 发送站内信
       $this->objController->updateClient($status, '删除');
    }
}