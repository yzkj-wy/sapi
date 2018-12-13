<?php
namespace Common\Strategy;

abstract class  AbstractMessage
{
    protected $userSetting; //用户配置参数
    
    protected $systemSetting; //系统配置
    
    /**
     * 发送消息
     */
    abstract public function sendMessage();
}
