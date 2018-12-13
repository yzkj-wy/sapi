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

use Common\Model\ComplainSubjectModel;
use Common\Tool\Event;

/**
 * 商品投诉逻辑处理
 * @author 王强
 * @version 1.0.1
 */
class ComplainSubjectLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct($data)
    {
        $this->data = $data;
         
        $this->modelObj = new ComplainSubjectModel();
    }
    
    public function getResult(){}
    
    public function getModelClassName()
    {
        return ComplainSubjectModel::class;
    }
    
    /**
     * 
     */
    public function delete()
    {
        return $this->modelObj->delete($this->data[ComplainSubjectModel::$id_d]);
    }
    
    /**
     * 获取验证规则
     * @return string
     */
    public function getCheckValidate()
    {
        $validate =  [
            ComplainSubjectModel::$complainSubject_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            ComplainSubjectModel::$complainDesc_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            ComplainSubjectModel::$id_d => [
                'number' => true
            ],
            
        ];
        
        return ($validate);
    }
    
   /**
    * {@inheritDoc}
    * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
    */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $validate =  [
            ComplainSubjectModel::$complainSubject_d => [
                'required' => '请输入'.$comment[ComplainSubjectModel::$complainSubject_d],
                'specialCharFilter' => $comment[ComplainSubjectModel::$complainSubject_d].'不能输入特殊字符'
            ],
            ComplainSubjectModel::$complainDesc_d => [
                'required' => '请输入'.$comment[ComplainSubjectModel::$complainDesc_d],
                'specialCharFilter' => $comment[ComplainSubjectModel::$complainDesc_d].'不能输入特殊字符'
            ],
            ComplainSubjectModel::$id_d => [
                'number' => 'id必须是数字'
            ],
        ];
        
        return ($validate);
    }
    
    /**
     * 保存
     */
    public function save()
    {
        $status = false;
        try {
           $status = $this->modelObj->save($this->data);
        } catch (\Exception $e) {
            $this->errorMessage = '已存在【'.$this->data[ComplainSubjectModel::$complainSubject_d].'】';
        }
        return $status;
    }
    
    /**
     * 保存
     */
    public function add()
    {
        new Event($this, 'messageParper');
        $status = false;
        try {
            $status = $this->modelObj->add($this->data);
        } catch (\Exception $e) {
            $this->errorMessage = '已存在【'.$this->data[ComplainSubjectModel::$complainSubject_d].'】';
        }
        return $status;
    }
    
    /**
     * 回调
     */
    public function messageParper($msg)
    {
        unset($msg[ComplainSubjectModel::$id_d]);
        
        return $msg;
    }
}