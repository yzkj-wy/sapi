<?php
namespace PlugInUnit\Validate\Children;

use PlugInUnit\Validate\Validate;
use PlugInUnit\Validate\Common\CommonAttribute;

/**
 * 验证规则是否有特殊字符
 * @author Administrator
 */
class CheckURL implements Validate
{
    use CommonAttribute;
    
    /**
     * 架构方法
     */
    public function __construct($data, $message = '')
    {
        $this->data = $data;
        
        $this->message = $message;
    }
    
    /**
     * {@inheritDoc}
     * @see \PlugInUnit\Validate\Validate::check()
     */
    public function check() :bool
    {
        if (empty($this->data)) {
            return true;
        }
        
        return  filter_var($this->data, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) ? true : false ;
    }
}