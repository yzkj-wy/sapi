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
use Common\Model\StoreClassModel;
use Think\Exception;

/**
 * 店铺分类
 * @author 王强
 * @version 1.0.0
 */
class StoreClassLogic extends AbstractGetDataLogic
{
    private $error;
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = [], $split = '')
    {
        $this->data = $args;
        
        $this->splitKey = $split;
        
        $this->modelObj = new StoreClassModel();
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
         //TODO
    }
    
    /**
     * 根据店铺数据 获取分类数据
     * @return array
     */
    public function getStoreClassData()
    {
        $data = $this->data;
        
        if (empty($data)) {
            return [];
        }
        
        $field = [
            StoreClassModel::$id_d,
            
            StoreClassModel::$scName_d
        ];
        $classData = $this->modelObj->getDataByOtherModel($data, $this->splitKey, $field, StoreClassModel::$id_d);
        
        return $classData;
        
    }
    /**
     * 添加数据
     */
    public function addStore()
    {
        $data = $this->data;
        
        if (empty($data)) {
            return [];
        }
       
        $status = false;
        
        try {
            $status = $this->modelObj->add($data);
        }catch (Exception $e) {
            $this->error = '已存在该分类名';
            return self::ADD_ERROR;
        }
        return $status;
    }
    
    /**
     * 更新
     */
    public function saveStore()
    {
        $data = $this->data;
        
        if (empty($data)) {
            return [];
        }
        
        try {
            $status = $this->modelObj->save($data);
        }catch (Exception $e) {
            $this->error = '已存在该分类名';
            return self::ADD_ERROR;
        }
        return $status;
    }
    
    public function getModelClassName()
    {
        return StoreClassModel::class;
    }
    public  function getStoreClass(){
        $res =  $this->modelObj->getStoreClass();
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
}