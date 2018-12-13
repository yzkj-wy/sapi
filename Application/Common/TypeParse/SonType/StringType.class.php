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

namespace Common\TypeParse\SonType;

use Common\TypeParse\AbstractParse;
/**
 * 字符串类型解析
 * @author 王强
 * @version 1.0.1
 */
class StringType extends AbstractParse 
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
     * 检测传参
     * @version 1.0
     */
    public function checkValue ()
    {
        $data = self::$typeData;
        
        if (empty($data) && $data !== 0 && $data !== '0') {
            return null;
        }
        
        $data = addslashes(strip_tags($data));
        
        return $data;
    }
}