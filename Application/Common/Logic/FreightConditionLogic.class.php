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

use Admin\Model\FreightConditionModel;
use Common\Tool\Tool;
use Common\TraitClass\AddAreaTrait;

/**
 * 运费条件逻辑处理
 * 
 * @author 王强
 * @version 1.0.0
 */
class FreightConditionLogic extends AbstractGetDataLogic
{
    use AddAreaTrait;
    /**
     * @var boolean
     */
    private $curretIdIsEmpty = FALSE;
    /**
     * @return the $curretIdIsEmpty
     */
    public function getCurretIdIsEmpty()
    {
        return $this->curretIdIsEmpty;
    }

    /**
     * 构造方法
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new FreightConditionModel();
    }

    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {}

    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return FreightConditionModel::class;
    }

    /**
     * 获取 运费配置信息
     */
    public function getFreightOneData()
    {   
        $this->data['id'] = 1;
        $notField = [
            FreightConditionModel::$createTime_d,
            FreightConditionModel::$updateTime_d
        ];
        $data = $this->modelObj->field($notField, true)
            ->where(FreightConditionModel::$freightId_d . '= %d', $this->data['id'])
            ->find();
        if (!empty($data)) {
            return $data;
        }
        
        $this->curretIdIsEmpty = true;
        
        return $this->setDefalutValue();
    }

    /**
     * 设置默认值
     */
    protected function setDefalutValue()
    {
        $array = [];
        
        // 设置默认值
        Tool::isSetDefaultValue($array, array(
            FreightConditionModel::$id_d,
            FreightConditionModel::$mailArea_monery_d,
            FreightConditionModel::$mailArea_num_d,
            FreightConditionModel::$mailArea_volume_d,
            FreightConditionModel::$mailArea_wieght_d
        ), 0);
        
        return $array;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            FreightConditionModel::$freightId_d => [
                'required' => '请输入' . $comment[FreightConditionModel::$freightId_d],
                'number' => $comment[FreightConditionModel::$freightId_d] . '必须是数字'
            ],
            FreightConditionModel::$mailArea_num_d => [
                'required' => '请输入' . $comment[FreightConditionModel::$mailArea_num_d],
                'number' => $comment[FreightConditionModel::$mailArea_num_d] . '必须是数字'
            ],
            FreightConditionModel::$mailArea_wieght_d => [
                'required' => '请输入' . $comment[FreightConditionModel::$mailArea_wieght_d],
                'number' => $comment[FreightConditionModel::$mailArea_wieght_d] . '必须是数字'
            ],
            
            FreightConditionModel::$mailArea_volume_d => [
                'required' => '请输入' . $comment[FreightConditionModel::$mailArea_volume_d],
                'number' => $comment[FreightConditionModel::$mailArea_volume_d] . '必须是数字'
            ],
            
            FreightConditionModel::$mailArea_monery_d => [
                'required' => '请输入' . $comment[FreightConditionModel::$mailArea_monery_d],
                'number' => $comment[FreightConditionModel::$mailArea_monery_d] . '必须是数字'
            ],
        ];
        return $message;
    }
    
    /**
     * 验证字段
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            FreightConditionModel::$freightId_d => [
                'required' => true,
                'number' => true
            ],
            FreightConditionModel::$mailArea_num_d => [
                'required' => true,
                'number' => true
            ],
            FreightConditionModel::$mailArea_wieght_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightConditionModel::$mailArea_volume_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightConditionModel::$mailArea_monery_d => [
                'required' => true,
                'number' => true
            ],
        ];
        
        return $validate;
    }
    
    /**
     * 保存
     * @param array 提交的数据
     * @return bool
     */
    public function saveCondition()
    {
        $this->modelObj->startTrans();
    
        $status =  $this->modelObj->save($this->data);
        
        if (!$this->modelObj->traceStation($status)) {
            $this->errorMessage = '保存失败';
            return false;
        }
        
        return $status;
    }
    
    /**
     * 添加条件
     * @return bool
     */
    public function addCondition ()
    {
        if (empty($this->data)) {
            return false;
        }
    
        $this->modelObj->startTrans();
    
        $status = $this->modelObj->add($this->data);
    
        if (!$this->modelObj->traceStation($status)) {
            $this->errorMessage = '添加失败';
            return false;
        }
        return $status;
    }
    
    /**
     * 获取包邮地区关联字段
     * @return string
     */
    public function getIdBySendSplitKey()
    {
        return FreightConditionModel::$id_d;
    }
    /**
     * 获取运费配置信息
     */
    public function getFreightInfo()
    {   
        $post['freight_id'] =  $this->data['freight_id'];
        $field = "id,freight_id,mail_area_num,mail_area_wieght,mail_area_volume,mail_area_monery";
        $res = $this->modelObj->getFreightOne($post,$field);       
        return $res;   
    }
    //设置包邮地址
    public function setFreight(){
        $post = $this->data;
       
        $where['freight_id'] =  $post['freight_id'];
        $condition = M('FreightCondition')->field('id')->where($where)->find();
        if (!empty($condition)) {
            $freight['mail_area_num'] = $post['mail_area_num'];
            $freight['mail_area_wieght'] = $post['mail_area_wieght'];
            $freight['freight_id'] = $post['freight_id'];
            $freight['mail_area_volume'] = $post['mail_area_volume'];
            $freight['mail_area_monery'] = $post['mail_area_monery'];
            $freight['update_time'] = time();
            $res = $this->modelObj->saveFreight($where,$freight);       
            if ($res['status'] == 1) {
                $area['freight_id'] = $condition['id'];
                $res = D('FreightArea')->saveArea($area,$post['area']);
                return $res;
            }else{
                return $res;
            }
        }else{
            $freight['mail_area_num'] = $post['mail_area_num'];
            $freight['mail_area_wieght'] = $post['mail_area_wieght'];
            $freight['freight_id'] = $post['freight_id'];
            $freight['mail_area_volume'] = $post['mail_area_volume'];
            $freight['mail_area_monery'] = $post['mail_area_monery'];
            $freight['create_time'] = time();
            $res = $this->modelObj->addFreight($freight);       
            if ($res['status'] == 1) {
                $area['freight_id'] = $res['data'];
                $res = D('FreightArea')->saveArea($area,$post['area']);
                return $res;
            }else{
                return $data;
            }
        }
    }
}