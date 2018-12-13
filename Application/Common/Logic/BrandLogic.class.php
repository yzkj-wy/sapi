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

use Admin\Model\BrandModel;
use Think\Cache;

/**
 * 品牌逻辑处理
 * @author 王强
 * @version 1.0.0
 */
class BrandLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
         
        $this->modelObj = new BrandModel();

        $this->splitKey = $split;

        $this->covertKey = BrandModel::$brandName_d;
    }
    
    /**
     * 获取店品牌数据
     */
    public function getResult()
    {
        $cacheObj = Cache::getInstance('', ['expire' => 100]);

        $brandList = $cacheObj->get('brandList');
        
        if (empty($brandList)) {
            $brandList = $this->modelObj->getField(BrandModel::$id_d.','.BrandModel::$brandName_d);
        } else {
            return $brandList;
        }
        
        if (empty($brandList)) {
            return [];
        }
        
        $cacheObj->set('brandList', $brandList);
        
        return $brandList;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return BrandModel::class;
    }
}