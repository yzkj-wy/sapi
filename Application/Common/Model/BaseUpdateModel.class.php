<?php
namespace Common\Model;

use Think\Model;
use Think\Hook;
use Common\TraitClass\MethodModel;
use Common\TraitClass\ModelToolTrait;
use Common\TraitClass\ColumShowHTMLTrait;
use Common\Behavior\AddLogData;
use Common\TraitClass\AddFieldTrait;

/**
 * 数据操作 控制
 * @author 王强
 * @version 1.0.2
 */
abstract class BaseUpdateModel extends Model
{
    use MethodModel;
    
    use ModelToolTrait;
    
    use InterFacePropertyClass;
    
    use ColumShowHTMLTrait;
    
    use AddFieldTrait;

    private static $obj = array();

    protected static $find = 'public static function getInitnation()';


    const DESC = ' DESC ';

    const ASC = ' ASC ';

    const desc = 'desc';
    
    const BETWEEN = ' between ';
    
    // //////////////////////////////////////////日志相关操作
    const LOG_INERT = 0x00;
    // |操作类型：0新增1修改2删除
    const LOG_UPDATE = 0x01;
    // |
    const LOG_DELETE = 0x02;
    // |
    protected $isLogObj = FALSE;
    // 是否是日志模型 是 不添加日志数据
    protected $solaveData = [];
    // 日志从表数据赋值
    protected static $deleteDataLog;
    // 删除时保存的日志数据
    protected static $cloneObj;
    // 要添加日志的模型对象
    // ///////////////////////////////////////////////////////
    const asc = 'asc';

    const DBAS = ' as ';

    const SUFFIX = '_d';
    
    // 日志表里 操其他表作的主键
    protected static $logPrimaryKey;

    /**
     * 取得子类的实例【用父类实例化子类】
     */
    public static function getInstance($className)
    {
        if (empty(self::$obj[$className])) {
            self::$obj[$className] = $className::getInitnation();
        }
        return self::$obj[$className];
    }

    /**
     * 重写添加
     * {@inheritDoc}
     * @see \Think\Model::add()
     */
    public function add($data = '', $options = array(), $replace = false)
    {
        if (empty($data)) {
            return false;
        }
        $data = $this->create($data);
        $insertId = parent::add($data, $options, $replace);
        
        if ($this->isLogObj) { // /是否是 日志模型 日志模型 不需要 添加日志
            
            return $insertId;
        }
        
        if (MODULE_NAME === 'Home') {
            return $insertId;
        }
        
        self::$cloneObj = clone $this;
        
        $this->solaveData = $data;
        
        // 写入日志主表
        $param = $this->addLogFlag($insertId);
        $param['type'] = self::LOG_INERT;
        Hook::add('insertLog', AddLogData::class);
        
        Hook::listen('insertLog', $param);
        
        return $insertId;
    }

    /**
     * 删除前的回调方法
     * {@inheritdoc}
     * @see \Think\Model::_before_delete()
     */
    protected function _before_delete($options)
    {
        $field = [
            'create_time',
            'update_time',
            'id'
        ];
        
        self::$deleteDataLog = (array)$this->field($field, true)->find($options);
    }
    /**
     * 删除后的回调方法
     * {@inheritdoc}
     * @see \Think\Model::_after_delete()
     */
    protected function _after_delete($data, $options)
    {
       
        if (empty($data)) {
            return FALSE;
        }
        
        if ($this->isLogObj) { // 屏蔽日志模型
            return true;
        }
        
        if (MODULE_NAME === 'Home') {
            return true;
        }
        self::$cloneObj = clone $this;
        
        $this->solaveData = self::$deleteDataLog;
       
        $param = $this->addLogFlag($data[static::$id_d]);
        
        $param['type'] = self::LOG_DELETE;
        
        Hook::add('deleteDataInsertLog', AddLogData::class);
        
        Hook::listen('deleteDataInsertLog', $param);
    }

