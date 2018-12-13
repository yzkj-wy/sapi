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

namespace Common\Model;

use Think\Model;

/**
 * 获取配置分类的数据模型
 * @author 王强
 * @version 1.0.1
 */
class ConfigChildrenModel extends Model
{
    private static $obj;
    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
    /**
     * 获取全部数据 
     */
    public function getAllConfig(array $options = NULL)
    {
        return $this->field('type_name')->where($options)->select();
    }
    
}