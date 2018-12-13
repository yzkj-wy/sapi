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

use Common\Model\StoreGradeModel;

/**
 * 店铺等级
 * @author 王强
 * @version 1.0
 */
class StoreGradeLogic extends AbstractGetDataLogic
{
    /**
     * 错误属性
     * @var string
     */
    private $error;
    
    /**
     * 架构方法
     * @param mixed $data   数据处理
     * @param string $split 分割键
     */
    public function __construct($data, $split = null)
    {
       $this->data = $data;
       
       $this->splitKey = $split;
       
       $this->modelObj = StoreGradeModel::getInitnation();
    }
    
    /**
     * 获取店铺等级
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        
        if (!isset($this->data[$this->splitKey])) {
            return [];
        }
        return $this->modelObj->field(StoreGradeModel::$levelName_d)->where(StoreGradeModel::$id_d.'=%d', $this->data[$this->splitKey])->find();
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [
            StoreGradeModel::$upperLimit_d,
            StoreGradeModel::$lowerLimit_d,
            StoreGradeModel::$description_d
        ];
    }
    
    /**
     * 获取等级数据
     */
    public function getGradeData()
    {
        $data = $this->data;
        if (empty($data)) {
            return [];
        }
        
        $field = [
            StoreGradeModel::$id_d,
            StoreGradeModel::$levelName_d,
        ];
        
        $gradeData = $this->modelObj->getDataByOtherModel($data, $this->splitKey, $field, StoreGradeModel::$id_d);
        
        return $gradeData;
        
    }
    
    /**
     * 保存
     */
    public function save()
    {
        if (empty($this->data)) {
            return self::ADD_ERROR;
        }
        
        $status = false;
        
        try {
           $status = $this->modelObj->save($this->data);
        } catch (\Exception $e) {
            $this->error = '已存在该等级名称';
        }
        return $status;
    }
    
    /**
     * 添加
     */
    public function add()
    {
        if (empty($this->data)) {
            return self::ADD_ERROR;
        }
    
        $status = false;
    
        try {
            $status = $this->modelObj->add($this->data);
        } catch (\Exception $e) {
            $this->error = '已存在该等级名称';
        }
        return $status;
    }
    
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 返回模型类名
     */
    public function getModelClassName ()
    {
        return StoreGradeModel::class;
    }
    //获取店铺等级 数据 
    public function getStoreGrade(){
        $res =  $this->modelObj->getStoreGrade();
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
}