<?php
namespace PlugInUnit\Validate\Children;

use PlugInUnit\Validate\Validate;
use PlugInUnit\Validate\Common\CommonAttribute;

/**
 * 验证规则是否是时间
 * @author 王强
 */
class IsDate implements Validate
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
         if( date('Y-m-d H:i:s', strtotime($this->data))  == $this->data ) {
            return true;
         }
         return false;
    }
    
}