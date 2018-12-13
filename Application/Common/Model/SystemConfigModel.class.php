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

namespace Common\Model;


class SystemConfigModel extends BaseModel
{
    private static $obj;

	public static $id_d;
    
	public static $key_d;
	
	public static $configValue_d;

	public static $classId_d;

	public static $parentKey_d;

	public static $createTime_d;

	public static $updateTime_d;
    

	public static $currentId_d;	//当前配置分类【编号】

    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
    
    public function getAllConfig(array $option = null)
    {
        $data = $this->field('create_time,update_time', true)->where($option)->select();
        
        if (!empty($data))
        {
            foreach ($data as $key => &$value)
            {
                if (!empty($value['config_value']))
                {
                    $unData = unserialize($value['config_value']);
                    unset($data[$key]['config_value']);
                    $value = array_merge($value, $unData);
                }
            }
        }
        return $data;
    }
    
    /**
     * @desc 依据某个键 获取 子集
     * @param string $key  父级键名
     * @return array
     */
    public function getDataByKey($key = null)
    {
        $cacheKey = md5($key).'_'.$key;
    
        $data = S($cacheKey);
    
        if (empty($data)) {
            
            $field = self::$classId_d.','.self::$configValue_d;
            
            $data = $this->where(self::$parentKey_d .' = "%s"', $key)->getField($field);
      
            if (empty($data)) {
                return array();
            }
        
            foreach ($data as $key => & $value)
            {
                $value = unserialize($value);
            }
            S($cacheKey, $data, 100);
       }
       return $data;
    }
    
    /**
     * 更新配置
     */
    public function updateConfig()
    {
        
    }
    /**
     * 获取配置
     */
    public function getConfigByWhere($where,$field)
    {
        $data = $this->field($field)->where($where)->select();
        return $data;
    }
}
