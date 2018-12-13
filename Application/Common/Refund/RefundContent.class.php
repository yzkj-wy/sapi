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
namespace Common\Refund;

/**
 * 退货相关上下文
 * @author 王强
 * @version 1.0.0
 */
class RefundContent
{
    private $config = [];
    
    private $data = [];//当前配置序号
    
    
    /**
     * 架构方法
     * @param array $data
     */
    public function __construct(array $config, array $data)
    {
        $this->config = $config;
        
        $this->data = $data;
    }
    
    /**
     * 处理
     * @param string $columKey
     * @return boolean
     */
    public function execParse($columKey)
    {   
        if (!isset($this->config[$this->data[$columKey]])) {
            return [];
        }
        
        $class = $this->config[$this->data[$columKey]];
       
        $reust = [];
        
        try {
            $reflection = new \ReflectionClass($class);
            
            $obj = $reflection->newInstance($this->data);
            
            $reust =$reflection->getMethod('refund')->invoke($obj);
            
        } catch (\Exception $e) {
            throw $e;
        }
        return $reust;
    }
}