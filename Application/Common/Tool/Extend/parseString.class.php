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

namespace Common\Tool\Extend;

use Common\Tool\Tool;
use Common\Tool\Event;

/**
 * 字符处理帮助类 
 */
class parseString
{
    protected $string = null;
    
    public  static $split = ':';
    
    public function __construct($string)
    {
        $this->string = $string;
    }
    
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    /**
     * 添加字符串 
     */
    public function addString($string)
    {
        if (empty($string))
        {
            return ;
        }
        
        $this->string = $string;
    }
    
    /**
     * 检测依据符号分割的数据
     * @param array $args  array(',',':')
     * @ 
     */
    public function checkSplit(array $args, $string = null)
    {
        $string = $string === null ? $this->string : $string;
        if (empty($args))
        {
            throw new \ErrorException('无效的参数');
        }
        
        foreach ($args as $key => $value)
        {
             if (false === strpos($string, $value))
             {
                 throw new \ErrorException('未找到这样的分隔符');
             }
        }
       
    }
    
    /**
     * 根据某种格式 分解字符串 
     */
    public function joinString($string = null, array $split = array('douHao' =>',','fenHao' => ':'))
    {
        $string = $string === null ? $this->string : $string;
        
        if (empty($string))
        {
            return null;
        }
        
        $this->checkSplit($split, $string);
        $receive = array();
        return self::splitString($receive, $string);
    }
    /**
     * 5:6,2:3
     * array(
     *   0 => '5:6'//5号商品6件
     *   1 => '2:3'
     * ),
     */
    private static function splitString( & $receive ,$string, $split = ',' )
    {
        if (false !== strpos($string, $split))
        {
            $string = explode($split, $string); 
        }
        
       
        foreach ($string as $key => $value)
        {
            if (false !== strpos($value, self::$split))
            {
                self::splitString($receive, $value, self::$split);
            }
            else 
            {
               $key % 2 ===0 ? $baseParam[] = $value : ($key % 2 === 1 ?   $param[] = $value : false);
            }
        }
        
        if (!empty($baseParam))
        {
            foreach ($baseParam as $keyValue => $valueKey) 
            {
                if (!empty($param[$keyValue]))
                {
                    $keyValue % 2 ===0 ? $receive[$valueKey] = $param[$keyValue] : ($keyValue % 2 === 1 ?   $receive[$valueKey] = $param[$keyValue] : false);
                }
            }
        }
        return $receive;
    }
    /**
     * 字符拼接 
     */
    public function characterJoin(array $goodIds, $setkey = 'goods_id')
    {
        if (empty($goodIds))
        {
            return null;
        }
        $goods = [];
        $i = 0;
        foreach ($goodIds as $key => $value)
        {
            if (empty($value[$setkey])) {
                continue;
            } else if (is_numeric($value[$setkey])) {
                $goods[$i]= $value[$setkey];
            } else {
                $goods[$i]= '"'.$value[$setkey].'"';
            }
            $i++;
        }
        $goods = array_unique($goods);
        
        return implode(',', $goods);
    }
    
    /**
     * 重组数组 
     */
    public function buildArray(array $data, $setKey ='goods_id')
    {
        if (empty($data))
        {
            return $data;
        }
        foreach ($data as $key => &$value) 
        {
            $setkey = $value[$setKey];
            $data[$setkey] = $value;
            unset($data[$key]);
        }
        
        return $data;
    }
    
   
    /** 
     * 处理多对多数组
     * @param array $oneArray 被合并得数组
     * @param array $twoMany  合并到得数组
     * @param string $conditionKey 根据某个键 合并数据
     * @return array
     */
    public function oneReflectManyArray(array $oneArray , array $twoMany, $conditionKey ='address_id', $splitKey = 'id')
    {
        if (empty($oneArray) || empty($twoMany))
        {
            return $twoMany;
        }
       
//         $obj = new ArrayChildren($twoMany);
        
//         $isExist = $obj->isExistSameValue($conditionKey);
        
        
        $temp = array();
        
//         $eachArray = $isExist ?   $oneArray :  $twoMany;
        
//         $noExist   =   $isExist ?  $twoMany : $oneArray;
       
        foreach ($oneArray as $key => &$value)
        {
            if (empty($value[$conditionKey])) {
                continue;
            }
           
            $temp[$value[$conditionKey]] = $value;
        }
        
        $flagArray = array();
        
        foreach ($twoMany as $key => &$name)
        { 
//             if (! array_key_exists($name[$conditionKey], $temp) ) {
//                 continue;
//             }
        	$flagArray[$name['id']] = array_merge(empty($temp[$name[$splitKey]]) ? array() : $temp[$name[$splitKey]], $name);
        }
        
        return $flagArray;
    }
    
    /**
     * 比较 大小  大 true 小 false
     */
    public static function compare ($first, $second)
    {
        return $first > $second ? $first : $second;
    }
  
}