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

use Admin\Model\FreightModeModel;
use Admin\Model\FreightSendModel;
/**
 * 运送方式逻辑处理
 * 
 * @author 王强
 */
class FreightModeLogic extends AbstractGetDataLogic
{

    /**
     * 临时消息机制
     * 
     * @var unknown
     */
    private $tempMessage = [];

    /**
     * 构造方法
     * 
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new FreightModeModel();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {}
    
    /**
     * 获取发货地区关联字段
     * @return string
     */
    public function getIdBySendSplitKey()
    {
        return FreightModeModel::$id_d;
    }
    
    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return FreightModeModel::class;
    }

    /**
     * 获取模板关联key
     */
    public function getFreightIdKey()
    {
        return FreightModeModel::$freightId_d;
    }

    /**
     * 获取关联快递字段
     * 
     * @return string
     */
    public function getFreightCarryMode()
    {
        return FreightModeModel::$carryWay_d;
    }

    /**
     * 获取验证字段
     * 
     * @return boolean[][]
     */
    public function getSerachValidate()
    {
        return [
            FreightModeModel::$freightId_d => [
                'specialCharFilter' => true
            ]
        ];
    }

    /**
     * 获取验证字段消息
     * 
     * @return string[][]
     */
    public function getSerachMessage()
    {
        $comment = $this->getComment();
        
        $this->tempMessage = $comment;
        
        return [
            FreightModeModel::$freightId_d => [
                'specialCharFilter' => '请不要特殊字符（' . $comment[FreightModeModel::$freightId_d] . '）'
            ]
        ];
    }

    /**
     * @return the $tempMessage
     */
    public function getTempMessage()
    {
        return $this->tempMessage;
    }
 
    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            FreightModeModel::$freightId_d => [
                'required' => '请输入' . $comment[FreightModeModel::$freightId_d],
                'number' => $comment[FreightModeModel::$freightId_d] . '必须是数字'
            ],
            FreightModeModel::$firstThing_d => [ 
                'required' => '请输入' . $comment[FreightModeModel::$firstThing_d],
                'number' => $comment[FreightModeModel::$firstThing_d] . '必须是数字'
            ],
            FreightModeModel::$firstWeight_d => [
                'required' => '请输入' . $comment[FreightModeModel::$firstWeight_d],
                'number' => $comment[FreightModeModel::$firstWeight_d] . '必须是数字'
            ],
            
            FreightModeModel::$fristVolum_d => [
                'required' => '请输入' . $comment[FreightModeModel::$fristVolum_d],
                'number' => $comment[FreightModeModel::$fristVolum_d] . '必须是数字'
            ],
            
            FreightModeModel::$fristMoney_d => [
                'required' => '请输入' . $comment[FreightModeModel::$fristMoney_d],
                'number' => $comment[FreightModeModel::$fristMoney_d] . '必须是数字'
            ],
            
            FreightModeModel::$continuedHeavy_d => [
                'required' => '请输入' . $comment[FreightModeModel::$continuedHeavy_d],
                'number' => $comment[FreightModeModel::$continuedHeavy_d] . '必须是数字'
            ],
            
            FreightModeModel::$continuedVolum_d => [
                'required' => '请输入' . $comment[FreightModeModel::$continuedVolum_d],
                'number' => $comment[FreightModeModel::$continuedVolum_d] . '必须是数字'
            ],
            
            FreightModeModel::$continuedMoney_d => [
                'required' => '请输入' . $comment[FreightModeModel::$continuedMoney_d],
                'number' => $comment[FreightModeModel::$continuedMoney_d] . '必须是数字'
            ],
            
            FreightModeModel::$carryWay_d => [
                'required' => '请输入' . $comment[FreightModeModel::$carryWay_d],
                'number' => $comment[FreightModeModel::$carryWay_d] . '必须是数字'
            ],
            
            FreightModeModel::$continuedThing_d => [
                'required' => '请输入' . $comment[FreightModeModel::$continuedThing_d],
                'number' => $comment[FreightModeModel::$continuedThing_d] . '必须是数字'
            ]
        ]
        ;
        return $message;
    }

    /**
     * 获取验证字段
     */
    public function getCheckValidate()
    {
        $message = [
            FreightModeModel::$freightId_d => [
                'required' => true,
                'number' => true
            ],
            FreightModeModel::$firstThing_d => [
                'required' => true,
                'number' => true
            ],
            FreightModeModel::$firstWeight_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$fristVolum_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$fristMoney_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$continuedHeavy_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$continuedVolum_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$continuedMoney_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$carryWay_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightModeModel::$continuedThing_d => [
                'required' => true,
                'number' => true
            ]
        ];
        return $message;
    }
    
    //添加运费设置
    public function getFreightModelAdd(){
        $freightSend = new FreightSendModel();
        $post = $this->data;
        $post['store_id'] = $_SESSION['store_id'];
        $res = $this->modelObj->addFreightMode($post);
        if ($res['status'] == 1) {
            if (!empty($post['area'])) {
                $result = $freightSend->addFreightSend($post['area'],$res['data']);
                return $result;
            }else{
                M()->commit();
                return $res;
            }
        }else{
            return $res;
        }
    }
    //修改运费设置
    public function getFreightModelSave(){
        $freightSend = new FreightSendModel();
        $post = $this->data;
        $where['id'] = $post['id'];
        $res = $this->modelObj->saveFreightMode($where,$post);
        if ($res['status'] == 1) {
            if (!empty($post['area'])) {
                $result = $freightSend->saveFreightSend($post['area'],$where['id']);
                return $result;
            }else{
               return $res; 
            }
        }else{
            return $res;
        }
    }
    //删除运费设置
    public function delFreightMode(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $res = $this->modelObj->delFreightMode($where);
        if ($res['status'] == 1) {
            $s_where['freight_id'] = $post['id'];
            $result = D('FreightSend')->delFreightSend($s_where);
            return $result;
        }else{
            return $res;
        }
    }

    /**
     * 开启事务
     */
    protected function openStart()
    {
        if (empty($this->data)) {
            return false;
        }
        
        return $this->modelObj->startTrans();
    }
    
    /**
     * 状态
     * @param integer $status
     */
    protected function checkSuccess($status)
    {
        if (!$this->modelObj->traceStation($status)) {
            $this->errorMessage = '修改失败';
            return false;
        }
        return $status;
    }
     //获取运费设置列表
    public function getFreightModelList(){
        $where['store_id'] = $_SESSION['store_id'];
        $field = "id,freight_id,first_thing,first_weight,frist_volum,frist_money,continued_heavy,continued_volum,continued_money,carry_way,continued_thing";
        $limit = 10;
        $page = empty($this->data['page'])?0:$this->data['page'];
        $res = $this->modelObj->getData($where,$field,$limit,$page);
        return $res; 
    }
     //获取运费设置列表
    public function getFreightModelOne(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $field = "id,freight_id,first_thing,first_weight,frist_volum,frist_money,continued_heavy,continued_volum,continued_money,carry_way,continued_thing";
        $res = $this->modelObj->getDataOne($where,$field);
        return $res; 
    }
     //搜索运费设置列表
    public function getFreightModelSearch(){
        $title = $this->data['title'];
        $f_field = "id";
        $f_where['express_title'] = array('like','%'.$title.'%');
        $freight_id = D('Freights')->getFreightListByWhere($f_where,$f_field); 
        if (!empty($freight_id['data'])) {
            $Freight = array_column($freight_id['data'],'id');
            $freight = implode(",",$Freight);
            $where['store_id'] = $_SESSION['store_id'];
            $where['freight_id'] = array("IN",$freight);
            $field = "id,freight_id,first_thing,first_weight,frist_volum,frist_money,continued_heavy,continued_volum,continued_money,carry_way,continued_thing";
            $limit = 10;
            $page = empty($this->data['page'])?0:$this->data['page'];
            $res = $this->modelObj->getData($where,$field,$limit,$page);
            return $res;
        }else{
            return array("status"=>"","message"=>"暂无数据","data"=>"");
        }
        
    }
}