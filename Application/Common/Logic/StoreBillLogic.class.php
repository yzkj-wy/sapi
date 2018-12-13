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
namespace Common\Logic;


use Common\Model\StoreBillModel;

class StoreBillLogic extends AbstractGetDataLogic
{
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->modelObj = new StoreBillModel();
    }

    public function getResult()
    {

    }
//
//    /**
//     * @description
//     * @return array
//     */
//    protected function searchTemporary()
//    {
//        return [
//            StoreBillModel::$storeId_d => $_SESSION['store_id']
//        ];
//    }

    /**
     * @description 获取结算信息
     * @return array
     */
    public function getList()
    {
        $this->searchTemporary = [
            StoreBillModel::$storeId_d => $_SESSION['store_id']
        ];
        return $this->getDataList();
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return StoreBillModel::class;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
        return [
            StoreBillModel::$id_d,
            StoreBillModel::$stmentSn_d,
            StoreBillModel::$startTime_d,
            StoreBillModel::$endTime_d,
            StoreBillModel::$status_d,
            StoreBillModel::$orderPrice_d,
            StoreBillModel::$payRemarks_d,
            StoreBillModel::$platformPercentage_d,
            StoreBillModel::$totalShipping_d,
            StoreBillModel::$payTime_d
        ];
    }
    
    /**
     * 审核处理
     * @return array
     */
    public function getOrderWhere()
    {
        $data = $this->getFindOne();
        
        if( empty( $data ) ){
            return [];
        }
        
        return [
            'store_id'   => $_SESSION['store_id'],
            'start_time' => $data[ StoreBillModel::$startTime_d ],
            'end_time'   => $data[ StoreBillModel::$endTime_d ]
        ];
    }
    
   	/**
   	 * 
   	 * {@inheritDoc}
   	 * @see \Common\Logic\AbstractGetDataLogic::getCacheKey()
   	 */
    protected function getCacheKey() :string
    {
        if (empty($_SESSION['store_id'])) {
            throw new \Exception('系统异常');
        }
        
        $key = 'bill_dr_'.$_SESSION['store_id'].'858_data';
        
        return $key;
    }
    
    /**
     * 店家确认验证消息
     */
    public function getMessageByShopConfirm()
    {
        return [
            StoreBillModel::$id_d => [
                'number' => '主键编号必须存在',
            ],
            StoreBillModel::$status_d => [
                'number' => '状态必须是数字，且必须介于${2-3}'
            ]
        ];
    }
}