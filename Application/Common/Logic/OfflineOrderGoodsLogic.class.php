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
declare(strict_types=1);
namespace Common\Logic;
use Common\Logic\AbstractGetDataLogic;
use Common\Model\OfflineOrderGoodsModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use Think\Cache;

/**
 * 逻辑处理层
 * @author 王波
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class OfflineOrderGoodsLogic extends AbstractGetDataLogic
{
    protected $orderStatus = [2, 3, 4];//获取指定订单状态

    /**
     * 订单数据
     * @var array
     */
    private $orderSendData = [];

    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
        $this->data = $data;

        $this->modelObj = new OfflineOrderGoodsModel();
    }

    /**
     * 获取数据
     */
    public function getResult()
    {
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return OfflineOrderGoodsModel::class;
    }
}