<?php
namespace Think\ModelTrait;

trait Save
{
    /**
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     * @access public
     * @param string|array $field  字段名
     * @param string $value  字段值
     * @return boolean
     */
    public function setField($field,$value='') {
        if(is_array($field)) {
            $data           =   $field;
        }else{
            $data[$field]   =   $value;
        }
        return $this->save($data);
    }
    
    /**
     * 字段值增长
     * @access public
     * @param string $field  字段名
     * @param integer $step  增长值
     * @param integer $lazyTime  延时时间(s)
     * @return boolean
     */
    public function setInc($field,$step=1,$lazyTime=0) {
        if($lazyTime>0) {// 延迟写入
            $condition   =  $this->options['where'];
            $guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step ) return true; // 等待下次写入
        }
        return $this->setField($field,array('exp',$field.'+'.$step));
    }
    
    /**
     * 字段值减少
     * @access public
     * @param string $field  字段名
     * @param integer $step  减少值
     * @param integer $lazyTime  延时时间(s)
     * @return boolean
     */
    public function setDec($field,$step=1,$lazyTime=0) {
        if($lazyTime>0) {// 延迟写入
            $condition   =  $this->options['where'];
            $guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step ) return true; // 等待下次写入
        }
        return $this->setField($field,array('exp',$field.'-'.$step));
    }
    
    /**
     * 延时更新检查 返回false表示需要延时
     * 否则返回实际写入的数值
     * @access public
     * @param string $guid  写入标识
     * @param integer $step  写入步进值
     * @param integer $lazyTime  延时时间(s)
     * @return false|integer
     */
    protected function lazyWrite($guid,$step,$lazyTime) {
        if(false !== ($value = S($guid))) { // 存在缓存写入数据
            if(NOW_TIME > S($guid.'_time')+$lazyTime) {
                // 延时更新时间到了，删除缓存数据 并实际写入数据库
                S($guid,NULL);
                S($guid.'_time',NULL);
                return $value+$step;
            }else{
                // 追加数据到缓存
                S($guid,$value+$step);
                return false;
            }
        }else{ // 没有缓存数据
            S($guid,$step);
            // 计时开始
            S($guid.'_time',NOW_TIME);
            return false;
        }
    }
    
    /**
     * 保存数据
     * @access public
     * @param mixed $data 数据
     * @param array $options 表达式
     * @return boolean
     */
    public function save($data='',$options=array()) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     =   array();
            }else{
                $this->error    =   L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 数据处理
        $data       =   $this->_facade($data);
        if(empty($data)){
            // 没有数据则不执行
            $this->error    =   L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 分析表达式
        $options    =   $this->_parseOptions($options);
        $pk         =   $this->getPk();
        if(!isset($options['where']) ) {
            // 如果存在主键数据 则自动作为更新条件
            if (is_string($pk) && isset($data[$pk])) {
                $where[$pk]     =   $data[$pk];
                unset($data[$pk]);
            } elseif (is_array($pk)) {
                // 增加复合主键支持
                foreach ($pk as $field) {
                    if(isset($data[$field])) {
                        $where[$field]      =   $data[$field];
                    } else {
                        // 如果缺少复合主键数据则不执行
                        $this->error        =   L('_OPERATION_WRONG_');
                        return false;
                    }
                    unset($data[$field]);
                }
            }
            if(!isset($where)){
                // 如果没有任何更新条件则不执行
                
                $this->error        =   L('_OPERATION_WRONG_');
                return false;
            }else{
                $options['where']   =   $where;
            }
        }
        
        if(is_array($options['where']) && isset($options['where'][$pk])){
            $pkValue    =   $options['where'][$pk];
        }
        if(false === $this->_before_update($data,$options)) {
            return false;
        }
        
        $result     =   $this->db->update($data,$options);
        if(false !== $result && is_numeric($result)) {
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_update($data,$options);
        }
        return $result;
    }
    
    
    /**
     * 执行SQL语句
     * @access public
     * @param string $sql  SQL指令
     * @param mixed $parse  是否需要解析SQL
     * @return false | integer
     */
    public function execute($sql,$parse=false) {
        if(!is_bool($parse) && !is_array($parse)) {
            $parse = func_get_args();
            array_shift($parse);
        }
        $sql  =   $this->parseSql($sql,$parse);
        return $this->db->execute($sql);
    }
    
    /**
     * 对保存到数据库的数据进行处理
     * @access protected
     * @param mixed $data 要操作的数据
     * @return boolean
     */
    protected function _facade($data) {
        
        // 安全过滤
        if(!empty($this->options['filter'])) {
            $data = array_map($this->options['filter'],$data);
            unset($this->options['filter']);
        }
        $this->_before_write($data);
        
        // 检查数据字段合法性
        if(empty($this->fields)) {
            
            return $data;
        }
        
        if(!empty($this->options['field'])) {
            $fields =   $this->options['field'];
            unset($this->options['field']);
            if(is_string($fields)) {
                $fields =   explode(',',$fields);
            }
        }else{
            $fields =   $this->fields;
        }
        
        foreach ($data as $key=>$val){
            if(!in_array($key,$fields,true)){
                if(!empty($this->options['strict'])){
                    E(L('_DATA_TYPE_INVALID_').':['.$key.'=>'.$val.']');
                }
                unset($data[$key]);
            }elseif(is_scalar($val)) {
                // 字段类型检查 和 强制转换
                $this->_parseType($data,$key);
            }
        }
        return $data;
    }
}