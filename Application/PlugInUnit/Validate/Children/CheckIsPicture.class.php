<?php
namespace PlugInUnit\Validate\Children;

use PlugInUnit\Validate\Common\CommonAttribute;

/**
 * 验证是否是图片
 * @author 王强
 */
class CheckIsPicture 
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
     * 非空验证长度
     * {@inheritDoc}
     * @see \PlugInUnit\Validate\Validate::check()
     */
    public function check() :bool
    {
        if (!empty($this->data)) {
            return getimagesize($this->data) ? true :false;
        }
        return true;
    }
}