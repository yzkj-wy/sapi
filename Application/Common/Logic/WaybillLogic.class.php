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

use Common\Model\WaybillModel;

/**
 * 运单逻辑处理
 * @author 王强
 * @version 1.0.0
 */
class WaybillLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
         
        $this->modelObj = new WaybillModel();
        
        $this->splitKey = $split;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return WaybillModel::class;
    }
    
    /**
     * 快递关联键
     */
    public function getExpressSplitKey()
    {
        return WaybillModel::$expressId_d;
    }
    
    /**
     * 获取关联字段
     */
    public function getSplitKeyById()
    {
        return WaybillModel::$id_d;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            WaybillModel::$waybillName_d
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [
            WaybillModel::$waybillLeft_d,
            WaybillModel::$waybillTop_d
        ];
    }
    
    /**
     * 检测状态消息提示
     * @return array
     */
    public function checkStatusUsable()
    {
        $comment = $this->getComment();
        
        $message = [
            WaybillModel::$id_d => [
                'required' => $comment[WaybillModel::$id_d].'不能为空',
                'number'   => $comment[WaybillModel::$id_d]. '必须是数字'
            ],
            
            WaybillModel::$waybillUsable_d => [
                'required' => $comment[WaybillModel::$waybillUsable_d].'不能为空',
                'number'   => $comment[WaybillModel::$waybillUsable_d]. '必须是数字'
            ]
        ];
        
        foreach ($message as $key => $value) {
        
            $result = $this->paramCheckNotify($value, $key);
             
            if ($result === false) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            WaybillModel::$waybillName_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillName_d],
                'specialCharFilter' => $comment[WaybillModel::$waybillName_d].'不能输入特殊字符'
            ],
            WaybillModel::$waybillImage_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillImage_d],
            ],
            WaybillModel::$waybillWidth_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillWidth_d],
                'number' => $comment[WaybillModel::$waybillWidth_d].'必须是数字'
            ],
            WaybillModel::$waybillHeight_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillHeight_d],
                'number' => $comment[WaybillModel::$waybillHeight_d].'必须是数字'
            ],
            WaybillModel::$waybillTop_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillTop_d],
                'number' => $comment[WaybillModel::$waybillTop_d].'必须是数字'
            ],
            
            WaybillModel::$waybillLeft_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillLeft_d],
                'number' => $comment[WaybillModel::$waybillLeft_d].'必须是数字'
            ],
            
            WaybillModel::$expressId_d => [
                'required' => '请输入'.$comment[WaybillModel::$expressId_d],
                'number' => $comment[WaybillModel::$expressId_d].'必须是数字'
            ],
            
            WaybillModel::$waybillUsable_d => [
                'required' => '请输入'.$comment[WaybillModel::$waybillUsable_d],
                'number' => $comment[WaybillModel::$waybillUsable_d].'必须是数字'
            ],
            
            WaybillModel::$id_d => [
                'required' => 'id必须存在',
                'number' => 'id必须是数字'
            ],
        ];
        
        return $message;
    }
    
    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            WaybillModel::$waybillName_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            WaybillModel::$waybillImage_d => [
                'required' => true,
            ],

            WaybillModel::$waybillWidth_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$waybillHeight_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$waybillUsable_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$waybillTop_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$waybillLeft_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$expressId_d => [
                'required' => true,
                'number' => true
            ],
            
            WaybillModel::$id_d => [
                'required' => true,
                'number' => true
            ],
        ];
        return $validate;
    }
}