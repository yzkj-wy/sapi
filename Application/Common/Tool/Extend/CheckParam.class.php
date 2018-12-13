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
namespace Common\Tool\Extend;

/**
 * 参数检测类
 * @author 王强
 * @version 1.0.0
 */
class CheckParam
{
    /**
     * 数据
     * @var array
     */
    private $data = [];
    
    private $noCheck = [];
    
    public function __construct(array $args, array $noCheck = [])
    {
        $this->data = $args;
        
        $this->noCheck = $noCheck;
    }
    
    /**
     * 验证字段是否存在
     * @param array $keys
     * @return boolean
     */
    public function keyExits(array $keys, array $checkData=[])
    {
        $data = empty($checkData) ? $this->data : $checkData;
    
        static $number = 0;
        
        if (empty($data)) {
            return false;
        }
        foreach ($data as $name => $value)
        {
            if (in_array($key, $this->noCheck)){//屏蔽不检测的键
                continue;
            } elseif (is_array($value)) {
                 $this->keyExits($keys, $value);
            }  elseif (is_numeric($name)) {
                continue;
            }  elseif (!in_array($name, $keys)) {
                $number++;
                return false;
            }
        }
        
        if ($number !== 0) {
            return false;
        }
        
        return true;
    }
    
   
    /**
     * 验证键值是否是数字类型
     * @param array $args
     * @return bool
     */
    public function isNumeric ($args, array $checkData = [])
    {
        $data = $this->data;
        
        static $number = 0;
        
        if (empty($data)) {
            return false;
        }
        
        foreach ($data as $key => $value)
        {
            if (is_array($value)) {
                $this->isNumeric($args, $value);
            }elseif (in_array($key, $args, true) && !is_numeric($value)) {
                $number++;
                return false;
            }
        }
        if ($number !== 0) {
            return false;
        }
        return true;
    }
}