    /**
     * 日志操作辅助函数
     * @param integer $insertId            
     * @return array
     */
    protected function addLogFlag($insertId)
    {
        if (($insertId = (int) $insertId) === 0 || ! $this->isEmpty($this->solaveData)) {
            return array();
        }
        $tabName = $this->getTableName();
        
        $param = [
            'table_name' => $tabName,
            'admin_id' => $_SESSION['aid'],
            'table_id' => $insertId,
            'comment' => $this->getAllTableNotes($tabName), // 表注释
            'edit_colums' => $this->solaveData
        ]; // 数据

        return $param;
    }

    /**
     * save 保存 更新 及其 日志操作
     * {@inheritDoc}
     * @see \Think\Model::save()
     */
    public function save($data = '', $options = array())
    {
        if (empty($data)) {
            return false;
        }
        $data = $this->create($data);
        ! $this->isOpenTranstion ?: $this->startTrans();
        
        if (MODULE_NAME === 'Home' || $this->isLogObj) { // 屏蔽日志模型
            
            $status = parent::save($data, $options);
            if ($status === false) {
                $this->rollback();
                return false;
            }
            return $status;
        }
        
        self::$cloneObj = clone $this;
        
        // 获取表名
        $tabName = $this->getTableName();
        
        // 保存现在的值
        $this->solaveData = array_keys($data);
     
        // 获取主键
        $pk = $this->getPk();
        
        // 判断主键
        $id = is_array($pk) ? array_shift($pk) : $pk;
        
        // 日志表 保存的操作表的主键
        self::$logPrimaryKey = $id;
        // 组装插入日志表数据
        $param = $this->addLogFlag(empty($data[$id]) ? 0 : $data[$id]);
        // 数据操作类型
        $param['type'] = self::LOG_UPDATE;
        
        // 开始行为获取之前的值
        Hook::listen('UpdateLogStart', $param);
        
        // 更新数据
        $status = parent::save($data, $options);
        if ($status === false) {
            $this->rollback();
            return false;
        }
        
        // 写入日志内容
        $content = [
            'edit_colums' => $data
        ];
        
        Hook::listen('UpdateLogEnd', $content);
        
        return $status;
    }

    /**
     * 查看事务
     * @return array
     * @author 王强<2272597637@qq.com>
     */
    protected function currentTranstation()
    {
        return $this->query('SHOW ENGINE INNODB STATUS');
    }

    /**
     * 查看是否有事务
     * @return boolean [false 没有 TRUE 有]
     */
    public function isHaveTranstation()
    {
        $data = $this->currentTranstation();
        return empty($data) ? false : true;
    }

    /**
     * 获取数据表全部表注释 并缓存
     * @param string $key 表名
     * @return array|string
     */
    public function getAllTableNotes($key = null)
    {
        $notes = S('Table_NOTES');
        
        if (! empty($notes)) {
            return empty($key) ? $notes : $notes[$key];
        }
        
        $data = $this->query('SELECT TABLE_COMMENT,TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = "' . C('DB_NAME') . '"');
        
        if (empty($data)) {
            return null;
        }
        $notes = array();
        foreach ($data as $name => $value) {
            $notes[$value['table_name']] = $value['table_comment'];
        }
        
        S('Table_NOTES', $notes, 800);
        
        return empty($key) ? $notes : $notes[$key];
    }


    /**
     * 事务消息
     */
    public function traceStation($status, $message = '更新失败')
    {
        if ($status === false) {
            $this->rollback();
            $this->error = $message;
            return false;
        }
        return true;
    }


    /**
     * 重写 构造方法
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '')
    {
        parent::__construct($name, $tablePrefix, $connection);
        
        // 实现自动添加代码[静态属性][开发时打开，部署时可关闭]
//         $this->autoAddProp();
        // 数据字段赋值 【用父类 实例化子类】$this 代指 子类的实例
        $this->setDbFileds();
    }
}