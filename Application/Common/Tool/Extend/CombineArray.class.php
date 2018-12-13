<?php
namespace Common\Tool\Extend;

class CombineArray 
{
    /**
     * 被组合的数组
     * @var array
     */
    private $array = [];
    
    //关联key
    private $key = '';
    
    /**
     * 构造方法
     * @param array $array
     * @param string $key
     */
    public function __construct(array $array, $key)
    {
        $this->array = $array;
        
        $this->key = $key;
    }
    /**
     * 处理关联数组
     * @param array $array 要合并的数组
     * @param unknown $byKey 要合并数组的关联key
     * @return [];
     */
    public function parseCombine (array $array, $byKey)
    {
        
        $data = $this->array;
        
        if (empty($data)) {
        	return [];
        }
        
        $temp = $this->arrayCoverToMap();
        
        if (empty($temp)) {
        	return $data;
        }
      	  
      
        $flag = [];
        foreach ($array as $name => $val) {
            
           $flag[$val[$byKey]] = array_merge(empty($temp[$val[$byKey]]) ? array() : $temp[$val[$byKey]], $val);
        }
        
        return $flag;
    }
    
    /**
     * 数组
     * @return array
     */
    private function arrayCoverToMap()
    {
    	$data = $this->array;
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$parseKey = $this->key;
    	
    	$temp = [];
    	foreach ($data as $key => $value)
    	{
    		$temp[$value[$parseKey]] = $value;
    	}
    	
    	unset($data);
    	
    	return $temp;
    }
    
    /**
     * 处理关联数组
     * @param array $array 要合并的数组
     * @param string $byKey 要合并数组的关联key
     * @return [];
     */
    public function parseCombineList (array $array, $byKey)
    {
    	$data = $this->array;
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$temp = $this->arrayCoverToMap();
    	
    	if (empty($temp)) {
    		return [];
    	}
    	
    	$flag = [];
    	
    	$i = 0;
    	
    	foreach ($array as $name => $val) {
    		
    		$flag[$i] = array_merge(empty($temp[$val[$byKey]]) ? array() : $temp[$val[$byKey]], $val);
    		
    		$i++;
    	}
    	
    	return $flag;
    }
    
    
    public function __destruct()
    {
        unset($this->array);
        unset($this->key);
    }
}