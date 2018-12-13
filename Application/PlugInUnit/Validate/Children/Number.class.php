<?php
namespace PlugInUnit\Validate\Children;

use PlugInUnit\Validate\Validate;
use PlugInUnit\Validate\Common\CommonAttribute;

/**
 * 验证规则是否有特殊字符
 * @author Administrator
 */
class Number implements Validate
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
        $status = !is_numeric($this->data) ? false : true;
      
        if (!$status) {
            return false;
        }
       
        $str = '';
        if (false !== ($length = strpos($this->message, '${'))) {
            $str = str_replace(['${', '}'], ['', ''], substr($this->message, $length));
        }
       
        if (empty($str)) {
            return true;
        }
       
        list($first, $second) = explode('-', $str);
        
        if ( $first > $this->data || $this->data > $second) {
            return false;
        }
        
        return true;
    }
    
    public function __destruct()
    {
        unset($this->data, $this->message);
    }
}