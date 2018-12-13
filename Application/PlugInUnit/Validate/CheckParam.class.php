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
declare(strict_types=1);
namespace PlugInUnit\Validate;
use Common\Tool\Event;
/**
 * 检测参数
 * @author 王强
 */
class CheckParam
{
    /**
     * 消息提示
     * @var array
     */
    private $messageNotice = [];
    
    //数据验证路径
    const VALIDATE_MASTER_PATH = '\\PlugInUnit\\Validate\\Children\\';
    
    /**
     * 检测类对象实例
     * @var array
     */
    private static $obj = [];
    
    /**
     * 反射类对象实例
     * @var array
     */
    private static $ref = [];
    
    /**
     * 错误消息
     * @var string
     */
    private $errorMessage = '';
    
    /**
     * @var array
     */
    private $data = [];

     /**
     * @return the $errorMessage
     */
    public function getErrorMessage() :string
    {
        return $this->errorMessage;
    }

    public function __construct(array $messageNotice, $data)
    {
        $this->messageNotice = $messageNotice;

        $this->data = $data;
    }
    
    /**
     * @return the $messageNotice
     */
    public function getMessageNotice()
    {
        return $this->messageNotice;
    }

    /**
     * @param multitype: $messageNotice
     */
    public function setMessageNotice($messageNotice)
    {
        $this->messageNotice = $messageNotice;
    }
    
    
    /**
     * 检测参数
     * @param array $validateKey 验证信息
     * @param string $key 待验证数据的key
     * @return bool
     */
    protected function paramCheckNotify(array $validateKey, $key) :bool
    {
        $class = '';
        $reflectionObj = '';
    
        $validateObj = null;
    
        $result = false;
        
        $object = null;
        
        $ref = null;
        
        foreach ($validateKey as $reflection => $message) {
            $class = self::VALIDATE_MASTER_PATH.ucfirst($reflection);
            try {
                
                if (!isset(static::$obj[$class])) {
                    $reflectionObj = new \ReflectionClass($class);
                   
                    $validateObj = $reflectionObj->newInstanceArgs([$this->data[$key], $message]);
                    
                    $result = $reflectionObj->getMethod('check')->invoke($validateObj);
                    
                    static::$obj[$class] = $validateObj;
//                   
                    static::$ref[$class] = $reflectionObj;
                   
                    
                } else {
                    $object = static::$obj[$class] ;
                   
                    $ref    = static::$ref[$class]; 
                   
                    $ref->getMethod('setData')->invoke($object, $this->data[$key]);
                    
                    $ref->getMethod('setMessage')->invoke($object, $message);
                    
                    $result = $ref->getMethod('check')->invoke($object);
                }
                
                if ($result === false) {
                    $this->errorMessage = $message;
                    return false;
                }
               
            } catch (\Exception $e) {
                showData($e->getMessage());
                 $this->errorMessage = $message;
                 return false;
            }
        }
        return $result;
    }
    
    
    /**
     * 检测参数
     * @return boolean
     */
    public function checkParam () :bool
    {
        $message = $this->messageNotice;
       
        if (empty($message) || empty($this->data)) {
            return false;
        }
        
        $message = Event::insertObjectCallBack('parperParam', $message);
      
        $result = false;
    
        foreach ($message as $key => $value) {
    
            $result = $this->paramCheckNotify($value, $key);
           
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
    /**
     * 对象克隆
     */
    public function __clone() 
    {
        $this->messageNotice = $this->messageNotice; // 按值传值
    
        $this->errorMessage = $this->errorMessage;
    }
    
    /**
     * 析构方法
     */
    public function __destruct() 
    {
        unset($this->messageNotice, $this->errorMessage);
    }
}