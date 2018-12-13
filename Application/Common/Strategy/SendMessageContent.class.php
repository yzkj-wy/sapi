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
namespace Common\Strategy;

/**
 * 消息上下文
 * @author Administrator
 * @version 1.0.0
 */
class SendMessageContent
{
    private $userSetting; //构造方法参数
    
    private $systemSetting; //系统配置
    
    /**
     * 架构方法
     * @param array $userSetting   用户配置
     * @param array $systemSetting 系统配置
     */
    public function __construct( array $userSetting, array $systemSetting)
    {
        $this->userSetting = $userSetting;
        
        $this->systemSetting = $systemSetting;
    }
   
    /**
     * 解析配置
     */
    
    protected function parseConfig ()
    {

        $siteSms = $this->systemSetting['smt_message_switch'] && ($this->systemSetting['smt_message_forced'] || $this->userSetting['sms_message_switch_']);
        
        $smsStatus = $this->systemSetting['smt_short_switch'] && ($this->systemSetting['smt_short_forced'] || $this->userSetting['sms_short_switch']);
        
        $mailStatus = $this->systemSetting['smt_mail_switch'] && ($this->systemSetting['smt_mail_forced'] || $this->userSetting['sms_mail_switch']);
        
        $config = C('msg_setting');
        
        $config[0]['status'] = $siteSms;
        
        $config[1]['status'] = $smsStatus;
        
        $config[2]['status'] = $mailStatus;
        
        return $config;
        
    }
    
    
    /**
     * 发送消息
     */
    public function send ()
    {
        $config = $this->parseConfig();

        foreach ($config as $key => $value) {
            
            if ($value['status'] === false) {
                continue;
            }
            try {
                $reflection   = new \ReflectionClass($value['class']);
                $obj    = $reflection->newInstanceArgs([$this->userSetting, $this->systemSetting]);
                $status = $reflection->getMethod('sendMessage')->invoke($obj);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        return $obj;
    }
    
}