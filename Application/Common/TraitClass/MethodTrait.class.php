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
 * 相关控制器处理方法
 * @author 王强
 */
trait MethodTrait 
{
    private $arrayData = array(); //数组类型数据

   
    
    /**
     * @return the $arrayData
     */
    public function getArrayData()
    {
        return $this->arrayData;
    }

    /**
     * @param multitype: $arrayData
     */
    public function setArrayData(array $arrayData)
    {
        $this->arrayData = $arrayData;
    }
    
    
    
}