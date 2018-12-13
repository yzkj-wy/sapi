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

use Common\Model\WaybillPrintItemModel;
use Common\Model\ExpressModel;

/**
 * 打印项逻辑处理
 * @author 王强
 */
class WaybillPrintItemLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data = [], $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new WaybillPrintItemModel();
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return WaybillPrintItemModel::class;
    }
    
    /**
     * 获取数据并缓存
     */
    public function getNoPageListCache()
    {
        $data = S('data_print_item');
        
        if (empty($data)) {
            $data =  $this->modelObj->where(WaybillPrintItemModel::$status_d.' = 1')->select();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        S('data_print_item', $data, 600);
        
        return $data;
    }
}