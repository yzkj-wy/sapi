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

use Common\Model\WaybillPrintDataModel;

class WaybillPrintDataLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
         
        $this->modelObj = new WaybillPrintDataModel();
    
        $this->splitKey = $split;
    
        //         $this->covertKey
    }
    
    /**
     * 获取店铺地址数据
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
        return WaybillPrintDataModel::class;
    }
    
    /**
     * @return array
     */
    public function getPrintData()
    {
        
        $field = [
            WaybillPrintDataModel::$id_d,
            WaybillPrintDataModel::$dialogHeight_d,
            WaybillPrintDataModel::$dialogLeft_d,
            WaybillPrintDataModel::$printItem_d,
            WaybillPrintDataModel::$dialogTop_d,
            WaybillPrintDataModel::$dialogWidth_d,
            WaybillPrintDataModel::$waybillId_d
        ];
        
        $data = $this->modelObj->where(WaybillPrintDataModel::$status_d.' = 0')->getField(implode(',', $field));
        
        
       
            
    }
}