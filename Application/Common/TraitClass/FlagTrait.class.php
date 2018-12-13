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


/**
 * 数据处理 
 */
trait  FlagTrait 
{
    /**
     * @desc  商品添加 提取不同规格中的 库存价格
     * @param array $data  规格数据
     * @param string $deleteKey  要去掉的键
     * @return array
     */
    public function loadSpecificalByStockAndPrice(array $data, $deleteKey = 'sku')
    {
        if (empty($data) || !is_array($data)) {
            return array();
        }
        
        foreach ($data as $key => & $value) {
            if (!array_key_exists($deleteKey, $value)) {
                continue;
            }
            unset($data[$key][$deleteKey]);
        }
        return $data;
    }
}