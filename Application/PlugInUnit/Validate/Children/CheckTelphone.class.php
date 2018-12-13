<?php
namespace PlugInUnit\Validate\Children;

use PlugInUnit\Validate\Validate;

/**
 * 验证规则是否是正确的电话号码
 * @author Administrator
 */
class CheckTelphone implements Validate
{
    private $data;
    
    /**
     * 架构方法
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \PlugInUnit\Validate\Validate::check()
     */
    public function check() :bool
    {
        return (preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$this->data)) ? true : false;
    }
}