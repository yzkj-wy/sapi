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
use Common\Model\StoreEvaluateModel;
use Think\Exception;

/**
 * 店铺分类
 * @author 王强
 * @version 1.0.0
 */
class StoreEvaluateLogic extends AbstractGetDataLogic
{
    private $error;
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = [], $split = '')
    {
        $this->data = $args;
        
        $this->splitKey = $split;
        
        $this->modelObj = new StoreEvaluateModel();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
         //TODO
    }
    
    public function getModelClassName()
    {
        return StoreClassModel::class;
    }
    public  function score(){
        $res =  $this->modelObj->getScore();
        return $res;
    }
}