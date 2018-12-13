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
namespace Common\Logic;

use Common\Model\SelectDbModel;
use Common\Tool\DbUntil;
use Common\Tool\Extend\ArrayChildren;

/**
 * 分库联系表
 * @author Administrator
 *
 */
class SelectDbLogic 
{
    private $tableName;
    
    private $modelObj;
    
    /**
     * 架构方法
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        
        $this->modelObj = new SelectDbModel();
    }
    
    /**
     * 获取数据
     */
    public function getDataSource()
    {
        $data = $this->getStatusOpenDb();
        showData($data);
        $name = $data[SelectDbModel::$tbName_d];
        
        $class = 'Admin\\Model\\'.ucfirst($name).'Model';
        $id = 0;
        try {
            //获取最大
            $reflection = new \ReflectionClass($class);
            
            $id = $reflection->getMethod('getMaxId')->invoke($reflection->newInstanceArgs(['', '', '']));
        } catch (\Exception $e) {
           echo $e->getMessage();
        }
        
        $dbUntil = new DbUntil($data[SelectDbModel::$numberEnd_d], C('DB_PREFIX').$name, $data[SelectDbModel::$dbName_d]);
        
        $dbCmd = $dbUntil->buildDataBase($id);
        
        if (empty($dbCmd)) {
            return false;
        }
        
        $tbSql = $dbUntil->createTable($id);
        
        showData($tbSql);
        
        $numberStart =  $dbUntil->getMaxId();
        
        showData($numberStart);
        
        $addData = [
            SelectDbModel::$numberStart_d => $numberStart,
            SelectDbModel::$dbName_d => $dbUntil->getNewDbName(),
            SelectDbModel::$numberEnd_d => $numberStart + $data[SelectDbModel::$numberEnd_d],
            SelectDbModel::$isOpen_d => 1
        ];
        
        //添加一条数据
        
        $status = $this->addDbInfo($addData, $data[SelectDbModel::$id_d]);
        
        if (!$status) {
            return false;
        }
        
        $dbCreateStatus =  $this->modelObj->execute($dbCmd);
        showData($dbCreateStatus);
        if (!$this->modelObj->traceStation($dbCreateStatus)) {
            return false;
        }
        
        $tbCreateStatus =  $this->modelObj->execute($tbSql);
        showData($dbCreateStatus);
        if (!$this->modelObj->traceStation($dbCreateStatus)) {
            return false;
        }
        
        $this->modelObj->commit();

        return true;
    }
    
    private function addDbInfo(array $data, $id)
    {
        
        $this->modelObj->startTrans();
        
        $status = $this->modelObj->add($data);
        showData($status);
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        
        $updata = [
            SelectDbModel::$id_d => $id,
            SelectDbModel::$isOpen_d => 0
        ];
        
        $status = $this->modelObj->save($updata);
        showData($status);
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取开启的数据库
     */
    public function getStatusOpenDb()
    {
        $field = [
            SelectDbModel::$createTime_d,
            SelectDbModel::$updateTime_d
        ];
        return $this->modelObj->field($field, true)->where( SelectDbModel::$tbName_d.' ="%s" and '.SelectDbModel::$isOpen_d.' = 1', $this->tableName)->find();
    }
    
    /**
     * 获取全部开启的数据库
     * @return mixed|boolean|NULL|string|unknown|object
     */
    public function getOpenDb()
    {
        $field = [
            SelectDbModel::$createTime_d,
            SelectDbModel::$updateTime_d
        ];
        
        $data = S('DB_INFO_SOURCE');
        
        if (empty($data)) {
            $data = $this->modelObj->field($field, true)->where( SelectDbModel::$tbName_d.' ="%s"', $this->tableName)->select();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        $data = (new ArrayChildren($data))->convertIdByData(SelectDbModel::$id_d);
        
        S('DB_INFO_SOURCE', $data, 15);
        
        return $data;
    }
    
    public function selectPage()
    {
        $dbData = $this->getOpenDb();
        
        $page = 15;
        
        $firstPage = array_shift($dbData);
        
        if ($page < SelectDbModel::$con_d) {
            C('DB_NAME', $firstPage[SelectDbModel::$dbName_d]);
            return ;
        } 
        
        
        
    }
}