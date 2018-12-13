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

namespace Common\TraitClass;

use Common\Tool\Event;

/**
 * 模型工具类 
 */
trait ModelToolTrait
{
    protected $fieldUpdate = 'id';
    
    //以。。。合并数组
    protected $mergeKey = null;
    
    
    protected $byNameSplit ;
    
    /**
     * @return the $fieldUpdate
     */
    public function getFieldUpdate()
    {
        return $this->fieldUpdate;
    }
    
    /**
     * @param string $fieldUpdate
     */
    public function setFieldUpdate($fieldUpdate)
    {
        $this->fieldUpdate = $fieldUpdate;
    }
    
    /**
     * 验证 数组以及是否为空
     */
    public function isEmpty (array $post)
    {
        return is_array($post) && (new \ArrayObject($post))->count();
    }
    
    
    /**
     * 获取最下级分类
     */
    protected static function flag($data, $forKey)
    {
        $flag = 0;
        foreach ($data[$forKey] as $key => $value) {
            if(!empty($value)) {
                $flag = $value;
                continue;
            }
            unset($data[$forKey][$key]);
        }
        return $flag;
    }
    
    /**
     * 是否 还有下级分类 
     */
    protected function isHaveSon ( &$data, $id)
    {
        if (empty($id)) {
            return ;
        }
       
        $data[$id] = $this->dataClass[$id];
        
        foreach ($this->dataClass as $name => $class)
        {
             if(!empty($id) && $class[static::$fid_d] == $id)
             {  
                 $this->isHaveSon($data, $class[static::$id_d]);
                 
                 $data[$id]['hasSon'] = 1;
             }
        }            
    }
    
   
    
    /**
     * 批量更新 组装sql语句【核心】
     * @param array $parseData 要更新的数据【已经解析好的】
     * @param array $keyArray  要修改的键
     * @param string 表名
     * @return $sql
     */
    public function buildUpdateSql( array $parseData, array $keyArray, $table)
    {
        if (empty($parseData) || !is_array($parseData) || empty($table)) {
            return array();
        }
    
        $sql = 'UPDATE '.$table.'  SET ';
        
        Event::listen('sql_update', $sql);//监听开端
        
        $flag = 0;
    
        $coulumValue = null;
    
        foreach ($keyArray as $k => $v) {
            $sql .=  '`'.$v.'`' .'= CASE '. '`'.$this->fieldUpdate.'`';
            foreach ($parseData as $a => $b)
            {
                $coulumValue = $this->isString ? '"'.$b[$flag].'"' : $b[$flag];
    
                $sql .= sprintf(" WHEN %s THEN %s \t\n ", $a, $coulumValue);
            }
            $flag++;
            $sql .='END,';
        }
    
        $sql = substr($sql, 0, -1);
        
        $where = ' WHERE `'.$this->fieldUpdate.'` in('.implode(',', array_keys($parseData)).')';
        //监听条件
        Event::listen('sql_update_where', $where);
        $sql .= $where;
    
        return $sql;
    }
    
    /**
     * 比较两次输入的密码是否相同 
     */
    public function parsePasswordSame(array $post)
    {
        if (!$this->isEmpty($post)) {
            return false;
        }
        
        $flag = null;
        $i = 0;
        foreach ($post as $value)
        {
            if ( ($i > 0 && $flag !== $value) || !$value ) {
                return false;
            }
            $flag = $value;
            $i++;
        }
        return $flag;
    }
    
    /**
     * 循环检测数据类型 
     */
    public function foreachDataTypeIsEmpty (array & $data, $dataType='intval', $numberValue = 0)
    {
        if (!$this->isEmpty($data)) {
            return false;
        }
        
        if (! function_exists($dataType)) {
            return false;
        }
        
        foreach ($data as $key => & $value) {
            if (($value= $dataType($value)) === $numberValue) {
                return false;
            }
        }
        return true;
    }
    
   
    
    /**
     * 处理时间 搜索条件
     * @param string 时间搜索
     * @return array 
     */
    public function parseTimeWhere($timeParam)
    {
        if (empty($timeParam) || false === strpos($timeParam, ' - ')) {
            return array();
        }
        
        list($startTime, $endTime) = explode(' - ', $timeParam);
        $startTime = strtotime($startTime);
        
        $endTime   = strtotime($endTime);
        
        return ['between', [$startTime, $endTime]];
    }
    
   
    
   
}